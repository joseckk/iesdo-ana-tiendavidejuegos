<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
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
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }

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

        $query = "SELECT v.id, to_char(v.fecha_baj, 'DD-MM-YYYY') AS v_fecha_baj
                       , v.vnombre AS v_vnombre
                       , t.tnombre AS t_tnombre
                    FROM videojuego v
                    JOIN tienda t
                      ON v.tienda_id = t.id
                     AND v.usuario_id = :usuario_id";

        if (comprobar_lista_usuario($pdo, $usuario_id, $query)) {

            if ($fecha_baj == '') {

                $sent = $pdo->prepare($query);
                $sent->execute(['usuario_id' => $usuario_id]);
                
            } else {
    
                $sent = $pdo->prepare("$query WHERE fecha_baj = :fecha_baj");
                $sent->execute(['usuario_id' => $usuario_id
                              , 'fecha_baj' => $fecha_baj]);
    
            }
        } else {
            $_SESSION['flash'] = 'No posees videojuegos alquilados';
            volver();
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
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
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