<?php
require_once("Crud_Inventario.php");
session_start();
error_reporting(0);

if (!isset($_SESSION['usuario'])) {
    header("location: ../../Login.php?error=2");
    exit();
} elseif ($_SESSION['rol'] === 'Mesero') {
    header("location: ../../Index.php");
    exit();
}

$conn = Database::conectar();
// Obtener la sede del cajero desde la sesión
$nombre_sede = $_SESSION['sede']; // Suponiendo que ya tienes la sede en la sesión

// Inicializar variable para los datos del producto
$producto_actual = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = (int) $_GET['id_producto'];
    $nuevaCantidad = (int) $_POST['cantidad'];
    $id_sede = (int) $_GET['id_sede'];
    // Validar entradas
    if ($nuevaCantidad <= 0) {
        $error = "La cantidad debe ser mayor a 0.";
    } else {
        $CrudInventario = new Crud_Inventario();

        // Llamar al método para actualizar la cantidad del producto
        try {
            $CrudInventario->actualizar($id_producto, $nuevaCantidad, $id_sede);
            header("location: Mostrar_Inventario.php");
            exit();
        } catch (Exception $e) {
            $error = "Error al actualizar el inventario: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
} if (isset($_GET['id_producto']) && isset($_GET['id_sede'])) {
    $id_producto = (int) $_GET['id_producto'];
    $id_sede = (int) $_GET['id_sede'];

    $CrudInventario = new Crud_Inventario();
    $producto_actual = $CrudInventario->obtenerProductoPorId($id_producto, $id_sede);

    if (!$producto_actual) {
        echo "No se encontró el producto o hubo un error.";
        exit();
    }
} else {
    echo "Faltan parámetros en la URL.";
    exit();
}


// Obtener todos los productos para seleccionar
$CrudInventario = new Crud_Inventario();
$productos = $CrudInventario->obtenerInventarioPorSede($id_sede); // Obtener inventario por sede
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Css/Cajero/ActualizarIC.css">
    <link rel="icon" href="../../Icono/Administrador/Actualizar.png">
    <title>Actualizar Inventario</title>
    <script src="../../JS/Desplegable.js"></script>
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
            <h2>Actualizar Inventario</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <div class="container">
            <!-- Mensaje de error si existe -->
            <?php if (isset($error)) { ?>
                <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <form action="" method="post">
                <?php if ($producto_actual) { ?>
                    <h3>Datos del Producto Actual:</h3>
                    <p><b>Nombre:</b>
                        <span><?php echo htmlspecialchars($producto_actual['nombre_producto'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </p>
                    <p><b>Cantidad Actual:</b>
                        <span><?php echo htmlspecialchars($producto_actual['cantidad'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </p>

                    <?php if ($_SESSION['rol'] == 'Administrador') { ?>
                        <p><b>Sede</b>
                            <span><?php echo htmlspecialchars($producto_actual['nombre_sede'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </P>
                    <?php } ?>

                    <!-- Input oculto para el id del producto -->
                    <input type="hidden" name="id_producto"
                        value="<?php echo htmlspecialchars($producto_actual['id_producto'], ENT_QUOTES, 'UTF-8'); ?>">

                    <label for="cantidad">Nueva Cantidad:</label>
                    <input type="number" name="cantidad" id="cantidad" required min="1"
                        placeholder="Ingrese nueva cantidad">
                <?php } else {
                    // Si no hay producto actual, redirigir a la página de mostrar inventario
                    header("Location: Mostrar_Inventario.php");
                    exit();
                } ?>

                <div class="button-group">
                    <a href="Mostrar_Inventario.php" class="back-btn">Volver</a>
                    <button type="submit" class="submit-btn">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>