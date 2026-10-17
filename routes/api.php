<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API endpoints
Route::post('/orders/checkout', [OrderController::class, 'store']);
Route::get('/orders/{orderNumber}/status', [OrderController::class, 'checkStatus']);

// Webhook callback endpoint (API format)
Route::post('/payments/webhook', [PaymentCallbackController::class, 'handle']);
