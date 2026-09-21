function openModal() {
        const modal = document.getElementById('formModal');
        const backdrop = document.getElementById('modalBackdrop');
        const box = document.getElementById('modalBox');

        modal.classList.remove('hidden');
        // force reflow supaya transisi jalan
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            box.classList.remove('opacity-0', 'scale-95');
        });
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        const modal = document.getElementById('formModal');
        const backdrop = document.getElementById('modalBackdrop');
        const box = document.getElementById('modalBox');

        backdrop.classList.add('opacity-0');
        box.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    }

    // Tutup dengan tombol Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !document.getElementById('formModal').classList.contains('hidden')) {
            closeModal();
        }
    });

    // ---------- Tab Kas Keluar / Kas Masuk ----------
    function setTipe(tipe) {
        document.getElementById('inputTipe').value = tipe;

        const kategori = document.getElementById('inputKategori');
        const kategoriKeluar = kategori.querySelector('[data-tipe="keluar"]');
        const kategoriMasuk = kategori.querySelector('[data-tipe="masuk"]');
        const kategoriAktif = tipe === 'masuk' ? kategoriMasuk : kategoriKeluar;
        const kategoriLain = tipe === 'masuk' ? kategoriKeluar : kategoriMasuk;

        kategoriLain.hidden = true;
        kategoriAktif.hidden = false;
        kategori.value = '';

        const tabKeluar = document.getElementById('tabKeluar');
        const tabMasuk = document.getElementById('tabMasuk');

        const activeClasses = ['bg-white', 'shadow', 'text-slate-800'];
        const inactiveClass = 'text-slate-400';

        if (tipe === 'keluar') {
            tabKeluar.classList.add(...activeClasses);
            tabKeluar.classList.remove(inactiveClass);
            tabMasuk.classList.remove(...activeClasses);
            tabMasuk.classList.add(inactiveClass);
        } else {
            tabMasuk.classList.add(...activeClasses);
            tabMasuk.classList.remove(inactiveClass);
            tabKeluar.classList.remove(...activeClasses);
            tabKeluar.classList.add(inactiveClass);
        }
    }

    // ---------- Toggle Verifikasi Langsung ----------
    function toggleVerifikasi() {
        const input = document.getElementById('inputVerifikasi');
        const btn = document.getElementById('toggleVerifikasi');
        const knob = document.getElementById('toggleKnob');

        const isOn = input.value === '1';
        input.value = isOn ? '0' : '1';

        btn.classList.toggle('bg-emerald-500', !isOn);
        btn.classList.toggle('bg-slate-300', isOn);
        knob.classList.toggle('translate-x-4', !isOn);
        knob.classList.toggle('translate-x-0.5', isOn);
    }

    // ---------- Nama file upload ----------
    function updateFileName(input) {
        const label = document.getElementById('fileNameLabel');
        label.textContent = input.files[0]?.name ?? 'Klik untuk upload';
    }

    // ---------- Submit ----------
    function handleTransaksiSubmit(event) {
        
        // Jika ingin submit via AJAX/fetch, tangani di sini dan panggil event.preventDefault().
        return true;
    }