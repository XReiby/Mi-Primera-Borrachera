<?php
session_start();
error_reporting(0);
if($_SESSION['usuario']==NULL){
    header("location: ../../Login.php?error=2");
    die();
}elseif($_SESSION['rol']=='Mesero'){
    header("location: ../../Index.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cajero</title>
    <script src="../../JS/Dropdown.js"></script>
    <link rel="stylesheet" href="../../Css/Cajero/GestionC.css">
    <link rel="icon" href="../../Icono/Cajero/Cajero.png">
</head>

<body>
    <div class="main-container">
    <header class="header">
        <h3 id="toggle-title"><?php echo $_SESSION['usuario'] ?></h3>
        <div id="dropdown-content">
            <ul>
            <li><?php echo $_SESSION['usuario'] . ' ' . $_SESSION['apellido']; ?></li>
                <li><?php echo $_SESSION['sede'] ?></li>
                <li><?php echo $_SESSION['rol'] ?></li>
                <li><a href="../Administrador/Cerrar_Sesion.php">Cerrar Sesión</a></li>
            </ul>
        </div>
        <h2>Gestión Cajero</h2>
        <a href="../../Index.php" class="icono-link">
            <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
        </a>
    </header>

        <div class="container">
            <div class="item cierre-pedidos" onclick="location.href='CierrePedidos.php'">
                <img src="../../Fondo/Cajero/cierre-pedidos.jpg" alt="Cierre de Pedidos">
                <div class="overlay">
                    <p>Cierre de Pedidos</p>
                </div>
            </div>
            <div class="item alimentacion-inventario" onclick="location.href='Mostrar_Inventario.php'">
            <img src="../../Fondo/Cajero/G_I.jpg" alt="Alimentación Inventario">
                <div class="overlay">
                    <p>Alimentación Inventario</p>
                </div>
            </div>
            <div class="item reportes-ventas" onclick="location.href='ReporteVentas.php'">
            <img src="../../Fondo/Cajero/reportes-ventas.jpg" alt="Reportes de Ventas">
                <div class="overlay">
                    <p>Reportes de Ventas</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>