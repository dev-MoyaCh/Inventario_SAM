<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOException;

abstract class DatabaseTestCase extends TestCase
{
    protected ?PDO $db = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        try {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=bd_inventario_test;charset=utf8mb4",
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            
            // Limpiar TODAS las tablas antes de cada test
            $this->limpiarTodasLasTablas();
            
        } catch (PDOException $e) {
            $this->fail("Error de conexión: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        $this->db = null;
        parent::tearDown();
    }

    private function limpiarTodasLasTablas(): void
    {
        try {
            // Deshabilitar foreign key checks
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // Truncar todas las tablas en orden correcto
            $tablas = ['Ventas', 'Ingresos', 'Salidas', 'Productos', 'Proveedores'];
            
            foreach ($tablas as $tabla) {
                // Verificar si la tabla existe
                $exists = $this->db->query("SHOW TABLES LIKE '$tabla'")->fetch();
                if ($exists) {
                    $this->db->exec("TRUNCATE TABLE $tabla");
                }
            }
            
            // Rehabilitar foreign key checks
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
            
        } catch (PDOException $e) {
            // Ignorar si no hay tablas
        }
    }

    protected function crearProducto(string $nombre, int $stock, float $precio): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO Productos (Nombre, Stock, Precio) VALUES (:nombre, :stock, :precio)"
        );
        
        $stmt->execute([
            'nombre' => $nombre,
            'stock' => $stock,
            'precio' => $precio
        ]);
        
        return (int) $this->db->lastInsertId();
    }
}