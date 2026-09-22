let hapusModeTransaksi = false;
let transaksiMap = {};

function initTransaksiData() {
    const el = document.getElementById('transaksiData');
    if (!el) return;
    JSON.parse(el.textContent).forEach((item) => {
        transaksiMap[item.id] = item;
    });
}

function resetTransaksiForm() {
    const form = document.getElementById('transaksiForm');
    if (!form) return;

    form.action = window.transaksiRoutes.store;
    form.querySelector('[name="_method"]')?.remove();

    document.getElementById('modalTitle').textContent = 'Tambah Transaksi Baru';
    form.reset();

    setTipe('keluar');
    setVerifikasiState(true);
    document.getElementById('fileNameLabel').textContent = 'Klik untuk upload';
}

function setVerifikasiState(isOn) {
    const input = document.getElementById('inputVerifikasi');
    const btn = document.getElementById('toggleVerifikasi');
    const knob = document.getElementById('toggleKnob');

    input.value = isOn ? '1' : '0';
    btn.classList.toggle('bg-emerald-500', isOn);
    btn.classList.toggle('bg-slate-300', !isOn);
    knob.classList.toggle('translate-x-4', isOn);
    knob.classList.toggle('translate-x-0.5', !isOn);
}

function openModal() {
    resetTransaksiForm();

    const modal = document.getElementById('formModal');
    const backdrop = document.getElementById('modalBackdrop');
    const box = document.getElementById('modalBox');

    modal.classList.remove('hidden');
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
        resetTransaksiForm();
    }, 200);
}

function editTransaksi(id) {
    const data = transaksiMap[id];
    if (!data) return;

    const form = document.getElementById('transaksiForm');
    form.action = `${window.transaksiRoutes.update}/${id}`;

    let methodInput = form.querySelector('[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.prepend(methodInput);
    }
    methodInput.value = 'PUT';

    document.getElementById('modalTitle').textContent = 'Edit Transaksi';

    setTipe(data.tipe);
    form.querySelector('[name="nominal"]').value = data.nominal;
    form.querySelector('[name="tanggal"]').value = data.tanggal;
    form.querySelector('[name="kategori"]').value = data.kategori;
    form.querySelector('[name="proyek_id"]').value = data.proyek_id ?? '';
    form.querySelector('[name="deskripsi"]').value = data.deskripsi ?? '';
    form.querySelector('[name="rekening_id"]').value = data.rekening_id ?? 'kas-besar';
    setVerifikasiState(!!data.verifikasi_langsung);
    document.getElementById('fileNameLabel').textContent = 'Klik untuk upload';

    const modal = document.getElementById('formModal');
    const backdrop = document.getElementById('modalBackdrop');
    const box = document.getElementById('modalBox');

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        box.classList.remove('opacity-0', 'scale-95');
    });
    document.body.classList.add('overflow-hidden');
}

function toggleHapusMode() {
    if (hapusModeTransaksi) {
        const checked = document.querySelectorAll('.transaksi-checkbox:checked');
        if (checked.length === 0) {
            exitHapusModeTransaksi();
            return;
        }

        if (!confirm(`Hapus ${checked.length} transaksi terpilih?`)) return;

        const container = document.getElementById('deleteTransaksiIds');
        container.innerHTML = '';
        checked.forEach((cb) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        document.getElementById('deleteTransaksiForm').submit();
        return;
    }

    hapusModeTransaksi = true;
    document.querySelectorAll('.hapus-col').forEach((el) => el.classList.remove('hidden'));
    document.getElementById('btnHapusTransaksi').innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Hapus Terpilih`;
}

function exitHapusModeTransaksi() {
    hapusModeTransaksi = false;
    document.querySelectorAll('.hapus-col').forEach((el) => el.classList.add('hidden'));
    document.querySelectorAll('.transaksi-checkbox').forEach((cb) => { cb.checked = false; });
    const selectAll = document.getElementById('selectAllTransaksi');
    if (selectAll) selectAll.checked = false;
    document.getElementById('btnHapusTransaksi').innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Hapus`;
}

function toggleSelectAllTransaksi(source) {
    document.querySelectorAll('.transaksi-checkbox').forEach((cb) => {
        cb.checked = source.checked;
    });
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (hapusModeTransaksi) {
            exitHapusModeTransaksi();
            return;
        }
        if (!document.getElementById('formModal').classList.contains('hidden')) {
            closeModal();
        }
    }
});

