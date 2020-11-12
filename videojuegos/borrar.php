<?php
session_start();

require '../comunes/auxiliar.php';


if (isset($_POST['id'])) {

    $id = trim($_POST['id']);

    $pdo = conectar();

    $sent = $pdo->prepare('DELETE FROM videojuego WHERE id = :id');
    
    $sent->execute(['id' => $id]);

}

$_SESSION['flash'] = 'Se ha borrado la fila correctamente';
volver();