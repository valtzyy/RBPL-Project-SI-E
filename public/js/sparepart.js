document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_sparepart');
    if (!searchInput) return;

    const dropdown = document.getElementById('autocomplete-dropdown');
    const stockCard = document.getElementById('stock-indicator');
    const stockSku = document.getElementById('stock-sku');
    const stockName = document.getElementById('stock-name');
    const stockCount = document.getElementById('stock-count');
    const stockBadge = document.getElementById('stock-badge');

    let currentFocus = -1; // Mengingat urutan item yang sedang disorot keyboard

    // Event 1: Input Pencarian
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = ''; 
        currentFocus = -1; // Reset fokus tiap kali mengetik

        if (query === '') {
            stockCard.classList.remove('show');
            dropdown.classList.remove('show');
            return;
        }

        if (query.length < 2) {
            dropdown.classList.remove('show');
            return;
        }

        // Ambil data langsung dari API backend secara real-time
        fetch('/api/sparepart/search?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                const filteredData = data.map(item => ({
                    id: item.id,
                    sku: item.sku,
                    nama: item.name,
                    stok: parseInt(item.stock)
                }));

                dropdown.classList.add('show'); // Tampilkan kotak dropdown
                dropdown.innerHTML = ''; 

                if (filteredData.length > 0) {
                    // Jika data cocok, gambar list itemnya
                    filteredData.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'dropdown-item';
                        li.dataset.item = JSON.stringify(item);
                        
                        let textColor = '#10b981'; 
                        if (item.stok === 0) textColor = '#ef4444'; 
                        else if (item.stok <= 10) textColor = '#f59e0b'; 

                        li.innerHTML = `
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <div>
                                    <div class="item-name">${item.nama}</div>
                                    <div class="item-sku">SKU: ${item.sku}</div>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-size: 13px; font-weight: 700; color: ${textColor}">
                                        ${item.stok} unit
                                    </span>
                                </div>
                            </div>
                        `;
                        dropdown.appendChild(li);
                    });
                } else {
                    // IDE 1: Munculkan Empty State jika tidak ada yang cocok
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
                    li.style.cursor = 'default'; // Kursor biasa, bukan tangan
                    dropdown.appendChild(li);
                }
            })
            .catch(err => {
                console.error("Error fetching spareparts:", err);
            });
    });

    // IDE 2: Event 2.1 - Navigasi Keyboard
    searchInput.addEventListener('keydown', function(e) {
        const items = dropdown.getElementsByClassName('dropdown-item');
        if (!dropdown.classList.contains('show') || items.length === 0) return;

        // Abaikan navigasi jika yang muncul hanya pesan "tidak ditemukan"
        if (items[0].classList.contains('empty-state')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault(); // Cegah kursor teks melompat
            currentFocus++;
            addActive(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            addActive(items);
        } else if (e.key === 'Enter') {
            e.preventDefault(); // Cegah form tersubmit (jika ada form)
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click(); // Simulasikan klik pada item yang disorot
            }
        }
    });

    // Fungsi pembantu untuk menambah warna sorotan
    function addActive(items) {
        if (!items) return;
        removeActive(items); // Bersihkan semua sorotan dulu
        
        if (currentFocus >= items.length) currentFocus = 0; // Balik ke atas jika sudah mentok bawah
        if (currentFocus < 0) currentFocus = (items.length - 1); // Balik ke bawah jika mentok atas
        
        items[currentFocus].classList.add('keyboard-active');
        
        // Auto-scroll list ke item yang disorot
        items[currentFocus].scrollIntoView({ block: 'nearest' });
    }

    // Fungsi pembantu untuk menghapus semua sorotan
    function removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove('keyboard-active');
        }
    }

    // Event 3: Klik Item Dropdown
    dropdown.addEventListener('click', function(e) {
        const itemEl = e.target.closest('.dropdown-item');
        
        // Cegah error jika mekanik mengklik kotak pesan "tidak ditemukan"
        if (!itemEl || itemEl.classList.contains('empty-state')) return;

        const itemData = JSON.parse(itemEl.dataset.item);

        searchInput.value = itemData.nama;
        dropdown.classList.remove('show');
        showStockIndicator(itemData);
    });

    // Fungsi Render Kartu Indikator Stok
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

        stockCard.classList.add('show');
    }

    // Event 4: Tutup dropdown jika klik area luar
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
});