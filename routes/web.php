<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\ComicController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// The "+" near the role means "which has this privilege or more" [admin > manager > editor > checker > user]

Route::prefix('admin')->group(function () {
    Auth::routes();

    Route::name('admin.')->middleware('auth')->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.comics.index');
        })->name('index');

        // Only managers+ can create, store, edit, update and destroy comics
        Route::resource('comics', ComicController::class)->except(['index', 'show'])->middleware('auth.manager');

        Route::name('comics.')->group(function () {
            // Only checkers+ can see list of chapter
            Route::get('comics', [ComicController::class, 'index'])->name('index')->middleware('auth.checker');

            Route::prefix('comics/{comic}')->group(function () {
                // Authorized checkers+ can see a comic
                Route::get('', [ComicController::class, 'show'])->name('show')->middleware('can.see');

                // Authorized editors+ can create, store, edit and update chapters
                Route::resource('chapters', ChapterController::class)->except(['destroy', 'show', 'index'])->middleware('can.edit');

                // Authorized checkers+ can see the list of comic's chapters and his chapters
                Route::resource('chapters', ChapterController::class)->only(['show', 'index'])->middleware('can.see');

                // Only managers+ can destroy chapters
                Route::delete('chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy')->middleware('auth.manager');

                // Only editors+ can store and destroy pages
                Route::resource('chapters/{chapter}/pages', PageController::class)->only(['store', 'destroy'])->names('chapters.pages')->middleware('can.edit');
                /*Route::post('chapters/{chapter}/pages', [PageController::class, 'store'])->name('chapters.pages.store')->middleware('can.edit');
                Route::delete('chapters/{chapter}/pages/{page}', [PageController::class, 'destroy'])->name('chapters.pages.destroy')->middleware('can.edit');*/
            });
        });

        Route::prefix('users')->name('users.')->group(function(){
            Route::get('/', [UserController::class, 'index'])->name('index')->middleware('auth.manager');
            Route::redirect('/{user}', '/admin/users');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('auth.admin');
            Route::patch('/{user}/update', [UserController::class, 'update'])->name('update')->middleware('auth.admin');
            Route::delete('/{user}/destroy', [UserController::class, 'destroy'])->name('destroy')->middleware('auth.admin');
        });
    });
});

Route::prefix('user')->name('user.')->middleware('auth')->group(function() {
    Route::get('/edit', [UserController::class, 'editYourself'])->name('edit');
    Route::patch('/update', [UserController::class, 'updateYourself'])->name('update');
});

Route::get('/{reader?}', HomeController::class)->name('home')->where('reader', '.*');
