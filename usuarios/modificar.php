<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar un usuario</title>
</head>
<body>
    
    <?php
    require '../comunes/auxiliar.php';

    comprobar_admin();
    head();

    $login = recoger_post('login');
    $password = recoger_post('password');
    $crypt_password = recoger_post('crypt_password');
    $id = recoger_get('id');

    $pdo = conectar();

    if (isset($login, $password, $crypt_password, $id, $pdo)) {
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


        // Modificar fila
        if ($error === $error_vacio) {
            try {
                $sent = $pdo->prepare("UPDATE usuario
                                          SET login = :login
                                            , password = crypt(:password, gen_salt('bf', 10))
                                        WHERE id = :id");
                                        
                $sent->execute(['login'=> $login
                              , 'password'=> $password
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
                                     FROM usuario
                                    WHERE id = :id');
            $sent->execute(['id'=> $id]);
            
            foreach ($sent as $fila) {
                $login = $fila['login'];
                $password = $fila['password'];
                $crypt_password = $fila['password'];
            }
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
            <input type="text" name="password" id="password" 
                    value="<?= hh($password) ?>">
        </p>
        <p>
            <label for="crypt_password">Repite la contraseña:</label>
            <input type="text" name="crypt_password" id="crypt_password" 
                    value="<?= hh($crypt_password) ?>">
        </p>
        <button type="submit">Modificar</button>
        <?php cancelar() ?>
    </form>
</body>
</html>