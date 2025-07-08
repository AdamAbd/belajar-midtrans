<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('app.midtrans.server_key');
        Config::$isProduction = config('app.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function show()
    {
        // 1. Buat record pembayaran di database dengan status 'pending'
        $payment = Payment::create([
            'order_id' => 'ORDER-' . uniqid(), // Buat order ID unik
            'amount'   => 15000, // Contoh jumlah pembayaran
            'status'   => 'pending',
        ]);

        // 2. Siapkan data untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $payment->order_id,
                'gross_amount' => $payment->amount,
            ],
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '081234567890',
            ],
        ];

        // 3. Dapatkan Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);

        // 4. Simpan Snap Token ke database
        $payment->snap_token = $snapToken;
        $payment->save();

        // 5. Kirim Snap Token ke view
        return view('payment', compact('snapToken', 'payment'));
    }

    public function callback(Request $request)
    {
        $serverKey = config('app.midtrans.server_key');
        
        // 1. Validasi signature key
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if ($hashed != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }
        
        // 2. Cari pembayaran berdasarkan order_id
        $payment = Payment::where('order_id', $request->order_id)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
        
        // 3. Update status pembayaran
        if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
            $payment->status = 'success';
        } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny') {
            $payment->status = 'failed';
        }

        $payment->payment_method = $request->payment_type;
        $payment->save();

        // Beri respons OK ke Midtrans
        return response()->json(['message' => 'Payment status updated successfully']);
    }
}
