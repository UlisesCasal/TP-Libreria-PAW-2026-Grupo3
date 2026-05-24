/* El objetivo de esta clase es evitar que el usuario envíe el 
formulario con datos inválidos, mostrándole mensajes de error en 
tiempo real, campo por campo, sin depender del comportamiento 
nativo del navegador (que es diferente en Chrome, Firefox, Safari).

El flujo sería:

1)Usuario llena el campo titulo y pasa al siguiente → el 
componente valida ese campo y si está vacío muestra un span 
rojo debajo con "El título es obligatorio"
2)Si el usuario lo corrige → el span desaparece (o se pone verde)
3)Si el usuario hace click en Crear libro sin completar todo → el 
componente valida todos los campos a la vez y muestra todos los 
errores juntos, sin enviar el form al servidor
*/
class formValidation{
    constructor(pFormulario){
        

    }

}