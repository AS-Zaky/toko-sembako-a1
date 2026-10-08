-- Toko Sembako A1 - Inventory Management System
-- PostgreSQL schema: five entities (users, kategori, produk, transaksi, detail_transaksi)

BEGIN;

CREATE TABLE IF NOT EXISTS users (
    id_user     INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        VARCHAR(10)  NOT NULL CHECK (role IN ('admin', 'penjaga')),
    aktif       BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS kategori (
    id_kategori   INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    deskripsi     TEXT
);

CREATE TABLE IF NOT EXISTS produk (
    id_produk     INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_kategori   INT NOT NULL REFERENCES kategori (id_kategori) ON UPDATE CASCADE ON DELETE RESTRICT,
    kode_produk   VARCHAR(50)  NOT NULL UNIQUE,
    nama_produk   VARCHAR(150) NOT NULL,
    satuan        VARCHAR(20)  NOT NULL,
    harga_beli    NUMERIC(12,2) NOT NULL DEFAULT 0 CHECK (harga_beli >= 0),
    harga_jual    NUMERIC(12,2) NOT NULL DEFAULT 0 CHECK (harga_jual >= 0),
    stok          NUMERIC(12,2) NOT NULL DEFAULT 0 CHECK (stok >= 0),
    stok_minimum  NUMERIC(12,2) NOT NULL DEFAULT 0 CHECK (stok_minimum >= 0)
);

CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi      INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_user           INT NOT NULL REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE RESTRICT,
    jenis_transaksi   VARCHAR(20) NOT NULL CHECK (jenis_transaksi IN
                          ('masuk', 'keluar_terjual', 'keluar_rusak', 'keluar_kedaluwarsa', 'penyesuaian')),
    tanggal_transaksi TIMESTAMP   NOT NULL DEFAULT NOW(),
    total_harga       NUMERIC(12,2) NOT NULL DEFAULT 0,
    supplier          VARCHAR(150),
    keterangan        TEXT
);

CREATE TABLE IF NOT EXISTS detail_transaksi (
    id_detail     INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_transaksi  INT NOT NULL REFERENCES transaksi (id_transaksi) ON UPDATE CASCADE ON DELETE CASCADE,
    id_produk     INT NOT NULL REFERENCES produk (id_produk) ON UPDATE CASCADE ON DELETE RESTRICT,
    jumlah        NUMERIC(12,2) NOT NULL,
    harga_satuan  NUMERIC(12,2) NOT NULL DEFAULT 0,
    subtotal      NUMERIC(12,2) NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_produk_nama       ON produk (nama_produk);
CREATE INDEX IF NOT EXISTS idx_produk_kode       ON produk (kode_produk);
CREATE INDEX IF NOT EXISTS idx_detail_transaksi  ON detail_transaksi (id_transaksi);
CREATE INDEX IF NOT EXISTS idx_detail_produk     ON detail_transaksi (id_produk);
CREATE INDEX IF NOT EXISTS idx_transaksi_tanggal ON transaksi (tanggal_transaksi);
CREATE INDEX IF NOT EXISTS idx_transaksi_jenis   ON transaksi (jenis_transaksi);

COMMIT;
