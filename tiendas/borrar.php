<?php

require '../comunes/auxiliar.php';


if (isset($_POST['id'])) {

    $id = trim($_POST['id']);

    $pdo = conectar();

    $sent = $pdo->prepare("DELETE FROM tienda WHERE id = :id");
    
    $sent->execute([':id' => $id]);
}

setcookie('borrar','1');
volver();