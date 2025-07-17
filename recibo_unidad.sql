CREATE TABLE IF NOT EXISTS recibo_unidad_motor (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_maquinaria INT NOT NULL,
  `cilindros` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `pistones` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `anillos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `inyectores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `block` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cabeza` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `varillas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `resortes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `punterias` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cigüeñal` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `arbol_de_elevas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `retenes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `ligas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `sensores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `poleas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `concha` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cremayera` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `clutch` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `coples` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bomba_de_inyeccion` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `juntas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `marcha` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `tuberia` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `alternador` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `filtros` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bases` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `soportes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `turbo` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `escape` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `chicotes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  FOREIGN KEY (id_maquinaria) REFERENCES maquinaria(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS recibo_unidad_hidraulico (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_recibo INT,
  `banco_de_valvulas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bombas_de_transito` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bombas_de_precarga` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bombas_de_accesorios` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `coples` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `clutch_hidraulico` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `gatos_de_levante` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `gatos_de_direccion` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `gatos_de_accesorios` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `mangueras` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `motores_hidraulicos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `orbitrol` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `torques_huv_satélites` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `valvulas_de_retencion` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `reductores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  FOREIGN KEY (id_recibo) REFERENCES recibo_unidad(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS recibo_unidad_consumibles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_recibo INT,
  `puntas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `porta_puntas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `garras` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cuchillas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cepillos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `separadores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `llantas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `rines` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bandas___orugas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  FOREIGN KEY (id_recibo) REFERENCES recibo_unidad(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS recibo_unidad_estetico (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_recibo INT,
  `pintura` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `calcomanias` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `asiento` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `tapiceria` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `tolvas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cristales` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `accesorios` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `sistema_de_riego` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  FOREIGN KEY (id_recibo) REFERENCES recibo_unidad(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS recibo_unidad_electrico (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_recibo INT,
  `alarmas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `arneses` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bobinas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `botones` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cables` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cables_de_sensores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `conectores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `electro_valvulas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `fusibles` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `porta_fusibles` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `indicadores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `presion_agua_temperaturavoltimetro` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `luces` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `modulos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `torreta` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `relevadores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `switch_llave` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `sensores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  FOREIGN KEY (id_recibo) REFERENCES recibo_unidad(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS recibo_unidad_motor (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_recibo INT NOT NULL,
  `cilindros` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `pistones` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `anillos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `inyectores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `block` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cabeza` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `varillas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `resortes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `punterias` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cigüeñal` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `arbol_de_elevas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `retenes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `ligas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `sensores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `poleas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `concha` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cremayera` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `clutch` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `coples` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bomba_de_inyeccion` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `juntas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `marcha` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `tuberia` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `alternador` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `filtros` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `bases` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `soportes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `turbo` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `escape` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `chicotes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  FOREIGN KEY (id_recibo) REFERENCES recibo_unidad(id)
);

CREATE TABLE IF NOT EXISTS recibo_unidad_mecanico (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_recibo INT NOT NULL,
  `transmisión` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `diferenciales` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  `cardán` ENUM('bueno', 'regular', 'malo') DEFAULT NULL,
  FOREIGN KEY (id_recibo) REFERENCES recibo_unidad(id)
);