/**
 * CatalogManager - Gestiona el filtrado dinámico, ordenamiento, 
 * paginación y el historial de búsquedas del catálogo.
 */
class CatalogManager {
    constructor(books) {
        this.allBooks = books;
        this.filteredBooks = [...books];
        this.currentPage = 1;
        this.isMobile = window.innerWidth <= 600;
        this.pageSize = this.isMobile ? 12 : 6;
        this.observer = null;

        // Claves para el historial
        this.STORAGE_KEY = "ultimas_busquedas";
        this.MAX_BUSQUEDAS = 5;

        // Elementos del DOM
        this.bookListContainer = document.getElementById('book-list');
        this.paginationContainer = document.getElementById('pagination-controls');
        this.filterForm = document.getElementById('filter-form');
        this.scrollAnchor = document.getElementById('scroll-anchor');
        this.searchInput = document.getElementById('q');
        this.suggestionsContainer = document.getElementById('sugerencias-busqueda');
        this.historySection = document.getElementById('historial-busquedas');

        this.init();
    }

    init() {
        // Escuchar cambios en los filtros y orden (Se ejecuta reactivamente)
        this.filterForm.addEventListener('input', () => this.handleFilterChange());
        
        // El botón "Limpiar Filtros" resetea el form
        this.filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.filterForm.reset();
            this.handleFilterChange();
        });

        // Configurar Resize para detectar cambio de modo (Desktop/Mobile)
        window.addEventListener('resize', () => {
            const wasMobile = this.isMobile;
            this.isMobile = window.innerWidth <= 600;
            if (wasMobile !== this.isMobile) {
                this.pageSize = this.isMobile ? 12 : 6;
                this.currentPage = 1;
                this.setupInfiniteScroll();
                this.render();
            }
        });

        // Integración del comportamiento del buscador con el historial de búsquedas
        if (this.searchInput) {
            this.searchInput.addEventListener('focus', () => this.mostrarDropdown());
            this.searchInput.addEventListener('blur', () => this.ocultarDropdown());
        }

        this.setupInfiniteScroll();
        this.mostrarHistorial();

        // Renderizado e inicialización pasándole los datos de la URL si existen
        this.cargarFiltrosDesdeURL();
        this.applyFilters(false); // false para que no guarde el historial al cargar la página
    }

    handleFilterChange() {
        this.currentPage = 1;
        this.applyFilters(true); // true guarda la combinación actual en el historial
    }

    /**
     * Aplica los filtros (incluyendo el buscador por texto) y el orden.
     */
    applyFilters(guardarEnHistorial = false) {
        const formData = new FormData(this.filterForm);
        const q = formData.get('q') ? formData.get('q').toLowerCase().trim() : '';
        const autor = formData.get('autor') ? formData.get('autor').toLowerCase().trim() : '';
        const genero = formData.get('genero');
        const precioMin = parseFloat(formData.get('precio_min')) || 0;
        const precioMax = parseFloat(formData.get('precio_max')) || Infinity;
        const orden = formData.get('orden');

        // 1. Filtrar combinando tus filtros + el buscador general "q"
        this.filteredBooks = this.allBooks.filter(book => {
            const matchesQ = q === "" || 
                             book.titulo.toLowerCase().includes(q) || 
                             book.autor.toLowerCase().includes(q) || 
                             book.genero.toLowerCase().includes(q);
            const matchesAutor = book.autor.toLowerCase().includes(autor);
            const matchesGenero = genero === "" || book.genero === genero;
            const matchesPrecio = book.precio >= precioMin && book.precio <= precioMax;
            
            return matchesQ && matchesAutor && matchesGenero && matchesPrecio;
        });

        // 2. Ordenar
        this.filteredBooks.sort((a, b) => {
            switch (orden) {
                case 'za': 
                    return b.titulo.localeCompare(a.titulo);
                case 'precio-ascendente': 
                    return a.precio - b.precio;
                case 'precio-descendente': 
                    return b.precio - a.precio;
                default: // 'az'
                    return a.titulo.localeCompare(b.titulo);
            }
        });

        // 3. Guardar en el historial local si corresponde
        if (guardarEnHistorial) {
            this.guardarBusquedaActual({ q, autor, genero, precio_min: formData.get('precio_min'), precio_max: formData.get('precio_max'), orden });
        }

        this.render();
    }

    /**
     * Renderiza los libros en el contenedor según el estado actual.
     */
    render() {
        if (this.filteredBooks.length === 0) {
            this.bookListContainer.innerHTML = '<p class="no-results">No se encontraron libros con los filtros aplicados.</p>';
            this.paginationContainer.innerHTML = '';
            return;
        }

        const start = (this.currentPage - 1) * this.pageSize;
        const end = this.isMobile ? this.currentPage * this.pageSize : start + this.pageSize;
        
        const booksToDisplay = this.isMobile 
            ? this.filteredBooks.slice(0, end) 
            : this.filteredBooks.slice(start, end);

        const fragment = document.createDocumentFragment();
        booksToDisplay.forEach(book => {
            const li = document.createElement('li');
            li.innerHTML = `
                <a href="/libro?id=${book.id}">
                    <img src="/assets/img/${book.imagen}" alt="${book.titulo}">
                </a>
                <p>${book.titulo}</p>
                <p>${book.autor}</p>
                <p>$${book.precio.toLocaleString('es-AR')}</p>
            `;
            fragment.appendChild(li);
        });

        this.bookListContainer.innerHTML = '';
        this.bookListContainer.appendChild(fragment);

        if (!this.isMobile) {
            this.renderPagination();
        } else {
            this.paginationContainer.innerHTML = '';
        }
    }

    renderPagination() {
        this.paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(this.filteredBooks.length / this.pageSize);

        if (totalPages <= 1) return;

        const prevBtn = document.createElement('button');
        prevBtn.innerText = '«';
        prevBtn.disabled = this.currentPage === 1;
        prevBtn.onclick = () => { this.currentPage--; this.render(); window.scrollTo({top: 0, behavior: 'smooth'}); };
        this.paginationContainer.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.innerText = i;
            if (i === this.currentPage) btn.classList.add('active');
            btn.onclick = () => {
                this.currentPage = i;
                this.render();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };
            this.paginationContainer.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.innerText = '»';
        nextBtn.disabled = this.currentPage === totalPages;
        nextBtn.onclick = () => { this.currentPage++; this.render(); window.scrollTo({top: 0, behavior: 'smooth'}); };
        this.paginationContainer.appendChild(nextBtn);
    }

    setupInfiniteScroll() {
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }

        if (!this.isMobile) return;

        const options = {
            root: null,
            rootMargin: '200px',
            threshold: 0.1
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && this.isMobile) {
                    const totalPages = Math.ceil(this.filteredBooks.length / this.pageSize);
                    if (this.currentPage < totalPages) {
                        this.currentPage++;
                        this.render();
                    }
                }
            });
        }, options);

        if (this.scrollAnchor) {
            this.observer.observe(this.scrollAnchor);
        }
    }

    /* --- LÓGICA DEL HISTORIAL ADAPTADA --- */

    obtenerBusquedas() {
        try {
            return JSON.parse(localStorage.getItem(this.STORAGE_KEY)) || [];
        } catch (error) {
            return [];
        }
    }

    guardarBusquedaActual(filtros) {
        const tienesFiltros = filtros.q || filtros.autor || filtros.genero || filtros.precio_min || filtros.precio_max;
        if (!tienesFiltros) return;

        const partes = [];
        if (filtros.q)          partes.push(`Búsqueda: ${filtros.q}`);
        if (filtros.autor)      partes.push(`Autor: ${filtros.autor}`);
        if (filtros.genero)     partes.push(`Género: ${filtros.genero}`);
        if (filtros.precio_min) partes.push(`Precio mín: $${filtros.precio_min}`);
        if (filtros.precio_max) partes.push(`Precio máx: $${filtros.precio_max}`);
        filtros.label = partes.join(', ');

        const busquedas = this.obtenerBusquedas();
        const idx = busquedas.findIndex(b => b.label === filtros.label);
        if (idx !== -1) busquedas.splice(idx, 1);

        busquedas.unshift(filtros);

        if (busquedas.length > this.MAX_BUSQUEDAS) {
            busquedas.splice(this.MAX_BUSQUEDAS);
        }

        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(busquedas));
        this.mostrarHistorial();
    }

    construirURL(filtros) {
        const params = new URLSearchParams();
        if (filtros.q)          params.set('q', filtros.q);
        if (filtros.autor)      params.set('autor', filtros.autor);
        if (filtros.genero)     params.set('genero', filtros.genero);
        if (filtros.precio_min) params.set('precio_min', filtros.precio_min);
        if (filtros.precio_max) params.set('precio_max', filtros.precio_max);
        if (filtros.orden)      params.set('orden', filtros.orden);
        return `/catalogo?${params.toString()}`;
    }

    mostrarDropdown() {
        if (!this.suggestionsContainer) return;
        const busquedas = this.obtenerBusquedas();
        if (busquedas.length === 0) return;

        this.suggestionsContainer.innerHTML = '';
        busquedas.forEach(b => {
            const a = document.createElement('a');
            a.href = this.construirURL(b);
            a.textContent = b.label;
            a.addEventListener('mousedown', e => {
                e.preventDefault();
                window.location.href = this.construirURL(b);
            });
            this.suggestionsContainer.appendChild(a);
        });

        this.suggestionsContainer.style.display = 'flex';
    }

    ocultarDropdown() {
        if (this.suggestionsContainer) this.suggestionsContainer.style.display = '';
    }

    mostrarHistorial() {
        if (!this.historySection) return;
        const busquedas = this.obtenerBusquedas();
        if (busquedas.length === 0) {
            this.historySection.style.display = 'none';
            return;
        }

        const ul = this.historySection.querySelector('ul');
        ul.innerHTML = '';
        busquedas.forEach(b => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = this.construirURL(b);
            a.textContent = b.label;
            li.appendChild(a);
            ul.appendChild(li);
        });

        this.historySection.style.display = '';
    }

    cargarFiltrosDesdeURL() {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.forEach((value, key) => {
            const input = this.filterForm.elements[key];
            if (input) input.value = value;
        });
    }
}

// Inicialización del script dinámico
document.addEventListener('DOMContentLoaded', () => {
    if (window.ALL_BOOKS) {
        new CatalogManager(window.ALL_BOOKS);
    }
});