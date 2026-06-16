document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_sparepart');
    if (!searchInput) return;

    const dropdown = document.getElementById('autocomplete-dropdown');
    const stockCard = document.getElementById('stock-indicator');
    const stockSku = document.getElementById('stock-sku');
    const stockName = document.getElementById('stock-name');
    const stockCount = document.getElementById('stock-count');
    const stockBadge = document.getElementById('stock-badge');

    let currentFocus = -1; 
    let selectedSparepartId = null; // Menyimpan ID part asli dari database

    // === Event 1: Input Pencarian (Kini menggunakan API Database) ===
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        dropdown.innerHTML = ''; 
        currentFocus = -1; 

        if (query === '') {
            stockCard.classList.remove('show');
            dropdown.classList.remove('show');
            return;
        }

        if (query.length < 2) {
            dropdown.classList.remove('show');
            return;
        }

        // Panggil API Pencarian buatan tim Back-End
        fetch('/api/sparepart/search?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (searchInput.value.trim() === '') return;
                dropdown.classList.add('show');
                dropdown.innerHTML = ''; 

                if (data.length > 0) {
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'dropdown-item';
                        
                        // Konversi format data dari BE agar cocok dengan UI kita
                        li.dataset.item = JSON.stringify({
                            id: item.id,
                            sku: item.sku,
                            nama: item.name,
                            stok: parseInt(item.stock)
                        });
                        
                        let textColor = '#10b981'; 
                        if (item.stock == 0) textColor = '#ef4444'; 
                        else if (item.stock <= 10) textColor = '#f59e0b'; 

                        li.innerHTML = `
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <div>
                                    <div class="item-name">${item.name}</div>
                                    <div class="item-sku">SKU: ${item.sku}</div>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-size: 13px; font-weight: 700; color: ${textColor}">
                                        ${item.stock} unit
                                    </span>
                                </div>
                            </div>
                        `;
                        dropdown.appendChild(li);
                    });
                } else {
                    // Tampilkan Empty State keren milikmu jika data tidak ada di database
                    const li = document.createElement('li');
                    li.className = 'dropdown-item empty-state';
                    li.innerHTML = `
                        <div style="text-align: center; color: var(--text-muted); padding: 12px 0;">
                            <svg style="width:24px; height:24px; margin:0 auto 8px auto; opacity:0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>Sparepart tidak ditemukan</div>
                        </div>
                    `;
                    li.style.cursor = 'default';
                    dropdown.appendChild(li);
                }
            })
            .catch(err => console.error("Error fetching data:", err));
    });

    // === Event 2: Navigasi Keyboard ===
    searchInput.addEventListener('keydown', function(e) {
        const items = dropdown.getElementsByClassName('dropdown-item');
        if (!dropdown.classList.contains('show') || items.length === 0) return;
        if (items[0].classList.contains('empty-state')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentFocus++;
            addActive(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            addActive(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click(); 
            }
        }
    });

    function addActive(items) {
        if (!items) return;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (items.length - 1);
        items[currentFocus].classList.add('keyboard-active');
        items[currentFocus].scrollIntoView({ block: 'nearest' });
    }

    function removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove('keyboard-active');
        }
    }

    // === Event 3: Klik Item Dropdown ===
    dropdown.addEventListener('click', function(e) {
        const itemEl = e.target.closest('.dropdown-item');
        if (!itemEl || itemEl.classList.contains('empty-state')) return;

        const itemData = JSON.parse(itemEl.dataset.item);

        searchInput.value = itemData.nama;
        dropdown.classList.remove('show');
        showStockIndicator(itemData);
    });

    // === Fungsi Render Kartu Indikator Stok ===
    function showStockIndicator(item) {
        stockSku.textContent = `SKU: ${item.sku}`;
        stockName.textContent = item.nama;
        stockCount.textContent = item.stok;

        stockBadge.className = 'badge';

        if (item.stok > 10) {
            stockBadge.classList.add('badge-success');
            stockBadge.textContent = 'Stok Aman';
        } else if (item.stok > 0 && item.stok <= 10) {
            stockBadge.classList.add('badge-warning');
            stockBadge.textContent = 'Stok Menipis';
        } else {
            stockBadge.classList.add('badge-danger');
            stockBadge.textContent = 'Stok Habis';
        }

        // Simpan ID part dan reset quantity ke 1
        selectedSparepartId = item.id;
        const qtyInput = document.getElementById('request-qty');
        if (qtyInput) qtyInput.value = 1;

        stockCard.classList.add('show');
    }

    // Tutup dropdown jika klik area luar
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // === Event 4: Tombol Request (Menghubungkan PBI-13.5) ===
    const btnRequest = document.getElementById('btn-request-part');
    const toastOverlay = document.getElementById('toast-overlay');
    const toastIcon = document.getElementById('toast-icon');
    const toastText = document.getElementById('toast-text');

    if (btnRequest) {
        btnRequest.addEventListener('click', function() {
            const qty = document.getElementById('request-qty').value;
            if (!selectedSparepartId) return;

            const formData = new FormData();
            formData.append('sparepart_id', selectedSparepartId);
            formData.append('work_order_id', 101); // Sesuai data dummy WO
            formData.append('quantity', qty);

            // 1. Munculkan Animasi Spinner saat mulai mengirim data
            if (toastOverlay) {
                toastOverlay.classList.add('active');
                toastIcon.className = 'spinner';
                toastIcon.innerHTML = '';
                toastText.textContent = 'Memproses request...';
                toastText.style.color = 'var(--text-main)';
            }
            btnRequest.disabled = true;

            // 2. Tembak API Back-End
            fetch('/api/sparepart/request', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // 3A. Jika Berhasil: Ubah jadi Ceklis Hijau
                    if (toastOverlay) {
                        toastIcon.className = 'checkmark';
                        toastIcon.innerHTML = '✓';
                        toastText.textContent = 'Berhasil ditambahkan!';
                        toastText.style.color = '#10b981';

                        // Sembunyikan kotak setelah 1.5 detik agar user bisa melihat ceklisnya
                        setTimeout(() => {
                            toastOverlay.classList.remove('active');
                            stockCard.classList.remove('show');
                            searchInput.value = '';
                        }, 1500);
                    }
                } else {
                    // 3B. Jika Gagal (misal stok habis): Tampilkan pesan error
                    if (toastOverlay) {
                        toastIcon.className = ''; // Hapus spinner
                        toastIcon.innerHTML = '❌';
                        toastText.textContent = data.message;
                        toastText.style.color = '#ef4444';

                        setTimeout(() => {
                            toastOverlay.classList.remove('active');
                        }, 2500);
                    }
                }
            })
            .catch(err => {
                if (toastOverlay) {
                    toastIcon.className = '';
                    toastIcon.innerHTML = '⚠️';
                    toastText.textContent = 'Terjadi kesalahan sistem.';
                    
                    setTimeout(() => {
                        toastOverlay.classList.remove('active');
                    }, 2500);
                }
            })
            .finally(() => {
                btnRequest.disabled = false;
            });
        });
    }
});