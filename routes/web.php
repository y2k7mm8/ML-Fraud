<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FraudDetectionController;

Route::get('/', function () {
    return view('fraud_detection');
});

Route::get('/fraud-detection', [FraudDetectionController::class, 'index']);

// The bootstrap configuration does not load routes/api.php, so we
// register the API endpoint here under the /api prefix so the
// frontend fetch() to /api/check-transaction will work.
Route::match(['get', 'post'], '/api/check-transaction', [FraudDetectionController::class, 'checkTransaction'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
