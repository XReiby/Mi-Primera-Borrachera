<?php
session_start();
error_reporting(0);
if ($_SESSION['rol'] != 'Administrador' && $_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] != 'Administrador') {
    header("location: ../../Index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <script src="../../JS/Desplegable.js"></script>
    <link rel="stylesheet" href="../../Css/Administrador/Style.css">
    <link rel="icon" href="../../Icono/Administrador/.php">
</head>

<body>
    <div class="main-container">
        <header class="header">
            <h3 id="toggle-title"><?php echo $_SESSION['usuario'] ?></h3>
            <div id="dropdown-content">
                <ul>
                    <li><?php echo $_SESSION['sede'] ?></li>
                    <li><?php echo $_SESSION['rol'] ?></li>
                    <li><a href="Cerrar_Sesion.php">Cerrar Sesión</a></li>
                </ul>
            </div>
            <h2>Administrador</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <div class="container">
            <div class="item Mesero" onclick="location.href='../Mesero/Index.php'">
                <img src="../../Fondo/Administrador/Mesero.png" alt="Mesero">
                <div class="overlay">
                    <p>Mesero</p>
                </div>
            </div>
            <div class="item Gestion_Usuarios" onclick="location.href='Mostrar_Usuarios.php'">
                <img src="../../Fondo/Administrador/Usuarios.jpeg" alt="Gestion_Usuarios">
                <div class="overlay">
                    <p>Gestion de Usuarios</p>
                </div>
            </div>
            <div class="item Cajero" onclick="location.href='../Cajero/Index.php'">
                <img src="../../Fondo/Administrador/Cajero.jpeg" alt="Cajero">
                <div class="overlay">
                    <p>Cajero</p>
                </div>
            </div>
            <div class="item Agregar_Sedes" onclick="location.href='Agregar_Sedes.php'">
                <img src="../../Fondo/Administrador/Sedes.jpg" alt="Agregar_Sedes">
                <div class="overlay">
                    <p>Agregar Sedes y/o Mesas</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>