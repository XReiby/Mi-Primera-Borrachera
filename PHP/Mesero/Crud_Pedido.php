<?php
require_once("../../IC/Conexion.php");
require_once("../Cajero/Inventario.php");
require_once("Pedido.php");
require_once("Detalle_Pedido.php");

class Crud_Pedido
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // Crear un nuevo pedido
    public function crearPedido($id_mesa, $productos)
    {
        $this->db->beginTransaction(); // Iniciar transacción

        try {
            $fecha = date("Y-m-d H:i:s");
            $total = 0; // Inicializar total

            // Insertar el pedido en la tabla Pedidos
            $insertPedido = $this->db->prepare("INSERT INTO Pedidos (id_mesa, estado, fecha, total) VALUES (:id_mesa, TRUE, :fecha, :total)");
            $insertPedido->bindValue(":id_mesa", $id_mesa);
            $insertPedido->bindValue(":fecha", $fecha);
            $insertPedido->bindValue(":total", $total);
            $insertPedido->execute();

            $id_pedido = $this->db->lastInsertId(); // Obtener ID del pedido creado

            // Insertar los productos en el detalle del pedido
            foreach ($productos as $producto) {
                $id_producto = $producto['id_producto'];
                $cantidad = $producto['cantidad'];

                // Validar que la cantidad sea un entero positivo
                if (!is_int($cantidad) || $cantidad <= 0) {
                    throw new Exception("La cantidad debe ser un número entero positivo.");
                }

                // Obtener el producto para calcular el precio
                $infoProducto = $this->obtenerProductoPorId($id_producto);
                if (!$infoProducto) {
                    throw new Exception("El producto con ID $id_producto no existe.");
                }
                
                $precio_producto = $infoProducto['precio'];
                $costo_producto = $infoProducto['costo'];
                $total += $precio_producto * $cantidad; // Calcular el total

                // Insertar en Detalle_Pedido
                $insertDetalle = $this->db->prepare("INSERT INTO Detalle_Pedido (id_producto, id_pedido, cantidad, costo_producto, precio_producto) VALUES (:id_producto, :id_pedido, :cantidad, :costo_producto, :precio_producto)");
                $insertDetalle->bindValue(":id_producto", $id_producto);
                $insertDetalle->bindValue(":id_pedido", $id_pedido);
                $insertDetalle->bindValue(":cantidad", $cantidad);
                $insertDetalle->bindValue(":costo_producto", $costo_producto);
                $insertDetalle->bindValue(":precio_producto", $precio_producto);
                $insertDetalle->execute();
            }

            // Actualizar el total del pedido
            $this->actualizarTotalPedido($id_pedido, $total);

            $this->db->commit(); // Confirmar transacción
        } catch (Exception $e) {
            $this->db->rollBack(); // Revertir transacción en caso de error
            throw new Exception("Error al crear el pedido: " . $e->getMessage());
        }
    }

    // Actualizar un pedido existente
    public function modificarPedido($id_pedido, $productos)
    {
        // Verificar si el pedido está abierto
        $pedido = $this->obtenerPedidoPorId($id_pedido);
        if (!$pedido || !$pedido['estado']) {
            throw new Exception("No se puede modificar un pedido cerrado.");
        }

        $this->db->beginTransaction(); // Iniciar transacción

        try {
            $total = 0; // Inicializar total

            // Actualizar los productos en el detalle del pedido
            foreach ($productos as $producto) {
                $id_producto = $producto['id_producto'];
                $cantidad = $producto['cantidad'];

                // Validar que la cantidad sea un entero positivo
                if (!is_int($cantidad) || $cantidad < 0) {
                    throw new Exception("La cantidad debe ser un número entero positivo.");
                }

                // Si la cantidad es 0, se elimina el producto
                if ($cantidad == 0) {
                    $this->eliminarProductoDelPedido($id_pedido, $id_producto);
                } else {
                    // Obtener el producto para calcular el precio
                    $infoProducto = $this->obtenerProductoPorId($id_producto);
                    if (!$infoProducto) {
                        throw new Exception("El producto con ID $id_producto no existe.");
                    }
                    
                    $precio_producto = $infoProducto['precio'];
                    $costo_producto = $infoProducto['costo'];
                    $total += $precio_producto * $cantidad; // Calcular el total

                    // Comprobar si el producto ya está en el pedido
                    if ($this->productoEnPedido($id_pedido, $id_producto)) {
                        $this->actualizarDetallePedido($id_pedido, $id_producto, $cantidad, $costo_producto, $precio_producto);
                    } else {
                        // Insertar en Detalle_Pedido
                        $this->agregarProductoAPedido($id_pedido, $id_producto, $cantidad, $costo_producto, $precio_producto);
                    }
                }
            }

            // Actualizar el total del pedido
            $this->actualizarTotalPedido($id_pedido, $total);

            $this->db->commit(); // Confirmar transacción
        } catch (Exception $e) {
            $this->db->rollBack(); // Revertir transacción en caso de error
            throw new Exception("Error al modificar el pedido: " . $e->getMessage());
        }
    }

    // Eliminar un producto del pedido
    private function eliminarProductoDelPedido($id_pedido, $id_producto)
    {
        $eliminarDetalle = $this->db->prepare("DELETE FROM Detalle_Pedido WHERE id_pedido = :id_pedido AND id_producto = :id_producto");
        $eliminarDetalle->bindValue(":id_pedido", $id_pedido);
        $eliminarDetalle->bindValue(":id_producto", $id_producto);
        $eliminarDetalle->execute();
    }

    // Agregar un producto al pedido
    private function agregarProductoAPedido($id_pedido, $id_producto, $cantidad, $costo_producto, $precio_producto)
    {
        $insertDetalle = $this->db->prepare("INSERT INTO Detalle_Pedido (id_producto, id_pedido, cantidad, costo_producto, precio_producto) VALUES (:id_producto, :id_pedido, :cantidad, :costo_producto, :precio_producto)");
        $insertDetalle->bindValue(":id_producto", $id_producto);
        $insertDetalle->bindValue(":id_pedido", $id_pedido);
        $insertDetalle->bindValue(":cantidad", $cantidad);
        $insertDetalle->bindValue(":costo_producto", $costo_producto);
        $insertDetalle->bindValue(":precio_producto", $precio_producto);
        $insertDetalle->execute();
    }

    // Actualizar un producto en el pedido
    private function actualizarDetallePedido($id_pedido, $id_producto, $cantidad, $costo_producto, $precio_producto)
    {
        $actualizarDetalle = $this->db->prepare("UPDATE Detalle_Pedido SET cantidad = :cantidad, costo_producto = :costo_producto, precio_producto = :precio_producto WHERE id_pedido = :id_pedido AND id_producto = :id_producto");
        $actualizarDetalle->bindValue(":cantidad", $cantidad);
        $actualizarDetalle->bindValue(":costo_producto", $costo_producto);
        $actualizarDetalle->bindValue(":precio_producto", $precio_producto);
        $actualizarDetalle->bindValue(":id_pedido", $id_pedido);
        $actualizarDetalle->bindValue(":id_producto", $id_producto);
        $actualizarDetalle->execute();
    }

    // Actualizar el total del pedido
    private function actualizarTotalPedido($id_pedido, $total)
    {
        $actualizarTotal = $this->db->prepare("UPDATE Pedidos SET total = :total WHERE id_pedido = :id_pedido");
        $actualizarTotal->bindValue(":total", $total);
        $actualizarTotal->bindValue(":id_pedido", $id_pedido);
        $actualizarTotal->execute();
    }

    // Obtener pedido por ID
    private function obtenerPedidoPorId($id_pedido)
    {
        $pedido = $this->db->prepare("SELECT * FROM Pedidos WHERE id_pedido = :id_pedido");
        $pedido->bindValue(":id_pedido", $id_pedido);
        $pedido->execute();
        return $pedido->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener producto por ID
    private function obtenerProductoPorId($id_producto)
    {
        $producto = $this->db->prepare("SELECT * FROM Productos WHERE id_producto = :id_producto");
        $producto->bindValue(":id_producto", $id_producto);
        $producto->execute();
        return $producto->fetch(PDO::FETCH_ASSOC);
    }

    // Comprobar si un producto está en el pedido
    private function productoEnPedido($id_pedido, $id_producto)
    {
        $detalle = $this->db->prepare("SELECT * FROM Detalle_Pedido WHERE id_pedido = :id_pedido AND id_producto = :id_producto");
        $detalle->bindValue(":id_pedido", $id_pedido);
        $detalle->bindValue(":id_producto", $id_producto);
        $detalle->execute();
        return $detalle->rowCount() > 0;
    }
}

session_start();  // Asegúrate de que la sesión se haya iniciado

// Manejo de peticiones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crudPedido = new Crud_Pedido();

    try {
        if (isset($_POST['guardar'])) {
            $id_mesa = $_POST['id_mesa'];
            $productos = json_decode($_POST['productos'], true); // Asumiendo que los productos llegan en formato JSON
    
            // Crear el pedido
            $crudPedido->crearPedido($id_mesa, $productos);
            
            // Guardar el mensaje de éxito en la variable de sesión
            $_SESSION['mensaje'] = "Pedido creado exitosamente.";
    
            // Redirigir a la página NuevoPedido.php
            header("location: NuevoPedido.php");
            exit(); // Asegurarse de detener el script después de la redirección
        }
    } catch (Exception $e) {
        // Manejo de excepciones, en caso de error
        $_SESSION['mensaje'] = "Error al crear el pedido: " . $e->getMessage();
        header("location: NuevoPedido.php");  // Redirigir en caso de error también
        exit();
    }
}

?>
