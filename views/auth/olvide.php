<div class="contenedor olvide">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu Acceso a UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/olvide" class="formulario" method="POST" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="text"
                    id="email"
                    placeholder="Tu Email"
                    name="email"
                />
            </div>

            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
        </div>
        <a href="https://fernandomolina.es">
            <p class="design">Design by: Fernando Molina | Marketing Digital</p>
        </a>
    </div> <!-- contenedor-sm -->
</div>