<?php
session_start();

// Include file koneksi database
require_once 'koneksi.php';

// Function untuk validasi password
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password harus mengandung minimal 1 huruf besar";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password harus mengandung minimal 1 huruf kecil";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password harus mengandung minimal 1 angka";
    }
    
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Password harus mengandung minimal 1 karakter khusus";
    }
    
    return $errors;
}

// Function untuk validasi email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Proses form jika ada data POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $subscribe = isset($_POST['subscribe']) ? 1 : 0;
    
    $errors = [];
    
    // Validasi input kosong
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    }
    
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong";
    }
    
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    }
    
    // Validasi format email
    if (!empty($email) && !validateEmail($email)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Validasi username (minimal 3 karakter, hanya alphanumeric dan underscore)
    if (!empty($username)) {
        if (strlen($username) < 3) {
            $errors[] = "Username minimal 3 karakter";
        }
        
        if (strlen($username) > 50) {
            $errors[] = "Username maksimal 50 karakter";
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = "Username hanya boleh mengandung huruf, angka, dan underscore";
        }
    }
    
    // Validasi password
    if (!empty($password)) {
        $passwordErrors = validatePassword($password);
        $errors = array_merge($errors, $passwordErrors);
    }
    
    // Cek apakah email sudah ada
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id_user FROM user WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            $result = $stmt->fetch();
            
            if ($result) {
                $errors[] = "Email atau Username sudah digunakan";
            }
        } catch (PDOException $e) {
            $errors[] = "Error saat mengecek data: " . $e->getMessage();
        }
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO user (email, username, password) VALUES (?, ?, ?)");
            $result = $stmt->execute([$email, $username, $hashedPassword]);
            
            if ($result) {
                // Set session untuk notifikasi sukses
                $_SESSION['success_message'] = "Akun berhasil dibuat! Silakan login.";
                $_SESSION['newsletter_subscription'] = $subscribe;
                
                // Redirect ke halaman login
                header("Location: ulogin.php");
                exit();
            } else {
                $errors[] = "Gagal membuat akun. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
            error_log("Database insert error: " . $e->getMessage());
        }
    }
    
    // Jika ada error, simpan di session dan redirect kembali
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = [
            'email' => $email,
            'username' => $username,
            'subscribe' => $subscribe
        ];
        
        header("Location: udaftar.php");
        exit();
    }
}

// Jika tidak ada POST data, redirect ke form pendaftaran
header("Location: udaftar.php");
exit();
?>