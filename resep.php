<?php
include 'koneksi.php';
// Optional: log errors to file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
// Ambil data resep beserta id resep, nama produk, kode produk, bahan, cara
$produk = [];
$query = mysqli_query($conn, "
    SELECT 
        resep.id_resep, 
        resep.id_produk, 
        produk.nama_produk, 
        produk.kode_produk, 
        resep.bahan, 
        resep.cara 
    FROM resep 
    JOIN produk ON resep.id_produk = produk.id_produk
");

while ($row = mysqli_fetch_assoc($query)) {
    $produk[] = [
        'id_resep' => $row['id_resep'],
        'id_produk' => $row['id_produk'],
        'nama_produk' => $row['nama_produk'],
        'kode_produk' => $row['kode_produk'],
        'resep' => [
            'bahan' => explode("\n", $row['bahan']),
            'cara' => $row['cara']
        ]
    ];
}


// Ambil data produk untuk autocomplete dan input (beserta id_produk)
$produkOptions = [];
$qProduk = mysqli_query($conn, "SELECT id_produk, nama_produk, kode_produk FROM produk");
while ($r = mysqli_fetch_assoc($qProduk)) {
    $produkOptions[] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Resep | RIDJIK</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Modal styling */
        .modal-bg {
            position: fixed;
            top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-height: 90vh;
            overflow-y: auto;
            width: 90%;
            max-width: 600px;
            position: relative;
        }
        .btn-close {
            position: absolute;
            top: 8px; right: 12px;
            background: none;
            border: none;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #333;
        }
    </style>
</head>
<body class="bg-gray-100 flex">

     <!-- Sidebar -->
      <button id="toggleSidebar" class="toggle-button">☰</button>
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
        <h2 class="text-3xl font-bold mb-8">Resep</h2>
        <button id="btnTambah" class="mb-6 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">
            + Tambah Resep
        </button>

        <!-- Tabel Resep & Produk -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-5 text-left">Nama Produk</th>
                        <th class="py-3 px-5 text-left">Kode Produk</th>
                        <th class="py-3 px-5 text-left">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($produk as $idx => $p): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-5"><?= htmlspecialchars($p['nama_produk']) ?></td>
                            <td class="py-3 px-5"><?= htmlspecialchars($p['kode_produk']) ?></td>
                            <td class="py-3 px-5">
                                <button 
                                    class="btn-detail bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded"
                                    data-index="<?= $idx ?>"
                                >
                                    Lihat Resep
                                </button>
                                <a 
                                    href="edit_resep.php?id=<?= $p['id_resep'] ?>" 
                                    class="ml-2 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
                                >
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal resep detail -->
        <div id="modalResep" class="modal-bg hidden">
            <div class="modal-content">
                <button id="closeModal" class="btn-close" aria-label="Tutup">&times;</button>
                <h3 id="modalJudul" class="text-2xl font-bold mb-4"></h3>
                <h4 class="font-semibold mb-2">Bahan-bahan:</h4>
                <ul id="modalBahan" class="list-disc list-inside mb-4 text-gray-800"></ul>
                <h4 class="font-semibold mb-2">Cara Pembuatan:</h4>
                <div id="modalCara" class="text-gray-700"></div>
                <button id="btnCloseModal" class="mt-6 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                    Tutup
                </button>
            </div>
        </div>

         <!-- Modal Tambah Resep -->
        <div id="modalTambah" class="modal-bg hidden">
            <div class="modal-content max-w-lg">
                <button id="closeTambah" class="btn-close" aria-label="Tutup">&times;</button>
                <h3 class="text-2xl font-bold mb-4">Tambah Resep Baru</h3>
                <form method="POST" action="simpan_resep.php" id="formTambahResep">
                    <!-- Hidden id_resep utk edit (kosong saat tambah) -->
                    <input type="hidden" name="id_resep" id="id_resep" value="">

                    <label class="block mb-2 font-semibold" for="namaProduk">Nama Produk</label>
                    <select
                        id="namaProduk"
                        name="namaProduk"
                        required
                        class="w-full p-2 border rounded mb-4"
                    >
                        <option value="" disabled selected>-- Pilih produk --</option>
                        <?php foreach ($produkOptions as $p): ?>
                            <option 
                                value="<?= $p['id_produk'] ?>" 
                                data-kode="<?= htmlspecialchars($p['kode_produk']) ?>"
                            >
                                <?= htmlspecialchars($p['nama_produk']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Hidden input id_produk yang otomatis di-set -->
                    <input type="hidden" name="id_produk" id="id_produk" value="">

                    <label class="block mb-2 font-semibold" for="kodeProduk">Kode Produk</label>
                    <input 
                        id="kodeProduk" 
                        name="kodeProduk" 
                        type="text" 
                        required 
                        readonly
                        class="w-full p-2 border rounded mb-4 bg-gray-100"
                    >

                    <label class="block mb-2 font-semibold" for="bahanResep">Bahan-bahan (Nomor otomatis jika enter)</label>
                    <textarea id="bahanResep" name="bahanResep" rows="5" required class="w-full p-2 border rounded mb-4"></textarea>

                    <label class="block mb-2 font-semibold" for="caraPembuatan">Cara Pembuatan</label>
                    <textarea id="caraPembuatan" name="caraPembuatan" rows="3" required class="w-full p-2 border rounded mb-6"></textarea>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">
                        Simpan Resep
                    </button>
                </form>
            </div>
        </div>
    </main>

<script>
    // Data produk untuk mencari id_produk dan kode_produk dari namaProduk input
    const produkOptions = <?= json_encode($produkOptions) ?>;
    const produk = <?= json_encode($produk) ?>;

    // Elemen modal tambah resep dan form
    const modalTambah = document.getElementById('modalTambah');
    const btnTambah = document.getElementById('btnTambah');
    const closeTambah = document.getElementById('closeTambah');
    const formTambah = document.getElementById('formTambahResep');

    // Elemen input form tambah
    const namaProdukInput = document.getElementById('namaProduk');
    const kodeProdukInput = document.getElementById('kodeProduk');
    const idProdukInput = document.getElementById('id_produk');
    const idResepInput = document.getElementById('id_resep');
    const bahanResepInput = document.getElementById('bahanResep');
    const caraPembuatanInput = document.getElementById('caraPembuatan');

    // Modal resep detail
    const modalResep = document.getElementById('modalResep');
    const modalJudul = document.getElementById('modalJudul');
    const modalBahan = document.getElementById('modalBahan');
    const modalCara = document.getElementById('modalCara');
    const closeModal = document.getElementById('closeModal');
    const btnCloseModal = document.getElementById('btnCloseModal');

    // Escape html utility
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Tampilkan modal tambah resep
    btnTambah.addEventListener('click', () => {
        resetFormTambah();
        modalTambah.classList.remove('hidden');
        namaProdukInput.focus();
    });

    // Tutup modal tambah resep
    closeTambah.addEventListener('click', () => {
        modalTambah.classList.add('hidden');
    });

    // Reset form tambah resep
    function resetFormTambah() {
        idResepInput.value = '';
        namaProdukInput.value = '';
        kodeProdukInput.value = '';
        idProdukInput.value = '';
        bahanResepInput.value = '';
        caraPembuatanInput.value = '';
    }

    // Set id_produk dan kode_produk otomatis saat memilih produk dari datalist
        // Ketika produk dipilih dari dropdown select
        namaProdukInput.addEventListener('change', () => {
            const selectedOption = namaProdukInput.options[namaProdukInput.selectedIndex];
            if (selectedOption && selectedOption.value !== "") {
                idProdukInput.value = selectedOption.value;
                kodeProdukInput.value = selectedOption.getAttribute('data-kode') || '';
                kodeProdukInput.classList.remove('bg-gray-100');
                kodeProdukInput.classList.add('bg-green-50', 'font-semibold');
            } else {
                idProdukInput.value = '';
                kodeProdukInput.value = '';
            }
        });

    
    // KODE BARU INI
    let isSubmitting = false;

formTambah.addEventListener('submit', e => {
    e.preventDefault();

    // Cek apakah sedang dalam proses submit
    const submitBtn = formTambah.querySelector('button[type="submit"]');
    if (submitBtn.disabled) {
        return; // Jangan proses jika sedang loading
    }

    // Validasi sederhana
    if (!idProdukInput.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Nama produk tidak valid atau tidak dipilih dari daftar.'
        });
        namaProdukInput.focus();
        return;
    }
    if (!bahanResepInput.value.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Bahan resep tidak boleh kosong.'
        });
        bahanResepInput.focus();
        return;
    }
    if (!caraPembuatanInput.value.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Cara pembuatan tidak boleh kosong.'
        });
        caraPembuatanInput.focus();
        return;
    }

    // TUTUP MODAL SEGERA SETELAH VALIDASI BERHASIL DAN TOMBOL DIKLIK
    modalTambah.classList.add('hidden');

    // Disable tombol submit dan ubah text
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Menyimpan...';
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

    // Tampilkan loading SweetAlert
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Sedang menyimpan resep, mohon tunggu.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData(formTambah);

    fetch('simpan_resep.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        console.log('Response status:', res.status);
        return res.text();
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const res = JSON.parse(text);
            console.log('Parsed JSON:', res);
            
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Resep berhasil disimpan!',
                    showConfirmButton: true
                }).then(() => {
                    // Reset form dan reload halaman
                    formTambah.reset();
                    window.location.reload();
                });
            } else {
                // MODAL TETAP TERTUTUP, HANYA TAMPILKAN ERROR
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menyimpan resep: ' + res.message,
                    showConfirmButton: true
                }).then(() => {
                    // Reset form setelah error
                    formTambah.reset();
                });
            }
        } catch (e) {
            console.error('JSON Parse Error:', e);
            console.error('Response text:', text);
            // MODAL TETAP TERTUTUP, HANYA TAMPILKAN ERROR
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan: Response tidak valid dari server',
                showConfirmButton: true
            }).then(() => {
                // Reset form setelah error
                formTambah.reset();
            });
        }
    })
    .catch(err => {
        console.error('Fetch Error:', err);
        // MODAL TETAP TERTUTUP, HANYA TAMPILKAN ERROR
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan resep: ' + err.message,
            showConfirmButton: true
        }).then(() => {
            // Reset form setelah error
            formTambah.reset();
        });
    })
    .finally(() => {
        // Re-enable tombol submit
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    });
});

