// ---------- Buka / Tutup Modal ----------
    function openProyekModal() {
        const modal = document.getElementById('proyekModal');
        const backdrop = document.getElementById('proyekModalBackdrop');
        const box = document.getElementById('proyekModalBox');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            box.classList.remove('opacity-0', 'scale-95');
        });
        document.body.classList.add('overflow-hidden');
    }

    function closeProyekModal() {
        const modal = document.getElementById('proyekModal');
        const backdrop = document.getElementById('proyekModalBackdrop');
        const box = document.getElementById('proyekModalBox');

        backdrop.classList.add('opacity-0');
        box.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !document.getElementById('proyekModal').classList.contains('hidden')) {
            closeProyekModal();
        }
    });

    // ---------- Tambah tag Departemen ----------
    function addDepartemen() {
        const nama = prompt('Nama departemen:');
        if (!nama) return;

        const wrapper = document.getElementById('departemenTags');
        const addBtn = wrapper.querySelector('button[onclick="addDepartemen()"]');

        const tag = document.createElement('span');
        tag.className = 'flex items-center gap-1 bg-slate-100 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-full';
        tag.innerHTML = `${nama}
            <input type="hidden" name="departemen[]" value="${nama}">
            <button type="button" onclick="this.closest('span').remove()" class="text-slate-400 hover:text-slate-600">&times;</button>`;

        wrapper.insertBefore(tag, addBtn);
    }

    // ---------- Submit ----------
    function handleProyekSubmit(event) {
        // Biarkan submit normal ke server (action="{{ route('proyek.store') }}").
        // Untuk submit via AJAX/fetch, tangani di sini dan panggil event.preventDefault().
        return true;
    }