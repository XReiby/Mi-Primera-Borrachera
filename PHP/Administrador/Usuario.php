<?php
class Usuario
{
    private $id;
    private $nombre;
    private $apellido;
    private $id_rol;
    private $id_sede;
    private $contrasena;
    private $nombre_rol;  // Para almacenar el nombre del rol
    private $nombre_sede;  // Para almacenar el nombre de la sede

    // Getters y Setters para el ID
    public function getID()
    {
        return $this->id;
    }

    public function setID($id)
    {
        $this->id = $id;
    }

    // Getters y Setters para Nombre
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    // Getters y Setters para Apellido
    public function getApellido()
    {
        return $this->apellido;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    // Getters y Setters para id_rol
    public function getIdRol()
    {
        return $this->id_rol;
    }

    public function setIdRol($id_rol)
    {
        $this->id_rol = $id_rol;
    }

    
    // Getters y Setters para id_sede
    public function getIdSede()
    {
        return $this->id_sede;
    }

    public function setIdSede($id_sede)
    {
        $this->id_sede = $id_sede;
    }

    // Getters y Setters para Contrasena
    public function getContrasena()
    {
        return $this->contrasena;
    }

    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;
    }

    public function getNombreRol()
    {
        return $this->nombre_rol;
    }

    public function setNombreRol($nombre_rol)
    {
        $this->nombre_rol = $nombre_rol;
    }

    // Getters y Setters para Nombre Sede
    public function getNombreSede()
    {
        return $this->nombre_sede;
    }

    public function setNombreSede($nombre_sede)
    {
        $this->nombre_sede = $nombre_sede;
    }
}
?>