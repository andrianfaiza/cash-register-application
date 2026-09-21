document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();

    // 1. Set teks awal secara real-time (Tanggal, Bulan, dan Tahun)
    const formattedDate = now.toLocaleDateString('id-ID', { 
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    document.getElementById('calendarText').textContent = formattedDate;

    // 2. Inisialisasi Kalender Flatpickr pada Tombol
    flatpickr("#btnCalendar", {
        locale: "id",
        defaultDate: now,
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const date = selectedDates[0];
                
                // Format teks saat user memilih tanggal baru dari kalender
                const dateSelected = date.toLocaleDateString('id-ID', { 
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                
                document.getElementById('calendarText').textContent = dateSelected;
                
                // (Opsional) Lakukan filter data / kirim form di sini jika diperlukan
                console.log("Tanggal terpilih:", dateStr);
            }
        }
    });
});