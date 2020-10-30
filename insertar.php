<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar un nuevo videojuego</title>
</head>
<body>
    
    <?php
    require './auxiliar.php';

    $video_tipo = isset($_POST['video_tipo']) ? trim($_POST['video_tipo']) : null;
    $vnombre = isset($_POST['vnombre']) ? trim($_POST['vnombre']) : null;
    $pegi = isset($_POST['pegi']) ? trim($_POST['pegi']) : null;

    $pdo = conectar();

    if (isset($video_tipo, $vnombre, $pegi, $pdo)) {
        // Validación y saneado de la entrada:
        $error_vacio = [
            'video_tipo' => [],
            'vnombre' => [],
            'pegi' => [],
        ];
        $error = $error_vacio;

        if ($_POST['video_tipo'] == '') {
            $error['video_tipo'][] = 'Error: El tipo de videojuego es obligatorio';
        } else {
            if (mb_strlen($_POST['video_tipo']) > 256) {
                $error['video_tipo'][] = 'Error: El tipo de videojuego es demasiado largo';
            }
        }

        if ($_POST['vnombre'] == '') {
            $error['vnombre'][] = 'Error: El nombre de videojuego es obligatorio';
        } else {
            if (mb_strlen($_POST['vnombre']) > 256) {
                $error['vnombre'][] = 'Error: El nombre de videojuego es demasiado largo';
            }
        }

        if ($_POST['pegi'] == '') {
            $_POST['pegi'] = 0;
            var_dump($_POST['pegi']);
        } else {
            if (!ctype_digit($_POST['pegi'])) {
                $error['pegi'][] = 'Error: El pegi del videojuego no tiene el formato correcto';
            } else {
                if ($pegi > 18) {
                    $error['pegi'][] = 'La edad recomendada no puede ser mayor de 18.';
                }
            }
        }

        // Insertar fila
        if ($error === $error_vacio) {

            $sent = $pdo->prepare('INSERT INTO videojuegos(video_tipo, vnombre, pegi)
                                        VALUES (:video_tipo, :vnombre, :pegi)');

            $sent->execute([ ':video_tipo' => $video_tipo
                            ,':vnombre' => $vnombre
                            ,':pegi' => $pegi]);
            
            header('Location: index.php');
        } else {
            if (!$error['video_tipo'] == '' || !$error['vnombre'] == '' || !$error['pegi'] == '') {

                foreach ($error as $key1 => $array) {
                    foreach ($array as $key2 => $value) {?>

                    <h3><?= $value ?></h3><?php
                    }
                }
            }
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
            <label for="pegi">Pegi:</label>
            <input type="text" name="pegi" id="pegi" 
                    value="<?= $pegi ?>">
        </p>
        <button type="submit">Insertar</button>
    </form>
</body>
</html>