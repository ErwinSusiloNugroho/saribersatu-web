<?php
include 'koneksi.php';

$tampilkan_form = false;
$pesan = "";
$status = ""; // untuk menentukan jenis alert

// Debug: Cek apakah POST data diterima
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug untuk melihat data yang diterima
    error_log("POST Data: " . print_r($_POST, true));
}

// Ambil data produk dari database
$query = "SELECT * FROM produk";
$result = mysqli_query($conn, $query);

// Proses form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // TAMBAH PRODUK BARU - cek dengan cara yang lebih sederhana
    if (isset($_POST['nama_produk']) && isset($_POST['kode_produk']) && isset($_POST['stok_produk']) && isset($_POST['harga_produk'])) {
        
        $nama = mysqli_real_escape_string($conn, $_POST['nama_produk']);
        $kode = mysqli_real_escape_string($conn, $_POST['kode_produk']);
        $stok = (int)$_POST['stok_produk'];
        $harga = (float)$_POST['harga_produk'];

        // Cek apakah kode produk sudah ada
        $check_query = "SELECT id_produk FROM produk WHERE kode_produk = '$kode'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Produk sudah ada
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=exists&kode=" . urlencode($kode));
            exit();
        }

        // Debug: Cek nilai yang akan diinsert
        error_log("Akan insert: nama=$nama, kode=$kode, stok=$stok, harga=$harga");

        $sql = "INSERT INTO produk (nama_produk, kode_produk, stok_produk, harga)
                VALUES ('$nama', '$kode', $stok, $harga)";
        
        if (mysqli_query($conn, $sql)) {
            // Redirect untuk mencegah resubmit
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=success&nama=" . urlencode($nama));
            exit();
        } else {
            $error_msg = mysqli_error($conn);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=error&msg=" . urlencode($error_msg));
            exit();
        }
    }
    
    // EDIT PRODUK - cek field edit
    elseif (isset($_POST['edit_id']) && isset($_POST['edit_nama']) && isset($_POST['edit_kode']) && isset($_POST['edit_stok']) && isset($_POST['edit_harga'])) {
        $id = (int)$_POST['edit_id'];
        $nama = mysqli_real_escape_string($conn, $_POST['edit_nama']);
        $kode = mysqli_real_escape_string($conn, $_POST['edit_kode']);
        $stok = (int)$_POST['edit_stok'];
        $harga = (float)$_POST['edit_harga'];

        // Cek apakah kode produk sudah ada (kecuali untuk produk yang sedang diedit)
        $check_query = "SELECT id_produk FROM produk WHERE kode_produk = '$kode' AND id_produk != $id";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Kode produk sudah digunakan produk lain
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=exists_edit&kode=" . urlencode($kode));
            exit();
        }

        $sql = "UPDATE produk SET nama_produk = '$nama', kode_produk = '$kode', stok_produk = $stok, harga = $harga WHERE id_produk = $id";
        
        if (mysqli_query($conn, $sql)) {
            // Redirect untuk mencegah resubmit
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=updated&nama=" . urlencode($nama));
            exit();
        } else {
            $error_msg = mysqli_error($conn);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=error_edit&msg=" . urlencode($error_msg));
            exit();
        }
    }
    
    // HAPUS PRODUK - fitur baru
    elseif (isset($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        
        // Ambil nama produk untuk pesan konfirmasi
        $get_name_query = "SELECT nama_produk FROM produk WHERE id_produk = $id";
        $name_result = mysqli_query($conn, $get_name_query);
        $product_data = mysqli_fetch_assoc($name_result);
        $nama_produk = $product_data['nama_produk'] ?? 'Unknown';

        $sql = "DELETE FROM produk WHERE id_produk = $id";
        
        if (mysqli_query($conn, $sql)) {
            // Redirect dengan pesan sukses
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=deleted&nama=" . urlencode($nama_produk));
            exit();
        } else {
            $error_msg = mysqli_error($conn);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=error_delete&msg=" . urlencode($error_msg));
            exit();
        }
    }
}

