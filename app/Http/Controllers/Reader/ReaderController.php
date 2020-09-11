<?php

namespace App\Http\Controllers\Reader;

use App\Models\Comic;
use App\Models\Chapter;
use App\Models\Page;
use App\Http\Controllers\Controller;
use App\Models\View;

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

        if ($ch) {
            $ch = explode("/", $ch);
            $length = count($ch);
            if ($length % 2) {
                return response()->json($response);
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
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            return response()->json($response);
        }
        $response['comic'] = Comic::generateReaderArrayWithChapters($comic);
        $chapter = $comic->publicChapters()->where([
            ['volume', $ch['vol']],
            ['chapter', $ch['ch']],
            ['subchapter', $ch['sub']],
            ['language', $language],
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
}
