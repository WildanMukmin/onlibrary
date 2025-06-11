<?php
include_once '../../includes/header.php';
require_once '../../includes/db_connection.php';
require_once '../../functions/buku.php';
include('../../functions/peminjaman.php');
require_once '../../includes/gate_auth.php';

addTransaction($_POST["id_peminjam"], $_POST["id_buku"], $_POST["tanggal_peminjaman"], $_POST["tanggal_pengembalian"]);
header("location: list.php");

if (!isset($_GET['id'])) {
    die("ID buku tidak ditemukan. <a href='list.php'>Kembali ke daftar buku</a>");
}

$id = $_GET['id'];

// Ambil data buku berdasarkan id
$buku = getBookById($id);

// Cek apakah buku ditemukan
if (!$buku) {
    die("Buku dengan ID $id tidak ditemukan. <a href='list.php'>Kembali ke daftar buku</a>");
}
?>

<div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
  <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-2xl">
    <h2 class="text-2xl font-bold text-blue-700 mb-6 text-center">📘 Form Tambah Peminjaman</h2>

    <form method="POST"  enctype="multipart/form-data" class="space-y-5">
      <div>
        <label class="block font-medium text-gray-700 mb-1">Judul Buku</label>
        <input type="text" name="judul_buku" value="<?= htmlspecialchars($buku['judul']) ?>" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required disabled>
        <input type="hidden" name="id_buku" value="<?= $id ?>">
    </div>

      <div>
        <label class="block font-medium text-gray-700 mb-1">Penulis</label>
        <input type="text" name="penulis" value="<?= htmlspecialchars($buku['penulis']) ?>" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required disabled>
    </div>

      <div>
        <label class="block font-medium text-gray-700 mb-1">Kategori</label>
        <input type="text" name="kategori" value="<?= htmlspecialchars($buku['kategori']) ?>" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required disabled>
    </div>

      <div>
        <label class="block font-medium text-gray-700 mb-1">Deskripsi</label>
        <input type="text" name="deskripsi" value="<?= htmlspecialchars($buku['deskripsi']) ?>" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required disabled>
    </div>
    
    <div>
        <label class="block font-medium text-gray-700 mb-1">ID Peminjam</label>
        <input type="text" name="id_peminjam" value="<?= $role == 'user' ? $user_id : '' ?>" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-medium text-gray-700 mb-1">Tanggal Pinjam</label>
          <input type="date" name="tanggal_peminjaman" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
          <label class="block font-medium text-gray-700 mb-1">Tanggal Pengembalian</label>
          <input type="date" name="tanggal_pengembalian" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
