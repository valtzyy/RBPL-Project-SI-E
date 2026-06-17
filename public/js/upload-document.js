document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('uploadForm');
    const ktpInput = document.getElementById('ktpInput');
    const kkInput = document.getElementById('kkInput');
    const slipInput = document.getElementById('slipInput');

    ktpInput.addEventListener('change', () => updateFile(ktpInput, 'ktpFile', 'ktpStatus', 'ktpCheck'));
    kkInput.addEventListener('change', () => updateFile(kkInput, 'kkFile', 'kkStatus', 'kkCheck'));
    slipInput.addEventListener('change', () => updateFile(slipInput, 'slipFile', 'slipStatus', 'slipCheck'));

    function updateFile(input, fileElement, statusElement, checkElement) {
        if (input.files.length) {
            document.getElementById(fileElement).innerText = input.files[0].name;
            document.getElementById(statusElement).innerText = 'Uploaded';
            document.getElementById(statusElement).className = 'status uploaded';
            document.getElementById(checkElement).innerHTML = '✔';
        }
        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        if (ktpInput.files.length) total++;
        if (kkInput.files.length) total++;
        if (slipInput.files.length) total++;

        document.getElementById('totalFile').innerText = total + ' / 3';
        document.getElementById('progressBar').style.width = ((total / 3) * 100) + '%';
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const applicationId = document.querySelector('input[name="application_id"]').value;
        if (!applicationId) {
            alert('Application ID tidak ditemukan');
            return;
        }

        const files = [
            { input: ktpInput, type: 'KTP' },
            { input: kkInput, type: 'KK' },
            { input: slipInput, type: 'SlipGaji' }
        ];

        for (const file of files) {
            if (file.input.files.length) {
                const base64 = await readFileAsBase64(file.input.files[0]);
                const payload = {
                    application_id: applicationId,
                    file_type: file.type,
                    file_base64: base64
                };

                try {
                    const response = await fetch('/credit/upload', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        const err = await response.text();
                        alert('Upload ' + file.type + ' gagal: ' + err);
                    }
                } catch (err) {
                    alert('Error: ' + err.message);
                }
            }
        }

        window.location.href = '/credit/status?app=' + applicationId;
    });

    function readFileAsBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }
});
