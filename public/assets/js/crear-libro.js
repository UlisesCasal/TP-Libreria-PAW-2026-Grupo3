document.addEventListener("DOMContentLoaded", () => {
    ConstructorElementos.cargarScript("DragDrop", "/assets/js/components/dragDrop.js", () => {
        new DragDrop("#tapa");
        new DragDrop("#contratapa");
    });

    // ─── Validación del formulario ──────────────────────────────
    ConstructorElementos.cargarScript("FormValidation", "/assets/js/components/formValidation.js", () => {
        new FormValidation("#form-crear-libro", {
            titulo: {
                validar: (input) => input.value.trim().length > 0,
                mensaje: "El título es obligatorio."
            },
            autor: {
                validar: (input) => input.value.trim().length > 0,
                mensaje: "El autor es obligatorio."
            },
            genero: {
                validar: (input) => input.value.trim().length > 0,
                mensaje: "El género es obligatorio."
            },
            fechapub: {
                validar: (input) => {
                    if (input.value.trim().length === 0) return false;
                    return new Date(input.value) <= new Date();
                },
                mensaje: "La fecha de publicación es obligatoria y no puede ser futura."
            },
            precio: {
                validar: (input) => input.value !== "" && parseFloat(input.value) > 0,
                mensaje: "El precio debe ser mayor a 0."
            },
            stock: {
                validar: (input) => input.value !== "" && parseInt(input.value) >= 0,
                mensaje: "El stock no puede ser menor a 0."
            },
            isbn: {
                validar: (input) => /^\d{3}-\d{2}-\d{3}-\d{4}-\d$/.test(input.value),
                mensaje: "El ISBN debe tener el formato XXX-XX-XXX-XXXX-X."
            },
            paginas: {
                validar: (input) => input.value !== "" && parseInt(input.value) > 0,
                mensaje: "La cantidad de páginas debe ser mayor a 0."
            },
            descr: {
                validar: (input) => input.value.trim().length > 0,
                mensaje: "La descripción es obligatoria."
            },
            tapa: {
                validar: (input) => {
                    const coverUrl = document.getElementById("cover_url").value;
                    if (coverUrl) return true;
                    return input.files.length > 0 && input.files[0].type.startsWith("image/");
                },
                mensaje: "Debe seleccionar una imagen para la tapa o buscarla por ISBN."
            },
            contratapa: {
                validar: (input) => input.files.length > 0 && input.files[0].type.startsWith("image/"),
                mensaje: "Debe seleccionar una imagen para la contratapa."
            }
        });
    });

    // ─── Referencias DOM ────────────────────────────────────────
    const tituloInput   = document.getElementById("titulo");
    const isbnInput     = document.getElementById("isbn");
    const btnBuscarIsbn = document.getElementById("btn-buscar-isbn");
    const coverUrlInput = document.getElementById("cover_url");
    const sugerencias   = document.getElementById("sugerencias-libro");

    if (!tituloInput || !sugerencias) return;

    // ─── BÚSQUEDA AUTOMÁTICA POR TÍTULO ─────────────────────────
    let timeoutBusqueda = null;
    let libroSeleccionado = false;

    function dispararBusqueda() {
        clearTimeout(timeoutBusqueda);

        const q = tituloInput.value.trim();
        if (q.length < 3) {
            sugerencias.innerHTML = "";
            sugerencias.classList.remove("visible");
            return;
        }

        libroSeleccionado = false;

        timeoutBusqueda = setTimeout(() => buscarSugerencias(q), 200);
    }

    // Buscar mientras escribe (input se dispara en cada tecla)
    tituloInput.addEventListener("input", dispararBusqueda);
    // Fallback: keyup por si algún navegador no dispara input correctamente
    tituloInput.addEventListener("keyup", dispararBusqueda);
    // Enter en el título NO envía el formulario, solo fuerza la búsqueda
    tituloInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            clearTimeout(timeoutBusqueda);
            const q = tituloInput.value.trim();
            if (q.length >= 3) buscarSugerencias(q);
        }
    });

    // Cerrar sugerencias al hacer clic afuera
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".titulo-busqueda")) {
            sugerencias.innerHTML = "";
            sugerencias.classList.remove("visible");
        }
    });

    /**
     * Busca libros en Open Library que coincidan con el texto ingresado.
     */
    async function buscarSugerencias(query) {
        try {
            const res = await fetch(`/api/buscar-libro?q=${encodeURIComponent(query)}`);
            const libros = await res.json();

            mostrarSugerencias(libros);
        } catch (e) {
            console.error("Error buscando libros:", e);
        }
    }

    /**
     * Muestra la lista de sugerencias debajo del campo título.
     */
    function mostrarSugerencias(libros) {
        sugerencias.innerHTML = "";

        if (!libros || libros.length === 0) {
            sugerencias.classList.remove("visible");
            return;
        }

        libros.forEach((libro) => {
            const li = document.createElement("li");
            li.className = "sugerencia-item";

            const img = document.createElement("img");
            img.src = libro.cover_url || "/assets/img/no-image.png";
            img.alt = "Tapa";
            img.className = "sugerencia-img";
            img.onerror = () => { img.src = "/assets/img/no-image.png"; };

            const info = document.createElement("div");
            info.className = "sugerencia-info";

            const titulo = document.createElement("strong");
            titulo.textContent = libro.titulo;

            const autor = document.createElement("span");
            autor.textContent = libro.autor ? ` — ${libro.autor}` : "";
            autor.className = "sugerencia-autor";

            info.appendChild(titulo);
            info.appendChild(autor);

            li.appendChild(img);
            li.appendChild(info);

            li.addEventListener("click", () => seleccionarLibro(libro));

            sugerencias.appendChild(li);
        });

        sugerencias.classList.add("visible");
    }

    /**
     * Cuando el usuario hace clic en una sugerencia, autocompleta el formulario.
     */
    function seleccionarLibro(libro) {
        libroSeleccionado = true;
        sugerencias.innerHTML = "";
        sugerencias.classList.remove("visible");

        // Autocompletar campos básicos
        setField("titulo", libro.titulo);
        setField("autor", libro.autor);
        setField("genero", libro.genero);
        setField("fechapub", libro.fechapub);
        setField("paginas", libro.paginas);

        // ISBN
        if (libro.isbn) {
            setField("isbn", formatearISBN(libro.isbn));
        }

        // Cover
        if (libro.cover_url) {
            coverUrlInput.value = libro.cover_url;
            mostrarPreviewTapa(libro.cover_url);
        }

        // Obtener detalle completo desde la obra (páginas, ISBN, descripción, tapa)
        if (libro.key) {
            buscarDetalleWork(libro.key);
        }
    }

    /**
     * Obtiene datos completos (páginas, ISBN, descripción) desde la obra.
     */
    let detalleEnProgreso = false;

    async function buscarDetalleWork(workKey) {
        if (detalleEnProgreso) return;
        detalleEnProgreso = true;

        try {
            const res = await fetch(`/api/detalle-libro?key=${encodeURIComponent(workKey)}`);
            const data = await res.json();

            if (data.error) return;

            // Páginas (solo si no se llenó antes)
            if (data.paginas && !document.getElementById("paginas").value) {
                setField("paginas", data.paginas);
            }

            // ISBN (solo si no se llenó antes)
            if (data.isbn && !document.getElementById("isbn").value) {
                setField("isbn", formatearISBN(data.isbn));
            }

            // Descripción (solo si no se llenó antes)
            if (data.descr && !document.getElementById("descr").value) {
                setField("descr", data.descr);
            }

            // Fecha de publicación (si no se llenó antes)
            if (data.fechapub && !document.getElementById("fechapub").value) {
                setField("fechapub", data.fechapub);
            }

            // Cover (si no se llenó antes)
            if (data.cover_url && !coverUrlInput.value) {
                coverUrlInput.value = data.cover_url;
                mostrarPreviewTapa(data.cover_url);
            }
        } catch (e) {
            // Silencioso
        } finally {
            detalleEnProgreso = false;
        }
    }

    /**
     * Formatea un ISBN al formato XXX-XX-XXX-XXXX-X.
     */
    function formatearISBN(isbn) {
        const limpio = isbn.replace(/[^0-9X]/gi, "");
        if (limpio.length === 13) {
            return `${limpio.slice(0,3)}-${limpio.slice(3,5)}-${limpio.slice(5,8)}-${limpio.slice(8,12)}-${limpio.slice(12)}`;
        }
        return isbn;
    }

    // ─── BÚSQUEDA POR ISBN (botón manual) ───────────────────────
    if (btnBuscarIsbn && isbnInput) {
        btnBuscarIsbn.addEventListener("click", () => buscarLibroPorISBN());
        isbnInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                buscarLibroPorISBN();
            }
        });
    }

    async function buscarLibroPorISBN() {
        const isbn = isbnInput.value.trim();
        if (!isbn) {
            alert("Primero ingresá un ISBN.");
            return;
        }

        if (!/^\d{3}-\d{2}-\d{3}-\d{4}-\d$/.test(isbn)) {
            alert("El ISBN debe tener el formato XXX-XX-XXX-XXXX-X.");
            return;
        }

        btnBuscarIsbn.disabled = true;
        btnBuscarIsbn.textContent = "Buscando...";

        try {
            const res = await fetch(`/api/buscar-isbn?isbn=${encodeURIComponent(isbn)}`);
            const data = await res.json();

            if (data.error) {
                alert(data.error);
                return;
            }

            setField("titulo", data.titulo);
            setField("autor", data.autor);
            setField("genero", data.genero);
            setField("fechapub", data.fechapub);
            setField("paginas", data.paginas);
            setField("descr", data.descr);

            if (data.cover_url) {
                coverUrlInput.value = data.cover_url;
                mostrarPreviewTapa(data.cover_url);
            }

            btnBuscarIsbn.textContent = "✓ Datos cargados";
            setTimeout(() => {
                btnBuscarIsbn.textContent = "Buscar en Open Library";
                btnBuscarIsbn.disabled = false;
            }, 2500);

        } catch (e) {
            alert("Error al conectar con Open Library.");
            btnBuscarIsbn.textContent = "Buscar en Open Library";
            btnBuscarIsbn.disabled = false;
        }
    }

    // ─── FUNCIONES COMUNES ───────────────────────────────────────

    function setField(id, value) {
        const el = document.getElementById(id);
        if (el && value) {
            el.value = value;
        }
    }

    function mostrarPreviewTapa(url) {
        const container = document.querySelector(".dragdrop-wrapper") ||
                          document.getElementById("tapa")?.parentElement;
        if (!container) return;

        const anterior = container.querySelector(".cover-preview-api");
        if (anterior) anterior.remove();

        const preview = document.createElement("div");
        preview.className = "cover-preview-api";
        preview.innerHTML = `
            <p style="font-size:0.85em;color:#555;margin-top:6px;">
                Tapa obtenida de la API:
            </p>
            <img src="${url}" alt="Preview" style="max-width:120px;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.15);">
        `;
        container.appendChild(preview);
    }

    // Si el usuario sube una tapa manualmente, limpiar la URL de la API
    document.getElementById("tapa")?.addEventListener("change", function () {
        if (this.files.length > 0) {
            coverUrlInput.value = "";
            const anterior = document.querySelector(".cover-preview-api");
            if (anterior) anterior.remove();
        }
    });
});
