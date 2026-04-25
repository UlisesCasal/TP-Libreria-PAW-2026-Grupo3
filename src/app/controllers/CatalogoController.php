<?php

namespace PAW\App\Controllers;

class CatalogoController {

    /**
     * Muestra el listado de libros del catálogo
     */
    public function listar() {
        require __DIR__ . '/../views/catalogo.view.php';
    }
}
