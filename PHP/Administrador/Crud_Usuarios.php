<?php
require_once('../../IC/Conexion.php');  // Asegúrate de tener la conexión configurada
require_once('Usuario.php');
require_once('Sedes.php');

class Crud_Usuarios
{
    // Método para insertar usuario
    public function insertar($usuario)
    {
        $db = Database::conectar();

        // Codificar la contraseña de forma reversible
        $contrasenaCodificada = base64_encode($usuario->getContrasena());

        $insert = $db->prepare("INSERT INTO Usuarios (id, nombre, apellido, id_rol, id_sede, contrasena) VALUES (:id, :nombre, :apellido, :id_rol, :id_sede, :contrasena)");
        $insert->bindValue(":id", $usuario->getID()); // Asegúrate de vincular el ID también
        $insert->bindValue(":nombre", $usuario->getNombre());
        $insert->bindValue(":apellido", $usuario->getApellido());
        $insert->bindValue(":id_rol", $usuario->getIdRol());
        $insert->bindValue(":id_sede", $usuario->getIdSede());
        $insert->bindValue(":contrasena", $contrasenaCodificada);
        $insert->execute();
    }



    // Método para mostrar todos los usuarios
    public function mostrar()
    {
        $db = Database::conectar();
        $listaUsuarios = [];
        $select = $db->query("
        SELECT u.*, r.nombre_rol, s.nombre_sede 
        FROM Usuarios u 
        JOIN rol r ON u.id_rol = r.id_rol 
        JOIN sedes s ON u.id_sede = s.id_sede
    ");

        foreach ($select->fetchAll() as $usuario) {
            $myUsuario = new Usuario();
            $myUsuario->setID($usuario['id']);
            $myUsuario->setNombre($usuario['nombre']);
            $myUsuario->setApellido($usuario['apellido']);
            $myUsuario->setIdRol($usuario['id_rol']);
            $myUsuario->setIdSede($usuario['id_sede']);
            $myUsuario->setContrasena($usuario['contrasena']);
            // Establecer el nombre del rol y de la sede
            $myUsuario->setNombreRol($usuario['nombre_rol']);
            $myUsuario->setNombreSede($usuario['nombre_sede']);
            $listaUsuarios[] = $myUsuario;
        }
        return $listaUsuarios;
    }

    // Método para actualizar usuario
    public function actualizar($usuario)
    {
        $db = Database::conectar();

        // Codificar la contraseña de forma reversible
        $contrasenaCodificada = base64_encode($usuario->getContrasena());

        $actualizar = $db->prepare('UPDATE Usuarios SET nombre=:nombre, apellido=:apellido, id_rol=:id_rol, id_sede=:id_sede, contrasena=:contrasena WHERE id=:id');

        $actualizar->bindValue(':id', $usuario->getID());
        $actualizar->bindValue(':nombre', $usuario->getNombre());
        $actualizar->bindValue(':apellido', $usuario->getApellido());
        $actualizar->bindValue(':id_rol', $usuario->getIdRol());
        $actualizar->bindValue(':id_sede', $usuario->getIdSede());
        $actualizar->bindValue(':contrasena', $contrasenaCodificada);
        $actualizar->execute();
    }



    // Método para eliminar usuario
    public function eliminar($id)
    {
        $db = Database::conectar();
        $eliminar = $db->prepare('DELETE FROM Usuarios WHERE id=:id');
        $eliminar->bindValue(':id', $id);
        $eliminar->execute();
    }

    // Método para obtener usuario por credenciales
    public function obtenerUsuarioPorCredenciales($id, $contrasena)
    {
        $db = Database::conectar();

        // Codificar la contraseña ingresada para compararla con la base de datos
        $contrasenaCodificada = base64_encode($contrasena);

        $query = "SELECT * FROM Usuarios WHERE id = :id AND contrasena = :contrasena";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':contrasena', $contrasenaCodificada);
        $stmt->execute();

        return $stmt->fetchObject('Usuario'); // Asegúrate de que 'Usuario' esté definido
    }


    // Método para obtener todos los roles
    public function obtenerRoles()
    {
        $db = Database::conectar();
        $select = $db->query("SELECT * FROM rol");
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener todas las sedes
    public function obtenerSedes()
    {
        $db = Database::conectar();
        $select = $db->query("SELECT * FROM sedes");
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para mostrar todas las sedes
        public function mostrarSedes()
    {
        $db = Database::conectar();
        $listaSedes = [];
        $query = "
            SELECT s.id_sede, s.nombre_sede, 
                (SELECT COUNT(*) FROM mesas WHERE mesas.id_sede = s.id_sede) AS cantidad_mesas
            FROM sedes s
        ";
        $select = $db->query($query);

        foreach ($select->fetchAll() as $sedes) {
            $mySedes = new Sedes();
            $mySedes->setID_Sede($sedes['id_sede']);
            $mySedes->setNombre_Sede($sedes['nombre_sede']);
            $mySedes->setCantidadMesas($sedes['cantidad_mesas']); // Nueva propiedad
            $listaSedes[] = $mySedes;
        }
        return $listaSedes;
    }

    public function agregarMesa($id_sede) {
        $conexion = Database::conectar();
    
        // Obtener el número de mesa más alto para la sede
        $sqlMaxNumero = "SELECT MAX(numero_mesa) AS max_mesa FROM mesas WHERE id_sede = ?";
        $stmtMax = $conexion->prepare($sqlMaxNumero);
        $stmtMax->execute([$id_sede]);
        $maxMesa = $stmtMax->fetchColumn();
    
        // Si no hay mesas, empezamos con el número 1
        $nuevoNumeroMesa = $maxMesa ? $maxMesa + 1 : 1;
    
        // Insertar la nueva mesa
        $sqlInsert = "INSERT INTO mesas (numero_mesa, id_sede) VALUES (?, ?)";
        $stmtInsert = $conexion->prepare($sqlInsert);
        $stmtInsert->execute([$nuevoNumeroMesa, $id_sede]);
    
        $conexion = null;
    }

    public function contarMesasPorSede($id_sede) {
        $conexion =Database::conectar();
    
        $sql = "SELECT COUNT(*) AS total_mesas FROM mesas WHERE id_sede = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_sede]);
    
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $conexion = null;
    
        return $resultado['total_mesas'];
    }
    
    public function agregarSede($nombre_sede){
        $db = Database::conectar();

        $insert = $db->prepare("INSERT INTO sedes (nombre_sede) VALUES (:nombre_sede)");
        $insert->bindValue(":nombre_sede", $nombre_sede);
        $insert->execute();
    }
}
?>