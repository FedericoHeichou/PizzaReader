<?php

namespace App\Http\Controllers\Reader;

use App\Comic;
use App\Chapter;
use App\Page;
use App\Http\Controllers\Controller;

class ReaderController extends Controller {
    public function comics() {
        $response = ['comics' => []];

        $comics = Comic::public()->get();
        foreach ($comics as $comic) {
            array_push($response['comics'], Comic::generateReaderArray($comic));
        }
        return response()->json($response);
    }

    public function comic($comic_slug) {
        $response = ['comic' => null, 'chapters' => []];

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            return response()->json($response);
        }
        $response['comic'] = Comic::generateReaderArray($comic);

        $chapters = $comic->publicChapters;
        foreach ($chapters as $chapter) {
            array_push($response['chapters'], Chapter::generateReaderArray($comic, $chapter));
        }
        return response()->json($response);
    }

    public function chapter($comic_slug, $language, $ch = null) {
        $response = ['comic' => null, 'chapter' => null, 'pages' => [], 'next_chapter' => null, 'previous_chapter' => null];

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
        }
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            return response()->json($response);
        }
        $response['comic'] = Comic::generateReaderArray($comic);
        $chapter = $comic->publicChapters()->where([
            ['volume', $ch['vol']],
            ['chapter', $ch['ch']],
            ['subchapter', $ch['sub']],
            ['language', $language],
        ])->first();
        $response['chapter'] = Chapter::generateReaderArray($comic, $chapter);
        $response['pages'] = Page::getAllPagesForReader($comic, $chapter);

        $previous_chapter = null;
        $next_chapter = null;
        $last = null;
        $can_break = false;
        if ($chapter) {
            foreach ($comic->publicChapters as $c) {
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
            $response['previous_chapter'] = Chapter::generateReaderArray($comic, $previous_chapter);
            $response['next_chapter'] = Chapter::generateReaderArray($comic, $next_chapter);
        }

        return response()->json($response);
    }
}
