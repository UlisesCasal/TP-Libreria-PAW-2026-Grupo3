<?php

namespace PAW\App\Controllers;

use PAW\Model\LibroModel;

class CatalogoController
{
    public function listar()
    {
        $modelo = new LibroModel();

        // Mantenemos la inicialización de filtros vacíos para que la vista no rompa
        $filtros = [
            'q'          => trim($_GET['q'] ?? ''),
            'autor'      => trim($_GET['autor'] ?? ''),
            'genero'     => trim($_GET['genero'] ?? ''),
            'precio_min' => $_GET['precio_min'] ?? '',
            'precio_max' => $_GET['precio_max'] ?? '',
            'orden'      => $_GET['orden'] ?? 'az',
        ];

        // Obtenemos todos los libros para que el JS dinámico pueda procesar todo en el cliente
        $libros = $modelo->getAll();

        // Si preferimos que el buscador "q" de arriba ande por servidor, se podria hacer:
        // if (!empty($filtros['q'])) { $libros = $modelo->filtrar($filtros); }

        require __DIR__ . '/../views/catalogo.view.php';
    }
}
