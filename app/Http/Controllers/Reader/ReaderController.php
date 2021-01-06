<?php

namespace App\Http\Controllers\Reader;

use App\Models\ChapterDownload;
use App\Models\ChapterPdf;
use App\Models\Comic;
use App\Models\Chapter;
use App\Models\Page;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\View;
use App\Models\VolumeDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReaderController extends Controller {

    public function comics() {
        return response()->json($this->getComics());
    }

    public function alph() {
        return response()->json($this->getComics("name"));
    }

    public function getComics($ord="order_index") {
        $response = ['comics' => []];

        $comics = Comic::public($ord)->get();
        foreach ($comics as $comic) {
            array_push($response['comics'], Comic::generateReaderArray($comic));
        }
        return $response;
    }

    public function search($search) {
        $response = ['comics' => []];
        $comics = Comic::publicSearch($search);
        foreach ($comics as $comic) {
            array_push($response['comics'], Comic::generateReaderArray($comic));
        }
        return response()->json($response);
    }

    public function targets($target) {
        $response = ['comics' => []];
        $comics = Comic::publicSearch($target, 'target');
        foreach ($comics as $comic) {
            array_push($response['comics'], Comic::generateReaderArray($comic));
        }
        return response()->json($response);
    }

    public function genres($genre) {
        $response = ['comics' => []];
        $comics = Comic::publicSearch($genre, 'genres');
        foreach ($comics as $comic) {
            array_push($response['comics'], Comic::generateReaderArray($comic));
        }
        return response()->json($response);
    }

    public function comic($comic_slug) {
        $comic = Comic::publicSlug($comic_slug);
        return response()->json(['comic' => Comic::generateReaderArrayWithChapters($comic)]);
    }

    public function chapter($comic_slug, $language, $ch = null) {
        $response = ['comic' => null, 'chapter' => null];
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) return response()->json($response);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            return response()->json($response);
        }
        $response['comic'] = Comic::generateReaderArrayWithChapters($comic);

        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter) {
            return response()->json($response);
        }

        View::incrementIfNew($chapter, request()->ip());

        $response['chapter'] = Chapter::generateReaderArray($comic, $chapter);
        $response['chapter']['pages'] = Page::getAllPagesForReader($comic, $chapter);

        $previous_chapter = null;
        $next_chapter = null;
        $last = null;
        $can_break = false;
        // Previous and Next chapter should be in the same language
        foreach ($comic->publicChapters()->where('language', $chapter->language)->get() as $c) {
            if ($can_break) {
                $previous_chapter = $c;
                break;
            }
            if ($c->id === $chapter->id) {
                $next_chapter = $last;
                $can_break = true;
            }
            $last = $c;
        }
        $response['chapter']['previous'] = Chapter::generateReaderArray($comic, $previous_chapter);
        $response['chapter']['next'] = Chapter::generateReaderArray($comic, $next_chapter);

        $response['chapter']['csrf_token'] = csrf_token();
        $your_vote = Rating::where([['chapter_id', $chapter->id], ['ip', request()->ip()]])->first();
        $response['chapter']['your_vote'] = $your_vote ? $your_vote->rating : null;
        return response()->json($response);
    }

    public function download($comic_slug, $language, $ch = null) {
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) abort(404);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            abort(404);
        }

        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter) {
            if ($ch['vol'] === null || $ch['ch'] !== null || $ch['sub'] !== null) {
                abort(404);
            }
            // Volume download is only for real publicChapters
            $chapters = $comic->chapters()->where([
                ['language', $language],
                ['volume', $ch['vol']],
            ])->published()->get();
            if ($chapters->isEmpty()) {
                abort(404);
            }
            if (!Chapter::canVolumeDownload($comic->id)) {
                abort(403);
            }
            $download = VolumeDownload::getDownload($comic, $language, $ch['vol']);
            if (!$download) {
                abort(404);
            }
            return Storage::download($download['path'], $download['name']);
        }
        if (!Chapter::canChapterDownload($comic->id)) {
            abort(403);
        }
        $download = ChapterDownload::getDownload($comic, $chapter);
        if (!$download) {
            abort(404);
        }
        return Storage::download($download['path'], $download['name']);
    }

    public function pdf($comic_slug, $language, $ch = null) {
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) abort(404);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            abort(404);
        }

        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter || !Chapter::canChapterPdf($comic->id)) {
            abort(404);
        }
        $pdf = ChapterPdf::getPdf($comic, $chapter);
        if (!$pdf) {
            abort(404);
        }
        return Storage::download($pdf['path'], $pdf['name']);
    }

    public function vote(Request $request, $comic_slug, $language, $ch = null) {
        $request->validate([
            'vote' => ['integer', 'min:1', 'max:5', 'required'],
        ]);
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) abort(404);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            abort(404);
        }

        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter) {
            abort(404);
        }

        Rating::updateOrCreate(
            ['chapter_id' => $chapter->id, 'ip' => request()->ip()],
            ['rating' => $request->vote]
        );

        $chapter->rating = $chapter->ratings()->avg('rating') * 2;
        $chapter->save();

        return response()->json(['rating' => $chapter->rating]);
    }

    private function explodeCh($language, $ch) {
        if ($ch) {
            $ch = explode("/", $ch);
            $length = count($ch);
            if ($length % 2) {
                return null;
            }
            $temp = ['lang' => $language, 'vol' => null, 'ch' => null, 'sub' => null];
            for ($i = 0; $i < $length; $i += 2) {
                if (in_array($ch[$i], ['vol', 'ch', 'sub'], true)) $temp[$ch[$i]] = $ch[$i + 1];
            }
            $ch = $temp;
            unset($temp);
        } else {
            $ch = ['lang' => $language, 'vol' => null, 'ch' => null, 'sub' => null];
        }
        return $ch;
    }
}
