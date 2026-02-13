<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FraudDetectionController;

Route::get('/', function () {
    return view('fraud_detection');
});

Route::get('/fraud-detection', [FraudDetectionController::class, 'index']);


Route::match(['get', 'post'], '/api/check-transaction', [FraudDetectionController::class, 'checkTransaction'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
