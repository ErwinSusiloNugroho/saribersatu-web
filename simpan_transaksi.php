<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpanTransaksi'])) {
    $jenis = $_POST['jenis'];
    $tanggal = $_POST['tanggal'];
    $produk = $_POST['produk'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);

    // Simpan transaksi utama
    $stmt = $conn->prepare("INSERT INTO transaksi (jenis, tanggal) VALUES (?, ?)");
    $stmt->bind_param("ss", $jenis, $tanggal);
    $stmt->execute();
    $transaksi_id = $stmt->insert_id;
    $stmt->close();

    //simpan nota
    $gambarName = '';
    if (isset($_FILES['nota_umum']) && $_FILES['nota_umum']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['nota_umum']['name']);
        $targetFile = $uploadDir . time() . '_' . $filename;
        move_uploaded_file($_FILES['nota_umum']['tmp_name'], $targetFile);
        $gambarName = $targetFile;
    }

    // Simpan detail transaksi
    foreach ($produk as $i => $p) {
        $jml = (int)$jumlah[$i];
        $hrg = (int)$harga[$i];
        $notaPath = '';

        if (isset($_FILES['nota']['name'][$i]) && $_FILES['nota']['error'][$i] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['nota']['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('nota_') . '.' . $ext;
            $targetFile = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['nota']['tmp_name'][$i], $targetFile)) {
                $notaPath = $targetFile;
            }
        }

        $stmt = $conn->prepare("INSERT INTO detail_transaksi (transaksi_id, produk, jumlah, harga, nota) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiis", $transaksi_id, $p, $jml, $hrg, $notaPath);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    header("Location: transaksi.php?status=success");
    exit;
}
?>
