<?php

namespace App\Http\Controllers\Admin;

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
        $form_fields = Chapter::getFormFieldsForValidation();
        $request->validate($form_fields);

        $fields = getFieldsFromRequest($request, $form_fields);
        $fields['published_on'] = Carbon::createFromFormat('Y-m-d\TH:i', $fields['published_on'], $fields['timezone'])->tz('UTC');
        Auth::user()->update(['timezone' => $fields['timezone']]);
        unset($fields['timezone']);
        $fields['salt'] = Str::random();
        $fields['comic_id'] = $comic->id;
        $fields['slug'] = Chapter::generateSlug($fields);
        if ($fields['team2_id'] === '0') unset($fields['team2_id']);
        $chapter = Chapter::create($fields);
        $path = Chapter::path($comic, $chapter);
        Storage::makeDirectory($path);
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
        $chapter->published_on = Carbon::createFromFormat('Y-m-d H:i:s', $chapter->published_on, 'UTC')->tz(Auth::user()->timezone);
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
        $chapter->published_on = Carbon::createFromFormat('Y-m-d H:i:s', $chapter->published_on, 'UTC')->tz(Auth::user()->timezone);
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
        $form_fields = Chapter::getFormFieldsForValidation();
        $request->validate($form_fields);

        $fields = getFieldsFromRequest($request, $form_fields);

        $fields['published_on'] = Carbon::createFromFormat('Y-m-d\TH:i', $fields['published_on'], $fields['timezone'])->tz('UTC');
        Auth::user()->update(['timezone' => $fields['timezone']]);
        unset($fields['timezone']);

        $fields['comic_id'] = $comic_id;

        // If has a new title or slug regenerate it
        if ((!isset($fields['slug']) && isset($fields['title']) && $chapter->title != $fields['title']) || (isset($fields['slug']) && $chapter->slug != $fields['slug'])) {
            $fields['slug'] = Chapter::generateSlug($fields);
        } else {
            unset($fields['slug']);
        }

        if($fields['views'] === null && $chapter->views !== null) unset($fields['views']);

        // Check if has a new path, then rename it
        $new_chapter = new Chapter;
        $new_chapter->slug = isset($fields['slug']) ? $fields['slug'] : $chapter->slug;
        $new_chapter->language = isset($fields['language']) ? $fields['language'] : null;
        $new_chapter->volume = isset($fields['volume']) ? $fields['volume'] : null;
        $new_chapter->chapter = isset($fields['chapter']) ? $fields['chapter'] : null;
        $new_chapter->subchapter = isset($fields['subchapter']) ? $fields['subchapter'] : null;
        $new_chapter->salt = $chapter->salt;
        $new_path = Chapter::path($comic, $new_chapter);
        if ($old_path !== $new_path) {
            Storage::move($old_path, $new_path);
        }
        unset($new_chapter);

        if ($fields['team2_id'] === '0') unset($fields['team2_id']);

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
        Storage::deleteDirectory(Chapter::path($comic, $chapter));
        Chapter::destroy($chapter_id);
        return redirect()->route('admin.comics.show', ['comic' => $comic->slug])->with('warning', 'Chapter "' . Chapter::name($comic, $chapter) . '" deleted');
    }
}
