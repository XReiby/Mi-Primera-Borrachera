<?php
require_once("../../IC/Conexion.php");
require_once("../../PHP/Administrador/Crud_Usuarios.php");

// Función para redirigir según el rol del usuario
function redirigir($nombre_rol)
{
    switch ($nombre_rol) {
        case 'Mesero': // Mesero
            header("Location: ../../PHP/Mesero/Index.php");
            break;
        case 'Cajero': // Cajero
            header("Location: ../../PHP/Cajero/Index.php");
            break;
        case 'Administrador': // Administrador
            header("Location: Index.php");
            break;
        default:
            header("Location: ../../Login.php?error=2"); // Redirige a un error si el rol no es reconocido
            break;
    }
    exit(); // Para asegurar que el script se detenga después de redirigir
}

// Si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; 
    $contrasena = $_POST['contrasena'];

    // Validar que los campos no estén vacíos
    if (!empty($id) && !empty($contrasena)) {
        $crudUsuario = new Crud_Usuarios();
        $userData = $crudUsuario->obtenerUsuarioPorCredenciales($id, $contrasena); // Obtenemos el usuario

        if ($userData) {
            $db = Database::conectar();
            
            // Obtener el rol del usuario
            $selectRol = $db->prepare('SELECT nombre_rol FROM rol WHERE id_rol = :id_rol');
            $selectRol->bindParam(':id_rol', $userData->getIdRol());
            $selectRol->execute();
            $rolResult = $selectRol->fetch(PDO::FETCH_ASSOC);

            // Obtener la sede del usuario
            $selectSede = $db->prepare('SELECT nombre_sede FROM sedes WHERE id_sede = :id_sede');
            $selectSede->bindParam(':id_sede', $userData->getIdSede());
            $selectSede->execute();
            $sedeResult = $selectSede->fetch(PDO::FETCH_ASSOC);

            if ($rolResult && $sedeResult) {
                session_start();
                $_SESSION['id'] = $userData->getID();
                $_SESSION['usuario'] = $userData->getNombre(); 
                $_SESSION['apellido'] = $userData->getApellido();
                $_SESSION['rol'] = $rolResult['nombre_rol']; 
                $_SESSION['sede'] = $sedeResult['nombre_sede'];

                redirigir($rolResult['nombre_rol']); // Redirigir según el nombre del rol del usuario
            } else {
                // Si no se encuentra el rol o la sede
                header("Location: ../../Login.php?error=1");
                exit();
            }
        } else {
            // ID o contraseña incorrectos
            header("Location: ../../Login.php?error=1");
            exit();
        }
    } else {
        // Si los campos están vacíos, redirigir al índice
        header("Location: ../../Index.php");
        exit();
    }
}
?>
