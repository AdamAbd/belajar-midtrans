<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{ config('app.midtrans.client_key') }}"></script>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        .card h1 { margin-top: 0; }
        .pay-button { background-color: #007bff; color: white; border: none; padding: 15px 30px; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 20px; }
        .pay-button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Detail Pembayaran</h1>
        <p><strong>Order ID:</strong> {{ $payment->order_id }}</p>
        <p><strong>Amount:</strong> Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
        <button id="pay-button" class="pay-button">Bayar Sekarang</button>
    </div>

    <script type="text/javascript">
      // Ambil tombol bayar
      var payButton = document.getElementById('pay-button');
      payButton.addEventListener('click', function () {
        // Panggil Snap API untuk membuka jendela pembayaran
        window.snap.pay('{{ $snapToken }}', {
          onSuccess: function(result){
            /* Anda bisa tambahkan logika di sini jika pembayaran sukses */
            alert("payment success!"); 
            console.log(result);
            // Redirect atau update UI
            window.location.href = '/'; // contoh redirect ke halaman utama
          },
          onPending: function(result){
            /* Anda bisa tambahkan logika di sini jika pembayaran pending */
            alert("waiting for your payment!"); 
            console.log(result);
          },
          onError: function(result){
            /* Anda bisa tambahkan logika di sini jika pembayaran gagal */
            alert("payment failed!"); 
            console.log(result);
          },
          onClose: function(){
            /* Anda bisa tambahkan logika di sini jika user menutup jendela pembayaran */
            alert('you closed the popup without finishing the payment');
          }
        })
      });
    </script>
</body>
</html>