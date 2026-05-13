{{--
    Shared UI behaviors untuk halaman admin/umkm list:
      1. Click-toggle action dropdowns (.dropdown / .action-btn)
         - Klik action button → toggle dropdown
         - Klik di luar          → close
         - Buka dropdown lain    → dropdown sebelumnya otomatis close
      2. Row navigasi (.admin-table tr[data-row-href])
         - Klik di mana saja di row → navigate ke data-row-href
         - Tapi BUKAN ketika user klik di dalam .dropdown / <a> / <button> / <form>
         - Keyboard accessible: Enter/Space pada row yang fokus juga navigate
--}}
@once
<script>
(function () {
    'use strict';

    // ── 1. DROPDOWN TOGGLE ─────────────────────────────────────────
    function closeAllDropdowns(except) {
        document.querySelectorAll('.dropdown.is-open').forEach(function (d) {
            if (d !== except) d.classList.remove('is-open');
        });
    }

    document.addEventListener('click', function (e) {
        // Klik tombol action → toggle dropdown induknya
        var btn = e.target.closest('.action-btn');
        if (btn) {
            var dropdown = btn.closest('.dropdown');
            if (dropdown) {
                e.preventDefault();
                e.stopPropagation();
                var willOpen = !dropdown.classList.contains('is-open');
                closeAllDropdowns(dropdown);
                dropdown.classList.toggle('is-open', willOpen);
                return;
            }
        }

        // Klik di dalam isi dropdown → biarkan (link/form berjalan normal)
        if (e.target.closest('.dropdown-content')) return;

        // Klik di luar semua dropdown → tutup
        closeAllDropdowns(null);
    });

    // Esc → tutup semua dropdown
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAllDropdowns(null);
    });

    // ── 2. ROW NAVIGATION ──────────────────────────────────────────
    // Selector "ignore": klik di dalam elemen ini TIDAK navigate.
    var IGNORE_SELECTOR = 'a, button, input, textarea, select, label, form, .dropdown, .dropdown-content';

    function rowFromEvent(e) {
        var row = e.target.closest('tr[data-row-href]');
        if (!row) return null;
        if (e.target.closest(IGNORE_SELECTOR)) return null;
        return row;
    }

    document.addEventListener('click', function (e) {
        var row = rowFromEvent(e);
        if (!row) return;
        var href = row.getAttribute('data-row-href');
        if (!href) return;
        // Middle-click / Ctrl+click → buka new tab (sama seperti <a>)
        if (e.button === 1 || e.metaKey || e.ctrlKey) {
            window.open(href, '_blank', 'noopener');
        } else {
            window.location.href = href;
        }
    });

    // Auxiliary click (middle button) — tidak men-trigger 'click' di semua browser
    document.addEventListener('auxclick', function (e) {
        if (e.button !== 1) return;
        var row = rowFromEvent(e);
        if (!row) return;
        var href = row.getAttribute('data-row-href');
        if (href) window.open(href, '_blank', 'noopener');
    });

    // Keyboard accessibility: row dengan tabindex="0" bisa di-Enter/Space
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        var row = e.target.closest('tr[data-row-href]');
        if (!row || row !== e.target) return;       // hanya kalau row itu sendiri yg fokus
        if (e.target.closest(IGNORE_SELECTOR)) return;
        e.preventDefault();
        var href = row.getAttribute('data-row-href');
        if (href) window.location.href = href;
    });
}());
</script>
@endonce
