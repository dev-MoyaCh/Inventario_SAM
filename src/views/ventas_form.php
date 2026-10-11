<div class="container mt-4">
    <h1>💰 Registrar Nueva Venta</h1>

    <div class="card mt-4">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Producto:</label>
                    <select name="ID_Producto" id="producto" class="form-select" required>
                        <option value="">— Selecciona un producto —</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?= $p['ID_Producto'] ?>" data-precio="<?= $p['Precio'] ?>" data-stock="<?= $p['Stock'] ?>">
                                <?= htmlspecialchars($p['Nombre']) ?> - $<?= number_format($p['Precio'], 2) ?> (Stock: <?= $p['Stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad:</label>
                    <input type="number" name="Cantidad" id="cantidad" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Total a pagar:</label>
                    <input type="text" id="total" class="form-control" readonly value="$0.00">
                </div>

                <button type="submit" class="btn btn-primary">💵 Registrar Venta</button>
                <a href="index.php?controller=venta&action=index" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<script>
const producto = document.getElementById('producto');
const cantidad = document.getElementById('cantidad');
const total = document.getElementById('total');

function calcularTotal() {
    const option = producto.options[producto.selectedIndex];
    const precio = parseFloat(option.dataset.precio || 0);
    const cant = parseInt(cantidad.value || 0);
    total.value = 'S/' + (precio * cant).toFixed(2);
}

producto.addEventListener('change', calcularTotal);
cantidad.addEventListener('input', calcularTotal);
</script>