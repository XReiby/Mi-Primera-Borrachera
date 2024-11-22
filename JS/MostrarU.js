document.addEventListener('DOMContentLoaded', function() {
    const deleteLinks = document.querySelectorAll('a[href*="accion=e"]');
    
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            // Confirmación antes de eliminar
            if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                event.preventDefault(); // Cancela la acción si el usuario cancela
            }
        });
    });
});
