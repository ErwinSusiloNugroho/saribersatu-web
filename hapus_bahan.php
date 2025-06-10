<?php
include 'koneksi.php';

// Enable error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Debug: Log received ID
    error_log("Received ID: " . $id);
    
    // Validasi ID - ubah validasi untuk mengizinkan ID 0
    if (!is_numeric($id) || $id < 0) {
        echo "ID tidak valid: " . $id;
        exit();
    }
    
    // Convert to integer
    $id = (int)$id;
    
    // Debug: Log converted ID
    error_log("Converted ID: " . $id);
    
    // Cek apakah bahan ada sebelum dihapus
    $check_query = "SELECT COUNT(*) as count FROM bahan WHERE id_bahan = $id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (!$check_result) {
        echo "Error checking data: " . mysqli_error($conn);
        exit();
    }
    
    $check_row = mysqli_fetch_assoc($check_result);
    
    if ($check_row['count'] == 0) {
        echo "Bahan dengan ID $id tidak ditemukan";
        exit();
    }
    
    // Hapus bahan
    $delete_query = "DELETE FROM bahan WHERE id_bahan = $id";
    
    if (mysqli_query($conn, $delete_query)) {
        $affected_rows = mysqli_affected_rows($conn);
        error_log("Affected rows: " . $affected_rows);
        
        if ($affected_rows > 0) {
            echo "ok";
        } else {
            echo "Tidak ada data yang dihapus";
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Request tidak valid - Missing POST data";
    error_log("POST data: " . print_r($_POST, true));
}

mysqli_close($conn);
?>