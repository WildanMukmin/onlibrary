<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/anggota.php';
require_once __DIR__ . '/../functions/buku.php';
require_once __DIR__ . '/../functions/peminjaman.php';
require_once '../includes/gate_auth.php';

$user = $_SESSION['user'];
$admin_name = $user['nama'];

if ($role === "admin") {

    $total_members = getTotalMembers();
    $total_book_stock = getTotalBookStock();
    $total_borrowed_books = getTotalBorrowedBooks();
?>
    <div>
        <h1 class="text-2xl font-bold mb-4">Halo, <?php echo $admin_name; ?></h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="bg-blue-100 p-6 rounded-lg flex items-center space-x-4">
                <div class="bg-blue-600 p-4 rounded-lg">
                    <i data-lucide="users-round" class="h-8 w-8 text-white"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total Anggota</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $total_members; ?></p>
                </div>
            </div>

            <div class="bg-green-100 p-6 rounded-lg flex items-center space-x-4">
                <div class="bg-green-600 p-4 rounded-lg">
                    <i data-lucide="book-copy" class="h-8 w-8 text-white"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total Stok Buku</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $total_book_stock; ?></p>
                </div>
            </div>

            <div class="bg-yellow-100 p-6 rounded-lg flex items-center space-x-4">
                <div class="bg-yellow-600 p-4 rounded-lg">
                    <i data-lucide="hand-helping" class="h-8 w-8 text-white"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Buku Sedang Dipinjam</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $total_borrowed_books; ?></p>
                </div>
            </div>
        </div>
    </div>

<?php
}
else if($role === "user"){
    require_once __DIR__ . '/../functions/peminjaman.php';

    $user_id = $_SESSION['user']['id'];
    
    $borrowed_books_list = getBorrowedBooksByUser($user_id);
    $borrowed_count = count($borrowed_books_list);
?>
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Halo, <?php echo htmlspecialchars($user['nama']); ?>!</h1>
        <p class="text-gray-600 mb-8">Berikut adalah ringkasan aktivitas perpustakaan Anda.</p>

        <div class="mb-8">
            <div class="inline-block bg-blue-100 p-6 rounded-lg flex items-center space-x-4">
                <div class="bg-blue-600 p-4 rounded-lg">
                    <i data-lucide="book-check" class="h-8 w-8 text-white"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Buku Sedang Dipinjam</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $borrowed_count; ?></p>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar Buku Pinjaman Anda</h2>
            <div class="overflow-x-auto rounded-lg shadow-lg">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-blue-600 text-white uppercase text-sm">
                        <tr>
                            <th class="px-6 py-3 text-left">No</th>
                            <th class="px-6 py-3 text-left">Judul Buku</th>
                            <th class="px-6 py-3 text-left">Batas Pengembalian</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        <?php if (!empty($borrowed_books_list)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($borrowed_books_list as $book): ?>
                                <tr class="hover:bg-gray-50 border-t">
                                    <td class="px-6 py-4"><?php echo $no++; ?></td>
                                    <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($book['judul_buku']); ?></td>
                                    <td class="px-6 py-4 font-bold text-red-600">
                                        <?php echo date("d M Y", strtotime($book['tanggal_pengembalian'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-500">
                                    Anda sedang tidak meminjam buku. Saatnya ke perpustakaan!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
}
require_once __DIR__ . '/../includes/footer.php';
?>