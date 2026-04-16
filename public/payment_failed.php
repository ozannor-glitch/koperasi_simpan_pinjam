<?php
// public/payment_failed.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Gagal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #2E7D32 0%, #1B5E20 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .failed-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            max-width: 350px;
            margin: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .error-icon {
            width: 80px;
            height: 80px;
            background: #f44336;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .error-icon svg {
            width: 50px;
            height: 50px;
            color: white;
        }

        h1 {
            color: #f44336;
            margin-bottom: 10px;
        }

        p {
            color: #666;
            margin-bottom: 30px;
        }

        .button {
            background: #2E7D32;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
        }

        .button.secondary {
            background: #666;
        }
    </style>
</head>
<body>
    <div class="failed-card">
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="white">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
            </svg>
        </div>
        <h1>Pembayaran Gagal</h1>
        <p>Maaf, pembayaran Anda gagal diproses. Silakan coba lagi.</p>
        <button class="button" onclick="retry()">Coba Lagi</button>
        <button class="button secondary" onclick="closeApp()">Kembali</button>
    </div>

    <script>
        function retry() {
            window.location.href = 'midtrans_payment.php?<?php echo $_SERVER['QUERY_STRING']; ?>';
        }

        function closeApp() {
            if (window.flutter_inappwebview) {
                window.flutter_inappwebview.callHandler('closeWebView');
            } else {
                window.close();
            }
        }
    </script>
</body>
</html>
