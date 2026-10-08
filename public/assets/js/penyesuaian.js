// Toko Sembako A1 - stock adjustment: single-product autocomplete with current stock.
(function () {
    'use strict';

    var searchInput = document.getElementById('adj-search');
    var idInput = document.getElementById('adj-id');
    var suggestions = document.getElementById('adj-suggestions');
    var current = document.getElementById('adj-current');
    var form = document.querySelector('[data-penyesuaian-form]');
    if (!searchInput || !idInput || !suggestions || !form) return;

    var timer = null;

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function selectProduct(p) {
        idInput.value = p.id_produk;
        searchInput.value = p.nama_produk;
        current.textContent = p.stok + ' ' + p.satuan;
        suggestions.classList.remove('open');
    }

    function doSearch() {
        var q = searchInput.value.trim();
        idInput.value = '';
        current.textContent = '-';
        if (q === '') { suggestions.classList.remove('open'); return; }
        fetch('../search-produk.php?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (items) {
                if (!items.length) { suggestions.classList.remove('open'); return; }
                suggestions.innerHTML = items.map(function (p, i) {
                    return '<div class="item-suggestion" data-i="' + i + '">' +
                        escapeHtml(p.nama_produk) +
                        '<small>' + escapeHtml(p.kode_produk) + ' &middot; stok ' + p.stok + ' ' + escapeHtml(p.satuan) + '</small>' +
                    '</div>';
                }).join('');
                suggestions.classList.add('open');
                suggestions.querySelectorAll('.item-suggestion').forEach(function (el, i) {
                    el.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        selectProduct(items[i]);
                    });
                });
            })
            .catch(function () { suggestions.classList.remove('open'); });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(doSearch, 250);
    });
    searchInput.addEventListener('blur', function () {
        setTimeout(function () { suggestions.classList.remove('open'); }, 150);
    });

    form.addEventListener('submit', function (e) {
        if (idInput.value === '') {
            e.preventDefault();
            window.alert('Pilih barang dari daftar saran terlebih dahulu.');
        }
    });
})();
