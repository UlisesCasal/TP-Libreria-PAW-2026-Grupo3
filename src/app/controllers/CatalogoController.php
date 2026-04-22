<?php

namespace PAW\App\Controllers;

class CatalogoController {

    /**
     * Muestra el listado de libros del catálogo
     */
    public function listar() {
        $vista = __DIR__ . '/../views/catalogo.html';

        if (!file_exists($vista)) {
            http_response_code(500);
            echo "No se encontro la vista del catalogo";
            return;
        }

        require $vista;
    }
}
