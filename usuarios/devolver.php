<?php
session_start();

require '../comunes/auxiliar.php';

comprobar_logueado();

if (!isset($_POST['csrf_token'])) {
    volver();
    return;
} elseif ($_POST['csrf_token'] != $_SESSION['csrf_token']) {
    volver();
    return;
}

const PAR = [
    'video_tipo'=> 'Tipo',
    'vnombre' => 'Nombre',
    'precio' => 'Precio',
    'pegi' => 'Pegi',
    'fecha_alt' => 'Fecha de alta',
    'fecha_baj' => 'Fecha de baja',
    'disponibilidad' => 'Disponibilidad',
    'usuario_id' => 'Usuario',
    'tienda_id' => 'Tienda', 
];

if (isset($_POST['id'])) {

    $id = recoger_post('id');

    $pdo = conectar();

    $sent = $pdo->prepare("SELECT id
                                , video_tipo
                                , vnombre
                                , precio
                                , pegi
                                , to_char(fecha_alt, 'DD-MM-YYYY') AS fecha_alt
                                , to_char(fecha_baj, 'DD-MM-YYYY') AS fecha_baj
                                , disponibilidad
                                , usuario_id
                                , tienda_id
                            FROM videojuego
                           WHERE id = :id");
    $sent->execute(['id' => $id]);
    $fila = $sent->fetch();

    extract($fila);

    $disponibilidad = '1';
    $fecha_alt = date('d-m-Y');
    $fecha_baj = null;
    $usuario_id = null;

    $marcadores = [];
    foreach (PAR as $k => $v) {
        $marcadores[] = "$k = :$k";
    }
    $marcadores = implode(', ', $marcadores);
    $sent = $pdo->prepare("UPDATE videojuego 
                              SET $marcadores
                            WHERE id = :id");
    $execute = [];
    foreach (PAR as $k => $v) {
        $execute[$k] = $$k;
    }

    $execute['id'] = $id;
    $sent->execute($execute);
  
    $_SESSION['flash'] = 'El videojuego ha sido devuelto satisfactoriamente';
    volver();

} else {
    $_SESSION['flash'] = 'El videojuego no se ha podido devolver';
    volver();
}