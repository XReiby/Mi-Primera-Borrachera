<?php
session_start();
if ($_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] == 'Cajero') {
    header("location: ../../Index.php");
    die();
}

include '../../IC/Conexion.php';

// Conectar a la base de datos
$conn = Database::conectar();

// Consulta para obtener los datos del inventario
if ($_SESSION['rol'] == 'Administrador') {
    // Si es administrador, muestra el inventario de todas las sedes
    $query = "SELECT i.id_inventario, p.nombre_producto, i.cantidad, p.precio, s.nombre_sede 
              FROM inventario i
              INNER JOIN productos p ON i.id_producto = p.id_producto
              INNER JOIN sedes s ON i.id_sede = s.id_sede
              ORDER BY s.nombre_sede ASC, p.nombre_producto ASC";  // Ordenar por sede y producto
} else {
    // Si es otro rol (por ejemplo, Mesero), filtra por sede
    $querySede = "SELECT id_sede FROM sedes WHERE nombre_sede = :nombre_sede";
    $stmtSede = $conn->prepare($querySede);
    $stmtSede->bindValue(':nombre_sede', $_SESSION['sede'], PDO::PARAM_STR);
    $stmtSede->execute();
    $sede = $stmtSede->fetch(PDO::FETCH_ASSOC);

    if ($sede) {
        $id_sede = $sede['id_sede'];

        $query = "SELECT i.id_inventario, p.nombre_producto, i.cantidad, p.precio, s.nombre_sede 
                  FROM inventario i
                  INNER JOIN productos p ON i.id_producto = p.id_producto
                  INNER JOIN sedes s ON i.id_sede = s.id_sede
                  WHERE i.id_sede = :id_sede
                  ORDER BY p.nombre_producto ASC";  // Ordenar por nombre del producto
    } else {
        $query = null;
        $result = []; // Si no se encuentra la sede, no hay resultados
    }
}

// Preparación y ejecución de la consulta
if ($query) {
    $stmt = $conn->prepare($query);

    // Solo vinculamos el id_sede si no es administrador
    if ($_SESSION['rol'] != 'Administrador') {
        $stmt->bindValue(':id_sede', $id_sede, PDO::PARAM_INT);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los resultados
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Inventario</title>
    <link rel="stylesheet" href="../../Css/Mesero/ConsultaIM.css">
    <link rel="icon" href="../../Icono/Mesero/Mesero6.webp">
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
            <h2>Consulta de Inventario</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <main>
            <div class="container">
                <div class="buscador">
                    <form action="">
                        <input type="text" name="buscar" placeholder="Buscar Producto">
                        <button type="submit"><img src="../../Fondo/Mesero/magnifying-glass-solid.svg" alt=""></button>
                    </form>
                </div>

                <table border="1">
                    <thead>
                        <tr colspan="5">
                            <th colspan="5">Inventario Disponible</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th>Nombre Producto</th>
                            <th>Cantidad Disponible</th>
                            <th>Precio</th>
                            <?php if ($_SESSION['rol'] == 'Administrador') { ?>
                                <th>Sede</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Mostrar los resultados de la consulta
                        if ($result) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nombre_producto']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['cantidad']) . "</td>";
                                echo "<td>" . number_format($row['precio']) . "</td>";
                                if ($_SESSION['rol'] == 'Administrador') {
                                echo "<td>" . htmlspecialchars($row['nombre_sede']) . "</td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No hay inventario disponible</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="botonescontainer">
                <a href="../Mesero/Index.php" class="volver">Volver</a>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
