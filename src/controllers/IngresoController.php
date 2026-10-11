<?php

namespace Controllers;

use Core\Controller;
use Models\Ingreso;
use Models\Producto;
use Models\Proveedor;
use Exception;

class IngresoController extends Controller
{
    private Ingreso $ingreso;
    private Producto $producto;
    private Proveedor $proveedor;

    public function __construct()
    {
        $this->ingreso = new Ingreso();
        $this->producto = new Producto();
        $this->proveedor = new Proveedor();
    }

    public function index(): void
    {
        $ingresos = $this->ingreso->getAll();
        $this->render('ingresos', ['ingresos' => $ingresos, 'productos' => $this->producto->getAll(), 'proveedores' => $this->proveedor->getAll()]);
    }

    public function actualizar(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?controller=ingreso&action=index'); }
        try { $id=(int)($_POST['ID_Ingreso']??0); $producto=(int)($_POST['ID_Producto']??0); $proveedor=(int)($_POST['ID_Proveedor']??0); $cantidad=(int)($_POST['Cantidad']??0);
            if ($id<=0||$producto<=0||$proveedor<=0||$cantidad<=0) throw new Exception('Todos los campos deben ser positivos.');
            $this->ingreso->actualizar($id,$producto,$proveedor,$cantidad); $_SESSION['mensaje']='Ingreso actualizado y stock recalculado.';
        } catch (Exception $e) { $_SESSION['error']='No se pudo actualizar el ingreso: '.$e->getMessage(); }
        $this->redirect('index.php?controller=ingreso&action=index');
    }
    public function eliminar(): void
    {
        $this->requireAdmin(); if ($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('index.php?controller=ingreso&action=index');
        try { $this->ingreso->eliminar((int)($_POST['id']??0)); $_SESSION['mensaje']='Ingreso eliminado y stock recalculado.'; } catch (Exception $e) { $_SESSION['error']='No se pudo eliminar el ingreso: '.$e->getMessage(); }
        $this->redirect('index.php?controller=ingreso&action=index');
    }

    public function registrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $productoId = (int) ($_POST['ID_Producto'] ?? 0);
                $proveedorId = (int) ($_POST['ID_Proveedor'] ?? 0);
                $cantidad = (int) ($_POST['Cantidad'] ?? 0);
                
                if ($productoId <= 0 || $proveedorId <= 0 || $cantidad <= 0) {
                    throw new Exception('Todos los campos son requeridos y deben ser positivos');
                }

                $this->ingreso->registrar($productoId, $proveedorId, $cantidad);
                $_SESSION['mensaje'] = "✅ Ingreso registrado correctamente";
            } catch (Exception $e) {
                $_SESSION['error'] = "❌ Error: " . $e->getMessage();
            }

            $this->redirect('index.php?controller=ingreso&action=index');
            return;
        }

        $productos = $this->producto->getAll();
        $proveedores = $this->proveedor->getAll();
        $this->render('ingresos_form', [
            'productos'   => $productos,
            'proveedores' => $proveedores,
        ]);
    }
}
