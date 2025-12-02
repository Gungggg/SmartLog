<?php
session_start();
require_once '../config/database.php';

// Validasi Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../dashboard.php");
    exit;
}

// 1. Ambil Data dari Form
$log_id      = (int) $_POST['log_id'];
$user_id     = $_SESSION['user_id'];
$title       = mysqli_real_escape_string($conn, $_POST['title']);
$content     = mysqli_real_escape_string($conn, $_POST['content']);
$category_id = (int) $_POST['category_id'];

// 2. Cek apakah Data ini valid milik User?
$check = mysqli_query($conn, "SELECT file_path FROM logs WHERE log_id = $log_id AND user_id = $user_id");
if (mysqli_num_rows($check) === 0) {
    die("Akses ditolak! Anda tidak memiliki izin mengedit catatan ini.");
}
$old_data = mysqli_fetch_assoc($check);

// 3. Logika Upload Gambar Baru (Jika Ada)
$update_image_query = ""; 

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $file       = $_FILES['image'];
    $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed    = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array(strtolower($ext), $allowed)) {
        // Hapus gambar lama jika ada
        if ($old_data['file_path'] && file_exists('../assets/uploads/' . $old_data['file_path'])) {
            unlink('../assets/uploads/' . $old_data['file_path']);
        }

        // Upload gambar baru
        $new_name = time() . '_' . $user_id . '_' . rand(100, 999) . '.' . $ext;
        $destination = '../assets/uploads/' . $new_name;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $update_image_query = ", file_path = '$new_name'";
        }
    }
}

$query = "UPDATE logs SET 
            title = '$title', 
            content = '$content', 
            category_id = '$category_id',
            ai_status = 'none', 
            ai_summary = NULL 
            $update_image_query 
          WHERE log_id = $log_id AND user_id = $user_id";

if (mysqli_query($conn, $query)) {
    header("Location: ../dashboard.php?msg=updated");
} else {
    echo "Gagal mengupdate data: " . mysqli_error($conn);
}
?>