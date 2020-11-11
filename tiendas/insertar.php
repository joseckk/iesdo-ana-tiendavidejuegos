<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar una nueva tienda</title>
</head>
<body>
    
    <?php
    require '../comunes/auxiliar.php';

    head();

    $cod_postal = recoger_post('cod_postal');
    $loc = recoger_post('loc');
    $tnombre = recoger_post('tnombre');

    $pdo = conectar();

    if (isset($cod_postal, $loc, $tnombre, $pdo)) {
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
                    if (existe_cod_postal($cod_postal, $pdo)) {
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


        // Insertar fila
        if ($error === $error_vacio) {
            try {
                    $sent = $pdo->prepare('INSERT INTO tienda(cod_postal, loc, tnombre)
                                                VALUES (:cod_postal, :loc, :tnombre)');

                    $sent->execute([ ':cod_postal' => $cod_postal
                                    ,':loc' => $loc
                                    ,':tnombre' => $tnombre]);

                    $_SESSION['flash'] = 'Se ha insertado la fila correctamente';
                    volver();
            } catch (PDOException $e) {
                error('No se ha podido insertar la fila.');
            }
        } else {
                mostrar_errores($error);
            } 
    }
    
    ?>

    <form action="" method="post">
        <p>
            <label for="cod_postal">Código Postal:</label>
            <input type="text" name="cod_postal" id="cod_postal" 
                    value="<?= $cod_postal ?>">
        </p>
        <p>
            <label for="loc">Localidad:</label>
            <input type="text" name="loc" id="loc" 
                    value="<?= $loc ?>">
        </p>
        <p>
            <label for="tnombre">Nombre:</label>
            <input type="text" name="tnombre" id="tnombre" 
                    value="<?= $tnombre ?>">
        </p>
        <button type="submit">Insertar</button>
        <?php cancelar() ?>
    </form>
</body>
</html>