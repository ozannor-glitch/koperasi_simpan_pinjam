<?php
// public/midtrans_payment.php

require_once '../vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

// Konfigurasi Midtrans
Config::$serverKey = 'SB-Mid-server-t_e68ioFniL793lNAAPHwN__';
Config::$isProduction = false;
Config::$isSanitized = true;
Config::$is3ds = true;

// Ambil parameter dari URL
$orderId = $_GET['order_id'] ?? '';
$amount = $_GET['amount'] ?? 0;
$name = $_GET['name'] ?? 'Member';
$email = $_GET['email'] ?? 'member@koperasi.com';
$savingTypeId = $_GET['saving_type_id'] ?? 1;
$transactionId = $_GET['transaction_id'] ?? 0;

// Gunakan URL dari environment
$appUrl = getenv('APP_URL') ?: 'https://grower-immersion-diploma.ngrok-free.dev';

if (empty($orderId) || $amount <= 0) {
    die('Parameter tidak lengkap');
}

// Parameter transaksi dengan webhook URL
$params = array(
    'transaction_details' => array(
        'order_id' => $orderId,
        'gross_amount' => $amount,
    ),
    'customer_details' => array(
        'first_name' => $name,
        'email' => $email,
    ),
    'item_details' => array(
        array(
            'id' => 'saving_' . $savingTypeId,
            'price' => $amount,
            'quantity' => 1,
            'name' => 'Setoran Tabungan Koperasi',
        ),
    ),
    // Webhook URL - Midtrans akan memanggil endpoint ini
    'callbacks' => array(
        'finish' => $appUrl . '/api/payment/success?order_id=' . $orderId . '&transaction_id=' . $transactionId,
        'error' => $appUrl . '/api/payment/failed?order_id=' . $orderId . '&transaction_id=' . $transactionId,
    ),
    // Expiry setting (opsional)
    'expiry' => array(
        'start_time' => date('Y-m-d H:i:s O'),
        'unit' => 'hours',
        'duration' => 24
    ),
);


