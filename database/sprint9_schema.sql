-- ====================================================================
-- SKEMA DATABASE SPRINT 9 (KREDIT & LEASING - APPROVAL & UANG MUKA)
-- ====================================================================
-- Penulis: Antigravity (Senior Backend Developer Expert)
-- Deskripsi: File SQL ini berisi query penyesuaian skema database untuk
--            tabel bahasa Inggris bawaan DBeaver Anda.
-- ====================================================================

-- 1. Menambahkan tipe file 'PK' (Perjanjian Kontrak) pada ENUM kolom file_type
--    di tabel `credit_documents` agar berkas kontrak digital bisa disimpan.
ALTER TABLE `credit_documents` 
MODIFY COLUMN `file_type` ENUM('KTP', 'KK', 'SlipGaji', 'PK') NOT NULL;

-- Catatan Penting untuk Farel:
-- - Tabel transaksi utama adalah `sales_transactions` dengan primary key `id`.
-- - Kolom status transaksi menggunakan ENUM('process', 'lunas', 'cancel').
-- - Status 'lunas' menandakan pembayaran/DP selesai dan siap masuk ke antrean serah terima.
-- - Tabel-tabel yang digunakan dalam modul Sprint 9 ini adalah:
--   1. `credit_applications` (Pengajuan Kredit)
--   2. `credit_decisions` (Keputusan Kredit dari Leasing)
--   3. `credit_documents` (Dokumen Persyaratan & Kontrak PK)
--   4. `down_payments` (Pembayaran Uang Muka / DP)
--   5. `sales_transactions` (Transaksi Utama Penjualan)
