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

*
