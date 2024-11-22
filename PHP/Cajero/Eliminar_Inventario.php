<?php
require_once("../../IC/Conexion.php");
require_once("Crud_Inventario.php");
session_start();

// Verificar si el usuario está logueado
if ($_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
}

// Verificar si se recibió el ID del producto a eliminar
if (isset($_POST['eliminar'])) {
    $id_producto = $_POST['id_producto'];  // El ID del producto a eliminar
    $nombre_sede = $_SESSION['sede'];  // Usamos la sede del usuario actual (almacenada en la sesión)

    // Crear el objeto CRUD
    $CrudInventario = new Crud_Inventario();

    try {
        // Eliminar el producto del inventario en la sede del usuario
        $resultado = $CrudInventario->eliminarDelInventario($id_producto, $nombre_sede);

        // Verificar si la eliminación fue exitosa
        if ($resultado) {
            header("Location: Mostrar_Inventario.php?mensaje=Producto eliminado del inventario con éxito");
        } else {
            header("Location: Mostrar_Inventario.php?error=No se pudo eliminar el producto");
        }
        exit();
    } catch (Exception $e) {
        // Manejar errores y redirigir con un mensaje de error
        header("Location: Mostrar_Inventario.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Redirigir si no se recibió el ID del producto
    header("Location: Mostrar_Inventario.php?error=Parámetros no válidos");
    exit();
}
?>