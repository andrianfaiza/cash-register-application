function toggleAccountMenu() {
    const dropdown = document.getElementById('accountDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Tutup dropdown jika user mengklik di luar area tombol profil dan dropdown
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('accountDropdown');
    const button = event.target.closest('button[onclick="toggleAccountMenu()"]');

    // Jika dropdown sedang terbuka dan yang diklik BUKAN tombol pemicu ATAU bagian dalam dropdown
    if (dropdown && !dropdown.classList.contains('hidden')) {
        if (!button && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    }
});