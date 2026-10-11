<?php

namespace Core;

/**
 * Clase abstracta base para todos los controladores.
 * Proporciona métodos para renderizar vistas y redirigir.
 */
abstract class Controller
{
    /**
     * Renderiza una vista dentro del layout.
     *
     * @param string $view Nombre de la vista (sin .php)
     * @param array  $data Datos que se pasarán a la vista
     */
    protected function render(string $view, array $data = []): void
    {
        // Extraer variables para que estén disponibles en la vista
        extract($data);

        $viewPath = __DIR__ . "/../views/{$view}.php";

        if (!file_exists($viewPath)) {
            die("Error: La vista '{$view}' no existe.");
        }

        // Incluir layout completo
        require __DIR__ . '/../views/layouts/header.php';
        require $viewPath;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    /** Restringe acciones sensibles al rol administrador. */
    protected function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=login&action=index');
        }
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            $_SESSION['error'] = 'No tienes permisos para realizar esta acción.';
            $this->redirect('index.php?controller=dashboard&action=index');
        }
    }

    /**
     * Redirige a otra URL.
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}