<?php

use App\Http\Controllers\Reader\ReaderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('reader.')->group(function () {
    Route::get('/comics/', [ReaderController::class, 'comics'])->name('comics');
    Route::get('/recommended/', [ReaderController::class, 'recommended'])->name('recommended');
    Route::get('/comics/{comic}', [ReaderController::class, 'comic'])->name('comic');
    Route::get('/read/{comic}/{language}/{ch?}', [ReaderController::class, 'chapter'])->name('read')->where('ch', '.*');
    Route::get('/download/{comic}/{language}/{ch?}', [ReaderController::class, 'download'])->name('download')->where('ch', '.*');
    Route::get('/pdf/{comic}/{language}/{ch?}', [ReaderController::class, 'pdf'])->name('pdf')->where('ch', '.*');
    Route::post('/vote/{comic}/{language}/{ch?}', [ReaderController::class, 'vote'])->name('vote')->where('ch', '.*')->middleware('web');
    Route::get('/search/{search}', [ReaderController::class, 'search'])->name('search');
    Route::get('/targets/{target}', [ReaderController::class, 'targets'])->name('targets');
    Route::get('/genres/{genre}', [ReaderController::class, 'genres'])->name('genres');
    Route::get('/info/', [ReaderController::class, 'info'])->name('info');
});
