<?php

namespace PAW\App\Controllers;

use PAW\Core\TwigEnvironment;
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

        // La búsqueda y el filtrado se realizan SOBRE LA BASE DE DATOS (SQL).
        // Si llegan parámetros de búsqueda/filtro por GET, se consulta filtrando
        // en el servidor; si no, se listan todos los libros.
        $hayFiltros = $filtros['q'] !== '' || $filtros['autor'] !== ''
            || $filtros['genero'] !== '' || $filtros['precio_min'] !== ''
            || $filtros['precio_max'] !== '' || ($_GET['orden'] ?? '') !== '';

        $libros = $hayFiltros ? $modelo->filtrar($filtros) : $modelo->getAll();

        TwigEnvironment::getInstance()->render('catalogo.twig', [
            'libros' => $libros,
            'filtros' => $filtros
        ]);
    }
}
