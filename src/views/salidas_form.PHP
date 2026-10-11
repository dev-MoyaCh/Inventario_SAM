<div class="container mt-4">
    <h1>📤 Registrar Nueva Salida</h1>

    <div class="card mt-4">
        <div class="card-body">
            <form method="POST">
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
                    <label class="form-label">Cantidad:</label>
                    <input type="number" name="Cantidad" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Razón de la salida:</label>
                    <input type="text" name="Razon" class="form-control" maxlength="50" required 
                           placeholder="Ej: Merma, Donación, Uso interno">
                </div>

                <button type="submit" class="btn btn-warning">✅ Registrar Salida</button>
                <a href="index.php?controller=salida&action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>