<?php

namespace Controllers;

use Core\Controller;
use Models\Salida;
use Models\Producto;
use Exception;

class SalidaController extends Controller
{
    private Salida $model;
    private Producto $producto;

    public function __construct()
    {
        $this->model = new Salida();
        $this->producto = new Producto();
    }

    public function index(): void {
        $salidas = $this->model->getAll();
        $this->render('salidas', ['salidas' => $salidas, 'productos' => $this->producto->getAll()]);
    }

    public function actualizar(): void
    {
        $this->requireAdmin(); if ($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('index.php?controller=salida&action=index');
        try { $id=(int)($_POST['ID_Salida']??0); $producto=(int)($_POST['ID_Producto']??0); $cantidad=(int)($_POST['Cantidad']??0); $razon=trim($_POST['Razon']??''); if($id<=0||$producto<=0||$cantidad<=0||$razon==='') throw new Exception('Verifica los campos.');
            $this->model->actualizar($id,$producto,$cantidad,$razon); $_SESSION['mensaje']='Salida actualizada y stock recalculado.';
        } catch (Exception $e) { $_SESSION['error']='No se pudo actualizar la salida: '.$e->getMessage(); }
        $this->redirect('index.php?controller=salida&action=index');
    }
    public function eliminar(): void
    {
        $this->requireAdmin(); if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('index.php?controller=salida&action=index');
        try { $this->model->eliminar((int)($_POST['id']??0)); $_SESSION['mensaje']='Salida eliminada y stock restituido.'; } catch (Exception $e) { $_SESSION['error']='No se pudo eliminar la salida: '.$e->getMessage(); }
        $this->redirect('index.php?controller=salida&action=index');
    }

    public function registrar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $razon = htmlspecialchars(trim($_POST['Razon'] ?? ''));
                if (empty($razon)) {
                    throw new Exception('La razón es requerida');
                }

                $this->model->registrar(
                    (int) $_POST['ID_Producto'],
                    (int) $_POST['Cantidad'],
                    $razon
                );
                $_SESSION['mensaje'] = "✅ Salida registrada correctamente";
            } catch (Exception $e) {
                $_SESSION['error'] = "❌ Error: " . $e->getMessage();
            }

            $this->redirect('index.php?controller=salida&action=index');
            return;
        }

        $productos = $this->producto->getAll();
        $this->render('salidas_form', ['productos' => $productos]);
    }
}