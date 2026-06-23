/**
 * upload-search.js
 * Behaviour interaktif untuk halaman pencarian pengajuan kredit.
 *
 * Fitur:
 *  1. Debounce auto-submit saat user mengetik (500 ms idle)
 *  2. Tombol ✕ (clear) reset input + submit form
 *  3. Highlight keyword pada teks di tabel
 *  4. Klik baris tabel → navigasi ke uploadForm?app=ID
 *  5. Keyboard shortcut: "/" untuk fokus ke search box
 */

(function () {
    'use strict';

    /* ── Elemen ─────────────────────────────────────────── */
    const form        = document.getElementById('searchForm');
    const input       = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearBtn');
    const tableRows   = document.querySelectorAll('#appTable tbody .us-row');

    if (!form || !input) return; // halaman tidak relevan

    /* ── 1. Debounce auto-submit ─────────────────────────── */
    let debounceTimer;

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            form.submit();
        }, 500);
    });

    /* ── 2. Tombol clear ─────────────────────────────────── */
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            input.value = '';
            form.submit();
        });
    }

    /* ── 3. Highlight keyword di tabel ──────────────────── */
    const keyword = input.value.trim();

    if (keyword && tableRows.length) {
        const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex   = new RegExp('(' + escaped + ')', 'gi');

        // Kolom yang di-highlight: customer, kendaraan, leasing, no. pengajuan
        const highlightCols = [0, 1, 2, 3]; // index td

        tableRows.forEach(function (row) {
            const cells = row.querySelectorAll('td');
            highlightCols.forEach(function (idx) {
                const cell = cells[idx];
                if (!cell) return;
                // Hanya wrap text node, hindari mengubah href/class
                cell.innerHTML = cell.innerHTML.replace(regex,
                    '<mark class="us-hl">$1</mark>'
                );
            });
        });

        // Inject style mark jika belum ada di CSS
        if (!document.getElementById('us-hl-style')) {
            const s = document.createElement('style');
            s.id = 'us-hl-style';
            s.textContent = '.us-hl{background:#fef08a;border-radius:2px;padding:0 1px;}';
            document.head.appendChild(s);
        }
    }

    /* ── 4. Klik baris → navigasi ────────────────────────── */
    tableRows.forEach(function (row) {
        const appId = row.dataset.id;
        if (!appId) return;

        row.style.cursor = 'pointer';

        row.addEventListener('click', function (e) {
            // Jangan intercept jika klik langsung pada <a> atau <button>
            if (e.target.closest('a, button')) return;
            window.location.href = '/credit/uploadForm?app=' + appId;
        });
    });

    /* ── 5. Shortcut "/" → fokus ke search ──────────────── */
    document.addEventListener('keydown', function (e) {
        // Abaikan jika sudah fokus di input/textarea
        if (document.activeElement.tagName === 'INPUT'  ||
            document.activeElement.tagName === 'TEXTAREA') return;

        if (e.key === '/') {
            e.preventDefault();
            input.focus();
            input.select();
        }
    });

})();
