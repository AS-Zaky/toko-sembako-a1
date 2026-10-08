-- Toko Sembako A1 - stock update trigger
-- Updates produk.stok on every detail_transaksi insert so stock and history never disagree.
-- Runs inside the same DB transaction as the movement; any failure rolls everything back.

CREATE OR REPLACE FUNCTION update_stok_on_detail()
RETURNS TRIGGER AS $$
DECLARE
    v_jenis VARCHAR(20);
    v_delta NUMERIC(12,2);
BEGIN
    SELECT jenis_transaksi INTO v_jenis
    FROM transaksi
    WHERE id_transaksi = NEW.id_transaksi;

    IF v_jenis = 'masuk' THEN
        v_delta := NEW.jumlah;
    ELSIF v_jenis IN ('keluar_terjual', 'keluar_rusak', 'keluar_kedaluwarsa') THEN
        v_delta := -NEW.jumlah;
    ELSIF v_jenis = 'penyesuaian' THEN
        -- jumlah is already signed (physical count minus system stock)
        v_delta := NEW.jumlah;
    ELSE
        RAISE EXCEPTION 'Jenis transaksi tidak dikenal: %', v_jenis;
    END IF;

    UPDATE produk
    SET stok = stok + v_delta
    WHERE id_produk = NEW.id_produk;

    -- The CHECK (stok >= 0) constraint rejects any update that would make stock negative.
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Produk tidak ditemukan: %', NEW.id_produk;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_update_stok ON detail_transaksi;
CREATE TRIGGER trg_update_stok
AFTER INSERT ON detail_transaksi
FOR EACH ROW
EXECUTE FUNCTION update_stok_on_detail();
