<?php

    function cookies() {

        if (isset($_COOKIE['borrar'])) {
            setcookie('borrar', '', 1); ?>
            <h3>Se ha eliminado una fila correctamente</h3><?php
        } 
    }

    function banner() {
        if (!isset($_COOKIE['acepta_cookies'])) {?>
            <h3>
                Este sitio usa cookies
                <a href="cookies.php">Aceptar</a>
            </h3><?php
        } 
    }

    function error($mensaje){?>
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

    function existe_vnombre($vnombre, $pdo) 
    {
        $sent = $pdo->prepare('SELECT COUNT(*)
                                 FROM videojuegos
                                WHERE vnombre = :vnombre');
        $sent->execute(['vnombre' => $vnombre]);
       
        return $sent->fetchColumn() != 0;
    }

    function existe_mismo_vnombre($vnombre, $pdo, $id) {
        $sent = $pdo->prepare('SELECT COUNT(*)
                                 FROM videojuegos
                                WHERE vnombre = :vnombre
                                  AND id != :id');
        $sent->execute([ 'vnombre' => $vnombre
                        ,'id'      => $id]);

        return $sent->fetchColumn() != 0;
    }

    function conectar() {

        $pdo = new PDO('pgsql:host=localhost;dbname=bd', 'joseka', 'joseka');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    function volver() {
        header('Location: index.php');
    }

    function mostrar_errores($error) {
                   
        foreach ($error as $key => $array) {
            foreach ($array as $value) {?>

            <h3><?= $value ?></h3><?php
            }
        }
    }

    function cancelar(){?>
        <a href="index.php">Volver</a><?php
    }