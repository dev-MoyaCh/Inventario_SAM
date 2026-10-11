<div class="container mt-4">
    <h1 class="mb-4">📊 Dashboard</h1>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">📦 Productos</h5>
                    <h2><?= $stats['productos'] ?></h2>
                    <small>Registrados</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">📊 Stock Total</h5>
                    <h2><?= $stats['stock_total'] ?></h2>
                    <small>Unidades en inventario</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">💰 Valor Inventario</h5>
                    <h2>S/<?= number_format($stats['valor_inventario'], 2) ?></h2>
                    <small>Valor total</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">💵 Ventas Totales</h5>
                    <h2>S/<?= number_format($stats['total_ventas'], 2) ?></h2>
                    <small><?= $stats['unidades_vendidas'] ?> unidades vendidas</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📥 Ingresos</h5>
                    <h3><?= $stats['ingresos'] ?></h3>
                    <small>Unidades ingresadas</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📤 Salidas</h5>
                    <h3><?= $stats['salidas'] ?></h3>
                    <small>Unidades retiradas</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3>⚡ Accesos Rápidos</h3>
            <div class="list-group">
                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                <a href="index.php?controller=producto&action=index" class="list-group-item list-group-item-action">📦 Gestionar Productos</a>
                <?php endif; ?>
                <a href="index.php?controller=ingreso&action=registrar" class="list-group-item list-group-item-action">📥 Registrar Ingreso</a>
                <a href="index.php?controller=salida&action=registrar" class="list-group-item list-group-item-action">📤 Registrar Salida</a>
                <a href="index.php?controller=venta&action=registrar" class="list-group-item list-group-item-action">💰 Registrar Venta</a>
            </div>
        </div>
    </div>
</div>