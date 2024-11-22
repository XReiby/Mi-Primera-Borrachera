<?php
session_start();
error_reporting(0);
if ($_SESSION['usuario'] == NULL) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

// Incluir la conexión a la base de datos
include '../../IC/Conexion.php';
$conn = Database::conectar();

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$id_pedido = $data['id_pedido'];
$detalles = $data['detalles'];

// Comenzar una transacción
$conn->beginTransaction();

try {
    // Primero, eliminar los detalles actuales del pedido
    $stmtDelete = $conn->prepare("DELETE FROM Detalle_Pedido WHERE id_pedido = :id_pedido");
    $stmtDelete->bindValue(":id_pedido", $id_pedido);
    $stmtDelete->execute();

    // Variables para calcular el nuevo total
    $nuevo_total = 0;

    // Insertar los nuevos detalles del pedido
    $stmtInsert = $conn->prepare("INSERT INTO Detalle_Pedido (id_producto, id_pedido, cantidad, costo_producto, precio_producto) VALUES (:id_producto, :id_pedido, :cantidad, :costo_producto, :precio_producto)");

    foreach ($detalles as $detalle) {
        $id_producto = $detalle['idProducto'];
        $cantidad = $detalle['cantidad'];

        // Obtener el precio y costo del producto
        $stmtProducto = $conn->prepare("SELECT precio, costo FROM Productos WHERE id_producto = :id_producto");
        $stmtProducto->bindValue(":id_producto", $id_producto);
        $stmtProducto->execute();
        $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $precio_producto = $producto['precio'];
            $costo_producto = $producto['costo'];
            $subtotal = $precio_producto * $cantidad;
            $nuevo_total += $subtotal;

            // Insertar el detalle del producto
            $stmtInsert->bindValue(":id_producto", $id_producto);
            $stmtInsert->bindValue(":id_pedido", $id_pedido);
            $stmtInsert->bindValue(":cantidad", $cantidad);
            $stmtInsert->bindValue(":costo_producto", $costo_producto);
            $stmtInsert->bindValue(":precio_producto", $precio_producto);
            $stmtInsert->execute();
        }
    }

    // Actualizar el total del pedido
    $stmtUpdate = $conn->prepare("UPDATE Pedidos SET total = :total WHERE id_pedido = :id_pedido");
    $stmtUpdate->bindValue(":total", $nuevo_total);
    $stmtUpdate->bindValue(":id_pedido", $id_pedido);
    $stmtUpdate->execute();

    // Confirmar la transacción
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // En caso de error, revertir la transacción
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
