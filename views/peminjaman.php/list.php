<?php 
include_once '../../includes/header.php';
include('../../functions/peminjaman.php');
require_once '../../includes/gate_auth.php';
$data_admin = getTransactions();
$data_user = getTransactionsById($_SESSION['user']['id']);
?>

<!-- List Peminjaman Admin -->
<?php if (isset($role) && $role === 'admin'): ?>
  <div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-3xl font-bold text-gray-800">📚 Daftar Peminjaman Buku</h1>
      <a href="../buku/list.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md shadow transition duration-200">
        + Tambah Peminjaman
      </a>
    </div>

    <div class="overflow-x-auto rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200 bg-white">
        <thead class="bg-blue-600 text-white uppercase text-sm">
          <tr>
            <th class="px-6 py-3 text-left">No</th>
            <th class="px-6 py-3 text-left">Nama Anggota</th>
            <th class="px-6 py-3 text-left">Judul Buku</th>
            <th class="px-6 py-3 text-left">Tanggal Pinjam</th>
            <th class="px-6 py-3 text-left">Tanggal Kembali</th>
            <th class="px-6 py-3 text-left">Status</th>
            <th class="px-6 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 text-sm">
          <?php $no = 1; foreach ($data_admin as $row): ?>
            <tr class="hover:bg-gray-50 border-t">
              <td class="px-6 py-4"><?= $no++ ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($row['nama_anggota']) ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($row['judul_buku']) ?></td>
              <td class="px-6 py-4"><?= $row['tanggal_peminjaman'] ?></td>
              <td class="px-6 py-4"><?= $row['tanggal_pengembalian'] ?></td>
              <td class="px-6 py-4">
                <?php if ($row['status'] == 'dipinjam'): ?>
                  <span class="inline-block px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Dipinjam</span>
                <?php else: ?>
                  <span class="inline-block px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Dikembalikan</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center space-x-2 flex items-center justify-around">
                <a href="edit.php?id=<?= $row['transaksi_id'] ?>" class="text-blue-600 hover:underline text-sm">✏️ Edit</a>
                <a href="delete_process.php?id=<?= $row['transaksi_id'] ?>" class="text-red-600 hover:underline text-sm" onclick="return confirm('Yakin ingin menghapus?')">🗑️ Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>


<!-- List Peminjaman User (Dengan Tab) -->
 <?php if (isset($role) && $role === 'user'): ?>
<div class="min-h-screen bg-gray-100 px-4 py-8">
  <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">📖 Daftar Aktivitas Anggota</h1>

    <!-- Tabs -->
    <div class="mb-4 border-b border-gray-200">
      <ul class="flex space-x-4" id="tabs">
        <li>
          <button class="tab-button text-blue-700 font-semibold border-b-2 border-blue-700 px-4 py-2" data-tab="peminjaman">Peminjaman</button>
        </li>
        <li>
          <button class="tab-button text-gray-500 hover:text-blue-700 px-4 py-2" data-tab="pengembalian">Pengembalian</button>
        </li>
      </ul>
    </div>

    <!-- Tab Content -->
    <div id="tab-content">
      <!-- Peminjaman Tab -->
      <div class="tab-pane" id="peminjaman">
        <div class="flex justify-end mb-4">
          <a href="../buku/list.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">+ Tambah Peminjaman</a>
        </div>
        <div class="overflow-x-auto rounded-lg shadow">
          <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-blue-600 text-white uppercase text-sm">
              <tr>
                <th class="px-6 py-3 text-left">No</th>
                <th class="px-6 py-3 text-left">Nama Anggota</th>
                <th class="px-6 py-3 text-left">Judul Buku</th>
                <th class="px-6 py-3 text-left">Tanggal Pinjam</th>
                <th class="px-6 py-3 text-left">Tanggal Pengembalian</th>
                <th class="px-6 py-3 text-left">Status</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
              <?php $no = 1; foreach ($data_user as $row): ?>
                <?php if ($row['status'] == 'dipinjam'): ?>
                <tr class="hover:bg-gray-50 border-t">
                  <td class="px-6 py-4"><?= $no++ ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($row['nama_anggota']) ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($row['judul_buku']) ?></td>
                  <td class="px-6 py-4"><?= $row['tanggal_peminjaman'] ?></td>
                  <td class="px-6 py-4"><?= $row['tanggal_pengembalian'] ?></td>
                  <td class="px-6 py-4"><?= $row['status'] ?></td>
                </tr>
                <?php endif;?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pengembalian Tab -->
      <div class="tab-pane hidden" id="pengembalian">
        <div class="overflow-x-auto rounded-lg shadow">
          <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-blue-600 text-white uppercase text-sm">
              <tr>
                <th class="px-6 py-3 text-left">No</th>
                <th class="px-6 py-3 text-left">Nama Anggota</th>
                <th class="px-6 py-3 text-left">Judul Buku</th>
                <th class="px-6 py-3 text-left">Tanggal Pinjam</th>
                <th class="px-6 py-3 text-left">Tanggal Pengembalian</th>
                <th class="px-6 py-3 text-left">Status</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
              <?php 
              
              $no = 1; foreach ($data_user as $row): ?>
              <?php if ($row['status'] == 'dikembalikan'): ?>
                <tr class="hover:bg-gray-50 border-t">
                  <td class="px-6 py-4"><?= $no++ ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($row['nama_anggota']) ?></td>
                  <td class="px-6 py-4"><?= htmlspecialchars($row['judul_buku']) ?></td>
                  <td class="px-6 py-4"><?= $row['tanggal_peminjaman'] ?></td>
                  <td class="px-6 py-4"><?= $row['tanggal_pengembalian'] ?></td>
                  <td class="px-6 py-4"><?= $row['status'] ?></td>
                </tr>

              <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
  const tabButtons = document.querySelectorAll('.tab-button');
  const tabPanes = document.querySelectorAll('.tab-pane');

  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      tabButtons.forEach(btn => btn.classList.remove('text-blue-700', 'font-semibold', 'border-b-2', 'border-blue-700'));
      button.classList.add('text-blue-700', 'font-semibold', 'border-b-2', 'border-blue-700');

      const target = button.getAttribute('data-tab');
      tabPanes.forEach(pane => {
        pane.classList.add('hidden');
        if (pane.id === target) {
          pane.classList.remove('hidden');
        }
      });
    });
  });
</script>

<?php include_once '../../includes/footer.php'; ?>
