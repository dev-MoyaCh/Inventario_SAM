<?php

namespace Controllers;

use Core\Controller;
use Models\Producto;
use Models\Venta;
use Models\Ingreso;
use Models\Salida;

class DashboardController extends Controller
{
    private Producto $producto;
    private Venta $venta;
    private Ingreso $ingreso;
    private Salida $salida;

    public function __construct() {
        $this->producto = new Producto();
        $this->venta = new Venta();
        $this->ingreso = new Ingreso();
        $this->salida = new Salida();
    }

    public function index(): void {
        $productos = $this->producto->getAll();

        $stats = [
            'productos'         => count($productos),
            'stock_total'       => array_sum(array_column($productos, 'Stock')),
            'valor_inventario'  => $this->producto->getStockValue(),
            'total_ventas'      => $this->venta->getTotalVentas(),
            'unidades_vendidas' => $this->venta->getTotalUnidades(),
            'ingresos'          => $this->ingreso->getTotalUnidades(),
            'salidas'           => $this->salida->getTotalUnidades(),
        ];

        $this->render('dashboard', ['stats' => $stats]);
    }
}
