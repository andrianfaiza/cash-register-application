document.addEventListener('DOMContentLoaded', function() {
    const settings = window.__APP_SETTINGS__ || { bahasa: 'id', format_tanggal: 'dd/mm/yyyy' };
    const isEnglish = settings.bahasa === 'en';
    const localeCode = isEnglish ? 'en-US' : 'id-ID';

    const now = new Date();

    // 1. Format tanggal Topbar
    function formatTopbarDate(d) {
        if (settings.format_tanggal === 'yyyy-mm-dd') {
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        } else if (settings.format_tanggal === 'dd/mm/yyyy') {
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${dd}/${mm}/${yyyy}`;
        } else {
            return d.toLocaleDateString(localeCode, {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }

    const calendarTextEl = document.getElementById('calendarText');
    if (calendarTextEl) {
        calendarTextEl.textContent = formatTopbarDate(now);
    }

    // 2. Inisialisasi Kalender Flatpickr pada Tombol Topbar
    const btnCalendar = document.getElementById('btnCalendar');
    if (btnCalendar && typeof flatpickr !== 'undefined') {
        const flatpickrFormat = settings.format_tanggal === 'yyyy-mm-dd' ? 'Y-m-d' : 'd/m/Y';
        flatpickr("#btnCalendar", {
            locale: isEnglish ? "default" : "id",
            defaultDate: now,
            dateFormat: flatpickrFormat,
            onChange: function(selectedDates) {
                if (selectedDates.length > 0 && calendarTextEl) {
                    calendarTextEl.textContent = formatTopbarDate(selectedDates[0]);
                }
            }
        });
    }

    // 3. Salam / Greeting sesuai Bahasa
    function updateGreeting() {
        const hour = new Date().getHours();
        const greetingElement = document.getElementById('greetingText');
        const name = greetingElement.dataset.userName || 'Guest'
        // const name =  {{ auth()->user()?->name ?? 'Pengguna' }};
        if (!greetingElement) return;

        let greeting = '';
        if (isEnglish) {
            if (hour >= 3 && hour < 12) {
                greeting = 'Good Morning';
            } else if (hour >= 12 && hour < 17) {
                greeting = 'Good Afternoon';
            } else if (hour >= 17 && hour < 21) {
                greeting = 'Good Evening';
            } else {
                greeting = 'Good Night';
            }
        } else {
            if (hour >= 3 && hour < 11) {
                greeting = 'Selamat Pagi';
            } else if (hour >= 11 && hour < 15) {
                greeting = 'Selamat Siang';
            } else if (hour >= 15 && hour < 18) {
                greeting = 'Selamat Sore';
            } else {
                greeting = 'Selamat Malam';
            }
        }

        greetingElement.textContent = greeting + ', ' + name;
    }

    updateGreeting();
});