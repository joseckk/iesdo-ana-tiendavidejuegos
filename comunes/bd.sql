--------------------------
-- Base de datos bd --
--------------------------

DROP TABLE IF EXISTS usuario CASCADE;


CREATE TABLE usuario
(
        id          bigserial        PRIMARY KEY
    ,   login       varchar(255)     NOT NULL UNIQUE
    ,   password    varchar(255)     NOT NULL      
);

DROP TABLE IF EXISTS tienda CASCADE;


CREATE TABLE tienda
(
        id          bigserial        PRIMARY KEY
    ,   cod_postal  numeric(5)       NOT NULL UNIQUE
    ,   loc         varchar(255)     NOT NULL 
    ,   tnombre     varchar(255)     
);

DROP TABLE IF EXISTS videojuego CASCADE;


CREATE TABLE videojuego
(
        id          bigserial        PRIMARY KEY
    ,   video_tipo  varchar(255)     NOT NULL
    ,   vnombre     varchar(255)     NOT NULL UNIQUE
    ,   pegi        numeric(2)       
    ,   tienda_id   bigint           NOT NULL REFERENCES tienda(id)       
);

INSERT INTO usuario (login, password)
VALUES ('admin', crypt('admin', gen_salt('bf', 10)))
     , ('pepe', crypt('pepe', gen_salt('bf', 10)))
     , ('manolo', crypt('manolo', gen_salt('bf', 10)));

INSERT INTO tienda (cod_postal, loc, tnombre)
VALUES (11550, 'CHIPIONA', 'VIDEO-MANIA')
     , (11540, 'SANLUCAR', 'GAME')
     , (11520, 'ROTA', 'JUEGOS-MARPEGA')
     , (11500, 'EL PUERTO DE SANTA MARIA', 'CeX');

INSERT INTO videojuego (video_tipo, vnombre, pegi, tienda_id)
VALUES ('ESPIONAJE', 'Metal Gear Solid', 18, 2)
     , ('ROL', 'Final Fantasy', 13, 3)
     , ('SIMULACIÓN', 'Gran Turismo', 7, 1)
     , ('DEPORTES', 'Fifa', 7, 1)
     , ('PLATAFORMA', 'Crash Bandicoot', 7, 1)
     , ('RPG', 'Demon Soul', 16, 4);