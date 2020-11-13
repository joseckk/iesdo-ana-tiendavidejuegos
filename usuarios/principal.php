<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
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

    head();
    comprobar_admin();
    
    $login = recoger_get('login');
    
    ?>

    <form action="" method="get">
        <label for="login">Nombre:</label>
        <input type="text" name="login" id="login" value="<?= hh($login) ?>">
        <button type="submit">buscar</button>
    </form> <br>

    <?php

        $pdo = conectar();
        
        if ($login == '') {
            $sent = $pdo->query("SELECT * FROM usuario");
        } else {
            $sent = $pdo->prepare("SELECT *
                                     FROM usuario
                                    WHERE login = :login");
            $sent->execute([':login' => $login]);
        }
    
    ?>

    <table border="1">
        <thead>
            <th>NOMBRE</th>
            <th>ACCIONES</th>
        </thead>
        <tbody>
            <?php foreach ($sent as $fila):
                extract($fila);?>
                <tr>
                    <td><?= hh($login) ?></td>
                    <td>
                        <form action="/usuarios/borrar.php" method="post" class="borrar">
                            <input type="hidden" name="id" value="<?= hh($id) ?>">
                            <button type="submit">borrar</button>
                        </form>
                        <a href="/usuarios/modificar.php?id=<?= hh($id) ?>">modificar</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table> <br>
    <a href="/usuarios/insertar.php">Insertar un nuevo usuario</a>
</body>
</html>