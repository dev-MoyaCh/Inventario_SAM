<?php
namespace Controllers;

use Core\Controller;
use Models\UsuarioModel;
use PDOException;
use Throwable;

/** Gestión de cuentas reservada exclusivamente al administrador. */
class UsuarioController extends Controller
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=login&action=index');
        }
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            $_SESSION['error'] = 'No tienes permisos para gestionar usuarios.';
            $this->redirect('index.php?controller=dashboard&action=index');
        }
    }

    public function index(): void
    {
        $this->render('usuarios', ['usuarios' => $this->model->listar()]);
    }

    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=usuarios&action=index');
        }
        $nombre = trim($_POST['Nombre'] ?? '');
        $usuario = trim($_POST['Usuario'] ?? '');
        $password = $_POST['Password'] ?? '';
        $rol = $_POST['Rol'] ?? 'usuario';

        if ($nombre === '' || $usuario === '' || $password === '') {
            $_SESSION['error'] = 'Completa todos los campos obligatorios.';
        } elseif (mb_strlen($nombre) > 50 || mb_strlen($usuario) > 50 || !in_array($rol, ['admin', 'usuario'], true) || strlen($password) < 8) {
            $_SESSION['error'] = 'Datos inválidos. La contraseña debe tener al menos 8 caracteres.';
        } else {
            try {
                $this->model->crear($nombre, $usuario, $password, $rol);
                $_SESSION['mensaje'] = 'Usuario registrado correctamente.';
            } catch (PDOException $e) {
                $_SESSION['error'] = (string)$e->getCode() === '23000' ? 'Ese nombre de usuario ya existe.' : 'No se pudo registrar el usuario.';
            }
        }
        $this->redirect('index.php?controller=usuarios&action=index');
    }

    public function editar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=usuarios&action=index');
        }
        $id = (int)($_POST['ID_Usuario'] ?? 0);
        $nombre = trim($_POST['Nombre'] ?? '');
        $usuario = trim($_POST['Usuario'] ?? '');
        $password = $_POST['Password'] ?? '';
        $rol = $_POST['Rol'] ?? '';
        if ($id <= 0 || $nombre === '' || $usuario === '' || mb_strlen($nombre) > 50 || mb_strlen($usuario) > 50 || !in_array($rol, ['admin', 'usuario'], true) || ($password !== '' && strlen($password) < 8)) {
            $_SESSION['error'] = 'Verifica los datos. La contraseña nueva debe tener al menos 8 caracteres.';
        } elseif ($id === (int)$_SESSION['user_id'] && $rol !== 'admin') {
            $_SESSION['error'] = 'No puedes quitarte el rol de administrador mientras tienes la sesión iniciada.';
        } else {
            try {
                $this->model->actualizar($id, $nombre, $usuario, $rol, $password !== '' ? $password : null);
                if ($id === (int)$_SESSION['user_id']) {
                    $_SESSION['user_name'] = $nombre;
                    $_SESSION['user_role'] = $rol;
                }
                $_SESSION['mensaje'] = 'Usuario actualizado correctamente.';
            } catch (PDOException $e) {
                $_SESSION['error'] = (string)$e->getCode() === '23000' ? 'Ese nombre de usuario ya existe.' : 'No se pudo actualizar el usuario.';
            }
        }
        $this->redirect('index.php?controller=usuarios&action=index');
    }

    public function cambiarEstado(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=usuarios&action=index');
        }
        $id = (int)($_POST['ID_Usuario'] ?? 0);
        $estado = (int)($_POST['Estado'] ?? 0);
        if ($id <= 0 || !in_array($estado, [0, 1], true)) {
            $_SESSION['error'] = 'Solicitud inválida.';
        } elseif ($id === (int)$_SESSION['user_id'] && $estado === 0) {
            $_SESSION['error'] = 'No puedes desactivar tu propia cuenta.';
        } else {
            $this->model->cambiarEstado($id, $estado);
            $_SESSION['mensaje'] = $estado === 1 ? 'Usuario activado.' : 'Usuario desactivado.';
        }
        $this->redirect('index.php?controller=usuarios&action=index');
    }
}
