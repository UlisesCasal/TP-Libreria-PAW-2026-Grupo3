document.addEventListener("DOMContentLoaded", () => {//cargar script crea el script y lo pone en el head del .html
    ConstructorElementos.cargarScript("FormValidation", "/assets/js/components/formValidation.js", () => {
        new FormValidation("#form-crear-cuenta", {
            email: {
                validar: (input) => input.value.trim().length > 0,
                mensaje: "El mail es obligatorio."
            },
            contraseña: {
                validar: (input) => input.value.trim().length > 0,
                mensaje: "La contraseña es obligatoria."
            },
            ccontraseña:{
                validar: (input) => {
                    if (input.value.trim() === "") return false;
                    return input.value === document.querySelector("#contraseña").value;
                },
                mensaje: "Las contraseñas no coinciden."
            },
            nombre_apellido:{
                validar: (input) => input.value.trim().length > 0,
                mensaje: "El nombre y el apellido son obligatorios."
            },
            telefono: {
                validar: (input) => {
                    if (input.value.trim() === "") return true;
                    return /^\d+$/.test(input.value.trim());
                },
                mensaje: "El teléfono debe contener solo números."
            }
        })});
    });