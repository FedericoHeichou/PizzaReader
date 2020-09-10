<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\ComicController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('admin')->group(function () {
    Auth::routes();

    Route::name('admin.')->middleware('auth')->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.comics.index');
        })->name('index');

        Route::resource('comics', ComicController::class)->except(['index', 'show'])->middleware('auth.manager');

        Route::name('comics.')->group(function () {
            Route::get('comics', [ComicController::class, 'index'])->name('index')->middleware('auth.editor');
            Route::prefix('comics/{comic}')->middleware('can.edit')->group(function () {
                Route::get('', [ComicController::class, 'show'])->name('show');
                Route::resource('chapters', ChapterController::class)->except(['destroy']);
                Route::delete('chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy')->middleware('auth.manager');
                Route::post('chapters/{chapter}/pages', [PageController::class, 'store'])->name('chapters.pages.store');
                Route::delete('chapters/{chapter}/pages/{page}', [PageController::class, 'destroy'])->name('chapters.pages.destroy');
            });
        });

        Route::resource('users', UserController::class, ['except' => ['show']])->middleware('auth.admin');
    });
});

Route::get('/{reader?}', [HomeController::class, 'index'])->name('home')->where('reader', '.*');
