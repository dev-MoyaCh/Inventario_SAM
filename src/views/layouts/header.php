<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: sticky;
            top: 0;
        }
        .sidebar a {
            color: white;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            border-left: 4px solid white;
        }
        .sidebar a.active {
            background: rgba(255,255,255,0.15);
            border-left: 4px solid white;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .alert {
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?controller=dashboard&action=index">
                <i class="bi bi-box-seam"></i> Grupo Tecnología SAM
            </a>
            
            <div class="d-flex align-items-center">
                <!-- Aquí se muestra el nombre del usuario si la sesión está activa -->
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i> 
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>
                </span>
                <!-- Botón de Cerrar Sesión -->
                <a href="index.php?controller=login&action=logout" 
                   class="btn btn-danger btn-sm"
                   title="Cerrar Sesión"
                   onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?');">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 sidebar p-0">
            <div class="p-3 text-center text-white border-bottom border-light">
                <h4 class="mb-0">📦 Inventario</h4>
            </div>
            <a href="index.php?controller=dashboard&action=index" class="<?= ($_GET['controller'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
            <a href="index.php?controller=producto&action=index" class="<?= in_array(($_GET['controller'] ?? ''), ['producto', 'productos'], true) ? 'active' : '' ?>"><i class="bi bi-box-seam"></i> Productos</a>
            <?php endif; ?>
            <a href="index.php?controller=ingreso&action=index" class="<?= in_array(($_GET['controller'] ?? ''), ['ingreso', 'ingresos'], true) ? 'active' : '' ?>"><i class="bi bi-box-arrow-in-down"></i> Ingresos</a>
            <a href="index.php?controller=salida&action=index" class="<?= in_array(($_GET['controller'] ?? ''), ['salida', 'salidas'], true) ? 'active' : '' ?>"><i class="bi bi-box-arrow-up"></i> Salidas</a>
            <a href="index.php?controller=venta&action=index" class="<?= in_array(($_GET['controller'] ?? ''), ['venta', 'ventas'], true) ? 'active' : '' ?>"><i class="bi bi-cash-coin"></i> Ventas</a>
            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
            <a href="index.php?controller=proveedor&action=index" class="<?= in_array(($_GET['controller'] ?? ''), ['proveedor', 'proveedores'], true) ? 'active' : '' ?>"><i class="bi bi-truck"></i> Proveedores</a>
            <a href="index.php?controller=usuarios&action=index" class="<?= in_array(($_GET['controller'] ?? ''), ['usuario', 'usuarios'], true) ? 'active' : '' ?>"><i class="bi bi-people"></i> Gestión de usuarios</a>
            <?php endif; ?>
        </nav>

        <!-- Contenido principal -->
        <main class="col-md-10 p-4">
            <?php
            // Mostrar mensajes de sesión globales
            if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
