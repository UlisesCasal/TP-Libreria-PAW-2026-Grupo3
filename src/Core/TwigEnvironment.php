<?php

namespace PAW\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigEnvironment
{
    private static ?self $instance = null;
    private Environment $twig;

    private function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../App/views/');
        $this->twig = new Environment($loader, [
            'cache' => false,
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function render(string $template, array $data = []): void
    {
        $this->twig->addGlobal('_session', $_SESSION ?? []);
        $this->twig->addGlobal('_server', $_SERVER);
        $this->twig->addGlobal('_get', $_GET);

        echo $this->twig->render($template, $data);
    }
}
