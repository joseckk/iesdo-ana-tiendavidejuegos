<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar una nueva tienda</title>
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
        <button type="submit">Modificar</button>
        <?php cancelar() ?>
    </form>
</body>
</html>