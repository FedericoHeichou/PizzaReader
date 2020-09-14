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
