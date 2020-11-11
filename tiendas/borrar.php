<?php

if (isset($_POST['id'])) {

    $id = trim($_POST['id']);

    $pdo = new PDO('pgsql:host=localhost;dbname=bd', 'jose', 'jose');

    $sent = $pdo->prepare("DELETE FROM videojuegos WHERE id = :id");
    
    $sent->execute([':id' => $id]);
}

setcookie('borrar','1');
header('location: index.php');