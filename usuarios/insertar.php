<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar una nuevo usuario</title>
</head>
<body>
    
    <?php
    require '../comunes/auxiliar.php';

    comprobar_admin();
    head();

    $login = recoger_post('login');
    $password = recoger_post('password');
    $crypt_password = recoger_post('crypt_password');

    $pdo = conectar();

    if (isset($login, $password, $crypt_password, $pdo)) {
        // Validación y saneado de la entrada:
        $error_vacio = [
            'login' => [],
            'password' => [],
            'crypt_password' => [],
        ];
        $error = $error_vacio;

        if ($login == '') {
            $error['login'][] = 'El nombre es obligatoria.';
        } else {
            if (mb_strlen($login) > 255) {
                $error['login'][] = 'El nombre es demasiado larga.';
            } else {
                if (comprobar_usuario($llogin, $pdo)) {
                    $error['login'][] = 'El usuario ya existe.';
                }
            }
        }


        if ($password == '') {
            $error['password'][] = 'La contraseña es obligatoria.';
        } else {
            if (mb_strlen($password) > 255) {
                $error['password'][] = 'La contraseña es demasiado larga.';
            } 
        }

        if ($crypt_password == '') {
            $error['crypt_password'][] = 'La debes repetir la contraseña.';
        } else {
            if (mb_strlen($crypt_password) > 255) {
                $error['crypt_password'][] = 'La contraseña es demasiado larga.';
            } else {
                if ($password != $crypt_password) {
                    $error['crypt_password'][] = 'La contraseñas no coinciden.';
                }
            }
        }


        // Insertar fila
        if ($error === $error_vacio) {
            try {
                    $sent = $pdo->prepare("INSERT INTO usuario(login, password)
                                                VALUES (:login, crypt(:password, gen_salt('bf', 10)))");

                    $sent->execute([ ':login' => $login
                                    ,':password' => $password]);

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
            <label for="login">Nombre:</label>
            <input type="text" name="login" id="login" 
                    value="<?= hh($login) ?>">
        </p>
        <p>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" 
                    value="<?= hh($password) ?>">
        </p>
        <p>
            <label for="crypt_password">Repite la contraseña:</label>
            <input type="password" name="crypt_password" id="crypt_password" 
                    value="<?= hh($crypt_password) ?>">
        </p>
        <button type="submit">Insertar</button>
        <?php cancelar() ?>
    </form>
</body>
</html>