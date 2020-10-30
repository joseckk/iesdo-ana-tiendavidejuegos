--------------------------
-- Base de datos bd --
--------------------------

DROP TABLE IF EXISTS videojuegos CASCADE;


CREATE TABLE videojuegos
(
        id          bigserial        PRIMARY KEY
    ,   video_tipo  varchar(255)     NOT NULL
    ,   vnombre     varchar(255)     NOT NULL UNIQUE
    ,   pegi        numeric(2)       
);


INSERT INTO videojuegos (video_tipo, vnombre, pegi)
VALUES ('ESPIONAJE', 'Metal Gear Solid', 18)
     , ('ROL', 'Final Fantasy', 13)
     , ('SIMULACIÓN', 'Gran Turismo', 7)
     , ('DEPORTES', 'Fifa', 7)
     , ('PLATAFORMA', 'Crash Bandicoot', 7)
     , ('RPG', 'Demon Soul', 16);