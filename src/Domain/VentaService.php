<?php

namespace Domain;

class VentaService
{
    /**
     * Calcula el total de una venta
     */
    public function calcularTotal(float $precio, int $cantidad): float
    {
        if ($precio < 0 || $cantidad <= 0) {
            throw new \InvalidArgumentException('Precio o cantidad inválidos');
        }
        return $precio * $cantidad;
    }

    /**
     * Valida si hay stock suficiente
     */
    public function validarStock(int $stockDisponible, int $cantidadSolicitada): bool
    {
        if ($cantidadSolicitada <= 0) {
            throw new \InvalidArgumentException('La cantidad debe ser mayor a 0');
        }
        return $stockDisponible >= $cantidadSolicitada;
    }

    /**
     * Calcula el nuevo stock después de una venta
     */
    public function calcularNuevoStock(int $stockActual, int $cantidadVendida): int
    {
        if (!$this->validarStock($stockActual, $cantidadVendida)) {
            throw new \UnderflowException('Stock insuficiente');
        }
        return $stockActual - $cantidadVendida;
    }
}