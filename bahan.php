<?php
include 'koneksi.php';

$tampilkan_form = false; // default form tambah bahan tidak muncul

// Ambil bahan dari database
$query = "SELECT * FROM bahan";
$result = mysqli_query($conn, $query);

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    // Proses simpan laporan stok
    if ($aksi === 'laporan' && isset($_POST['tanggal'], $_POST['stok_laporan'])) {
        $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
        $stok_laporan = $_POST['stok_laporan'];

        foreach ($stok_laporan as $id_bahan => $stok) {
            $id_bahan_esc = (int)$id_bahan;
            $stok_esc = (int)$stok;

            $sql = "INSERT INTO laporan_stok (id_bahan, tanggal, stok)
                    VALUES ('$id_bahan_esc', '$tanggal', '$stok_esc')";
            mysqli_query($conn, $sql);
        }
        $pesan = "Data berhasil disimpan ke laporan!";
        $tipe_pesan = "success";
    }

    // Proses tambah bahan baru
    if ($aksi === 'tambah_bahan' && isset($_POST['nama_bahan'], $_POST['stok_bahan'], $_POST['nama_suppliyer'], $_POST['harga'])) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama_bahan']);
        $stok = (int)$_POST['stok_bahan'];
        $suppliyer = mysqli_real_escape_string($conn, $_POST['nama_suppliyer']);
        $harga = (int)$_POST['harga'];
        
        // Generate ID bahan manual karena tidak ada AUTO_INCREMENT
        $max_id_query = "SELECT COALESCE(MAX(id_bahan), 0) + 1 as next_id FROM bahan";
        $max_id_result = mysqli_query($conn, $max_id_query);
        $max_id_row = mysqli_fetch_assoc($max_id_result);
        $next_id = $max_id_row['next_id'];

        // Insert ke tabel bahan
        $sql = "INSERT INTO bahan (id_bahan, nama_bahan, nama_suppliyer, stok_bahan, harga)
                VALUES ('$next_id', '$nama', '$suppliyer', '$stok', '$harga')";

        if (mysqli_query($conn, $sql)) {
            $pesan = "Bahan baru berhasil ditambahkan!";
            $tipe_pesan = "success";
            $tampilkan_form = true;
            $result = mysqli_query($conn, $query); // refresh data
        } else {
            $pesan = "Gagal menambahkan bahan baru: " . mysqli_error($conn);
            $tipe_pesan = "error";
            $tampilkan_form = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Bahan | RIDJIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="style.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 h-screen">
<div class="flex h-full overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 min-h-screen bg-gray-800 text-white p-6">
        <button id="toggleSidebar" class="toggle-button">☰</button>
        <h1 class="text-2xl font-bold mb-10">RIDJIK</h1>
        <nav class="flex flex-col space-y-5">
            <a href="dashboard.php" class="nav-link">
                <span>🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="produk.php" class="nav-link">
                <span>📦</span>
                <span>Produk</span>
            </a>
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

    <main class="flex-1 p-10 space-y-6 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-bold">Daftar stok bahan</h2>
        </div>
        
        <!-- tambah bahan -->
        <form method="post" id="formTambah" class="max-w-xl mt-6 bg-white p-6 rounded shadow <?= $tampilkan_form ? '' : 'hidden' ?>">
            <input type="hidden" name="aksi" value="tambah_bahan">
            <h2 class="text-2xl font-bold mb-4">Tambah Bahan Baru</h2>

            <label class="block mb-2">Nama Bahan</label>
            <input name="nama_bahan" class="border w-full mb-4 p-2 rounded" required>

            <label class="block mb-2">Stok</label>
            <input name="stok_bahan" type="number" class="border w-full mb-4 p-2 rounded" required>

            <label class="block mb-2">Nama Suppliyer</label>
            <input name="nama_suppliyer" class="border w-full mb-4 p-2 rounded" required>

            <label class="block mb-2">Harga</label>
            <input name="harga" type="number" class="border w-full mb-4 p-2 rounded" required>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow">
                Simpan Bahan
            </button>
        </form>
        
        <button onclick="toggleForm()" id="btnToggle" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
                + Tambah Bahan Baru
        </button>
        
        <form method="post">
            <input type="hidden" name="aksi" value="laporan">
            <table class="w-full bg-white rounded-lg shadow">
                <thead class="bg-gray-300">
                    <tr>
                        <th class="p-3 text-left">Nama Bahan</th>
                        <th class="p-3 text-left">Stok</th>
                        <th class="p-3 text-left">Nama Suppliyer</th>
                        <th class="p-3 text-left">Harga</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset pointer untuk membaca ulang hasil query
                    mysqli_data_seek($result, 0);
                    while ($item = mysqli_fetch_assoc($result)) : ?>
                    <tr class="border-t" data-id="<?= (int)$item['id_bahan'] ?>">
                        <td class="p-3"><?= htmlspecialchars($item['nama_bahan']) ?></td>
                        <td class="p-3">
                            <?= (int)$item['stok_bahan'] ?>
                            <input type="hidden" name="stok_laporan[<?= (int)$item['id_bahan'] ?>]" value="<?= (int)$item['stok_bahan'] ?>">
                        </td>
                        <td class="p-3"><?= htmlspecialchars($item['nama_suppliyer']) ?></td>
                        <td class="p-3">Rp <?= number_format((int)$item['harga'], 0, ',', '.') ?></td>
                        <td class="p-3">
                            <a href="edit_bahan.php?id=<?= (int)$item['id_bahan'] ?>" class="edit bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm mr-2">Edit</a>
                            <button type="button" class="hapus bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm" data-id="<?= (int)$item['id_bahan'] ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="mt-6 flex items-center gap-4">
                <label for="tanggal" class="font-medium">Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" class="border px-4 py-2 rounded" required>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Simpan ke Laporan</button>
            </div>
        </form>
    </main>
</div>

<script>
    const form = document.getElementById('formTambah');
    const button = document.getElementById('btnToggle');

    function toggleForm() {
        form.classList.toggle('hidden');
        if (form.classList.contains('hidden')) {
            button.textContent = "+ Tambah Bahan Baru";
        } else {
            button.textContent = "Tutup Form Tambah";
        }
    }

    // Otomatis buka form jika baru saja menambahkan bahan
    <?php if ($tampilkan_form): ?>
        document.addEventListener('DOMContentLoaded', () => {
            form.classList.remove('hidden');
            button.textContent = "Tutup Form Tambah";
        });
    <?php endif; ?>

    // Show SweetAlert for messages
    <?php if (isset($pesan)): ?>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: '<?= $tipe_pesan === "success" ? "Berhasil!" : "Error!" ?>',
                text: '<?= htmlspecialchars($pesan) ?>',
                icon: '<?= $tipe_pesan ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            });
        });
    <?php endif; ?>

    // Hapus bahan dengan SweetAlert
    document.querySelectorAll('.hapus').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const tr = this.closest('tr');
            const namaBahan = tr.querySelector('td:first-child').textContent;

            // Debug log
            console.log('Button clicked, ID:', id);
            console.log('Row data-id:', tr.getAttribute('data-id'));

            if (!id) {
                Swal.fire({
                    title: 'Error!',
                    text: 'ID bahan tidak ditemukan',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Bahan?',
                text: `Yakin ingin menghapus bahan "${namaBahan}" (ID: ${id})? Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang memproses permintaan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    console.log('Sending delete request for ID:', id);

                    fetch('hapus_bahan.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + encodeURIComponent(id)
                    })
                    .then(response => response.text())
                    .then(result => {
                        console.log('Server response:', result);
                        
                        if (result.trim() === 'ok') {
                            tr.remove();
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Bahan berhasil dihapus',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Gagal menghapus bahan: ' + result,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus bahan',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    });

    // SweetAlert for form submission
    document.querySelector('form[method="post"]').addEventListener('submit', function(e) {
        if (this.querySelector('input[name="aksi"][value="laporan"]')) {
            e.preventDefault();
            
            const tanggal = document.getElementById('tanggal').value;
            if (!tanggal) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Tanggal harus diisi!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Simpan Laporan?',
                text: `Yakin ingin menyimpan laporan stok untuk tanggal ${tanggal}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        }
    });
</script>
</body>
</html>