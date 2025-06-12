-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: perpustakaan
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `anggota`
--

DROP TABLE IF EXISTS `anggota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anggota` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` enum('admin','user') DEFAULT 'user',
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nomor` varchar(20) DEFAULT NULL,
  `alamat` text,
  `registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anggota`
--

LOCK TABLES `anggota` WRITE;
/*!40000 ALTER TABLE `anggota` DISABLE KEYS */;
INSERT INTO `anggota` VALUES (1,'admin','Wildan Mukmin','wildan.cooliah@gmail.com','$2y$10$BuneRjKMKr0H4i.0ksjJGOFbaymkzlmWcE8ptL0Ad/tY7ebAQ8j4K','0895640025480','H6HH+R98, Jl. PB. Marga, Sukadana Ham, Kec. Tj. Karang Bar., Kota Bandar Lampung, Lampung 35215','2025-06-04 14:21:12'),(2,'user','Febrina Auliza Azahra','febrina.auzahra@gmail.com','$2y$10$HvWmqAh19bUUmmIqnr/VyOhn7yO5fANBmFZoGsqrLTX.YxXmdV9ki','0895640026434','Kec. Kuta, Kabupaten Badung, Bali','2025-06-04 14:21:40'),(3,'user','Febrina Aulia Azahra2','febrn16@gmail.com','$2y$10$DexxZDbYQ.Dvm0ljREYzX.1Uv69JRavSP6tr3a6riW4wqVgCvcSeW','08960025480','komp doa','2025-06-05 12:23:14'),(4,'user','will dannn','codingbank26@gmail.com','$2y$10$1FdD314zpacVCO4Fk04QCOeIU/WX76RCnteBJ1UL1dKvWGJx0v5Eq','08960025480','komp doa','2025-06-12 09:04:45');
/*!40000 ALTER TABLE `anggota` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buku`
--

DROP TABLE IF EXISTS `buku`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buku` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(255) NOT NULL,
  `penerbit` varchar(255) NOT NULL,
  `tahun_terbit` varchar(255) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `deskripsi` text,
  `stok` int DEFAULT '0',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `isbn` (`isbn`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buku`
--

LOCK TABLES `buku` WRITE;
/*!40000 ALTER TABLE `buku` DISABLE KEYS */;
INSERT INTO `buku` VALUES (1,'Laskar Pelangi','Andrea Hirata','Bentang Pustaka','2005','9789793062793','Novel','Kisah perjuangan anak-anak di Belitung',11,'/project-web-teori/public/images/buku/9789793062793_laskar_pelangi_1749120799.png','2025-06-04 14:20:45'),(2,'Atomic Habits','James Clear','Penguin Random House','2018','9780735211292','Self-Development','Cara membentuk kebiasaan positif',2,'/project-web-teori/public/images/buku/9780735211292_1749126169.png','2025-06-04 14:20:45'),(3,'Bumi','Tere Liye','Gramedia','2014','9786020324783','Fiksi','Petualangan fantasi remaja bernama Raib',6,'/project-web-teori/public/images/buku/9786020324783_1749126388.png','2025-06-04 14:20:45');
/*!40000 ALTER TABLE `buku` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi`
--

DROP TABLE IF EXISTS `transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_anggota` int NOT NULL,
  `id_buku` int NOT NULL,
  `tanggal_peminjaman` date NOT NULL,
  `tanggal_pengembalian` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan') DEFAULT 'dipinjam',
  PRIMARY KEY (`id`),
  KEY `id_anggota` (`id_anggota`),
  KEY `id_buku` (`id_buku`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi`
--

LOCK TABLES `transaksi` WRITE;
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;
INSERT INTO `transaksi` VALUES (1,2,2,'2025-06-09','2025-06-28','dikembalikan'),(2,3,1,'2025-06-26','2025-07-05','dikembalikan'),(4,3,3,'2025-06-12',NULL,'dikembalikan'),(5,2,1,'2025-06-12','2025-06-12','dikembalikan'),(6,4,1,'2025-06-12',NULL,'dikembalikan'),(7,3,2,'2025-06-12','2025-06-12','dikembalikan'),(8,3,2,'2025-06-12',NULL,'dikembalikan'),(11,3,2,'2025-06-12',NULL,'dikembalikan'),(12,3,2,'2025-06-12',NULL,'dipinjam'),(13,3,2,'2025-06-12',NULL,'dipinjam'),(14,3,3,'2025-06-12',NULL,'dipinjam');
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `kurangi_stok_setelah_pinjam` AFTER INSERT ON `transaksi` FOR EACH ROW BEGIN
  IF NEW.status = 'dipinjam' THEN
    UPDATE buku
    SET stok = stok - 1
    WHERE id = NEW.id_buku;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tambah_stok_setelah_kembali` AFTER UPDATE ON `transaksi` FOR EACH ROW BEGIN
  IF OLD.status = 'dipinjam' AND NEW.status = 'dikembalikan' THEN
    UPDATE buku
    SET stok = stok + 1
    WHERE id = NEW.id_buku;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-12 16:59:05
