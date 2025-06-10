<?php
require 'koneksi.php';

// Siapkan query dasar
$query = "SELECT * FROM produk";

// Eksekusi query
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lihat Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">
    <aside class="w-64 h-screen bg-grey p-6 shadow fixed sidebar">
        <h1 class="text-2xl font-bold mb-10 text-white-600">RIDJIK</h1>
        <nav class="flex flex-col space-y-5">
            <a href="udashboard.php" class="sidebar-link">🏠<span>Beranda</span></a>
            <a href="uproduk.php" class="sidebar-link active">🛒<span>Lihat Produk</span></a>
            <a href="ubahan.php" class="sidebar-link">🗃️<span>Bahan</span></a>
           <a href="ulogin.php" class="sidebar-link">🔒<span>Logout</span></a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content ml-64 p-10">
        <h1 class="text-3xl font-bold mb-6">Daftar Produk</h1>

        <!-- Produk Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow product-table">
                <thead class="table-header">
                    <tr>
                        <th class="table-cell">Nama Produk</th>
                        <th class="table-cell">Kode Produk</th>
                        <th class="table-cell">Harga</th>
                        <th class="table-cell"> Stok </th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="table-cell"><?= htmlspecialchars($row['nama_produk']) ?></td>
                                <td class="table-cell"><?= $row['kode_produk'] ?></td>
                                <td class="table-cell">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td class="table-cell"><?= $row['stok_produk'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-gray-500">Tidak ada produk ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
