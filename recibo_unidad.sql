
-- Tabla relacionada solo a maquinaria usada
CREATE TABLE IF NOT EXISTS recibo_unidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquinaria_id INT NOT NULL,
    cilindros ENUM('bueno', 'regular', 'malo'),
    pistones ENUM('bueno', 'regular', 'malo'),
    anillos ENUM('bueno', 'regular', 'malo'),
    inyectores ENUM('bueno', 'regular', 'malo'),
    block ENUM('bueno', 'regular', 'malo'),
    cabeza ENUM('bueno', 'regular', 'malo'),
    transmision ENUM('bueno', 'regular', 'malo'),
    diferenciales ENUM('bueno', 'regular', 'malo'),
    cardan ENUM('bueno', 'regular', 'malo'),
    alarmas ENUM('bueno', 'regular', 'malo'),
    arneses ENUM('bueno', 'regular', 'malo'),
    sistema_hidraulico ENUM('bueno', 'regular', 'malo'),
    estetico ENUM('bueno', 'regular', 'malo'),
    consumibles ENUM('bueno', 'regular', 'malo'),
    condicion_total INT,
    FOREIGN KEY (maquinaria_id) REFERENCES maquinaria(id) ON DELETE CASCADE
);
