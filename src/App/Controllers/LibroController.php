<?php

namespace PAW\App\Controllers;

use PAW\Model\LibroModel;

class LibroController
{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';
    }

    public function mostrar_lib()
    {
        $modelo = new LibroModel();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $libro = $id > 0 ? $modelo->getById($id) : null;

        if ($libro === null) {
            // Si no hay id válido, mostrar el primero disponible
            $todos = $modelo->getAll();
            $libro = $todos[0] ?? null;
        }

        // Libros relacionados: mismo género, excluyendo el actual
        $relacionados = [];
        if ($libro !== null) {
            $todos = $modelo->getAll();
            foreach ($todos as $l) {
                if ($l['genero'] === $libro['genero'] && $l['id'] !== $libro['id']) {
                    $relacionados[] = $l;
                    if (count($relacionados) >= 3) break;
                }
            }
        }

        require $this->viewdir . 'libro.view.php';
    }

    public function compra_lib()
    {
        $cantidad = $_POST['cantidad'] ?? '';
        require $this->viewdir . 'formulario.view.php';
    }
}
