<?php
session_start();
error_reporting(0);

if (empty($_SESSION['usuario'])) {
    header("location: ../../Login.php?error=2");
    exit();
}

if ($_SESSION['rol'] === 'Cajero') {
    header("location: ../../Index.php");
    exit();
}

include '../../IC/Conexion.php'; // Conexión con PDO
$conn = Database::conectar();

// Verificar el rol del usuario
$rol_usuario = $_SESSION['rol'];
if (!in_array($rol_usuario, ['Administrador', 'Mesero'])) {
    header("location: ../../Index.php");
    exit();
}

// Recuperar el último pedido (si existe)
$pedido = isset($pedido) ? $pedido : null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Css/Mesero/NuevoP.css">
    <link rel="icon" href="../../Icono/Mesero/Mesero3.webp">
    <title>Nuevo Pedido</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../JS/Desplegable.js"></script>
</head>

<body>
    <header class="header">
        <h3 id="toggle-title"><?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <div id="dropdown-content">
            <ul>
                <li><?php echo htmlspecialchars($_SESSION['usuario'] . ' ' . $_SESSION['apellido'], ENT_QUOTES, 'UTF-8'); ?>
                </li>
                <li><?php echo htmlspecialchars($_SESSION['sede'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><?php echo htmlspecialchars($_SESSION['rol'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><a href="../Administrador/Cerrar_Sesion.php">Cerrar Sesión</a></li>
            </ul>
        </div>
        <h2>Nuevo Pedido</h2>
        <a href="../../Index.php" class="icono-link">
            <img src="../../Icono/Icono.png" alt="Logo" class="Icono">
        </a>
    </header>

    <main>
        <?php
        // Mostrar el mensaje si existe
        if (isset($_SESSION['mensaje'])) {
            echo "<p class='mensaje-exito'>" . $_SESSION['mensaje'] . "</p>";

            // Limpiar el mensaje para no mostrarlo en futuras solicitudes
            unset($_SESSION['mensaje']);
        }
        ?>
        <div class="container">

            <h1>Crear Nuevo Pedido</h1>
            <form id="formPedido" method="POST" action="Crud_Pedido.php">
                <!-- Select de mesas -->
                <select id="mesa" name="id_mesa" required>
                    <option value="">Seleccione una mesa</option>
                    <?php
                    $mesas = [];
                    if ($_SESSION['rol'] === 'Administrador') {
                        // Consulta para obtener las mesas con su sede, ordenadas por sede y número de mesa
                        $stmtMesas = $conn->query("
                            SELECT mesas.id_mesa, mesas.numero_mesa, sedes.nombre_sede 
                            FROM mesas
                            INNER JOIN sedes ON mesas.id_sede = sedes.id_sede
                            ORDER BY sedes.nombre_sede, mesas.numero_mesa
                        ");
                        $mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        // Consulta para obtener las mesas de la sede del usuario, ordenadas por número de mesa
                        $querySede = "SELECT id_sede FROM sedes WHERE nombre_sede = :nombre_sede";
                        $stmt = $conn->prepare($querySede);
                        $stmt->bindValue(':nombre_sede', $_SESSION['sede'], PDO::PARAM_STR);
                        $stmt->execute();
                        $sede = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($sede) {
                            $stmtMesas = $conn->prepare("SELECT mesas.id_mesa, mesas.numero_mesa
                                                        FROM mesas
                                                        WHERE mesas.id_sede = :id_sede
                                                        ORDER BY mesas.numero_mesa");
                            $stmtMesas->bindValue(':id_sede', $sede['id_sede'], PDO::PARAM_INT);
                            $stmtMesas->execute();
                            $mesas = $stmtMesas->fetchAll(PDO::FETCH_ASSOC);
                        }
                    }

                    foreach ($mesas as $mesa) {
                        $infoSede = isset($mesa['nombre_sede']) ? " - Sede: {$mesa['nombre_sede']}" : "";
                        echo "<option value='{$mesa['id_mesa']}'>Mesa {$mesa['numero_mesa']}{$infoSede}</option>";
                    }
                    ?>
                </select>

                <!-- Select de productos -->
                <select id="productoselector" name="id_producto" required>
                    <option value="">Seleccione un producto</option>
                    <?php
                    // Consulta para obtener los productos ordenados por nombre
                    $productos = $conn->query("SELECT id_producto, nombre_producto, precio FROM productos ORDER BY nombre_producto")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($productos as $producto) {
                        echo "<option value='{$producto['id_producto']}' data-precio='{$producto['precio']}'>" . htmlspecialchars($producto['nombre_producto'], ENT_QUOTES, 'UTF-8') . "</option>";
                    }
                    ?>
                </select>

                <input type="number" id="cantidad" name="cantidad" placeholder="Cantidad" min="1">

                <h3>Productos en el Pedido</h3>
                <ul id="lista-productos"></ul>

                <h3>Total del Pedido</h3>
                <p id="total">Total del Pedido: $0</p>

                <input type="hidden" name="productos" id="productos">

                <div class="botonescontainer">
                    <button type="submit" name="guardar" id="guardar">Crear Pedido</button>
                    <a href="../Mesero/Index.php" class="volver">Volver</a>
                    <button type="button" id="agregar">Agregar</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        let productosPedido = []; // Array para almacenar productos y cantidades

        function actualizarListaProductos() {
            const lista = document.getElementById('lista-productos');
            lista.innerHTML = ''; // Limpiar la lista

            let total = 0; // Inicializar el total

            productosPedido.forEach(producto => {
                const li = document.createElement('li');
                li.textContent = `${producto.nombre} - Cantidad: ${producto.cantidad} - Precio: $${producto.precio}`;
                lista.appendChild(li);

                total += producto.cantidad * producto.precio; // Calcular total
            });

            const totalElement = document.getElementById('total');
            totalElement.textContent = `Total del Pedido: $${total}`; // Mostrar como entero

            document.getElementById('productos').value = JSON.stringify(productosPedido);
        }

        document.getElementById('agregar').onclick = function () {
            const idProducto = document.getElementById('productoselector').value;
            const cantidad = parseInt(document.getElementById('cantidad').value, 10);
            const nombreProducto = document.querySelector(`#productoselector option[value='${idProducto}']`).textContent;
            const precioProducto = parseInt(document.querySelector(`#productoselector option[value='${idProducto}']`).dataset.precio, 10); // Convertir a entero

            if (!idProducto || isNaN(cantidad) || cantidad <= 0) {
                alert("Seleccione un producto válido y una cantidad mayor a 0.");
                return;
            }

            const index = productosPedido.findIndex(p => p.id_producto === idProducto);
            if (index === -1) {
                productosPedido.push({ id_producto: idProducto, nombre: nombreProducto, cantidad: cantidad, precio: precioProducto });
            } else {
                productosPedido[index].cantidad += cantidad;
            }

            actualizarListaProductos();
            document.getElementById('cantidad').value = '';
        };
    </script>
</body>

</html>