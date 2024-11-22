document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const guardarCambiosBtn = document.querySelector("button[name='guardar_cambios']");

    guardarCambiosBtn.addEventListener("click", function (e) {
        e.preventDefault(); // Evitar el envío automático del formulario

        // Validar que los campos de cantidad y precio sean enteros no negativos
        const cantidadInputs = document.querySelectorAll("input[name='cantidad[]']");
        const precioInputs = document.querySelectorAll("input[name='precio[]']");

        let valid = true;

        cantidadInputs.forEach(input => {
            if (parseInt(input.value) < 0) {
                alert("La cantidad no puede ser negativa.");
                valid = false;
            }
        });

        precioInputs.forEach(input => {
            const precioValue = parseInt(input.value);
            if (precioValue < 0) {
                alert("El precio no puede ser negativo.");
                valid = false;
            } else if (!Number.isInteger(precioValue)) {
                alert("El precio debe ser un número entero.");
                valid = false;
            }
        });

        if (valid) {
            const confirmacion = confirm("¿Estás seguro de que quieres guardar los cambios?");
            // Si el usuario confirma, envía el formulario
            if (confirmacion) {
                form.submit();
            }
        }
    });

    // Inicializar tabla
    const productos = document.querySelectorAll(".producto");
    let currentPage = 1;
    const rowsPerPage = 5;
    const totalPages = Math.ceil(productos.length / rowsPerPage);

    function renderPage() {
        productos.forEach((producto, index) => {
            if (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) {
                producto.style.display = "";
            } else {
                producto.style.display = "none";
            }
        });
    }

    window.prevPage = function () {
        if (currentPage > 1) {
            currentPage--;
            renderPage();
        }
    };

    window.nextPage = function () {
        if (currentPage < totalPages) {
            currentPage++;
            renderPage();
        }
    };

    // Filtro de búsqueda
    document.getElementById("buscarProducto").addEventListener("input", function () {
        const filtro = this.value.toLowerCase();
        productos.forEach(producto => {
            const nombreProducto = producto.querySelector("input[name='nombre_producto[]']").value.toLowerCase();
            producto.style.display = nombreProducto.includes(filtro) ? "" : "none";
        });
    });

    // Inicializar tabla
    renderPage();
});

// Función para mostrar/ocultar el menú desplegable
function toggleDropdown() {
    const dropdown = document.getElementById("dropdown-content");
    dropdown.style.display = dropdown.style.display === "none" || dropdown.style.display === "" ? "block" : "none";
}

// Cerrar el menú al hacer clic fuera de él
window.onclick = function(event) {
    if (!event.target.matches('#toggle-title')) {
        const dropdowns = document.getElementById("dropdown-content");
        if (dropdowns.style.display === "block") {
            dropdowns.style.display = "none";
        }
    }
}
