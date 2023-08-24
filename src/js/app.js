const mobileMenuBtn = document.querySelector('#mobile-menu');
const cerrarMenuBtn = document.querySelector('#cerrar-menu');
const sidebar = document.querySelector('.sidebar');

if(mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function() {
        sidebar.classList.add('mostrar');
    });
}

if(cerrarMenuBtn) {
    cerrarMenuBtn.addEventListener('click', function() {
        sidebar.classList.add('ocultar');

        setTimeout(() => {
            sidebar.classList.remove('mostrar');
            sidebar.classList.remove('ocultar');
        }, 500);
    });
}

(function() {
    const nombreTarea = document.querySelector("body > div.dashboard > div > div.contenido > ul > div:nth-child(3) > a > li");
    // Reviso si la clase .btn-eliminar existe
    if(document.querySelector('.btn-eliminar')) {
        const btnEliminar = document.querySelectorAll('.btn-eliminar');

        // Itero sobre los resultados
        btnEliminar.forEach( btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Selecciono el formulario del botón
                const formulario = btn.parentNode;

                // Selecciono el contenedor 'caja' más cercano y luego busco el nombre de la tarea
                const nombreTarea = btn.closest('.caja').querySelector('.proyecto').textContent.trim();

                // Alerta Sweetalert
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `Estás a punto de eliminar el proyecto "${nombreTarea}". ¡No podrás deshacerlo!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: '¡Sí, eliminar!'
                }).then((result) => {
                    // Válido el resultado, si es true hago el submit al formulario
                    if(result.value) {
                        formulario.submit();
                    }
                })
            });
        });

    }
})();