<?php
session_start();
error_reporting(0);
if ($_SESSION['rol'] != 'Administrador' && $_SESSION['usuario'] == NULL) {
    header("location: ../../Login.php?error=2");
    die();
} elseif ($_SESSION['rol'] != 'Administrador') {
    header("location: ../../Index.php");
    die();
}

// Función de Insertion Sort para ordenar alfabéticamente por nombre
function insertionSort($usuarios) {
    for ($i = 1; $i < count($usuarios); $i++) {
        $key = $usuarios[$i];
        $j = $i - 1;

        // Comparar y mover los elementos que son mayores que $key
        while ($j >= 0 && strcmp(strtolower($usuarios[$j]->getNombre()), strtolower($key->getNombre())) > 0) {
            $usuarios[$j + 1] = $usuarios[$j];
            $j = $j - 1;
        }
        $usuarios[$j + 1] = $key;
    }
    return $usuarios;
}

require_once("Crud_Usuarios.php");
$CrudUsuario = new Crud_Usuarios();
$ListaUsuarios = $CrudUsuario->mostrar();

// Ordenar los usuarios utilizando Insertion Sort
$ListaUsuarios = insertionSort($ListaUsuarios);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../JS/MostrarU.js" defer></script>
    <script src="../../JS/Desplegable.js"></script>
    <title>Mostrar Usuarios</title>
    <link rel="stylesheet" href="../../Css/Administrador/MostrarU.css">
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
        <h2>Gestión de Usuarios</h2>
        <a href="../../Index.php" class="icono-link">
            <img src="../../Icono/Icono.png" alt="Mi Primera Borrachera Logo" class="Icono">
        </a>
    </header>

    <div class="container">
        <div class="user-grid">
            <?php foreach ($ListaUsuarios as $Usuario) { ?>
                <div class="user-card">
                    <div class="user-image">
                        <img src="../../Icono/Administrador/User.png" alt="Usuario" class="user">
                    </div>

                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($Usuario->getNombre(), ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($Usuario->getApellido(), ENT_QUOTES, 'UTF-8'); ?></h3>
                        <ul>
                            <li><?php echo htmlspecialchars($Usuario->getNombreRol(), ENT_QUOTES, 'UTF-8'); ?></li>
                            <li><?php echo htmlspecialchars($Usuario->getNombreSede(), ENT_QUOTES, 'UTF-8'); ?></li>
                        </ul>
                    </div>

                    <div class="actions">
                        <a href="Actualizar_Usuario.php?Id=<?php echo $Usuario->getID(); ?>&accion=a">
                            <div class="img_Actualizar">
                                <h5>Editar</h5>
                                <img src="../../Icono/Administrador/Actualizar.png" alt="Actualizar" width="24" height="24">
                            </div>
                        </a>
                        <a href="Administrar_Usuarios.php?Id=<?php echo $Usuario->getID(); ?>&accion=e">
                            <div class="img_Eliminar">
                                <h5>Eliminar</h5>
                                <img src="../../Icono/Administrador/Eliminar.png" alt="Eliminar" width="24" height="24">
                            </div>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="add-user">
            <a href="Ingresar_Usuario.php">
                <img src="../../Icono/Administrador/Agregar.png" alt="Agregar" class="user-icon">
            </a>
        </div>
    </div>
</body>

</html>
