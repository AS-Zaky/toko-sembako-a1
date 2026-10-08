-- Toko Sembako A1 - seed data
-- First admin account, default categories, and sample products.
-- Default admin login: username "admin", password "admin123" (change after first login).

BEGIN;

INSERT INTO users (nama, username, password, role)
VALUES ('Administrator', 'admin', '$2y$10$e0MYzXz5q1m0Qy5y0e6ZkeJm0n5Q0mQy0e6ZkeJm0n5Q0mQy0e6Zk', 'admin')
ON CONFLICT (username) DO NOTHING;

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
    ('Beras & Tepung', 'Beras, tepung, dan bahan pokok kering'),
    ('Minyak & Gula',  'Minyak goreng, gula pasir, dan pemanis'),
    ('Mie & Bumbu',    'Mie instan dan bumbu dapur'),
    ('Minuman',        'Kopi, teh, dan minuman kemasan'),
    ('Lainnya',        'Kebutuhan rumah tangga lainnya')
ON CONFLICT (nama_kategori) DO NOTHING;

INSERT INTO produk (id_kategori, kode_produk, nama_produk, satuan, harga_beli, harga_jual, stok, stok_minimum)
SELECT k.id_kategori, v.kode, v.nama, v.satuan, v.harga_beli, v.harga_jual, v.stok, v.stok_minimum
FROM (VALUES
    ('Beras & Tepung', 'BR001', 'Beras Medium 5kg',        'karung', 55000, 60000, 10, 3),
    ('Beras & Tepung', 'TP001', 'Tepung Terigu 1kg',       'bungkus', 9000, 11000, 20, 5),
    ('Minyak & Gula',  'MY001', 'Minyak Goreng 2L',        'botol',  38000, 42000, 15, 5),
    ('Minyak & Gula',  'GL001', 'Gula Pasir 1kg',          'bungkus', 16000, 18000, 25, 8),
    ('Mie & Bumbu',    'MI001', 'Mie Instan Goreng',       'pcs',    3000,  3500,  100, 24),
    ('Minuman',        'KP001', 'Kopi Bubuk 100g',         'sachet', 8000,  10000, 30, 10),
    ('Minuman',        'TH001', 'Teh Celup 25s',           'kotak',  7000,  9000,  18, 6),
    ('Lainnya',        'SB001', 'Sabun Cuci Piring 800ml', 'botol',  22000, 26000, 12, 4)
) AS v(kategori, kode, nama, satuan, harga_beli, harga_jual, stok, stok_minimum)
JOIN kategori k ON k.nama_kategori = v.kategori
ON CONFLICT (kode_produk) DO NOTHING;

COMMIT;
