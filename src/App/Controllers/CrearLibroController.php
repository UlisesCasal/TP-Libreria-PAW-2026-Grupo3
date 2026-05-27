<?php

namespace PAW\App\Controllers;

use PAW\Model\LibroModel;

class CrearLibroController{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';
    }
    

    private function libroCreado()
    {
        require $this->viewdir . 'crearLibroCreado.view.php';//falta crear la vista
    }

    private function elLibroYaExiste(){
        require $this->viewdir . 'libroYaExiste.view.php';//falta crear la vista
    }

    public function altaLibro(){
        $modelo = new LibroModel();
        $isbn = $_POST['isbn'];
        if(!$modelo->existeIsbn($isbn)){
            $libro=[//en un futuro va a haber que crear la clase libro para cargarla en la bd
                'titulo'=> $_POST['titulo'],
                'autor'=> $_POST['autor'],
                'genero'=> $_POST['genero'],
                'precio'=> $_POST['precio'],
                'stock'=> $_POST['stock'],
                'nombre_archi_tapa'=> $_FILES['tapa']['name'],//le paso el nombre del archivo original para ser cargado en el libros.txt
                'isbn'=> $_POST['isbn'],
                'paginas'=> $_POST['paginas'],
                'fecha-pub'=> $_POST['fechapub'],
                'descr'=> $_POST['descr'],
                'nombre_archi_contratapa'=> $_FILES['contratapa']['name']//le paso el nombre del archivo original para ser cargado en el libros.txt
            ];
        // Formato: id|titulo|autor|genero|precio|stock|imagen|isbn|paginas|publicacion|descripcion

            move_uploaded_file($_FILES['tapa']['tmp_name'], __DIR__ . '/../../../public/assets/tapas/' . $_FILES['tapa']['name']);//guardo permanente la tapa del libro (si no hago esto se pierde en el request)
            move_uploaded_file($_FILES['contratapa']['tmp_name'], __DIR__ . '/../../../public/assets/contratapas/' . $_FILES['contratapa']['name']);//guardo permanente la contratapa del libro (si no hago esto se pierde en el request)
            $modelo->cargaLibro($libro);//se carga el libro
            $this->libroCreado();//muestro al usuario la alta del libro
        }
        else{
            $this->elLibroYaExiste();
        }
    }
    public function mostrarForm(){
        require $this->viewdir . 'crear-libro.view.php';
    }
    //falta validar que se completen todos lso campos en el formulario!!! 
    //doble capa de seguridad con JS + php
}