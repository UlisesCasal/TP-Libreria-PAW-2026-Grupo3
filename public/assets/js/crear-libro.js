document.addEventListener("DOMContentLoaded", () => {//cargar script crea el script y lo pone en el head del .html
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
                validar: (input) => /^\d{3}-\d{2}-\d{3}-\d{5}-\d$/.test(input.value),
                mensaje: "El ISBN debe tener el formato 978-84-376-0494-7."
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
                validar: (input) => input.files.length > 0 && input.files[0].type.startsWith("image/"),
                mensaje: "Debe seleccionar una imagen para la tapa."
            },
            contratapa: {
                validar: (input) => input.files.length > 0 && input.files[0].type.startsWith("image/"),
                mensaje: "Debe seleccionar una imagen para la contratapa."
            }
        });
    });
});/*<label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="ej.: El principito">
                <label for="autor">Autor</label>
                <input type="text" id="autor" name="autor" placeholder="ej.:Borges">
                <label for="genero">Genero</label>
                <input type="text" id="genero" name="genero" placeholder="ej.:Romantico">
                <label for="fechapub">Fecha de publicacion</label>
                <input type="date" id="fechapub" name="fechapub" placeholder="ej.:02/11/2022">
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" min="0" step="0.01" placeholder="ej.:120.00">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" min="0" placeholder="ej.:5">
                <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn" placeholder="ej.:978-84-376-0494-7">
                <label for="paginas">Paginas</label>
                <input type="number" id="paginas" name="paginas" min="1" placeholder="ej.:978">
                <label for="descr">Descripcion</label>
                <textarea id="descr" name="descr" rows="4" placeholder="ej.: Novela romántica que se basa en Romeo y Julieta"></textarea>  
                <label for="tapa">Tapa</label>
                <input type="file" id="tapa" name="tapa">     
                <label for="contratapa">Contratapa</label>
                <input type="file" id="contratapa" name="contratapa">         */