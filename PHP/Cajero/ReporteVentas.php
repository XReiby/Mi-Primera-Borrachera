<?php
// Mostrar errores (solo para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../IC/Conexion.php';
session_start();

// Verificar autenticación
if (empty($_SESSION['usuario'])) {
    header("location: ../../Login.php?error=2");
    exit();
} elseif ($_SESSION['rol'] === 'Mesero') {
    header("location: ../../Index.php");
    exit();
}

// Conexión a la base de datos
$conn = Database::conectar();

// Obtener ID de la sede
$nombre_sede = $_SESSION['sede'];
$query_sede = "SELECT id_sede FROM Sedes WHERE nombre_sede = :nombre_sede";
$stmt_sede = $conn->prepare($query_sede);
$stmt_sede->bindParam(':nombre_sede', $nombre_sede, PDO::PARAM_STR);
$stmt_sede->execute();
$id_sede = $stmt_sede->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fecha_inicio'], $_POST['fecha_fin'])) {
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        if (!strtotime($fecha_inicio) || !strtotime($fecha_fin) || $fecha_inicio > $fecha_fin) {
            echo json_encode(["error" => "Rango de fechas inválido."]);
            exit();
        }

        // Generar consulta
        $query = "
SELECT 
    p.id_producto,
    p.nombre_producto,
    SUM(dp.cantidad) AS cantidad_vendida,
    p.precio AS precio_producto,
    SUM(dp.cantidad * p.precio) AS ganancias, -- Calcular ganancias
    s.nombre_sede
FROM detalle_pedido dp
JOIN pedidos pd ON dp.id_pedido = pd.id_pedido
JOIN mesas m ON pd.id_mesa = m.id_mesa
JOIN sedes s ON m.id_sede = s.id_sede
JOIN productos p ON dp.id_producto = p.id_producto
WHERE pd.fecha BETWEEN :fecha_inicio AND :fecha_fin
AND s.id_sede = :id_sede
GROUP BY p.id_producto, p.nombre_producto, p.precio, s.nombre_sede
ORDER BY p.nombre_producto;
";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':id_sede', $id_sede, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                echo json_encode(["error" => "No hay datos para el rango seleccionado."]);
            } else {
                echo json_encode($resultados);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Css/Cajero/ReporteC.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="../../JS/InventarioC.js"></script>
    <script src="../../JS/Dropdown.js"></script>
    <script src="../../JS/Reporte.js"></script>
    <title>Reporte de Ventas</title>
    <link rel="icon" href="../../Icono/Cajero/Cajero.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <h2>Generar Reporte de Ventas</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
            </a>
        </header>

        <form id="reporteForm" onsubmit="generarReporte(event)">
            <div class="fecha-container">
                <div class="fecha-item">
                    <label for="fecha_inicio">Fecha de inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>

                <div class="fecha-item">
                    <label for="fecha_fin">Fecha de fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>
            </div>
            <button type="submit">Generar Reporte</button>
        </form>



        <div id="tabla-resultados">
            <table>
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre Producto</th>
                        <th>Cantidad Vendida</th>
                        <th>Precio de Venta</th>
                        <th>Ganancias</th>
                        <th>Sede</th>
                    </tr>
                </thead>
                <tbody id="tabla-datos">
                    <!-- Datos dinámicos -->
                </tbody>
            </table>
        </div>

        <div class="export-buttons">
            <button onclick="exportarReporte('csv')">Exportar CSV</button>
            <button onclick="exportarReporte('xlsx')">Exportar XLSX</button>
        </div>
    </div>
</body>

</html>