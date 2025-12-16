<?php require_once __DIR__ . '/../app/helpers.php'; ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar Akun - Travel Buddies</title>
    <link rel="stylesheet" href="assets/css/style.css?v=16">
</head>
<body class="auth-body">
    
    <div class="auth-card" style="max-width: 450px;">
        
        <h2 class="auth-title">Join Us!</h2>
        <p class="auth-subtitle">Buat akun baru dan mulai jelajahi dunia.</p>
        
        <?php if($m=flash_get('error')): ?>
            <div class="alert alert-danger"><?= e($m) ?></div>
        <?php endif; ?>

        <form method="post" action="register_handler.php" id="registerForm" style="text-align:left;">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            
            <label>Nama Lengkap</label>
            <input name="name" type="text" required placeholder="Contoh: John Doe">
            
            <label>Email Address</label>
            <input name="email" type="email" id="emailInput" required placeholder="nama@email.com">
            <small id="emailFeedback" style="display:block; margin-top:-15px; margin-bottom:15px; font-size:0.85rem; height: 18px;"></small>

            <label>Password</label>
            <input name="password" type="password" minlength="6" required placeholder="Minimal 6 karakter">
            
            <button class="btn" id="btnSubmit" style="width:100%; margin-top:10px; border-radius:50px; padding:14px; font-size:1rem;">
                Buat Akun Baru
            </button>
        </form>
        
        <div style="margin-top: 25px; font-size: 0.9rem; color: var(--secondary);">
            Sudah punya akun? <a href="login.php" style="color:var(--accent); font-weight:700;">Login disini</a>
            <br><br>
            <a href="index.php" style="color: var(--text-main); text-decoration: none; font-weight:500;">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        const emailInput = document.getElementById('emailInput');
        const feedback = document.getElementById('emailFeedback');
        const btnSubmit = document.getElementById('btnSubmit');
        let timeout = null;

        emailInput.addEventListener('input', function() {
            const email = this.value.trim();
            feedback.textContent = '';
            emailInput.style.borderColor = '#e2e8f0'; // Warna border default
            
            // Validasi dasar client-side
            if (email.length < 5 || !email.includes('@')) return;
            
            // Debounce (tunggu user selesai mengetik)
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                feedback.textContent = 'Memeriksa ketersediaan...';
                feedback.style.color = '#64748b';
                
                fetch('api_check_email.php?email=' + encodeURIComponent(email))
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'taken') {
                            feedback.textContent = '❌ Email ini sudah terdaftar. Gunakan yang lain.';
                            feedback.style.color = '#ef4444'; // Merah
                            emailInput.style.borderColor = '#ef4444';
                            btnSubmit.disabled = true; 
                            btnSubmit.style.opacity = '0.5';
                        } else {
                            feedback.textContent = '✅ Email tersedia.';
                            feedback.style.color = '#16a34a'; // Hijau
                            emailInput.style.borderColor = '#16a34a';
                            btnSubmit.disabled = false; 
                            btnSubmit.style.opacity = '1';
                        }
                    })
                    .catch(err => {
                        console.error('Error checking email:', err);
                        feedback.textContent = '';
                    });
            }, 500);
        });
    </script>
</body>
</html>