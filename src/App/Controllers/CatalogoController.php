<?php

namespace PAW\App\Controllers;

use PAW\Model\LibroModel;

class CatalogoController
{
    public function listar()
    {
        $modelo = new LibroModel();

        // Obtenemos todos los libros para el procesamiento en el cliente (JS)
        $libros = $modelo->getAll();

        require __DIR__ . '/../views/catalogo.view.php';
    }
}
