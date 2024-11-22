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

$conn = Database::conectar();

// Obtener el valor de la búsqueda
$busqueda = isset($_POST['buscarProducto']) ? trim($_POST['buscarProducto']) : '';

// Crear el objeto CRUD
$CrudInventario = new Crud_Inventario();

// Variable para almacenar la lista de inventario
$ListaInventario = [];

// Si el rol es 'Administrador', mostrar inventario de todas las sedes
if ($_SESSION['rol'] == 'Administrador') {
    // Obtener inventario de todas las sedes, con opción de búsqueda
    $ListaInventario = $CrudInventario->obtenerInventarioDeTodasLasSedes($busqueda);
} else {
    // Si no es administrador, obtener el inventario de la sede del usuario
    $querySede = "SELECT id_sede FROM sedes WHERE nombre_sede = :nombre_sede";
    $stmtSede = $conn->prepare($querySede);
    $stmtSede->bindValue(':nombre_sede', $_SESSION['sede'], PDO::PARAM_STR);
    $stmtSede->execute();
    $sede = $stmtSede->fetch(PDO::FETCH_ASSOC);

    // Validar si se encontró la sede
    if ($sede) {
        $id_sede = $sede['id_sede']; // Obtener el id_sede
        // Cargar inventario filtrado por sede
        $ListaInventario = $CrudInventario->obtenerInventarioPorSede($id_sede);
    } else {
        echo "No se encontró la sede.";
        exit();
    }
}

// Ordenar alfabéticamente los productos por su nombre
$ListaInventario = $CrudInventario->ordenarProductos($ListaInventario);

if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['mensaje'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../JS/InventarioC.js"></script>
    <script src="../../JS/Dropdown.js"></script>
    <link rel="stylesheet" href="../../Css/Cajero/InventarioC.css">
    <link rel="icon" href="../../Icono/Cajero/Cajero.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Gestión de Inventario</title>
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
            <h2>Gestión de Inventario</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <div class="container">
            <!-- Sección de Búsqueda de Productos -->
            <form method="post" action="" class="Busqueda">
                <div class="search-container">
                    <input type="text" name="buscarProducto" class="busqueda-input" placeholder="Buscar Producto"
                        value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>" required>
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <!-- Mensaje de búsqueda -->
            <?php if ($busqueda && empty($ListaInventario)) { ?>
                <p>No se encontraron productos para la búsqueda:
                    <?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php } ?>

            <!-- Tabla de Productos -->
            <table>
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre Producto</th>
                        <th>Cantidad Disponible</th>
                        <?php if ($_SESSION['rol'] == 'Administrador') { ?>
                            <th>Sede</th>
                        <?php } ?>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ListaInventario)) { ?>
                        <?php foreach ($ListaInventario as $Producto) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($Producto['id_producto'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($Producto['nombre_producto'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($Producto['cantidad'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if ($_SESSION['rol'] == 'Administrador') {
                                    $query = "
                                    SELECT 
                                        p.id_producto, 
                                        p.nombre_producto, 
                                        i.cantidad, 
                                        s.nombre_sede
                                    FROM 
                                        productos p
                                    JOIN 
                                        inventario i ON p.id_producto = i.id_producto
                                    JOIN 
                                        sedes s ON i.id_sede = s.id_sede
                                    ORDER BY 
                                        s.nombre_sede, p.nombre_producto
                                ";
                                $stmt = $conn->query($query);
                                $ListaInventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <td><?php echo htmlspecialchars($Producto['nombre_sede'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php } ?>
                                <td>
                                    <div class="actions">
                                        <a
                                            href="Actualizar_Inventario.php?id_producto=<?php echo htmlspecialchars($Producto['id_producto'], ENT_QUOTES, 'UTF-8'); ?>&id_sede=<?php echo $id_sede ?>">
                                            <div class="img_Actualizar">
                                                <h5>Editar</h5>
                                                <img src="../../Icono/Administrador/Actualizar.png" alt="Actualizar" width="24"
                                                    height="24">
                                            </div>
                                        </a>
                                        <form method="post" action="Eliminar_Inventario.php">
                                            <input type="hidden" name="id_producto"
                                                value="<?php echo htmlspecialchars($Producto['id_producto'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" name="eliminar" class="btn_eliminar">
                                                <div class="img_Eliminar">
                                                    <h5>Eliminar</h5>
                                                    <img src="../../Icono/Administrador/Eliminar.png" alt="Eliminar" width="24"
                                                        height="24">
                                                </div>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>
                        <tr>
                            <td colspan="5">No hay productos disponibles.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="button-container">
                <div class="add-user">
                    <a href="Ingresar_Inventario.php">
                        <img src="../../Icono/Administrador/Agregar.png" alt="Agregar" class="user-icon">
                    </a>
                </div>
                <a href="Index.php" class="action-btn">Volver</a>
            </div>
        </div>
    </div>
</body>

</html>