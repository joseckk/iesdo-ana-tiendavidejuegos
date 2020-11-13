<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videojuegos</title>
    <style>
        .borrar {
            display: inline;
        }
        .alquilar {
            display: inline;
        }
    </style>
</head>
<body>
    <a href="../index.php"><h2>INICIO</h2></a>
    <?php
    require '../comunes/auxiliar.php';

    head();
    comprobar_logueado();
    
    $vnombre = recoger_get('vnombre');
    
    ?>

    <form action="" method="get">
        <label for="vnombre">Nombre:</label>
        <input type="text" name="vnombre" id="vnombre" value="<?= hh($vnombre) ?>">
        <button type="submit">buscar</button>
    </form> <br>

    <?php

        $pdo = conectar();
        
        if ($vnombre == '') {
            $sent = $pdo->query("SELECT * FROM videojuego");
        } else {
            $sent = $pdo->prepare("SELECT *
                                     FROM videojuego
                                    WHERE vnombre = :vnombre");
            $sent->execute([':vnombre' => $vnombre]);
        }
    
    ?>

    <table border="1">
        <thead>
            <th>TIPO</th>
            <th>NOMBRE</th>
            <th>PRECIO</th>
            <th>PEGI</th>
            <th>FECHA DE ALTA</th>
            <th>FECHA DE BAJA</th>
            <th>DISPONIBILIDAD</th>
            <th>TIENDA</th>
            <th>ACCIONES</th>
        </thead>
        <tbody>
            <?php foreach ($sent as $fila):
                extract($fila);
                
                $fmt = new NumberFormatter('es-Es', NumberFormatter::CURRENCY);
                if ($precio != '') {
                    $precio_fmt = $fmt->formatCurrency($precio, 'EUR');
                } else {
                    $precio_fmt = null;
                }

                
                $fecha_alt_fmt = new DateTime($fecha_alt);
                $fecha_alt_fmt->setTimezone(new DateTimeZone('Europe/Madrid'));
                $fecha_alt_fmt = $fecha_alt_fmt->format('d-m-Y');
                

                if ($fecha_baj != '') {
                    $fecha_baj_fmt = new DateTime($fecha_baj);
                    $fecha_baj_fmt->setTimezone(new DateTimeZone('Europe/Madrid'));
                    $fecha_baj_fmt = $fecha_baj_fmt->format('d-m-Y');
                } else {
                    $fecha_baj_fmt = null;
                }

                ($disponibilidad == true) ? $disponibilidad_fmt = 'stock' : $disponibilidad_fmt = 'sin fecha de entrada';

                $tienda_id_fmt = buscar_tienda($tienda_id, $pdo);
                
                ?>
                <tr>
                    <td><?= hh($video_tipo) ?></td>
                    <td><?= hh($vnombre) ?></td>
                    <td><?= hh($precio_fmt) ?></td>
                    <td><?= hh($pegi) ?></td>
                    <td><?= hh($fecha_alt_fmt) ?></td>
                    <td><?= hh($fecha_baj_fmt) ?></td>
                    <td><?= hh($disponibilidad_fmt) ?></td>
                    <td><?= hh($tienda_id_fmt) ?></td>
                    <td>
                        <form action="/videojuegos/borrar.php" method="post" class="borrar">
                            <input type="hidden" name="id" value="<?= hh($id) ?>">
                            <button type="submit">borrar</button>
                        </form>
                        <form action="/videojuegos/alquilar.php" method="post" class="alquilar">
                            <input type="hidden" name="id" value="<?= hh($id) ?>">
                            <button type="submit">alquilar</button>
                        </form>
                        <a href="/videojuegos/modificar.php?id=<?= hh($id) ?>">modificar</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table> <br>
    <a href="/videojuegos/insertar.php">Insertar un nuevo videojuego</a>
</body>
</html>