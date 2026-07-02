document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createForm');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        // Validasi client-side
        if (!formData.get('transaction_id') || !formData.get('leasing_name')) {
            alert('Lengkapi semua field terlebih dahulu.');
            return;
        }

        try {
            const response = await fetch('/credit/create', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            let result;
            try { result = JSON.parse(text); } catch { result = { message: text }; }

            if (response.ok) {
                alert('Pengajuan berhasil dibuat! ID: ' + (result.application_id || ''));
                window.location.href = '/credit/upload-search'
            } else {
                alert('Gagal: ' + (result.message || response.statusText));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        }
    });
});