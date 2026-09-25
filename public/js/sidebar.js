document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    let hoverTimer = null;

    // Saat kursor masuk ke area sidebar
    sidebar.addEventListener('mouseenter', function () {
        // Beri jeda waktu (misal: 1000ms / 1 detik atau sesuaikan)
        hoverTimer = setTimeout(() => {
            sidebar.classList.remove('w-16');
            sidebar.classList.add('w-64');
        }, 5000); // Ubah angka ini jika ingin mengubah durasi jeda
    });

    // Saat kursor keluar dari sidebar
    sidebar.addEventListener('mouseleave', function () {
        clearTimeout(hoverTimer); // Batal buka jika kursor keluar sebelum timer habis
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-16');
    });
});