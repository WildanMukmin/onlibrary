<?php
$page_title = "Daftar Anggota";
require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../functions/anggota.php';
require_once '../../includes/gate_admin.php';

$members = getMembers();
?>

<?php
if (isset($_SESSION['success_time']) && time() - $_SESSION['success_time'] > 10) {
    unset($_SESSION['success'], $_SESSION['success_time']);
}

if (isset($_SESSION['error_time']) && time() - $_SESSION['error_time'] > 10) {
    unset($_SESSION['error'], $_SESSION['error_time']);
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-gray-800">👥 Manajemen Anggota</h1>
        <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md shadow transition duration-200">
            + Tambah Anggota
        </a>
    </div>
    <div>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 mb-4 rounded-lg">
                <?php echo $_SESSION['success']; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 mb-4 rounded-lg">
                <?php echo $_SESSION['error']; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-blue-600 text-white uppercase text-sm">
                <tr>
                    <th class="px-6 py-3 text-left">No</th>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Alamat</th>
                    <th class="px-6 py-3 text-left">Nomor</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <?php if ($members && $members->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($members as $member): ?>
                        <tr class="hover:bg-gray-50 border-t">
                            <td class="px-6 py-4"><?php echo $no++; ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($member['nama']?? ''); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($member['email']?? ''); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($member['alamat']?? ''); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($member['nomor']?? ''); ?></td>
                            <td class="px-6 py-4 text-center space-x-2 flex">
                                <a href="edit.php?id=<?php echo $member['id']; ?>" class="text-blue-600 hover:underline text-sm">✏️ Edit</a>
                                <a href="delete.php?id=<?php echo $member['id']; ?>" class="text-red-600 hover:underline text-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">🗑️ Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-500">Belum ada anggota yang terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php'; 
?>