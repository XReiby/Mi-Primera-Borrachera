<?php
class Inventario
{
    // Propiedades
    private $id_inventario;
    private $id_producto;
    private $cantidad;
    private $id_sede;

    // Constructor
    public function __construct($id_inventario = null, $id_producto = null, $cantidad = null, $id_sede = null)
    {
        $this->id_inventario = $id_inventario;
        $this->id_producto = $id_producto;
        $this->cantidad = $cantidad;
        $this->id_sede = $id_sede;
    }

    // Métodos Getters y Setters
    public function getIDInventario()
    {
        return $this->id_inventario;
    }

    public function setIDInventario($id_inventario)
    {
        $this->id_inventario = $id_inventario;
    }

    public function getIDProducto()
    {
        return $this->id_producto;
    }

    public function setIDProducto($id_producto)
    {
        $this->id_producto = $id_producto;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    public function getIDSede()
    {
        return $this->id_sede;
    }

    public function setIDSede($id_sede)
    {
        $this->id_sede = $id_sede;
    }
}
?>
