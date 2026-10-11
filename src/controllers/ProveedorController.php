<?php

namespace Controllers;

use Core\Controller;
use Models\Proveedor;
use Exception;

class ProveedorController extends Controller
{
    private Proveedor $model;

    public function __construct()
    {
        $this->model = new Proveedor();
        $this->requireAdmin();
    }

    public function index(): void {
        $proveedores = $this->model->getAll();
        $this->render('proveedores', ['proveedores' => $proveedores]);
    }

    public function actualizar(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?controller=proveedor&action=index'); }
        $id=(int)($_POST['ID_Proveedor'] ?? 0); $razon=trim($_POST['Razon_Social'] ?? ''); $correo=trim($_POST['Correo'] ?? ''); $numero=trim($_POST['Numero'] ?? '');
        if ($id<=0 || $razon==='' || mb_strlen($razon)>50 || ($correo!=='' && !filter_var($correo,FILTER_VALIDATE_EMAIL)) || mb_strlen($correo)>50 || mb_strlen($numero)>20) {
            $_SESSION['error']='Verifica los datos del proveedor.';
        } else { $this->model->update($id,['Razon_Social'=>$razon,'Correo'=>$correo ?: null,'Numero'=>$numero ?: null]); $_SESSION['mensaje']='Proveedor actualizado correctamente.'; }
        $this->redirect('index.php?controller=proveedor&action=index');
    }

    public function eliminar(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?controller=proveedor&action=index'); }
        $id=(int)($_POST['id'] ?? 0);
        try { if ($id>0) { $this->model->delete($id); $_SESSION['mensaje']='Proveedor eliminado.'; } }
        catch (Exception $e) { $_SESSION['error']='No se puede eliminar un proveedor que tiene ingresos asociados.'; }
        $this->redirect('index.php?controller=proveedor&action=index');
    }

    public function crear(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validar datos requeridos
                if (empty($_POST['Razon_Social']) || empty($_POST['Correo']) || empty($_POST['Numero'])) {
                    throw new Exception('Todos los campos son requeridos');
                }

                // Validar formato de email
                if (!filter_var($_POST['Correo'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('El correo electrónico no es válido');
                }

                // Validar que el número sea válido
                $numero = trim($_POST['Numero']);
                if (strlen($numero) < 7) {
                    throw new Exception('El número de teléfono debe tener al menos 7 dígitos');
                }

                $this->model->create([
                    'Razon_Social' => htmlspecialchars(trim($_POST['Razon_Social'])),
                    'Correo'       => htmlspecialchars(trim($_POST['Correo'])),
                    'Numero'       => htmlspecialchars($numero),
                ]);

                $_SESSION['mensaje'] = '✅ Proveedor creado correctamente';
            } catch (Exception $e) {
                $_SESSION['error'] = '❌ Error: ' . $e->getMessage();
            }
        }

        $this->redirect('index.php?controller=proveedor&action=index');
    }
}