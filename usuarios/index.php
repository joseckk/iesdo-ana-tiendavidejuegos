<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de pedidos del usuario</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <?php
    require '../comunes/auxiliar.php';

    comprobar_logueado();
    head();
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }

    $fecha_baj = recoger_get('fecha_baj');
    
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

    <div class="container-fluid">
        <div class="row-md-12">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                            
                    <img src="/imagenes/Rubik.jpg" width="5%" height="2%">

                    <a class="navbar-brand ml-5" href="../index.php">Inicio</a>

                </nav>
            </div>
        </div>

        <form class="form-inline" action="" method="get">
            <div class="form-group mt-5 mr-5 mb-5">
                <label class="col-md-4 control-label ml-5 mr-1" for="fecha_baj">Fecha de alquiler:</label>
                <input type="text" class="col-md-4 form-control ml-1 mr-3" name="fecha_baj" id="fecha_baj" 
                        value="<?= hh($fecha_baj) ?>">
                <button type="submit" class="btn btn-primary">buscar</button>
            </div>
        </form>


        <table class="table table-hover table-bordered text-center">
            <thead class="thead-dark">
                <th scope="col">FECHA DE ALQUILER</th>
                <th scope="col">NOMBRE</th>
                <th scope="col">TIENDA</th>
                <th scope="col">ACCIONES</th>
            </thead>
            <tbody>
                <?php foreach($sent as $fila): 
                    $id = $fila['id'] ?>
                    <tr>
                        <td scope="row"><?= $fila['v_fecha_baj'] ?></td>
                        <td scope="row"><?= $fila['v_vnombre'] ?></td>
                        <td scope="row"><?= $fila['t_tnombre'] ?></td>
                        <td scope="row">
                            <form action="/usuarios/devolver.php" method="post">
                                <input type="hidden" name="csrf_token"
                                    value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="id" value="<?= hh($id) ?>">
                                <button type="submit" class="bg-primary">devolver producto</button>
                            </form>
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