<?php
include_once '../../includes/header.php';
require_once '../../includes/db_connection.php';
include('../../functions/functions.php');
require_once '../../includes/gate_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  pinjamBuku($_POST["id_peminjam"], $_POST["id_buku"]);
  header("location: list.php");
}

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

    <form action="" method="POST" class="space-y-5">
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

      <div class="flex justify-end space-x-3 pt-4">
        <a href="list.php" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">Batal</a>
          <?php if ($buku['stok'] > 0):?>
              <button type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-xl shadow-md transition">
              simpan
          </button>
          <?php endif?>
          <?php if($buku['stok'] <= 0):?>
              <button disabled class="bg-blue-200 hover:bg-blue-300 text-white font-semibold px-5 py-2 rounded-xl shadow-md transition">
              simpan
          </button>
          <?php endif?>
      </div>
    </form>
  </div>
</div>

<?php include_once '../../includes/footer.php'; ?>