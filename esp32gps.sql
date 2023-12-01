-- Copiando estrutura do banco de dados para esp32gps
CREATE DATABASE IF NOT EXISTS `esp32gps`
USE `esp32gps`;

-- Copiando estrutura para tabela esp32gps.coordenadas
CREATE TABLE IF NOT EXISTS `coordenadas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_dispositivo` int unsigned NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `velocidade` decimal(5,2) NOT NULL,
  `inicio` tinyint(1) NOT NULL,
  `fim` tinyint(1) NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_dispositivo` (`id_dispositivo`),
  CONSTRAINT `FK_coordenadas_dispositivos` FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Coordenadas do GPS';

-- Copiando estrutura para tabela esp32gps.dispositivos
CREATE TABLE IF NOT EXISTS `dispositivos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Dispositivos do GPS';

-- Copiando estrutura para tabela esp32gps.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando estrutura para tabela esp32gps.usuario_dispositivo
CREATE TABLE IF NOT EXISTS `usuario_dispositivo` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int unsigned NOT NULL,
  `id_dispositivo` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_dispositivo` (`id_dispositivo`),
  CONSTRAINT `FK_usuario_dispositivo_dispositivos` FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos` (`id`),
  CONSTRAINT `FK_usuario_dispositivo_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COMMENT='Mostra o dispositivo que o usuário selecionou no site';