/**
 * CatalogManager - Gestiona el filtrado, ordenamiento y paginación del catálogo de libros.
 */
class CatalogManager {
    constructor(books) {
        this.allBooks = books;
        this.filteredBooks = [...books];
        this.currentPage = 1;
        this.isMobile = window.innerWidth <= 600;
        this.pageSize = this.isMobile ? 12 : 6; // Más libros en mobile para mejor scroll
        this.observer = null;

        // Elementos del DOM
        this.bookListContainer = document.getElementById('book-list');
        this.paginationContainer = document.getElementById('pagination-controls');
        this.filterForm = document.getElementById('filter-form');
        this.scrollAnchor = document.getElementById('scroll-anchor');

        this.init();
    }

    init() {
        // Escuchar cambios en los filtros y orden
        this.filterForm.addEventListener('input', () => this.handleFilterChange());
        
        // El botón "submit" ahora limpia los filtros
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
                this.setupInfiniteScroll(); // Re-configurar según el modo
                this.render();
            }
        });

        this.setupInfiniteScroll();

        // Renderizado inicial
        this.applyFilters();
    }

    handleFilterChange() {
        this.currentPage = 1;
        this.applyFilters();
    }

    /**
     * Aplica los filtros y el orden al dataset completo.
     */
    applyFilters() {
        const formData = new FormData(this.filterForm);
        const autor = formData.get('autor').toLowerCase();
        const genero = formData.get('genero');
        const precioMin = parseFloat(formData.get('precio_min')) || 0;
        const precioMax = parseFloat(formData.get('precio_max')) || Infinity;
        const orden = formData.get('orden');

        // 1. Filtrar
        this.filteredBooks = this.allBooks.filter(book => {
            const matchesAutor = book.autor.toLowerCase().includes(autor);
            const matchesGenero = genero === "" || book.genero === genero;
            const matchesPrecio = book.precio >= precioMin && book.precio <= precioMax;
            return matchesAutor && matchesGenero && matchesPrecio;
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
        // En mobile (scroll infinito) mostramos desde el inicio hasta la página actual
        const end = this.isMobile ? this.currentPage * this.pageSize : start + this.pageSize;
        
        const booksToDisplay = this.isMobile 
            ? this.filteredBooks.slice(0, end) 
            : this.filteredBooks.slice(start, end);

        // Renderizado eficiente
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

        // Actualizar paginación tradicional si no es mobile
        if (!this.isMobile) {
            this.renderPagination();
        } else {
            this.paginationContainer.innerHTML = '';
        }
    }

    /**
     * Renderiza los controles de paginación (botones).
     */
    renderPagination() {
        this.paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(this.filteredBooks.length / this.pageSize);

        if (totalPages <= 1) return;

        // Botón Anterior
        const prevBtn = document.createElement('button');
        prevBtn.innerText = '«';
        prevBtn.disabled = this.currentPage === 1;
        prevBtn.onclick = () => { this.currentPage--; this.render(); window.scrollTo({top: 0, behavior: 'smooth'}); };
        this.paginationContainer.appendChild(prevBtn);

        // Números de página
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

        // Botón Siguiente
        const nextBtn = document.createElement('button');
        nextBtn.innerText = '»';
        nextBtn.disabled = this.currentPage === totalPages;
        nextBtn.onclick = () => { this.currentPage++; this.render(); window.scrollTo({top: 0, behavior: 'smooth'}); };
        this.paginationContainer.appendChild(nextBtn);
    }

    /**
     * Configura el scroll infinito mediante IntersectionObserver.
     */
    setupInfiniteScroll() {
        // Limpiar observador previo si existe
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }

        if (!this.isMobile) return;

        const options = {
            root: null,
            rootMargin: '200px', // Cargar un poco antes de llegar al final
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
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (window.ALL_BOOKS) {
        new CatalogManager(window.ALL_BOOKS);
    }
});
