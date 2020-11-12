<?php

    function banner() 
    {
        if (!isset($_COOKIE['acepta_cookies'])) {?>
            <h3>
                Este sitio usa cookies
                <a href="cookies.php">Aceptar</a>
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

    function comprobar_disponibilidad($vnombre, $pdo) 
    {
        $sent = $pdo->prepare('SELECT *
                                 FROM videojuego
                                WHERE vnombre = :vnombre
                                  AND usuario_id == null');
        $sent->execute(['vnombre' => $vnombre]);

        return $sent->fetchColumn() != 0;
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

    function selected($a, $b)
    {
        return ($a == $b) ? 'selected' : '';
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

            <h3><?= $value ?></h3><?php
            }
        }
    }

    function cancelar()
    {?>
        <a href="index.php">Volver</a><?php
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
        banner();
        flash();
    }