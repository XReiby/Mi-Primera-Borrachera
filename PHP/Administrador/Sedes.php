<?php
class Sedes
{
    private $id_sede;
    private $nombre_sede;
    private $Cantidad_Mesas;

    public function getID_Sede()
    {
        return $this->id_sede;
    }

    public function setID_Sede($id_sede)
    {
        $this->id_sede = $id_sede;
    }

    // Getters y Setters para Nombre
    public function getNombre_Sede()
    {
        return $this->nombre_sede;
    }

    public function setNombre_Sede($nombre_sede)
    {
        $this->nombre_sede = $nombre_sede;
    }

    public function getCantidadMesas(
        
    ){
        return $this->Cantidad_Mesas;
    }

    public function setCantidadMesas($Cantidad_Mesas){
        $this->Cantidad_Mesas = $Cantidad_Mesas;
    }
}
?>