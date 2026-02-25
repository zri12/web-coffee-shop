{{-- ============================================================
     MATERIAL SYMBOLS ICON GUARD
     Mencegah dua masalah:
     1. Teks icon muncul saat font belum load (FOUC)
     2. Chrome Auto-Translate menerjemahkan nama icon ke Bahasa lain
     ============================================================ --}}
<style>
    /*
     * Sembunyikan teks fallback icon Material Symbols dengan color:transparent.
     * Tidak menggunakan visibility/width:0 agar font tetap "in use" dan
     * document.fonts.load() dapat mendeteksinya dengan benar.
     */
    .material-symbols-outlined {
        color: transparent !important;
        -webkit-user-select: none !important;
        user-select: none !important;
        /* Pertahankan ukuran agar tidak ada layout shift saat font siap */
        display: inline-block !important;
        font-style: normal !important;
    }
    html.fonts-loaded .material-symbols-outlined {
        color: inherit !important;
        -webkit-user-select: none !important;
        user-select: none !important;
    }
</style>
<script>
(function () {
    'use strict';

    /**
     * Tandai satu elemen icon agar TIDAK diterjemahkan oleh browser.
     * Chrome Auto-Translate dan Google Translate menghormati:
     *   - attribute translate="no"
     *   - class "notranslate"
     */
    function protectIcon(el) {
        el.setAttribute('translate', 'no');
        el.classList.add('notranslate');
    }

    /** Terapkan ke semua icon yang sudah ada di DOM */
    function protectAll() {
        document.querySelectorAll('.material-symbols-outlined').forEach(protectIcon);
    }

    // Jalankan segera (untuk icon di atas inline dalam <head>)
    protectAll();

    // Jalankan lagi setelah DOM selesai dibangun
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', protectAll);
    }

    // MutationObserver: tangkap icon yang ditambahkan secara dinamis
    // (toast, modal, order cards yang di-inject via JS/AJAX)
    if (typeof MutationObserver !== 'undefined') {
        new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var nodes = mutations[i].addedNodes;
                for (var j = 0; j < nodes.length; j++) {
                    var n = nodes[j];
                    if (!n || n.nodeType !== 1) continue;
                    if (n.classList && n.classList.contains('material-symbols-outlined')) {
                        protectIcon(n);
                    }
                    if (n.querySelectorAll) {
                        n.querySelectorAll('.material-symbols-outlined').forEach(protectIcon);
                    }
                }
            }
        }).observe(document.documentElement, { childList: true, subtree: true });
    }

    /*
     * FONT LOADING DETECTION
     * Gunakan document.fonts.load() bukan document.fonts.ready.
     * Alasan: document.fonts.ready bisa resolve sebelum font benar-benar
     * siap jika karakter belum di-render.
     * document.fonts.load() secara eksplisit meminta font diunduh.
     */
    var _fb = setTimeout(function () {
        document.documentElement.classList.add('fonts-loaded');
    }, 3000);

    var tryLoad = function () {
        if (document.fonts && document.fonts.load) {
            document.fonts.load("1em 'Material Symbols Outlined'")
                .then(function () {
                    clearTimeout(_fb);
                    document.documentElement.classList.add('fonts-loaded');
                })
                .catch(function () {
                    clearTimeout(_fb);
                    document.documentElement.classList.add('fonts-loaded');
                });
        } else {
            clearTimeout(_fb);
            document.documentElement.classList.add('fonts-loaded');
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryLoad);
    } else {
        tryLoad();
    }
}());
</script>
