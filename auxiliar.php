<?php

    function cookie() {

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

    function conectar() {

        $pdo = new PDO('pgsql:host=localhost;dbname=bd', 'jose', 'jose');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }