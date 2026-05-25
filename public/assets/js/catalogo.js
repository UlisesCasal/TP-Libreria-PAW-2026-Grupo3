const STORAGE_KEY = "ultimas_busquedas";
const MAX_BUSQUEDAS = 5;

function obtenerBusquedas() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch (error) {
        return [];
    }
}

function guardarBusquedas(filtros) {
    const tienesFiltros = filtros.q || filtros.autor || filtros.genero || filtros.precio_min || filtros.precio_max;
    if (!tienesFiltros) return;

    const partes = [];
    if (filtros.q)          partes.push(`Búsqueda: ${filtros.q}`);
    if (filtros.autor)      partes.push(`Autor: ${filtros.autor}`);
    if (filtros.genero)     partes.push(`Género: ${filtros.genero}`);
    if (filtros.precio_min) partes.push(`Precio mín: $${filtros.precio_min}`);
    if (filtros.precio_max) partes.push(`Precio máx: $${filtros.precio_max}`);
    filtros.label = partes.join(', ');

    const busquedas = obtenerBusquedas();

    // Dedup: si la misma búsqueda ya existe, la movemos al frente
    const idx = busquedas.findIndex(b => b.label === filtros.label);
    if (idx !== -1) busquedas.splice(idx, 1);

    busquedas.unshift(filtros);

    if (busquedas.length > MAX_BUSQUEDAS) {
        busquedas.splice(MAX_BUSQUEDAS); // Elimina la más antigua
    }

    localStorage.setItem(STORAGE_KEY, JSON.stringify(busquedas));
}

function construirURL(filtros) {
    const params = new URLSearchParams();
    if (filtros.q)          params.set('q', filtros.q);
    if (filtros.autor)      params.set('autor', filtros.autor);
    if (filtros.genero)     params.set('genero', filtros.genero);
    if (filtros.precio_min) params.set('precio_min', filtros.precio_min);
    if (filtros.precio_max) params.set('precio_max', filtros.precio_max);
    if (filtros.orden)      params.set('orden', filtros.orden);
    return `/catalogo?${params.toString()}`;
}

function mostrarDropdown() {
    const input      = document.getElementById('q');
    const contenedor = document.getElementById('sugerencias-busqueda');
    if (!input || !contenedor) return;

    const busquedas = obtenerBusquedas();
    if (busquedas.length === 0) return;

    contenedor.innerHTML = '';
    busquedas.forEach(b => {
        const a = document.createElement('a');
        a.href      = construirURL(b);
        a.textContent = b.label;
        // mousedown antes de blur para que el click se registre
        a.addEventListener('mousedown', e => {
            e.preventDefault();
            window.location.href = construirURL(b);
        });
        contenedor.appendChild(a);
    });

    contenedor.style.display = 'flex';
}

function ocultarDropdown() {
    const contenedor = document.getElementById('sugerencias-busqueda');
    if (contenedor) contenedor.style.display = '';
}

function mostrarHistorial() {
    const seccion = document.getElementById('historial-busquedas');
    if (!seccion) return;

    const busquedas = obtenerBusquedas();
    if (busquedas.length === 0) {
        seccion.style.display = 'none';
        return;
    }

    const ul = seccion.querySelector('ul');
    ul.innerHTML = '';
    busquedas.forEach(b => {
        const li = document.createElement('li');
        const a  = document.createElement('a');
        a.href      = construirURL(b);
        a.textContent = b.label;
        li.appendChild(a);
        ul.appendChild(li);
    });

    seccion.style.display = '';
}

document.addEventListener('DOMContentLoaded', () => {
    // Guardar búsqueda actual leyendo los parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    guardarBusquedas({
        q:          urlParams.get('q')          || '',
        autor:      urlParams.get('autor')      || '',
        genero:     urlParams.get('genero')     || '',
        precio_min: urlParams.get('precio_min') || '',
        precio_max: urlParams.get('precio_max') || '',
        orden:      urlParams.get('orden')      || ''
    });

    // Sección persistente de últimas búsquedas
    mostrarHistorial();

    // Dropdown de últimas búsquedas
    const input = document.getElementById('q');
    if (input) {
        input.addEventListener('focus', mostrarDropdown);
        input.addEventListener('blur',  ocultarDropdown);
    }
});
