-- Ejecutar en la base de datos bd_inventario existente.
-- Mantiene el administrador existente y hace que los nuevos usuarios sean usuarios normales por defecto.
ALTER TABLE usuarios MODIFY Rol VARCHAR(20) NOT NULL DEFAULT 'usuario';