// Handle status messages dari redirect
$alert_data = [];
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'success':
            $alert_data = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Produk "' . htmlspecialchars($_GET['nama']) . '" berhasil ditambahkan!'
            ];
            break;
        case 'updated':
            $alert_data = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Produk "' . htmlspecialchars($_GET['nama']) . '" berhasil diperbarui!'
            ];
            break;
        case 'deleted':
            $alert_data = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Produk "' . htmlspecialchars($_GET['nama']) . '" berhasil dihapus!'
            ];
            break;
        case 'exists':
            $alert_data = [
                'type' => 'warning',
                'title' => 'Produk Sudah Ada!',
                'text' => 'Kode produk "' . htmlspecialchars($_GET['kode']) . '" sudah digunakan. Silakan gunakan kode yang berbeda.'
            ];
            break;
        case 'exists_edit':
            $alert_data = [
                'type' => 'warning',
                'title' => 'Kode Sudah Digunakan!',
                'text' => 'Kode produk "' . htmlspecialchars($_GET['kode']) . '" sudah digunakan produk lain. Silakan gunakan kode yang berbeda.'
            ];
            break;
        case 'error':
            $alert_data = [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Gagal menambahkan produk: ' . htmlspecialchars($_GET['msg'])
            ];
            break;
        case 'error_edit':
            $alert_data = [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Gagal mengedit produk: ' . htmlspecialchars($_GET['msg'])
            ];
            break;
        case 'error_delete':
            $alert_data = [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Gagal menghapus produk: ' . htmlspecialchars($_GET['msg'])
            ];
            break;
    }
}

// Refresh data setelah semua operasi
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Produk | RIDJIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="style.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 h-screen">
<div class="flex h-full overflow-auto">
    <!-- Sidebar -->
    <aside class="w-64 min-h-screen bg-gray-800 text-white p-6">
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
<main class="flex-1 p-10 space-y-6 overflow-y-auto">
    <h2 class="text-3xl font-bold mb-4">Daftar Produk</h2>
    
    <!-- Tombol toggle untuk form tambah -->
    <button onclick="toggleForm()" id="btnToggle" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
        + Tambah Produk Baru
    </button>
    
    <!-- Form tambah produk -->
    <form method="post" id="formTambah" class="max-w-xl mt-6 bg-white p-6 rounded shadow <?= $tampilkan_form ? '' : 'hidden' ?>">
        <h2 class="text-2xl font-bold mb-4">Tambah Produk Baru</h2>
        
        <label class="block mb-2">Kode Produk</label>
        <input name="kode_produk" type="text" class="border w-full mb-4 p-2 rounded" required />

        <label class="block mb-2">Nama Produk</label>
        <input name="nama_produk" type="text" class="border w-full mb-4 p-2 rounded" required />

        <label class="block mb-2">Stok</label>
        <input name="stok_produk" type="number" class="border w-full mb-4 p-2 rounded" required />

        <label class="block mb-2">Harga Produk</label>
        <input name="harga_produk" type="number" step="0.01" min="0" class="border w-full mb-4 p-2 rounded" required />

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow">
            Simpan Produk
        </button>
    </form>

    <!-- Tabel list produk -->
    <div class="mt-6">
        <table class="w-full bg-white rounded-lg shadow">
            <thead class="bg-gray-300">
                <tr>
                    <th class="p-3 text-left">Nama Produk</th>
                    <th class="p-3 text-left">Kode Produk</th>
                    <th class="p-3 text-left">Stok</th>
                    <th class="p-3 text-left">Harga/pcs</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($item = mysqli_fetch_assoc($result)) : ?>
                <tr class="border-t" data-id="<?= $item['id_produk'] ?>">
                    <td class="p-3 nama"><?= htmlspecialchars($item['nama_produk']) ?></td>
                    <td class="p-3 kode"><?= htmlspecialchars($item['kode_produk']) ?></td>
                    <td class="p-3 stok"><?= (int)$item['stok_produk'] ?></td>
                    <td class="p-3 harga" data-harga="<?= $item['harga'] ?>">
                        <?= number_format($item['harga'], 2, ',', '.') ?>
                    </td>
                    <td class="p-3">
                        <button type="button" class="edit bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">
                            Edit
                        </button>
                        <button type="button" class="delete bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm" 
                                data-id="<?= $item['id_produk'] ?>" 
                                data-nama="<?= htmlspecialchars($item['nama_produk']) ?>">
                            Hapus
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</div>

