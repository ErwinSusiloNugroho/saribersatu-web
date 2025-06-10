<?php  
// Konfigurasi database 
$host = 'localhost'; 
$dbname = 'ridjik'; 
$db_username = 'root'; 
$db_password = '';  

try {     
    // Koneksi PDO untuk konsistensi
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password);     
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Koneksi MySQLi untuk kompatibilitas (jika masih dibutuhkan)
    $conn = new mysqli($host, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Koneksi MySQLi gagal: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    
} catch(PDOException $e) {     
    die("Koneksi PDO gagal: " . $e->getMessage()); 
} catch(Exception $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>