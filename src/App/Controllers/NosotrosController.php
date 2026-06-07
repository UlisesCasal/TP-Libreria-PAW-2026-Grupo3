<?php

namespace PAW\App\Controllers;

use PAW\Core\TwigEnvironment;

class NosotrosController{
    public function mostrar_nosotros(){
        TwigEnvironment::getInstance()->render('nosotros.twig', []);
    }
    
}