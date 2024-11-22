<?php
// Conexión a la base de datos
require '../../IC/conexion.php';
$conn = Database::conectar(); // Asegúrate de crear la conexión correctamente

// Iniciar la sesión
session_start();

// Obtener los datos del formulario
$id_pedido = $_POST['id_pedido'];
$metodo_pago = $_POST['metodo_pago'];

// Verificar el inventario para cada producto del pedido
$query = "SELECT DP.id_producto, DP.cantidad, I.cantidad AS stock 
          FROM Detalle_Pedido DP
          JOIN Inventario I ON DP.id_producto = I.id_producto
          WHERE DP.id_pedido = :id_pedido AND I.id_sede = :id_sede";

$stmt = $conn->prepare($query);  // Cambiar $pdo a $conn
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->bindParam(':id_sede', $_SESSION['id_sede'], PDO::PARAM_INT); // Asegúrate de que la sesión esté iniciada y contenga 'id_sede'
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos as $producto) {
    // Validar que no haya inventario negativo
    if ($producto['stock'] < $producto['cantidad']) {
        echo "Error: Inventario insuficiente para el producto " . $producto['id_producto'];
        exit;
    }

    // Validar que las cantidades sean enteros
    if (!is_int($producto['cantidad'])) {
        echo "Error: Solo se permiten cantidades enteras.";
        exit;
    }
}

// Actualizar el estado del pedido a cerrado
$query_update = "UPDATE Pedidos SET estado = false WHERE id_pedido = :id_pedido";
$stmt_update = $conn->prepare($query_update);  // Cambiar $pdo a $conn
$stmt_update->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt_update->execute();

// Mensaje de éxito
$_SESSION['mensaje_exito'] = "Pedido cerrado exitosamente.";
header("Location: CierrePedidos.php"); // Redirigir a la página de cierre de pedidos
exit; // Asegúrate de terminar la ejecución
?>
