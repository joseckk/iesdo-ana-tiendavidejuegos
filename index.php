<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videojuegos</title>
</head>
<body>
    
    <?php
    require './auxiliar.php';
    banner();
    cookie();

    $video_tipo = isset($_GET['video_tipo']) ? trim($_GET['video_tipo']) : null;
    

    ?>

    <form action="" method="get">
        <label for="video_tipo">Tipo de videojuego:</label>
        <input type="text" name="video_tipo" id="video_tipo" value="<?= $video_tipo ?>">
        <button type="submit">buscar</button>
    </form> <br>

    <?php 

        $pdo = new PDO('pgsql:host=localhost;dbname=bd', 'jose', 'jose');

        if ($video_tipo == '') {

            $sent = $pdo->query("SELECT * FROM videojuegos");

        } else {

            $sent = $pdo->prepare("SELECT *
                                     FROM videojuegos
                                    WHERE video_tipo = :video_tipo");
            $sent->execute([':video_tipo' => $video_tipo]);

        }
    
    ?>

    <table border="1">
        <thead>
            <th>TIPO</th>
            <th>NOMBRE</th>
            <th>PEGI</th>
            <th>ACCIONES</th>
        </thead>
        <tbody>
            <?php foreach($sent as $fila): ?>
                <tr>
                    <td><?= $fila['video_tipo'] ?></td>
                    <td><?= $fila['vnombre'] ?></td>
                    <td><?= $fila['pegi'] ?></td>
                    <td>
                        <form action="borrar.php" method="post">
                            <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                            <button type="submit">borrar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table> <br>
    <a href="insertar.php">Insertar un nuevo videojuego</a>
</body>
</html>