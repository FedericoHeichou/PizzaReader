<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Comic;
use App\Chapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterController extends Controller {
    public function index($comic_slug) {
        return redirect()->route('admin.comics.show', $comic_slug)->with('success', 'prova');
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
        return view('admin.comics.chapters.show')->with(['comic' => $comic, 'chapter' => $chapter, 'pages' => Chapter::getAllPagesForFileUpload($comic, $chapter)]);
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
        $old_path = 'public/' . Chapter::buildPath($comic, $chapter);
        $form_fields = Chapter::getFormFieldsForValidation();
        $request->validate($form_fields);

        $fields = getFieldsFromRequest($request, $form_fields);

        $fields['comic_id'] = $comic_id;

        // If has a new slug regenerate it
        if (!isset($fields['slug']) || $chapter->slug != $fields['slug']) {
            $fields['slug'] = Chapter::generateSlug($fields);
        }

        // If has a new slug rename the directory
        if ($chapter->slug != $fields['slug']) {
            $new_chapter = new Chapter;
            $new_chapter->slug = $fields['slug'];
            $new_chapter->salt = $chapter->salt;
            $new_path = 'public/' . Chapter::buildPath($comic, $new_chapter);
            Storage::move($old_path, $new_path);
        }

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
        return redirect()->route('admin.comics.show', ['comic' => $comic->slug])->with('warning', 'Chapter "' . Chapter::name($chapter) . '" deleted');
    }
}
