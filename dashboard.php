<?php
include 'koneksi.php';

// Total Produk
$total_produk_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk");
$total_produk = mysqli_fetch_assoc($total_produk_result)['total'];

// Total Stok
$stok_result = mysqli_query($conn, "SELECT SUM(stok_produk) AS total_stok FROM produk");
$total_stok = mysqli_fetch_assoc($stok_result)['total_stok'];

// Total Transaksi
$transaksi_result = mysqli_query($conn, "SELECT COUNT(*) AS total_transaksi FROM transaksi");
$total_transaksi = mysqli_fetch_assoc($transaksi_result)['total_transaksi'];

// Query untuk data transaksi bulanan
$monthly_transactions = [];
for ($i = 1; $i <= 12; $i++) {
    $month_query = "SELECT COUNT(*) as total 
                   FROM transaksi 
                   WHERE MONTH(tanggal) = $i 
                   AND YEAR(tanggal) = YEAR(CURDATE())";
    $month_result = mysqli_query($conn, $month_query);
    $month_data = mysqli_fetch_assoc($month_result);
    
    $month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                   'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    $monthly_transactions[] = [
        'month' => $month_names[$i-1],
        'transactions' => (int)$month_data['total']
    ];
}

// Produk Terbaru
$produk_terbaru_result = mysqli_query($conn, "SELECT nama_produk, kode_produk, stok_produk FROM produk ORDER BY id_produk DESC LIMIT 5");

// Transaksi Terbaru
$query = "
    SELECT 
    t.id_transaksi, 
    t.tanggal, 
    td.total,
    GROUP_CONCAT(CONCAT(dt.produk, ' ', dt.jumlah, ' item') SEPARATOR ', ') AS daftar_produk
    FROM transaksi t
    JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
    JOIN (
        SELECT id_transaksi, SUM(jumlah * harga) AS total
        FROM detail_transaksi
        GROUP BY id_transaksi
    ) td ON t.id_transaksi = td.id_transaksi
    GROUP BY t.id_transaksi
    ORDER BY t.tanggal DESC
    LIMIT 5;
