--  Script COMPLETO — Pokédex BD
--  Crea la base de datos desde cero

CREATE DATABASE IF NOT EXISTS pokedexbd
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pokedexbd;

--  TABLA: usuarios
--  Almacena las cuentas de los entrenadores
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,       -- hash bcrypt
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

--  TABLA: favoritos
--  Cada fila es un Pokémon guardado por un usuario
CREATE TABLE IF NOT EXISTS favoritos (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT          NOT NULL,
    pokemon_id INT          NOT NULL,
    nombre     VARCHAR(100) NOT NULL,
    imagen     VARCHAR(255) NOT NULL,
    guardado_at TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,

    -- Un usuario no puede tener el mismo Pokémon dos veces
    UNIQUE KEY unique_fav (usuario_id, pokemon_id),

    -- Si se borra el usuario, se borran sus favoritos
    CONSTRAINT fk_fav_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
);

--  DATOS DE PRUEBA
--  Usuario: admin   Contraseña: 123456
INSERT IGNORE INTO usuarios (username, password)
VALUES ('admin', '$2y$10$vI8p7H8p7H8p7H8p7H8p7OeF.vI8p7H8p7H8p7H8p7H8p7H8p7H8p7');


