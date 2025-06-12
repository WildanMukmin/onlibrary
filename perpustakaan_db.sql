-- Buat database
CREATE DATABASE IF NOT EXISTS perpustakaan;
USE perpustakaan;

-- Atur mode
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Tabel anggota
CREATE TABLE `anggota` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `role` ENUM('admin','user') DEFAULT 'user',
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nomor` VARCHAR(20) DEFAULT NULL,
  `alamat` TEXT,
  `registered_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data anggota
INSERT INTO `anggota` (`id`, `role`, `nama`, `email`, `password`, `nomor`, `alamat`, `registered_at`) VALUES
(1, 'admin', 'Wildan Mukmin', 'wildan.cooliah@gmail.com', '$2y$10$BuneRjKMKr0H4i.0ksjJGOFbaymkzlmWcE8ptL0Ad/tY7ebAQ8j4K', '0895640025480', 'H6HH+R98, Jl. PB. Marga, Sukadana Ham, Kec. Tj. Karang Bar., Kota Bandar Lampung, Lampung 35215', '2025-06-04 14:21:12'),
(2, 'user', 'Febrina Auliza Azahra', 'febrina.auzahra@gmail.com', '$2y$10$HvWmqAh19bUUmmIqnr/VyOhn7yO5fANBmFZoGsqrLTX.YxXmdV9ki', '0895640026434', 'Kec. Kuta, Kabupaten Badung, Bali', '2025-06-04 14:21:40'),
(3, 'user', 'Febrina Aulia Azahra', 'febrn16@gmail.com', '$2y$10$DexxZDbYQ.Dvm0ljREYzX.1Uv69JRavSP6tr3a6riW4wqVgCvcSeW', '08960025480', 'komp doa', '2025-06-05 12:23:14');

-- Tabel buku
CREATE TABLE `buku` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `penulis` VARCHAR(255) NOT NULL,
  `penerbit` VARCHAR(255) NOT NULL,
  `tahun_terbit` VARCHAR(255) NOT NULL,
  `isbn` VARCHAR(20) NOT NULL UNIQUE,
  `kategori` VARCHAR(100) DEFAULT NULL,
  `deskripsi` TEXT,
  `stok` INT DEFAULT 0,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data buku
INSERT INTO `buku` (`id`, `judul`, `penulis`, `penerbit`, `tahun_terbit`, `isbn`, `kategori`, `deskripsi`, `stok`, `image_path`, `created_at`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', '2005', '9789793062793', 'Novel', 'Kisah perjuangan anak-anak di Belitung', 10, '/onibrary/images/buku/9789793062793_laskar_pelangi_1749120799.png', '2025-06-04 14:20:45'),
(2, 'Atomic Habits', 'James Clear', 'Penguin Random House', '2018', '9780735211292', 'Self-Development', 'Cara membentuk kebiasaan positif', 5, '/onibrary/images/buku/9780735211292_1749126169.png', '2025-06-04 14:20:45'),
(3, 'Bumi', 'Tere Liye', 'Gramedia', '2014', '9786020324783', 'Fiksi', 'Petualangan fantasi remaja bernama Raib', 7, '/onibrary/images/buku/9786020324783_1749126388.png', '2025-06-04 14:20:45');

-- Tabel transaksi
CREATE TABLE `transaksi` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_anggota` INT NOT NULL,
  `id_buku` INT NOT NULL,
  `tanggal_peminjaman` DATE NOT NULL,
  `tanggal_pengembalian` DATE DEFAULT NULL,
  `status` ENUM('dipinjam','dikembalikan') DEFAULT 'dipinjam',
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  KEY `id_buku` (`id_buku`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data transaksi
INSERT INTO `transaksi` (`id`, `id_anggota`, `id_buku`, `tanggal_peminjaman`, `tanggal_pengembalian`, `status`) VALUES
(1, 2, 2, '2025-06-09', '2025-06-28', 'dipinjam'),
(2, 3, 1, '2025-06-26', '2025-07-05', 'dipinjam');

COMMIT;

-- Function: Cek jumlah buku yang dipinjam
DELIMITER $$

CREATE FUNCTION jumlah_buku_dipinjam(idAnggota INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE total INT;
  SELECT COUNT(*) INTO total
  FROM transaksi
  WHERE id_anggota = idAnggota AND status = 'dipinjam';
  RETURN total;
END $$

DELIMITER ;

-- Stored Procedure: Pinjam Buku
DELIMITER $$

CREATE PROCEDURE pinjam_buku (
  IN p_id_anggota INT,
  IN p_id_buku INT,
  OUT p_status VARCHAR(100)
)
BEGIN
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
END $$

DELIMITER ;

-- Trigger: Kurangi stok saat pinjam
DELIMITER $$

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

DELIMITER ;

-- Trigger: Tambah stok saat dikembalikan
DELIMITER $$

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

DELIMITER ;
