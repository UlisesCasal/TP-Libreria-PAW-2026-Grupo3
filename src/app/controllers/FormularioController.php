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

    // Muestra el formulario de pago
    public function index()
    {
        $errores = [];
        $exito = false;
        $mensajeExito = $this->libroDisponible ? 'Compra realizada con éxito.' : 'Reserva realizada con éxito.';
        require $this->viewdir . 'formulario.view.php';
    }

    public function process()
    {
        // ── RECOLECCIÓN DE DATOS ──────────────────────────
        // Los datos llegan por POST con codificación
        // application/x-www-form-urlencoded (default de HTML forms)
        // Se usa el operador ?? para evitar errores si el campo no viene

        $nombre    = trim($_POST['nombre']    ?? '');
        $apellido  = trim($_POST['apellido']  ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $tipoEnvio = $_POST['tipo-envio']     ?? '';
        $metodoPago = $_POST['metodo-pago']   ?? '';

        // Campos de envío a domicilio — solo si eligió domicilio
        $nombreDestinatario = trim($_POST['nombre-destinatario'] ?? '');
        $direccion          = trim($_POST['direccion']           ?? '');
        $pais               = $_POST['pais']                     ?? '';
        $provincia          = $_POST['provincia']                ?? '';
        $ciudad             = $_POST['ciudad']                   ?? '';
        $codigoPostal       = trim($_POST['codigo-postal']       ?? '');
        $telefono           = trim($_POST['telefono']            ?? '');

        // Campos de tarjeta — solo si eligió tarjeta
        $nombreTitular    = trim($_POST['nombre-titular']    ?? '');
        $numeroTarjeta    = trim($_POST['numero-tarjeta']    ?? '');
        $vencMes          = trim($_POST['venc-mes']          ?? '');
        $vencAnio         = trim($_POST['venc-anio']         ?? '');
        $codigoSeguridad  = trim($_POST['codigo-seguridad']  ?? '');

        // Título y autor del libro — vienen por GET desde la página del libro
        $tituloLibro = trim($_GET['titulo'] ?? 'Sin título');
        $autorLibro  = trim($_GET['autor']  ?? 'Sin autor');

        // ── VALIDACIONES BACKEND ──────────────────────────
        // Son consistentes con los required y pattern del HTML

        $errores = [];

        // Datos personales — obligatorios siempre
        if (empty($nombre)) {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (empty($apellido)) {
            $errores[] = 'El apellido es obligatorio.';
        }
        if (empty($documento) || !preg_match('/^\d{7,8}$/', $documento)) {
            $errores[] = 'El documento debe tener 7 u 8 dígitos.';
        }

        // Lugar de envío — obligatorio
        if (empty($tipoEnvio)) {
            $errores[] = 'Debe seleccionar un tipo de envío.';
        }

        // Validaciones específicas de envío a domicilio
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

        // Método de pago — obligatorio
        if (empty($metodoPago)) {
            $errores[] = 'Debe seleccionar un método de pago.';
        }

        // Validaciones específicas de tarjeta
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

        // ── SI HAY ERRORES — volver al formulario ─────────
        if (!empty($errores)) {
            $exito = false;
            require $this->viewdir . 'formulario.view.php';
            return;
        }

        // ── ENVÍO DE EMAIL ────────────────────────────────
        $tipo = $this->libroDisponible ? 'COMPRA' : 'RESERVA';

        $asunto = "[{$tipo}] {$tituloLibro} — {$nombre} {$apellido}";

        // El cuerpo del email se puede formatear como HTML para mayor claridad
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

        // Si el envío es a domicilio, agregamos los datos de envío al cuerpo del email
        if ($tipoEnvio === 'domicilio') {
            $cuerpo .= "
                <p><strong>Destinatario:</strong> {$nombreDestinatario}</p>
                <p><strong>Dirección:</strong> {$direccion}, {$ciudad}, {$provincia}, {$pais}</p>
                <p><strong>CP:</strong> {$codigoPostal}</p>
                <p><strong>Teléfono:</strong> {$telefono}</p>
            ";
        }

        // Agregamos el método de pago al cuerpo del email
        $cuerpo .= "
            <h3>Pago</h3>
            <p><strong>Método:</strong> {$metodoPago}</p>
        ";

        $exito = $this->enviarEmail($asunto, $cuerpo); // Enviar el email y guardar el resultado (true/false) en $exito

        require $this->viewdir . 'formulario.view.php';
    }

    // Función para enviar el email utilizando PHPMailer
    private function enviarEmail(string $asunto, string $cuerpo): bool
    {
        $mail = new PHPMailer(true);

        // Configuración del servidor SMTP y credenciales (definidos en config.php)
        try {
            $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST; 
            $mail->SMTPAuth   = true; 
            $mail->Username   = SMTP_USER; 
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
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
            var_dump($mail->ErrorInfo);
            return false;
        }
    }
}