<?php
include 'koneksi.php';

$pesan = '';
$tipe_pesan = '';
$bahan = null;

// Ambil ID dari URL
$id_bahan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_bahan <= 0) {
    header('Location: bahan.php');
    exit();
}

// Ambil data bahan berdasarkan ID
$query = "SELECT * FROM bahan WHERE id_bahan = '$id_bahan'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    header('Location: bahan.php');
    exit();
}

$bahan = mysqli_fetch_assoc($result);

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nama_bahan'], $_POST['stok_bahan'], $_POST['nama_suppliyer'], $_POST['harga'])) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama_bahan']);
        $stok = (int)$_POST['stok_bahan'];
        $suppliyer = mysqli_real_escape_string($conn, $_POST['nama_suppliyer']);
        $harga = (int)$_POST['harga'];

        $sql = "UPDATE bahan SET 
                nama_bahan = '$nama', 
                stok_bahan = '$stok', 
                nama_suppliyer = '$suppliyer', 
                harga = '$harga' 
                WHERE id_bahan = '$id_bahan'";

        if (mysqli_query($conn, $sql)) {
            $pesan = "Data bahan berhasil diperbarui!";
            $tipe_pesan = "success";
            
            // Refresh data bahan setelah update
            $result = mysqli_query($conn, $query);
            $bahan = mysqli_fetch_assoc($result);
        } else {
            $pesan = "Gagal memperbarui data bahan: " . mysqli_error($conn);
            $tipe_pesan = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Bahan | RIDJIK</title>
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
            <h2 class="text-3xl font-bold">Edit Bahan</h2>
            <a href="bahan.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow">
                ← Kembali ke Daftar Bahan
            </a>
        </div>
        
        <!-- Form Edit Bahan -->
        <div class="max-w-2xl bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">Edit Data Bahan</h3>
            
            <form method="post" id="formEdit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ID Bahan
                        </label>
                        <input type="text" value="<?= (int)$bahan['id_bahan'] ?>" 
                               class="border border-gray-300 w-full p-3 rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Bahan <span class="text-red-500">*</span>
                        </label>
                        <input name="nama_bahan" type="text" 
                               value="<?= htmlspecialchars($bahan['nama_bahan']) ?>"
                               class="border border-gray-300 w-full p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input name="stok_bahan" type="number" min="0"
                               value="<?= (int)$bahan['stok_bahan'] ?>"
                               class="border border-gray-300 w-full p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Supplier <span class="text-red-500">*</span>
                        </label>
                        <input name="nama_suppliyer" type="text" 
                               value="<?= htmlspecialchars($bahan['nama_suppliyer']) ?>"
                               class="border border-gray-300 w-full p-3 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                            <input name="harga" type="number" min="0"
                                   value="<?= (int)$bahan['harga'] ?>"
                                   class="border border-gray-300 w-full p-3 pl-12 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   required>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="bahan.php" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg shadow transition duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow transition duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Info Card -->
        <div class="max-w-2xl bg-blue-50 border border-blue-200 p-4 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Pastikan semua data yang dimasukkan sudah benar sebelum menyimpan perubahan. 
                        Data yang telah disimpan akan langsung mempengaruhi stok dan laporan.
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Show SweetAlert for messages
    <?php if (!empty($pesan)): ?>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: '<?= $tipe_pesan === "success" ? "Berhasil!" : "Error!" ?>',
                text: '<?= htmlspecialchars($pesan) ?>',
                icon: '<?= $tipe_pesan ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                <?php if ($tipe_pesan === "success"): ?>
                if (result.isConfirmed) {
                    // Redirect ke halaman bahan setelah sukses
                    window.location.href = 'bahan.php';
                }
                <?php endif; ?>
            });
        });
    <?php endif; ?>

    // Form validation and submission
    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const nama = formData.get('nama_bahan').trim();
        const stok = parseInt(formData.get('stok_bahan'));
        const supplier = formData.get('nama_suppliyer').trim();
        const harga = parseInt(formData.get('harga'));
        
        // Validation
        if (!nama || !supplier) {
            Swal.fire({
                title: 'Error!',
                text: 'Nama bahan dan supplier tidak boleh kosong!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        if (stok < 0 || harga < 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Stok dan harga tidak boleh bernilai negatif!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Confirmation dialog
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: `Yakin ingin menyimpan perubahan untuk bahan "${nama}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang memproses perubahan data',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                
                // Submit form
                this.submit();
            }
        });
    });

    // Format currency input
    const hargaInput = document.querySelector('input[name="harga"]');
    hargaInput.addEventListener('input', function() {
        // Remove non-numeric characters except for decimal point
        let value = this.value.replace(/[^\d]/g, '');
        this.value = value;
    });

    // Auto-focus on first input
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('input[name="nama_bahan"]').focus();
    });
</script>
</body>
</html>