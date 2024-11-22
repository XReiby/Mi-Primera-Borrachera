let productosPedido = []; // Array para almacenar productos y cantidades

document.getElementById('agregar').onclick = function() {
    const idProducto = document.getElementById('productoselector').value;
    const cantidad = parseInt(document.getElementById('cantidad').value);

    // Validar que la cantidad sea un número entero positivo
    if (isNaN(cantidad) || cantidad <= 0) {
        alert('Por favor, ingresa una cantidad válida (número entero positivo).');
        return; // Salir de la función si la validación falla
    }

    const nombreProducto = document.querySelector(`#productoselector option[value='${idProducto}']`).textContent;
    const precioProducto = parseFloat(document.querySelector(`#productoselector option[value='${idProducto}']`).dataset.precio);

    const index = productosPedido.findIndex(p => p.id_producto === idProducto);
    if (index > -1) {
        productosPedido[index].cantidad += cantidad; // Sumar cantidad
    } else {
        productosPedido.push({ id_producto: idProducto, nombre: nombreProducto, cantidad: cantidad, precio: precioProducto });
    }

    actualizarListaProductos();
    document.getElementById('cantidad').value = ''; // Limpiar campo de cantidad
};

function actualizarListaProductos() {
    const lista = document.getElementById('lista-productos');
    lista.innerHTML = ''; // Limpiar la lista

    productosPedido.forEach(producto => {
        const li = document.createElement('li');
        li.textContent = `${producto.nombre} - Cantidad: ${producto.cantidad} - Precio: ${producto.precio.toFixed(2)}`;
        lista.appendChild(li);
    });

    document.getElementById('productos').value = JSON.stringify(productosPedido);
}

// Validar que solo se puedan ingresar números enteros positivos en el campo de cantidad
document.getElementById('cantidad').addEventListener('input', function() {
    // Reemplaza cualquier cosa que no sea un dígito
    this.value = this.value.replace(/[^0-9]/g, '');
});
