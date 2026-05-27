/**
 * SwiffySlider — Librería de carousel para libros
 * Recibe un contenedor con .swiffy-slider ya construido y lo anima.
 * Soporta: slide | fade | zoom | flip
 * Navegación: botones prev/next, thumbs, dots, swipe táctil, teclas ←→
 * Barra de progreso de descarga de imágenes antes de iniciar.
 */
class SwiffySlider {
    /**
     * @param {HTMLElement} container  El elemento .swiffy-slider
     * @param {object}      options
     *   items       {number}  slides visibles (1–4, default 3)
     *   effect      {string}  'slide'|'fade'|'zoom'|'flip' (default 'slide')
     *   autoplay    {number}  ms entre avances automáticos, 0=off (default 0)
     *   loop        {boolean} ciclo infinito (default true)
     */
    constructor(container, options = {}) {
        if (!(container instanceof HTMLElement)) {
            throw new Error('SwiffySlider: container debe ser un HTMLElement');
        }

        this._container  = container;
        this._opts = Object.assign({
            items:    3,
            effect:   'slide',
            autoplay: 0,
            loop:     true,
        }, options);

        this._currentIndex  = 0;
        this._autoplayTimer = null;
        this._touching      = false;
        this._touchStartX   = 0;
        this._touchStartY   = 0;
        this._effects       = ['slide', 'fade', 'zoom', 'flip'];

        this._init();
    }

    /* ─── Inicialización ─── */

    _init() {
        this._applyDataAttrs();
        this._cacheDOM();
        this._buildOverlay();
        this._buildEffectSwitcher();
        this._buildDots();
        this._buildThumbs();
        this._bindEvents();
        this._startImageLoader();
    }

    _applyDataAttrs() {
        this._container.dataset.items  = this._opts.items;
        this._container.dataset.effect = this._opts.effect;
    }

    _cacheDOM() {
        this._track  = this._container.querySelector('.swiffy-track');
        this._slides = Array.from(this._container.querySelectorAll('.swiffy-slide'));
        this._btnPrev = this._container.querySelector('.swiffy-btn-prev');
        this._btnNext = this._container.querySelector('.swiffy-btn-next');
        this._thumbsEl = this._container.querySelector('.swiffy-thumbs');
        this._dotsEl   = this._container.querySelector('.swiffy-dots');
        this._switcherEl = this._container.querySelector('.swiffy-effect-switcher');
    }

    /* ─── Overlay de progreso ─── */

    _buildOverlay() {
        const ov = document.createElement('div');
        ov.className = 'swiffy-loading-overlay';
        ov.innerHTML = `
            <div class="swiffy-progress-pct">0%</div>
            <div class="swiffy-progress-track">
                <div class="swiffy-progress-bar"></div>
            </div>
            <div class="swiffy-loading-text">Cargando imágenes…</div>
        `;
        this._track.prepend(ov);
        this._overlay   = ov;
        this._progBar   = ov.querySelector('.swiffy-progress-bar');
        this._progPct   = ov.querySelector('.swiffy-progress-pct');
    }

    _startImageLoader() {
        const imgs = Array.from(this._container.querySelectorAll('.swiffy-book-cover img'));
        if (imgs.length === 0) { this._onLoadComplete(); return; }

        let loaded = 0;
        const total = imgs.length;

        const tick = () => {
            loaded++;
            const pct = Math.round((loaded / total) * 100);
            this._progBar.style.width = pct + '%';
            this._progPct.textContent = pct + '%';
            if (loaded >= total) this._onLoadComplete();
        };

        imgs.forEach(img => {
            if (img.complete && img.naturalWidth > 0) {
                tick();
            } else {
                img.addEventListener('load',  tick, { once: true });
                img.addEventListener('error', tick, { once: true });
            }
        });
    }

    _onLoadComplete() {
        /* pequeña pausa para que el usuario vea el 100% */
        setTimeout(() => {
            this._overlay.classList.add('hidden');
            this._goTo(0, false);
            if (this._opts.autoplay > 0) this._startAutoplay();
        }, 380);
    }

    /* ─── Switcher de efectos ─── */

