<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Domain\IngresoService;
use InvalidArgumentException;
use DateTime;

class IngresoServiceTest extends TestCase
{
    private IngresoService $ingresoService;

    protected function setUp(): void
    {
        $this->ingresoService = new IngresoService();
    }

    // ========== CICLO 1: validarCantidad ==========

    
    public function test_validar_cantidad_positiva(): void
    {
        $this->assertTrue($this->ingresoService->validarCantidad(10));
    }

    public function test_validar_cantidad_cero_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La cantidad debe ser mayor a 0');
        $this->ingresoService->validarCantidad(0);
    }

    public function test_validar_cantidad_negativa_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La cantidad debe ser mayor a 0');
        $this->ingresoService->validarCantidad(-5);
    }

    // ========== CICLO 2: calcularFechaIngreso ==========

    public function test_calcular_fecha_ingreso_es_hoy(): void
    {
        $fecha = $this->ingresoService->calcularFechaIngreso();
        $hoy = new DateTime();
        
        $this->assertEquals($hoy->format('Y-m-d'), $fecha->format('Y-m-d'));
    }

    public function test_calcular_fecha_ingreso_con_fecha_personalizada(): void
    {
        $fechaPersonalizada = new DateTime('2026-01-15');
        $fecha = $this->ingresoService->calcularFechaIngreso($fechaPersonalizada);
        
        $this->assertEquals('2026-01-15', $fecha->format('Y-m-d'));
    }

    // ========== CICLO 3: validarDatosIngreso ==========

    public function test_validar_datos_ingreso_validos(): void
    {
        $datos = [
            'ID_Producto' => 1,
            'ID_Proveedor' => 1,
            'Cantidad' => 10
        ];
        
        $this->assertTrue($this->ingresoService->validarDatosIngreso($datos));
    }

    public function test_validar_datos_ingreso_sin_producto_lanza_excepcion(): void
    {
        $datos = [
            'ID_Proveedor' => 1,
            'Cantidad' => 10
        ];
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El ID del producto es requerido');
        $this->ingresoService->validarDatosIngreso($datos);
    }

    public function test_validar_datos_ingreso_sin_proveedor_lanza_excepcion(): void
    {
        $datos = [
            'ID_Producto' => 1,
            'Cantidad' => 10
        ];
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El ID del proveedor es requerido');
        $this->ingresoService->validarDatosIngreso($datos);
    }

    public function test_validar_datos_ingreso_sin_cantidad_lanza_excepcion(): void
    {
        $datos = [
            'ID_Producto' => 1,
            'ID_Proveedor' => 1
        ];
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La cantidad es requerida');
        $this->ingresoService->validarDatosIngreso($datos);
    }

    // ========== CICLO 4: calcularTotalIngreso ==========

    public function test_calcular_total_ingreso(): void
    {
        $cantidad = 5;
        $precioUnitario = 100.00;
        $total = $this->ingresoService->calcularTotalIngreso($cantidad, $precioUnitario);
        
        $this->assertEquals(500.00, $total);
    }

    public function test_calcular_total_ingreso_con_decimales(): void
    {
        $cantidad = 3;
        $precioUnitario = 19.99;
        $total = $this->ingresoService->calcularTotalIngreso($cantidad, $precioUnitario);
        
        $this->assertEquals(59.97, $total);
    }
}