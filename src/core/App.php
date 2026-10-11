<?php

namespace Core;

use Exception;

/**
 * Router que interpreta la URL y despacha al controlador correcto.
 * URL esperada: index.php?controller=productos&action=index
 * Soporta: producto/productos, salida/salidas, venta/ventas, etc.
 */
class App
{
    private string $controller;
    private string $action;
    private array  $params;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $controllerSolicitado = $_GET['controller'] ?? '';
        $actionSolicitada     = $_GET['action'] ?? 'index';

        // 1. Si NO hay sesión activa
        if (!isset($_SESSION['user_id'])) {
            // Si intenta acceder a cualquier cosa que NO sea login, redirigir al login
            if ($controllerSolicitado !== 'login' && $controllerSolicitado !== '') {
                $_SESSION['login_error'] = "Debe iniciar sesión para acceder.";
                header("Location: index.php?controller=login&action=index");
                exit;
            }
            // Si está vacío o es login, permitir que cargue el login
            $this->controller = ($controllerSolicitado === '') ? 'login' : $controllerSolicitado;
        } 
        // 2. Si YA hay sesión activa
        else {
            // Si intenta acceder al login, PERO la acción NO es logout, redirigir al dashboard
            if ($controllerSolicitado === 'login' && $actionSolicitada !== 'logout') {
                header("Location: index.php?controller=dashboard&action=index");
                exit;
            }
            // Si está vacío, ir al dashboard
            $this->controller = ($controllerSolicitado === '') ? 'dashboard' : $controllerSolicitado;
        }

        $this->action = $actionSolicitada;
        $this->params = $_GET;
    }

    /**
     * Ejecuta el controlador y la acción correspondiente.
     */
    public function run(): void
    {
        try {
            $controllerName = $this->resolveControllerName($this->controller);
            $controllerClass = "Controllers\\{$controllerName}";
            $action = $this->action;

            // Verificar que la clase del controlador existe (autoload)
            if (!class_exists($controllerClass)) {
                throw new Exception("La clase del controlador '{$controllerClass}' no existe.");
            }

            $controller = new $controllerClass();

            // Verificar que el método existe
            if (!method_exists($controller, $action)) {
                throw new Exception("La acción '{$action}' no existe en {$controllerName}.");
            }

            // Ejecutar la acción
            $controller->$action();

        } catch (Exception $e) {
            http_response_code(500);
            die("<div style='padding: 20px; background: #fee; border: 1px solid #c00; color: #600; margin: 20px; border-radius: 4px;'>" .
                "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) .
                "</div>");
        }
    }

    /**
     * Resuelve el nombre del controlador desde la solicitud GET.
     * Transforma plural a singular: "productos" → "Producto"
     * Retorna con sufijo Controller: "ProductoController"
     */
    private function resolveControllerName(string $requestName): string
    {
        // Mapeo de rutas conocidas
        $controllerMap = [
            'dashboard'   => 'DashboardController',
            'usuario'     => 'UsuarioController',
            'usuarios'    => 'UsuarioController',
            'producto'    => 'ProductoController',
            'productos'   => 'ProductoController',
            'ingreso'     => 'IngresoController',
            'ingresos'    => 'IngresoController',
            'salida'      => 'SalidaController',
            'salidas'     => 'SalidaController',
            'venta'       => 'VentaController',
            'ventas'      => 'VentaController',
            'proveedor'   => 'ProveedorController',
            'proveedores' => 'ProveedorController',
            'movimiento'  => 'MovimientoController',
            'movimientos' => 'MovimientoController',
        ];

        $lowerName = strtolower(trim($requestName));

        if (isset($controllerMap[$lowerName])) {
            return $controllerMap[$lowerName];
        }

        // Si no está en el mapa, aplicar regla estándar: quitar 's' final y capitalizar
        $singular = rtrim($lowerName, 's');
        return ucfirst($singular) . 'Controller';
    }
}
