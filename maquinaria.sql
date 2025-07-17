
-- Crear base de datos y tablas desde cero

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

-- Contraseña de ejemplo: admin / admin123 (SHA2)
INSERT INTO usuarios (usuario, password) VALUES
('admin', SHA2('admin123', 256));

CREATE TABLE maquinaria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  tipo VARCHAR(100),
  modelo VARCHAR(100),
  ubicacion VARCHAR(100),
  tipo_maquinaria ENUM('nueva', 'usada'),
  condicion_estimada INT,
  imagen VARCHAR(255)
);
