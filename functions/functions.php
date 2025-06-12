<?php
// ===== TRANSACTION FUNCTIONS =====
require_once __DIR__ . '/../includes/db_connection.php';

/**
 * Menggunakan MySQL Function untuk mengecek jumlah buku yang dipinjam
 */
function getJumlahBukuDipinjam($id_anggota) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT jumlah_buku_dipinjam(?) as jumlah");
    $stmt->bind_param("i", $id_anggota);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['jumlah'] ?? 0;
}

/**
 * Menggunakan Stored Procedure untuk meminjam buku
 */
function pinjamBuku($id_anggota, $id_buku) {
    global $conn;
    
    try {
        // Mulai transaction
        $conn->begin_transaction();
        
        // Panggil stored procedure
        $stmt = $conn->prepare("CALL pinjam_buku(?, ?, @status)");
        $stmt->bind_param("ii", $id_anggota, $id_buku);
        $stmt->execute();
        
        // Ambil status hasil
        $result = $conn->query("SELECT @status as status");
        $status = $result->fetch_assoc()['status'];
        
        if ($status === 'Peminjaman berhasil') {
            $conn->commit();
            return ['success' => true, 'message' => $status];
        } else {
            $conn->rollback();
            return ['success' => false, 'message' => $status];
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Mengembalikan buku dengan transaction yang aman
 */
function kembalikanBuku($transaksi_id) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Update status transaksi
        $stmt = $conn->prepare("UPDATE transaksi SET status = 'dikembalikan', tanggal_pengembalian = CURDATE() WHERE id = ? AND status = 'dipinjam'");
        $stmt->bind_param("i", $transaksi_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $conn->commit();
            return ['success' => true, 'message' => 'Buku berhasil dikembalikan'];
        } else {
            $conn->rollback();
            return ['success' => false, 'message' => 'Transaksi tidak ditemukan atau sudah dikembalikan'];
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getTransactions() {
    global $conn;

    $sql = "
        SELECT 
            transaksi.id AS transaksi_id,
            anggota.id AS anggota_id,
            anggota.nama AS nama_anggota,
            anggota.email AS email_anggota,
            buku.id AS buku_id,
            buku.judul AS judul_buku,
            buku.penulis AS penulis_buku,
            transaksi.tanggal_peminjaman,
            transaksi.tanggal_pengembalian,
            transaksi.status
        FROM transaksi
        JOIN anggota ON transaksi.id_anggota = anggota.id
        JOIN buku ON transaksi.id_buku = buku.id
        ORDER BY transaksi.tanggal_peminjaman DESC
    ";

    $result = $conn->query($sql);
    return $result;
}

function getTotalBorrowedBooks() {
    global $conn;
    $sql = "SELECT COUNT(id) as total_borrowed FROM transaksi WHERE status = 'dipinjam'";
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_assoc()['total_borrowed'];
    }
    return 0;
}

function getTransactionsById($id) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            transaksi.id AS transaksi_id,
            anggota.id AS anggota_id,
            anggota.nama AS nama_anggota,
            anggota.email AS email_anggota,
            buku.id AS buku_id,
            buku.judul AS judul_buku,
            buku.penulis AS penulis_buku,
            transaksi.tanggal_peminjaman,
            transaksi.tanggal_pengembalian,
            transaksi.status
        FROM transaksi
        JOIN anggota ON transaksi.id_anggota = anggota.id
        JOIN buku ON transaksi.id_buku = buku.id
        WHERE transaksi.id_anggota = ?
        ORDER BY transaksi.tanggal_peminjaman DESC
    ");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

function getTransactionById($id) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            transaksi.id AS transaksi_id,
            anggota.id AS anggota_id,
            anggota.nama AS nama_anggota,
            buku.id AS buku_id,
            buku.judul AS judul_buku,
            buku.penulis AS penulis_buku,
            transaksi.tanggal_peminjaman,
            transaksi.tanggal_pengembalian,
            transaksi.status
        FROM transaksi
        JOIN anggota ON transaksi.id_anggota = anggota.id
        JOIN buku ON transaksi.id_buku = buku.id
        WHERE transaksi.id = ?
    ");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function deleteTransactionById($id) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("DELETE FROM transaksi WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'message' => 'Transaksi berhasil dihapus'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getBorrowedBooksByUser($user_id) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT 
            b.judul AS judul_buku, 
            t.tanggal_pengembalian 
        FROM transaksi t 
        JOIN buku b ON t.id_buku = b.id 
        WHERE t.id_anggota = ? AND t.status = 'dipinjam'
        ORDER BY t.tanggal_pengembalian ASC
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/**
 * Get transaction statistics
 */
function getTransactionStats() {
    global $conn;
    
    $sql = "
        SELECT 
            COUNT(*) as total_transaksi,
            SUM(CASE WHEN status = 'dipinjam' THEN 1 ELSE 0 END) as sedang_dipinjam,
            SUM(CASE WHEN status = 'dikembalikan' THEN 1 ELSE 0 END) as sudah_dikembalikan
        FROM transaksi
    ";
    
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// ===== BOOK FUNCTIONS =====

function getBooks() {
    global $conn;
    $sql = "SELECT * FROM buku ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result;
}

function getTotalBookStock() {
    global $conn;
    $sql = "SELECT SUM(stok) as total_stock FROM buku";
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_assoc()['total_stock'] ?? 0;
    }
    return 0;
}

function getAvailableBooks() {
    global $conn;
    $sql = "SELECT * FROM buku WHERE stok > 0 ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result;
}

function getBookById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addBook($judul, $penulis, $penerbit, $tahun_terbit, $isbn, $kategori, $deskripsi, $stok, $image) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Handle image upload
        $image_path = '';
        if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/images/buku/';
            $fileName = $isbn . '_' . time() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                $image_path = '/onlibrary/public/images/buku/' . $fileName;
            }
        }
        
        $stmt = $conn->prepare("
            INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, isbn, kategori, deskripsi, stok, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("sssssssss", $judul, $penulis, $penerbit, $tahun_terbit, $isbn, $kategori, $deskripsi, $stok, $image_path);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'message' => 'Buku berhasil ditambahkan'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function updateBook($id, $judul, $penulis, $penerbit, $tahun_terbit, $isbn, $kategori, $deskripsi, $stok, $image) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        $oldBook = getBookById($id);
        $image_path = $oldBook['image_path'];
        
        // Handle image upload
        if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if (!empty($oldBook['image_path'])) {
                $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($oldBook['image_path'], PHP_URL_PATH);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $uploadDir = '../../public/images/buku/';
            $fileName = $isbn . '_' . time() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                $image_path = '/onlibrary/public/images/buku/' . $fileName;
            }
        }

        $stmt = $conn->prepare("
            UPDATE buku 
            SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ?, isbn = ?, 
                kategori = ?, deskripsi = ?, stok = ?, image_path = ?
            WHERE id = ?
        ");
        
        $stmt->bind_param("sssssssssi", $judul, $penulis, $penerbit, $tahun_terbit, $isbn, $kategori, $deskripsi, $stok, $image_path, $id);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'message' => 'Buku berhasil diperbarui'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteBook($id) {
    global $conn;

    try {
        $conn->begin_transaction();
        
        $book = getBookById($id);
        
        // Delete image file
        if ($book && !empty($book['image_path'])) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($book['image_path'], PHP_URL_PATH);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $stmt = $conn->prepare("DELETE FROM buku WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'message' => 'Buku berhasil dihapus'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Get book statistics
 */
function getBookStats() {
    global $conn;
    
    $sql = "
        SELECT 
            COUNT(*) as total_buku,
            SUM(stok) as total_stok,
            SUM(CASE WHEN stok > 0 THEN 1 ELSE 0 END) as buku_tersedia,
            SUM(CASE WHEN stok = 0 THEN 1 ELSE 0 END) as buku_habis
        FROM buku
    ";
    
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// ===== MEMBER FUNCTIONS =====

function getMembers(){
    global $conn;
    $sql = "SELECT * FROM anggota ORDER BY registered_at DESC";
    $result = $conn->query($sql);
    return $result;
}

function getMember($member_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM anggota WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getTotalMembers() {
    global $conn;
    $sql = "SELECT COUNT(id) as total FROM anggota";
    $result = $conn->query($sql);
    if ($result) {
        return $result->fetch_assoc()['total'];
    }
    return 0;
}

function addMember($nama, $email, $password, $nomor, $alamat) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Validasi input
        if (empty($nama) || empty($email) || empty($password) || empty($nomor) || empty($alamat)) {
            throw new Exception("Semua field wajib diisi.");
        }

        // Cek email sudah ada
        $checkStmt = $conn->prepare("SELECT id FROM anggota WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            throw new Exception("Email sudah terdaftar.");
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert member
        $stmt = $conn->prepare("INSERT INTO anggota (nama, email, password, nomor, alamat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $email, $hashedPassword, $nomor, $alamat);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'message' => 'Anggota berhasil ditambahkan'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function updateMember($id, $nama, $alamat, $nomor) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("UPDATE anggota SET nama = ?, alamat = ?, nomor = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama, $alamat, $nomor, $id);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'message' => 'Anggota berhasil diperbarui'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteMember($id) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("DELETE FROM anggota WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'message' => 'Anggota berhasil dihapus'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Get member with their borrowing statistics
 */
function getMemberWithStats($member_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            jumlah_buku_dipinjam(a.id) as jumlah_dipinjam,
            COUNT(t.id) as total_transaksi
        FROM anggota a
        LEFT JOIN transaksi t ON a.id = t.id_anggota
        WHERE a.id = ?
        GROUP BY a.id
    ");
    
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get top borrowers
 */
function getTopBorrowers($limit = 10) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT 
            a.id,
            a.nama,
            a.email,
            COUNT(t.id) as total_peminjaman,
            jumlah_buku_dipinjam(a.id) as sedang_dipinjam
        FROM anggota a
        LEFT JOIN transaksi t ON a.id = t.id_anggota
        WHERE a.role = 'user'
        GROUP BY a.id
        ORDER BY total_peminjaman DESC
        LIMIT ?
    ");
    
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Authentication function
 */
function authenticateUser($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM anggota WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            return [
                'success' => true, 
                'user' => [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Email atau password salah'];
}

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total members
    $stats['total_members'] = getTotalMembers();
    
    // Total books and stock
    $bookStats = getBookStats();
    $stats['total_books'] = $bookStats['total_buku'];
    $stats['total_stock'] = $bookStats['total_stok'];
    
    // Transaction stats
    $transactionStats = getTransactionStats();
    $stats['total_transactions'] = $transactionStats['total_transaksi'];
    $stats['books_borrowed'] = $transactionStats['sedang_dipinjam'];
    
    // Overdue books
    $overdueResult = $conn->query("
        SELECT COUNT(*) as overdue_count 
        FROM transaksi 
        WHERE status = 'dipinjam' AND tanggal_pengembalian < CURDATE()
    ");
    $stats['overdue_books'] = $overdueResult->fetch_assoc()['overdue_count'];
    
    return $stats;
}

/**
 * Get overdue transactions
 */
function getOverdueTransactions() {
    global $conn;
    
    $sql = "
        SELECT 
            t.id as transaksi_id,
            a.nama as nama_anggota,
            a.email,
            b.judul as judul_buku,
            t.tanggal_peminjaman,
            t.tanggal_pengembalian,
            DATEDIFF(CURDATE(), t.tanggal_pengembalian) as hari_terlambat
        FROM transaksi t
        JOIN anggota a ON t.id_anggota = a.id
        JOIN buku b ON t.id_buku = b.id
        WHERE t.status = 'dipinjam' AND t.tanggal_pengembalian < CURDATE()
        ORDER BY hari_terlambat DESC
    ";
    
    return $conn->query($sql);
}

?>