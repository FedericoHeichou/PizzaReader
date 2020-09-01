<?php

namespace App\Http\Controllers\Admin;

use App\Comic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class ComicController extends Controller {
    public function index() {
        return view('admin.comics')->with('comics', Auth::user()->comics);
    }

    public function create() {
        return view('admin.comic.create');
    }

    public function store(Request $request) {

    }

    public function show($comic_stub) {
        $comic = Comic::stub($comic_stub);
        if (!$comic) {
            abort(404);
        }
        return view('admin.comic')->with(['comic' => $comic, 'chapters' => $comic->chapters]);
    }

    public function edit($comic_stub) {
        $comic = Comic::stub($comic_stub);
        if (!$comic) {
            abort(404);
        }
        return view('admin.comic.edit')->with('comic', $comic);
    }

    public function update(Request $request, $comic_id) {

    }

    public function destroy($comic_id) {

    }
}
