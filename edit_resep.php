<?php
include 'koneksi.php';

// Sanitasi id (pastikan integer)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data resep sekaligus produk dengan JOIN
$res = mysqli_query($conn, "
    SELECT resep.*, produk.nama_produk, produk.kode_produk
    FROM resep 
    JOIN produk ON resep.id_produk = produk.id_produk
    WHERE resep.id_resep = $id
");
$data = mysqli_fetch_assoc($res);

if (!$data) {
    // Jika data tidak ditemukan, redirect atau tampilkan error
    header("Location: resep.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bahan = mysqli_real_escape_string($conn, trim($_POST['bahan']));
    $cara = mysqli_real_escape_string($conn, trim($_POST['cara']));

    // Update hanya bahan dan cara, karena nama/kode produk ada di tabel produk
    mysqli_query($conn, "
        UPDATE resep SET 
            bahan = '$bahan',
            cara = '$cara'
        WHERE id_resep = $id
    ");

    header("Location: resep.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Resep</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f9ff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2b2b2b;
            margin-bottom: 30px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 22px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
            background-color: #fdfdfd;
            font-family: 'poppins'
        }

        textarea#bahan, textarea#cara {
            font-family: 'poppins';
            font-size: 16px;
            line-height: 1.8;
        }

        button {
            background: #00b894;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }

        button:hover {
            background: #019875;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #555;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Edit Resep Produk</h2>
        <form method="POST">
            <!-- Tampilkan nama dan kode produk dari tabel produk, read-only -->
            <label for="nama">Nama Produk</label>
            <input type="text" id="nama" value="<?= htmlspecialchars($data['nama_produk']) ?>" readonly>

            <label for="kode">Kode Produk</label>
            <input type="text" id="kode" value="<?= htmlspecialchars($data['kode_produk']) ?>" readonly>

            <label for="bahan">Bahan (auto penomoran)</label>
            <textarea name="bahan" id="bahan" required><?= htmlspecialchars($data['bahan']) ?></textarea>

            <label for="cara">Cara Pembuatan (auto penomoran)</label>
            <textarea name="cara" id="cara" required><?= htmlspecialchars($data['cara']) ?></textarea>

            <button type="submit">💾 Simpan Perubahan</button>
        </form>
        <a href="resep.php" class="back-link">← Kembali ke Daftar Resep</a>
    </div>

    <script>
    // Script auto numbering tetap sama...
    function enableAutoNumbering(textareaId) {
        const textarea = document.getElementById(textareaId);
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const lines = textarea.value.substring(0, textarea.selectionStart).split('\n');
                const currentLineIndex = lines.length;
                const nextNumber = currentLineIndex + 1;
                const beforeCursor = textarea.value.substring(0, textarea.selectionStart);
                const afterCursor = textarea.value.substring(textarea.selectionStart);
                const newText = beforeCursor + '\n' + nextNumber + '. ' + afterCursor;
                textarea.value = newText;
                const cursorPosition = beforeCursor.length + (`\n${nextNumber}. `).length;
                textarea.setSelectionRange(cursorPosition, cursorPosition);
            }
        });

        if (textarea.value.trim() === '') {
            textarea.value = '1. ';
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }
    }

    enableAutoNumbering('bahan');
    enableAutoNumbering('cara');
    </script>
</body>
</html>
