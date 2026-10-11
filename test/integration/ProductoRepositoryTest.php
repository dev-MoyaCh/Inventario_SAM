<?php

namespace Tests\Integration;

use Models\Producto;

class ProductoRepositoryTest extends DatabaseTestCase
{
    private Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->producto = new Producto();
    }

    public function test_crear_producto(): void
    {
        $id = $this->producto->create([
            'Nombre' => 'Laptop HP',
            'Stock' => 10,
            'Precio' => 15000.00
        ]);

        $this->assertGreaterThan(0, $id);

        $producto = $this->producto->findById($id);
        $this->assertEquals('Laptop HP', $producto['Nombre']);
        $this->assertEquals(10, $producto['Stock']);
        $this->assertEquals(15000.00, $producto['Precio']);
    }
    
    public function test_obtener_todos_los_productos(): void
    {
        $this->crearProducto('Producto A', 5, 100);
        $this->crearProducto('Producto B', 3, 200);

        $productos = $this->producto->getAll();

        $this->assertCount(2, $productos);
    }

    public function test_actualizar_stock(): void
    {
        $id = $this->crearProducto('Producto X', 10, 50);

        $this->producto->updateStock($id, -3); // Vender 3

        $producto = $this->producto->findById($id);
        $this->assertEquals(7, $producto['Stock']);
    }

    public function test_eliminar_producto(): void
    {
        $id = $this->crearProducto('Producto Z', 1, 10);

        $resultado = $this->producto->delete($id);

        $this->assertTrue($resultado);
        $this->assertNull($this->producto->findById($id));
    }

    public function test_calcular_valor_total_del_stock(): void
    {
        $this->crearProducto('Producto A', 10, 100); // 1000
        $this->crearProducto('Producto B', 5, 200);  // 1000

        $total = $this->producto->getStockValue();

        $this->assertEquals(2000.0, $total);
    }
}