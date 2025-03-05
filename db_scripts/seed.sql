--  Desactivar integridad referencial antes de insertar datos
SET FOREIGN_KEY_CHECKS = 0;

--  Limpiar las tablas antes de insertar datos de prueba
TRUNCATE TABLE auditoria;
TRUNCATE TABLE sesion;
TRUNCATE TABLE usuario;
TRUNCATE TABLE empresa;
TRUNCATE TABLE rol;
TRUNCATE TABLE parametro;

--  Insertar roles
INSERT INTO rol (id_rol, nombre) VALUES 
(1, 'superadmin'),  --  Fabricante que puede acceder a cualquier empresa
(2, 'admin'),       --  Administrador de la empresa / tienda
(3, 'operador');    --  Operador de la tienda

--  Insertar una empresa de prueba con clave encriptada
INSERT INTO empresa (id_empresa, uuid_empresa, razon_social, ruc, direccion, telefono, email, email_facturacion, cod_actividad, regimen_tributacion, clave_acceso, estado)
VALUES (
    1, UUID(), 'Tienda Demo S.A.', '0999999999001', 'Av. Principal 123', '0987654321', 
    'contacto@tiendademo.com', 'facturacion@tiendademo.com', '471001', 'RIMPE', 
    AES_ENCRYPT('claveSuperSecreta', UNHEX('a33e6f1edb9ebfe374dae211a02024a9')), 'activo'
);

--  Insertar preguntas secretas en la tabla `parametro`
INSERT INTO parametro (tipo, clave, valor, formato, estado) VALUES
('pregunta_secreta', 'PREG_01', '¿Cuál es el nombre de tu primera mascota?', 'string', 'activo'),
('pregunta_secreta', 'PREG_02', '¿En qué ciudad naciste?', 'string', 'activo'),
('pregunta_secreta', 'PREG_03', '¿Cuál es el apellido de tu mejor amigo de la infancia?', 'string', 'activo');

--  Insertar usuarios con distintos roles y respuestas secretas encriptadas
INSERT INTO usuario (uuid_usuario, id_empresa, id_rol, username, dni, nombre, email, password_hash, intentos_fallidos, bloqueado, id_pregunta, respuesta_secreta, estado)
VALUES 
(UUID(), 0, 1, 'superadmin', '0000000000', 'Fabricante', 'fabricante@empresa.com', 
    SHA2('SuperAdmin123!', 256), 0, 'no', 1, AES_ENCRYPT('MiMascota', UNHEX('a33e6f1edb9ebfe374dae211a02024a9')), 'activo'),

(UUID(), 1, 2, 'admin_tienda', '0999999999', 'Admin Tienda', 'admin@tiendademo.com', 
    SHA2('Admin123!', 256), 0, 'no', 2, AES_ENCRYPT('Quito', UNHEX('a33e6f1edb9ebfe374dae211a02024a9')), 'activo'),

(UUID(), 1, 3, 'operador1', '0988888888', 'Operador 1', 'operador1@tiendademo.com', 
    SHA2('Operador123!', 256), 0, 'no', 3, AES_ENCRYPT('Pérez', UNHEX('a33e6f1edb9ebfe374dae211a02024a9')), 'activo');

--  Insertar una sesión activa de prueba
/*
INSERT INTO sesion (id_usuario, token, ip, user_agent, inicio, ultimo_acceso, estado)
VALUES 
(1, 'token_superadmin', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64)', NOW(), NOW(), 'activa'),
(2, 'token_admin', '192.168.1.101', 'Mozilla/5.0 (Android 11; Mobile)', NOW(), NOW(), 'activa'),
(3, 'token_operador', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iOS 15_2 like Mac OS X)', NOW(), NOW(), 'activa');
*/

--  Insertar registros de auditoría con datos de prueba
INSERT INTO auditoria (id_usuario, metodo, endpoint, ip, user_agent, entrada, salida, fecha)
VALUES
(1, 'POST', '/api/login.php', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64)', '{"username": "superadmin"}', '{"success": "Login exitoso"}', NOW()),
(2, 'POST', '/api/login.php', '192.168.1.101', 'Mozilla/5.0 (Android 11; Mobile)', '{"username": "admin_tienda"}', '{"success": "Login exitoso"}', NOW()),
(3, 'POST', '/api/login.php', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iOS 15_2 like Mac OS X)', '{"username": "operador1"}', '{"success": "Login exitoso"}', NOW());

--  Reactivar la integridad referencial
SET FOREIGN_KEY_CHECKS = 1;

--  Verificar la clave de acceso de la empresa
SELECT razon_social, AES_DECRYPT(clave_acceso, UNHEX('a33e6f1edb9ebfe374dae211a02024a9')) AS clave_acceso FROM empresa;

--  Verificar la respuesta secreta de los usuarios
SELECT username, AES_DECRYPT(respuesta_secreta, UNHEX('a33e6f1edb9ebfe374dae211a02024a9')) AS respuesta_secreta FROM usuario;

SELECT username, 
       CAST(AES_DECRYPT(respuesta_secreta, UNHEX('a33e6f1edb9ebfe374dae211a02024a9')) AS CHAR) AS respuesta_secreta
FROM usuario;

