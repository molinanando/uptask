<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login (Router $router) {
        
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                // Verificar que el Usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                } else {
                    // El Usuario existe
                    if(password_verify($_POST['password'], $usuario->password)) {
                        // Iniciar Sesión
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        header('location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
                
        }
        $alertas = Usuario::getAlertas();
        // Render a la Vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout () {
        session_start();
        $_SESSION = [];
        header('location: /');

    }

    public static function crear (Router $router) {
        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            $existeUsuario = Usuario::where('email', $usuario->email);

            if(empty($alertas)) {
                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear Password
                    $usuario->hashPassword();

                    // Eliminar Password2
                    unset($usuario->password2);

                    // Generar Token
                    $usuario->crearToken();

                    // Crear un Nuevo Usuario
                    $resultado = $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if($resultado) {
                        header('location: /mensaje');
                    }
                }
            }
            
        }

        // Render a la Vista
        $router->render('auth/crear', [
        'titulo' => 'Crear Cuenta',
        'usuario' => $usuario,
        'alertas' => $alertas
        ]);
    }

    public static function olvide (Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado === "1") {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el usuario
                    $usuario->guardar();

                    // Enviar el Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu Email');
                } else {
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();
        // Muestra la Vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvidé mi Contraseña',
            'alertas' => $alertas
            ]);
    }

    public static function reestablecer (Router $router) {

        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('location: /');

        // Identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Añadir la nueva Contraseña
            $usuario ->sincronizar($_POST);

            // Validar la Contraseña
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // Hashear la Contraseña
                $usuario->hashPassword();

                // Eliminar el Token
                $usuario->token = null;

                // Guardar el Usuario
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado) {
                    header('location: /');
                }

            }
        }

        $alertas = Usuario::getAlertas();
        // Muestra la Vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje (Router $router) {
       // Muestra la Vista
       $router->render('auth/mensaje', [
        'titulo' => 'Cuenta Creada Exitosamente'
    ]);
    }

    public static function confirmar (Router $router) {

       $token = s($_GET['token']);
       
       if(!$token) header('location: /');

        // Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // No se encontró un usuario con ese token
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = '';
            unset($usuario->password2);

            // Guardar en la BD
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();

        // Muestra la Vista
       $router->render('auth/confirmar', [
        'titulo' => 'Confirma tu cuenta UpTask',
        'alertas' => $alertas
    ]);
    }

}