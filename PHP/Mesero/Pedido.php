<?php
class Pedido
{
    private $id_pedido;
    private $id_mesa;
    private $estado;
    private $fecha;
    private $total;

    // Getters y Setters
    public function getIDPedido()
    {
        return $this->id_pedido;
    }

    public function setIDPedido($id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function getIDMesa()
    {
        return $this->id_mesa;
    }

    public function setIDMesa($id_mesa)
    {
        $this->id_mesa = $id_mesa;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }
}
?>
