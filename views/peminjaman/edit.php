<?php
include_once '../../includes/header.php';
require_once '../../includes/db_connection.php';
require_once '../../functions/peminjaman.php';
require_once '../../includes/gate_admin.php';


updateTransaction($_POST["id"], $_POST["id_peminjam"], $_POST["id_buku"], $_POST["tanggal_peminjaman"], $_POST["tanggal_pengembalian"], $_POST["status"]);
header("location: list.php");

$id = $_GET["id"];
$transaksi = getTransactionById($id);
?>

<div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
  <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-2xl">
    <h2 class="text-2xl font-bold text-blue-700 mb-6 text-center">✏️ Form Edit Peminjaman</h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
      <input type="hidden" name="id" value="<?= $transaksi['transaksi_id'] ?>">

      <div>
        <label class="block font-medium text-gray-700 mb-1">Judul Buku</label>
        <input type="text" value="<?= htmlspecialchars($transaksi['judul_buku']) ?>" class="w-full px-4 py-2 border rounded-md" disabled>
        <input type="hidden" name="id_buku" value="<?= $transaksi['buku_id'] ?>">
      </div>

      <div>
        <label class="block font-medium text-gray-700 mb-1">Penulis</label>
        <input type="text" value="<?= htmlspecialchars($transaksi['penulis_buku']) ?>" class="w-full px-4 py-2 border rounded-md" disabled>
      </div>

      <div>
        <label class="block font-medium text-gray-700 mb-1">Nama Peminjam</label>
        <input type="text" value="<?= htmlspecialchars($transaksi['nama_anggota']) ?>" class="w-full px-4 py-2 border rounded-md" disabled>
        <input type="hidden" name="id_peminjam" value="<?= $transaksi['anggota_id'] ?>">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-medium text-gray-700 mb-1">Tanggal Peminjaman</label>
          <input type="date" name="tanggal_peminjaman" value="<?= $transaksi['tanggal_peminjaman'] ?>" class="w-full px-4 py-2 border rounded-md" required>
        </div>
        <div>
          <label class="block font-medium text-gray-700 mb-1">Tanggal Pengembalian</label>
          <input type="date" name="tanggal_pengembalian" value="<?= $transaksi['tanggal_pengembalian'] ?>" class="w-full px-4 py-2 border rounded-md">
        </div>
      </div>

      <div>
        <label class="block font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full px-4 py-2 border rounded-md">
          <option value="Dipinjam" <?= $transaksi['status'] == 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
          <option value="Dikembalikan" <?= $transaksi['status'] == 'Dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
        </select>
      </div>


      <div class="flex justify-end space-x-3 pt-4">
        <a href="list.php" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md">Batal</a>
        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<?php include_once '../../includes/footer.php'; ?>
