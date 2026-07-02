document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('financeForm');
    const resultBox = document.getElementById('resultBox');
    const resultTitle = document.getElementById('resultTitle');
    const resultMessage = document.getElementById('resultMessage');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const id_kredit = parseInt(document.getElementById('id_kredit').value);
        const nominal_dibayar = parseFloat(document.getElementById('nominal_dibayar').value);
        const verified_by = parseInt(document.getElementById('verified_by').value);

        // Reset result box
        resultBox.style.display = 'none';
        resultBox.className = 'result-card';

        try {
            const response = await fetch('/verifikasi-dp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_kredit, nominal_dibayar, verified_by })
            });

            const data = await response.json();

            resultBox.style.display = 'block';
            if (response.ok && data.status === 'success') {
                resultBox.classList.add('result-success');
                resultTitle.innerHTML = '✔ Berhasil';
                resultMessage.textContent = data.message + ` (ID Transaksi: ${data.data.transaction_id}, Status Transaksi: ${data.data.status_transaksi})`;
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
