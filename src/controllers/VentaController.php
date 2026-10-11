<?php

namespace Controllers;

use Core\Controller;
use Models\Venta;
use Models\Producto;
use Exception;

class VentaController extends Controller
{
    private Venta $model;
    private Producto $producto;

    public function __construct()
    {
        $this->model = new Venta();
        $this->producto = new Producto();
    }

    public function index(): void {
        $ventas = $this->model->getAll();
        $this->render('ventas', ['ventas' => $ventas, 'productos' => $this->producto->getAll()]);
    }

    public function actualizar(): void
    {
        $this->requireAdmin(); if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('index.php?controller=venta&action=index');
        try { $id=(int)($_POST['ID_Venta']??0); $producto=(int)($_POST['ID_Producto']??0); $cantidad=(int)($_POST['Cantidad']??0); if($id<=0||$producto<=0||$cantidad<=0) throw new Exception('Producto y cantidad deben ser positivos.');
            $this->model->actualizar($id,$producto,$cantidad); $_SESSION['mensaje']='Venta actualizada y stock recalculado.';
        } catch (Exception $e) { $_SESSION['error']='No se pudo actualizar la venta: '.$e->getMessage(); }
        $this->redirect('index.php?controller=venta&action=index');
    }
    public function eliminar(): void
    {
        $this->requireAdmin(); if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('index.php?controller=venta&action=index');
        try { $this->model->eliminar((int)($_POST['id']??0)); $_SESSION['mensaje']='Venta eliminada y stock restituido.'; } catch (Exception $e) { $_SESSION['error']='No se pudo eliminar la venta: '.$e->getMessage(); }
        $this->redirect('index.php?controller=venta&action=index');
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $productoId = (int) ($_POST['ID_Producto'] ?? 0);
                $cantidad = (int) ($_POST['Cantidad'] ?? 0);
                
                if ($productoId <= 0 || $cantidad <= 0) {
                    throw new Exception('Producto y cantidad son requeridos');
                }

                // Calcular total
                $producto = $this->producto->findById($productoId);
                if (!$producto) {
                    throw new Exception('El producto no existe');
                }
                $total = $cantidad * $producto['Precio'];

                $this->model->registrar($productoId, $cantidad, $total);
                $_SESSION['mensaje'] = "✅ Venta registrada correctamente. Total: $$total";
            } catch (Exception $e) {
                $_SESSION['error'] = "❌ Error: " . $e->getMessage();
            }

            $this->redirect('index.php?controller=venta&action=index');
            return;
        }

        $productos = $this->producto->getAll();
        $this->render('ventas_form', ['productos' => $productos]);
    }
}
