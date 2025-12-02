<?php
session_start();
require_once '../config/database.php';

// Pastikan hanya request POST yang diproses
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboard.php");
    exit;
}

$user_id     = $_SESSION['user_id'];
$title       = mysqli_real_escape_string($conn, $_POST['title']);
$content     = mysqli_real_escape_string($conn, $_POST['content']);
$category_id = (int) $_POST['category_id'];

$file_name_db = null; // Default null jika tidak ada gambar

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $file       = $_FILES['image'];
    $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed    = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Cek ekstensi file
    if (in_array(strtolower($ext), $allowed)) {
        // Generate nama unik (biar tidak bentrok)
        // Format: time_userid_random.jpg
        $new_name = time() . '_' . $user_id . '_' . rand(100, 999) . '.' . $ext;
        $destination = '../assets/uploads/' . $new_name;

        // Pindahkan file ke folder tujuan
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $file_name_db = $new_name;
        }
    }
}

$query = "INSERT INTO logs (user_id, category_id, title, content, file_path, ai_status) 
          VALUES ('$user_id', '$category_id', '$title', '$content', " . ($file_name_db ? "'$file_name_db'" : "NULL") . ", 'none')";

if (mysqli_query($conn, $query)) {
    // Sukses
    header("Location: ../dashboard.php?msg=created");
} else {
    // Gagal (Tampilkan error database untuk debugging sementara)
    echo "Error: " . mysqli_error($conn);
}
?>