<?php

namespace PAW\App\Controllers;

use PAW\Core\TwigEnvironment;
use PAW\Model\CarritoModel;
use PAW\Model\PedidoModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ReservasController
{
    private PedidoModel  $pedidoModel;
    private CarritoModel $carritoModel;

    public function __construct()
    {
        $this->pedidoModel  = new PedidoModel();
        $this->carritoModel = new CarritoModel();
    }

    // Listado de pedidos — solo accesible para el personal
    public function getAll()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'personal') {
            TwigEnvironment::getInstance()->render('pedidos.twig', [
                'error' => 'No tiene permisos para acceder a esta sección.',
            ]);
            return;
        }

        $pedidos = $this->pedidoModel->obtenerTodos();
        TwigEnvironment::getInstance()->render('pedidos.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    // Muestra el formulario de compra
    public function mostrarFormulario()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: /inicio-sesion');
            exit;
        }

        $usuario  = $_SESSION['usuario'];
        $items    = $this->carritoModel->obtenerItems($usuario['id']);

        if (empty($items)) {
            header('Location: /carrito');
            exit;
        }

        $subtotal = $this->calcularSubtotal($items);

        TwigEnvironment::getInstance()->render('formulario.twig', [
            'errores'  => [],
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    // Procesa el formulario de compra
    public function processCompra()
    {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['usuario'])) {
            header('Location: /inicio-sesion');
            exit;
        }

        $usuario = $_SESSION['usuario'];

        // Leer datos del formulario
        $nombre    = trim($_POST['nombre']    ?? '');
        $apellido  = trim($_POST['apellido']  ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $tipoEnvio  = $_POST['tipo-envio']   ?? '';
        $metodoPago = $_POST['metodo-pago']  ?? '';

        $nombreDestinatario = trim($_POST['nombre-destinatario'] ?? '');
        $direccion          = trim($_POST['direccion']           ?? '');
        $pais               = $_POST['pais']                     ?? '';
        $provincia          = $_POST['provincia']                ?? '';
        $ciudad             = $_POST['ciudad']                   ?? '';
        $codigoPostal       = trim($_POST['codigo-postal']       ?? '');
        $telefono           = trim($_POST['telefono']            ?? '');

        $nombreTitular   = trim($_POST['nombre-titular']   ?? '');
        $numeroTarjeta   = trim($_POST['numero-tarjeta']   ?? '');
        $vencMes         = trim($_POST['venc-mes']         ?? '');
        $vencAnio        = trim($_POST['venc-anio']        ?? '');
        $codigoSeguridad = trim($_POST['codigo-seguridad'] ?? '');

        // Validaciones
        $errores = [];

        if (empty($nombre)) {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (empty($apellido)) {
            $errores[] = 'El apellido es obligatorio.';
        }
        if (empty($documento) || !preg_match('/^\d{7,8}$/', $documento)) {
            $errores[] = 'El documento debe tener 7 u 8 dígitos.';
        }
        if (empty($tipoEnvio)) {
            $errores[] = 'Debe seleccionar un tipo de envío.';
        }

        if ($tipoEnvio === 'domicilio') {
            if (empty($nombreDestinatario)) {
                $errores[] = 'El nombre del destinatario es obligatorio.';
            }
            if (empty($direccion)) {
                $errores[] = 'La dirección es obligatoria.';
            }
            if (empty($pais)) {
                $errores[] = 'El país es obligatorio.';
            }
            if (empty($provincia)) {
                $errores[] = 'La provincia es obligatoria.';
            }
            if (empty($ciudad)) {
                $errores[] = 'La ciudad es obligatoria.';
            }
            if (!empty($codigoPostal) && !preg_match('/^\d{4,5}$/', $codigoPostal)) {
                $errores[] = 'El código postal debe tener 4 o 5 dígitos.';
            }
        }

        if (empty($metodoPago)) {
            $errores[] = 'Debe seleccionar un método de pago.';
        }

        if ($metodoPago === 'tarjeta') {
            if (empty($nombreTitular)) {
                $errores[] = 'El nombre del titular es obligatorio.';
            }
            if (!preg_match('/^[\d\s]{13,19}$/', $numeroTarjeta)) {
                $errores[] = 'El número de tarjeta debe tener entre 13 y 19 dígitos.';
            }
            if (empty($vencMes) || $vencMes < 1 || $vencMes > 12) {
                $errores[] = 'El mes de vencimiento es inválido.';
            }
            if (empty($vencAnio) || $vencAnio < 23 || $vencAnio > 30) {
                $errores[] = 'El año de vencimiento es inválido.';
            }
            if (!preg_match('/^\d{3,4}$/', $codigoSeguridad)) {
                $errores[] = 'El CVV debe tener 3 o 4 dígitos.';
            }
        }

        // Verificar que el carrito no esté vacío
        $items = $this->carritoModel->obtenerItems($usuario['id']);
        if (empty($items)) {
            $errores[] = 'El carrito está vacío. Agregue libros antes de confirmar la compra.';
        }

        if (!empty($errores)) {
            TwigEnvironment::getInstance()->render('formulario.twig', [
                'errores'  => $errores,
                'items'    => $items,
                'subtotal' => $this->calcularSubtotal($items),
            ]);
            return;
        }

        // Armar items para PedidoModel::crear()
        $itemsPedido = array_map(fn($item) => [
            'libro_id'        => (int)$item['libro_id'],
            'cantidad'        => (int)$item['cantidad'],
            'precio_unitario' => (float)$item['precio'],
        ], $items);

        $datosPedido = [
            'tipo_envio'          => $tipoEnvio,
            'metodo_pago'         => $metodoPago,
            'nombre_destinatario' => $nombreDestinatario,
            'direccion'           => $direccion,
            'ciudad'              => $ciudad,
            'provincia'           => $provincia,
            'pais'                => $pais,
            'codigo_postal'       => $codigoPostal,
            'telefono'            => $telefono,
        ];

        // Persistir pedido e items en la BD (transacción interna en PedidoModel)
        try {
            $this->pedidoModel->crear($usuario['id'], $datosPedido, $itemsPedido);
        } catch (\Throwable $e) {
            error_log('Error al crear pedido: ' . $e->getMessage());
            TwigEnvironment::getInstance()->render('formulario.twig', [
                'errores'  => ['Ocurrió un error al procesar la compra. Intente nuevamente.'],
                'items'    => $items,
                'subtotal' => $this->calcularSubtotal($items),
            ]);
            return;
        }

        // Enviar mail al personal con el detalle del pedido
        $total = array_sum(array_map(
            fn($i) => (int)$i['cantidad'] * (float)$i['precio'],
            $items
        ));

        $cuerpoItems = '';
        foreach ($items as $item) {
            $subtotal     = (int)$item['cantidad'] * (float)$item['precio'];
            $cuerpoItems .= "<li>{$item['titulo']} — {$item['autor']}"
                          . " x{$item['cantidad']} = $" . number_format($subtotal, 2, ',', '.') . "</li>";
        }

        $asunto = "[COMPRA] {$nombre} {$apellido} — " . count($items) . " libro(s)";
        $cuerpo = "
            <h2>Nueva compra recibida</h2>
            <h3>Cliente</h3>
            <p><strong>Nombre:</strong> {$nombre} {$apellido}</p>
            <p><strong>Documento:</strong> {$documento}</p>
            <p><strong>Email:</strong> {$usuario['email']}</p>
            <h3>Libros</h3>
            <ul>{$cuerpoItems}</ul>
            <p><strong>Total: $" . number_format($total, 2, ',', '.') . "</strong></p>
            <h3>Envío</h3>
            <p><strong>Tipo:</strong> {$tipoEnvio}</p>
        ";

        if ($tipoEnvio === 'domicilio') {
            $cuerpo .= "
                <p><strong>Destinatario:</strong> {$nombreDestinatario}</p>
                <p><strong>Dirección:</strong> {$direccion}, {$ciudad}, {$provincia}, {$pais}</p>
                <p><strong>CP:</strong> {$codigoPostal}</p>
                <p><strong>Teléfono:</strong> {$telefono}</p>
            ";
        }

        $cuerpo .= "<h3>Pago</h3><p><strong>Método:</strong> {$metodoPago}</p>";

        $this->enviarEmail($asunto, $cuerpo);

        // Vaciar el carrito del usuario
        $this->carritoModel->vaciarCarrito($usuario['id']);

        // Mostrar vista de éxito
        TwigEnvironment::getInstance()->render('compra-exitosa.twig', [
            'tipoOperacion' => 'compra',
        ]);
    }

    private function calcularSubtotal(array $items): float
    {
        return (float)array_sum(array_map(
            fn($i) => (int)$i['cantidad'] * (float)$i['precio'],
            $items
        ));
    }

    private function enviarEmail(string $asunto, string $cuerpo): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_REMITENTE, MAIL_NOMBRE);
            $mail->addAddress(MAIL_DESTINATARIO);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpo;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log($mail->ErrorInfo);
            return false;
        }
    }
}
