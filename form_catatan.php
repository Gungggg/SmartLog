<?php
session_start();
require_once 'helpers/session_check.php';
require_once 'config/database.php';
require_once 'config/constants.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit;
}

// Inisialisasi Variabel (Default Kosong untuk Mode Tambah)
$mode = 'tambah';
$data = [
    'title' => '',
    'content' => '',
    'category_id' => '',
    'file_path' => ''
];

// Cek Mode Edit (Jika ada ID di URL)
if (isset($_GET['id'])) {
    $mode = 'edit';
    $id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Ambil data lama, pastikan milik user yang sedang login (Security)
    $query = mysqli_query($conn, "SELECT * FROM logs WHERE log_id = $id AND user_id = $user_id");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        // Jika iseng ubah ID di URL ke punya orang lain
        header("Location: dashboard.php");
        exit;
    }
}

// Ambil Daftar Kategori untuk Dropdown
$categories = mysqli_query($conn, "SELECT * FROM categories");

$pageTitle = ($mode == 'edit' ? 'Edit' : 'Tambah') . ' Catatan';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6 md:p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                <?= $mode == 'edit' ? '✏️ Edit Catatan' : '📝 Tulis Catatan Baru' ?>
            </h1>

            <form action="actions/<?= $mode == 'edit' ? 'note_update.php' : 'note_create.php' ?>" method="POST" enctype="multipart/form-data" class="space-y-6">

                <?php if ($mode == 'edit'): ?>
                    <input type="hidden" name="log_id" value="<?= $data['log_id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Catatan</label>
                    <input type="text" name="title" value="<?= $data['title'] ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition"
                        placeholder="Contoh: Ide Bisnis Kopi">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= $data['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
                                <?= $cat['category_name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Isi Konten</label>
                    <textarea name="content" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition" placeholder="Tulis detailnya di sini..."><?= $data['content'] ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lampirkan Gambar (Opsional)</label>
                    
                    <?php if (!empty($data['file_path'])): ?>
                        <div class="mb-2">
                            <img src="<?= BASE_URL ?>assets/uploads/<?= $data['file_path'] ?>" class="h-24 w-auto rounded border p-1">
                            <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t">
                    <a href="dashboard.php" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow transition">
                        <?= $mode == 'edit' ? 'Simpan Perubahan' : 'Simpan Catatan' ?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>