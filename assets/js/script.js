$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg) {
        let title, text, icon;

        switch(msg) {
            case 'created':
                title = 'Berhasil!';
                text = 'Catatan baru telah dibuat.';
                icon = 'success';
                break;
            case 'updated':
                title = 'Disimpan!';
                text = 'Perubahan catatan berhasil disimpan.';
                icon = 'success';
                break;
            case 'deleted':
                title = 'Terhapus!';
                text = 'Catatan telah dihapus permanen.';
                icon = 'success';
                break;
        }

        if (title) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                timer: 3000,
                showConfirmButton: false
            });
            
            // Bersihkan URL agar kalau di-refresh notifikasi tidak muncul lagi
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }

    $('.delete-btn').on('click', function(e) {
        e.preventDefault(); // Matikan link asli
        const href = $(this).attr('href'); // Ambil link hapusnya

        Swal.fire({
            title: 'Yakin hapus catatan ini?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Warna Merah Tailwind
            cancelButtonColor: '#6b7280', // Warna Abu
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user klik Ya, arahkan ke link hapus
                window.location.href = href;
            }
        });
    });
});
function generateAI(id, btn) {
    // Simpan teks asli tombol untuk jaga-jaga kalau error
    const originalContent = btn.innerHTML;
    btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sedang Berpikir...`;
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    });
    Toast.fire({ icon: 'info', title: 'Menghubungi AI...' });

    // 3. Panggil API Backend
    fetch('api/summarize.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;

        if (!response.ok) {
            const error = (data && data.message) || response.statusText;
            throw new Error(error);
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            // 4. SUKSES: Ganti Tombol dengan Hasil
            const summaryHTML = `
                <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 animate-fade-in">
                    <p class="text-xs text-indigo-700 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                        <span>${data.summary}</span>
                    </p>
                </div>
            `;
            btn.outerHTML = summaryHTML;
            Toast.fire({ icon: 'success', title: 'Selesai!' });

        } else {
            throw new Error(data.message || 'Gagal memproses data.');
        }
    })
    .catch(error => {
        console.error('Error Detail:', error);
        
        // 5. GAGAL: Kembalikan Tombol & Tampilkan Pesan Jelas
        Swal.fire({
            title: 'Gagal!',
            text: error.message,
            icon: 'error',
            confirmButtonColor: '#4f46e5'
        });
        
        // Reset tombol ke semula
        btn.innerHTML = originalContent;
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
    });
}

let idleTimer;
const timeoutLimit = 3 * 60 * 1000;

function resetTimer() {
    clearTimeout(idleTimer);
    
    // Set timer baru: Jika tidak ada gerakan selama 3 menit, jalankan logout
    idleTimer = setTimeout(function() {
        // Tampilkan peringatan sebelum menendang user
        Swal.fire({
            title: 'Sesi Habis!',
            text: 'Anda tidak aktif selama 3 menit. Silakan login kembali.',
            icon: 'warning',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        }).then(() => {
            // Arahkan ke file logout
            window.location.href = 'logout.php';
        });
    }, timeoutLimit);
}

window.onload = resetTimer;
document.onmousemove = resetTimer;
document.onkeypress = resetTimer;
document.ontouchstart = resetTimer; 
document.onclick = resetTimer;
document.onscroll = resetTimer;