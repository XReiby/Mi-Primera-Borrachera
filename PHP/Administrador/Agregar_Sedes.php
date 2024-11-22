<?php
session_start();
if ($_SESSION['rol'] != 'Administrador' && $_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] != 'Administrador') {
    header("location: ../../Index.php");
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_mesa'])) {
    require_once("Crud_Usuarios.php");
    $CrudUsuario = new Crud_Usuarios();
    $sedeId = $_POST['sede_id'];
    $CrudUsuario->agregarMesa($sedeId);
    header("Location: " . $_SERVER['PHP_SELF']); // Recarga la página
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm-btn'])) {
    require_once("Crud_Usuarios.php");
    $CrudUsuario = new Crud_Usuarios();
    $nombre_sede = $_POST['sedeName'];
    $CrudUsuario->agregarSede($nombre_sede);
    header("Location: " . $_SERVER['PHP_SELF']); // Recarga la página
    exit();
}

function ordenarSedesAlfabeticamente(&$sedes) {
    $n = count($sedes);
    for ($i = 0; $i < $n - 1; $i++) {
        $minIndex = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if (strcasecmp($sedes[$j]->getNombre_Sede(), $sedes[$minIndex]->getNombre_Sede()) < 0) {
                $minIndex = $j;
            }
        }
        // Intercambiar
        $temp = $sedes[$i];
        $sedes[$i] = $sedes[$minIndex];
        $sedes[$minIndex] = $temp;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../JS/Desplegable.js"></script>
    <script>
        function mostrarPopup() {
            document.getElementById("popup").style.display = "block";
        }

        function cerrarPopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>
    <title>Mostrar Sedes</title>
    <link rel="stylesheet" href="../../Css/Administrador/Sedes.css">
    <link rel="icon" href="../../Icono/Administrador/MU.png">
</head>

<body>
    <header class="header">
        <h3 id="toggle-title"><?php echo $_SESSION['usuario'] ?></h3>
        <div id="dropdown-content">
            <ul>
                <li><?php echo $_SESSION['usuario'] . ' ' . $_SESSION['apellido']; ?></li>
                <li><?php echo $_SESSION['sede'] ?></li>
                <li><?php echo $_SESSION['rol'] ?></li>
                <li><a href="Cerrar_Sesion.php">Cerrar Sesión</a></li>
            </ul>
        </div>
        <h2>Sedes</h2>
        <a href="../../Index.php" class="icono-link">
            <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
        </a>
    </header>

    <div class="container">
        <?php
        require_once("Crud_Usuarios.php");
        $CrudUsuario = new Crud_Usuarios();
        $ListaSedes = $CrudUsuario->mostrarSedes();
        
        // Ordenar las sedes alfabéticamente usando el algoritmo de selección
        ordenarSedesAlfabeticamente($ListaSedes);
        ?>
        <div class="user-grid">
            <?php foreach ($ListaSedes as $sede) { ?>
                <div class="user-card">
                    <div class="user-info">
                        <div class="user-image">
                            <img src="../../Icono/Administrador/User.png" alt="Usuario">
                        </div>
                        <h3><?php echo htmlspecialchars($sede->getNombre_Sede(), ENT_QUOTES, 'UTF-8'); ?></h3>
                    </div>
                    <div class="user-mesas">
                        <p>Mesas: <?php echo $CrudUsuario->contarMesasPorSede($sede->getId_Sede()); ?></p>
                        <form method="POST" action="" style="display: inline-block;">
                            <input type="hidden" name="sede_id" value="<?php echo $sede->getId_Sede(); ?>">
                            <button type="submit" name="add_mesa" class="add-mesa-btn">Agregar Mesa</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="add-user" onclick="mostrarPopup()">
            <img src="../../Icono/Administrador/Agregar.png" alt="Agregar" class="user-icon">
        </div>
    </div>

    <div id="popup" class="popup" style="display: none;">
    <div class="popup-content">
        <h2>Agregar Sede</h2>
        <form method="POST" action="" style="display: inline-block;">
            <label for="sedeName">Nombre de la sede:</label>
            <input type="text" id="sedeName" name="sedeName" required>
            <div class="popup-buttons">
                <button type="button" id="closePopup" class="btn cancel-btn" onclick="cerrarPopup()">Volver</button>
                <button type="submit" name="confirm-btn" class="btn confirm-btn">Confirmar</button>
            </div>
        </form>
    </div>
</div>


</body>

</html>