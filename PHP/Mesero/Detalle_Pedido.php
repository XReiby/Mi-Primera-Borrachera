<?php
class DetallePedido
{
    private $id_detalle;
    private $id_producto;
    private $id_pedido;
    private $cantidad;
    private $costo_producto;
    private $precio_producto;

    // Getters y Setters
    public function getIDDetalle()
    {
        return $this->id_detalle;
    }

    public function setIDDetalle($id_detalle)
    {
        $this->id_detalle = $id_detalle;
    }

    public function getIDProducto()
    {
        return $this->id_producto;
    }

    public function setIDProducto($id_producto)
    {
        $this->id_producto = $id_producto;
    }

    public function getIDPedido()
    {
        return $this->id_pedido;
    }

    public function setIDPedido($id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    public function getCostoProducto()
    {
        return $this->costo_producto;
    }

    public function setCostoProducto($costo_producto)
    {
        $this->costo_producto = $costo_producto;
    }

    public function getPrecioProducto()
    {
        return $this->precio_producto;
    }

    public function setPrecioProducto($precio_producto)
    {
        $this->precio_producto = $precio_producto;
    }
}
?>
