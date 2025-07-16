
-- Tabla principal de maquinaria
CREATE TABLE IF NOT EXISTS maquinaria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('nueva','usada') NOT NULL,
    modelo VARCHAR(100),
    numero_serie VARCHAR(100),
    marca VARCHAR(100),
    anio INT,
    ubicacion VARCHAR(100),
    condicion_estimada INT DEFAULT 0,
    imagen VARCHAR(255),
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla que almacena la revisión técnica por componentes
CREATE TABLE IF NOT EXISTS recibo_unidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_maquinaria INT NOT NULL,
    seccion VARCHAR(100),
    componente VARCHAR(100),
    estado ENUM('bueno', 'regular', 'malo'),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_maquinaria) REFERENCES maquinaria(id) ON DELETE CASCADE
);

-- Catálogo de componentes por sección (estructura referencial)
CREATE TABLE IF NOT EXISTS estructura_recibo_unidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seccion VARCHAR(100) NOT NULL,
    componente VARCHAR(100) NOT NULL
);

-- Ejemplo opcional para poblar estructura base
INSERT INTO estructura_recibo_unidad (seccion, componente) VALUES
('MOTOR', 'Pistones'),
('MOTOR', 'Anillos'),
('SISTEMA MECÁNICO', 'Transmisión'),
('SISTEMA HIDRÁULICO', 'Bomba hidráulica'),
('SISTEMA ELÉCTRICO Y ELECTRÓNICO', 'Alternador'),
('ESTÉTICO', 'Pintura exterior'),
('CONSUMIBLES', 'Filtro de aire');
