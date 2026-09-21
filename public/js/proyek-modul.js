let hapusModeProyek = false;
let proyekMap = {};

function initProyekData() {
    const el = document.getElementById('proyekData');
    if (!el) return;
    JSON.parse(el.textContent).forEach((item) => {
        proyekMap[item.id] = item;
    });
}

function createDepartemenTag(nama) {
    const tag = document.createElement('span');
    tag.className = 'flex items-center gap-1 bg-slate-100 text-slate-700 text-xs font-medium px-3 py-1.5 rounded-full';
    tag.innerHTML = `${nama}
        <input type="hidden" name="departemen[]" value="${nama}">
        <button type="button" onclick="this.closest('span').remove()" class="text-slate-400 hover:text-slate-600">&times;</button>`;
    return tag;
}

function resetDepartemenTags(departemen = ['Logistik', 'Operasional']) {
    const wrapper = document.getElementById('departemenTags');
    const addBtn = wrapper.querySelector('button[onclick="addDepartemen()"]');
    wrapper.querySelectorAll('span').forEach((span) => span.remove());
    departemen.forEach((nama) => {
        wrapper.insertBefore(createDepartemenTag(nama), addBtn);
    });
}

function resetProyekForm() {
    const form = document.getElementById('proyekForm');
    if (!form) return;

    form.action = window.proyekRoutes.store;
    form.querySelector('[name="_method"]')?.remove();
    form.reset();

    resetDepartemenTags();
    const leadSelect = document.getElementById('projectLeadSelect');
    leadSelect.classList.add('hidden');
    leadSelect.value = '';
}

function openProyekModal() {
    resetProyekForm();

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
        resetProyekForm();
    }, 200);
}

function editProyek(id) {
    const data = proyekMap[id];
    if (!data) return;

    const form = document.getElementById('proyekForm');
    form.action = `${window.proyekRoutes.update}/${id}`;

    let methodInput = form.querySelector('[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.prepend(methodInput);
    }
    methodInput.value = 'PUT';

    form.querySelector('[name="nama_proyek"]').value = data.nama_proyek;
    form.querySelector('[name="kategori_proyek"]').value = data.kategori_proyek;
    form.querySelector('[name="deskripsi"]').value = data.deskripsi ?? '';
    form.querySelector('[name="pagu_anggaran"]').value = data.pagu_anggaran;
    form.querySelector('[name="tanggal_mulai"]').value = data.tanggal_mulai ?? '';
    form.querySelector('[name="tanggal_selesai"]').value = data.tanggal_selesai ?? '';

    const leadSelect = document.getElementById('projectLeadSelect');
    if (data.project_lead_id) {
        leadSelect.classList.remove('hidden');
        leadSelect.value = data.project_lead_id;
    } else {
        leadSelect.classList.add('hidden');
        leadSelect.value = '';
    }

    resetDepartemenTags(data.departemen?.length ? data.departemen : []);

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

function toggleHapusMode() {
    if (hapusModeProyek) {
        const checked = document.querySelectorAll('.proyek-checkbox:checked');
        if (checked.length === 0) {
            exitHapusModeProyek();
            return;
        }

        if (!confirm(`Hapus ${checked.length} proyek terpilih?`)) return;

        const container = document.getElementById('deleteProyekIds');
        container.innerHTML = '';
        checked.forEach((cb) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        document.getElementById('deleteProyekForm').submit();
        return;
    }

    hapusModeProyek = true;
    document.querySelectorAll('.hapus-col').forEach((el) => el.classList.remove('hidden'));
    document.getElementById('btnHapusProyek').innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Hapus Terpilih`;
}

function exitHapusModeProyek() {
    hapusModeProyek = false;
    document.querySelectorAll('.hapus-col').forEach((el) => el.classList.add('hidden'));
    document.querySelectorAll('.proyek-checkbox').forEach((cb) => { cb.checked = false; });
    document.getElementById('btnHapusProyek').innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Hapus`;
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (hapusModeProyek) {
            exitHapusModeProyek();
            return;
        }
        if (!document.getElementById('proyekModal').classList.contains('hidden')) {
            closeProyekModal();
        }
    }
});

function addDepartemen() {
    const nama = prompt('Nama departemen:');
    if (!nama) return;

    const wrapper = document.getElementById('departemenTags');
    const addBtn = wrapper.querySelector('button[onclick="addDepartemen()"]');
    wrapper.insertBefore(createDepartemenTag(nama), addBtn);
}

function handleProyekSubmit() {
    return true;
}

document.addEventListener('DOMContentLoaded', initProyekData);
