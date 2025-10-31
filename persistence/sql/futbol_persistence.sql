-- --------------------------------------------------------
-- Host: 127.0.0.1
-- Versión del servidor: 10.4.32-MariaDB
-- HeidiSQL Versión: 12.12.0.7122
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `futbol_persistencia` 
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `futbol_persistencia`;

-- -----------------------------------------------------
-- Tabla: equipos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `equipos` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `estadio` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Tabla: jornadas
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jornadas` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `numero` INT NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `numero` (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Tabla: partidos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `partidos` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Equipo-Local-Id` INT NOT NULL,
  `Equipo-Visitante-Id` INT NOT NULL,
  `Jornada-Id` INT NOT NULL,
  `Resultado` ENUM('1','X','2') NULL DEFAULT NULL,
  `Estadio` VARCHAR(150) NULL DEFAULT NULL,
  
  PRIMARY KEY (`Id`),
  UNIQUE KEY `unique_partido_jornada` (`Equipo-Local-Id`, `Equipo-Visitante-Id`, `Jornada-Id`),
  
  CONSTRAINT `FK_equipo_local`
    FOREIGN KEY (`Equipo-Local-Id`) REFERENCES `equipos` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `FK_equipo_visitante`
    FOREIGN KEY (`Equipo-Visitante-Id`) REFERENCES `equipos` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `FK_jornada`
    FOREIGN KEY (`Jornada-Id`) REFERENCES `jornadas` (`Id`) ON DELETE CASCADE,
    
  CONSTRAINT `chk_equipos_distintos`
    CHECK (`Equipo-Local-Id` <> `Equipo-Visitante-Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===================================
-- LIMPIAR DATOS ANTERIORES (SI EXISTEN)
-- ===================================
DELETE FROM partidos;
DELETE FROM equipos;
DELETE FROM jornadas;

ALTER TABLE partidos AUTO_INCREMENT = 1;
ALTER TABLE equipos AUTO_INCREMENT = 1;
ALTER TABLE jornadas AUTO_INCREMENT = 1;

-- ===================================
-- DATOS DE EJEMPLO
-- ===================================

-- EQUIPOS
INSERT INTO `equipos` (`nombre`, `estadio`) VALUES
('Real Madrid', 'Santiago Bernabéu'),
('FC Barcelona', 'Camp Nou'),
('Atlético de Madrid', 'Cívitas Metropolitano'),
('Valencia CF', 'Mestalla');

-- JORNADAS
INSERT INTO `jornadas` (`numero`) VALUES (1), (2), (3);

-- PARTIDOS (¡CON BACKTICKS!)
INSERT INTO `partidos` (`Equipo-Local-Id`, `Equipo-Visitante-Id`, `Jornada-Id`, `Resultado`, `Estadio`) VALUES
(1, 2, 1, '1', 'Santiago Bernabéu'),
(3, 4, 1, 'X', 'Cívitas Metropolitano'),
(2, 3, 2, '2', 'Camp Nou'),
(4, 1, 2, NULL, 'Mestalla'),
(1, 3, 3, NULL, 'Santiago Bernabéu'),
(2, 4, 3, '1', 'Camp Nou');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;