function toggleAccountMenu() {
    const dropdown = document.getElementById('accountDropdown');
    const dropdownMobile = document.getElementById('accountDropdownMobile');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
    if (dropdownMobile) {
        dropdownMobile.classList.toggle('hidden');
    }
}

function toggleProfilePhotoMenu(event) {
    event.stopPropagation();
    const menu = document.getElementById('profilePhotoMenu');
    const button = document.getElementById('profilePhotoMenuButton');
    if (!menu || !button) return;

    const isOpening = menu.classList.contains('hidden');
    menu.classList.toggle('hidden', !isOpening);
    button.setAttribute('aria-expanded', String(isOpening));
}

function submitProfilePhoto(input) {
    if (input.files?.length) {
        document.getElementById('avatarForm')?.requestSubmit();
    }
}

function openProfilePhotoPicker(mode) {
    const input = document.getElementById('profilePhotoInput');
    if (!input) return;

    if (mode === 'camera') {
        input.setAttribute('capture', 'user');
    } else {
        input.removeAttribute('capture');
    }

    input.click();
}

function openProfilePhotoPreview() {
    document.getElementById('profilePhotoMenu')?.classList.add('hidden');
    document.getElementById('profilePhotoMenuButton')?.setAttribute('aria-expanded', 'false');
    const preview = document.getElementById('profilePhotoPreview');
    preview?.classList.remove('hidden');
    preview?.classList.add('flex');
}

function closeProfilePhotoPreview() {
    const preview = document.getElementById('profilePhotoPreview');
    preview?.classList.add('hidden');
    preview?.classList.remove('flex');
}

// Tutup dropdown jika user mengklik di luar area tombol profil dan dropdown
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('accountDropdown');
    const dropdownMobile = document.getElementById('accountDropdownMobile');
    const button = event.target.closest('button[onclick="toggleAccountMenu()"]');
    const photoMenu = document.getElementById('profilePhotoMenu');
    const photoMenuButton = document.getElementById('profilePhotoMenuButton');

    // Jika dropdown sedang terbuka dan yang diklik BUKAN tombol pemicu ATAU bagian dalam dropdown
    if (dropdown && !dropdown.classList.contains('hidden')) {
        if (!button && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    }
    if (dropdownMobile && !dropdownMobile.classList.contains('hidden')) {
        if (!button && !dropdownMobile.contains(event.target)) {
            dropdownMobile.classList.add('hidden');
        }
    }
    if (photoMenu && !photoMenu.contains(event.target) && !photoMenuButton?.contains(event.target)) {
        photoMenu.classList.add('hidden');
        photoMenuButton?.setAttribute('aria-expanded', 'false');
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeProfilePhotoPreview();
});
