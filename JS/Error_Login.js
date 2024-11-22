window.onload = function() {
    var params = new URLSearchParams(window.location.search);

    if (params.get('error')) {
        var errorMessage = document.getElementById('error-message');
        errorMessage.style.display = 'block';

        if (params.get('error') === '1') {
            errorMessage.textContent = 'Clave o Usuario Incorrecto';
        } else if (params.get('error') === '2') {
            errorMessage.textContent = 'Acceso Denegado, Por Favor Iniciar Sesión';
        }

        document.querySelector('input[name="usuario"]').focus(); // Poner foco en el campo usuario
    }
}
