function toggleNotificationMenu() {
    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown) return;

    dropdown.classList.toggle('hidden');
}

function tandaiSemuaDibaca() {
    // 1. Sembunyikan badge counter lonceng
    const badge = document.getElementById('notifBadge');
    if (badge) {
        badge.classList.add('hidden');
        badge.textContent = '0';
    }

    // 2. Ubah pill counter di header dropdown
    const countPill = document.getElementById('notifCountPill');
    if (countPill) {
        const isEnglish = (window.__APP_SETTINGS__ && window.__APP_SETTINGS__.bahasa === 'en');
        countPill.textContent = '0 ' + (isEnglish ? 'New' : 'Baru');
        countPill.classList.remove('bg-blue-100', 'text-blue-700', 'dark:bg-blue-950/80', 'dark:text-blue-300');
        countPill.classList.add('bg-slate-100', 'text-slate-500', 'dark:bg-slate-800', 'dark:text-slate-400');
    }

    // 3. Hilangkan highlight unread dan titik biru
    document.querySelectorAll('.notif-item').forEach(el => {
        el.classList.remove('bg-blue-50/40', 'dark:bg-blue-950/20');
    });

    document.querySelectorAll('.unread-dot').forEach(el => {
        el.remove();
    });

    // 4. Simpan status ke localStorage agar tetap ditandai dibaca saat reload
    localStorage.setItem('kas_notifications_read_all', new Date().toISOString());
}

// Tutup dropdown jika user klik di luar area lonceng/dropdown
document.addEventListener('click', function (event) {
    const container = document.getElementById('notificationContainer');
    const dropdown = document.getElementById('notificationDropdown');

    if (container && dropdown && !dropdown.classList.contains('hidden')) {
        if (!container.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    }
});