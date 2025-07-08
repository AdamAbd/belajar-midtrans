<?php

/**
 * Script sederhana untuk menguji apakah CSRF sudah dinonaktifkan untuk callback Midtrans
 * 
 * Cara menggunakan:
 * 1. Jalankan server Laravel: php artisan serve
 * 2. Jalankan script ini: php test_csrf.php
 * 3. Periksa response yang diterima
 */

// Data simulasi callback dari Midtrans
$callbackData = [
    'order_id' => 'ORDER-test123',
    'status_code' => '200',
    'gross_amount' => '15000.00',
    'signature_key' => 'dummy_signature', // Ini akan gagal validasi, tapi untuk test CSRF sudah cukup
    'transaction_status' => 'settlement',
    'payment_type' => 'credit_card'
];

// URL callback endpoint (menggunakan API route)
$url = 'http://localhost:8000/midtrans/callback';

// Inisialisasi cURL
$ch = curl_init();

// Set opsi cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($callbackData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Eksekusi request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Tutup cURL
curl_close($ch);

// Tampilkan hasil
echo "=== TEST CSRF UNTUK MIDTRANS CALLBACK ===\n";
echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response: $response\n";
}

// Interpretasi hasil
echo "\n=== INTERPRETASI HASIL ===\n";
if ($httpCode == 419) {
    echo "❌ CSRF masih aktif! Response 419 menandakan CSRF token mismatch.\n";
    echo "   Periksa kembali konfigurasi VerifyCsrfToken.php\n";
} elseif ($httpCode == 403) {
    echo "✅ CSRF sudah dinonaktifkan! Response 403 karena signature tidak valid (expected).\n";
    echo "   Ini normal karena kita menggunakan dummy signature untuk testing.\n";
} elseif ($httpCode == 404) {
    echo "⚠️  Route tidak ditemukan. Pastikan route /midtrans/callback sudah didefinisikan.\n";
} elseif ($httpCode == 500) {
    echo "⚠️  Server error. Periksa log Laravel untuk detail error.\n";
} else {
    echo "ℹ️  HTTP Code: $httpCode - Periksa response untuk detail lebih lanjut.\n";
}

echo "\n=== CATATAN ===\n";
echo "- Pastikan server Laravel berjalan di http://localhost:8000\n";
echo "- Jika mendapat error 'Connection refused', jalankan: php artisan serve\n";
echo "- Response 403 dengan pesan 'Invalid signature' menandakan CSRF sudah dinonaktifkan\n";