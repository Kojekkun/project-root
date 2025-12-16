<?php
// public/verification_success.php
require_once __DIR__ . '/../app/helpers.php';
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Berhasil</title>
    <meta http-equiv="refresh" content="5;url=login.php">
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f4f6;
            margin: 0;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        h2 { color: #1f2937; margin-bottom: 10px; }
        p { color: #6b7280; margin-bottom: 30px; }
        
        /* --- ANIMASI CENTANG (Checkmark) --- */
        .checkmark-wrapper {
            width: 80px; height: 80px; margin: 0 auto 20px auto;
            position: relative;
        }
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #22c55e; /* Warna Hijau */
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark {
            width: 80px; height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: white;
            stroke-miterlimit: 10;
            margin: 10% auto;
            box-shadow: inset 0px 0px 0px #22c55e;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill { 
            100% { box-shadow: inset 0px 0px 0px 50px #22c55e; } 
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="checkmark-wrapper">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>
        
        <h2>Verifikasi Berhasil!</h2>
        <p>Akun Anda telah aktif. Mengalihkan ke halaman login...</p>
        <a href="login.php" style="text-decoration:none; color:#2563eb; font-weight:bold;">Login Sekarang &rarr;</a>
    </div>
</body>
</html>