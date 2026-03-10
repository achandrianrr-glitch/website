-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Mar 2026 pada 19.09
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `website_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(200) NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `merek_id` bigint(20) UNSIGNED DEFAULT NULL,
  `merek_manual` varchar(100) DEFAULT NULL,
  `lokasi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lokasi_manual` varchar(100) DEFAULT NULL,
  `tipe` enum('aset','stok') NOT NULL DEFAULT 'aset',
  `spesifikasi` text DEFAULT NULL,
  `tahun_pengadaan` year(4) DEFAULT NULL,
  `qty_total` smallint(5) UNSIGNED DEFAULT 0,
  `qty_tersedia` smallint(5) UNSIGNED DEFAULT 0,
  `qty_dipinjam` smallint(5) UNSIGNED DEFAULT 0,
  `qty_rusak` smallint(5) UNSIGNED DEFAULT 0,
  `qty_keluar` smallint(5) UNSIGNED DEFAULT 0,
  `kondisi_stok` tinyint(3) UNSIGNED DEFAULT 100,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `peminjaman_id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `unit_barang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `jumlah` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `status_item` enum('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam',
  `waktu_kembali` datetime DEFAULT NULL,
  `kondisi_kembali` tinyint(3) UNSIGNED DEFAULT NULL,
  `catatan_kembali` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'RPL 1', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(2, 'RPL 2', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(3, 'TKJ 1', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(4, 'TKJ 2', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(5, 'TEI', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(6, 'ANIMASI 1', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(7, 'ANIMASI 2', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(8, 'TSM 1', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(9, 'TSM 2', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(10, 'TSM 3', '2026-03-10 10:43:52', '2026-03-10 10:43:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Laptop', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(2, 'Komputer PC', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(3, 'Proyektor', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(4, 'Printer', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(5, 'Scanner', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(6, 'Kabel & Aksesori', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(7, 'Perangkat Jaringan', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(8, 'Kursi Lab', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(9, 'Meja Lab', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(10, 'Alat Tulis', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(11, 'Perlengkapan Listrik', NULL, '2026-03-10 10:43:52', '2026-03-10 10:43:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'X', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(2, 'XI', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(3, 'XII', '2026-03-10 10:43:52', '2026-03-10 10:43:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `lokasi`
--

CREATE TABLE `lokasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `lokasi`
--

INSERT INTO `lokasi` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Lab RPS 1', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(2, 'Lab RPS 2', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(3, 'Lab RPS 3', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(4, 'Kantor RPS', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(5, 'Gudang RPS', '2026-03-10 10:43:53', '2026-03-10 10:43:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `merek`
--

CREATE TABLE `merek` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `merek`
--

