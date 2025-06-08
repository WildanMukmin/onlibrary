<?php
$page_title = "Tambah Anggota";
require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../functions/anggota.php';
require_once '../../includes/gate_admin.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $nomor = mysqli_real_escape_string($conn, trim($_POST['nomor']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));

    // Validasi data tidak kosong (opsional tambahan)
    if (empty($nama) || empty($email) || empty($_POST['password']) || empty($nomor) || empty($alamat)) {
        $error_message = "Semua field wajib diisi.";
    } else {
        addMember($nama, $email, $password, $nomor, $alamat);
        header("Location: list.php");
        exit;
    }
}
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">➕ Tambah Anggota Baru</h1>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 text-red-700 px-4 py-3 mb-4 rounded-lg">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="add.php" method="POST" class="space-y-6">
            
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            
            <div>
                <label for="nomor" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                <input type="text" name="nomor" id="nomor" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <a href="list.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">Simpan Anggota</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>