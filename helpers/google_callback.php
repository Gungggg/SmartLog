<?php
// helpers/google_callback.php

// 1. AKTIFKAN PELAPORAN ERROR AGAR KITA TAHU MASALAHNYA
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database.php';
require_once 'google_setup.php';

// Cek apakah ada code dari Google?
if (isset($_GET['code'])) {
    
    try {
        // 2. TUKAR KODE DENGAN TOKEN
        // Di Windows/Localhost, ini sering gagal karena sertifikat SSL.
        // Kita coba tangkap errornya.
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        // Cek jika response ada error
        if (isset($token['error'])) {
            throw new Exception("Gagal mendapatkan token: " . json_encode($token));
        }

        $client->setAccessToken($token['access_token']);
        
        // 3. AMBIL PROFIL USER
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Debugging: Tampilkan data yang didapat (Sementara)
        // echo "<pre>"; print_r($google_account_info); echo "</pre>"; exit; 

        $email = $google_account_info->email;
        $name = $google_account_info->name;
        $google_id = $google_account_info->id;
        $avatar = $google_account_info->picture;

        // 4. CEK DATABASE
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' OR google_id = '$google_id'");
        
        if (!$check) {
            throw new Exception("Error Query Select: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($check) > 0) {
            // == USER SUDAH ADA ==
            $user = mysqli_fetch_assoc($check);
            
            // Update data jika perlu
            $update = mysqli_query($conn, "UPDATE users SET google_id = '$google_id', avatar = '$avatar' WHERE user_id = {$user['user_id']}");
            if (!$update) throw new Exception("Error Update: " . mysqli_error($conn));

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['logged_in'] = true;
            
            // Redirect ke Dashboard
            header("Location: ../dashboard.php");
            exit;
            
        } else {
            // == USER BARU ==
            $username = explode('@', $email)[0] . rand(100, 999);
            $dummy_password = password_hash(uniqid(), PASSWORD_DEFAULT);

            // Perhatikan kolom email di sini
            $insert = "INSERT INTO users (full_name, username, password, email, google_id, avatar) VALUES ('$name', '$username', '$dummy_password', '$email', '$google_id', '$avatar')";
            
            if (mysqli_query($conn, $insert)) {
                $new_id = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $new_id;
                $_SESSION['full_name'] = $name;
                $_SESSION['logged_in'] = true;
                
                header("Location: ../dashboard.php?welcome=true");
                exit;
            } else {
                throw new Exception("Gagal Register User Baru: " . mysqli_error($conn));
            }
        }

    } catch (Exception $e) {
        // TAMPILKAN ERROR JIKA ADA
        echo "<div style='color:red; font-family:sans-serif; padding:20px;'>";
        echo "<h3>Terjadi Kesalahan!</h3>";
        echo "Pesan: " . $e->getMessage();
        echo "</div>";
        exit;
    }

} else {
    // Jika diakses langsung tanpa code
    header("Location: ../index.php");
    exit;
}
?>