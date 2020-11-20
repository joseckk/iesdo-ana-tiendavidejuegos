<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <?php
    require './auxiliar.php';

    head();

    $login = recoger_post('login');
    $password = recoger_post('password');

    $error = [
        'login' => [],
        'password' => [],
    ];
    $error_vacio = $error;

    if (isset($login, $password)) {
        $pdo = conectar();
        $sent = $pdo->prepare('SELECT *
                                 FROM usuario
                                WHERE login = :login');
        $sent->execute(['login' => $login]);
        $fila = $sent->fetch();
        if ($fila === false) {
            $error['login'][] = 'Las credenciales son incorrectas';
        } else {
            if (!password_verify($password, $fila['password'])) {
                $error['login'][] = 'Las credenciales son incorrectas';
            }
        }
        if ($error === $error_vacio) {
            // Loguear
            $_SESSION['login'] = [];
            $_SESSION['login']['id'] = $fila['id'];
            $_SESSION['login']['nombre'] = $fila['login'];
            volver();
        } else {
            mostrar_errores($error);
        }
    }
    ?>
<<<<<<< HEAD

    <div class="container">
        <form action="" method="post">
            <div class="form-group mt-5 mr-5">
                <label class="col-lg-4 control-label" for="login" >Nombre de usuario: </label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="login" id="login" 
                            value="<?= hh($login) ?>">
                </div>
            </div>
            <div class="form-group mt-5">
                <label class="col-lg-4 control-label" for="password">Contraseña: </label>
                <div class="col-lg-4">
                    <input type="password" class="form-control" name="password" id="password" 
                            placeholder="introduzca su contraseña">
                </div>
            </div>
            <div class="col-lg-4">
                <button type="submit" class="btn btn-primary mt-5">login</button>
            </div>
        </form>
    </div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
=======
    
    <div class="container text-center">
        <form action="" method="post">
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="login">Nombre: </label>
                        <input type="text" class="form-control" id="login" placeholder="Introduce tu nombre de usuario"
                                value="<?= hh($login) ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" placeholder="Contraseña">
                    </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
>>>>>>> 3b7978765dfaa50195910694235f17d9e8e0f323
</body>
</html>