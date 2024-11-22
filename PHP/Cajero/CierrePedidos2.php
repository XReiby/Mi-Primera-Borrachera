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
$conn = Database::conectar();  // Usar $conn en lugar de $pdo

// Obtener el id del pedido desde la URL
$id_pedido = $_GET['id_pedido'];

// Obtener los detalles del pedido
$query = "SELECT DP.id_producto, P.nombre_producto, DP.cantidad, DP.precio_producto 
          FROM Detalle_Pedido DP
          JOIN Productos P ON DP.id_producto = P.id_producto
          WHERE DP.id_pedido = :id_pedido";

$stmt = $conn->prepare($query);  // Usar $conn en lugar de $pdo
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->execute();
$detalles_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el total del pedido
$query_total = "SELECT total FROM Pedidos WHERE id_pedido = :id_pedido";
$stmt_total = $conn->prepare($query_total);  // Usar $conn en lugar de $pdo
$stmt_total->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt_total->execute();
$total = $stmt_total->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre Pedidos</title>
    <script src="../../JS/Desplegable.js"></script>
    <link rel="stylesheet" href="../../Css/Cajero/CierrePedidos2.css">
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
            <h2>Cerrar Pedido</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <div class="container">
            <!-- Mostrar detalles del pedido -->
            <div class="detalles-pedido">
                <h2>Detalle del Pedido</h2>
                <ul>
                    <?php foreach ($detalles_pedido as $detalle): ?>
                        <li><?php echo $detalle['nombre_producto']; ?> - Cantidad: <?php echo $detalle['cantidad']; ?> -
                            Precio: $<?php echo $detalle['precio_producto']; ?></li>
                    <?php endforeach; ?>
                </ul>
                <p>Total: $<?php echo $total; ?></p>

                <!-- Selección de método de pago -->
                <form action="procesar_cierre_pedido.php" method="POST">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select name="metodo_pago" id="metodo_pago">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta_credito">Tarjeta de Crédito</option>
                        <option value="tarjeta_debito">Tarjeta de Débito</option>
                    </select>
                    <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                    <button type="submit">Cerrar Pedido</button>
                </form>
            </div>

        </div>
</body>

</html>