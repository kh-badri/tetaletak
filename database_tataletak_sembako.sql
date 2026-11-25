-- =====================================================================
-- SKRIP SQL STRUKTUR TABEL & DATA AWAL TOKO SEMBAKO (ALGORITMA APRIORI)
-- Database: tataletak_db
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `tataletak_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tataletak_db`;

-- ---------------------------------------------------------------------
-- 1. TABEL MASTER PRODUK SEMBAKO (60 PRODUK)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `produk`;
CREATE TABLE `produk` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` TEXT NOT NULL,
  `kategori_produk` TEXT NOT NULL,
  `harga_satuan` INT(11) NOT NULL,
  `satuan` VARCHAR(50) DEFAULT 'pcs',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_kategori` (`kategori_produk`),
  INDEX `idx_nama` (`nama_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. TABEL DATASET TRANSAKSI PEMBELIAN (MARKET BASKET APRIORI)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dataset_produk`;
CREATE TABLE `dataset_produk` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `no_transaksi` VARCHAR(50) NOT NULL,
  `waktu_penjualan` DATE NOT NULL,
  `id_produk` INT(11) NULL,
  `nama_produk` TEXT NOT NULL,
  `kategori_produk` TEXT NOT NULL,
  `harga_produk_rp` INT(11) NOT NULL,
  `jumlah_terjual_unit` INT(11) NOT NULL,
  `total_harga` INT(11) NOT NULL,
  `status_promosi` VARCHAR(10) NULL DEFAULT '',
  `terjadi_penurunan` VARCHAR(10) NULL DEFAULT '',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_no_transaksi` (`no_transaksi`),
  INDEX `idx_waktu` (`waktu_penjualan`),
  INDEX `idx_nama_produk` (`nama_produk`(150))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TABEL RIWAYAT ANALISIS APRIORI
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `history_analisis` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tanggal_analisis` DATETIME NOT NULL,
  `accuracy` DECIMAL(5,2) NULL,
  `total_data` INT(11) NOT NULL,
  `hasil_analisis` LONGTEXT NOT NULL,
  `hasil_evaluasi` LONGTEXT NOT NULL,
  `hasil_prediksi` LONGTEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