";
$transaksi_terbaru_result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | RIDJIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="flex">

    <!-- Sidebar -->
    <aside class="sidebar bg-gray-800 text-white p-6 w-64 min-h-screen overflow-hidden">
        <h1 class="text-2xl font-bold mb-10">RIDJIK</h1>
        <nav class="flex flex-col space-y-5">
            <a href="dashboard.php" class="nav-link">
                <span>🏠</span> 
                <span>Dashboard</span>
            </a>
            <a href="produk.php" class="nav-link"><span>📦</span> <span>Produk</span></a>
            <a href="bahan.php" class="nav-link">
                <span>🗃️</span> 
                <span>Bahan</span>
            </a>
            <a href="transaksi.php" class="nav-link">
                <span>📈</span> 
                <span>Transaksi</span>
            </a>
            <a href="resep.php" class="nav-link">
                <span>📋</span>
                <span>Resep</span>
            </a>
            <a href="#" class="nav-link">
                <span>📑</span> 
                <span>Laporan</span>
            </a>
            <a href="login.php" class="nav-link">
                <span>🚪</span> 
                <span>Log Out</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content p-10 w-full">
        <h2 class="text-3xl font-bold mb-8">Dashboard</h2>
        
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <a href="produk.php" class="group">
                <div class="bg-white p-6 rounded-lg shadow text-center transition duration-300 group-hover:bg-gray-100 cursor-pointer">
                    <div class="text-blue-500 text-4xl mb-2">🛍️</div>
                    <h3 class="text-lg font-semibold">Total Produk</h3>
                    <p class="text-2xl font-bold mt-2"><?= $total_produk ?></p>
                </div>
            </a>
             
            <div class="bg-white p-6 rounded-lg shadow text-center hover:bg-gray-100 transition duration-300 cursor-pointer">
                <div class="text-green-500 text-4xl mb-2">📦</div>
                <h3 class="text-lg font-semibold">Stok Tersedia</h3>
                <p class="text-2xl font-bold mt-2"><?= number_format($total_stok) ?></p>
            </div>

            <!-- Card Transaksi dengan Mini Chart -->
            <div class="bg-white p-6 rounded-lg shadow hover:bg-gray-100 transition duration-300 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-orange-500 text-4xl mb-2">📈</div>
                        <h3 class="text-lg font-semibold">Total Transaksi</h3>
                        <p class="text-2xl font-bold mt-2"><?= $total_transaksi ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Chart Section -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Grafik Transaksi Bulanan</h3>
                <div class="flex gap-2">
                    <button id="lineChart" class="chart-btn px-3 py-1 rounded text-sm bg-blue-500 text-white">
                        📈 Line
                    </button>
                    <button id="barChart" class="chart-btn px-3 py-1 rounded text-sm bg-gray-200 text-gray-700 hover:bg-gray-300">
                        📊 Bar
                    </button>
                </div>
            </div>

            <div class="h-64 mb-4">
                <canvas id="transactionChart"></canvas>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-sm">
                <div class="p-2 bg-gray-50 rounded">
                    <div class="text-gray-500">Rata-rata</div>
                    <div class="font-bold" id="avgStat"><?= round(array_sum(array_column($monthly_transactions, 'transactions')) / 12) ?></div>
                </div>
                <div class="p-2 bg-green-50 rounded">
                    <div class="text-gray-500">Tertinggi</div>
                    <div class="font-bold text-green-600" id="maxStat"><?= max(array_column($monthly_transactions, 'transactions')) ?></div>
                </div>
                <div class="p-2 bg-red-50 rounded">
                    <div class="text-gray-500">Terendah</div>
                    <div class="font-bold text-red-600" id="minStat"><?= min(array_column($monthly_transactions, 'transactions')) ?></div>
                </div>
                <div class="p-2 bg-blue-50 rounded">
                    <div class="text-gray-500">Bulan Ini</div>
                    <div class="font-bold text-blue-600" id="currentMonth"><?= $monthly_transactions[date('n')-1]['transactions'] ?></div>
                </div>
            </div>
        </div>

        <!-- Produk dan Transaksi Terbaru -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Produk Terbaru -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Produk Terbaru</h3>
                <?php if (mysqli_num_rows($produk_terbaru_result) > 0): ?>
                    <ul class="space-y-2">
                        <?php while($produk = mysqli_fetch_assoc($produk_terbaru_result)): ?>
                            <li class="border-b pb-2">
                                <strong><?= htmlspecialchars($produk['nama_produk']) ?></strong><br>
                                <span class="text-sm text-gray-500">Kode: <?= htmlspecialchars($produk['kode_produk']) ?> | Stok: <?= $produk['stok_produk'] ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada data produk</p>
                <?php endif; ?>
            </div>

            <!-- Transaksi Terbaru -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Transaksi Terkini</h3>
                <?php if (mysqli_num_rows($transaksi_terbaru_result) > 0): ?>
                    <ul class="space-y-3">
                        <?php while($trx = mysqli_fetch_assoc($transaksi_terbaru_result)): ?>
                            <li class="border-b pb-2">
                                <div class="font-medium text-sm"><?= $trx['daftar_produk'] ?></div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?= date('d/m/Y', strtotime($trx['tanggal'])) ?> - 
                                    <span class="font-semibold text-green-600">Rp<?= number_format($trx['total'], 0, ',', '.') ?></span>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada data transaksi</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

</div>

<script>
// Data dari PHP
const monthlyData = <?= json_encode($monthly_transactions) ?>;

let chart;
let miniChart;

// Initialize main chart
function initChart(type = 'line') {
    const ctx = document.getElementById('transactionChart').getContext('2d');
    
    if (chart) {
        chart.destroy();
    }

    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

    chart = new Chart(ctx, {
        type: type,
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Transaksi',
                data: monthlyData.map(d => d.transactions),
                borderColor: '#3b82f6',
                backgroundColor: type === 'line' ? gradient : '#3b82f6',
                borderWidth: 2,
                fill: type === 'line',
                tension: 0.3,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

// Initialize mini chart
function initMiniChart() {
    const ctx = document.getElementById('miniChart').getContext('2d');
    
    miniChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.slice(-6).map(d => d.month),
            datasets: [{
                data: monthlyData.slice(-6).map(d => d.transactions),
                borderColor: '#f97316',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    display: false
                },
                x: {
                    display: false
                }
            },
            elements: {
                point: {
                    radius: 0
                }
            }
        }
    });
}

// Chart type buttons
document.getElementById('lineChart').addEventListener('click', function() {
    initChart('line');
    document.querySelectorAll('.chart-btn').forEach(btn => {
        btn.className = 'chart-btn px-3 py-1 rounded text-sm bg-gray-200 text-gray-700 hover:bg-gray-300';
    });
    this.className = 'chart-btn px-3 py-1 rounded text-sm bg-blue-500 text-white';
});

document.getElementById('barChart').addEventListener('click', function() {
    initChart('bar');
    document.querySelectorAll('.chart-btn').forEach(btn => {
        btn.className = 'chart-btn px-3 py-1 rounded text-sm bg-gray-200 text-gray-700 hover:bg-gray-300';
    });
    this.className = 'chart-btn px-3 py-1 rounded text-sm bg-blue-500 text-white';
});

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initChart('line');
    initMiniChart();
});
</script>

</body>
</html>