<?php
require_once("../../IC/Conexion.php");
require_once("Inventario.php"); // Incluyendo la clase Inventario
require_once("../Mesero/Pedido.php");
require_once("../Mesero/Detalle_Pedido.php");

class Crud_Inventario
{
    public function __construct()
    {
    }

    // Insertar un producto en el inventario (para la sede del cajero)
    public function insertar($id_producto, $cantidad, $id_sede)
    {
        // Validar que la cantidad sea un entero positivo
        if (!is_int($cantidad) || $cantidad < 0) {
            throw new Exception("La cantidad debe ser un número entero positivo.");
        }

        $Db = Database::conectar();
        // Verificar si el producto ya existe en el inventario de la sede
        $verificar = $Db->prepare("SELECT * FROM Inventario WHERE id_producto = :id_producto AND id_sede = :id_sede");
        $verificar->bindValue(":id_producto", $id_producto);
        $verificar->bindValue(":id_sede", $id_sede);
        $verificar->execute();

        if ($verificar->rowCount() > 0) {
            // Si ya existe, se actualiza la cantidad
            $this->actualizar($id_producto, $cantidad, $id_sede);
        } else {
            // Si no existe, se inserta como nuevo
            $insert = $Db->prepare("INSERT INTO Inventario (id_producto, cantidad, id_sede) VALUES (:id_producto, :cantidad, :id_sede)");
            $insert->bindValue(":id_producto", $id_producto);
            $insert->bindValue(":cantidad", $cantidad);
            $insert->bindValue(":id_sede", $id_sede);
            $insert->execute();
        }
    }

    // Actualizar cantidad de un producto en el inventario
    public function actualizar($id_producto, $nuevaCantidad, $id_sede)
    {
        // Validar que la nueva cantidad sea un entero positivo
        if (!is_int($nuevaCantidad) || $nuevaCantidad < 0) {
            throw new Exception("La cantidad debe ser un número entero positivo.");
        }

        $Db = Database::conectar();
        $actualizar = $Db->prepare("UPDATE Inventario SET cantidad = :cantidad WHERE id_producto = :id_producto AND id_sede = :id_sede");
        $actualizar->bindValue(":cantidad", $nuevaCantidad);
        $actualizar->bindValue(":id_producto", $id_producto);
        $actualizar->bindValue(":id_sede", $id_sede);
        $actualizar->execute();
    }

    public function eliminarDelInventario($id_producto, $nombre_sede)
    {
        $conn = Database::conectar();  // Conexión a la base de datos

        // Obtener el id_sede correspondiente al nombre_sede
        $querySede = "SELECT id_sede FROM Sedes WHERE nombre_sede = :nombre_sede";
        $stmtSede = $conn->prepare($querySede);
        $stmtSede->bindValue(':nombre_sede', $nombre_sede, PDO::PARAM_STR);
        $stmtSede->execute();
        $sede = $stmtSede->fetch(PDO::FETCH_ASSOC);

        // Validar si se encontró la sede
        if ($sede) {
            $id_sede = $sede['id_sede'];  // Obtener el id_sede correcto

            // Eliminar el producto del inventario con el id_sede e id_producto
            $queryEliminar = "DELETE FROM Inventario WHERE id_producto = :id_producto AND id_sede = :id_sede";
            $stmtEliminar = $conn->prepare($queryEliminar);
            $stmtEliminar->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmtEliminar->bindValue(':id_sede', $id_sede, PDO::PARAM_INT);

            if ($stmtEliminar->execute()) {
                return true;  // Eliminación exitosa
            } else {
                return false;  // Error en la eliminación
            }
        } else {
            return false;  // Error si no se encuentra la sede
        }
    }

    // Obtener inventario de productos en una sede específica
    public function obtenerInventarioPorSede($id_sede)
    {
        $Db = Database::conectar();
        $select = $Db->prepare("SELECT i.id_producto, p.nombre_producto, i.cantidad
                                FROM Inventario i
                                JOIN Productos p ON i.id_producto = p.id_producto
                                WHERE i.id_sede = :id_sede");
        $select->bindValue(":id_sede", $id_sede);
        $select->execute();

        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un producto por ID, incluyendo su cantidad en la sede
    public function obtenerProductoPorId($id_producto, $id_sede)
{
    $Db = Database::conectar();
    $select = $Db->prepare("SELECT i.cantidad, p.nombre_producto, s.nombre_sede
                            FROM Inventario i
                            JOIN Productos p ON i.id_producto = p.id_producto
                            JOIN Sedes s ON i.id_sede = s.id_sede
                            WHERE i.id_producto = :id_producto AND i.id_sede = :id_sede");
    $select->bindValue(":id_producto", $id_producto);
    $select->bindValue(":id_sede", $id_sede);
    $select->execute();

    return $select->fetch(PDO::FETCH_ASSOC); // Retorna la cantidad, nombre del producto y nombre de la sede
}

    // Método para obtener todos los productos
    public function obtenerTodosProductos()
    {
        $Db = Database::conectar();
        $select = $Db->query("SELECT * FROM Productos");
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

        // Método para ordenar alfabéticamente una lista de productos
        public function ordenarProductos($productos) {
            $n = count($productos);
            for ($i = 0; $i < $n - 1; $i++) {
                for ($j = 0; $j < $n - $i - 1; $j++) {
                    if (strcasecmp($productos[$j]['nombre_producto'], $productos[$j + 1]['nombre_producto']) > 0) {
                        // Intercambiar elementos
                        $temp = $productos[$j];
                        $productos[$j] = $productos[$j + 1];
                        $productos[$j + 1] = $temp;
                    }
                }
            }
            return $productos;
        }

    public function obtenerInventarioDeTodasLasSedes($busqueda = '')
    {
        $conn = Database::conectar();

        // Crear la consulta base
        $sql = "
            SELECT p.id_producto, p.nombre_producto, i.cantidad, s.nombre_sede
            FROM inventario i
            INNER JOIN productos p ON i.id_producto = p.id_producto
            INNER JOIN sedes s ON i.id_sede = s.id_sede
        ";
        // Si hay una búsqueda, agregar el filtro de búsqueda
        if (!empty($busqueda)) {
            $sql .= " WHERE p.nombre_producto ILIKE :busqueda ";
        }
        // Ordenar los resultados por sede y luego por nombre de producto
        $sql .= " ORDER BY s.nombre_sede ASC, p.nombre_producto ASC";

        $stmt = $conn->prepare($sql);

        // Si hay una búsqueda, vincular el parámetro
        if (!empty($busqueda)) {
            $stmt->bindValue(':busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        
        // Obtener los resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>