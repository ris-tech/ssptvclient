<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ViewTvController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TvController;
use App\Http\Controllers\SlideController;
use App\Jobs\getWeather;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::redirect('/', '/'.env('APP_SSP_URL'));
Route::get('/{id}', [ViewTvController::class, 'view'])->name('view');
Route::post('/getWeatherData/', [ViewTvController::class, 'getWeatherData'])->name('tv.getWeatherData');

Route::get('/getWeather/', function() {

    getWeather::dispatch();
});
