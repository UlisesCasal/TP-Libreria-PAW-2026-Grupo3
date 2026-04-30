<?php

namespace PAW\App\Controllers;

use PAW\Model\LibroModel;

class CatalogoController
{
    public function listar()
    {
        $modelo = new LibroModel();

        $filtros = [
            'autor'       => trim($_GET['autor'] ?? ''),
            'genero'      => trim($_GET['genero'] ?? ''),
            'precio_min'  => $_GET['precio_min'] ?? '',
            'precio_max'  => $_GET['precio_max'] ?? '',
            'orden'       => $_GET['orden'] ?? 'az',
        ];

        $libros = $modelo->filtrar($filtros);

        require __DIR__ . '/../views/catalogo.view.php';
    }
}
