<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiendas</title>
    <style>
        .borrar {
            display: inline;
        }
    </style>
</head>
<body>
    <a href="../index.php"><h2>INICIO</h2></a>
    <?php
    require '../comunes/auxiliar.php';

    comprobar_logueado();
    head();
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }

    $cod_postal = recoger_get('cod_postal');
    
    ?>

    <form action="" method="get">
        <label for="cod_postal">Código Postal:</label>
        <input type="text" name="cod_postal" id="cod_postal" value="<?= hh($cod_postal) ?>">
        <button type="submit">buscar</button>
    </form> <br>

    <?php 

        $pdo = conectar();

        if ($cod_postal == '') {

            $sent = $pdo->query("SELECT * FROM tienda");

        } else {

            $sent = $pdo->prepare("SELECT *
                                     FROM tienda
                                    WHERE cod_postal = :cod_postal");
            $sent->execute([':cod_postal' => $cod_postal]);

        }
    
    ?>

    <table border="1">
        <thead>
            <th>CÓDIGO POSTAL</th>
            <th>LOCALIDAD</th>
            <th>NOMBRE</th>
            <th>ACCIONES</th>
        </thead>
        <tbody>
            <?php foreach($sent as $fila): 
                $id = $fila['id'] ?>
                <tr>
                    <td><?= $fila['cod_postal'] ?></td>
                    <td><?= $fila['loc'] ?></td>
                    <td><?= $fila['tnombre'] ?></td>
                    <td>
                        <form action="/tiendas/borrar.php" method="post" class="borrar">    
                            <input type="hidden" name="id" value="<?= hh($id) ?>">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit">borrar</button>
                        </form>
                        <a href="/tiendas/modificar.php?id=<?= hh($id) ?>">modificar</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table> <br>
    <a href="/tiendas/insertar.php">Insertar una nueva tienda</a>
</body>
</html>