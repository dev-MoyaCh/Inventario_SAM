<div class="container mt-4">
    <h1>📥 Registrar Nuevo Ingreso</h1>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Producto:</label>
            <select name="ID_Producto" class="form-select" required>
                <option value="">— Selecciona un producto —</option>
                <?php foreach ($productos as $p): ?>
                    <option value="<?= $p['ID_Producto'] ?>">
                        <?= htmlspecialchars($p['Nombre']) ?> (Stock: <?= $p['Stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Proveedor:</label>
            <select name="ID_Proveedor" class="form-select" required>
                <option value="">— Selecciona un proveedor —</option>
                <?php foreach ($proveedores as $prov): ?>
                    <option value="<?= $prov['ID_Proveedor'] ?>">
                        <?= htmlspecialchars($prov['Razon_Social']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad:</label>
            <input type="number" name="Cantidad" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-success">✅ Registrar Ingreso</button>
        <a href="index.php?controller=ingreso&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>