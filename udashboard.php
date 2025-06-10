<?php
// udashboard.php
require 'koneksi.php';

// Ambil jumlah produk
$qProduk = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
$produkCount = mysqli_fetch_assoc($qProduk)['total'] ?? 0;

// Ambil jumlah transaksi
$qTransaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
$transaksiCount = mysqli_fetch_assoc($qTransaksi)['total'] ?? 0;

// Ambil 3 produk terbaru
$qProdukBaru = mysqli_query($conn, "SELECT nama_produk FROM produk ORDER BY id_produk DESC LIMIT 3");
$produkTerbaru = [];
while ($row = mysqli_fetch_assoc($qProdukBaru)) {
    $produkTerbaru[] = $row['nama_produk'];
}

// Ambil 2 transaksi terakhir
$qTransaksiTerakhir = mysqli_query($conn, "
    SELECT td.produk, td.jumlah, td.harga
    FROM detail_transaksi td
    JOIN transaksi t ON td.id_transaksi = t.id_transaksi
    ORDER BY td.id_transaksi DESC LIMIT 5
");
$transaksiTerakhir = [];
while ($row = mysqli_fetch_assoc($qTransaksiTerakhir)) {
    $transaksiTerakhir[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User | Gudang Ceria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">
    <aside class="w-64 h-screen bg-grey p-6 shadow fixed sidebar">
        <h1 class="text-2xl font-bold mb-10 text-white-600">RIDJIK</h1>
        <nav class="flex flex-col space-y-5">
            <a href="udashboard.php" class="sidebar-link active">🏠<span>Beranda</span></a>
            <a href="uproduk.php" class="sidebar-link">🛒<span>Lihat Produk</span></a>
            <a href="ubahan.php" class="sidebar-link">🗃️<span>Bahan</span></a>
            <a href="ulogin.php" class="sidebar-link">🔒<span>Logout</span></a>
        </nav>
    </aside>
    <main class="flex-1 p-10 main-content ml-64">
        <h2 class="text-3xl font-bold mb-8">Selamat Datang, User!</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-blue-500 text-4xl mb-2">🛒</div>
                <h3 class="text-lg font-semibold">Produk Tersedia</h3>
                <p class="text-2xl font-bold mt-2"><?= $produkCount ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-green-500 text-4xl mb-2">📜</div>
                <h3 class="text-lg font-semibold">Transaksi</h3>
                <p class="text-2xl font-bold mt-2"><?= $transaksiCount ?></p>
            </div>
        </div>

        <!-- Riwayat dan Info Produk -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Produk Terbaru</h3>
                <ul class="text-gray-600 space-y-2">
                    <?php foreach ($produkTerbaru as $produk): ?>
                        <li>• <?= htmlspecialchars($produk) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Transaksi Terakhir</h3>
                <ul class="text-gray-600 space-y-2">
                    <?php foreach ($transaksiTerakhir as $t): ?>
                        <li><?= htmlspecialchars($t['produk']) ?> - <?= $t['jumlah'] ?> item <p>Rp <?= number_format($t['harga'], 0, ',', '.') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
