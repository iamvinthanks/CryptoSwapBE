<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//API route for register new user
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
});
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user/balance', [App\Http\Controllers\API\WalletController::class, 'CryptoBalance']);
    Route::post('/swap/confirmation', [App\Http\Controllers\API\CryposwapController::class, 'confirmationBeforeswap']);
    Route::post('/swap/crypto-to-idr', [App\Http\Controllers\API\CryposwapController::class, 'cryptoToidr']);
});
Route::post('/swap/troncallback', [App\Http\Controllers\API\CryposwapController::class, 'tronCallback']);
