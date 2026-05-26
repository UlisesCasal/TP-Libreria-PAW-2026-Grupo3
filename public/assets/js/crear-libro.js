document.addEventListener("DOMContentLoaded", () => {//cargar script crea el script y lo pone en el head del .html
    ConstructorElementos.cargarScript("DragDrop", "/assets/js/components/dragDrop.js", () => {
        new DragDrop("#tapa");
        new DragDrop("#contratapa");
    });
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
                validar: (input) => input.files.length > 0 && input.files[0].type.startsWith("image/"),
                mensaje: "Debe seleccionar una imagen para la tapa."
            },
            contratapa: {
                validar: (input) => input.files.length > 0 && input.files[0].type.startsWith("image/"),
                mensaje: "Debe seleccionar una imagen para la contratapa."
            }
        });
    });
});