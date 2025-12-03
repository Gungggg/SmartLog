<?php 
session_start();
if(isset($_SESSION['logged_in'])) {
    header("Location: dashboard.php");
    exit;
}
require_once 'config/constants.php';
require_once 'helpers/google_setup.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk / Daftar | SmartLog</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<video autoplay muted loop id="bg-video" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
    <source src="assets/video/bg.mp4" type="video/mp4">
    Browser Anda tidak mendukung video HTML5.
</video>
<div class="container" id="container">
    
    <div class="form-container sign-up-container">
    <form action="helpers/auth_helper.php?action=register" method="POST">
        <h1>Buat Akun</h1>
        
        <div class="social-container">
            <a href="<?= $google_login_url ?>" class="social" style="border-color: #db4437; color: #db4437;"><i class="fab fa-google"></i></a>
        </div>
        
        <span style="color: #999; margin-bottom: 15px;">atau gunakan email untuk pendaftaran</span>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'username_taken'): ?>
            <div class="alert alert-error">Username sudah digunakan!</div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'user_not_found'): ?>
            <div class="alert alert-error" style="background-color: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;">
                Akun belum terdaftar. Silakan buat baru!
            </div>
        <?php endif; ?>
        
        <input type="text" name="full_name" placeholder="Nama Lengkap" required />
        <input type="text" name="username" placeholder="Username" value="<?= isset($_GET['user']) ? htmlspecialchars($_GET['user']) : '' ?>" required />
        <input type="password" name="password" placeholder="Password" required />
        
        <button type="submit">Daftar</button>
    </form>
</div>

    <div class="form-container sign-in-container">
        <form action="helpers/auth_helper.php?action=login" method="POST">
            <h1>Masuk</h1>
            
            <div class="social-container">
                <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                <a href="<?= $google_login_url ?>" class="social" title="Login dengan Google" 
                style="border-color: #db4437; color: #db4437;">
                    <i class="fab fa-google"></i>
                </a>
                <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
            </div>
            
            <span style="color: #999; margin-bottom: 15px;">atau gunakan akun anda</span>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_login'): ?>
                <div class="alert alert-error">Username atau password salah!</div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] == 'session_expired'): ?>
                <div class="alert alert-error" style="background-color: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;">
                    Waktu habis! Silakan login ulang.
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="alert alert-success">Registrasi berhasil! Silakan login.</div>
            <?php endif; ?>

            <input type="text" name="username" placeholder="Username" required />
            <input type="password" name="password" placeholder="Password" required />
            
            <a href="#">Lupa password anda?</a>
            <button type="submit">Masuk</button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Selamat Datang!</h1>
                <p>Untuk tetap terhubung dengan kami, silakan login dengan info pribadi anda</p>
                <button class="ghost" id="signIn">Masuk</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Halo, Teman!</h1>
                <p>Masukkan detail pribadi anda dan mulailah perjalanan cerdas bersama SmartLog</p>
                <button class="ghost" id="signUp">Daftar</button>
            </div>
        </div>
    </div>
</div>

<div style="margin-top: 30px; text-align: center; color: #999; font-size: 14px;">
    &copy; 2025 SmartLog.
</div>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'register') {
        container.classList.add("right-panel-active");
    }
</script>

</body>
</html>