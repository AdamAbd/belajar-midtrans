<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Route untuk menangani notifikasi/callback dari Midtrans
// Route API tidak menggunakan CSRF protection secara default
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])
    ->name('api.midtrans.callback');