<!-- Form tersembunyi untuk hapus produk -->
<form id="deleteForm" method="post" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteId" />
</form>

<script>
    // Tampilkan alert jika ada
    <?php if (!empty($alert_data)) : ?>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?= $alert_data['type'] ?>',
            title: '<?= $alert_data['title'] ?>',
            text: '<?= $alert_data['text'] ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    });
    <?php endif; ?>

    const formTambah = document.getElementById('formTambah');
    const btnToggle = document.getElementById('btnToggle');

    function toggleForm() {
        formTambah.classList.toggle('hidden');
        if (formTambah.classList.contains('hidden')) {
            btnToggle.textContent = "+ Tambah Produk Baru";
        } else {
            btnToggle.textContent = "Tutup Form";
        }
    }

    // Prevent double submit pada form tambah dengan loading state
    formTambah.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="animate-spin">⏳</span> Menyimpan...';
        
        // Re-enable setelah 5 detik jika ada error
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Produk';
        }, 5000);
    });

    // Handle edit button
    document.querySelectorAll('.edit').forEach(button => {
        button.addEventListener('click', function() {
            const tr = this.closest('tr');
            const id = tr.getAttribute('data-id');
            const nama = tr.querySelector('.nama').textContent.trim();
            const kode = tr.querySelector('.kode').textContent.trim();
            const stok = tr.querySelector('.stok').textContent.trim();
            const harga = tr.querySelector('.harga').dataset.harga;

            // Hapus form edit yang sudah ada
            const existingForm = document.querySelector('.edit-form-row');
            if (existingForm) existingForm.remove();

            const formRow = document.createElement('tr');
            formRow.classList.add('edit-form-row', 'bg-gray-100');

            formRow.innerHTML = `
                <td colspan="5" class="p-3">
                    <form method="post" class="grid grid-cols-5 gap-2 items-center">
                        <input type="text" name="edit_nama" value="${nama}" class="border p-2 rounded w-full" required />
                        <input type="text" name="edit_kode" value="${kode}" class="border p-2 rounded w-full" required />
                        <input type="number" name="edit_stok" value="${stok}" class="border p-2 rounded w-full" required />
                        <input type="number" name="edit_harga" value="${harga}" step="0.01" class="border p-2 rounded w-full" required />
                        <div class="flex gap-2">
                            <input type="hidden" name="edit_id" value="${id}" />
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">Simpan</button>
                            <button type="button" class="cancel-btn bg-gray-400 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm">Batal</button>
                        </div>
                    </form>
                </td>
            `;

            tr.after(formRow);

            // Prevent double submit pada form edit
            const editForm = formRow.querySelector('form');
            editForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin">⏳</span> Menyimpan...';
            });

            // Tombol batal
            formRow.querySelector('.cancel-btn').addEventListener('click', () => {
                formRow.remove();
            });
        });
    });

    // Handle delete button - fitur baru
    document.querySelectorAll('.delete').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Produk "${nama}" akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set ID ke form tersembunyi dan submit
                    document.getElementById('deleteId').value = id;
                    document.getElementById('deleteForm').submit();
                }
            });
        });
    });

    // Fungsi untuk menampilkan konfirmasi sebelum submit (opsional)
    function confirmSubmit(formType, itemName) {
        return Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${formType} produk "${itemName}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal'
        });
    }
</script>

</body>
</html>