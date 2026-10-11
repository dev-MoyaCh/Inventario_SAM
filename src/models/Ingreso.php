<?php

namespace Models;

use Core\Model;
use Exception;

class Ingreso extends Model {
    protected string $table = 'Ingresos';
    protected string $primaryKey = 'ID_Ingreso';

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT i.*, p.Nombre as ProductoNombre, pr.Razon_Social as ProveedorNombre
            FROM Ingresos i
            INNER JOIN Productos p ON i.ID_Producto = p.ID_Producto
            INNER JOIN Proveedores pr ON i.ID_Proveedor = pr.ID_Proveedor
            ORDER BY i.Fecha_Ingreso ASC
        ");
        return $stmt->fetchAll();
    }

    public function registrar(int $productoId, int $proveedorId, int $cantidad): int {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO Ingresos (ID_Producto, ID_Proveedor, Cantidad, Fecha_Ingreso) 
                 VALUES (:producto, :proveedor, :cantidad, NOW())"
            );
            $stmt->execute([
                'producto'  => $productoId,
                'proveedor' => $proveedorId,
                'cantidad'  => $cantidad
            ]);
            $id = (int) $this->db->lastInsertId();

            // Actualizar stock del producto
            $stmtStock = $this->db->prepare(
                "UPDATE Productos SET Stock = Stock + :cantidad WHERE ID_Producto = :id"
            );
            $stmtStock->execute(['cantidad' => $cantidad, 'id' => $productoId]);

            $this->db->commit();
            return $id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function actualizar(int $id, int $productoId, int $proveedorId, int $cantidad): void {
        $this->db->beginTransaction();
        try {
            $q=$this->db->prepare("SELECT ID_Producto, Cantidad FROM Ingresos WHERE ID_Ingreso=:id FOR UPDATE"); $q->execute(['id'=>$id]); $old=$q->fetch();
            if (!$old) { throw new Exception('El ingreso no existe.'); }
            $q=$this->db->prepare("SELECT Stock FROM Productos WHERE ID_Producto=:id FOR UPDATE"); $q->execute(['id'=>(int)$old['ID_Producto']]); $stockActual=$q->fetch();
            if (!$stockActual || (int)$stockActual['Stock'] < (int)$old['Cantidad']) { throw new Exception('No se puede modificar el ingreso porque parte de ese stock ya fue utilizado.'); }
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock-:cantidad WHERE ID_Producto=:id"); $q->execute(['cantidad'=>(int)$old['Cantidad'],'id'=>(int)$old['ID_Producto']]);
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock+:cantidad WHERE ID_Producto=:id"); $q->execute(['cantidad'=>$cantidad,'id'=>$productoId]);
            $q=$this->db->prepare("UPDATE Ingresos SET ID_Producto=:producto, ID_Proveedor=:proveedor, Cantidad=:cantidad WHERE ID_Ingreso=:id");
            $q->execute(['producto'=>$productoId,'proveedor'=>$proveedorId,'cantidad'=>$cantidad,'id'=>$id]); $this->db->commit();
        } catch (\Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }

    public function eliminar(int $id): void {
        $this->db->beginTransaction();
        try { $q=$this->db->prepare("SELECT ID_Producto,Cantidad FROM Ingresos WHERE ID_Ingreso=:id FOR UPDATE"); $q->execute(['id'=>$id]); $old=$q->fetch(); if (!$old) throw new Exception('El ingreso no existe.');
            $q=$this->db->prepare("SELECT Stock FROM Productos WHERE ID_Producto=:producto FOR UPDATE"); $q->execute(['producto'=>(int)$old['ID_Producto']]); $stockActual=$q->fetch();
            if (!$stockActual || (int)$stockActual['Stock'] < (int)$old['Cantidad']) { throw new Exception('No se puede eliminar el ingreso porque parte de ese stock ya fue utilizado.'); }
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock-:cantidad WHERE ID_Producto=:producto"); $q->execute(['cantidad'=>(int)$old['Cantidad'],'producto'=>(int)$old['ID_Producto']]);
            $q=$this->db->prepare("DELETE FROM Ingresos WHERE ID_Ingreso=:id"); $q->execute(['id'=>$id]); $this->db->commit();
        } catch (\Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }

    public function getTotalUnidades(): int {
        $stmt = $this->db->query("SELECT COALESCE(SUM(Cantidad), 0) as total FROM Ingresos");
        return (int) $stmt->fetch()['total'];
    }
}
