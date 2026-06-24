document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('approvalForm');
    const resultBox = document.getElementById('resultBox');
    const resultTitle = document.getElementById('resultTitle');
    const resultMessage = document.getElementById('resultMessage');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const id_kredit = parseInt(document.getElementById('id_kredit').value);
        const status_approval = document.getElementById('status_approval').value;
        const catatan = document.getElementById('catatan').value;

        // Reset result box
        resultBox.style.display = 'none';
        resultBox.className = 'result-card';

        try {
            const response = await fetch('/webhook-approval', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_kredit, status_approval, catatan })
            });

            const data = await response.json();

            resultBox.style.display = 'block';
            if (response.ok && data.status === 'success') {
                resultBox.classList.add('result-success');
                resultTitle.innerHTML = '✔ Berhasil';
                resultMessage.textContent = data.message + ' Mengalihkan ke halaman verifikasi DP...';
                
                // Redirect ke halaman verifikasi DP setelah 1.5 detik
                setTimeout(() => {
                    window.location.href = '/verifikasi-dp?id_kredit=' + id_kredit;
                }, 1500);
            } else {
                resultBox.classList.add('result-error');
                resultTitle.innerHTML = '❌ Gagal';
                resultMessage.textContent = data.message;
            }
        } catch (err) {
            resultBox.style.display = 'block';
            resultBox.classList.add('result-error');
            resultTitle.innerHTML = '❌ Kesalahan Sistem';
            resultMessage.textContent = 'Gagal memproses request: ' + err.message;
        }
    });
});
