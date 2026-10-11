<?php

namespace Models;

use Core\Model;

class Producto extends Model {
    protected string $table = 'Productos';
    protected string $primaryKey = 'ID_Producto';

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM Productos ORDER BY ID_Producto ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Productos WHERE ID_Producto = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO Productos (Nombre, Stock, Precio) 
             VALUES (:nombre, :stock, :precio)"
        );
        $stmt->execute([
            'nombre' => $data['Nombre'],
            'stock'  => (int)($data['Stock'] ?? 0),
            'precio' => (float)($data['Precio'] ?? 0)
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE Productos SET Nombre = :nombre, Stock = :stock, Precio = :precio WHERE ID_Producto = :id");
        return $stmt->execute(['nombre'=>$data['Nombre'], 'stock'=>(int)$data['Stock'], 'precio'=>(float)$data['Precio'], 'id'=>$id]);
    }

    public function updateStock(int $id, int $cantidad): bool {
        $stmt = $this->db->prepare(
            "UPDATE Productos SET Stock = Stock + :cantidad WHERE ID_Producto = :id"
        );
        return $stmt->execute(['cantidad' => $cantidad, 'id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM Productos WHERE ID_Producto = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getStockValue(): float {
        $stmt = $this->db->query("SELECT SUM(Stock * Precio) as total FROM Productos");
        return (float) $stmt->fetch()['total'];
    }
}