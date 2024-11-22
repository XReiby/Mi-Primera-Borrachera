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

// Obtener el ID del pedido de la URL
$id_pedido = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Obtener detalles del pedido
$pedido = $conn->prepare("SELECT p.id_pedido, m.numero_mesa, p.estado, p.fecha, p.total 
                          FROM Pedidos p 
                          JOIN Mesas m ON p.id_mesa = m.id_mesa 
                          WHERE p.id_pedido = :id_pedido");
$pedido->bindValue(":id_pedido", $id_pedido);
$pedido->execute();
$pedido_data = $pedido->fetch(PDO::FETCH_ASSOC);

// Obtener productos del pedido
$productos = $conn->prepare("SELECT dp.id_producto, pr.nombre_producto, dp.cantidad, dp.precio_producto 
                              FROM Detalle_Pedido dp 
                              JOIN Productos pr ON dp.id_producto = pr.id_producto 
                              WHERE dp.id_pedido = :id_pedido");
$productos->bindValue(":id_pedido", $id_pedido);
$productos->execute();
$productos_data = $productos->fetchAll(PDO::FETCH_ASSOC);

// Verificar si el pedido existe
if (!$pedido_data) {
    die("Pedido no encontrado.");
}

// Obtener lista de productos disponibles para agregar
$productos_disponibles = $conn->query("SELECT id_producto, nombre_producto, precio FROM Productos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Pedido</title>
    <link rel="stylesheet" href="../../Css/Mesero/ModificarP.css">
    <link rel="icon" href="../../Icono/Mesero/Mesero5.webp">
    <script src="../../JS/Desplegable.js"></script>
    <script>
        let productosPedido = <?php echo json_encode($productos_data); ?>;

        function permitirEdicion(btn) {
            const fila = btn.closest('tr');
            const cantidadInput = fila.querySelector('.cantidad-producto');
            cantidadInput.disabled = false;
            cantidadInput.focus();
            cantidadInput.onchange = calcularTotal;
        }

        function eliminarProducto(btn) {
            const fila = btn.closest('tr');
            const idProducto = fila.querySelector('.id-producto').value;

            // Remover el producto del array
            productosPedido = productosPedido.filter(p => p.id_producto != idProducto);

            // Remover la fila de la tabla
            fila.remove();

            // Recalcular el total
            calcularTotal();
        }

        function calcularTotal() {
            const filas = document.querySelectorAll('tbody tr');
            let total = 0;

            filas.forEach(fila => {
                const cantidadInput = fila.querySelector('.cantidad-producto');
                const precio = parseFloat(fila.querySelector('.precio').textContent);
                const cantidad = parseInt(cantidadInput.value);

                if (!isNaN(cantidad) && cantidad > 0) {
                    const subtotal = precio * cantidad;
                    fila.querySelector('.subtotal').textContent = subtotal.toFixed(2);
                    total += subtotal;
                } else {
                    fila.querySelector('.subtotal').textContent = '0.00';
                }
            });

            document.getElementById('total-general').textContent = total.toFixed(2);
        }


        function guardarCambios() {
            const filas = document.querySelectorAll('tbody tr');
            const detalles = [];

            filas.forEach(fila => {
                const idProducto = fila.querySelector('.id-producto').value;
                const cantidad = fila.querySelector('.cantidad-producto').value;
                detalles.push({ idProducto, cantidad });
            });

            fetch('ActualizarPedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id_pedido: <?php echo $id_pedido; ?>, detalles })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cambios guardados exitosamente.');
                        window.location.reload(); // Recargar la página para reflejar los cambios
                    } else {
                        alert('Error al guardar cambios: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function agregarProducto() {
            const selectProducto = document.getElementById('producto-nuevo');
            const idProducto = selectProducto.value;
            const nombreProducto = selectProducto.options[selectProducto.selectedIndex].text;
            const precioProducto = parseFloat(selectProducto.options[selectProducto.selectedIndex].dataset.precio);
            const cantidadNueva = parseInt(document.getElementById('cantidad-nuevo').value);

            if (!idProducto || isNaN(cantidadNueva) || cantidadNueva <= 0) {
                alert("Seleccione un producto válido y una cantidad mayor a 0.");
                return;
            }

            // Verificar si el producto ya existe en el pedido
            const productoExistente = productosPedido.find(p => p.id_producto == idProducto);
            if (productoExistente) {
                // Actualizar la cantidad del producto existente
                productoExistente.cantidad += cantidadNueva;

                // Actualizar la fila en la tabla
                const filaExistente = document.querySelector(`tr[data-id='${idProducto}']`);
                filaExistente.querySelector('.cantidad-producto').value = productoExistente.cantidad;
                filaExistente.querySelector('.subtotal').textContent = (productoExistente.cantidad * productoExistente.precio_producto).toFixed(2);
            } else {
                // Agregar un nuevo producto
                productosPedido.push({
                    id_producto: idProducto,
                    nombre_producto: nombreProducto,
                    cantidad: cantidadNueva,
                    precio_producto: precioProducto,
                });

                // Agregar una nueva fila a la tabla
                const nuevaFila = `
                    <tr data-id="${idProducto}">
                        <input type="hidden" class="id-producto" value="${idProducto}">
                        <td>${nombreProducto}</td>
                        <td>
                            <input type="number" value="${cantidadNueva}" min="1" class="cantidad-producto" onchange="calcularTotal()">
                        </td>
                        <td class="precio">${precioProducto.toFixed(2)}</td>
                        <td class="subtotal">${(cantidadNueva * precioProducto).toFixed(2)}</td>
                        <td>
                            <button type="button" class="eliminar" onclick="eliminarProducto(this)"><img src="../../Fondo/Mesero/B_Eliminar.svg" alt=""></button>
                        </td>
                    </tr>
                `;
                document.querySelector('tbody').insertAdjacentHTML('beforeend', nuevaFila);
            }

            calcularTotal(); // Recalcular el total
        }
    </script>
</head>

<body>
    <div class="main-container">
        <header class="header">
            <h3 id="toggle-title"><?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <h2>Modificar Pedido</h2>
            <a href="../../Index.php" class="icono-link">
                <img src="../../Icono/Icono.png" alt="Logo" class="Icono">
            </a>
        </header>
        <main>
            <div class="container">
                <h3>Detalles del Pedido</h3>
                <p>Mesa: <?php echo htmlspecialchars($pedido_data['numero_mesa'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Fecha: <?php echo htmlspecialchars($pedido_data['fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>Total: <span id="total-general"><?php echo intval($pedido_data['total']); ?></span></p>

                <form id="form-pedido">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_data as $producto): ?>
                                <tr
                                    data-id="<?php echo htmlspecialchars($producto['id_producto'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" class="id-producto"
                                        value="<?php echo htmlspecialchars($producto['id_producto'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <td><?php echo htmlspecialchars($producto['nombre_producto'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <input type="number"
                                            value="<?php echo htmlspecialchars($producto['cantidad'], ENT_QUOTES, 'UTF-8'); ?>"
                                            min="1" class="cantidad-producto" onchange="calcularTotal()">
                                    </td>
                                    <td class="precio">
                                        <?php echo htmlspecialchars($producto['precio_producto'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td class="subtotal">
                                        <?php echo htmlspecialchars($producto['cantidad'] * $producto['precio_producto'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <button type="button" class="eliminar" onclick="eliminarProducto(this)"><img
                                                src="../../Fondo/Mesero/B_Eliminar.svg" alt=""></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
                <h3>Agregar Producto</h3>
                <select id="producto-nuevo">
                    <option value="">Seleccionar Producto</option>
                    <?php foreach ($productos_disponibles as $producto): ?>
                        <option value="<?php echo htmlspecialchars($producto['id_producto'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-precio="<?php echo htmlspecialchars($producto['precio'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($producto['nombre_producto'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="cantidad-nuevo" placeholder="Cantidad" min='1'>
                <div class="botonescontainer">
                    <button class="volver" onclick="window.location.href='GestionPedidos.php'">Volver</button>
                    <button class="guardar" onclick="guardarCambios()">Guardar Cambios</button>
                    <button class="agregar" type="button" onclick="agregarProducto()">Agregar Producto</button>
                </div>
        </main>
    </div>
</body>

</html>