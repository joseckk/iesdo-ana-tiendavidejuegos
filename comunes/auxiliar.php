<?php

    function banner() 
    {
        if (!isset($_COOKIE['acepta_cookies'])) {?>
            <h3>
                Este sitio usa cookies
                <a href="../cookies.php">Aceptar</a>
            </h3><?php
        } 
    }

    function error($mensaje)
    {?>
        <h3><?= $mensaje ?></h3><?php
        return true;
    }

    function recoger($tipo, $nombre)
    {
        return filter_input($tipo, $nombre, FILTER_CALLBACK, [
            'options' => 'trim'
        ]);
    }
    
    function recoger_get($nombre)
    {
        return recoger(INPUT_GET, $nombre);
    }
    
    function recoger_post($nombre)
    {
        return recoger(INPUT_POST, $nombre);
    }

    function existe_cod_postal($cod_postal, $pdo) 
    {
        $sent = $pdo->prepare('SELECT COUNT(*)
                                 FROM tienda
                                WHERE cod_postal = :cod_postal');
        $sent->execute(['cod_postal' => $cod_postal]);
       
        return $sent->fetchColumn() != 0;
    }

    function existe_cod_postal_otra_fila($cod_postal, $pdo, $id) 
    {
        $sent = $pdo->prepare('SELECT COUNT(*)
                                 FROM tienda
                                WHERE cod_postal = :cod_postal
                                  AND id != :id');
        $sent->execute([ 'cod_postal' => $cod_postal
                        ,'id'      => $id]);

        return $sent->fetchColumn() != 0;
    }

    function buscar_tienda($tienda_id, $pdo)
    {
        $sent = $pdo->prepare('SELECT tnombre
                                 FROM tienda
                                WHERE id = :tienda_id');
        $sent->execute(['tienda_id' => $tienda_id]);

        foreach ($sent as $fila) {
            extract($fila);
        }

        return $tnombre;
    }

    function existe_usuario($usuario_id, $pdo)
    {
        $sent = $pdo->prepare('SELECT *
                                 FROM usuario
                                WHERE id = :usuario_id');
        $sent->execute(['usuario_id' => $usuario_id]);

        return $sent->fetchColumn() != 0;
    }

    function existe_tienda($tienda_id, $pdo)
    {
        $sent = $pdo->prepare('SELECT *
                                 FROM tienda
                                WHERE id = :tienda_id');
        $sent->execute(['tienda_id' => $tienda_id]);

        return $sent->fetchColumn() != 0;
    }

    function encontrar_tienda($tnombre, $pdo)
    {
        $sent = $pdo->prepare('SELECT id AS t_id 
                                 FROM tienda 
                                WHERE tnombre = :tnombre');
        $sent->execute(['tnombre' => $tnombre]);

        $fila = $sent->fetch();

        if ($fila != null) {
            $tienda_id = $fila['t_id'];

            return $tienda_id;
        }
        return $tienda_id = null;
    }

    function lista_usuarios($pdo)
    {
        $sent = $pdo->query('SELECT id, login
                               FROM usuario
                           ORDER BY id');
        $ret = [];
        
        foreach ($sent as $fila) {
            if ($fila['login'] != 'admin')
                $ret[$fila['id']] = "{$fila['login']}";
        }

        return $ret;
    }

    function lista_tiendas($pdo)
    {
        $sent = $pdo->query('SELECT id, cod_postal, tnombre
                               FROM tienda
                           ORDER BY cod_postal');
        $ret = [];
        
        foreach ($sent as $fila) {
            $ret[$fila['id']] = "({$fila['cod_postal']}) {$fila['tnombre']}";
        }

        return $ret;
    }

    function comprobar_lista_usuario($pdo, $usuario_id, $query)
    {        
        $sent = $pdo->prepare($query);
        $sent->execute(['usuario_id' => $usuario_id]);
       
        return $sent->fetchColumn() != 0;
    }

    function comprobar_estado($id) 
    {
        $pdo = conectar();

        $sent = $pdo->prepare('SELECT *
                                 FROM videojuego
                                WHERE usuario_id = :id');
        $sent->execute(['id' => $id]);

        return $sent->fetchColumn() != 0;
    }

    function comprobar_usuario($login, $pdo)
    {
        $sent = $pdo->prepare('SELECT *
                                 FROM usuario
                                WHERE login = :login');
        $sent->execute(['login' => $login]);

        return $sent->fetchColumn() != 0;
    }

    function comprobar_usuario_otra_fila($login, $pdo, $id)
    {
        $sent = $pdo->prepare('SELECT *
                                 FROM usuario
                                WHERE login = :login
                                  AND id != :id');
        $sent->execute(['login' => $login,
                        'id' => $id]);

        return $sent->fetchColumn() != 0;
    }

    function mostrar_tabla($nombre, $patron, $parametro, $pdo)
    {   
        $parametro_valido = false;

        if ($patron == '' || $parametro == '') {
            $parametro_valido = true;
            $sent = $pdo->query("SELECT * FROM $nombre");
        } else {
            if ($patron == 'loc' || $patron == 'tnombre' 
                || $patron == 'video_tipo' || $patron == 'vnombre') {

                $parametro_valido = true;
            }

            if ($patron == 'precio' || $patron == 'pegi' 
                    || $patron == 'cod_postal') {

                if (is_numeric($parametro)) {
                    $parametro_valido = true;
                }
            }

            if ($patron == 'fecha_alt' || $patron == 'fecha_baj') {
                $matches = [];
                
                if (!preg_match(
                    '/^(\d\d)-(\d\d)-(\d{4})$/',
                    $parametro, $matches
                )) {
                    $parametro_valido = false;
                } else {
                    $dia = $matches[1];
                    $mes = $matches[2];
                    $anyo = $matches[3];
                    if (!checkdate($mes, $dia, $anyo)) {
                        $parametro_valido = false;
                    } else {
                        $parametro = "$anyo-$mes-$dia";
                        $parametro_valido = true;
                    }
                }
            }

            if ($patron == 'disponibilidad') {
                if ($parametro == 'stock') {
                    $parametro = 1;
                    $parametro_valido = true;
                } else {
                    if ($parametro == 'sin fecha de entrada') {
                        $parametro = 0;
                        $parametro_valido = true;
                    }
                }
            }

            if (encontrar_tienda($parametro, $pdo) != null) {
                $parametro = intval(encontrar_tienda($parametro, $pdo));
                $parametro_valido = true;
            }
        }

        if ($parametro_valido == true) {
            if (is_numeric($parametro)){
                $patron_fmt = ':' . $patron;
                $sent = $pdo->prepare("SELECT *
                                         FROM $nombre
                                        WHERE $patron = $patron_fmt");
                $sent->execute([$patron => $parametro]);
            } else {
                if ($parametro != '') {
                    if ($patron == 'fecha_alt' || $patron == 'fecha_baj') {
                        $sent = $pdo->query("SELECT *
                                               FROM $nombre
                                              WHERE $patron = '$parametro'
                                           ORDER BY $patron");
                    } else {
                        $sent = $pdo->query("SELECT *
                                               FROM $nombre
                                              WHERE $patron LIKE '%$parametro%'
                                           ORDER BY $patron");
                    }
                }
            }
            return $sent;
        }
    }

    function selected($a, $b)
    {
        return ($a == $b) ? 'selected' : '';
    }

    function logueado()
    {
        return $_SESSION['login'] ?? false;
    }

    function encabezado()
    {
        if ($logueado = logueado()): ?>
            <form class="row justify-content-end mt-2 mr-5" action="/comunes/logout.php" method="post">
                <a class="col-sm-1" href="/usuarios/index.php"><h3><strong>Mi lista</strong></h3></a>
                <?= $logueado['nombre'] ?>
                <button type="submit" class="btn btn-outline-danger ml-2">Logout</button>
            </form><?php
        else: ?>
            <form class="row justify-content-end mt-2 mr-5" action="/comunes/login.php" >
                <button type="submit" class="btn btn-outline-success">Login</button>
            </form><?php
        endif;
    }


    function conectar() 
    {

        $pdo = new PDO('pgsql:host=localhost;dbname=bd', 'joseka', 'joseka');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    function volver() 
    {
        header('Location: ../index.php');
    }

    function mostrar_errores($error) 
    {
                   
        foreach ($error as $key => $array) {
            foreach ($array as $value) {?>
                <div class="row ml-5">
                    <div class="alert alert-danger" role="alert">
                            <?= $value ?>
                    </div>
                </div><?php
            }
        }
    }

    function cancelar()
    {?>
        <a href="../index.php">Volver</a><?php
    }

    function flash()
    {
        if (isset($_SESSION['flash'])) {
            echo "<h3>{$_SESSION['flash']}</h3>";
            unset($_SESSION['flash']);
        }
    }

    function head()
    {   
        encabezado();
        banner();
        flash();
    }

    function comprobar_logueado()
    {
        if (!logueado()) {
            $_SESSION['flash'] = 'Debe estar logueado.';
            header('Location: /comunes/login.php');
        }
    }

    function comprobar_admin()
    {
        comprobar_logueado();

        if (logueado()['nombre'] != 'admin') {
            $_SESSION['flash'] = 'Debe ser administrador.';
            volver();
        }
    }

    function hh($s)
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE);
    }