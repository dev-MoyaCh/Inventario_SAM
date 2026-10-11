<?php

namespace Domain;

use InvalidArgumentException;
use DateTime;

class IngresoService
{
    
     // Valida que la cantidad sea positiva
     
    public function validarCantidad(int $cantidad): bool
    {
        if ($cantidad <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a 0');
        }
        return true;
    }

    
      //Calcula la fecha de ingreso (por defecto hoy)
     
    public function calcularFechaIngreso(?DateTime $fecha = null): DateTime
    {
        return $fecha ?? new DateTime();
    }

    
        //Valida los datos completos del ingreso
     
    public function validarDatosIngreso(array $datos): bool
    {
        if (empty($datos['ID_Producto'])) {
            throw new InvalidArgumentException('El ID del producto es requerido');
        }

        if (empty($datos['ID_Proveedor'])) {
            throw new InvalidArgumentException('El ID del proveedor es requerido');
        }

        if (!isset($datos['Cantidad'])) {
            throw new InvalidArgumentException('La cantidad es requerida');
        }

        $this->validarCantidad($datos['Cantidad']);

        return true;
    }

    
      //Calcula el total del ingreso
     
    public function calcularTotalIngreso(int $cantidad, float $precioUnitario): float
    {
        return $cantidad * $precioUnitario;
    }
}