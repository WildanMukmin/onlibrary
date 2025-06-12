<?php
$page_title = "Beranda";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-white p-8 rounded-lg shadow-md text-center">
    <h1 class="text-4xl font-extrabold text-blue-700 mb-4">Selamat Datang di Perpustakaan Online! <?php echo $username ?></h1>
    <p class="text-lg text-gray-700 mb-6">
        Kelola koleksi buku dan data anggota perpustakaan Anda dengan mudah.
    </p>
    <div class="space-x-4">
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                Login Sekarang
            </a>
            <a href="register.php" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                Daftar Akun Baru
            </a>
        <?php else: ?>
            <p class="text-xl text-gray-800">
                Anda telah login sebagai <span class="font-semibold"><?php echo htmlspecialchars($username); ?></span>.
            </p>
            <a href="<?php echo $role === 'admin' ? '../views/dashboard.php' : '../views/dashboard.php'; ?>" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 mt-4">
                Menuju Dashboard
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>