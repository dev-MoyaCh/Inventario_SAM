<?php

namespace Models;

use Core\Model;

class Venta extends Model {
    protected string $table = 'Ventas';
    protected string $primaryKey = 'ID_Venta';

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT v.*, p.Nombre as ProductoNombre
            FROM Ventas v
            INNER JOIN Productos p ON v.ID_Producto = p.ID_Producto
            ORDER BY v.ID_Venta ASC
        ");
        return $stmt->fetchAll();
    }

    public function registrar(int $productoId, int $cantidad, float $total): int {
        // Validar stock suficiente
        $stmtProducto = $this->db->prepare("SELECT Stock, Precio FROM Productos WHERE ID_Producto = :id");
        $stmtProducto->execute(['id' => $productoId]);
        $producto = $stmtProducto->fetch();

        if (!$producto || $producto['Stock'] < $cantidad) {
            throw new Exception("Stock insuficiente. Disponible: " . ($producto['Stock'] ?? 0));
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO Ventas (ID_Producto, Cantidad, Total, Fecha_Venta) 
                 VALUES (:producto, :cantidad, :total, NOW())"
            );
            $stmt->execute([
                'producto' => $productoId,
                'cantidad' => $cantidad,
                'total'    => $total
            ]);
            $id = (int) $this->db->lastInsertId();

            // Descontar stock
            $stmtStock = $this->db->prepare(
                "UPDATE Productos SET Stock = Stock - :cantidad WHERE ID_Producto = :id"
            );
            $stmtStock->execute(['cantidad' => $cantidad, 'id' => $productoId]);

            $this->db->commit();
            return $id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function actualizar(int $id, int $productoId, int $cantidad): void {
        $this->db->beginTransaction(); try { $q=$this->db->prepare("SELECT ID_Producto,Cantidad FROM Ventas WHERE ID_Venta=:id FOR UPDATE"); $q->execute(['id'=>$id]); $old=$q->fetch(); if (!$old) throw new \Exception('La venta no existe.');
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock+:cantidad WHERE ID_Producto=:id"); $q->execute(['cantidad'=>(int)$old['Cantidad'],'id'=>(int)$old['ID_Producto']]);
            $q=$this->db->prepare("SELECT Stock,Precio FROM Productos WHERE ID_Producto=:id FOR UPDATE"); $q->execute(['id'=>$productoId]); $prod=$q->fetch(); if (!$prod || (int)$prod['Stock']<$cantidad) throw new \Exception('Stock insuficiente para actualizar la venta.');
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock-:cantidad WHERE ID_Producto=:id"); $q->execute(['cantidad'=>$cantidad,'id'=>$productoId]);
            $total=$cantidad*(float)$prod['Precio']; $q=$this->db->prepare("UPDATE Ventas SET ID_Producto=:producto,Cantidad=:cantidad,Total=:total WHERE ID_Venta=:id"); $q->execute(['producto'=>$productoId,'cantidad'=>$cantidad,'total'=>$total,'id'=>$id]); $this->db->commit();
        } catch (\Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }

    public function eliminar(int $id): void {
        $this->db->beginTransaction(); try { $q=$this->db->prepare("SELECT ID_Producto,Cantidad FROM Ventas WHERE ID_Venta=:id FOR UPDATE"); $q->execute(['id'=>$id]); $old=$q->fetch(); if (!$old) throw new \Exception('La venta no existe.');
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock+:cantidad WHERE ID_Producto=:producto"); $q->execute(['cantidad'=>(int)$old['Cantidad'],'producto'=>(int)$old['ID_Producto']]); $q=$this->db->prepare("DELETE FROM Ventas WHERE ID_Venta=:id"); $q->execute(['id'=>$id]); $this->db->commit();
        } catch (\Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }

    public function getTotalVentas(): float {
        $stmt = $this->db->query("SELECT COALESCE(SUM(Total), 0) as total FROM Ventas");
        return (float) $stmt->fetch()['total'];
    }

    public function getTotalUnidades(): int {
        $stmt = $this->db->query("SELECT COALESCE(SUM(Cantidad), 0) as total FROM Ventas");
        return (int) $stmt->fetch()['total'];
    }
}