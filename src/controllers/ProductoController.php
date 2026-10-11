<?php

namespace Controllers;

use Core\Controller;
use Models\Producto;
use Exception;

class ProductoController extends Controller
{
    private Producto $model;

    public function __construct()
    {
        $this->model = new Producto();
        $this->requireAdmin();
    }

    public function index(): void
    {
        $productos = $this->model->getAll();
        $this->render('productos', ['productos' => $productos]);
    }

    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validar datos requeridos
                if (empty($_POST['Nombre']) || empty($_POST['Stock']) || empty($_POST['Precio'])) {
                    throw new Exception('Todos los campos son requeridos');
                }

                $stock = (int) $_POST['Stock'];
                $precio = (float) $_POST['Precio'];

                if ($stock < 0 || $precio < 0) {
                    throw new Exception('Stock y Precio deben ser valores positivos');
                }

                $this->model->create([
                    'Nombre' => htmlspecialchars(trim($_POST['Nombre'])),
                    'Stock'  => $stock,
                    'Precio' => $precio,
                ]);

                $_SESSION['mensaje'] = '✅ Producto creado correctamente';
            } catch (Exception $e) {
                $_SESSION['error'] = '❌ Error: ' . $e->getMessage();
            }

            $this->redirect('index.php?controller=producto&action=index');
            return;
        }
    }

    public function actualizar(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?controller=producto&action=index'); }
        $id=(int)($_POST['ID_Producto'] ?? 0); $nombre=trim($_POST['Nombre'] ?? '');
        $stock=filter_var($_POST['Stock'] ?? null, FILTER_VALIDATE_INT); $precio=filter_var($_POST['Precio'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($id<=0 || $nombre==='' || mb_strlen($nombre)>50 || $stock===false || $stock<0 || $precio===false || $precio<0) {
            $_SESSION['error']='Verifica el nombre, el stock y el precio del producto.';
        } else {
            try { $this->model->update($id,['Nombre'=>$nombre,'Stock'=>$stock,'Precio'=>$precio]); $_SESSION['mensaje']='Producto actualizado correctamente.'; }
            catch (Exception $e) { $_SESSION['error']='No se pudo actualizar el producto. Puede tener movimientos relacionados.'; }
        }
        $this->redirect('index.php?controller=producto&action=index');
    }

    public function eliminar(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?controller=producto&action=index'); }
        $id=(int)($_POST['id'] ?? 0);
        try { if ($id>0) { $this->model->delete($id); $_SESSION['mensaje']='Producto eliminado.'; } }
        catch (Exception $e) { $_SESSION['error']='No se puede eliminar un producto que tiene ingresos, salidas o ventas asociadas.'; }
        $this->redirect('index.php?controller=producto&action=index');
    }
}