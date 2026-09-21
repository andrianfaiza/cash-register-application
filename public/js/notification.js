function toggleNotificationMenu() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Tutup dropdown jika user klik di luar area lonceng/dropdown
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('notificationDropdown');
    const button = event.target.closest('button[onclick="toggleNotificationMenu()"]');

    if (dropdown && !dropdown.classList.contains('hidden')) {
        if (!button && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    }
});