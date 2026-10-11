<?php
namespace Models;

use Config\Database;
use PDO;

class UsuarioModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listar(): array {
        $stmt = $this->db->query("SELECT ID_Usuario, Nombre, Usuario, Rol, Estado FROM usuarios ORDER BY ID_Usuario DESC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT ID_Usuario, Nombre, Usuario, Rol, Estado FROM usuarios WHERE ID_Usuario = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function crear(string $nombre, string $usuario, string $password, string $rol): void {
        $stmt = $this->db->prepare("INSERT INTO usuarios (Nombre, Usuario, Password, Rol, Estado) VALUES (:nombre, :usuario, :password, :rol, 1)");
        $stmt->execute([
            'nombre' => trim($nombre),
            'usuario' => trim($usuario),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'rol' => $rol,
        ]);
    }

    public function actualizar(int $id, string $nombre, string $usuario, string $rol, ?string $password = null): void {
        if ($password !== null && $password !== '') {
            $stmt = $this->db->prepare("UPDATE usuarios SET Nombre = :nombre, Usuario = :usuario, Rol = :rol, Password = :password WHERE ID_Usuario = :id");
            $stmt->execute(['nombre' => trim($nombre), 'usuario' => trim($usuario), 'rol' => $rol, 'password' => password_hash($password, PASSWORD_DEFAULT), 'id' => $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE usuarios SET Nombre = :nombre, Usuario = :usuario, Rol = :rol WHERE ID_Usuario = :id");
            $stmt->execute(['nombre' => trim($nombre), 'usuario' => trim($usuario), 'rol' => $rol, 'id' => $id]);
        }
    }

    public function cambiarEstado(int $id, int $estado): void {
        $stmt = $this->db->prepare("UPDATE usuarios SET Estado = :estado WHERE ID_Usuario = :id");
        $stmt->execute(['estado' => $estado, 'id' => $id]);
    }

    public function validarUsuario(string $usuario, string $password): ?array {
        $stmt = $this->db->prepare("SELECT ID_Usuario, Nombre, Usuario, Password, Rol FROM usuarios WHERE Usuario = :usuario AND Estado = 1");
        $stmt->execute(['usuario' => $usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['Password'])) {
            unset($user['Password']);
            return $user;
        }

        return null;
    }
}