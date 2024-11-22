function validarFechas(fecha_inicio, fecha_fin) {
    if (!fecha_inicio || !fecha_fin) {
        alert("Por favor, completa las fechas.");
        return false;
    }

    if (new Date(fecha_inicio) > new Date(fecha_fin)) {
        alert("La fecha de inicio no puede ser posterior a la fecha de fin.");
        return false;
    }

    return true;
}

function limpiarTabla() {
    const tablaDatos = document.getElementById('tabla-datos');
    tablaDatos.innerHTML = '';
}

// Función para enviar datos del formulario y mostrar resultados
function generarReporte(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('reporteForm'));
    fetch('ReporteVentas.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const tabla = document.getElementById('tabla-datos');
        tabla.innerHTML = ''; // Limpiar tabla previa
        if (data.error) {
            alert(data.error);
        } else {
            data.forEach(fila => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${fila.id_producto}</td>
                    <td>${fila.nombre_producto}</td>
                    <td>${fila.cantidad_vendida}</td>
                    <td>${fila.precio_producto}</td>
                    <td>${fila.ganancias}</td>
                    <td>${fila.nombre_sede}</td>
                `;
                tabla.appendChild(tr);
            });
        }
    })
    .catch(error => console.error('Error:', error));
}

// Exportar tabla a CSV o XLSX
function exportarReporte(formato) {
    const tabla = document.querySelector('#tabla-resultados table');
    const ws = XLSX.utils.table_to_sheet(tabla);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Reporte');

    // Obtener las fechas del formulario
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;

    // Crear un nombre dinámico con las fechas
    const fechaFormateada = `${fechaInicio.replace(/-/g, '')}_${fechaFin.replace(/-/g, '')}`;
    const nombreArchivo = `ReporteVentas_${fechaFormateada}`;

    // Exportar el archivo con el nombre dinámico
    if (formato === 'csv') {
        XLSX.writeFile(wb, `${nombreArchivo}.csv`, { bookType: 'csv' });
    } else if (formato === 'xlsx') {
        XLSX.writeFile(wb, `${nombreArchivo}.xlsx`);
    }
}
