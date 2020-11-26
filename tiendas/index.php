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
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }

    $pdo = conectar();

    const LISTA = [
        '0' => [
            'id' => '0',
            'nombre' => 'Código postal',
            'valor' => 'cod_postal',
        ],
        '1' => [
            'id' => '1',
            'nombre' => 'Localidad',
            'valor' => 'loc'
        ],
        '2' => [
            'id' => '2',
            'nombre' => 'Nombre',
            'valor' => 'tnombre'
        ],
    ];

    $val = recoger_get('val');

    $patron_id = recoger_get('patron_id');
    ?>

    <div class="container-fluid">
        <div class="row-md-12">
        <?php head() ?>
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

        <div class="row">
            <form class="form-inline" action="" method="get">
                <div class="form-group mt-5 mr-5 mb-5">
                    <label class="col-lg-4 control-label ml-5 mr-1" for="patron_id"><strong>Patrón de busqueda:</strong></label>
                    <div class="col-lg-4">
                        <select class="form-control" name="patron_id" id="patron_id">
                            <option value="<?= '' ?>"></option>
                            <?php foreach (LISTA as $key => $value) :?>
                                <option value="<?= $key ?>" <?= selected($patron_id, $key) ?>>
                                    <?= hh($value['nombre']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <input type="text" class="col-md-8 form-control mt-2 mr-3" name="val" id="val" 
                            value="<?= hh($val) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">buscar</button>
                </div>
            </form>
        </div>

        <?php
            if ($patron_id == '') {
                $sent = mostrar_tabla('tienda', '', $val, $pdo); 
            } else {
                $patron_id ?? $patron_id = 0 ;

                $k = LISTA[$patron_id]['valor'];            
                                              
                $sent = mostrar_tabla('tienda', $k, $val, $pdo);

                if ($sent == null || $sent->rowCount() == 0) {?>
                <div class="row ml-5">
                    <div class="alert alert-success" role="alert">
                            No se encuentran coincidencias
                    </div>
                </div><?php
                    return;
                }
            }
        ?>

        <div class="row-md-12">
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
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>