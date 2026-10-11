<?php

namespace Models;

use Core\Model;

class Proveedor extends Model {
    protected string $table = 'Proveedores';
    protected string $primaryKey = 'ID_Proveedor';

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM Proveedores ORDER BY ID_Proveedor ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Proveedores WHERE ID_Proveedor = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE Proveedores SET Razon_Social=:razon, Correo=:correo, Numero=:numero WHERE ID_Proveedor=:id");
        return $stmt->execute(['razon'=>$data['Razon_Social'], 'correo'=>$data['Correo'], 'numero'=>$data['Numero'], 'id'=>$id]);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO Proveedores (Razon_Social, Correo, Numero) 
             VALUES (:razon, :correo, :numero)"
        );
        $stmt->execute([
            'razon'   => $data['Razon_Social'],
            'correo'  => $data['Correo'],
            'numero'  => $data['Numero']
        ]);
        return (int) $this->db->lastInsertId();
    }
}