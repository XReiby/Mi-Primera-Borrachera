<?php
session_start();
error_reporting(0);
if($_SESSION['usuario']==NULL){
    header("location: ../../Login.php?error=2");
    die();
}elseif($_SESSION['rol']=='Cajero'){
    header("location: ../../Index.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesero</title>
    <script src="../../JS/Desplegable.js"></script>
    <link rel="stylesheet" href="../../Css/Mesero/GestionM.css">
    <link rel="icon" href="../../Icono/Mesero/Mesero.png">
</head>

<body>
    <div class="main-container"> <!-- Contenedor principal -->
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
        <h2>Gestión Mesero</h2>
        <a href="../../Index.php" class="icono-link">
            <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
        </a>
    </header>
        <div class="container">
            <div class="item nuevo-pedido" onclick="location.href='NuevoPedido.php'">
            <img src="../../Fondo/Mesero/nuevo-pedido.png" alt="Nuevo Pedido">
                <div class="overlay">
                    <p>Nuevo Pedido</p>
                </div>
            </div>
            <div class="item gestion-pedidos" onclick="location.href='GestionPedidos.php'">
            <img src="../../Fondo/Mesero/gestion-pedidos.png" alt="Gestión de Pedidos">
                <div class="overlay">
                    <p>Gestión de Pedidos</p>
                </div>
            </div>
            <div class="item consultar-inventario" onclick="location.href='ConsultarInventario.php'">
            <img src="../../Fondo/Mesero/consultar-inventario.png" alt="Consultar Inventario">
                <div class="overlay">
                    <p>Consultar Inventario</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
