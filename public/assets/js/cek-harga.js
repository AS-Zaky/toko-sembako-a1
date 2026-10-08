// Toko Sembako A1 - live price & stock lookup (cek-harga.php).
(function () {
    'use strict';

    var input = document.getElementById('lookup');
    var results = document.getElementById('lookup-results');
    if (!input || !results) return;

    var timer = null;

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function statusClass(status) {
        if (status === 'Tersedia') return 'status-tersedia';
        if (status === 'Menipis') return 'status-menipis';
        return 'status-habis';
    }

    function render(items) {
        if (!items.length) {
            results.innerHTML = '<p class="empty-state">Barang tidak ditemukan.</p>';
            return;
        }
        results.innerHTML = items.map(function (p) {
            return '<div class="lookup-card">' +
                '<div>' +
                    '<div class="lookup-card-name">' + escapeHtml(p.nama_produk) + '</div>' +
                    '<div class="lookup-card-meta">' + escapeHtml(p.kode_produk) + ' &middot; ' + escapeHtml(p.nama_kategori) + '</div>' +
                '</div>' +
                '<div class="lookup-card-right">' +
                    '<div class="lookup-card-price">' + escapeHtml(p.harga_jual_format) + '</div>' +
                    '<div class="lookup-card-meta">Stok: ' + p.stok + ' ' + escapeHtml(p.satuan) + '</div>' +
                    '<span class="catalog-card-status ' + statusClass(p.status) + '">' + escapeHtml(p.status) + '</span>' +
                '</div>' +
            '</div>';
        }).join('');
    }

    function search() {
        var q = input.value.trim();
        if (q === '') {
            results.innerHTML = '<p class="empty-state">Mulai mengetik untuk mencari.</p>';
            return;
        }
        fetch('search-produk.php?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(render)
            .catch(function () {
                results.innerHTML = '<p class="empty-state">Gagal memuat hasil.</p>';
            });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(search, 250);
    });
})();
