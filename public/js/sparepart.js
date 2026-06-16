// public/js/sparepart.js

document.addEventListener('DOMContentLoaded', function() {

    // ====================================================================
    // FITUR 1: MEKANIK - PENCARIAN & REQUEST Suku Cadang (PBI-13.3, 13.4, 13.5)
    // ====================================================================
    const searchInput = document.getElementById('search_sparepart');
    const dropdown = document.getElementById('autocomplete-dropdown');
    const stockCard = document.getElementById('stock-indicator');
    
    const stockSku = document.getElementById('stock-sku');
    const stockName = document.getElementById('stock-name');
    const stockCount = document.getElementById('stock-count');
    const stockBadge = document.getElementById('stock-badge');
    
    let selectedSparepartId = null;
    let currentFocus = -1; // Menghitung baris dropdown yang sedang aktif dipilih via keyboard

    // --- PROTEKSI UI: Pengaturan Gaya Dropdown Otomatis Agar Melayang & Tanpa Bulet ---
    if (dropdown) {
        dropdown.style.listStyle = 'none';
        dropdown.style.padding = '0';
        dropdown.style.margin = '4px 0 0 0';
        dropdown.style.position = 'absolute';
        dropdown.style.top = '100%';
        dropdown.style.left = '0';
        dropdown.style.width = '100%';
        dropdown.style.backgroundColor = 'white';
        dropdown.style.border = '1px solid #e2e8f0';
        dropdown.style.borderRadius = '8px';
        dropdown.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        dropdown.style.zIndex = '999';
        dropdown.style.maxHeight = '250px'; // Batasi tinggi maksimal dropdown
        dropdown.style.overflowY = 'auto';  // Aktifkan scrollbar jika item melimpah
        dropdown.style.display = 'none'; 
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            currentFocus = -1; // Reset fokus setiap kali ada ketikan baru

            if (query.length < 2) {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
                return;
            }

            fetch('/api/sparepart/search?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(response => {
                    if (searchInput.value.trim() === '') return; 
                    const data = response.data || response;

                    dropdown.style.display = 'block'; 
                    dropdown.innerHTML = ''; 

                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'dropdown-item';
                            
                            // Gaya Baris Dropdown
                            li.style.padding = '12px 16px';
                            li.style.cursor = 'pointer';
                            li.style.borderBottom = '1px solid #f1f5f9';
                            li.style.transition = 'background 0.15s';
                            
                            // Efek Hover Menggunakan Mouse (tetap sinkron dengan keyboard)
                            li.addEventListener('mouseover', () => {
                                removeActiveStyles();
                                li.style.backgroundColor = '#f8fafc';
                            });
                            li.addEventListener('mouseout', () => {
                                li.style.backgroundColor = 'transparent';
                            });
                            
                            const itemName = item.name || item.nama_sparepart;
                            const itemStock = item.stock !== undefined ? item.stock : item.stok;

                            let badgeClass = 'badge-success';
                            let badgeColor = '#22c55e'; 
                            if (itemStock == 0) {
                                badgeClass = 'badge-danger';
                                badgeColor = '#ef4444'; 
                            } else if (itemStock < 5) {
                                badgeClass = 'badge-warning';
                                badgeColor = '#eab308'; 
                            }

                            li.innerHTML = `
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">${itemName}</div>
                                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">SKU: ${item.sku}</div>
                                    </div>
                                    <span style="font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 9999px; background-color: ${badgeColor}20; color: ${badgeColor};" class="badge ${badgeClass}">${itemStock} unit</span>
                                </div>
                            `;
                            
                            // Aksi ketika item diklik mouse
                            li.addEventListener('click', () => {
                                selectItem(item, itemName, itemStock, badgeClass);
                            });
                            dropdown.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.style.padding = '12px 16px';
                        li.style.color = '#64748b';
                        li.style.fontSize = '14px';
                        li.textContent = 'Sparepart tidak ditemukan';
                        dropdown.appendChild(li);
                    }
                })
                .catch(err => console.error("Error search API:", err));
        });

        // --- NAVIGASI KEYBOARD (Panah Atas, Panah Bawah, Enter) ---
        searchInput.addEventListener('keydown', function(e) {
            let listItems = dropdown.getElementsByTagName('li');
            
            // Jika dropdown sedang tutup atau kosong, abaikan fungsi keyboard
            if (dropdown.style.display === 'none' || listItems.length === 0 || listItems[0].textContent === 'Sparepart tidak ditemukan') {
                return;
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault(); // Mencegah kursor melompat di kotak input
                currentFocus++;
                setActiveRow(listItems);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentFocus--;
                setActiveRow(listItems);
            } else if (e.key === 'Enter') {
                e.preventDefault(); // Mencegah submit form bawaan browser
                if (currentFocus > -1 && listItems[currentFocus]) {
                    listItems[currentFocus].click(); // Simulasikan klik pada baris yang aktif
                }
            }
        });

        // Sembunyikan dropdown jika klik di luar area input
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                closeDropdown();
            }
        });
    }

    // Fungsi Pembantu 1: Menerapkan gaya aktif visual pada baris pilihan keyboard
    function setActiveRow(items) {
        if (!items) return false;
        removeActiveStyles(); // Reset semua warna latar belakang terlebih dahulu
        
        if (currentFocus >= items.length) currentFocus = 0; // Jika mentok bawah, kembali ke atas
        if (currentFocus < 0) currentFocus = items.length - 1; // Jika mentok atas, lompat ke paling bawah
        
        // Beri warna latar belakang penanda fokus aktif
        items[currentFocus].style.backgroundColor = '#f1f5f9';
        
        // Pastikan baris yang aktif otomatis menggulir (scroll) ke dalam pandangan mata
        items[currentFocus].scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    // Fungsi Pembantu 2: Membersihkan semua gaya background aktif
    function removeActiveStyles() {
        let listItems = dropdown.getElementsByTagName('li');
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].style.backgroundColor = 'transparent';
        }
    }

    // Fungsi Pembantu 3: Menutup & mengosongkan kontainer dropdown
    function closeDropdown() {
        dropdown.style.display = 'none';
        dropdown.innerHTML = '';
        currentFocus = -1;
    }

    // Fungsi Pembantu 4: Logika penanganan ketika item terpilih (via klik / Enter)
    function selectItem(item, itemName, itemStock, badgeClass) {
        searchInput.value = itemName;
        closeDropdown();
        
        selectedSparepartId = item.id;
        
        if (stockCard) {
            stockCard.classList.add('show');
            stockCard.style.display = 'flex'; 
        }
        if (stockSku) stockSku.textContent = item.sku;
        if (stockName) stockName.textContent = itemName;
        if (stockCount) stockCount.textContent = itemStock;
        if (stockBadge) {
            stockBadge.textContent = itemStock > 0 ? (itemStock < 5 ? 'Stok Menipis' : 'Stok Aman') : 'Habis';
            stockBadge.className = 'badge ' + badgeClass;
        }

        const btnRequest = document.getElementById('btn-request-part');
        if(btnRequest) {
            btnRequest.disabled = itemStock === 0;
        }
    }

    // Aksi tombol Tambah ke Work Order (Mekanik)
    const btnRequest = document.getElementById('btn-request-part');
    const toastOverlay = document.getElementById('toast-overlay');
    const toastIcon = document.getElementById('toast-icon');
    const toastText = document.getElementById('toast-text');

    if (btnRequest) {
        btnRequest.addEventListener('click', function() {
            const qty = document.getElementById('request-qty').value;
            if (!selectedSparepartId) return;

            const woIdMekanikInput = document.getElementById('input_wo_id_mekanik');
            const woIdMekanik = woIdMekanikInput ? woIdMekanikInput.value : 101;

            const formData = new FormData();
            formData.append('sparepart_id', selectedSparepartId);
            formData.append('work_order_id', woIdMekanik); 
            formData.append('quantity', qty);

            if (toastOverlay) {
                toastOverlay.classList.add('active');
                toastIcon.className = 'spinner';
                toastIcon.innerHTML = '';
                toastText.textContent = 'Memproses request...';
                toastText.style.color = 'var(--text-main)';
            }
            btnRequest.disabled = true;

            fetch('/api/sparepart/request', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if (toastOverlay) {
                        toastIcon.className = 'checkmark';
                        toastIcon.innerHTML = '✓';
                        toastText.textContent = 'Berhasil ditambahkan!';
                        toastText.style.color = 'var(--success-text)';

                        setTimeout(() => {
                            toastOverlay.classList.remove('active');
                            if (stockCard) {
                                stockCard.classList.remove('show');
                                stockCard.style.display = 'none';
                            }
                            searchInput.value = '';
                            selectedSparepartId = null;
                        }, 1500);
                    }
                } else {
                    if (toastOverlay) {
                        toastIcon.className = '';
                        toastIcon.innerHTML = '❌';
                        toastText.textContent = data.message || 'Gagal menambahkan';
                        toastText.style.color = 'var(--danger)';
                        setTimeout(() => toastOverlay.classList.remove('active'), 2500);
                    }
                }
            })
            .catch(err => {
                if (toastOverlay) {
                    toastIcon.className = '';
                    toastIcon.innerHTML = '⚠️';
                    toastText.textContent = 'Terjadi kesalahan sistem.';
                    toastText.style.color = 'var(--danger)';
                    setTimeout(() => toastOverlay.classList.remove('active'), 2500);
                }
            })
            .finally(() => {
                btnRequest.disabled = false;
            });
        });
    }

    // ====================================================================
    // FITUR 2: KASIR - TARIK INVOICE DRAFT (PBI-13.6)
    // ====================================================================
    const btnLoadInvoice = document.getElementById('btn-load-invoice');
    const invoiceCard = document.getElementById('invoice-card');
    const invoiceItems = document.getElementById('invoice-items');
    const invTotal = document.getElementById('inv-total');
    const invWoStr = document.getElementById('inv-wo');

    if (btnLoadInvoice) {
        btnLoadInvoice.addEventListener('click', function() {
            const woIdInput = document.getElementById('input_wo_id');
            const woId = woIdInput ? woIdInput.value : null;
            
            const toastOverlayKasir = document.getElementById('toast-overlay');
            const toastIconKasir = document.getElementById('toast-icon');
            const toastTextKasir = document.getElementById('toast-text');
            
            if (!toastOverlayKasir) return; 
            
            if (!woId) {
                toastOverlayKasir.classList.add('active');
                toastIconKasir.className = '';
                toastIconKasir.innerHTML = '⚠️';
                toastTextKasir.textContent = 'Masukkan nomor Work Order!';
                toastTextKasir.style.color = 'var(--warning)';
                setTimeout(() => toastOverlayKasir.classList.remove('active'), 2000);
                return;
            }

            const originalText = btnLoadInvoice.innerText;
            btnLoadInvoice.innerText = 'Mencari...';
            btnLoadInvoice.disabled = true;

            toastOverlayKasir.classList.add('active');
            toastIconKasir.className = 'spinner';
            toastIconKasir.innerHTML = '';
            toastTextKasir.textContent = 'Mencari data tagihan...';
            toastTextKasir.style.color = 'var(--text-main)';

            fetch('/api/invoice/draft?work_order_id=' + woId)
                .then(res => res.json())
                .then(data => {
                    invoiceItems.innerHTML = '';

                    if (data && data.success && data.spareparts && data.spareparts.length > 0) {
                        toastIconKasir.className = 'checkmark';
                        toastIconKasir.innerHTML = '✓';
                        toastTextKasir.textContent = 'Tagihan ditemukan!';
                        toastTextKasir.style.color = 'var(--success-text)';
                        setTimeout(() => toastOverlayKasir.classList.remove('active'), 1000);

                        invoiceCard.style.display = 'flex';
                        invWoStr.textContent = woId;

                        data.spareparts.forEach(item => {
                            const price = parseFloat(item.price);
                            const subtotal = parseFloat(item.subtotal);

                            invoiceItems.innerHTML += `
                                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-body); padding: 12px; border-radius: 8px;">
                                    <div>
                                        <p style="margin: 0; font-weight: 600; color: var(--text-main); font-size: 15px;">${item.name}</p>
                                        <p style="margin: 0; font-size: 13px; color: var(--text-muted);">${item.quantity}x @ Rp ${price.toLocaleString('id-ID')}</p>
                                    </div>
                                    <div style="font-weight: 600; color: var(--text-main);">
                                        Rp ${subtotal.toLocaleString('id-ID')}
                                    </div>
                                </div>
                            `;
                        });

                        const biayaJasa = parseFloat(data.service_fee || 100000);
                        
                        invoiceItems.innerHTML += `
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFF7ED; padding: 12px; border-radius: 8px; border: 1px solid #FFEDD5;">
                                <div>
                                    <p style="margin: 0; font-weight: 600; color: #C2410C; font-size: 15px;">Biaya Jasa Servis / Mekanik</p>
                                    <p style="margin: 0; font-size: 13px; color: #EA580C;">Flat Rate</p>
                                </div>
                                <div style="font-weight: 600; color: #C2410C;">
                                    Rp ${biayaJasa.toLocaleString('id-ID')}
                                </div>
                            </div>
                        `;

                        const grandTotal = parseFloat(data.total_amount || 0);
                        invTotal.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

                    } else {
                        invoiceCard.style.display = 'none';
                        toastIconKasir.className = '';
                        toastIconKasir.innerHTML = '❌';
                        toastTextKasir.textContent = data.message || ('WO #' + woId + ' kosong / tidak ditemukan.');
                        toastTextKasir.style.color = 'var(--danger)';
                        setTimeout(() => toastOverlayKasir.classList.remove('active'), 2500);
                    }
                })
                .catch(err => {
                    console.error('Error fetching invoice:', err);
                    toastIconKasir.className = '';
                    toastIconKasir.innerHTML = '⚠️';
                    toastTextKasir.textContent = 'Terjadi kesalahan sistem.';
                    toastTextKasir.style.color = 'var(--danger)';
                    setTimeout(() => toastOverlayKasir.classList.remove('active'), 2500);
                })
                .finally(() => {
                    btnLoadInvoice.innerText = originalText;
                    btnLoadInvoice.disabled = false;
                });
        });
    }

    // ====================================================================
    // FITUR 3: KASIR - CETAK & SELESAIKAN (PBI-13.6 Tambahan)
    // ====================================================================
    const btnPrint = document.getElementById('btn-print-invoice');
    
    if (btnPrint) {
        btnPrint.addEventListener('click', function() {
            const toastOverlayPrint = document.getElementById('toast-overlay');
            const toastIconPrint = document.getElementById('toast-icon');
            const toastTextPrint = document.getElementById('toast-text');

            if (toastOverlayPrint) {
                toastOverlayPrint.classList.add('active');
                toastIconPrint.className = 'checkmark';
                toastIconPrint.innerHTML = '✓';
                toastTextPrint.textContent = 'Transaksi Selesai! Mencetak struk...';
                toastTextPrint.style.color = 'var(--success-text)';
            }

            setTimeout(() => {
                window.print();
            }, 1000);

            setTimeout(() => {
                if (toastOverlayPrint) toastOverlayPrint.classList.remove('active');
                window.location.reload(); 
            }, 3000);
        });
    }

});