<?php

namespace Models;

use Core\Model;
use Exception;

class Salida extends Model {
    protected string $table = 'Salidas';
    protected string $primaryKey = 'ID_Salida';

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT s.*, p.Nombre as ProductoNombre
            FROM Salidas s
            INNER JOIN Productos p ON s.ID_Producto = p.ID_Producto
            ORDER BY s.Fecha_Salida ASC
        ");
        return $stmt->fetchAll();
    }

    public function registrar(int $productoId, int $cantidad, string $razon): int {
        // Validar stock suficiente
        $stmtProducto = $this->db->prepare("SELECT Stock FROM Productos WHERE ID_Producto = :id");
        $stmtProducto->execute(['id' => $productoId]);
        $producto = $stmtProducto->fetch();

        if (!$producto || $producto['Stock'] < $cantidad) {
            throw new Exception("Stock insuficiente. Disponible: " . ($producto['Stock'] ?? 0));
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO Salidas (ID_Producto, Cantidad, Razon, Fecha_Salida) 
                 VALUES (:producto, :cantidad, :razon, NOW())"
            );
            $stmt->execute([
                'producto' => $productoId,
                'cantidad' => $cantidad,
                'razon'    => $razon
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

    public function actualizar(int $id, int $productoId, int $cantidad, string $razon): void {
        $this->db->beginTransaction();
        try { $q=$this->db->prepare("SELECT ID_Producto,Cantidad FROM Salidas WHERE ID_Salida=:id FOR UPDATE"); $q->execute(['id'=>$id]); $old=$q->fetch(); if (!$old) throw new Exception('La salida no existe.');
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock+:cantidad WHERE ID_Producto=:id"); $q->execute(['cantidad'=>(int)$old['Cantidad'],'id'=>(int)$old['ID_Producto']]);
            $q=$this->db->prepare("SELECT Stock FROM Productos WHERE ID_Producto=:id FOR UPDATE"); $q->execute(['id'=>$productoId]); $prod=$q->fetch(); if (!$prod || (int)$prod['Stock']<$cantidad) throw new Exception('Stock insuficiente para actualizar la salida.');
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock-:cantidad WHERE ID_Producto=:id"); $q->execute(['cantidad'=>$cantidad,'id'=>$productoId]);
            $q=$this->db->prepare("UPDATE Salidas SET ID_Producto=:producto,Cantidad=:cantidad,Razon=:razon WHERE ID_Salida=:id"); $q->execute(['producto'=>$productoId,'cantidad'=>$cantidad,'razon'=>$razon,'id'=>$id]); $this->db->commit();
        } catch (\Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }

    public function eliminar(int $id): void {
        $this->db->beginTransaction(); try { $q=$this->db->prepare("SELECT ID_Producto,Cantidad FROM Salidas WHERE ID_Salida=:id FOR UPDATE"); $q->execute(['id'=>$id]); $old=$q->fetch(); if (!$old) throw new Exception('La salida no existe.');
            $q=$this->db->prepare("UPDATE Productos SET Stock=Stock+:cantidad WHERE ID_Producto=:producto"); $q->execute(['cantidad'=>(int)$old['Cantidad'],'producto'=>(int)$old['ID_Producto']]); $q=$this->db->prepare("DELETE FROM Salidas WHERE ID_Salida=:id"); $q->execute(['id'=>$id]); $this->db->commit();
        } catch (\Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }

    public function getTotalUnidades(): int {
        $stmt = $this->db->query("SELECT COALESCE(SUM(Cantidad), 0) as total FROM Salidas");
        return (int) $stmt->fetch()['total'];
    }
}