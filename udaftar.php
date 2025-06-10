<?php
session_start();

// Ambil error dan old input jika ada
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old_input = isset($_SESSION['old_input']) ? $_SESSION['old_input'] : [];

// Hapus dari session setelah diambil
unset($_SESSION['errors']);
unset($_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - RIDJIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="flex w-full max-w-5xl shadow-lg rounded-xl overflow-hidden bg-white">
        <!-- Form Pendaftaran -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-2xl font-semibold text-green-800 mb-4">Welcome To RIDJIK</h2>
            <p class="text-sm mb-6">Already have an account? <a href="ulogin.php" class="text-green-600 underline">Log in</a></p>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="proses_daftar.php" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" 
                           value="<?= htmlspecialchars($old_input['email'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
                           required>
                </div>
                <div>
                    <label for="username" class="block text-gray-700">Username</label>
                    <input type="text" name="username" id="username" 
                           value="<?= htmlspecialchars($old_input['username'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
                           required>
                    <p class="text-xs text-gray-600 mt-1">Minimal 3 karakter, hanya huruf, angka, dan underscore</p>
                </div>
                <div>
                    <label for="password" class="block text-gray-700">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" 
                               class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
                               required>
                        <button type="button" id="togglePassword" class="absolute right-2 top-2 text-gray-500">Hide</button>
                    </div>
                    <div class="text-xs text-gray-600 mt-2 space-y-1">
                        <p>• Use 8 or more characters</p>
                        <p>• One uppercase character</p>
                        <p>• One lowercase character</p>
                        <p>• One special character</p>
                        <p>• One number</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="subscribe" id="subscribe" 
                           <?= isset($old_input['subscribe']) && $old_input['subscribe'] ? 'checked' : '' ?>
                           class="mr-2">
                    <label for="subscribe" class="text-sm text-gray-700">I want to receive emails about the product, feature updates, events, and marketing promotions.</label>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700">Create an account</button>
            </form>

        </div>

        <!-- Gambar di samping -->
        <div class="hidden md:block md:w-1/2">
            <div class="h-full w-full bg-cover bg-center" style="background-image: url('img/4.jpg');"></div>
        </div>
    </div>
    <script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        toggleBtn.textContent = isPassword ? 'Show' : 'Hide';
    });
</script>

</body>
</html>