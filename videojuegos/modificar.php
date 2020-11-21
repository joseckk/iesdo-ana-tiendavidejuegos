<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar un videojuego</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    
    <?php
    require '../comunes/auxiliar.php';

    head();
    comprobar_admin();

    const PAR = [
        'video_tipo'=> 'Tipo',
        'vnombre' => 'Nombre',
        'precio' => 'Precio',
        'pegi' => 'Pegi',
        'fecha_alt' => 'Fecha de alta',
        'fecha_baj' => 'Fecha de baja',
        'disponibilidad' => 'Disponibilidad',
        'usuario_id' => 'Usuario',
        'tienda_id' => 'Tienda', 
    ];

    $id = recoger_get('id');

    if (!isset($id)) {
        volver();
    } else {
        $pdo = conectar();
        $sent = $pdo->prepare("SELECT id
                                    , video_tipo
                                    , vnombre
                                    , precio
                                    , pegi
                                    , to_char(fecha_alt, 'DD-MM-YYYY') AS fecha_alt
                                    , to_char(fecha_baj, 'DD-MM-YYYY') AS fecha_baj
                                    , disponibilidad
                                    , usuario_id
                                    , tienda_id
                                FROM videojuego
                                WHERE id = :id");
        $sent->execute(['id' => $id]);
        $fila = $sent->fetch();
        if ($fila === false) {
            volver();
        }
    }

    foreach (PAR as $k => $v) {
        $$k = recoger_post($k);
        $tmp = $k . '_fmt';
        $$tmp = $$k;
    }

    unset($tmp);
    
    $existen = true;

    foreach (PAR as $k => $v) {
        if (!isset($$k)) {
            $existen = false;
            break;
        }
    }


    if ($existen) {
        // Validación y saneado de la entrada:
        $error_vacio = [];
        foreach (PAR as $k => $v) {
            $error_vacio[$k] = [];
        }
        $error = $error_vacio;

        if ($video_tipo == '') {
            $error['video_tipo'][] = 'El tipo es obligatorio.';
        } else {
            if (mb_strlen($video_tipo) > 255) {
                $error['video_tipo'][] = 'El tipo es demasiado largo.';
            }
        }

        if ($vnombre == '') {
            $error['vnombre'][] = 'El nombre es obligatorio.';
        } else {
            if (mb_strlen($vnombre) > 255) {
                $error['vnombre'][] = 'El nombre es demasiado largo.';
            }
        }

        if ($precio == '') {
            $error['precio'][] = 'El precio es obligatorio.';
        } else {
            if (!is_numeric($precio)) {
                $error['precio'][] = 'El precio debe de ser numérico.';
            } else {
                if ($precio > 999.99) {
                    $error['precio'][] = 'El precio no tiene el formato correcto.';
                }
            }
        }

        if ($pegi == '') {
            $pegi = null;
        } else {
            if (!ctype_digit($pegi)) {
                $error['pegi'][] = 'El pegi debe ser un digito.';
            } else {
                if ($pegi > 99) {
                    $error['pegi'][] = 'El pegi es demasiado grande.';
                }
            }
        }

        if ($fecha_alt == '') {
            $error['fecha_alt'][] = 'La fecha de alta es obligatoria.';
        } else {
            $matches = [];
            if (!preg_match(
                '/^(\d\d)-(\d\d)-(\d{4})$/',
                $fecha_alt, $matches
            )) {
                $error['fecha_alt'][] = 'El formato de la fecha no es válido';
            } else {
                $dia = $matches[1];
                $mes = $matches[2];
                $anyo = $matches[3];
                if (!checkdate($mes, $dia, $anyo)) {
                    $error['fecha_alt'][] = 'La fecha es incorrecta';
                } else {
                    $fecha_alt = "$anyo-$mes-$dia";
                }
            }
        }

        if ($fecha_baj == '') {
            $fecha_baj = null;
        } else {
            $matches = [];
            if (!preg_match(
                '/^(\d\d)-(\d\d)-(\d{4})$/',
                $fecha_baj, $matches
            )) {
                $error['fecha_baj'][] = 'El formato de la fecha no es válido';
            } else {
                $dia = $matches[1];
                $mes = $matches[2];
                $anyo = $matches[3];
                if (!checkdate($mes, $dia, $anyo)) {
                    $error['fecha_baj'][] = 'La fecha es incorrecta';
                } else {
                    $fecha_baj = "$anyo-$mes-$dia";
                }
            }
        }

        if ($disponibilidad == '') {
            $error['disponibilidad'][] = 'La disponibilidad es obligatoria';
        } else {
            if ($disponibilidad == 'true') {
                $disponibilidad = true;
            } elseif ($disponibilidad == 'false') {
                $disponibilidad = false;
            } 
        }

        if ($usuario_id == '') {
            $usuario_id = null;
        } else {
            if (!existe_usuario($usuario_id, $pdo)) {
                $error['usuario_id'][] = 'Ese usuario no existe';
            }
        }

        if ($tienda_id == '') {
            $error['tienda_id'][] = 'La tienda es obligatoria';
        } else {
            if (!existe_tienda($tienda_id, $pdo)) {
                $error['tienda_id'][] = 'Esa tienda no existe';
            }
        }

        // Modificar fila
        if ($error === $error_vacio) {
            try {
                $marcadores = [];
                foreach (PAR as $k => $v) {
                    $marcadores[] = "$k = :$k";
                }
                $marcadores = implode(', ', $marcadores);
                $sent = $pdo->prepare("UPDATE videojuego 
                                          SET $marcadores
                                        WHERE id = :id");
                $execute = [];
                foreach (PAR as $k => $v) {
                    $execute[$k] = $$k;
                }
                $execute['id'] = $id;
                $sent->execute($execute);
                $_SESSION['flash'] = 'La fila se ha modificado correctamente.';
                volver();

            } catch (PDOException $e) {
                error('No se ha podido modificar la fila.');
            }
        } else {
                mostrar_errores($error);
            } 
    } else {
        if (isset($id)) {

                $video_tipo_fmt = $fila['video_tipo'];
                $vnombre_fmt = $fila['vnombre'];
                $precio_fmt = $fila['precio'];
                $pegi_fmt = $fila['pegi'];
                $fecha_alt_fmt = $fila['fecha_alt'];
                $fecha_baj_fmt = $fila['fecha_baj'];
                $disponibilidad = $fila['disponibilidad'];
                $usuario_id = $fila['usuario_id'];
                $tienda_id = $fila['tienda_id'];
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
                <label class="col-lg-4 control-label" for="video_tipo">Tipo:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="video_tipo" id="video_tipo" 
                            value="<?= hh($video_tipo_fmt) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="vnombre">Nombre:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="vnombre" id="vnombre"
                            value="<?= hh($vnombre_fmt) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="precio">Precio:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="precio" id="precio"
                            value="<?= hh($precio_fmt) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="pegi">Pegi:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="pegi" id="pegi"
                            value="<?= hh($pegi_fmt) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="fecha_alt">Fecha de alta:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="fecha_alt" id="fecha_alt"
                            value="<?= hh($fecha_alt_fmt) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="fecha_baj">Fecha de baja:</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="fecha_baj" id="fecha_baj"
                            value="<?= hh($fecha_baj_fmt) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="disponibilidad">Disponibilidad:</label>
                <div class="col-lg-4">
                    <select class="form-control" name="disponibilidad" id="disponibilidad">
                        <option value="<?= '' ?>"></option>
                        <option value= true <?= selected($disponibilidad, '1') ?>>stock</option>
                        <option value= false <?= selected($disponibilidad, '0') ?>>sin fecha de entrada</option>
                    </select>
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="usuario_id">Usuario:</label>
                <div class="col-lg-4">
                    <select class="form-control" name="usuario_id" id="usuario_id">
                        <option value="<?= '' ?>"></option>
                        <?php foreach (lista_usuarios($pdo) as $key => $value) :?>
                            <option value="<?= $key ?>" <?= selected($usuario_id, $key) ?>>
                                <?= hh($value) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="tienda_id">Tienda:</label>
                <div class="col-lg-4">
                    <select class="form-control" name="tienda_id" id="tienda_id">
                        <option value="<?= '' ?>"></option>
                        <?php foreach (lista_tiendas($pdo) as $key => $value) :?>
                            <option value="<?= $key ?>" <?= selected($tienda_id, $key) ?>>
                                <?= hh($value) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
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