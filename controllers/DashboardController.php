<?php 

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {

    public static function index (Router $router) {

        session_start();

        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto (Router $router) {
        session_start();

        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);

            // Validación
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                // Generar una URL única
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar Proyecto
                $proyecto->guardar();

                // Redireccionar
                header('location: /proyecto?id=' . $proyecto->url);

            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto (Router $router) {
        session_start();

        isAuth();

        $token = $_GET['id'];

        if(!$token) header('location: /dashboard');
        // Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']) {
            header('location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil (Router $router) {
        session_start();

        isAuth();

        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    // Mensaje de Error
                    Usuario::setAlerta('error', 'Email no válido, cuenta ya registrada');
                    $alertas = Usuario::getAlertas();

                } else {
                    // Guardar el Registro
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = Usuario::getAlertas();
    
                    // Asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router) {
        
        session_start();
        
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)) {
                $resultado = $usuario->comprobar_password();

                if($resultado) {

                    $usuario->password = $usuario->password_nuevo;

                    // Eliminar propiedades no necesarias
                    unset($usuario->password_actual);

                    unset($usuario->nuevo);

                    // Hashear el nuevo Password
                    $usuario->hashPassword();

                    // Actualizar
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito', 'Contraseña guardada correctamente');
                        $alertas = Usuario::getAlertas();
                    }
                } else {
                    Usuario::setAlerta('error', 'Contraseña Incorrecta');
                    $alertas = Usuario::getAlertas();
                }
            }
        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function eliminar_proyecto() {
        session_start();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if($id) {
                $proyecto = Proyecto::find($id);

                if($proyecto->propietarioId === $_SESSION['id']) {
                    $proyecto->eliminar();
                }

                // Redireccionar
                header('location: /dashboard'); 
            }
           
        }
    }
}