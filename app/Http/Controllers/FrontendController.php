<?php

namespace App\Http\Controllers;

class FrontendController extends Controller {
    public function __invoke(){
        return view('layouts.reader');
    }
}
