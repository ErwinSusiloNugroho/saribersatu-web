<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $query = "DELETE FROM produk WHERE id_produk = $id";
    if (mysqli_query($conn, $query)) {
        echo "ok";
    } else {
        echo "error";
    }
}
?>
