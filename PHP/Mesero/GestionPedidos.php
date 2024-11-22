<?php
session_start();
error_reporting(0);

if ($_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] == 'Cajero') {
    header("location: ../../Index.php");
    die();
}

include '../../IC/Conexion.php'; // Incluir la conexión a la base de datos
$conn = Database::conectar(); // Llamar a la conexión PDO

// Si el rol es mesero, solo obtener los pedidos de la sede del mesero
if ($_SESSION['rol'] == 'Mesero') {
    // Modificamos la consulta para obtener solo los pedidos de la sede del mesero
    $pedidos = $conn->prepare("SELECT p.id_pedido, m.numero_mesa, p.estado, p.fecha, p.total 
                               FROM Pedidos p 
                               JOIN Mesas m ON p.id_mesa = m.id_mesa
                               WHERE m.id_sede = (SELECT id_sede FROM Sedes WHERE nombre_sede = :sede)"); 
    $pedidos->bindValue(':sede', $_SESSION['sede']);
    $pedidos->execute();
    $pedidos = $pedidos->fetchAll(PDO::FETCH_ASSOC);
} else if ($_SESSION['rol'] == 'Administrador') {
    // Si es administrador, obtenemos todos los pedidos, incluyendo la sede de cada mesa
    $pedidos = $conn->query("SELECT p.id_pedido, m.numero_mesa, p.estado, p.fecha, p.total, s.nombre_sede 
                             FROM Pedidos p 
                             JOIN Mesas m ON p.id_mesa = m.id_mesa
                             JOIN Sedes s ON m.id_sede = s.id_sede")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <script src="../../JS/Desplegable.js"></script>
    <link rel="stylesheet" href="../../Css/Mesero/GesP.css">
    <link rel="icon" href="../../Icono/Mesero/Mesero4.webp">
</head>
<body>
    <div class="main-container">
        <header class="header">
            <h3 id="toggle-title"><?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <div id="dropdown-content">
                <ul>
                    <li><?php echo htmlspecialchars($_SESSION['usuario'] . ' ' . $_SESSION['apellido'], ENT_QUOTES, 'UTF-8'); ?></li>
                    <li><?php echo htmlspecialchars($_SESSION['sede'], ENT_QUOTES, 'UTF-8'); ?></li>
                    <li><?php echo htmlspecialchars($_SESSION['rol'], ENT_QUOTES, 'UTF-8'); ?></li>
                    <li><a href="../Administrador/Cerrar_Sesion.php">Cerrar Sesión</a></li>
                </ul>
            </div>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Logo" class="Icono">
            </a>
        </header>
        <main>
            <div class="container">
                <h2>Gestión de Pedidos</h2>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Mesa</th>
                            <?php if ($_SESSION['rol'] == 'Administrador'): ?>
                                <th>Sede</th> <!-- Solo se muestra la columna de sede si el rol es Administrador -->
                            <?php endif; ?>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pedido['id_pedido'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($pedido['numero_mesa'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php if ($_SESSION['rol'] == 'Administrador'): ?>
                                    <td><?php echo htmlspecialchars($pedido['nombre_sede'], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Mostrar la sede solo si es administrador -->
                                <?php endif; ?>
                                <td><?php echo $pedido['estado'] ? 'Abierto' : 'Cerrado'; ?></td>
                                <td><?php echo htmlspecialchars($pedido['fecha'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($pedido['total'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php if ($pedido['estado']): ?>
                                        <a href="../Mesero/ModificarPedido.php?id=<?php echo $pedido['id_pedido']; ?>" class="volver">Modificar</a>
                                    <?php else: ?>
                                        <button disabled>Modificación no permitida</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="../Mesero/Index.php" class="volver">Volver</a>
            </div>
        </main>
    </div>
</body>
</html>
