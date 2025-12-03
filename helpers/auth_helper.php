<?php
session_start();
require_once '../config/database.php'; // Memuat koneksi database

// Menangkap parameter 'action' dari URL (login atau register)
$action = $_GET['action'] ?? '';

if ($action === 'register') {
    // 1. Sanitasi Input (Mencegah karakter aneh merusak query)
    $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // 2. Cek apakah username sudah ada?
    $check_query = "SELECT username FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Error Handling: Username kembar
        header("Location: " . BASE_URL . "register.php?error=username_taken");
        exit;
    }

    // password_hash() membuat string acak yang unik setiap kali
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. Simpan ke Database
    $insert_query = "INSERT INTO users (full_name, username, password) VALUES ('$fullname', '$username', '$hashed_password')";
    
    if (mysqli_query($conn, $insert_query)) {
        header("Location: " . BASE_URL . "index.php?success=registered");
    } else {
        header("Location: " . BASE_URL . "register.php?error=system_fail");
    }
}

elseif ($action === 'login') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // 1. Cari user berdasarkan username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 0) {
        header("Location: ../index.php?action=register&error=user_not_found&user=" . urlencode($username));
        exit;
    }

    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password'])) {
        // Password Benar -> Login Sukses
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['logged_in'] = true;
        header("Location: ../dashboard.php");
        exit;
    } else {
        // Password Salah -> Tetap di Login
        header("Location: ../index.php?error=invalid_password");
        exit;
    }
    }
?>