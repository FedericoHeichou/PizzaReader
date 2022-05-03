<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChapterDownload;
use App\Models\Comic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ComicController extends Controller {
    public function index() {
        if (Auth::user()->hasPermission('manager') || Auth::user()->all_comics) {
            $comics = Comic::orderBy('name')->get();
        } else {
            $comics = Auth::user()->comics()->orderBy('name')->get();
        }
        return view('admin.comics.index')->with('comics', $comics);
    }

    public function create() {
        return view('admin.comics.create');
    }

    public function store(Request $request) {
        $fields = Comic::getFieldsIfValid($request);

        $fields['salt'] = Str::random();
        $fields['slug'] = Comic::generateSlug($fields);

        $comic = Comic::create($fields);
        $path = Comic::path($comic);
        Storage::makeDirectory($path);
        if (isset($fields['thumbnail'])) {
            $request->file('thumbnail')->storeAs($path, $comic->thumbnail);
            $this->storeSmall($request->file('thumbnail'), $path, $comic->thumbnail);
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

        $fields = Comic::getFieldsIfValid($request);

        // If has a new title or slug regenerate it
        if ((!isset($fields['slug']) && isset($fields['name']) && $comic->name != $fields['name']) || (isset($fields['slug']) && $comic->slug != $fields['slug'])) {
            $fields['slug'] = Comic::generateSlug($fields);
        } else {
            unset($fields['slug']);
        }

        // If has a new thumbnail delete the old one and store the new
        if (isset($fields['thumbnail'])) {
            if ($comic->thumbnail) {
                Storage::delete($old_path . '/' . $comic->thumbnail);
                Storage::delete($old_path . '/' . getSmallThumbnail($comic->thumbnail));
            }
            $path = Comic::path($comic);
            $request->file('thumbnail')->storeAs($path, $fields['thumbnail']);
            $this->storeSmall($request->file('thumbnail'), $path, $fields['thumbnail']);
        }

        // If has a new slug rename the directory
        if (isset($fields['slug']) && $comic->slug != $fields['slug']) {
            $new_comic = new Comic;
            $new_comic->slug = $fields['slug'];
            $new_comic->salt = $comic->salt;
            $new_path = Comic::path($new_comic);
            Storage::move($old_path, $new_path);
            $comic->slug = $fields['slug'];
        }

        // Check if we need to delete its zips
        if($comic->name != $fields['name']) {
            foreach ($comic->chapters as $chapter) {
                ChapterDownload::cleanDownload($chapter->download, $comic, $chapter, $chapter);
            }
        }

        Comic::where('id', $comic->id)->update($fields);
        return redirect()->route('admin.comics.show', $comic->slug)->with('success', 'Comic updated');
    }

    public function destroy($comic_id) {
        $comic = Comic::find($comic_id);
        if (!$comic) {
            abort(404);
        }
        Storage::deleteDirectory(Comic::path($comic));
        Comic::destroy($comic->id);
        return redirect()->route('admin.comics.index')->with('warning', 'Comic "' . $comic->name . '" and its chapters deleted');
    }

    public function search($search) {
        $response = ['comics' => []];

        $comics = Comic::fullSearch($search);
        foreach ($comics as $comic) {
            array_push($response['comics'], ['id' => $comic->id, 'name' => $comic->name]);
        }
        return response()->json($response);
    }

    public static function storeSmall($file, $path, $name, $new_width=150, $new_height=213) {
        $height = Image::make($file)->height();
        $width = Image::make($file)->width();
        if ($width > $height) $new_width = null;
        else $new_height = null;
        $file = Image::make($file)->resize($new_width, $new_height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $file->encode('jpg');
        $new_name = getSmallThumbnail($name);
        $file->save(storage_path("app/$path/$new_name"));
    }
}
