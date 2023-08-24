<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<?php if(count($proyectos) === 0 ) { ?>
    <p class="no-proyectos">No hay Proyectos aún <a href="/crear-proyecto">Comienza creando uno</a></p>

<?php }  else { ?>
    <ul class="listado-proyectos">
        <?php foreach($proyectos as $proyecto) { ?>
            <div class="caja">
                
            <a href="/proyecto?id=<?php echo $proyecto->url; ?>">
                <li class="proyecto">
                        <?php echo $proyecto->proyecto; ?>
                </li>
            </a>
            <form action="/proyecto/eliminar" method="POST">
                <input type="hidden" name="id" value="<?php echo $proyecto->id; ?>"/>
                <input type="submit" class="boton btn-eliminar" value="Eliminar"/>
            </form>
            </div>
        <?php } ?>
    </ul>

<?php } ?>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>