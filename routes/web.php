<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScoreController;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::resource('users', UserController::class)->middleware('auth');

Route::get('/', function () {
    return view('dashboard', ['content' => 'Dashboard']);
})->middleware('auth');

// Route::get('/scoring', ScoreController::class)->middleware('auth');
// Route::get('/scoring/{npwp}', [ScoreController::class, 'show'])->middleware('auth');
// Route::post('/scoring/store', [ScoreController::class, 'store'])->middleware('auth');
Route::controller(ScoreController::class)->group(function () {
    Route::get('/scoring', 'index')->name('scoring_list');
    Route::get('/scoring/{npwp}', 'show')->name('scoring_show');
    Route::post('/scoring/store/{npwp}', 'store')->name('scoring_store');
})->middleware('auth');
