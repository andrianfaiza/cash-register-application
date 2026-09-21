// ---------- Tab Profil & Akun / Preferensi ----------
        function setSettingsTab(tab) {
            const tabProfil = document.getElementById('tabProfil');
            const tabPreferensi = document.getElementById('tabPreferensi');
            const panelProfil = document.getElementById('panelProfil');
            const panelPreferensi = document.getElementById('panelPreferensi');

            const activeClasses = ['bg-white', 'shadow', 'text-slate-800'];

            if (tab === 'profil') {
                tabProfil.classList.add(...activeClasses);
                tabProfil.classList.remove('text-slate-500');
                tabPreferensi.classList.remove(...activeClasses);
                tabPreferensi.classList.add('text-slate-500');
                panelProfil.classList.remove('hidden');
                panelPreferensi.classList.add('hidden');
            } else {
                tabPreferensi.classList.add(...activeClasses);
                tabPreferensi.classList.remove('text-slate-500');
                tabProfil.classList.remove(...activeClasses);
                tabProfil.classList.add('text-slate-500');
                panelPreferensi.classList.remove('hidden');
                panelProfil.classList.add('hidden');
            }
        }

        // ---------- Toggle switch generik ----------
        function toggleSwitch(id) {
            const btn = document.getElementById(id);
            const knob = btn.querySelector('.knob');
            const isOn = btn.classList.contains('bg-emerald-500');

            btn.classList.toggle('bg-emerald-500', !isOn);
            btn.classList.toggle('bg-slate-300', isOn);
            knob.classList.toggle('translate-x-4', !isOn);
            knob.classList.toggle('translate-x-0.5', isOn);

            // Sinkronkan hidden input pasangannya (jika ada)
            const hiddenMap = {
                toggleEmailNotif: 'inputNotifEmail',
                toggleSistemNotif: 'inputNotifSistem',
            };
            const hiddenId = hiddenMap[id];
            if (hiddenId) {
                document.getElementById(hiddenId).value = isOn ? '0' : '1';
            }
        }