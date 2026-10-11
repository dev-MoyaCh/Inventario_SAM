<?php
namespace Controllers;

use Models\UsuarioModel;

class LoginController {
    private UsuarioModel $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index(): void {
        require_once __DIR__ . '/../views/login_view.php';
    }

    public function authenticate(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $usuario = $_POST['usuario'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->usuarioModel->validarUsuario($usuario, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['ID_Usuario'];
            $_SESSION['user_name'] = $user['Nombre'];
            $_SESSION['user_role'] = $user['Rol'];
            
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        } else {
            $_SESSION['login_error'] = "Credenciales incorrectas o usuario inactivo.";
            header("Location: index.php?controller=login&action=index");
            exit;
        }
    }

    public function logout(): void {
        // Iniciar sesión si no está activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // 1. Eliminar todas las variables de sesión
        $_SESSION = array();
        
        // 2. Eliminar la cookie de sesión del navegador
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // 3. Destruir la sesión en el servidor
        session_destroy();
        
        // 4. Redirigir al login
        header("Location: index.php?controller=login&action=index");
        exit;
    }
}