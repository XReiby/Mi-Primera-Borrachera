<?php
class Producto
{
    private $id_producto;
    private $nombre_producto;
    private $precio;
    private $costo;

    // Getters y Setters
    public function getIDProducto()
    {
        return $this->id_producto;
    }

    public function setIDProducto($id_producto)
    {
        $this->id_producto = $id_producto;
    }

    public function getNombreProducto()
    {
        return $this->nombre_producto;
    }

    public function setNombreProducto($nombre_producto)
    {
        $this->nombre_producto = $nombre_producto;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function getCosto()
    {
        return $this->costo;
    }

    public function setCosto($costo)
    {
        $this->costo = $costo;
    }
}

?>