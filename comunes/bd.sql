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
        id              bigserial        PRIMARY KEY
    ,   video_tipo      varchar(255)     NOT NULL
    ,   vnombre         varchar(255)     NOT NULL
    ,   precio          numeric(4,2)     NOT NULL   
    ,   pegi            numeric(2)       
    ,   fecha_alt       timestamp        NOT NULL
    ,   fecha_baj       timestamp        
    ,   disponibilidad  boolean          NOT NULL
    ,   usuario_id      bigint           REFERENCES usuario (id)
    ,   tienda_id       bigint           NOT NULL REFERENCES tienda (id)       
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

INSERT INTO videojuego (video_tipo, vnombre, precio, pegi, 
                        fecha_alt, fecha_baj, disponibilidad, usuario_id, tienda_id)
VALUES ('ESPIONAJE', 'Metal Gear Solid', 59.99, NULL, '2015-06-02 18:23:15', NULL, false, 2, 3)
     , ('ROL', 'Final Fantasy', 30.00, 3, '2020-06-02 20:23:15', NULL, false, 3, 1)
     , ('SIMULACIÓN', 'Gran Turismo', 45.50, 3, '2020-01-01 12:30:15', NULL, true, NULL, 4)
     , ('DEPORTES', 'Fifa', 59.99, 3, '2020-09-15 14:23:15', NULL, true, NULL, 1)
     , ('PLATAFORMA', 'Crash Bandicoot', 39.90, 3, '2019-08-28 21:23:15', NULL, true, NULL, 3)
     , ('RPG', 'Demon Soul', 25.00, NULL, '2015-01-30 15:00:15', NULL, true, NULL, 2);