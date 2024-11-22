<?php
require_once("Crud_Inventario.php");
session_start();

if ($_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] == 'Mesero') {
    header("location: ../../Index.php");
    die();
}

// Conexión a la base de datos
require_once('../../IC/Conexion.php');
$conn = Database::conectar();

// Obtener el id_sede del cajero logueado
$nombre_sede = $_SESSION['sede'];

// Consulta para obtener el id_sede basado en el nombre de la sede
$query_sede = "SELECT id_sede FROM Sedes WHERE nombre_sede = :nombre_sede";
$stmt_sede = $conn->prepare($query_sede);
$stmt_sede->bindParam(':nombre_sede', $nombre_sede, PDO::PARAM_STR);
$stmt_sede->execute();
$resultado_sede = $stmt_sede->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el id_sede
if ($resultado_sede) {
    $id_sede = $resultado_sede['id_sede'];

    // Consulta para obtener los pedidos abiertos de la sede del cajero, incluyendo estado y fecha
    $query = "SELECT P.id_pedido, M.numero_mesa, P.total, P.estado, P.fecha
              FROM Pedidos P
              JOIN Mesas M ON P.id_mesa = M.id_mesa
              WHERE M.id_sede = :id_sede AND P.estado = true";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_sede', $id_sede, PDO::PARAM_INT);
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "No se encontró la sede.";
    die();
}

// Mostrar el mensaje de éxito si está establecido
if (isset($_SESSION['mensaje_exito'])) {
    echo "<div class='mensaje-exito'>" . $_SESSION['mensaje_exito'] . "</div>";
    unset($_SESSION['mensaje_exito']); // Limpiar el mensaje después de mostrarlo
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre Pedidos</title>
    <script src="../../JS/Desplegable.js"></script>
    <script src="../../JS/Carousel.js"></script>
    <link rel="stylesheet" href="../../Css/Cajero/CierrePedidos.css">
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
            <h2>Pedidos</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <!-- Contenedor del carrusel de pedidos -->
        <div class="carousel-container">
            <div class="pedidos-abiertos">
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <h3>Mesa: <?php echo $pedido['numero_mesa']; ?></h3>
                        <p>Estado: <?php echo $pedido['estado'] ? 'Abierto' : 'Cerrado'; ?></p>
                        <p>Fecha: <?php echo date('d-m-Y H:i:s', strtotime($pedido['fecha'])); ?></p>
                        <p>Total: $<?php echo $pedido['total']; ?></p>
                        <a href="CierrePedidos2.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" class="btn-cerrar">Cerrar
                            Pedido</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="flechas-container">
                <button class="flecha carousel-prev">&#10094;</button>
                <button class="flecha carousel-next">&#10095;</button>
            </div>
        </div>
    </div>

    <script src="../../JS/Carousel.js"></script>
</body>

</html>