<?php
session_start();
require_once '../config/database.php';

// Cek ID di URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // 1. Ambil nama file gambar dulu (untuk dihapus dari folder)
    $query_file = mysqli_query($conn, "SELECT file_path FROM logs WHERE log_id = $id AND user_id = $user_id");
    $data = mysqli_fetch_assoc($query_file);

    if ($data) {
        // Hapus file fisik jika ada
        if ($data['file_path'] && file_exists('../assets/uploads/' . $data['file_path'])) {
            unlink('../assets/uploads/' . $data['file_path']);
        }

        // 2. Hapus data dari database
        $query_delete = "DELETE FROM logs WHERE log_id = $id AND user_id = $user_id";
        mysqli_query($conn, $query_delete);
    }
}

// Kembali ke Dashboard
header("Location: ../dashboard.php?msg=deleted");
?>