INSERT INTO `merek` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Asus', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(2, 'Acer', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(3, 'Dell', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(4, 'HP', '2026-03-10 10:43:52', '2026-03-10 10:43:52'),
(5, 'Lenovo', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(6, 'Apple', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(7, 'MSI', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(8, 'Canon', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(9, 'Epson', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(10, 'Brother', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(11, 'Samsung', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(12, 'LG', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(13, 'TP-Link', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(14, 'D-Link', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(15, 'Mikrotik', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(16, 'Logitech', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(17, 'Philips', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(18, 'Sony', '2026-03-10 10:43:53', '2026-03-10 10:43:53'),
(19, 'Toshiba', '2026-03-10 10:43:53', '2026-03-10 10:43:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_03_08_065041_create_sessions_table', 1),
(4, '2026_03_08_065107_create_pengguna_table', 1),
(5, '2026_03_08_065108_create_kategori_table', 1),
(6, '2026_03_08_065110_create_merek_table', 1),
(7, '2026_03_08_065111_create_lokasi_table', 1),
(8, '2026_03_08_065112_create_kelas_table', 1),
(9, '2026_03_08_065113_create_jurusan_table', 1),
(10, '2026_03_08_065114_create_barang_table', 1),
(11, '2026_03_08_065115_create_unit_barang_table', 1),
(12, '2026_03_08_065116_create_peminjaman_table', 1),
(13, '2026_03_08_065116_create_transaksi_table', 1),
(14, '2026_03_08_065117_create_detail_peminjaman_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_pinjam` varchar(20) NOT NULL,
  `nama_peminjam` varchar(150) NOT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `jurusan_id` bigint(20) UNSIGNED NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `mata_pelajaran` varchar(100) DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `waktu_pinjam` time NOT NULL,
  `status` enum('aktif','selesai') NOT NULL DEFAULT 'aktif',
  `catatan` text DEFAULT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$12$tMlfdeH30cfx0l2AiFaLtO9ja1Oe.NkvbQqVROFzWQpm5H./bNRrW', NULL, '2026-03-10 10:43:54', '2026-03-10 10:43:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Fgqd0aDKc9XBJm96KWzZIBRIJRpc7JlbZ1Tj5GGs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibnVxMXhqN0Nyb0lSTEN3dTFQTmh1TEY4d21nR1dIZEZOdzBJSkZ1SSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1773166062);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `unit_barang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `jumlah` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `alasan_keluar` enum('pindah_lokasi','dibuang','hibah','lainnya') DEFAULT NULL,
  `lokasi_tujuan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lokasi_tujuan_manual` varchar(100) DEFAULT NULL,
  `sumber_tujuan` varchar(200) DEFAULT NULL,
  `tanggal_transaksi` date NOT NULL,
  `kondisi_saat_itu` tinyint(3) UNSIGNED DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit_barang`
--

CREATE TABLE `unit_barang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barang_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_unit` varchar(50) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `kondisi` tinyint(3) UNSIGNED NOT NULL DEFAULT 100,
  `status` enum('tersedia','dipinjam','rusak','keluar') NOT NULL DEFAULT 'tersedia',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_merek_id_foreign` (`merek_id`),
  ADD KEY `barang_lokasi_id_foreign` (`lokasi_id`),
  ADD KEY `barang_kategori_id_index` (`kategori_id`),
  ADD KEY `barang_tipe_index` (`tipe`),
  ADD KEY `barang_aktif_index` (`aktif`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_peminjaman_unit_barang_id_foreign` (`unit_barang_id`),
  ADD KEY `detail_peminjaman_peminjaman_id_index` (`peminjaman_id`),
  ADD KEY `detail_peminjaman_barang_id_index` (`barang_id`),
  ADD KEY `detail_peminjaman_status_item_index` (`status_item`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `merek`
--
ALTER TABLE `merek`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `peminjaman_kode_pinjam_unique` (`kode_pinjam`),
  ADD KEY `peminjaman_kelas_id_foreign` (`kelas_id`),
  ADD KEY `peminjaman_jurusan_id_foreign` (`jurusan_id`),
  ADD KEY `peminjaman_pengguna_id_foreign` (`pengguna_id`),
  ADD KEY `peminjaman_status_index` (`status`),
  ADD KEY `peminjaman_tanggal_pinjam_index` (`tanggal_pinjam`),
  ADD KEY `peminjaman_kode_pinjam_index` (`kode_pinjam`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengguna_email_unique` (`email`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_unit_barang_id_foreign` (`unit_barang_id`),
  ADD KEY `transaksi_lokasi_tujuan_id_foreign` (`lokasi_tujuan_id`),
  ADD KEY `transaksi_pengguna_id_foreign` (`pengguna_id`),
  ADD KEY `transaksi_jenis_index` (`jenis`),
  ADD KEY `transaksi_tanggal_transaksi_index` (`tanggal_transaksi`),
  ADD KEY `transaksi_barang_id_index` (`barang_id`);

--
-- Indeks untuk tabel `unit_barang`
--
ALTER TABLE `unit_barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_barang_barang_id_nomor_unit_unique` (`barang_id`,`nomor_unit`),
  ADD KEY `unit_barang_status_index` (`status`),
  ADD KEY `unit_barang_barang_id_index` (`barang_id`),
  ADD KEY `unit_barang_kondisi_index` (`kondisi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `merek`
--
ALTER TABLE `merek`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `unit_barang`
--
ALTER TABLE `unit_barang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`),
  ADD CONSTRAINT `barang_lokasi_id_foreign` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `barang_merek_id_foreign` FOREIGN KEY (`merek_id`) REFERENCES `merek` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`),
  ADD CONSTRAINT `detail_peminjaman_peminjaman_id_foreign` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_unit_barang_id_foreign` FOREIGN KEY (`unit_barang_id`) REFERENCES `unit_barang` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_jurusan_id_foreign` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`),
  ADD CONSTRAINT `peminjaman_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`),
  ADD CONSTRAINT `peminjaman_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`),
  ADD CONSTRAINT `transaksi_lokasi_tujuan_id_foreign` FOREIGN KEY (`lokasi_tujuan_id`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaksi_pengguna_id_foreign` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `transaksi_unit_barang_id_foreign` FOREIGN KEY (`unit_barang_id`) REFERENCES `unit_barang` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `unit_barang`
--
ALTER TABLE `unit_barang`
  ADD CONSTRAINT `unit_barang_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
