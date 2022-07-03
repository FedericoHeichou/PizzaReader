<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/manifest.json', function () { return response(view('manifest'))->header('Content-Type', 'application/json; charset=UTF-8'); });
Route::get('/sitemap.xml', function () { return response(view('sitemap'))->header('Content-Type', 'text/xml; charset=UTF-8'); });
Route::get('/cron.php', function () { \Illuminate\Support\Facades\Artisan::call('schedule:run'); return response('', $status = 204); });
Route::get('/{frontend?}', FrontendController::class)->name('home')->where('frontend', '(comics|read|targets|genres|recommended).*');
