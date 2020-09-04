<?php

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

Route::namespace('Reader')->name('reader.')->group(function () {
    Route::get('/comics/', 'ReaderController@comics')->name('comics');
    Route::get('/comics/{comic}', 'ReaderController@comic')->name('comic');
    Route::get('/read/{comic}/{language}/{ch?}', 'ReaderController@chapter')->name('read')->where('ch', '.*');
});