try {
    $snapToken = Snap::getSnapToken($params);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Pembayaran - Koperasi Digital</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                background: #f5f5f5;
                min-height: 100vh;
            }

            .container {
                max-width: 500px;
                margin: 0 auto;
                padding: 20px;
                min-height: 100vh;
                display: flex;
            }

            .payment-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0,0,0,0.1);
                width: 100%;
            }

            .header {
                background: linear-gradient(135deg, #2E7D32 0%, #1B5E20 100%);
                color: white;
                padding: 30px 20px;
                text-align: center;
            }

            .header h1 {
                font-size: 24px;
                margin-bottom: 8px;
            }

            .header .amount {
                font-size: 32px;
                font-weight: bold;
                margin-top: 10px;
            }

            .content {
                padding: 20px;
            }

            .info-box {
                background: #f5f5f5;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 20px;
            }

            .info-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
            }

            .info-label {
                color: #666;
                font-size: 14px;
            }

            .info-value {
                font-weight: 500;
                color: #333;
            }

            .payment-methods {
                margin-bottom: 20px;
            }

            .payment-methods h3 {
                font-size: 16px;
                margin-bottom: 12px;
                color: #333;
            }

            .method-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .method-item {
                display: flex;
                align-items: center;
                padding: 12px;
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s;
            }

            .method-item:hover {
                border-color: #2E7D32;
                background: #f0f8f0;
            }

            .method-item.selected {
                border-color: #2E7D32;
                background: #e8f5e9;
            }

            .method-icon {
                width: 40px;
                height: 40px;
                background: #f0f0f0;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
            }

            .method-info {
                flex: 1;
            }

            .method-name {
                font-weight: 500;
                color: #333;
            }

            .method-desc {
                font-size: 12px;
                color: #666;
            }

            .radio {
                width: 20px;
                height: 20px;
                border: 2px solid #ddd;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .radio.selected {
                border-color: #2E7D32;
            }

            .radio.selected::after {
                content: '';
                width: 12px;
                height: 12px;
                background: #2E7D32;
                border-radius: 50%;
            }

            .pay-button {
                width: 100%;
                background: #2E7D32;
                color: white;
                border: none;
                padding: 16px;
                border-radius: 12px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s;
                margin-bottom: 12px;
            }

            .pay-button:hover {
                background: #1B5E20;
                transform: translateY(-2px);
            }

            .pay-button:disabled {
                background: #ccc;
                cursor: not-allowed;
                transform: none;
            }

            .loading {
                display: none;
                text-align: center;
                padding: 20px;
            }

            .loading.active {
                display: block;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid #2E7D32;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 10px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .back-button {
                display: block;
                text-align: center;
                color: #666;
                text-decoration: none;
                font-size: 14px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="payment-card">
                <div class="header">
                    <h1>Pembayaran Setoran</h1>
                    <p>Koperasi Digital</p>
                    <div class="amount">Rp <?php echo number_format($amount, 0, ',', '.'); ?></div>
                </div>

                <div class="content">
                    <div class="info-box">
                        <div class="info-row">
                            <span class="info-label">No. Order</span>
                            <span class="info-value"><?php echo htmlspecialchars($orderId); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nama</span>
                            <span class="info-value"><?php echo htmlspecialchars($name); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($email); ?></span>
                        </div>
                    </div>

                    <div class="payment-methods">
                        <h3>Pilih Metode Pembayaran</h3>
                        <div class="method-list">
                            <div class="method-item" data-method="bank_transfer">
                                <div class="method-icon">
                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#2E7D32">
                                        <path d="M4 10h16v2H4zM6 14h4v2H6zM14 14h4v2h-4zM12 6h8v2h-8z"/>
                                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12z"/>
                                    </svg>
                                </div>
                                <div class="method-info">
                                    <div class="method-name">Transfer Bank</div>
                                    <div class="method-desc">BCA, Mandiri, BNI, BRI</div>
                                </div>
                                <div class="radio"></div>
                            </div>
                            <div class="method-item" data-method="qris">
                                <div class="method-icon">
                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#2E7D32">
                                        <path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM13 13h8v8h-8z"/>
                                    </svg>
                                </div>
                                <div class="method-info">
                                    <div class="method-name">QRIS</div>
                                    <div class="method-desc">Scan QR Code</div>
                                </div>
                                <div class="radio"></div>
                            </div>
                            <div class="method-item" data-method="gopay">
                                <div class="method-icon">
                                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#2E7D32">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                </div>
                                <div class="method-info">
                                    <div class="method-name">GoPay</div>
                                    <div class="method-desc">Dari aplikasi Gojek</div>
                                </div>
                                <div class="radio"></div>
                            </div>
                        </div>
                    </div>

                    <button class="pay-button" id="payButton" disabled>Pilih Metode Pembayaran</button>

                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <p>Memproses pembayaran...</p>
                    </div>

                    <a href="#" class="back-button" onclick="closeWebView()">← Kembali ke Aplikasi</a>
                </div>
            </div>
        </div>

        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-KPbY-DSY9M-Cgzco"></script>
        <script>
            let snapToken = '<?php echo $snapToken; ?>';
            let selectedMethod = null;
            let paymentCompleted = false;

            // Pilih metode pembayaran
            document.querySelectorAll('.method-item').forEach(item => {
                item.addEventListener('click', function() {
                    if (paymentCompleted) return;

                    document.querySelectorAll('.method-item').forEach(m => m.classList.remove('selected'));
                    document.querySelectorAll('.radio').forEach(r => r.classList.remove('selected'));

                    this.classList.add('selected');
                    this.querySelector('.radio').classList.add('selected');

                    selectedMethod = this.dataset.method;
                    document.getElementById('payButton').disabled = false;
                    document.getElementById('payButton').innerText = `Bayar dengan ${getMethodName(selectedMethod)}`;
                });
            });

            function getMethodName(method) {
                const names = {
                    'bank_transfer': 'Transfer Bank',
                    'qris': 'QRIS',
                    'gopay': 'GoPay'
                };
                return names[method] || method;
            }

            // Proses pembayaran
            document.getElementById('payButton').addEventListener('click', function() {
                if (paymentCompleted) return;

                document.getElementById('loading').classList.add('active');
                document.getElementById('payButton').disabled = true;

                // Panggil Snap dengan metode yang dipilih
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Payment Success:', result);
                        paymentCompleted = true;
                        // Redirect ke success page
                        window.location.href = '/api/payment/success?order_id=<?php echo $orderId; ?>&transaction_id=<?php echo $transactionId; ?>';
                    },
                    onPending: function(result) {
                        console.log('Payment Pending:', result);
                        paymentCompleted = true;
                        window.location.href = '/api/payment/success?order_id=<?php echo $orderId; ?>&transaction_id=<?php echo $transactionId; ?>';
                    },
                    onError: function(result) {
                        console.log('Payment Error:', result);
                        paymentCompleted = true;
                        window.location.href = '/api/payment/failed?order_id=<?php echo $orderId; ?>&transaction_id=<?php echo $transactionId; ?>';
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        if (!paymentCompleted) {
                            document.getElementById('loading').classList.remove('active');
                            document.getElementById('payButton').disabled = false;
                        }
                    }
                });
            });

            function closeWebView() {
                // Kirim pesan ke Flutter untuk menutup WebView
                if (window.flutter_inappwebview) {
                    window.flutter_inappwebview.callHandler('closeWebView');
                } else if (window.ReactNativeWebView) {
                    window.ReactNativeWebView.postMessage(JSON.stringify({type: 'close'}));
                } else {
                    window.close();
                }
            }
        </script>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    echo '<div style="padding:20px;text-align:center;font-family:sans-serif;">';
    echo '<h2 style="color:#f44336;">Error</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#2E7D32;color:white;border:none;border-radius:8px;">Tutup</button>';
    echo '</div>';
}
?>
