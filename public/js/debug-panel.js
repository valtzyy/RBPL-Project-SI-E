document.addEventListener('DOMContentLoaded', function () {
    const formWebhook = document.getElementById('formWebhook');
    const formFinance = document.getElementById('formFinance');
    const btnResetData = document.getElementById('btnResetData');

    const cardStep1 = document.getElementById('cardStep1');
    const cardStep2 = document.getElementById('cardStep2');
    const badgeStep1 = document.getElementById('badgeStep1');
    const badgeStep2 = document.getElementById('badgeStep2');
    const overlayStep2 = document.getElementById('overlayStep2');
    const checkStep1 = document.getElementById('checkStep1');
    const checkStep2 = document.getElementById('checkStep2');

    const wIdKredit = document.getElementById('w_id_kredit');
    const fIdKredit = document.getElementById('f_id_kredit');

    const totalFileBadge = document.getElementById('totalFile');
    const progressBar = document.getElementById('progressBar');
    
    const consoleOutput = document.getElementById('consoleOutput');
    const consoleStatus = document.getElementById('consoleStatus');

    let step1Completed = false;
    let step2Completed = false;

    function setConsole(statusText, isSuccess, data) {
        consoleStatus.textContent = statusText;
        if (isSuccess) {
            consoleStatus.style.color = '#10b981'; // Green
        } else {
            consoleStatus.style.color = '#ef4444'; // Red
        }
        consoleOutput.textContent = JSON.stringify(data, null, 2);
    }

    function updateStep1State(approved) {
        step1Completed = approved;
        if (approved) {
            badgeStep1.textContent = 'Disetujui';
            badgeStep1.className = 'status uploaded';
            checkStep1.innerHTML = '✔';
            checkStep1.style.color = '#166534';
            
            // Unlock Step 2
            overlayStep2.style.display = 'none';
            badgeStep2.textContent = 'Terbuka';
            badgeStep2.className = 'status pending';
            
            // Sync ID Kredit to Step 2
            fIdKredit.value = wIdKredit.value;
        } else {
            badgeStep1.textContent = 'Menunggu';
            badgeStep1.className = 'status pending';
            checkStep1.innerHTML = '○';
            checkStep1.style.color = '';

            // Lock Step 2
            overlayStep2.style.display = 'flex';
            badgeStep2.textContent = 'Terkunci';
            badgeStep2.className = 'status pending';
            
            step2Completed = false;
            badgeStep2.textContent = 'Terkunci';
            checkStep2.innerHTML = '○';
            checkStep2.style.color = '';
        }
        updateSummary();
    }

    function updateStep2State(completed) {
        step2Completed = completed;
        if (completed) {
            badgeStep2.textContent = 'Lunas';
            badgeStep2.className = 'status uploaded';
            checkStep2.innerHTML = '✔';
            checkStep2.style.color = '#166534';
        } else {
            if (step1Completed) {
                badgeStep2.textContent = 'Terbuka';
                badgeStep2.className = 'status pending';
            } else {
                badgeStep2.textContent = 'Terkunci';
                badgeStep2.className = 'status pending';
            }
            checkStep2.innerHTML = '○';
            checkStep2.style.color = '';
        }
        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        if (step1Completed) total++;
        if (step2Completed) total++;

        totalFileBadge.innerText = total + ' / 2';
        progressBar.style.width = ((total / 2) * 100) + '%';
    }

    // A. Handle Form Webhook Approval
    formWebhook.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id_kredit = parseInt(wIdKredit.value);
        const status_approval = 'disetujui';
        const catatan = document.getElementById('w_catatan').value;

        consoleOutput.textContent = '⏳ Memproses request...';
        consoleStatus.textContent = 'SENDING';
        consoleStatus.style.color = '#64748b';

        try {
            const response = await fetch('/webhook-approval', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_kredit, status_approval, catatan })
            });

            const data = await response.json();
            
            if (response.ok && data.status === 'success') {
                setConsole(`${response.status} OK`, true, data);
                updateStep1State(true);
            } else {
                setConsole(`${response.status} ERROR`, false, data);
                updateStep1State(false);
            }
        } catch (err) {
            setConsole('FETCH ERROR', false, { status: 'error', message: err.message });
            updateStep1State(false);
        }
    });

    // B. Handle Form Verifikasi DP
    formFinance.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id_kredit = parseInt(fIdKredit.value);
        const nominal_dibayar = parseFloat(document.getElementById('f_nominal_dibayar').value);
        const verified_by_val = document.getElementById('f_verified_by').value;
        const verified_by = verified_by_val ? parseInt(verified_by_val) : null;

        consoleOutput.textContent = '⏳ Memproses request...';
        consoleStatus.textContent = 'SENDING';
        consoleStatus.style.color = '#64748b';

        try {
            const response = await fetch('/verifikasi-dp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_kredit, nominal_dibayar, verified_by })
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                setConsole(`${response.status} OK`, true, data);
                updateStep2State(true);
            } else {
                setConsole(`${response.status} ERROR`, false, data);
                updateStep2State(false);
            }
        } catch (err) {
            setConsole('FETCH ERROR', false, { status: 'error', message: err.message });
            updateStep2State(false);
        }
    });

    // C. Handle Reset Data Uji Coba
    btnResetData.addEventListener('click', async function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '⏳ Mereset...';

        try {
            const response = await fetch('/debug-reset', { method: 'POST' });
            const data = await response.json();

            if (data.status === 'success') {
                alert('Sukses! ' + data.message);
                updateStep1State(false);
                updateStep2State(false);
                setConsole('IDLE', true, { message: 'Database reset successfully.' });
            } else {
                alert('Gagal mereset: ' + data.message);
            }
        } catch (err) {
            alert('Fetch Error: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
});
