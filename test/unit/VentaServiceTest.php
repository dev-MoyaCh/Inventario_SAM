<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Domain\VentaService;
use InvalidArgumentException;
use UnderflowException;

class VentaServiceTest extends TestCase
{
    private VentaService $ventaService;

    protected function setUp(): void
    {
        $this->ventaService = new VentaService();
    }

    public function test_calcular_total_simple(): void
    {
        $total = $this->ventaService->calcularTotal(100.0, 3);
        $this->assertEquals(300.0, $total);
    }

    public function test_calcular_total_con_decimales(): void
    {
        $total = $this->ventaService->calcularTotal(19.99, 2);
        $this->assertEquals(39.98, $total);
    }

    public function test_calcular_total_lanza_excepcion_con_precio_negativo(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->ventaService->calcularTotal(-10.0, 2);
    }

    public function test_calcular_total_lanza_excepcion_con_cantidad_cero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->ventaService->calcularTotal(100.0, 0);
    }

    public function test_validar_stock_suficiente(): void
    {
        $this->assertTrue($this->ventaService->validarStock(10, 5));
    }

    public function test_validar_stock_insuficiente(): void
    {
        $this->assertFalse($this->ventaService->validarStock(3, 5));
    }

    public function test_calcular_nuevo_stock(): void
    {
        $nuevoStock = $this->ventaService->calcularNuevoStock(20, 5);
        $this->assertEquals(15, $nuevoStock);
    }

    public function test_calcular_nuevo_stock_lanza_excepcion(): void
    {
        $this->expectException(UnderflowException::class);
        $this->ventaService->calcularNuevoStock(3, 5);
    }
}