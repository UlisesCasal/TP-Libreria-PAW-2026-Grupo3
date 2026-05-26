/**
 * Carousel — librería de slides con carga progresiva
 *
 * Uso:
 *   new Carousel(containerElement [, options])
 *
 * Opciones:
 *   effect     : 'slide' | 'fade' | 'zoom'   (default: 'slide')
 *   autoplay   : boolean                       (default: true)
 *   interval   : ms entre slides              (default: 4000)
 *   fullscreen : boolean — ocupa 100vw/100vh  (default: false)
 *
 * El contenedor debe contener <img> y tener un alto explícito en CSS.
 * En modo fullscreen el alto lo pone la librería (position:fixed inset:0).
 */

(function (global) {
  'use strict';

  const SVG_PREV = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="15 18 9 12 15 6"/>
  </svg>`;

  const SVG_NEXT = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="9 18 15 12 9 6"/>
  </svg>`;

  const EFFECTS = ['slide', 'fade', 'zoom'];

  /* ================================================================
     Constructor
  ================================================================ */
  function Carousel(container, options) {
    if (!(container instanceof HTMLElement)) {
      throw new Error('Carousel: el primer argumento debe ser un HTMLElement.');
    }

    this._container     = container;
    this._opts          = Object.assign({
      effect    : 'slide',
      autoplay  : true,
      interval  : 4000,
      fullscreen: false,
    }, options || {});

    this._images        = [];
    this._index         = 0;
    this._total         = 0;
    this._transitioning = false;
    this._autoplayTimer = null;
    this._effect        = EFFECTS.includes(this._opts.effect) ? this._opts.effect : 'slide';
    this._touchStartX   = 0;
    this._touchDeltaX   = 0;

    this._init();
  }

  /* ────────────────────────────────────────────────────────────────
     1. Init
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._init = function () {
    const imgs = Array.from(this._container.querySelectorAll('img'));
    if (imgs.length === 0) {
      console.warn('Carousel: no se encontraron imágenes en el contenedor.');
      return;
    }

    this._images = imgs.map(function (img) {
      return { src: img.src, alt: img.alt || '' };
    });
    this._total = this._images.length;

    this._buildDOM();
    this._bindEvents();
    this._loadImages();
  };

  /* ────────────────────────────────────────────────────────────────
     2. DOM
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._buildDOM = function () {
    const root = this._container;
    root.innerHTML = '';
    root.classList.add('carousel-root');
    root.setAttribute('data-effect', this._effect);
    root.setAttribute('role', 'region');
    root.setAttribute('aria-label', 'Carousel de imágenes');
    root.setAttribute('tabindex', '0');
    if (this._opts.fullscreen) root.classList.add('carousel-fullscreen');

    /* Overlay de carga */
    const overlay = document.createElement('div');
    overlay.className = 'carousel-load-overlay';
    overlay.innerHTML = `
      <span class="carousel-load-label">Cargando imágenes…</span>
      <div class="carousel-load-bar-track">
        <div class="carousel-load-bar-fill"></div>
      </div>
      <span class="carousel-load-percent">0%</span>
    `;
    root.appendChild(overlay);
    this._overlay     = overlay;
    this._loadFill    = overlay.querySelector('.carousel-load-bar-fill');
    this._loadPercent = overlay.querySelector('.carousel-load-percent');

    /* Stage — ocupa todo el alto disponible (flex:1 en CSS) */
    const stage = document.createElement('div');
    stage.className = 'carousel-stage';
    root.appendChild(stage);
    this._stage = stage;

    /* Slides — todas position:absolute, alto controlado solo por opacity */
    this._slides = this._images.map(function (imgData, i) {
      const slide = document.createElement('div');
      slide.className = 'carousel-slide' + (i === 0 ? ' carousel-slide-active' : '');
      slide.setAttribute('role', 'group');
      slide.setAttribute('aria-label', 'Imagen ' + (i + 1) + ' de ' + this._total);
      slide.setAttribute('aria-hidden', i !== 0 ? 'true' : 'false');

      const img = document.createElement('img');
      img.src = imgData.src;
      img.alt = imgData.alt;
      img.draggable = false;
      slide.appendChild(img);
      stage.appendChild(slide);
      return slide;
    }, this);

    /* Contador */
    const counter = document.createElement('div');
    counter.className = 'carousel-counter';
    counter.setAttribute('aria-live', 'polite');
    counter.textContent = '1 / ' + this._total;
    root.appendChild(counter);
    this._counter = counter;

    /* Prev / Next */
    const btnPrev = document.createElement('button');
    btnPrev.className = 'carousel-btn carousel-btn-prev';
    btnPrev.setAttribute('aria-label', 'Imagen anterior');
    btnPrev.innerHTML = SVG_PREV;
    root.appendChild(btnPrev);
    this._btnPrev = btnPrev;

    const btnNext = document.createElement('button');
    btnNext.className = 'carousel-btn carousel-btn-next';
    btnNext.setAttribute('aria-label', 'Imagen siguiente');
    btnNext.innerHTML = SVG_NEXT;
    root.appendChild(btnNext);
    this._btnNext = btnNext;

    /* Selector de efectos */
    const effectSel = document.createElement('div');
    effectSel.className = 'carousel-effect-selector';
    effectSel.setAttribute('role', 'group');
    effectSel.setAttribute('aria-label', 'Seleccionar efecto');
    EFFECTS.forEach(function (eff) {
      const btn = document.createElement('button');
      btn.className = 'carousel-effect-btn';
      btn.textContent = eff;
      btn.setAttribute('aria-pressed', eff === this._effect ? 'true' : 'false');
      btn.dataset.effect = eff;
      effectSel.appendChild(btn);
    }, this);
    root.appendChild(effectSel);
    this._effectSel = effectSel;

    /* Dots */
    const dotsWrap = document.createElement('div');
    dotsWrap.className = 'carousel-dots';
    dotsWrap.setAttribute('role', 'tablist');
    this._dots = this._images.map(function (_, i) {
      const dot = document.createElement('button');
      dot.className = 'carousel-dot' + (i === 0 ? ' carousel-dot-active' : '');
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-label', 'Ir a imagen ' + (i + 1));
      dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
      dotsWrap.appendChild(dot);
      return dot;
    }, this);
    root.appendChild(dotsWrap);

    /* Thumbnails — fuera del stage, al final del flex column */
    const thumbsWrap = document.createElement('div');
    thumbsWrap.className = 'carousel-thumbs';
    thumbsWrap.setAttribute('role', 'list');
    this._thumbs = this._images.map(function (imgData, i) {
      const wrap = document.createElement('div');
      wrap.className = 'carousel-thumb' + (i === 0 ? ' carousel-thumb-active' : '');
      wrap.setAttribute('role', 'listitem');
      wrap.setAttribute('tabindex', '0');
      wrap.setAttribute('aria-label', 'Imagen ' + (i + 1));
      const tImg = document.createElement('img');
      tImg.src = imgData.src;
      tImg.alt = imgData.alt;
      tImg.draggable = false;
      wrap.appendChild(tImg);
      thumbsWrap.appendChild(wrap);
      return wrap;
    }, this);
    root.appendChild(thumbsWrap);
    this._thumbsWrap = thumbsWrap;
  };

  /* ────────────────────────────────────────────────────────────────
     3. Eventos
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._bindEvents = function () {
    const self = this;

    this._btnPrev.addEventListener('click', function () { self.prev(); });
    this._btnNext.addEventListener('click', function () { self.next(); });

    this._dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () { self.goTo(i); });
    });

    this._thumbs.forEach(function (thumb, i) {
      thumb.addEventListener('click', function () { self.goTo(i); });
      thumb.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); self.goTo(i); }
      });
    });

    /* Flechas del teclado */
    this._container.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft')  { e.preventDefault(); self.prev(); }
      if (e.key === 'ArrowRight') { e.preventDefault(); self.next(); }
    });

    /* Touch swipe */
    this._stage.addEventListener('touchstart', function (e) {
      self._touchStartX = e.touches[0].clientX;
      self._touchDeltaX = 0;
    }, { passive: true });

    this._stage.addEventListener('touchmove', function (e) {
      self._touchDeltaX = e.touches[0].clientX - self._touchStartX;
    }, { passive: true });

    this._stage.addEventListener('touchend', function () {
      if (Math.abs(self._touchDeltaX) > 40) {
        self._touchDeltaX < 0 ? self.next() : self.prev();
      }
    });

    /* Mouse drag */
    let mouseDown = false, mouseStartX = 0;
    this._stage.addEventListener('mousedown', function (e) {
      mouseDown = true;
      mouseStartX = e.clientX;
    });
    document.addEventListener('mouseup', function (e) {
      if (!mouseDown) return;
      mouseDown = false;
      const delta = e.clientX - mouseStartX;
      if (Math.abs(delta) > 50) { delta < 0 ? self.next() : self.prev(); }
    });

    /* Pausa al hover */
    this._container.addEventListener('mouseenter', function () { self._pauseAutoplay(); });
    this._container.addEventListener('mouseleave', function () { self._resumeAutoplay(); });

    /* Selector de efecto */
    this._effectSel.addEventListener('click', function (e) {
      const btn = e.target.closest('.carousel-effect-btn');
      if (btn) self._setEffect(btn.dataset.effect);
    });
  };

  /* ────────────────────────────────────────────────────────────────
     4. Carga con barra de progreso
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._loadImages = function () {
    const self = this;
    const total = this._images.length;
    let loaded = 0;

    function onLoad() {
      loaded++;
      const pct = Math.round((loaded / total) * 100);
      self._loadFill.style.width = pct + '%';
      self._loadPercent.textContent = pct + '%';

      if (loaded === total) {
        setTimeout(function () {
          self._overlay.classList.add('carousel-load-hidden');
          setTimeout(function () {
            self._overlay.style.display = 'none';
            self._startAutoplay();
          }, 500);
        }, 300);
      }
    }

    this._images.forEach(function (imgData) {
      const probe = new Image();
      probe.onload  = onLoad;
      probe.onerror = onLoad;
      probe.src     = imgData.src;
    });
  };

  /* ────────────────────────────────────────────────────────────────
     5. Navegación
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype.goTo = function (newIndex, direction) {
    if (this._transitioning || newIndex === this._index) return;

    const oldIndex = this._index;
    const dir = direction !== undefined ? direction : (newIndex > oldIndex ? 'next' : 'prev');

    this._transitioning = true;
    this._index = newIndex;

    this._applyTransition(this._slides[oldIndex], this._slides[newIndex], dir);
    this._updateUI(newIndex);

    setTimeout(function () {
      this._transitioning = false;
    }.bind(this), this._getTransitionDuration() + 50);
  };

  Carousel.prototype.next = function () {
    this.goTo((this._index + 1) % this._total, 'next');
    this._resetAutoplay();
  };

  Carousel.prototype.prev = function () {
    this.goTo((this._index - 1 + this._total) % this._total, 'prev');
    this._resetAutoplay();
  };

  /* ────────────────────────────────────────────────────────────────
     6. Transiciones
     Todas las slides son siempre position:absolute.
     Solo se agregan/quitan clases — nunca se toca style.position.
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._applyTransition = function (oldSlide, newSlide, dir) {
    const eff = this._effect;
    const dur = this._getTransitionDuration();

    /* Limpiar todo */
    this._slides.forEach(function (s) {
      s.className = 'carousel-slide';
      s.setAttribute('aria-hidden', 'true');
    });

    if (eff === 'fade') {
      /* Vieja queda visible un instante mientras nueva aparece */
      oldSlide.classList.add('carousel-slide-active');
      void oldSlide.offsetWidth;
      newSlide.classList.add('carousel-slide-active');
      newSlide.setAttribute('aria-hidden', 'false');
      setTimeout(function () {
        oldSlide.classList.remove('carousel-slide-active');
      }, dur);

    } else if (eff === 'slide') {
      const enterClass = dir === 'next' ? 'carousel-enter-next' : 'carousel-enter-prev';
      const leaveClass = dir === 'next' ? 'carousel-leave-next' : 'carousel-leave-prev';

      /* Nueva slide empieza fuera */
      newSlide.classList.add(enterClass);
      void newSlide.offsetWidth; /* forzar reflow */

      /* Activar ambas: la vieja sale, la nueva entra */
      oldSlide.classList.add('carousel-slide-active', leaveClass);
      newSlide.classList.remove(enterClass);
      newSlide.classList.add('carousel-slide-active');
      newSlide.setAttribute('aria-hidden', 'false');

      setTimeout(function () {
        oldSlide.classList.remove('carousel-slide-active', leaveClass);
      }, dur);

    } else if (eff === 'zoom') {
      newSlide.classList.add('carousel-enter-zoom');
      void newSlide.offsetWidth;

      oldSlide.classList.add('carousel-slide-active', 'carousel-leave-zoom');
      newSlide.classList.remove('carousel-enter-zoom');
      newSlide.classList.add('carousel-slide-active');
      newSlide.setAttribute('aria-hidden', 'false');

      setTimeout(function () {
        oldSlide.classList.remove('carousel-slide-active', 'carousel-leave-zoom');
      }, dur);
    }
  };

  Carousel.prototype._getTransitionDuration = function () {
    const raw = getComputedStyle(this._container)
      .getPropertyValue('--carousel-transition-duration').trim();
    if (!raw) return 700;
    if (raw.endsWith('ms')) return parseInt(raw, 10);
    if (raw.endsWith('s'))  return parseFloat(raw) * 1000;
    return 700;
  };

  /* ────────────────────────────────────────────────────────────────
     7. Actualizar UI
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._updateUI = function (index) {
    this._counter.textContent = (index + 1) + ' / ' + this._total;

    this._dots.forEach(function (dot, i) {
      const active = i === index;
      dot.classList.toggle('carousel-dot-active', active);
      dot.setAttribute('aria-selected', active ? 'true' : 'false');
    });

    this._thumbs.forEach(function (thumb, i) {
      thumb.classList.toggle('carousel-thumb-active', i === index);
    });

    const activeThumb = this._thumbs[index];
    if (activeThumb) {
      activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
  };

  /* ────────────────────────────────────────────────────────────────
     8. Cambio de efecto en caliente
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._setEffect = function (eff) {
    if (!EFFECTS.includes(eff)) return;
    this._effect = eff;
    this._container.setAttribute('data-effect', eff);
    this._effectSel.querySelectorAll('.carousel-effect-btn').forEach(function (btn) {
      btn.setAttribute('aria-pressed', btn.dataset.effect === eff ? 'true' : 'false');
    });
  };

  /* ────────────────────────────────────────────────────────────────
     9. Autoplay
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype._startAutoplay = function () {
    if (!this._opts.autoplay) return;
    const self = this;
    this._autoplayTimer = setInterval(function () { self.next(); }, this._opts.interval);
  };

  Carousel.prototype._pauseAutoplay = function () {
    if (this._autoplayTimer) { clearInterval(this._autoplayTimer); this._autoplayTimer = null; }
  };

  Carousel.prototype._resumeAutoplay = function () {
    if (!this._opts.autoplay || this._autoplayTimer) return;
    this._startAutoplay();
  };

  Carousel.prototype._resetAutoplay = function () {
    this._pauseAutoplay();
    this._resumeAutoplay();
  };

  /* ────────────────────────────────────────────────────────────────
     10. API pública
  ──────────────────────────────────────────────────────────────── */
  Carousel.prototype.destroy = function () {
    this._pauseAutoplay();
    this._container.innerHTML = this._images.map(function (img) {
      return '<img src="' + img.src + '" alt="' + img.alt + '">';
    }).join('');
    this._container.classList.remove('carousel-root', 'carousel-fullscreen');
    this._container.removeAttribute('data-effect');
    this._container.removeAttribute('role');
    this._container.removeAttribute('aria-label');
    this._container.removeAttribute('tabindex');
  };

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = Carousel;
  } else {
    global.Carousel = Carousel;
  }

}(typeof globalThis !== 'undefined' ? globalThis : window));
