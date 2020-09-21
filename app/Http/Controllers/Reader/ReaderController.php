<?php

namespace App\Http\Controllers\Reader;

use App\Models\ChapterDownload;
use App\Models\Comic;
use App\Models\Chapter;
use App\Models\Page;
use App\Http\Controllers\Controller;
use App\Models\View;
use App\Models\VolumeDownload;
use Illuminate\Support\Facades\Storage;

class ReaderController extends Controller {
    public function comics() {
        $response = ['comics' => []];

        $comics = Comic::public()->get();
        foreach ($comics as $comic) {
            array_push($response['comics'], Comic::generateReaderArray($comic));
        }
        return response()->json($response);
    }

    public function search($search) {
        $response = ['comics' => []];

        $comics = Comic::publicSearch($search);
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
        $ch = $this->explodeCh($ch);
        if (!$ch) return response()->json($response);


        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            return response()->json($response);
        }
        $response['comic'] = Comic::generateReaderArrayWithChapters($comic);
        $chapter = $comic->publicChapters()->where([
            ['language', $language],
            ['volume', $ch['vol']],
            ['chapter', $ch['ch']],
            ['subchapter', $ch['sub']],
        ])->first();
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

        return response()->json($response);
    }

    public function download($comic_slug, $language, $ch = null) {
        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            abort(404);
        }

        $ch = $this->explodeCh($ch);
        if (!$ch) abort(404);
        $chapter = $comic->publicChapters()->where([
            ['language', $language],
            ['volume', $ch['vol']],
            ['chapter', $ch['ch']],
            ['subchapter', $ch['sub']],
        ])->first();
        if (!$chapter) {
            // Volume download is only for real publicChapters
            $chapters = $comic->chapters()->where([
                ['language', $language],
                ['volume', $ch['vol']],
                ['hidden', 0],
            ])->get();
            if ($chapters->isEmpty()) {
                abort(404);
            }
            return Storage::download(VolumeDownload::getDownload($comic, $language, $ch['vol']));
        }
        return Storage::download(ChapterDownload::getDownload($comic, $chapter));
    }

    private function explodeCh($ch) {
        if ($ch) {
            $ch = explode("/", $ch);
            $length = count($ch);
            if ($length % 2) {
                return null;
            }
            $temp = ['vol' => null, 'ch' => null, 'sub' => null];
            for ($i = 0; $i < $length; $i += 2) {
                if (in_array($ch[$i], ['vol', 'ch', 'sub'], true)) $temp[$ch[$i]] = $ch[$i + 1];
            }
            $ch = $temp;
            unset($temp);
        } else {
            $ch = ['vol' => null, 'ch' => null, 'sub' => null];
        }
        return $ch;
    }
}
