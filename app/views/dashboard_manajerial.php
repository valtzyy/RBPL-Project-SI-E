<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Management Dashboard</title>
    <!-- Google Font & Chart.js -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1300px;
            margin: 0 auto;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        h1 {
            margin: 0;
            font-size: 26px;
            color: #1a202c;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-btn {
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: background 0.2s;
        }
        .nav-btn:hover {
            background-color: #0056b3;
        }
        
        /* KPI Grid Card */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            padding: 20px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #4a5568;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 8px;
        }
        .kpi-metric {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .kpi-metric span.label {
            color: #718096;
        }
        .kpi-metric span.val {
            font-weight: 600;
            color: #2d3748;
        }
        .big-val {
            font-size: 22px;
            font-weight: 700;
            color: #2b6cb0;
            margin-bottom: 12px;
        }

        /* Charts Layout */
        .chart-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        @media(max-width: 768px) {
            .chart-row {
                grid-template-columns: 1fr;
            }
        }
        .chart-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            padding: 20px;
            text-align: center;
        }
        .chart-card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 16px;
            color: #2d3748;
            text-align: left;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 250px;
            width: 100%;
        }

        /* Lists & Tables Layout */
        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        @media(max-width: 992px) {
            .table-row {
                grid-template-columns: 1fr;
            }
        }
        .table-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            padding: 20px;
        }
        .table-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: #2d3748;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }
        .data-table th, .data-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: 600;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .badge-lunas { background-color: #c6f6d5; color: #22543d; }
        .badge-process { background-color: #feebc8; color: #744210; }
        .badge-cancel { background-color: #fed7d7; color: #742a2a; }

        /* Alert Section */
        .alert-box {
            background-color: #fffaf0;
            border-left: 4px solid #dd6b20;
            color: #7b341e;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .alert-box h4 {
            margin-top: 0;
            margin-bottom: 8px;
            font-weight: 600;
            color: #dd6b20;
        }
        .alert-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .alert-box li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <h1>📊 Executive Managerial Dashboard</h1>
            <a href="/sparepart" class="nav-btn">📦 Masuk ke Logistik & Gudang Suku Cadang</a>
        </header>

        <!-- Low Stock Alerts -->
        <div id="lowStockAlert" class="alert-box" style="display: none;">
            <h4>⚠️ Peringatan Stok Kritis (Low-Level Stock Suku Cadang)</h4>
            <ul id="lowStockList">
                <!-- Dynamic Content -->
            </ul>
        </div>

        <!-- KPI Grid -->
        <div class="kpi-grid">
            <!-- Card 1: Penjualan & Finansial -->
            <div class="card">
                <h3 class="card-title">💵 Rapor Finansial & Penjualan Mobil</h3>
                <div class="big-val" id="kpiTotalRevenue">Rp 0</div>
                <div class="kpi-metric">
                    <span class="label">Volume Penjualan Armada</span>
                    <span class="val" id="kpiTotalVolume">0 Unit</span>
                </div>
                <div class="kpi-metric">
                    <span class="label">Rasio Pembayaran Lunas</span>
                    <span class="val" id="kpiPersenLunas">0%</span>
                </div>
                <div class="kpi-metric">
                    <span class="label">Aplikasi Kredit Ditolak</span>
                    <span class="val" id="kpiPersenDitolak" style="color: #c53030;">0%</span>
                </div>
            </div>

            <!-- Card 2: Nilai Aset Dealer -->
            <div class="card">
                <h3 class="card-title">🏢 Nilai Total Aset Terinventaris</h3>
                <div class="big-val" id="kpiTotalAsset">Rp 0</div>
                <div class="kpi-metric">
                    <span class="label">Aset Unit Mobil (Tersedia)</span>
                    <span class="val" id="kpiVehicleAsset">Rp 0</span>
                </div>
                <div class="kpi-metric">
                    <span class="label">Aset Suku Cadang (Gudang)</span>
                    <span class="val" id="kpiSparepartAsset">Rp 0</span>
                </div>
                <div class="kpi-metric">
                    <span class="label">Update Terakhir</span>
                    <span class="val"><?= date('d M Y H:i') ?></span>
                </div>
            </div>

            <!-- Card 3: Rasio Perputaran Suku Cadang -->
            <div class="card">
                <h3 class="card-title">🔄 Perputaran Suku Cadang Gudang</h3>
                <div class="big-val" id="kpiTurnoverRatio">0.00x <span style="font-size: 13px; font-weight: normal; color: #4a5568;">/ thn</span></div>
                <div class="kpi-metric">
                    <span class="label">Nilai Suku Cadang Terpakai (COGS)</span>
                    <span class="val" id="kpiSparepartCogs">Rp 0</span>
                </div>
                <div class="kpi-metric">
                    <span class="label">Rata-rata Nilai Inventaris</span>
                    <span class="val" id="kpiSparepartAvg">Rp 0</span>
                </div>
                <div class="kpi-metric">
                    <span class="label">Status Efisiensi Gudang</span>
                    <span class="val" id="kpiSparepartStatus" style="color: #2b6cb0;">Optimal</span>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="chart-row">
            <!-- Chart 1: Rapor Konversi Kredit -->
            <div class="chart-card">
                <h3>📈 Rasio Aplikasi Kredit vs Pembayaran Lunas</h3>
                <div class="chart-container" style="height: 220px;">
                    <canvas id="chartKpi"></canvas>
                </div>
            </div>

            <!-- Chart 2: Tren Servis Bulanan -->
            <div class="chart-card">
                <h3>🛠️ Tren Kunjungan Servis Bulanan (Kedatangan Kendaraan)</h3>
                <div class="chart-container">
                    <canvas id="chartServiceTrends"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="chart-row">
            <!-- Chart 3: Tren Penjualan Bulanan (Unit & Nominal) -->
            <div class="chart-card">
                <h3>🚗 Tren Grafik Volume & Nominal Penjualan Mobil Bulanan</h3>
                <div class="chart-container">
                    <canvas id="chartSalesTrends"></canvas>
                </div>
            </div>

            <!-- Chart 4: Komposisi Stok Kendaraan -->
            <div class="chart-card">
                <h3>📊 Komposisi Status & Nilai Stok Unit Mobil</h3>
                <div class="chart-container">
                    <canvas id="chartStockStatus"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="table-row">
            <!-- Recent Sales Table -->
            <div class="table-card">
                <h3>📋 Log 10 Transaksi Penjualan Mobil Terbaru</h3>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Transaksi</th>
                                <th>Pelanggan</th>
                                <th>Mobil</th>
                                <th>Tipe Bayar</th>
                                <th>Nominal Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentTransactionsBody">
                            <tr>
                                <td colspan="7" style="text-align: center; color: #718096; padding: 20px;">Memuat data transaksi...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Brands List -->
            <div class="table-card">
                <h3>🏆 5 Merek Mobil Terlaris (Lunas)</h3>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Merek</th>
                                <th>Terjual</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="topBrandsBody">
                            <tr>
                                <td colspan="3" style="text-align: center; color: #718096; padding: 20px;">Memuat data merek...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Data Loading -->
    <script>
        // Formatter Helper
        const formatRupiah = (val) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(val);
        };

        // 1. Fetch & Render KPI Finansial & Credit (Doughnut)
        fetch('/api/dashboard/kpi')
            .then(res => res.json())
            .then(data => {
                document.getElementById('kpiTotalVolume').innerText = data.total_unit + " Unit";
                document.getElementById('kpiPersenLunas').innerText = data.persen_lunas + "%";
                document.getElementById('kpiPersenDitolak').innerText = data.persen_ditolak + "%";
                
                new Chart(document.getElementById('chartKpi'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pembayaran Lunas (%)', 'Aplikasi Kredit Ditolak (%)'],
                        datasets: [{
                            data: [data.persen_lunas, data.persen_ditolak],
                            backgroundColor: ['#28a745', '#dc3545'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });

        // 2. Fetch & Render Tren Kunjungan Servis Bulanan (Line Chart)
        fetch('/api/dashboard/trends')
            .then(res => res.json())
            .then(data => {
                const labelBulan = data.map(item => item.bulan);
                const valueTotal = data.map(item => item.total);

                new Chart(document.getElementById('chartServiceTrends'), {
                    type: 'line',
                    data: {
                        labels: labelBulan,
                        datasets: [{
                            label: 'Jumlah Kendaraan Servis',
                            data: valueTotal,
                            borderColor: '#3182ce',
                            backgroundColor: 'rgba(49, 130, 206, 0.1)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            });

        // 3. Fetch & Render Tren Penjualan Mobil Bulanan (Double Axis Combo Chart)
        fetch('/api/dashboard/sales-trends')
            .then(res => res.json())
            .then(data => {
                const labelBulan = data.map(item => item.bulan);
                const valJumlah = data.map(item => item.jumlah_terjual);
                const valNominal = data.map(item => item.total_nominal);

                // Hitung total revenue untuk dimasukkan ke KPI Card
                const totalRev = valNominal.reduce((acc, curr) => acc + parseFloat(curr || 0), 0);
                document.getElementById('kpiTotalRevenue').innerText = formatRupiah(totalRev);

                new Chart(document.getElementById('chartSalesTrends'), {
                    type: 'bar',
                    data: {
                        labels: labelBulan,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Volume Terjual (Unit)',
                                data: valJumlah,
                                backgroundColor: '#4c51bf',
                                yAxisID: 'yVolume'
                            },
                            {
                                type: 'line',
                                label: 'Nominal Penjualan (Rupiah)',
                                data: valNominal,
                                borderColor: '#e53e3e',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.2,
                                yAxisID: 'yRevenue'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yVolume: {
                                type: 'linear',
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Volume (Unit)'
                                },
                                ticks: { precision: 0 }
                            },
                            yRevenue: {
                                type: 'linear',
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Nominal Rupiah'
                                },
                                grid: {
                                    drawOnChartArea: false // prevent lines overlay
                                },
                                ticks: {
                                    callback: function(value) {
                                        return formatRupiah(value);
                                    }
                                }
                            }
                        }
                    }
                });
            });

        // 4. Fetch & Render Metrik Aset & Turn Over Ratio Suku Cadang
        fetch('/api/dashboard/inventory-kpi')
            .then(res => res.json())
            .then(data => {
                document.getElementById('kpiTotalAsset').innerText = formatRupiah(data.total_asset_value);
                document.getElementById('kpiVehicleAsset').innerText = formatRupiah(data.vehicle_asset_value);
                document.getElementById('kpiSparepartAsset').innerText = formatRupiah(data.sparepart_asset_value);
                
                document.getElementById('kpiTurnoverRatio').innerHTML = data.sparepart_turnover_ratio.toFixed(2) + 'x <span style="font-size: 13px; font-weight: normal; color: #4a5568;">/ thn</span>';
                document.getElementById('kpiSparepartCogs').innerText = formatRupiah(data.sparepart_cogs);
                document.getElementById('kpiSparepartAvg').innerText = formatRupiah(data.sparepart_avg_inventory);

                // Tentukan status efisiensi gudang
                const ratio = data.sparepart_turnover_ratio;
                const statusSpan = document.getElementById('kpiSparepartStatus');
                if (ratio === 0) {
                    statusSpan.innerText = 'Tidak Ada Perputaran';
                    statusSpan.style.color = '#718096';
                } else if (ratio < 0.5) {
                    statusSpan.innerText = 'Lambat (Overstock)';
                    statusSpan.style.color = '#dd6b20';
                } else {
                    statusSpan.innerText = 'Optimal / Cepat';
                    statusSpan.style.color = '#38a169';
                }
            });

        // 5. Fetch & Render Detail Tabel Transaksi, Merek Mobil, & Status Stok
        fetch('/api/dashboard/details')
            .then(res => res.json())
            .then(data => {
                // Render Recent Transactions
                const txBody = document.getElementById('recentTransactionsBody');
                txBody.innerHTML = '';
                if (data.recent_transactions.length === 0) {
                    txBody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #718096;">Belum ada data transaksi.</td></tr>`;
                } else {
                    data.recent_transactions.forEach(tx => {
                        const dateStr = new Date(tx.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                        txBody.innerHTML += `
                            <tr>
                                <td>${dateStr}</td>
                                <td><strong>${tx.transaction_code}</strong></td>
                                <td>${tx.customer_name}</td>
                                <td>${tx.brand} ${tx.type}</td>
                                <td>${tx.payment_type || '-'}</td>
                                <td>${formatRupiah(tx.price)}</td>
                                <td><span class="badge badge-${tx.status}">${tx.status}</span></td>
                            </tr>
                        `;
                    });
                }

                // Render Top Brands
                const brandBody = document.getElementById('topBrandsBody');
                brandBody.innerHTML = '';
                if (data.top_brands.length === 0) {
                    brandBody.innerHTML = `<tr><td colspan="3" style="text-align: center; color: #718096;">Belum ada merek terlaris.</td></tr>`;
                } else {
                    data.top_brands.forEach(br => {
                        brandBody.innerHTML += `
                            <tr>
                                <td><strong>${br.brand}</strong></td>
                                <td>${br.total_sold} Unit</td>
                                <td>${formatRupiah(br.total_revenue)}</td>
                            </tr>
                        `;
                    });
                }

                // Render Low Stock Warnings
                if (data.low_stock_spareparts && data.low_stock_spareparts.length > 0) {
                    const alertBox = document.getElementById('lowStockAlert');
                    const alertList = document.getElementById('lowStockList');
                    alertList.innerHTML = '';
                    data.low_stock_spareparts.forEach(sp => {
                        alertList.innerHTML += `<li>Suku cadang <strong>${sp.name}</strong> (SKU: ${sp.sku}) dalam kondisi kritis! Sisa Stok: ${sp.stock} Pcs (Batas Min: ${sp.min_stock} Pcs).</li>`;
                    });
                    alertBox.style.display = 'block';
                }

                // Render Stock Status Pie/Doughnut Chart
                const labelsStock = data.stock_stats.map(item => item.status);
                const valuesStock = data.stock_stats.map(item => item.total);

                new Chart(document.getElementById('chartStockStatus'), {
                    type: 'doughnut',
                    data: {
                        labels: labelsStock,
                        datasets: [{
                            data: valuesStock,
                            backgroundColor: ['#3182ce', '#dd6b20', '#38a169'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });
    </script>
</body>
</html>