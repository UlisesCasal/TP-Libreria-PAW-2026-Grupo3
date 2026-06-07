<?php

namespace PAW\App\Controllers;

use PAW\Core\TwigEnvironment;
use PAW\Model\LibroModel;

class CrearLibroController{

    private function libroCreado()
    {
        TwigEnvironment::getInstance()->render('crearLibroCreado.twig', []);
    }

    private function elLibroYaExiste(){
        TwigEnvironment::getInstance()->render('libroYaExiste.twig', []);
    }

    public function altaLibro(){
        $modelo = new LibroModel();
        $isbn = $_POST['isbn'];
        if(!$modelo->existeIsbn($isbn)){
            // ─── Manejo de la tapa ──────────────────────────────────
            // Si no se subió una imagen, intentar descargar de Open Library
            $tapaName = '';
            if (!empty($_FILES['tapa']['name'])) {
                // Usar la imagen subida por el usuario
                $tapaName = $_FILES['tapa']['name'];
                move_uploaded_file($_FILES['tapa']['tmp_name'], __DIR__ . '/../../../public/assets/tapas/' . $tapaName);
            } elseif (!empty($_POST['cover_url'])) {
                // Descargar la tapa desde la URL de Open Library
                $tapaName = 'cover_' . $isbn . '.jpg';
                $tapaUrl = $_POST['cover_url'];
                $tapaContent = @file_get_contents($tapaUrl);
                if ($tapaContent !== false) {
                    file_put_contents(__DIR__ . '/../../../public/assets/tapas/' . $tapaName, $tapaContent);
                } else {
                    $tapaName = ''; // No se pudo descargar
                }
            }

            // ─── Contratapa ─────────────────────────────────────────
            $contratapaName = '';
            if (!empty($_FILES['contratapa']['name'])) {
                $contratapaName = $_FILES['contratapa']['name'];
                move_uploaded_file($_FILES['contratapa']['tmp_name'], __DIR__ . '/../../../public/assets/contratapas/' . $contratapaName);
            }

            $libro = [
                'titulo'               => $_POST['titulo'],
                'autor'                => $_POST['autor'],
                'genero'               => $_POST['genero'],
                'precio'               => $_POST['precio'],
                'stock'                => $_POST['stock'],
                'nombre_archi_tapa'    => $tapaName,
                'isbn'                 => $_POST['isbn'],
                'paginas'              => $_POST['paginas'],
                'fecha-pub'            => $_POST['fechapub'],
                'descr'                => $_POST['descr'],
                'nombre_archi_contratapa' => $contratapaName,
            ];

            $modelo->cargaLibro($libro);
            $this->libroCreado();
        }
        else{
            $this->elLibroYaExiste();
        }
    }
    public function mostrarForm(){
        TwigEnvironment::getInstance()->render('crear-libro.twig', []);
    }
    //falta validar que se completen todos lso campos en el formulario!!! 
    //doble capa de seguridad con JS + php
}