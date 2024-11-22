<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Css/style.css">
    <link rel="icon" href="./Icono/Index.png">
    <script src="./JS/Error_Login.js"></script>
    <title>Iniciar Sesión</title>
</head>

<body>
    <div class="contenedor-principal">
        <div class="logo-div">
            <img src="./Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
        </div>
        <div class="form-div">
            <div class="login-box">
                <form action="./PHP/Administrador/Validar_Usuario.php" method="post">
                    <img src="./Icono/Index.png" alt="icono" class="logo">
                    <h1>INICIAR SESIÓN</h1>

                    <div class="input-container">
                        <input type="text" name="id" placeholder="ID de Usuario" required>
                    </div>

                    <div class="input-container">
                        <input type="password" name="contrasena" placeholder="Contraseña" required>
                    </div>

                    <!-- Mostrar mensaje de error aquí -->
                    <?php if (isset($_GET['error'])): ?>
                        <p id="error-message" style="color: red;">
                            <?php
                            if ($_GET['error'] == '1') {
                                echo 'Clave o ID Incorrecto';
                            } elseif ($_GET['error'] == '2') {
                                echo 'Acceso Denegado, Por Favor Iniciar Sesión';
                            }
                            ?>
                        </p>
                    <?php endif; ?>

                    <input type="submit" value="LOGIN" class="login-button">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
