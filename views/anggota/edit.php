<?php
$page_title = "Edit Anggota";
require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../functions/anggota.php';
require_once '../../includes/gate_admin.php';

$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($member_id <= 0) {
    header("Location: list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $nomor = trim($_POST['nomor']);
    
    if (updateMember($member_id, $nama, $alamat, $nomor)) {
        header("Location: list.php?status=updated");
        exit;
    } else {
        $error_message = "Gagal memperbarui data anggota. Silakan coba lagi.";
    }
}

$member = getMember($member_id);
if (!$member) {
    header("Location: list.php?error=notfound");
    exit;
}
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">✏️ Edit Data Anggota</h1>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 text-red-700 px-4 py-3 mb-4 rounded-lg">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="edit.php?id=<?php echo $member['id']; ?>" method="POST" class="space-y-6">
            
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($member['nama']); ?>">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email (Tidak dapat diubah)</label>
                <input type="email" id="email" disabled class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" value="<?php echo htmlspecialchars($member['email']); ?>">
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($member['alamat']); ?></textarea>
            </div>
            
            <div>
                <label for="nomor" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                <input type="text" name="nomor" id="nomor" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($member['nomor']); ?>">
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <a href="list.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>