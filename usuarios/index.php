<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de pedidos del usuario</title>
</head>
<body>
<a href="../index.php"><h2>INICIO</h2></a>
    <?php
    require '../comunes/auxiliar.php';

    comprobar_logueado();
    head();
    
    $fecha_baj = recoger_get('fecha_baj');
    
    ?>

    <form action="" method="get">
        <label for="fecha_baj">Fecha de alquiler:</label>
        <input type="text" name="fecha_baj" id="fecha_baj" value="<?= hh($fecha_baj) ?>">
        <button type="submit">buscar</button>
    </form> <br>

    <?php 

        $pdo = conectar();
        $logueado = logueado();
        $usuario_id = $logueado['id'];
        $query = consulta_usuario();

        if ($fecha_baj == '') {

            $sent = $pdo->prepare($query);
            $sent->execute(['usuario_id' => $usuario_id]);

        } else {

            $sent = $pdo->prepare("$query WHERE fecha_baj = :fecha_baj");
            $sent->execute(['usuario_id' => $usuario_id
                          , 'fecha_baj' => $fecha_baj]);

        }
    
    ?>

    <table border="1">
        <thead>
            <th>FECHA DE ALQUILER</th>
            <th>NOMBRE</th>
            <th>TIENDA</th>
            <th>ACCIONES</th>
        </thead>
        <tbody>
            <?php foreach($sent as $fila): 
                $id = $fila['id'] ?>
                <tr>
                    <td><?= $fila['v_fecha_baj'] ?></td>
                    <td><?= $fila['v_vnombre'] ?></td>
                    <td><?= $fila['t_tnombre'] ?></td>
                    <td>
                        <form action="/usuarios/devolver.php" method="post" class="devolver">
                            <input type="hidden" name="id" value="<?= hh($id) ?>">
                            <button type="submit">devolver producto</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>
</html>