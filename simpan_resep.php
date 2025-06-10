<?php
header('Content-Type: application/json');
include 'koneksi.php';
ob_start(); // Menahan output yang tidak disengaja
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function respond($success, $message, $data = null) {
    ob_clean(); // Bersihkan buffer sebelum output JSON
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Metode request harus POST');
}

// Ambil data dari POST
$namaProduk = isset($_POST['namaProduk']) ? trim($_POST['namaProduk']) : '';
$id_produk = isset($_POST['id_produk']) ? intval($_POST['id_produk']) : 0; 
$kodeProduk = isset($_POST['kodeProduk']) ? trim($_POST['kodeProduk']) : ''; 
$bahanResep = isset($_POST['bahanResep']) ? trim($_POST['bahanResep']) : '';
$caraPembuatan = isset($_POST['caraPembuatan']) ? trim($_POST['caraPembuatan']) : '';

// Validasi input
if (!$namaProduk || !$id_produk || !$kodeProduk || !$bahanResep || !$caraPembuatan) {
    respond(false, 'Semua field harus diisi. Debug: nama=' . $namaProduk . ', id=' . $id_produk . ', kode=' . $kodeProduk);
}

// Cek apakah produk ada di tabel produk berdasarkan id_produk
$sqlCekProduk = "SELECT id_produk, nama_produk, kode_produk FROM produk WHERE id_produk = ?";
$stmt = $conn->prepare($sqlCekProduk);
if (!$stmt) {
    respond(false, 'Prepare statement gagal: ' . $conn->error);
}
$stmt->bind_param('i', $id_produk);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    respond(false, 'Produk tidak ditemukan dengan ID: ' . $id_produk);
}

$produkData = $result->fetch_assoc();
$stmt->close();

// Cek apakah sudah ada resep untuk produk ini
$sqlCekResep = "SELECT id_resep FROM resep WHERE id_produk = ?";
$stmtCek = $conn->prepare($sqlCekResep);
$stmtCek->bind_param('i', $id_produk);
$stmtCek->execute();
$resultCek = $stmtCek->get_result();

if ($resultCek->num_rows > 0) {
    $stmtCek->close();
    respond(false, 'Produk ini sudah memiliki resep. Gunakan fitur edit untuk mengubah resep.');
}
$stmtCek->close();

// Insert resep baru
$sqlInsert = "INSERT INTO resep (id_produk, bahan, cara) VALUES (?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
if (!$stmtInsert) {
    respond(false, 'Prepare insert gagal: ' . $conn->error);
}

$stmtInsert->bind_param('iss', $id_produk, $bahanResep, $caraPembuatan);

if (!$stmtInsert->execute()) {
    respond(false, 'Gagal menyimpan data resep: ' . $stmtInsert->error);
}

$idResepBaru = $conn->insert_id;
$stmtInsert->close();

// Update id_resep di tabel produk
$sqlUpdateProduk = "UPDATE produk SET id_resep = ? WHERE id_produk = ?";
$stmtUpdate = $conn->prepare($sqlUpdateProduk);
if (!$stmtUpdate) {
    respond(false, 'Prepare update produk gagal: ' . $conn->error);
}

$stmtUpdate->bind_param('ii', $idResepBaru, $id_produk);
if (!$stmtUpdate->execute()) {
    respond(false, 'Gagal mengupdate produk dengan id_resep: ' . $stmtUpdate->error);
}
$stmtUpdate->close();

// Response success dengan format yang sesuai dengan JavaScript
respond(true, 'Resep berhasil disimpan!', [
    'id_resep' => $idResepBaru,
    'id_produk' => $id_produk,
    'nama_produk' => $produkData['nama_produk'],
    'kode_produk' => $produkData['kode_produk'],
    'resep' => [
        'bahan' => explode("\n", $bahanResep),
        'cara' => $caraPembuatan
    ]
]);
?>