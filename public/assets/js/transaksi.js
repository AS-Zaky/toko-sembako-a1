// Toko Sembako A1 - goods in/out forms: dynamic rows with product autocomplete.
(function () {
    'use strict';

    var form = document.querySelector('[data-transaksi-form]');
    if (!form) return;

    var list = form.querySelector('[data-item-list]');
    var addBtn = form.querySelector('[data-add-row]');
    var mode = form.getAttribute('data-mode') || 'masuk';

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function closeAllSuggestions() {
        list.querySelectorAll('.item-suggestions').forEach(function (s) { s.classList.remove('open'); });
    }

    function bindRow(row) {
        var searchInput = row.querySelector('.item-search');
        var idInput = row.querySelector('.item-id');
        var suggestions = row.querySelector('.item-suggestions');
        var priceInput = row.querySelector('.item-price-input');
        var stockDisplay = row.querySelector('.item-stock-display');
        var timer = null;

        function selectProduct(p) {
            idInput.value = p.id_produk;
            searchInput.value = p.nama_produk;
            suggestions.classList.remove('open');
            if (priceInput) priceInput.value = p.harga_jual; // for goods-in the attendant can override with buy price
            if (stockDisplay) stockDisplay.textContent = p.stok + ' ' + p.satuan;
            searchInput.dataset.selected = '1';
        }

        function doSearch() {
            var q = searchInput.value.trim();
            searchInput.dataset.selected = '';
            idInput.value = '';
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
        searchInput.addEventListener('focus', function () {
            if (suggestions.innerHTML !== '') suggestions.classList.add('open');
        });
        searchInput.addEventListener('blur', function () {
            setTimeout(function () { suggestions.classList.remove('open'); }, 150);
        });

        var removeBtn = row.querySelector('[data-remove-row]');
        removeBtn.addEventListener('click', function () {
            var rows = list.querySelectorAll('[data-item-row]');
            if (rows.length > 1) {
                row.remove();
            } else {
                searchInput.value = '';
                idInput.value = '';
                if (priceInput) priceInput.value = '';
                if (stockDisplay) stockDisplay.textContent = '-';
                row.querySelector('.item-qty-input').value = '';
            }
        });
    }

    list.querySelectorAll('[data-item-row]').forEach(bindRow);

    addBtn.addEventListener('click', function () {
        var first = list.querySelector('[data-item-row]');
        var clone = first.cloneNode(true);
        clone.querySelectorAll('input').forEach(function (input) { input.value = ''; });
        var stockDisplay = clone.querySelector('.item-stock-display');
        if (stockDisplay) stockDisplay.textContent = '-';
        var suggestions = clone.querySelector('.item-suggestions');
        if (suggestions) { suggestions.innerHTML = ''; suggestions.classList.remove('open'); }
        list.appendChild(clone);
        bindRow(clone);
        clone.querySelector('.item-search').focus();
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.item-product')) closeAllSuggestions();
    });

    // Prevent submit while a row has text but no product chosen.
    form.addEventListener('submit', function (e) {
        var invalid = false;
        list.querySelectorAll('[data-item-row]').forEach(function (row) {
            var searchInput = row.querySelector('.item-search');
            var idInput = row.querySelector('.item-id');
            if (searchInput.value.trim() !== '' && idInput.value === '') invalid = true;
        });
        if (invalid) {
            e.preventDefault();
            window.alert('Pilih barang dari daftar saran untuk setiap baris.');
        }
    });
})();
