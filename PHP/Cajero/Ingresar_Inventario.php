<?php
require_once("Crud_Inventario.php");
session_start();
error_reporting(0);

if ($_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] == 'Mesero') {
    header("location: ../../Index.php");
    die();
}

$conn = Database::conectar();

// Obtener la sede del cajero desde la sesión
$nombre_sede = $_SESSION['sede']; // Suponiendo que ya tienes la sede en la sesión

// Si es Administrador, obtener todas las sedes
if ($_SESSION['rol'] === 'Administrador') {
    $querySedes = "SELECT id_sede, nombre_sede FROM Sedes";
    $stmtSedes = $conn->query($querySedes);
    $sedes = $stmtSedes->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Obtener solo la sede asignada si no es Administrador
    $querySede = "SELECT id_sede FROM Sedes WHERE nombre_sede = :nombre_sede";
    $stmtSede = $conn->prepare($querySede);
    $stmtSede->bindValue(':nombre_sede', $nombre_sede, PDO::PARAM_STR);
    $stmtSede->execute();
    $sede = $stmtSede->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que el id_producto y cantidad existan y sean válidos
    if (!isset($_POST['id_producto']) || !isset($_POST['cantidad'])) {
        $error = "Producto y cantidad son obligatorios.";
    } else {
        $id_producto = (int) $_POST['id_producto'];
        $cantidad = (int) $_POST['cantidad'];

        // Obtener la sede seleccionada si es Administrador
        $id_sede = ($_SESSION['rol'] === 'Administrador') ? $_POST['id_sede'] : $sede['id_sede'];

        // Validar entradas
        if ($cantidad <= 0) {
            $error = "La cantidad debe ser mayor a 0.";
        } else {
            $CrudInventario = new Crud_Inventario();

            // Llamar al método para insertar el nuevo producto
            try {
                $CrudInventario->insertar($id_producto, $cantidad, $id_sede);
                // Usar sesiones para el mensaje de éxito
                $_SESSION['success_message'] = "Producto agregado correctamente";
                header("location: Mostrar_Inventario.php");
                exit();
            } catch (Exception $e) {
                $error = $e->getMessage(); // Mensaje completo del error
            }
        }
    }
}

// Obtener todos los productos para seleccionar
$CrudInventario = new Crud_Inventario();
$productos = $CrudInventario->obtenerTodosProductos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Css/Cajero/IngresarIC.css">
    <link rel="icon" href="../../Icono/Administrador/Agregar.png">
    <title>Agregar Producto al Inventario</title>
    <script src="../../JS/Dropdown.js"></script>
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
            <h2>Agregar Producto</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <div class="container">
            <form action="" method="post">
                <label for="id_producto"><b>Producto:</b></label>
                <select id="id_producto" name="id_producto" required>
                    <option value="">Selecciona un producto</option>
                    <?php foreach ($productos as $producto) { ?>
                        <option value="<?php echo htmlspecialchars($producto['id_producto'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($producto['nombre_producto'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="cantidad"><b>Cantidad:</b></label>
                <input type="number" id="cantidad" name="cantidad" min="1" max="1000" required>

                <!-- Mostrar el campo de sede solo si el usuario es Administrador -->
                <?php if ($_SESSION['rol'] === 'Administrador') { ?>
                    <label for="id_sede"><b>Sede:</b></label>
                    <select id="id_sede" name="id_sede" required>
                        <option value="">Selecciona una sede</option>
                        <?php foreach ($sedes as $sede) { ?>
                            <option value="<?php echo htmlspecialchars($sede['id_sede'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($sede['nombre_sede'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php } ?>
                    </select>
                <?php } ?>

                <div class="button-group">
                    <a href="Mostrar_Inventario.php" class="back-btn">Volver</a>
                    <button type="submit" class="submit-btn">Agregar Producto</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>