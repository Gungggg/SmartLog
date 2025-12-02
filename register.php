<?php 
session_start();
require_once 'config/constants.php';
$pageTitle = 'Daftar Akun';
include 'includes/header.php'; 
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 to-purple-100 p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <div class="p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h1>
                <p class="text-gray-500 text-sm mt-1">Bergabunglah dengan SmartLog hari ini</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'username_taken'): ?>
                <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">
                    Username sudah digunakan, coba yang lain.
                </div>
            <?php endif; ?>

            <form action="helpers/auth_helper.php?action=register" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition" placeholder="Contoh: Budi Santoso">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition" placeholder="Username unik">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition" placeholder="Minimal 6 karakter">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-secondary hover:bg-emerald-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    Daftar Akun
                </button>
            </form>
        </div>

        <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Sudah punya akun? <a href="index.php" class="text-primary font-semibold hover:underline">Login disini</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>