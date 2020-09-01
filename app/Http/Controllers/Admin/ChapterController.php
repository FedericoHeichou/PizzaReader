<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Comic;

class ChapterController extends Controller {
    public function index($comic_stub) {
        return redirect()->route('admin.comics.show', $comic_stub);
    }

    public function create() {

    }

    public function store(Request $request) {

    }

    public function show($chapter_id) {

    }

    public function edit($chapter_id) {

    }

    public function update(Request $request, $chapter_id) {

    }

    public function destroy($chapter_id) {

    }
}
