<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewTvController;
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

Route::redirect('/', '/'.env('APP_SSP_URL'));
Route::get('/{id}', [ViewTvController::class, 'view'])->name('view');
Route::post('/getWeatherData/', [ViewTvController::class, 'getWeatherData'])->name('tv.getWeatherData');
Route::post('/getWeatherData/', [ViewTvController::class, 'getWeatherData'])->name('tv.getWeatherData');
Route::get('/chk/getUpdatedData/', [ViewTvController::class, 'getUpdatedData'])->name('getUpdatedData');
Route::get('/chk/NewData/', [ViewTvController::class, 'NewData'])->name('getNewData');
