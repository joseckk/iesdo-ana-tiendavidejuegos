<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiendas</title>
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

    $cod_postal = recoger_get('cod_postal');

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

    <div class="container-fluid">
        <div class="row-md-12">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                        
                    <img src="/imagenes/Rubik.jpg" width="5%" height="2%">

                    <a class="navbar-brand ml-5" href="../index.php">Inicio</a>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="/tiendas/insertar.php">Insertar una nueva tienda<span class="sr-only">(current)</span></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <div>
            <form class="form-inline" action="" method="get">
                <div class="form-group mt-5 mr-5 mb-5">
                    <label class="col-md-4 control-label ml-5 mr-1" for="cod_postal"><strong>Código postal:</strong></label>
                    <input type="text" class="col-md-4 form-control ml-1 mr-3" name="cod_postal" id="cod_postal" 
                            value="<?= hh($cod_postal) ?>">
                    <button type="submit" class="btn btn-primary">buscar</button>
                </div>
            </form>
        </div>



        <table class="table table-hover table-bordered text-center">
            <thead class="thead-dark">
                <th scope="col">CÓDIGO POSTAL</th>
                <th scope="col">LOCALIDAD</th>
                <th scope="col">NOMBRE</th>
                <th scope="col">ACCIONES</th>
            </thead>
            <tbody>
                <?php foreach($sent as $fila): 
                    $id = $fila['id'] ?>
                    <tr>
                        <td scope="row"><?= $fila['cod_postal'] ?></td>
                        <td scope="row"><?= $fila['loc'] ?></td>
                        <td scope="row"><?= $fila['tnombre'] ?></td>
                        <td scope="row">
                            <form action="/tiendas/borrar.php" method="post">    
                                <input type="hidden" name="id" value="<?= hh($id) ?>">
                                <input type="hidden" name="csrf_token"
                                    value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="bg-danger">borrar</button>
                            </form>
                            <a href="/tiendas/modificar.php?id=<?= hh($id) ?>">modificar</a>
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