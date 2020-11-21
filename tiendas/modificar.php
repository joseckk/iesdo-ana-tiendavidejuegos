<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar una nueva tienda</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    
    <?php
    require '../comunes/auxiliar.php';

    comprobar_admin();
    head();

    $cod_postal = recoger_post('cod_postal');
    $loc = recoger_post('loc');
    $tnombre = recoger_post('tnombre');
    $id = recoger_get('id');

    $pdo = conectar();

    if (isset($cod_postal, $loc, $tnombre, $id, $pdo)) {
        // Validación y saneado de la entrada:
        $error_vacio = [
            'cod_postal' => [],
            'loc' => [],
            'tnombre' => [],
        ];
        $error = $error_vacio;

        if ($cod_postal == '') {
            $error['cod_postal'][] = 'El código postal es obligatorio.';
        } else {
            if (!ctype_digit($cod_postal)) {
                $error['cod_postal'][] = 'El código postal debe ser un dígito.';
            } else {
                if ($cod_postal > 99999) {
                    $error['cod_postal'][] = 'El código postal es demasiado grande.';
                } else {
                    if (existe_cod_postal_otra_fila($cod_postal, $pdo, $id)) {
                        $error['cod_postal'][] = 'Esa tienda ya existe.';
                    }
                }
            }
        }

        if ($loc == '') {
            $error['loc'][] = 'La localidad es obligatoria.';
        } else {
            if (mb_strlen($loc) > 255) {
                $error['loc'][] = 'La localidad es demasiado larga.';
            }
        }

        if ($tnombre == '') {
            $tnombre = null;
        } else {
            if (mb_strlen($tnombre) > 255) {
                $error['tnombre'][] = 'El nombre es demasiado largo.';
            }
        }


        // Modificar fila
        if ($error === $error_vacio) {
            try {
                $sent = $pdo->prepare('UPDATE tienda
                                          SET cod_postal = :cod_postal
                                            , loc = :loc
                                            , tnombre = :tnombre
                                        WHERE id = :id');
                                        
                $sent->execute(['cod_postal'=> $cod_postal
                              , 'loc'=> $loc
                              , 'tnombre'=> $tnombre
                              , 'id'=> $id]);
                              
                $_SESSION['flash'] = 'Se ha modificado la fila correctamente';
                volver();
            } catch (PDOException $e) {
                error('No se ha podido modificar la fila.');
            }
        } else {
                mostrar_errores($error);
            } 
    } else {
        if (isset($id)) {
            $sent = $pdo->prepare('SELECT *
                                     FROM tienda
                                    WHERE id = :id');
            $sent->execute(['id'=> $id]);
            
            foreach ($sent as $fila) {
                $cod_postal = $fila['cod_postal'];
                $loc = $fila['loc'];
                $tnombre = $fila['tnombre'];
            }
        }
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
    </div>

    <div class="container">
        <form action="" method="post">
            <div class="form-group mt-5 mr-5">
                <label class="col-lg-4 control-label" for="cod_postal">Código postal:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="cod_postal" id="cod_postal" 
                            value="<?= hh($cod_postal) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="loc">Localidad:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="loc" id="loc"
                            value="<?= hh($loc) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="tnombre">Nombre:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="tnombre" id="tnombre"
                            value="<?= hh($tnombre) ?>">
                </div>
            </div>
            <div class="col-lg-4">
                <button type="submit" class="btn btn-primary mt-5">Modificar</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>