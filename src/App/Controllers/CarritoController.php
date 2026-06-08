<?php

namespace PAW\App\Controllers;

use PAW\Core\TwigEnvironment;
use PAW\Model\CarritoModel;

class CarritoController
{
    private CarritoModel $carritoModel;

    public function __construct()
    {
        $this->carritoModel = new CarritoModel();
    }

    // Muestra el carrito del usuario logueado
    public function ver()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /inicio-sesion');
            exit;
        }

        $items = $this->carritoModel->obtenerItems($_SESSION['usuario']['id']);
        $total = array_sum(array_map(
            fn($i) => (int)$i['cantidad'] * (float)$i['precio'],
            $items
        ));

        TwigEnvironment::getInstance()->render('carrito.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    // Agrega un libro al carrito (POST: libro_id, cantidad)
    public function agregar()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /inicio-sesion');
            exit;
        }

        $libroId  = (int)($_POST['libro_id']  ?? 0);
        $cantidad = (int)($_POST['cantidad']   ?? 1);

        if ($libroId <= 0 || $cantidad <= 0) {
            header('Location: /catalogo');
            exit;
        }

        $this->carritoModel->agregarItem($_SESSION['usuario']['id'], $libroId, $cantidad);

        header('Location: /carrito');
        exit;
    }

    // Elimina un libro del carrito (POST: libro_id)
    // Si la petición es AJAX (X-Requested-With: XMLHttpRequest) devuelve JSON en lugar de redirigir
    public function eliminar()
    {
        $esAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if (!isset($_SESSION['usuario'])) {
            if ($esAjax) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'No autenticado']);
                exit;
            }
            header('Location: /inicio-sesion');
            exit;
        }

        $libroId = (int)($_POST['libro_id'] ?? 0);

        if ($libroId <= 0) {
            if ($esAjax) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'libro_id inválido']);
                exit;
            }
            header('Location: /carrito');
            exit;
        }

        $this->carritoModel->eliminarItem($_SESSION['usuario']['id'], $libroId);

        if ($esAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        }

        header('Location: /carrito');
        exit;
    }
}
