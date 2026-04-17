<?php
// public/payment_success.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
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

        .success-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            max-width: 350px;
            margin: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .check-icon {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .check-icon svg {
            width: 50px;
            height: 50px;
            color: white;
        }

        h1 {
            color: #2E7D32;
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
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="check-icon">
            <svg viewBox="0 0 24 24" fill="white">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
            </svg>
        </div>
        <h1>Pembayaran Berhasil!</h1>
        <p>Setoran Anda telah kami terima. Saldo akan segera bertambah.</p>
        <button class="button" onclick="closeApp()">Kembali ke Aplikasi</button>
    </div>

    <script>
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
