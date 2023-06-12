<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\GudangBerikatController;

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

// Route menu scoring
Route::get('/scoring', [ScoreController::class, 'index'])->middleware('auth');
Route::get('/scoring/komoditi/{npwp}', [ScoreController::class, 'komoditi'])->middleware('auth');
Route::get('/scoring/pemasok/{npwp}', [ScoreController::class, 'pemasok'])->middleware('auth');
Route::get('/scoring/tonaseCIF/{npwp}', [ScoreController::class, 'tonaseCIF'])->middleware('auth');
Route::post('/scoring/store_komoditi/{npwp}', [ScoreController::class, 'store_komoditi'])->middleware('auth');
Route::put('/scoring/update/{npwp}', [ScoreController::class, 'update'])->middleware('auth');

// Route menu gudang berikat
Route::get('/gudang_berikat', [GudangBerikatController::class, 'index'])->middleware('auth');
Route::get('/gudang_berikat/komoditi/{npwp}', [ScoreController::class, 'komoditi'])->middleware('auth');
Route::get('/gudang_berikat/pemasok/{npwp}', [ScoreController::class, 'pemasok'])->middleware('auth');
Route::get('/gudang_berikat/tonaseCIF/{npwp}', [ScoreController::class, 'tonaseCIF'])->middleware('auth');
