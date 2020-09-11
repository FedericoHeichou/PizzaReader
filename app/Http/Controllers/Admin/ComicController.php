<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Auth;

class ComicController extends Controller {
    public function index() {
        if (Auth::user()->hasPermission('manager')) {
            $comics = Comic::all();
        } else {
            $comics = Auth::user()->comics;
        }
        return view('admin.comics.index')->with('comics', $comics);
    }

    public function create() {
        return view('admin.comics.create');
    }

    public function store(Request $request) {
        $form_fields = Comic::getFormFieldsForValidation();
        $request->validate($form_fields);

        $fields = getFieldsFromRequest($request, $form_fields);
        $fields['salt'] = Str::random();
        $fields['slug'] = Comic::generateSlug($fields);
        if (isset($fields['thumbnail']) && $fields['thumbnail']) {
            $fields['thumbnail'] = preg_replace("/%/", "", $request->file('thumbnail')->getClientOriginalName());
        }

        if (isset($fields['genres']) && $fields['genres']) {
            $fields['genres'] = trimCommas($fields['genres']);
        }

        $comic = Comic::create($fields);
        $path = Comic::path($comic);
        Storage::makeDirectory($path);
        if (isset($fields['thumbnail']) && $fields['thumbnail']) {
            $request->file('thumbnail')->storeAs($path, $comic->thumbnail);
        }
        return redirect()->route('admin.comics.show', $comic->slug)->with('success', 'Comic created');
    }

    public function show($comic_slug) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        $comic->url = Comic::getUrl($comic);
        return view('admin.comics.show')->with(['comic' => $comic, 'chapters' => $comic->chapters]);
    }

    public function edit($comic_slug) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        return view('admin.comics.edit')->with('comic', $comic);
    }

    public function update(Request $request, $comic_id) {
        $comic = Comic::find($comic_id);
        if (!$comic) {
            abort(404);
        }
        $old_path = Comic::path($comic);
        $form_fields = Comic::getFormFieldsForValidation();
        $request->validate($form_fields);

        $fields = getFieldsFromRequest($request, $form_fields);

        // If has a new title or slug regenerate it
        if ((!isset($fields['slug']) && isset($fields['name']) && $comic->name != $fields['name']) || (isset($fields['slug']) && $comic->slug != $fields['slug'])) {
            $fields['slug'] = Comic::generateSlug($fields);
        } else {
            unset($fields['slug']);
        }

        if (isset($fields['thumbnail']) && $fields['thumbnail']) {
            $fields['thumbnail'] = preg_replace("/%/", "", $request->file('thumbnail')->getClientOriginalName());
            Storage::delete($old_path . '/' . $comic->thumbnail);
        } else { // Please don't delete the old thumbnail
            unset($fields['thumbnail']);
        }

        // If has a new slug rename the directory
        if (isset($fields['slug']) && $comic->slug != $fields['slug']) {
            $new_comic = new Comic;
            $new_comic->slug = $fields['slug'];
            $new_comic->salt = $comic->salt;
            $new_path = Comic::path($new_comic);
            Storage::move($old_path, $new_path);
        }

        if (isset($fields['genres']) && $fields['genres']) {
            $fields['genres'] = trimCommas($fields['genres']);
        }

        Comic::where('id', $comic_id)->update($fields);
        $comic = Comic::find($comic_id);
        if (isset($fields['thumbnail']) && $fields['thumbnail']) {
            $request->file('thumbnail')->storeAs(Comic::path($comic), $comic->thumbnail);
        }
        return redirect()->route('admin.comics.show', $comic->slug)->with('success', 'Comic updated');
    }

    public function destroy($comic_id) {
        $comic = Comic::find($comic_id);
        if (!$comic) {
            abort(404);
        }
        Storage::deleteDirectory(Comic::path($comic));
        Comic::destroy($comic_id);
        return redirect()->route('admin.comics.index')->with('warning', 'Comic "' . $comic->name . '" and its chapters deleted');
    }
}
