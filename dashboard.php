<?php
// 1. Cek Session (Hanya user login yang boleh akses)
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit;
}

// 2. Load Konfigurasi & Helper
require_once 'helpers/session_check.php';
require_once 'config/database.php';
require_once 'config/constants.php';

// 3. Ambil Data Catatan dari Database (Khusus user yang login)
$user_id = $_SESSION['user_id'];
$query = "SELECT logs.*, categories.category_name, categories.color_code 
            FROM logs 
            LEFT JOIN categories ON logs.category_id = categories.category_id 
            WHERE logs.user_id = $user_id 
            ORDER BY logs.created_at DESC";

$result = mysqli_query($conn, $query);

// 4. Setup Tampilan
$pageTitle = 'Dashboard';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Catatan Saya</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola ide dan aktivitas harianmu di sini.</p>
        </div>
        
        <a href="form_catatan.php" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Catatan Baru
        </a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col h-full group">
                    
                    <?php if (!empty($row['file_path'])): ?>
                        <div class="h-40 w-full overflow-hidden rounded-t-xl bg-gray-100 relative">
                            <img src="<?= BASE_URL ?>assets/uploads/<?= $row['file_path'] ?>" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                                onerror="this.style.display='none'" alt="Foto Catatan">
                        </div>
                    <?php endif; ?>

                    <div class="p-5 flex-1 flex flex-col">
                        
                        <div class="flex justify-between items-start mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $row['color_code'] ?? 'bg-gray-100' ?> text-gray-800">
                                <?= $row['category_name'] ?? 'Umum' ?>
                            </span>
                            <span class="text-xs text-gray-400">
                                <?= date('d M Y', strtotime($row['created_at'])) ?>
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1" title="<?= $row['title'] ?>">
                            <?= $row['title'] ?>
                        </h3>

                        <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-1">
                            <?= strip_tags($row['content']) ?>
                        </p>

                        <div class="mt-3 mb-4">
                            <?php if ($row['ai_status'] === 'completed' && !empty($row['ai_summary'])): ?>
                                <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 animate-fade-in">
                                    <p class="text-xs text-indigo-700 flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                                        <span>
                                            <b>AI Insight:</b> 
                                            <?= htmlspecialchars(substr($row['ai_summary'] ?? '', 0, 80)) ?>...
                                        </span>
                                    </p>
                                </div>
                            <?php else: ?>
                                <button onclick="generateAI(<?= $row['log_id'] ?>, this)" 
                                        class="w-full py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 group">
                                    <svg class="w-4 h-4 group-hover:animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    Generate AI Summary
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                            <a href="form_catatan.php?id=<?= $row['log_id'] ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Edit
                            </a>
                            
                            <a href="actions/note_delete.php?id=<?= $row['log_id'] ?>" class="text-sm text-red-500 hover:text-red-700 font-medium delete-btn">
                                Hapus
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
        </div>

    <?php else: ?>
        <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
            <div class="bg-gray-50 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Belum ada catatan</h3>
            <p class="text-gray-500 text-sm mt-1 mb-6">Mulai tulis ide cemerlang Anda sekarang.</p>
            <a href="form_catatan.php" class="px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-indigo-700 transition">
                Buat Catatan Pertama
            </a>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>