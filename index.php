<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indice página principal</title>
</head>
<body>
    <?php 
        require 'comunes/auxiliar.php';

        comprobar_logueado();
        head();
    ?>
    <p class="tiendas"><a href="tiendas/index.php">Consultar tiendas</a></p>
    <p class="videojuegos"><a href="videojuegos/index.php">Consultar videojuegos</a></p>
    <p class="usuarios"><a href="usuarios/principal.php">Consultar usuarios</a></p>
</body>
</html>