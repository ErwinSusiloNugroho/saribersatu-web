<?php  
session_start();       

// Include file koneksi database
require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {     
    $username = trim($_POST['username']);
    $password = $_POST['password'];          
    
    if (!empty($username) && !empty($password)) {         
        try {
            // Query untuk mencari user berdasarkan username atau email
            $query = "SELECT * FROM user WHERE username = :username OR email = :username";         
            $stmt = $pdo->prepare($query);         
            $stmt->bindParam(':username', $username);         
            $stmt->execute();                  
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);                 
            
            // Verifikasi password dengan password_verify untuk password yang di-hash
            if ($user && password_verify($password, $user['password'])) {             
                // Login berhasil             
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email']; 
                $_SESSION['user_type'] = 'user';                         
                
                header("Location: udashboard.php");     
                exit;         
            } else {             
                $error = "Username/Email atau password salah.";         
            }
        } catch (PDOException $e) {
            $error = "Error koneksi database: " . $e->getMessage();
        }     
    } else {         
        $error = "Mohon isi semua field.";     
    } 
} 

// Ambil pesan sukses jika ada
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="login-container">
    <div class="login-left">
      <div class="login-header">
        <h2>RIDJIK INVENTORY MANAGEMENT STOCK</h2>
        <h1>Login</h1>
      </div>

      <?php if (!empty($success_message)): ?>
        <div class="success-message" style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 10px;">
          <?= htmlspecialchars($success_message) ?>
        </div>
      <?php endif; ?>

      <?php if (isset($error)): ?>
        <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 10px;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="" class="login-form">
        <div class="input-group">
          <input type="text" class="input-field" name="username" placeholder="Username atau Email" required>
        </div>
        <div class="input-group password-group">
          <input type="password" class="input-field" name="password" placeholder="Password" required>
          <span class="toggle-password" data-visible="false">&#128065;</span>
        </div>
        <div class="options">
          <label><input type="checkbox" name="remember"> <strong>Remember me</strong></label>
          <a href="#">Forget Password?</a>
        </div>
        <button type="submit" class="login-btn">Login</button>
        <div class="options" style="margin-top: 15px; justify-content: center;">
          <a href="udaftar.php">Create an account</a>
        </div>
      </form>
    </div>
    <div class="login-right">
      <img src="img/4.jpg" alt="Right side image">
    </div>
  </div>

  <script>
  const icon = document.querySelector('.toggle-password');
  const passwordField = document.querySelector('input[name="password"]');

  icon.innerHTML = '🙈';
  icon.setAttribute('data-visible', 'false');

  icon.addEventListener('click', function () {
    const isVisible = icon.getAttribute('data-visible') === 'true';

    if (isVisible) {
      passwordField.setAttribute('type', 'password');
      icon.innerHTML = '🙈';
      icon.setAttribute('data-visible', 'false');
    } else {
      passwordField.setAttribute('type', 'text');
      icon.innerHTML = '🙉';
      icon.setAttribute('data-visible', 'true');
    }
  });
</script>

</body>
</html>