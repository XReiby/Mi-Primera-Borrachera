<?php
require_once("Crud_Usuarios.php");
require_once("Usuario.php");

// Iniciar sesión y manejar errores
session_start();
error_reporting(0);
if ($_SESSION['rol'] != 'Administrador' && $_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] != 'Administrador') {
    header("location: ../../Index.php");
    die();
}

$CrudUsuario = new Crud_Usuarios();

if (isset($_GET['Id'])) {
    $id = $_GET['Id'];
    $usuarios = $CrudUsuario->mostrar();

    foreach ($usuarios as $u) {
        if ($u->getID() == $id) {
            $Usuario = $u;
            break;
        }
    }
} else {
    header("Location: Mostrar_Usuarios.php");
    exit();
}

// Obtener roles y sedes
$roles = $CrudUsuario->obtenerRoles(); // Método para obtener roles
$sedes = $CrudUsuario->obtenerSedes(); // Método para obtener sedes
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../JS/ActualizarU.js" defer></script>
    <script src="../../JS/Credenciales.js"></script>
    <script src="../../JS/Desplegable.js"></script>
    <title>Actualizar Usuario</title>
    <link rel="stylesheet" href="../../Css/Administrador/ActualizarU.css">
    <link rel="icon" href="../../Icono/Administrador/ActualizarU.png">
</head>

<body>
<header class="header">
        <h3 id="toggle-title"><?php echo $_SESSION['usuario'] ?></h3>
        <div id="dropdown-content">
            <ul>
            <li><?php echo $_SESSION['usuario'] . ' ' . $_SESSION['apellido']; ?></li>
                <li><?php echo $_SESSION['sede'] ?></li>
                <li><?php echo $_SESSION['rol'] ?></li>
                <li><a href="Cerrar_Sesion.php">Cerrar Sesión</a></li>
            </ul>
        </div>
        <h2>Editar Usuario</h2>
        <a href="../../Index.php" class="icono-link">
            <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
        </a>
    </header>
    <div class="container">
        <form action="Administrar_Usuarios.php" method="post">
            <label for="id"><b>ID:</b></label>
            <span id="id"><?php echo $Usuario->getID(); ?></span>
            <input type="hidden" name="id" value="<?php echo $Usuario->getID(); ?>">
            <label for="nombre"><b>Nombre:</b></label>
            <input type="text" id="nombre" name="nombre"
                value="<?php echo htmlspecialchars($Usuario->getNombre(), ENT_QUOTES, 'UTF-8'); ?>" minlength="4"
                maxlength="25" required>

            <label for="apellido"><b>Apellido:</b></label>
            <input type="text" id="apellido" name="apellido"
                value="<?php echo htmlspecialchars($Usuario->getApellido(), ENT_QUOTES, 'UTF-8'); ?>" minlength="4"
                maxlength="25" required>

            <label for="rol"><b>Rol:</b></label>
            <select id="rol" name="rol" required>
                <option value="" disabled selected>Selecciona un Rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?php echo $rol['id_rol']; ?>" <?php echo ($Usuario->getIdRol() == $rol['id_rol']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($rol['nombre_rol'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="sede"><b>Sede:</b></label>
            <select id="sede" name="sede" required>
                <option value="" disabled selected>Selecciona una Sede</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?php echo $sede['id_sede']; ?>" <?php echo ($Usuario->getIdSede() == $sede['id_sede']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sede['nombre_sede'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="contraseña"><b>Contraseña:</b></label>
            <input type="text" id="contraseña" name="contraseña"
            value="<?php echo htmlspecialchars(base64_decode($Usuario->getContrasena()), ENT_QUOTES, 'UTF-8'); ?>" required>
            <div id="password-error" style="color: red; margin-top: -15px;"></div>


            <input type="hidden" name="actualizar" value="actualizar">

            <div class="button-group">
                <a href="Mostrar_Usuarios.php" class="back-btn">Volver</a>
                <button type="submit" class="submit-btn">Guardar</button>
            </div>
        </form>
    </div>

</body>

</html>