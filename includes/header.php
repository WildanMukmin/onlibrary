<?php
include("config.php");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Perpustakaan Online'; ?> - Sistem Manajemen Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">
    <header class="bg-blue-700 text-white shadow-md">
        <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="<?= $base_url ?>/public/index.php"
                class="text-2xl font-bold">
                OnLibrary
            </a>
            <div class="flex items-center space-x-6">
                <?php if ($is_logged_in): ?>
                    <?php if ($role === 'admin'): ?>
                        <a href="<?= $base_url ?>/views/dashboard.php" class="inline-flex items-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                            <i data-lucide="house" class="h-5 w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="<?= $base_url ?>/views/anggota/list.php" class="inline-flex items-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                            <i data-lucide="users-round" class="h-5 w-5"></i>
                            <span>Kelola Anggota</span>
                        </a>
                        <a href="<?= $base_url ?>/views/buku/list.php" class="inline-flex items-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                            <i data-lucide="book-copy" class="h-5 w-5"></i>
                            <span>Kelola Buku</span>
                        </a>
                        <a href="<?= $base_url ?>/views/peminjaman/list.php" class="inline-flex items-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                            <i data-lucide="hand-helping" class="h-5 w-5"></i>
                            <span>Kelola Peminjaman</span>
                        </a>
                    <?php elseif ($role === 'user'): ?>
                        <a href="<?= $base_url ?>/views/dashboard.php" class="inline-flex items-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                            <i data-lucide="house" class="h-5 w-5"></i>
                            <span>Dashboard</span>
                            <a href="<?= $base_url ?>/views/buku/list.php" class="inline-flex items-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                                <i data-lucide="book-copy" class="h-5 w-5"></i>
                                <span>Koleksi Buku</span>
                                <a href="<?= $base_url ?>/views/peminjaman/list.php" class="inline-flex items-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                                    <i data-lucide="hand-helping" class="h-5 w-5"></i>
                                    <span>Riwayat Peminjaman</span>
                                <?php endif; ?>
                                <a href="<?= $base_url ?>/public/auth/logout.php"
                                    class="inline-flex bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md transition duration-300">
                                    <i data-lucide="log-out" class="h-5 w-5 mr-2"></i>
                                    Logout</a>
                            <?php else: ?>
                                <a href="<?= $base_url ?>/public/auth/login.php" class="bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">Login</a>
                                <a href="<?= $base_url ?>/public/auth/register.php"
                                    class="bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-md transition duration-300">Register</a>
                            <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">