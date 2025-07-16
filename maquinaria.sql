CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);
INSERT INTO usuarios (usuario, password) VALUES ('admin', SHA2('1234', 256));

CREATE TABLE maquinaria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  tipo ENUM('nueva', 'usada') NOT NULL,
  modelo VARCHAR(50),
  numero_serie VARCHAR(100),
  ubicacion VARCHAR(100),
  imagen VARCHAR(255),
  condicion_estimada INT DEFAULT 0
);