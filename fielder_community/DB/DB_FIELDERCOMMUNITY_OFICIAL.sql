DROP DATABASE IF EXISTS FielderCommunity;
CREATE DATABASE FielderCommunity;
USE  FielderCommunity;

-- DROP TABLE IF EXISTS users;
CREATE TABLE users(
	user_id INT NOT NULl AUTO_INCREMENT,
    CONSTRAINT PK_user_id PRIMARY KEY(user_id),
    nombre_usuario VARCHAR(50) NOT NULL,
    CONSTRAINT Unq_nombre_usuario UNIQUE(nombre_usuario),
    email VARCHAR(60) NOT NULL,
    CONSTRAINT Unq_email UNIQUE(email),
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(64) NOT NULL
);

-- DROP PROCEDURE alta_users;

DELIMITER %%
CREATE PROCEDURE alta_users(IN nombre_usuario_p VARCHAR(50), IN email_p VARCHAR(60), IN password_p VARCHAR(255))
BEGIN
    DECLARE valid BOOLEAN DEFAULT TRUE;
    DECLARE salt_value VARCHAR(64); -- Cambiar la longitud de la variable
    
    -- Limpiar y validar datos de entrada
    SET nombre_usuario_p = TRIM(nombre_usuario_p);
    SET email_p = TRIM(email_p);
    SET password_p = TRIM(password_p);

    -- Validar nombre de usuario
    IF nombre_usuario_p IS NULL OR nombre_usuario_p = '' THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El nombre de usuario no puede estar vacío.';
    ELSEIF LENGTH(nombre_usuario_p) < 3 OR LENGTH(nombre_usuario_p) > 50 THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45003'
        SET MESSAGE_TEXT = 'El nombre de usuario debe tener entre 3 y 50 caracteres.';
    ELSEIF EXISTS (SELECT 1 FROM users WHERE nombre_usuario = nombre_usuario_p OR email = email_p) THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45005'
        SET MESSAGE_TEXT = 'El nombre de usuario o el email ya están en uso. Por favor, elija otros.';
    END IF;
    
    -- Validar email
    IF email_p IS NULL OR email_p = '' THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45001'
        SET MESSAGE_TEXT = 'El email no puede estar vacío.';
    ELSEIF LENGTH(email_p) > 60 THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45006'
        SET MESSAGE_TEXT = 'El email no puede tener más de 60 caracteres.';
    ELSEIF NOT (email_p REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$') THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45008'
        SET MESSAGE_TEXT = 'El formato del email no es válido.';
    END IF;
    
    -- Validar contraseña
    IF password_p IS NULL OR password_p = '' THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45002'
        SET MESSAGE_TEXT = 'La contraseña no puede estar vacía.';
    ELSEIF LENGTH(password_p) < 8 THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45009'
        SET MESSAGE_TEXT = 'La contraseña debe tener al menos 8 caracteres.';
    ELSEIF NOT (
        password_p REGEXP '[[:digit:]]' AND -- Al menos un número
        password_p REGEXP '[[:upper:]]' AND -- Al menos una letra mayúscula
        password_p REGEXP '[[:punct:]]' -- Al menos un caracter especial
    ) THEN
        SET valid = FALSE;
        SIGNAL SQLSTATE '45010'
        SET MESSAGE_TEXT = 'La contraseña debe contener al menos un número, una letra mayúscula y un caracter especial.';
    END IF;

    -- Si todas las validaciones son exitosas, proceder con la inserción
    IF valid THEN
        -- Hashear la contraseña con una sal antes de almacenarla
        SET salt_value = SHA2(UUID(), 256); -- Generar una salt única para cada usuario
        SET password_p = SHA2(CONCAT(password_p, salt_value), 256);
    ELSE
        SIGNAL SQLSTATE '45011'
        SET MESSAGE_TEXT = 'Error en las validaciones. No se puede realizar la inserción.';
    END IF;
    
    -- Uso de una transacción para garantizar la integridad de los datos
    START TRANSACTION;
    INSERT INTO users(nombre_usuario, email, password, salt) VALUES(nombre_usuario_p, email_p, password_p, salt_value);
    COMMIT;
END %%
DELIMITER ;

CALL alta_users('JazzielGod2','jazziel222@gmail.com','Jazziel111*');

SELECT * FROM users;





