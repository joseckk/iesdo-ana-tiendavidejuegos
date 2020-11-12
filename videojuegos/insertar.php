<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar un nuevo videojuego</title>
</head>
<body>
    
    <?php
    require '../comunes/auxiliar.php';

    head();

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

    foreach (PAR as $k => $v) {
        $$k = recoger_post($k);
        $tmp = $k . '_fmt';
        $$tmp = $$k;
    }

    unset($tmp);

    $pdo = conectar();
    
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
                    $fecha_alt_fmt = $fecha_alt;
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
                    $fecha_baj_fmt = $fecha_baj;
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
            } else {
                if (!comprobar_disponibilidad($vnombre, $pdo)) {
                    $error['disponibilidad'][] = 'El videojuego ya está alquilado';
                }
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

        // Insertar fila
        if ($error === $error_vacio) {
            try {
                $columnas = implode(', ', array_keys(PAR));
                $marcadores = [];
                foreach (PAR as $k => $v) {
                    $marcadores[] = ":$k";
                }
                $marcadores = implode(', ', $marcadores);
                $sent = $pdo->prepare("INSERT INTO videojuego ($columnas)
                                       VALUES ($marcadores)");
                $execute = [];
                foreach (PAR as $k => $v) {
                    $execute[$k] = $$k;
                }
                $sent->execute($execute);
                $_SESSION['flash'] = 'La fila se ha insertado correctamente.';
                volver();

            } catch (PDOException $e) {
                var_dump($e);
                error('No se ha podido insertar la fila.');
            }
        } else {
                mostrar_errores($error);
            } 
    }
    
    ?>

    <form action="" method="post">
        <p>
            <label for="video_tipo">Tipo:</label>
            <input type="text" name="video_tipo" id="video_tipo" 
                    value="<?= $video_tipo ?>">
        </p>
        <p>
            <label for="vnombre">Nombre:</label>
            <input type="text" name="vnombre" id="vnombre" 
                    value="<?= $vnombre ?>">
        </p>
        <p>
            <label for="precio">Precio:</label>
            <input type="text" name="precio" id="precio" 
                    value="<?= $precio ?>">
        </p>
        <p>
            <label for="pegi">Pegi:</label>
            <input type="text" name="pegi" id="pegi" 
                    value="<?= $pegi ?>">
        </p>
        <p>
            <label for="fecha_alt">Fecha de alta:</label>
            <input type="text" name="fecha_alt" id="fecha_alt" 
                    value="<?= $fecha_alt_fmt ?>">
        </p>
        <p>
            <label for="fecha_baj">Fecha de baja:</label>
            <input type="text" name="fecha_baj" id="fecha_baj" 
                    value="<?= $fecha_baj_fmt ?>">
        </p>
        <p>
            <label for="disponibilidad">Disponibilidad:</label>
            <select name="disponibilidad" id="disponibilidad">
                <option value="<?= '' ?>"></option>
                <option value= true >stock</option>
                <option value= false >sin fecha de entrada</option>
            </select>
        </p>
        <p>
            <label for="usuario_id">Usuario:</label>
            <select name="usuario_id" id="usuario_id">
                <option value="<?= '' ?>"></option>
                <?php foreach (lista_usuarios($pdo) as $key => $value) :?>
                    <option value="<?= $key ?>" <?= selected($usuario_id, $key) ?>>
                        <?= $value ?>
                    </option>
                <?php endforeach ?>
            </select>
        </p>
        <p>
            <label for="tienda_id">Tienda:</label>
            <select name="tienda_id" id="tienda_id">
                <option value="<?= '' ?>"></option>
                <?php foreach (lista_tiendas($pdo) as $key => $value) :?>
                    <option value="<?= $key ?>" <?= selected($tienda_id, $key) ?>>
                        <?= $value ?>
                    </option>
                <?php endforeach ?>
            </select>
        </p>
        <button type="submit">Insertar</button>
        <?php cancelar() ?>
    </form>
</body>
</html>