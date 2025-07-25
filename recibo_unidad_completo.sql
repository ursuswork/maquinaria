ALTER TABLE recibo_unidad ADD COLUMN `cilindros` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `pistones` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `anillos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `inyectores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `block` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cabeza` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `varillas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `resortes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `punterias` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cigueñal` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `arbol_de_elevas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `retenes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `ligas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `sensores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `poleas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `concha` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cremayera` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `clutch` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `coples` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `bomba_de_inyeccion` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `juntas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `marcha` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `tuberia` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `alternador` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `filtros` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `bases` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `soportes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `turbo` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `escape` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `chicotes` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `banco_de_valvulas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `bombas_de_transito` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `bombas_de_precarga` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `bombas_de_accesorios` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `clutch_hidraulico` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `gatos_de_levante` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `gatos_de_direccion` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `gatos_de_accesorios` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `mangueras` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `motores_hidraulicos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `orbitrol` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `torques_huv_satelites` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `valvulas_de_retencion` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `reductores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `puntas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `porta_puntas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `garras` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cuchillas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cepillos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `separadores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `llantas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `rines` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `bandas_orugas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `pintura` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `calcomanias` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `asiento` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `tapiceria` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `tolvas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cristales` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `accesorios` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `sistema_de_riego` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `alarmas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `arneses` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `bobinas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `botones` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cables` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cables_de_sensores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `conectores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `electro_valvulas` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `fusibles` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `porta_fusibles` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `indicadores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `presion_agua_temperatura_voltimetro` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `luces` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `modulos` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `torreta` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `relevadores` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `switch_llave` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `transmision` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `diferenciales` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;
ALTER TABLE recibo_unidad ADD COLUMN `cardan` ENUM('bueno', 'regular', 'malo') DEFAULT NULL;