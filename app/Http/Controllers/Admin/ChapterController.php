<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChapterDownload;
use App\Models\VolumeDownload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comic;
use App\Models\Chapter;
use App\Models\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChapterController extends Controller {
    public function index($comic_slug) {
        return redirect()->route('admin.comics.show', $comic_slug);
    }

    public function create($comic_slug) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        return view('admin.comics.chapters.create', ['comic' => $comic]);
    }

    public function store(Request $request, $comic_slug) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }

        try {
            $fields = Chapter::getFieldsIfValid($comic, $request);
        } catch (\DuplicatedChapter $e){
            return back()->with('error', $e->getMessage())->withInput();
        }

        $fields['salt'] = Str::random();
        $fields['slug'] = Chapter::generateSlug($fields);
        if(!isset($fields['views'])) $fields['views'] = 0;
        $chapter = Chapter::create($fields);
        $path = Chapter::path($comic, $chapter);
        Storage::makeDirectory($path);
        Storage::setVisibility($path, 'public');
        $now = Carbon::now();
        if(!$chapter['hidden'] && !$chapter['licensed'] && $now >= $chapter->publish_start && (!$chapter->publish_end || $now < $chapter->publish_end))
            VolumeDownload::cleanDownload(Chapter::volume_download($chapter), $comic);
        return redirect()->route('admin.comics.chapters.show', ['comic' => $comic_slug, 'chapter' => $chapter->id])->with('success', 'Chapter created');
    }

    public function show($comic_slug, $chapter_id) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::find($chapter_id);
        if (!$chapter || $chapter->comic_id !== $comic->id) {
            abort(404);
        }
        $chapter->url = Chapter::getUrl($comic, $chapter);
        $chapter->published_on = convertToTimezone($chapter->published_on, Auth::user()->timezone);
        $chapter->publish_start = convertToTimezone($chapter->publish_start, Auth::user()->timezone);
        if($chapter->publish_end) $chapter->publish_end = convertToTimezone($chapter->publish_end, Auth::user()->timezone);
        return view('admin.comics.chapters.show')->with(['comic' => $comic, 'chapter' => $chapter, 'pages' => Page::getAllPagesForFileUpload($comic, $chapter)]);
    }

    public function edit($comic_slug, $chapter_id) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::find($chapter_id);
        if (!$chapter || $chapter->comic_id !== $comic->id) {
            abort(404);
        }
        $chapter->published_on = convertToTimezone($chapter->published_on, Auth::user()->timezone);
        $chapter->publish_start = convertToTimezone($chapter->publish_start, Auth::user()->timezone);
        if($chapter->publish_end) $chapter->publish_end = convertToTimezone($chapter->publish_end, Auth::user()->timezone);
        return view('admin.comics.chapters.edit')->with(['comic' => $comic, 'chapter' => $chapter]);
    }

    public function update(Request $request, $comic_id, $chapter_id) {
        $comic = Comic::find($comic_id);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::find($chapter_id);
        if (!$chapter || $chapter->comic_id !== $comic->id) {
            abort(404);
        }
        $old_path = Chapter::path($comic, $chapter);

        try {
            $fields = Chapter::getFieldsIfValid($comic, $request);
        } catch (\DuplicatedChapter $e){
            return back()->with('error', $e->getMessage())->withInput();
        }

        // If has a new title or slug regenerate it
        if ((!isset($fields['slug']) && isset($fields['title']) && $chapter->title != $fields['title']) || (isset($fields['slug']) && $chapter->slug != $fields['slug'])) {
            $fields['slug'] = Chapter::generateSlug($fields);
        } else {
            unset($fields['slug']);
        }

        $new_chapter = new Chapter;
        $new_chapter->comic_id = $chapter->comic_id;
        $new_chapter->slug = $fields['slug'] ?? $chapter->slug;
        $new_chapter->language = $fields['language'] ?? null;
        $new_chapter->volume = $fields['volume'] ?? null;
        $new_chapter->chapter = $fields['chapter'] ?? null;
        $new_chapter->subchapter = $fields['subchapter'] ?? null;
        $new_chapter->salt = $chapter->salt;
        $new_chapter['hidden'] = $fields['hidden'] ?? $chapter['hidden'];
        $new_chapter['licensed'] = $fields['licensed'] ?? $chapter['licensed'];
        $new_chapter['team_id'] = $fields['team_id'] ?? $chapter->team_id;
        $new_chapter->publish_start = $fields['publish_start'] ?? $chapter->publish_start;
        $new_chapter->publish_end = $fields['publish_end'] ?? $chapter->publish_end;
        $new_path = Chapter::path($comic, $new_chapter);

        // Check if we need to delete its zips
        if(ChapterDownload::name($comic, $chapter) !== ChapterDownload::name($comic, $new_chapter)) {
            ChapterDownload::cleanDownload($chapter->download, $comic, $chapter, $chapter);
        }
        // If you are hiding or showing the chapter
        if ($new_chapter['hidden'] != $chapter['hidden'] ||
            $new_chapter['licensed'] != $chapter['licensed'] ||
            $new_chapter->publish_start != $chapter->publish_start || $new_chapter->publish_end != $chapter->publish_end) {
            VolumeDownload::cleanDownload(Chapter::volume_download($new_chapter), $comic);
        }
        // Check if has a new path, then rename it
        if ($old_path !== $new_path) {
            Storage::move($old_path, $new_path);
        }

        unset($new_chapter);

        Chapter::where('id', $chapter_id)->update($fields);
        return redirect()->route('admin.comics.chapters.show', ['comic' => $comic->slug, 'chapter' => $chapter_id])->with('success', 'Chapter updated');
    }

    public function destroy($comic_id, $chapter_id) {
        $comic = Comic::find($comic_id);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::find($chapter_id);
        if (!$chapter || $chapter->comic_id !== $comic->id) {
            abort(404);
        }
        if(!$chapter['hidden'] && !$chapter['licensed']) VolumeDownload::cleanDownload(Chapter::volume_download($chapter), $comic);
        Storage::deleteDirectory(Chapter::path($comic, $chapter));
        $chapter->delete();
        return redirect()->route('admin.comics.show', ['comic' => $comic->slug])->with('warning', 'Chapter "' . Chapter::name($comic, $chapter) . '" deleted');
    }
}