// Handle tombol close modal
document.getElementById('closeTambah').addEventListener('click', () => {
    modalTambah.classList.add('hidden');
    formTambah.reset();
});

// Handle klik di luar modal untuk menutup
modalTambah.addEventListener('click', (e) => {
    if (e.target === modalTambah) {
        modalTambah.classList.add('hidden');
        formTambah.reset();
    }
});
    // Tampilkan detail resep modal
    function tampilkanResepDetail(data) {
        modalJudul.textContent = data.nama_produk + ' (' + data.kode_produk + ')';

        modalBahan.innerHTML = '';
        if (Array.isArray(data.resep.bahan)) {
            data.resep.bahan.forEach(b => {
                const li = document.createElement('ol');
                li.textContent = b;
                modalBahan.appendChild(li);
            });
        }

        modalCara.innerHTML = '';

        const langkah = data.resep.cara.split('\n');

        const ol = document.createElement('ol');
        langkah.forEach(l => {
            if(l.trim() !== '') {
                const li = document.createElement('li');
                li.textContent = l;
                ol.appendChild(li);
            }
        });
        modalCara.appendChild(ol);
        modalResep.classList.remove('hidden');
    }

    // Tutup modal resep detail
    function tutupModalResep() {
        modalResep.classList.add('hidden');
    }
    closeModal.addEventListener('click', tutupModalResep);
    btnCloseModal.addEventListener('click', tutupModalResep);

    // Pasang event listener pada semua tombol lihat resep yang ada di halaman
    document.querySelectorAll('.btn-detail').forEach((btn, idx) => {
        btn.addEventListener('click', () => {
            tampilkanResepDetail(produk[idx]);
        });
    });

    // Sidebar toggle (optional, jika ada toggle button)
    const sidebar = document.querySelector('aside.sidebar');
    const toggleSidebar = document.getElementById('toggleSidebar');
    if(toggleSidebar) {
        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    function setupNumberedTextarea(textarea) {
    textarea.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault(); // cegah enter default (newline biasa)

            const lines = textarea.value.split('\n');
            const lastLine = lines[lines.length - 1];
            const match = lastLine.match(/^(\d+)\.\s*/);
            let nextNumber = 1;
            if (match) {
                nextNumber = parseInt(match[1]) + 1;
            }

            textarea.value += '\n' + nextNumber + '. ';

            setTimeout(() => {
                textarea.selectionStart = textarea.selectionEnd = textarea.value.length;
            }, 0);
        }
    });

    // Saat fokus pertama kali dan kosong, langsung nomor 1.
    textarea.addEventListener('focus', () => {
        if (textarea.value.trim() === '') {
            textarea.value = '1. ';
            setTimeout(() => {
                textarea.selectionStart = textarea.selectionEnd = textarea.value.length;
            }, 0);
        }
    });
}

