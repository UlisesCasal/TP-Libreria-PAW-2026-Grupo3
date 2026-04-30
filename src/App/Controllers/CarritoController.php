<?php

namespace PAW\App\Controllers;

class CarritoController {

    /**
     * Muestra el carrito actual
     */
    public function ver() {
        $vista = __DIR__ . '/../views/carrito.view.php';

        if (!file_exists($vista)) {
            http_response_code(500);
            echo "No se encontro la vista del carrito";
            return;
        }

        require $vista;
    }

    /**
     * Agrega un libro al carrito
     */
    public function agregar() {
        // Aquí irá la lógica para:
        // 1. Obtener el id del libro desde $_POST
        // 2. Agregarlo a $_SESSION['carrito']
        // 3. Redirigir al carrito

        echo "Libro agregado al carrito";
    }

    /**
     * Elimina un libro del carrito
     */
    public function eliminar() {
        // Aquí irá la lógica para:
        // 1. Obtener el id del libro desde $_POST
        // 2. Eliminarlo de $_SESSION['carrito']
        // 3. Redirigir al carrito

        echo "Libro eliminado del carrito";
    }
}
