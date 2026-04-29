<?php

namespace PAW\App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class FormularioController
{
    private string $viewdir;

    // Simula si el libro está disponible o no
    // Cuando tenga DB esto viene de un Model
    private bool $libroDisponible = true;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';
    }

    public function index()
    {
        $errores = [];
        $exito = false;
        $mensajeExito = $this->libroDisponible ? 'Compra realizada con éxito.' : 'Reserva realizada con éxito.';
        require $this->viewdir . 'formulario.view.php';
    }

    public function process()
    {
        $nombre    = trim($_POST['nombre']    ?? '');
        $apellido  = trim($_POST['apellido']  ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $tipoEnvio = $_POST['tipo-envio']     ?? '';
        $metodoPago = $_POST['metodo-pago']   ?? '';

        $nombreDestinatario = trim($_POST['nombre-destinatario'] ?? '');
        $direccion          = trim($_POST['direccion']           ?? '');
        $pais               = $_POST['pais']                     ?? '';
        $provincia          = $_POST['provincia']                ?? '';
        $ciudad             = $_POST['ciudad']                   ?? '';
        $codigoPostal       = trim($_POST['codigo-postal']       ?? '');
        $telefono           = trim($_POST['telefono']            ?? '');

        $nombreTitular    = trim($_POST['nombre-titular']    ?? '');
        $numeroTarjeta    = trim($_POST['numero-tarjeta']    ?? '');
        $vencMes          = trim($_POST['venc-mes']          ?? '');
        $vencAnio         = trim($_POST['venc-anio']         ?? '');
        $codigoSeguridad  = trim($_POST['codigo-seguridad']  ?? '');

        $tituloLibro = trim($_GET['titulo'] ?? 'Sin título');
        $autorLibro  = trim($_GET['autor']  ?? 'Sin autor');

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

        if (!empty($errores)) {
            $exito = false;
            $mensajeExito = '';
            require $this->viewdir . 'formulario.view.php';
            return;
        }

        $tipo = $this->libroDisponible ? 'COMPRA' : 'RESERVA';
        $asunto = "[{$tipo}] {$tituloLibro} — {$nombre} {$apellido}";

        $cuerpo = "
            <h2>Nueva {$tipo} recibida</h2>
            <h3>Libro</h3>
            <p><strong>Título:</strong> {$tituloLibro}</p>
            <p><strong>Autor:</strong> {$autorLibro}</p>
            <h3>Cliente</h3>
            <p><strong>Nombre:</strong> {$nombre} {$apellido}</p>
            <p><strong>Documento:</strong> {$documento}</p>
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

        $cuerpo .= "
            <h3>Pago</h3>
            <p><strong>Método:</strong> {$metodoPago}</p>
        ";

        $exito = $this->enviarEmail($asunto, $cuerpo);
        
        if (!$exito) {
            $errores[] = 'Hubo un problema al enviar el correo de confirmación. Por favor, intente más tarde.';
            require $this->viewdir . 'formulario.view.php';
            return;
        }

        $tipoOperacion = $this->libroDisponible ? 'compra' : 'reserva';
        require $this->viewdir . 'compra-exitosa.view.php';
    }

    public function historial()
    {
        // Si no está logueado, redirigir al login
        if (!isset($_SESSION['usuario'])) {
            header('Location: /inicio-sesion');
            exit;
        }

        // Simulación de historial vacío
        $compras = []; 
        require $this->viewdir . 'mis-compras.view.php';
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