function setTipe(tipe) {
    document.getElementById('inputTipe').value = tipe;

    const kategori = document.getElementById('inputKategori');
    const kategoriKeluar = kategori.querySelector('[data-tipe="keluar"]');
    const kategoriMasuk = kategori.querySelector('[data-tipe="masuk"]');
    const kategoriAktif = tipe === 'masuk' ? kategoriMasuk : kategoriKeluar;
    const kategoriLain = tipe === 'masuk' ? kategoriKeluar : kategoriMasuk;

    kategoriLain.hidden = true;
    kategoriAktif.hidden = false;

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

function toggleVerifikasi() {
    const input = document.getElementById('inputVerifikasi');
    setVerifikasiState(input.value !== '1');
}

function updateFileName(input) {
    const label = document.getElementById('fileNameLabel');
    label.textContent = input.files[0]?.name ?? 'Klik untuk upload';
}

function handleTransaksiSubmit() {
    return true;
}

function openDetailModal(id) {
    const data = transaksiMap[id];
    if (!data) return;

    const isMasuk = data.tipe === 'masuk';
    const badgeTipe = document.getElementById('detailBadgeTipe');
    badgeTipe.textContent = isMasuk ? 'IN' : 'OUT';
    badgeTipe.className = `inline-flex items-center justify-center w-9 h-9 rounded-xl font-bold text-sm ${isMasuk ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400'}`;

    document.getElementById('detailTanggal').textContent = data.tanggal_formatted || data.tanggal;
    
    const nominalEl = document.getElementById('detailNominal');
    nominalEl.textContent = `${isMasuk ? '+' : '-'} Rp ${Number(data.nominal).toLocaleString('id-ID')}`;
    nominalEl.className = `text-2xl font-black ${isMasuk ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'}`;

    document.getElementById('detailKategori').textContent = (data.kategori || '-').toUpperCase();
    document.getElementById('detailProyek').textContent = data.proyek_nama || 'Non-Proyek';
    document.getElementById('detailRekening').textContent = data.rekening_id || 'KAS BESAR';

    const statusEl = document.getElementById('detailStatus');
    statusEl.textContent = data.status || 'Sukses';
    statusEl.className = `inline-block font-semibold px-2.5 py-1 rounded-full text-xs ${data.status === 'Sukses' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400'}`;

    document.getElementById('detailDeskripsi').textContent = data.deskripsi || 'Tidak ada deskripsi.';

    const noBukti = document.getElementById('detailNoBukti');
    const buktiContent = document.getElementById('detailBuktiContent');
    const buktiImg = document.getElementById('detailBuktiImg');
    const buktiLink = document.getElementById('detailBuktiLink');

    if (data.bukti) {
        noBukti.classList.add('hidden');
        buktiContent.classList.remove('hidden');
        buktiContent.classList.add('flex');
        buktiLink.href = data.bukti;

        const isImage = data.bukti.match(/\.(jpeg|jpg|gif|png|webp)$/i);
        if (isImage) {
            buktiImg.src = data.bukti;
            buktiImg.classList.remove('hidden');
        } else {
            buktiImg.classList.add('hidden');
        }
    } else {
        noBukti.classList.remove('hidden');
        buktiContent.classList.add('hidden');
        buktiContent.classList.remove('flex');
    }

    const modal = document.getElementById('detailModal');
    const backdrop = document.getElementById('detailModalBackdrop');
    const box = document.getElementById('detailModalBox');

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        box.classList.remove('opacity-0', 'scale-95');
    });
    document.body.classList.add('overflow-hidden');
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    const backdrop = document.getElementById('detailModalBackdrop');
    const box = document.getElementById('detailModalBox');

    backdrop.classList.add('opacity-0');
    box.classList.add('opacity-0', 'scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }, 200);
}

document.addEventListener('DOMContentLoaded', initTransaksiData);
