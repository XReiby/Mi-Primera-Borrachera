<?php
require_once("Crud_Usuarios.php");
require_once("Usuario.php");

session_start();
error_reporting(0);
if ($_SESSION['rol'] != 'Administrador' && $_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] != 'Administrador') {
    header("location: ../../Index.php");
    die();
}

// Crear instancias de las clases necesarias
$CrudUsuario = new Crud_Usuarios();
$roles = $CrudUsuario->obtenerRoles(); // Método para obtener roles desde la base de datos
$sedes = $CrudUsuario->obtenerSedes(); // Método para obtener sedes desde la base de datos
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../JS/IngresarU.js" defer></script>
    <script src="../../JS/Credenciales.js"></script>
    <script src="../../JS/Desplegable.js"></script>
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../../Css/Administrador/IngresarU.css">
    <link rel="icon" href="../../Icono/Administrador/Agregar.png">
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
        <h2>Ingresar Usuario</h2>
        <a href="../../Index.php" class="icono-link">
            <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
        </a>
    </header>
    <div class="container">
        <form action="Administrar_Usuarios.php" method="post">
            <label for="id"><b>ID:</b></label>
            <input type="text" id="id" name="id" pattern="\d+" placeholder="ID del Usuario (no se puede cambiar)" required>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'id_existente'): ?>
                <div style="color: red; margin-top: -15px; margin-bottom: 10px;">
                    El ID de usuario ya existe. Por favor, elige otro.
                </div>
            <?php endif; ?>

            <label for="nombre"><b>Nombre:</b></label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre" minlength="4" maxlength="25" required>

            <label for="apellido"><b>Apellido:</b></label>
            <input type="text" id="apellido" name="apellido" placeholder="Apellido" minlength="4" maxlength="25"
                required>

            <label for="rol"><b>Rol:</b></label>
            <select id="rol" name="rol" required>
                <option value="" disabled selected>Selecciona un Rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?php echo htmlspecialchars($rol['id_rol'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($rol['nombre_rol'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="sede"><b>Sede:</b></label>
            <select id="sede" name="sede" required>
                <option value="" disabled selected>Selecciona una Sede</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?php echo htmlspecialchars($sede['id_sede'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($sede['nombre_sede'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="contraseña"><b>Contraseña:</b></label>
            <input type="text" id="contraseña" name="contraseña" placeholder="Contraseña" minlength="8" maxlength="30" required>
            <div id="password-error" style="color: red; margin-top: -15px;"></div>

            <input type="hidden" name="insertar" value="insertar">

            <div class="button-group">
                <a href="Mostrar_Usuarios.php" class="back-btn">Volver</a>
                <button type="submit" class="submit-btn">Guardar</button>
            </div>
        </form>
    </div>

</body>

</html>