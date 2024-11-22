    document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const passwordInput = document.getElementById("contraseña");
    const passwordError = document.getElementById("password-error");

    form.addEventListener("submit", function (event) {
        passwordError.textContent = ""; // Limpiar mensajes de error previos
        const password = passwordInput.value;

        // Verificar longitud mínima
        if (password.length < 8) {
            passwordError.textContent = "La contraseña debe tener mínimo 8 caracteres.";
            event.preventDefault(); // Evitar el envío del formulario
            return;
        }

        // Verificar si contiene al menos una letra mayúscula
        if (!/[A-Z]/.test(password)) {
            passwordError.textContent = "La contraseña debe tener al menos una mayúscula.";
            event.preventDefault();
            return;
        }

        // Verificar si contiene al menos una letra minuscula
        if (!/[a-z]/.test(password)) {
            passwordError.textContent = "La contraseña debe tener al menos una minuscula.";
            event.preventDefault();
            return;
        }

        // Verificar si contiene al menos un número
        if (!/\d/.test(password)) {
            passwordError.textContent = "La contraseña debe tener al menos un número.";
            event.preventDefault();
            return;
        }

        // Verificar si contiene al menos un carácter especial
        if (!/[\W_]/.test(password)) {
            passwordError.textContent = "La contraseña debe tener al menos un carácter especial.";
            event.preventDefault();
            return;
        }
    });
});