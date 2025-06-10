<?php  
session_start();   

// Konfigurasi database
$host = 'localhost';
$dbname = 'ridjik';
$db_username = 'root';
$db_password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['user']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        // Query untuk mencari admin berdasarkan username
        $query = "SELECT * FROM admin WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && $password === $admin['password']) {
            // Login berhasil
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['user_type'] = 'admin';
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Mohon isi semua field.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Admin</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <<body class="login-page">
  <div class="login-card input">
    <img src="img/profile.jpg" class="profile" alt="Admin">
    <h2>Login</h2>
    <p>Welcome ADMIN</p>

    <?php if (isset($error)): ?>
      <p style="color: red; font-size: 14px; margin-bottom: 10px;"><?= $error ?></p>
    <?php endif; ?>

    <form action="" method="post">
      <input type="user" name="user" placeholder="username" required>
      <input type="password" name="password" placeholder="Your password" required>
      <a href="#" class="forgot">Forget your password</a>
      <button type="submit">Log in</button>
    </form>
  </div>
</body>

</body>
</html>
