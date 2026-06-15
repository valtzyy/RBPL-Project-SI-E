<!DOCTYPE html>
<html lang="en">
<head>
    <title>Executive Management Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="font-family: Arial, sans-serif; margin: 30px;">
    <h1>📊 Executive Dashboard Manajerial</h1>
    <a href="/sparepart">📦 Masuk ke Sistem Logistik Gudang Suku Cadang</a>
    <hr>

    <div style="display: flex; gap: 30px; margin-top: 20px;">
        <div style="width: 45%; border: 1px solid #ccc; padding: 20px; border-radius: 8px; text-align: center;">
            <h3> Rapor Konversi Penjualan & Rasio Aplikasi Kredit</h3>
            <div style="width: 300px; margin: 0 auto;">
                <canvas id="chartKpi"></canvas>
            </div>
            <p id="totalUnitText" style="font-weight: bold; margin-top: 15px;"></p>
        </div>

        <div style="width: 50%; border: 1px solid #ccc; padding: 20px; border-radius: 8px;">
            <h3>📈 Grafik Tren Volume Kunjungan Servis Bulanan</h3>
            <canvas id="chartServiceTrends"></canvas>
        </div>
    </div>

    <script>
        // Ambil Data Metrik KPI Finansial via AJAX Endpoint (PBI-14.6)
        fetch('/api/dashboard/kpi')
            .then(res => res.json())
            .then(data => {
                document.getElementById('totalUnitText').innerText = "Total Volume Penjualan Armada Mobil: " + data.total_unit + " Unit";
                
                new Chart(document.getElementById('chartKpi'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Lunas (%)', 'Kredit Ditolak (%)'],
                        datasets: [{
                            data: [data.persen_lunas, data.persen_ditolak],
                            backgroundColor: ['#28a745', '#dc3545']
                        }]
                    }
                });
            });

        // Ambil Data Grafik Tren Kedatangan Servis via AJAX Endpoint (PBI-14.7)
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
                            borderColor: '#007bff',
                            fill: false,
                            tension: 0.1
                        }]
                    }
                });
            });
    </script>
</body>
</html>