    _buildEffectSwitcher() {
        if (!this._switcherEl) return;
        this._switcherEl.innerHTML = '';
        this._effects.forEach(ef => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'swiffy-effect-btn' + (ef === this._opts.effect ? ' active' : '');
            btn.textContent = ef.charAt(0).toUpperCase() + ef.slice(1);
            btn.dataset.effect = ef;
            this._switcherEl.appendChild(btn);
        });
    }

    /* ─── Dots ─── */

    _buildDots() {
        if (!this._dotsEl) return;
        this._dotsEl.innerHTML = '';
        const count = this._slideCount();
        for (let i = 0; i < count; i++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'swiffy-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('aria-label', `Ir al slide ${i + 1}`);
            dot.dataset.index = i;
            this._dotsEl.appendChild(dot);
        }
    }

    _updateDots(index) {
        if (!this._dotsEl) return;
        this._dotsEl.querySelectorAll('.swiffy-dot').forEach((d, i) => {
            d.classList.toggle('active', i === index);
        });
    }

    /* ─── Thumbs ─── */

    _buildThumbs() {
        if (!this._thumbsEl) return;
        this._thumbsEl.innerHTML = '';
        this._slides.forEach((slide, i) => {
            const src = slide.querySelector('img')?.src || '';
            const alt = slide.querySelector('img')?.alt || `Slide ${i + 1}`;
            const thumb = document.createElement('div');
            thumb.className = 'swiffy-thumb' + (i === 0 ? ' active' : '');
            thumb.dataset.index = i;
            thumb.setAttribute('role', 'button');
            thumb.setAttribute('tabindex', '0');
            thumb.setAttribute('aria-label', `Ir al slide ${i + 1}`);
            const img = document.createElement('img');
            img.src = src;
            img.alt = alt;
            img.loading = 'lazy';
            thumb.appendChild(img);
            this._thumbsEl.appendChild(thumb);
        });
    }

    _updateThumbs(index) {
        if (!this._thumbsEl) return;
        const thumbs = this._thumbsEl.querySelectorAll('.swiffy-thumb');
        thumbs.forEach((t, i) => t.classList.toggle('active', i === index));
        /* scroll para que el thumb activo sea visible */
        if (thumbs[index]) {
            thumbs[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    /* ─── Navegación ─── */

    _slideCount() {
        const effect = this._container.dataset.effect;
        const isSingle = ['fade', 'zoom', 'flip'].includes(effect);
        if (isSingle) return this._slides.length;
        const visible = parseInt(this._container.dataset.items) || 1;
        return Math.max(1, this._slides.length - visible + 1);
    }

    _goTo(index, animate = true) {
        const count = this._slideCount();
        if (this._opts.loop) {
            index = ((index % count) + count) % count;
        } else {
            index = Math.max(0, Math.min(index, count - 1));
        }
        this._currentIndex = index;
        this._applyPosition(animate);
        this._updateDots(index);
        this._updateThumbs(index);
    }

    _applyPosition(animate) {
        const effect = this._container.dataset.effect;

        if (['fade', 'zoom', 'flip'].includes(effect)) {
            this._slides.forEach((s, i) => {
                s.classList.toggle('active', i === this._currentIndex);
            });
            return;
        }

        /* slide: mover el track */
        const visible = parseInt(this._container.dataset.items) || 1;
        const slideW  = 100 / visible; /* % por slide */
        const offset  = -(this._currentIndex * slideW);

        if (!animate) {
            this._track.style.transition = 'none';
            this._track.style.transform  = `translateX(${offset}%)`;
            /* forzar reflow */
            void this._track.offsetWidth;
            this._track.style.transition = '';
        } else {
            this._track.style.transform = `translateX(${offset}%)`;
        }
    }

    next() {
        this._goTo(this._currentIndex + 1);
        this._resetAutoplay();
    }

    prev() {
        this._goTo(this._currentIndex - 1);
        this._resetAutoplay();
    }

    /* ─── Autoplay ─── */

    _startAutoplay() {
        this._autoplayTimer = setInterval(() => this.next(), this._opts.autoplay);
    }

    _resetAutoplay() {
        if (this._opts.autoplay <= 0) return;
        clearInterval(this._autoplayTimer);
        this._startAutoplay();
    }

    /* ─── Cambio de efecto ─── */

    setEffect(effect) {
        if (!this._effects.includes(effect)) return;
        this._opts.effect = effect;
        this._container.dataset.effect = effect;

        /* limpiar clases active y transform residual del modo anterior */
        this._slides.forEach(s => s.classList.remove('active'));
        this._track.style.transform = '';

        this._buildDots();
        this._buildEffectSwitcher();
        this._goTo(0, false);
    }

    /* ─── Eventos ─── */

    _bindEvents() {
        /* botones prev/next */
        this._btnPrev?.addEventListener('click', () => this.prev());
        this._btnNext?.addEventListener('click', () => this.next());

        /* dots */
        this._dotsEl?.addEventListener('click', e => {
            const dot = e.target.closest('.swiffy-dot');
            if (dot) { this._goTo(+dot.dataset.index); this._resetAutoplay(); }
        });

        /* thumbs */
        this._thumbsEl?.addEventListener('click', e => {
            const thumb = e.target.closest('.swiffy-thumb');
            if (thumb) { this._goTo(+thumb.dataset.index); this._resetAutoplay(); }
        });

        /* thumbs: teclado accesible */
        this._thumbsEl?.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                const thumb = e.target.closest('.swiffy-thumb');
                if (thumb) { e.preventDefault(); this._goTo(+thumb.dataset.index); }
            }
        });

        /* switcher de efectos */
        this._switcherEl?.addEventListener('click', e => {
            const btn = e.target.closest('.swiffy-effect-btn');
            if (btn) this.setEffect(btn.dataset.effect);
        });

        /* teclas de flecha — solo cuando el foco está dentro del slider */
        this._container.setAttribute('tabindex', '0');
        this._container.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft')  { e.preventDefault(); this.prev(); }
            if (e.key === 'ArrowRight') { e.preventDefault(); this.next(); }
        });

        /* swipe táctil */
        this._container.addEventListener('touchstart', e => this._onTouchStart(e), { passive: true });
        this._container.addEventListener('touchmove',  e => this._onTouchMove(e),  { passive: true });
        this._container.addEventListener('touchend',   e => this._onTouchEnd(e),   { passive: true });

        /* swipe con mouse (drag) */
        this._container.addEventListener('mousedown', e => this._onMouseDown(e));
        this._container.addEventListener('mousemove', e => this._onMouseMove(e));
        this._container.addEventListener('mouseup',   e => this._onMouseUp(e));
        this._container.addEventListener('mouseleave',e => this._onMouseUp(e));

        /* responsive: recalcular posición si cambia el viewport */
        window.addEventListener('resize', () => {
            this._updateResponsiveItems();
            this._goTo(this._currentIndex, false);
        });
    }

    /* ─── Swipe táctil ─── */

    _onTouchStart(e) {
        this._touching    = true;
        this._touchStartX = e.touches[0].clientX;
        this._touchStartY = e.touches[0].clientY;
    }

    _onTouchMove(e) {
        /* noop — usamos touchend para decidir */
    }

    _onTouchEnd(e) {
        if (!this._touching) return;
        this._touching = false;
        const dx = e.changedTouches[0].clientX - this._touchStartX;
        const dy = e.changedTouches[0].clientY - this._touchStartY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 40) {
            dx < 0 ? this.next() : this.prev();
        }
    }

    /* ─── Swipe con mouse ─── */

    _onMouseDown(e) {
        this._touching    = true;
        this._touchStartX = e.clientX;
        this._touchStartY = e.clientY;
        this._container.style.cursor = 'grabbing';
    }

    _onMouseMove(e) {
        /* noop */
    }

    _onMouseUp(e) {
        if (!this._touching) return;
        this._touching = false;
        this._container.style.cursor = '';
        const dx = e.clientX - this._touchStartX;
        const dy = e.clientY - this._touchStartY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 40) {
            dx < 0 ? this.next() : this.prev();
        }
    }

    /* ─── Responsive items ─── */

    _updateResponsiveItems() {
        const w = window.innerWidth;
        const base = this._opts.items;
        let current;
        if (w <= 600)      current = 1;
        else if (w <= 900) current = Math.min(base, 2);
        else               current = base;

        this._container.dataset.items = current;
    }

    /* ─── API pública de conveniencia ─── */

    destroy() {
        clearInterval(this._autoplayTimer);
    }
}

/* ─── Auto-inicialización ─── */
/*
 * Uso declarativo: agrega data-swiffy al .swiffy-slider y el script
 * lo levanta solo al cargarse la página.
 * Ej: <div class="swiffy-slider" data-swiffy data-effect="fade" data-items="3">
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.swiffy-slider[data-swiffy]').forEach(el => {
        new SwiffySlider(el, {
            items:   parseInt(el.dataset.items  || '3'),
            effect:  el.dataset.effect  || 'slide',
            autoplay: parseInt(el.dataset.autoplay || '0'),
            loop:    el.dataset.loop !== 'false',
        });
    });
});
