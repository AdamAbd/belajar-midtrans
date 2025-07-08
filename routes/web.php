<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk menampilkan halaman pembayaran
Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');

// Route untuk menangani notifikasi/callback dari Midtrans
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->name('midtrans.callback');