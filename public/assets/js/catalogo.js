const STORAGE_KEY = "ultimas_busquedas";
const MAX_BUSQUEDAS = 5;

function obtenerBusquedas() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch (error) {
        return [];
    }
}

// guardarBusquedas: recibe un objeto "filtros" (autor, género, precios) y
// guarda una representación legible de esos filtros en el historial de
// búsquedas almacenado en localStorage bajo la clave STORAGE_KEY.
// Si no hay filtros relevantes, no hace nada.
function guardarBusquedas(filtros) {
    const tienesFiltros = filtros.autor || filtros.genero || filtros.precio_min || filtros.precio_max;
    if (!tienesFiltros) return;

    const partes = [];
    if (filtros.autor) partes.push(`Autor: ${filtros.autor}`);
    if (filtros.genero) partes.push(`Género: ${filtros.genero}`);
    if (filtros.precio_min) partes.push(`Precio mín: $${filtros.precio_min}`);
    if (filtros.precio_max) partes.push(`Precio máx: $${filtros.precio_max}`);
    filtros.label = partes.join(', ');

    const busquedas = obtenerBusquedas();
    // busquedas.unshift(filtros): añade el nuevo objeto "filtros" al inicio
    // del array "busquedas" (posición 0). unshift inserta al principio,
    // desplazando los elementos existentes hacia la derecha. De esta forma
    // la búsqueda más reciente queda primero en el historial.
    busquedas.unshift(filtros);

    // limitar el historial a MAX_BUSQUEDAS y guardar en localStorage
    if (busquedas.length > MAX_BUSQUEDAS){
        busquedas.splice(MAX_BUSQUEDAS); //Elimino la busqueda mas antigua
    }

    localStorage.setItem(STORAGE_KEY, JSON.stringify(busquedas));
}

function construirURL(filtros){
    const params = new URLSearchParams();
    if (filtros.autor) params.set('autor', filtros.autor);
    if (filtros.genero) params.set('genero', filtros.genero);
    if (filtros.precio_min) params.set('precio_min', filtros.precio_min);
    if (filtros.precio_max) params.set('precio_max', filtros.precio_max);
    if (filtros.orden) params.set('orden', filtros.orden);
    return `/catalogo?${params.toString()}`;
}

function renderizarBusquedas(){
    //Esta funcion se triggerea cuando el usuario hace click en el cuadro de busqueda
    const contenedor = document.getElementById('historial-busquedas');
    if (!contenedor) return;

    const busquedas = obtenerBusquedas();

    if (busquedas.length === 0){
        contenedor.innerHTML = '<p>No hay busquedas.</p>';
        return;
    }
    //Armo la lista desplegable de las busquedas que realizo el usuario:
    const lista = document.createElement('ul');
    busquedas.forEach( b => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = construirURL(b); //Armo el href de la navegacion
        a.textContent = b.label;
        li.appendChild(a);
        lista.appendChild(li);
    });

    contenedor.innerHTML = '';
    contenedor.appendChild(lista);
}

document.addEventListener('DOMContentLoaded', () =>{
    renderizarBusquedas();

    const form = document.querySelector('form[action="/catalogo"]');
    if (!form) return;
    
    form.addEventListener('submit', () => {
        const filtros = {
            autor: form.querySelector('[name="autor"]').value.trim(),
            genero: form.querySelector('[name="genero"]').value,
            precio_min: form.querySelector('[name="precio_min"]').value,
            precio_max: form.querySelector('[name="precio_max"]').value,
            orden: form.querySelector('[name="orden"]').value
        };
        guardarBusquedas(filtros);
    });
});
