<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css"> <link rel="stylesheet" href="assets/css/formulario.css">
    <title>Formulario de compra</title>
</head>

<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>

<main>
    <section aria-label="Formulario de pago" class="seccion-pago">
        <h2>Formulario de pago</h2>

        <?php if (!empty($errores)): ?>
            <ul class="errores">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($exito): ?>
            <p class="exito">
                <?= $mensajeExito ?> Te enviamos un email de confirmación.
            </p>
        <?php endif; ?>

        <form action="/formulario?titulo=El+nombre+del+libro&autor=El+Autor" method="post" class="form-pago">

            <fieldset>
                <legend>1. Datos personales</legend>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan" required>
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" placeholder="Ej: González" required>
                <label for="documento">Número de documento:</label>
                <input type="text" id="documento" name="documento" placeholder="Ej: 12345678" required>
            </fieldset>

            <fieldset>
                <legend>2. Lugar de envío</legend>
                <div class="opcion">
                    <input type="radio" id="envio-domicilio" name="tipo-envio" value="domicilio" required checked>
                    <label for="envio-domicilio">Envío a domicilio</label>
                </div>

                <fieldset class="fieldset-anidado fieldset-direccion-entrega">
                    <legend>Dirección de entrega</legend>
                    <label for="nombre-destinatario">Nombre completo del destinatario:</label>
                    <input type="text" id="nombre-destinatario" name="nombre-destinatario" placeholder="Nombre y apellido" required>
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Ej: Calle Principal 123" required>
                    
                    <div class="fila-doble">
                        <div>
                            <label for="pais">País:</label>
                            <select id="pais" name="pais" required>
                                <option value="">Selecciona tu país</option>
                                <option value="argentina">Argentina</option>
                                <option value="chile">Chile</option>
                                <option value="uruguay">Uruguay</option>
                                <option value="brasil">Brasil</option>
                                <option value="paraguay">Paraguay</option>
                            </select>
                        </div>
                        <div>
                            <label for="codigo-postal">Código postal:</label>
                            <input type="text" id="codigo-postal" name="codigo-postal" placeholder="Ej 6600" pattern="\d{4,5}">
                        </div>
                    </div>

                    <div class="fila-doble">
                        <div>
                            <label for="provincia">Provincia:</label>
                            <select id="provincia" name="provincia" required>
                                <option value="">Selecciona tu provincia</option>
                                <option value="buenos-aires">Buenos Aires</option>
                                <option value="cordoba">Córdoba</option>
                                <option value="santa-fe">Santa Fe</option>
                                <option value="mendoza">Mendoza</option>
                                <option value="tucuman">Tucumán</option>
                            </select>
                        </div>
                        <div>
                            <label for="ciudad">Ciudad:</label>
                            <select id="ciudad" name="ciudad" required>
                                <option value="">Selecciona tu ciudad</option>
                                <option value="capital-federal">Capital Federal</option>
                                <option value="cordoba">Córdoba</option>
                                <option value="rosario">Rosario</option>
                                <option value="mendoza">Mendoza</option>
                                <option value="san-miguel-de-tucuman">San Miguel de Tucumán</option>
                            </select>
                        </div>
                    </div>
                    
                    <label for="telefono">Número de teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="Ej +54 9 11 5555-5555">
                </fieldset>

                <div class="opcion">
                    <input type="radio" id="envio-libreria" name="tipo-envio" value="libreria" required>
                    <label for="envio-libreria">Retiro en sucursal</label>
                </div>
                <div class="opcion">
                    <input type="radio" id="envio-punto" name="tipo-envio" value="punto-entrega" required>
                    <label for="envio-punto">Coordinar punto de entrega</label>
                </div>
            </fieldset>

            <fieldset>
                <legend>3. Método de pago</legend>
                <div class="opcion">
                    <input type="radio" id="pago-tarjeta" name="metodo-pago" value="tarjeta" required checked>
                    <label for="pago-tarjeta">Tarjeta de crédito/débito</label>
                </div>

                <fieldset class="fieldset-anidado fieldset-datos-tarjeta">
                    <legend>Datos de la tarjeta</legend>
                    <label for="nombre-titular">Nombre del titular:</label>
                    <input type="text" id="nombre-titular" name="nombre-titular" placeholder="Nombre y apellido como figura en la tarjeta">
                    <label for="numero-tarjeta">Número de tarjeta:</label>
                    <input type="text" id="numero-tarjeta" name="numero-tarjeta" placeholder="Ej 1234 5678 9012 3456">
                    
                    <div class="fila-doble">
                        <div>
                            <label for="venc-mes">Fecha de expiración:</label>
                            <div class="grupo-fecha-expiracion">
                                <input type="number" id="venc-mes" name="venc-mes" placeholder="MM" min="1" max="12" required>
                                <input type="number" id="venc-anio" name="venc-anio" placeholder="AA" min="23" max="30" required>
                            </div>
                        </div>
                        <div>
                            <label for="codigo-seguridad">Código de seguridad (CVV):</label>
                            <input type="text" id="codigo-seguridad" name="codigo-seguridad" placeholder="Ej 123" pattern="\d{3,4}" required>
                        </div>
                    </div>

                    <div class="opcion">
                        <input type="checkbox" id="guardar-tarjeta" name="guardar-tarjeta">
                        <label for="guardar-tarjeta">Guardar tarjeta para futuras compras</label>
                    </div>
                </fieldset>

                <div class="opcion">
                    <input type="radio" id="pago-pago-facil" name="metodo-pago" value="pago-facil" required>
                    <label for="pago-pago-facil">Pago Fácil</label>
                </div>
                <div class="opcion">
                    <input type="radio" id="pago-rapipago" name="metodo-pago" value="rapipago" required>
                    <label for="pago-rapipago">Rapipago</label>
                </div>
                <div class="opcion">
                    <input type="radio" id="pago-transferencia" name="metodo-pago" value="transferencia" required>
                    <label for="pago-transferencia">Transferencia bancaria</label>
                </div>
            </fieldset>

            <button type="submit" class="btn-finalizar">Finalizar compra</button>
        </form>

        <aside class="resumen-pago">
            <h3>Resumen de pago</h3>
            <ul>
                <li><span>Subtotal</span><span>$30.00</span></li>
                <li><span>Envío</span><span>$5.00</span></li>
                <li><span>Total</span><span>$35.00</span></li>
            </ul>
            <p><small>Fecha estimada de entrega: 5-7 días hábiles</small></p>
        </aside>
    </section>
</main>

            <!-- Sección de contacto -->
    <?php require __DIR__ . '/parts/footer.view.php'; ?>

</body>

</html>