<?php
    session_start();

    if($_SESSION['rol']==null){
        header("location: ./Login.php");
    }elseif($_SESSION['rol']== 'Mesero'){
        header("location: ./PHP/Mesero/Index.php");
    }elseif($_SESSION['rol']== 'Cajero'){
        header("location: ./PHP/Cajero/Index.php");
    }elseif($_SESSION['rol']== 'Administrador'){
        header("location: ./PHP/Administrador/Index.php");
    }

?>