setupNumberedTextarea(document.getElementById('bahanResep'));
setupNumberedTextarea(document.getElementById('caraPembuatan'));

function tambahAtauUpdateTabelResep(data) {
    const tbody = document.querySelector('table tbody');

    // Cari row dengan data-id=id_resep
    let row = tbody.querySelector(`tr[data-id="${data.id_resep}"]`);
    if (row) {
        // Update isi baris
        row.querySelector('td:nth-child(1)').textContent = data.nama_produk;
        row.querySelector('td:nth-child(2)').textContent = data.kode_produk;

        // Update tombol lihat resep data-id
        const btnDetail = row.querySelector('.btn-detail');
        btnDetail.setAttribute('data-id', data.id_resep);
        btnDetail.onclick = () => tampilkanResepDetail(data);
    } else {
        // Tambah baris baru
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', data.id_resep);
        tr.className = 'border-b hover:bg-gray-50';

        tr.innerHTML = `
            <td class="py-3 px-5">${escapeHtml(data.nama_produk)}</td>
            <td class="py-3 px-5">${escapeHtml(data.kode_produk)}</td>
            <td class="py-3 px-5">
                <button 
                    class="btn-detail bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded"
                    data-id="${data.id_resep}"
                >
                    Lihat Resep
                </button>
                <a 
                    href="edit_resep.php?id=${data.id_resep}" 
                    class="ml-2 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
                >
                    Edit
                </a>
            </td>
        `;

        tbody.appendChild(tr);

        // Pasang event listener tombol detail resep
        tr.querySelector('.btn-detail').onclick = () => tampilkanResepDetail(data);
    }
}

</script>
</body>
</html>
