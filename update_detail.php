<?php
require 'koneksi.php'; 

$id_detail = $_POST['id_detail_transaksi'];
$produk = $_POST['produk'];
$jumlah = $_POST['jumlah'];
$harga = $_POST['harga'];
$nota_lama = $_POST['nota_lama'];

$nota_baru = $nota_lama;
$target_dir = "nota/";

// Ambil id_transaksi dari detail
$sql = "SELECT id_transaksi FROM detail_transaksi WHERE id_transaksi = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_detail);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$id_transaksi = $data['id_transaksi'] ?? null;

if (!$id_transaksi) {
    die("ID transaksi tidak ditemukan.");
}

// Cek apakah ada file nota baru
if (isset($_FILES['nota']) && $_FILES['nota']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['nota']['name'], PATHINFO_EXTENSION);
    $nama_baru = uniqid('nota_') . '.' . $ext;
    $target_file = $target_dir . $nama_baru;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['nota']['tmp_name'], $target_file)) {
        // Hapus file lama
        if (!empty($nota_lama) && file_exists($target_dir . $nota_lama)) {
            unlink($target_dir . $nota_lama);
        }
        $nota_baru = $nama_baru;

        // Update nota di tabel transaksi
        $updateNota = $conn->prepare("UPDATE transaksi SET nota = ? WHERE id_transaksi = ?");
        $updateNota->bind_param("si", $nota_baru, $id_transaksi);
        $updateNota->execute();
    }
}

// Update detail produk
$updateDetail = $conn->prepare("UPDATE detail_transaksi SET produk=?, jumlah=?, harga=? WHERE id_transaksi=?");
$updateDetail->bind_param("sddi", $produk, $jumlah, $harga, $id_detail);
$updateDetail->execute();

header("Location: transaksi.php");
exit;
