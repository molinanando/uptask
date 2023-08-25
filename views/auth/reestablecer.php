<div class="contenedor reestablecer">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Escribe tu Nueva Contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if($mostrar) { ?>

        <form class="formulario" method="POST">
            <div class="campo">
                <label for="password">Contraseña</label>
                <input 
                    type="password"
                    id="password"
                    placeholder="Tu Contraseña"
                    name="password"
                />
            </div>

            <input type="submit" class="boton" value="Guardar Contraseña">
        </form>

        <?php } ?>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
        </div>
        <a href="https://fernandomolina.es">
            <p class="design">Design by: Fernando Molina | Marketing Digital</p>
        </a>
    </div> <!-- contenedor-sm -->
</div>