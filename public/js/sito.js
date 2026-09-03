/* =========================================================
   Piccoli effetti del sito. Niente librerie: JavaScript puro.
   1) il prato dietro l'intestazione sfuma mentre si scorre
   2) gli elementi compaiono con calma quando entrano nello schermo
   ========================================================= */

(function () {
    'use strict';

    var animazioniRidotte = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- 1) il prato che sparisce ---------- */

    var prato = document.querySelector('.mm-prato');

    if (prato && !animazioniRidotte) {
        var altezza = prato.parentElement.offsetHeight || 600;
        var inAttesa = false;

        function aggiornaPrato() {
            var scorrimento = window.scrollY || window.pageYOffset;

            // da 1 (in cima) a 0 (quando si e' scesi di tutta l'intestazione)
            var quanto = 1 - scorrimento / (altezza * 0.85);
            if (quanto < 0) { quanto = 0; }
            if (quanto > 1) { quanto = 1; }

            prato.style.opacity = quanto;
            // si sposta appena piu' piano della pagina: effetto profondita'
            prato.style.transform = 'translateY(' + (scorrimento * 0.25) + 'px)';

            inAttesa = false;
        }

        function alloScorrimento() {
            if (!inAttesa) {
                inAttesa = true;
                window.requestAnimationFrame(aggiornaPrato);
            }
        }

        window.addEventListener('scroll', alloScorrimento, { passive: true });
        window.addEventListener('resize', function () {
            altezza = prato.parentElement.offsetHeight || 600;
            aggiornaPrato();
        });

        aggiornaPrato();
    }

    /* ---------- 2) comparsa morbida ---------- */

    var daAnimare = document.querySelectorAll('.mm-anim');

    if (!daAnimare.length) {
        return;
    }

    if (animazioniRidotte || !('IntersectionObserver' in window)) {
        // Nessuna animazione: si mostra tutto subito.
        Array.prototype.forEach.call(daAnimare, function (el) {
            el.classList.add('visibile');
        });
        return;
    }

    var osservatore = new IntersectionObserver(function (voci) {
        voci.forEach(function (voce) {
            if (voce.isIntersecting) {
                voce.target.classList.add('visibile');
                osservatore.unobserve(voce.target);
            }
        });
    }, { rootMargin: '0px 0px -60px 0px', threshold: 0.08 });

    Array.prototype.forEach.call(daAnimare, function (el, i) {
        // ritardo a scalare: gli elementi affiancati non compaiono tutti insieme
        el.style.transitionDelay = ((i % 4) * 90) + 'ms';
        osservatore.observe(el);
    });
})();
