document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(event) {
        const nombre = document.getElementById('nombre').value;
        const rol = document.getElementById('rol').value;
        const sede = document.getElementById('sede').value;
        const contraseña = document.getElementById('contraseña').value;
        
        // Confirmación antes de enviar el formulario
        if (!confirm('¿Estás seguro de que deseas guardar los datos del usuario? (El Id no se podra modificar despues)')) {
            event.preventDefault(); // Cancela el envío del formulario si el usuario cancela
        }
    });
});
