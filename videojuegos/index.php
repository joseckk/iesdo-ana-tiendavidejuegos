<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videojuegos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <?php
    require '../comunes/auxiliar.php';

    head();
    comprobar_logueado();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
    
    $vnombre = recoger_get('vnombre');

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

    <div class="container-fluid">
        <div class="row-md-12">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                        
                    <img src="/imagenes/Rubik.jpg" width="5%" height="2%">

                    <a class="navbar-brand ml-5" href="../index.php">Inicio</a>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="/videojuegos/insertar.php">Insertar un nuevo videojuego<span class="sr-only">(current)</span></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>


        <form class="form-inline" action="" method="get">
            <div class="form-group mt-5 mr-5 mb-5">
                <label class="col-md-4 control-label ml-5 mr-1" for="vnombre">Nombre:</label>
                <input type="text" class="col-md-4 form-control ml-1 mr-3" 
                        name="vnombre" id="vnombre" value="<?= hh($vnombre) ?>">
                <button type="submit" class="btn btn-primary">buscar</button>
            </div>
        </form>

        <table class="table table-hover table-bordered text-center">
            <thead class="thead-dark">
                <th scope="col">TIPO</th>
                <th scope="col">NOMBRE</th>
                <th scope="col">PRECIO</th>
                <th scope="col">PEGI</th>
                <th scope="col">FECHA DE ALTA</th>
                <th scope="col">FECHA DE BAJA</th>
                <th scope="col">DISPONIBILIDAD</th>
                <th scope="col">TIENDA</th>
                <th scope="col">ACCIONES</th>
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
                        <td scope="row"><?= hh($video_tipo) ?></td>
                        <td scope="row"><?= hh($vnombre) ?></td>
                        <td scope="row"><?= hh($precio_fmt) ?></td>
                        <td scope="row"><?= hh($pegi) ?></td>
                        <td scope="row"><?= hh($fecha_alt_fmt) ?></td>
                        <td scope="row"><?= hh($fecha_baj_fmt) ?></td>
                        <td scope="row"><?= hh($disponibilidad_fmt) ?></td>
                        <td scope="row"><?= hh($tienda_id_fmt) ?></td>
                        <td scope="row">
                            <form action="/videojuegos/borrar.php" method="post">
                                <input type="hidden" name="id" value="<?= hh($id) ?>">
                                <input type="hidden" name="csrf_token"
                                    value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="bg-danger">borrar</button>
                            </form>
                            <form action="/videojuegos/alquilar.php" method="post">
                                <input type="hidden" name="id" value="<?= hh($id) ?>">
                                <input type="hidden" name="csrf_token"
                                    value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="bg-success">alquilar</button>
                            </form>
                            <a href="/videojuegos/modificar.php?id=<?= hh($id) ?>">modificar</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

    </div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>