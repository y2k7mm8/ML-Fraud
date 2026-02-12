<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\FraudDetectionController;

Route::match(['get', 'post'], '/check-transaction', [FraudDetectionController::class, 'checkTransaction']);