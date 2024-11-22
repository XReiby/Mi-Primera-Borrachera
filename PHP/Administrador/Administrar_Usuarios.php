<?php
require_once("Crud_Usuarios.php");
require_once("Usuario.php");

session_start();

$CrudUsuario = new Crud_Usuarios();
$Usuario = new Usuario();

if (isset($_POST['insertar'])) {
    $Usuario->setId($_POST['id']);
    $Usuario->setNombre($_POST['nombre']);
    $Usuario->setApellido($_POST['apellido']);

    // Verificar si el ID de usuario ya existe
    $usuarios = $CrudUsuario->mostrar();
    foreach ($usuarios as $u) {
        if ($u->getID() == $Usuario->getID()) {
            header("Location: Ingresar_Usuario.php?error=id_existente");
            exit();
        }
    }

    // Guardar el usuario si el ID no existe
    $Usuario->setIdRol($_POST['rol']);
    $Usuario->setIdSede($_POST['sede']);
    $Usuario->setContrasena($_POST['contraseña']);
    $CrudUsuario->insertar($Usuario);

    header("Location: Mostrar_Usuarios.php");
    exit();
} elseif (isset($_POST['actualizar'])) {
    $Usuario->setId($_POST['id']);
    $Usuario->setNombre($_POST['nombre']);
    $Usuario->setApellido($_POST['apellido']); // Establecer el apellido del usuario
    $Usuario->setIdRol($_POST['rol']); // Cambiado de setRol a setIdRol
    $Usuario->setIdSede($_POST['sede']); // Cambiado de setSede a setIdSede
    $Usuario->setContrasena($_POST['contraseña']); // Asegúrate de usar el nombre correcto
    $CrudUsuario->actualizar($Usuario);
    
    $db = Database::conectar();

    // Obtener el nombre del rol
    $selectRol = $db->prepare('SELECT nombre_rol FROM rol WHERE id_rol = :id_rol');
    $selectRol->bindParam(':id_rol', $_POST['rol']);
    $selectRol->execute();
    $rolResult = $selectRol->fetch(PDO::FETCH_ASSOC);

    // Obtener el nombre de la sede
    $selectSede = $db->prepare('SELECT nombre_sede FROM sedes WHERE id_sede = :id_sede');
    $selectSede->bindParam(':id_sede', $_POST['sede']);
    $selectSede->execute();
    $sedeResult = $selectSede->fetch(PDO::FETCH_ASSOC);

    // Verificar si el ID del usuario actualizado es el mismo que el ID guardado en la sesión
    if ($Usuario->getID() == $_SESSION['id']) {
        $_SESSION['usuario'] = $Usuario->getNombre(); 
        $_SESSION['apellido'] = $Usuario->getApellido();
        $_SESSION['rol'] = $rolResult['nombre_rol']; 
        $_SESSION['sede'] = $sedeResult['nombre_sede']; 
    }

    // Redirigir a la lista de usuarios si no hay errores
    header("Location: Mostrar_Usuarios.php");
    exit();
} elseif (isset($_GET['accion'])) {
    if ($_GET['accion'] == 'e') {
        if ($_GET['Id'] == $_SESSION['id']) {
            echo "<script>alert('No puedes eliminar tu propio usuario'); window.location.href='Mostrar_Usuarios.php';</script>";
            exit();
        }
        $CrudUsuario->eliminar($_GET['Id']);
        header("Location: Mostrar_Usuarios.php");
        exit();
    } elseif ($_GET['accion'] == 'a') {
        header("Location: Actualizar_Usuario.php");
        exit();
    }
}
?>