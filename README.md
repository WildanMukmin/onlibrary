# 📚 OnLibrary
Proyek ini merupakan sistem manajemen perpustakaan sederhana yang dibangun menggunakan PHP dan MySQL. Tujuannya adalah untuk mengelola data buku, anggota, dan transaksi peminjaman secara aman dan konsisten, dengan memanfaatkan stored procedure, trigger, transaction, dan stored function. Sistem ini juga dirancang untuk memiliki mekanisme backup otomatis demi menjaga keamanan data.

## 📌 Detail Konsep
### 🧠 Stored Procedure 
Stored procedure bertindak seperti SOP internal yang menetapkan alur eksekusi berbagai operasi penting di sistem perbankan. Procedure ini disimpan langsung di lapisan database, sehingga dapat menjamin konsistensi, efisiensi, dan keamanan eksekusi, terutama dalam sistem terdistribusi atau multi-user.

`pinjam_buku`
`pinjam_buku(p_id_anggota, p_id_buku, p_status)`: Memeriksa apakah anggota telah mencapai batas peminjaman maksimum dan apakah stok buku masih tersedia sebelum mencatat transaksi peminjaman baru.

```
  DECLARE jumlahDipinjam INT;
  DECLARE stokSekarang INT;

  SET jumlahDipinjam = jumlah_buku_dipinjam(p_id_anggota);

  IF jumlahDipinjam >= 3 THEN
    SET p_status = 'Anggota telah meminjam maksimal 3 buku';
  ELSE
    SELECT stok INTO stokSekarang FROM buku WHERE id = p_id_buku;

    IF stokSekarang <= 0 THEN
      SET p_status = 'Stok buku habis';
    ELSE
      INSERT INTO transaksi (id_anggota, id_buku, tanggal_peminjaman, status)
      VALUES (p_id_anggota, p_id_buku, CURDATE(), 'dipinjam');
      SET p_status = 'Peminjaman berhasil';
    END IF;
  END IF;
```

### 🚨 Trigger
Trigger dalam sistem ini berfungsi sebagai pengawas otomatis yang menjaga integritas data stok buku. Ada dua trigger utama yang bekerja pada tabel transaksi.

*`kurangi_stok_setelah_pinjam`: Aktif setelah transaksi peminjaman baru ditambahkan (AFTER INSERT). Trigger ini secara otomatis mengurangi jumlah stok pada tabel buku.
```
CREATE TRIGGER kurangi_stok_setelah_pinjam
AFTER INSERT ON transaksi
FOR EACH ROW
BEGIN
  IF NEW.status = 'dipinjam' THEN
    UPDATE buku
    SET stok = stok - 1
    WHERE id = NEW.id_buku;
  END IF;
END $$
```

*`tambah_stok_setelah_kembali`: Aktif setelah status transaksi diubah menjadi 'dikembalikan' (AFTER UPDATE). Trigger ini mengembalikan jumlah stok pada tabel buku.
```
CREATE TRIGGER tambah_stok_setelah_kembali
AFTER UPDATE ON transaksi
FOR EACH ROW
BEGIN
  IF OLD.status = 'dipinjam' AND NEW.status = 'dikembalikan' THEN
    UPDATE buku
    SET stok = stok + 1
    WHERE id = NEW.id_buku;
  END IF;
END $$
```

### 🔄 Transaction (Transaksi)
Dalam sistem perpustakaan, sebuah operasi seperti pengembalian buku harus bersifat atomik. Artinya, status peminjaman harus berhasil diubah, dan stok buku harus berhasil diperbarui. Jika salah satu gagal, seluruh proses harus dibatalkan. Prinsip ini diimplementasikan menggunakan beginTransaction() dan commit() pada operasi database.
*Implementasi transaction untuk procedure `pinjam_buku`
```
    try {
        $conn->begin_transaction();
        $stmt = $conn->prepare("CALL pinjam_buku(?, ?, @status)");
        $stmt->bind_param("ii", $id_anggota, $id_buku);
        $stmt->execute();
        
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
```

### 📺 Stored Function 
* Aplikasi
`dashboard.php`
```php
$borrowed_books_list = getBorrowedBooksByUser($user_id);
$borrowed_count = getJumlahBukuDipinjam($user_id);
```
```html
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
```
```php
function getJumlahBukuDipinjam($id_anggota) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT jumlah_buku_dipinjam(?) as jumlah");
    $stmt->bind_param("i", $id_anggota);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['jumlah'] ?? 0;
}
```

### 🔄 Backup Otomatis
Untuk menjaga ketersediaan dan keamanan data, sistem ini dilengkapi fitur backup otomatis. Proses ini dijalankan menggunakan `skrip .bat` yang dieksekusi secara berkala oleh Task Scheduler di Windows. Skrip ini menggunakan `mysqldump` untuk membuat salinan database. Setiap file backup disimpan di direktori backup/ dengan nama yang menyertakan tanggal dan waktu, sehingga mudah untuk ditelusuri.

`backup.bat `
```
@echo off
setlocal enabledelayedexpansion

set "backupDir=D:\laragon\www\onlibrary\backup"
set "mysqlDir=D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin"

:: Extract date parts
set "year=%date:~6,4%"
set "month=%date:~3,2%"
set "day=%date:~0,2%"

:: Extract time parts and ensure two-digit hour
set "hour=%time:~0,2%"
set "minute=%time:~3,2%"

:: Replace leading space with zero for hours less than 10
if "!hour:~0,1!"==" " set "hour=0!hour:~1,1!"

:: Construct timestamp
set "timestamp=!year!-!month!-!day!_!hour!-!minute!"

"%mysqlDir%\mysqldump" -uroot perpustakaan > "%backupDir%\backup_onlibrary_%timestamp%.sql"

endlocal
```

## 🧩 Relevansi Proyek dengan Pemrosesan Data Terdistribusi
Sistem ini dirancang dengan memperhatikan prinsip-prinsip dasar pemrosesan data terdistribusi:

* `Konsistensi`: Logika bisnis yang krusial (peminjaman, penghitungan stok) ditempatkan di dalam database (melalui stored procedures, functions, dan triggers) untuk memastikan bahwa aturan bisnis selalu diterapkan secara seragam.
* `Reliabilitas`: Penggunaan transaction (COMMIT/ROLLBACK) memastikan bahwa setiap operasi bersifat "all-or-nothing," menjaga database tetap dalam keadaan stabil dan andal bahkan jika terjadi kegagalan sistem atau interupsi.
* `Integritas`: Dengan menempatkan validasi dan proses di lapisan database, integritas data tetap terjaga, tidak peduli berapa banyak aplikasi atau layanan (node) yang mengaksesnya di masa depan.

