<?php

use App\Http\Controllers\ExchangeRatesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/exchange_rates', [ExchangeRatesController::class, 'index']);
Route::get('/exchange_rates/external', [ExchangeRatesController::class, 'fetchExchangeRates']);
Route::get('/exchange/convert', [ExchangeRatesController::class, 'fetchConvertedCurrency']);
Route::get('/historical/{base}/{to}', [ExchangeRatesController::class, 'get']);
Route::prefix('/exchange_rate')->group(function () {
    Route::post('/store', [ExchangeRatesController::class, 'store']);
    Route::put('/{id}', [ExchangeRatesController::class, 'update']);
    Route::delete('/{id}', [ExchangeRatesController::class, 'destroy']);
});