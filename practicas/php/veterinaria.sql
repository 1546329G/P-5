-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: veterinaria
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `dni` varchar(20) NOT NULL,
  `propietario` varchar(255) NOT NULL,
  `paciente` varchar(255) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `especie` varchar(100) NOT NULL,
  `raza` varchar(100) NOT NULL,
  `sexo` enum('macho','hembra') NOT NULL,
  `color` varchar(100) NOT NULL,
  `fechaSeguimientoInicio` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (2,'vdsvsd','cdcds','cdscdscd','dsvsdv','vsdvds','0222-02-22','Felino','cscsd','','cdscdsc','2222-02-22'),(3,'dsvdsv','dscds','vdsvds','roberto fernades ','vdsv','0222-02-22','Aves','dvdsv','','dsvdsv','0000-00-00'),(4,'dsvdsv','dscds','8888','nila','vdsv','0222-02-22','Canino','dvdsv','','dsvdsv','0000-00-00'),(5,'dfvfvdf','vdsv','dvdv','fvfv','fdvdfvfd','0002-02-22','Canino','vfdvfd','macho','vfdv','0222-02-22'),(6,'dsffsd','fsdf','234567','dsfdsfds','fsdf','0222-02-22','Canino','dscsd','hembra','cdscds','0222-02-22'),(9,'alto','99','99','ana','ana1','1111-11-11','Canino','es rey','macho','es rey','1111-11-11'),(10,'GG','222','2222222','GG','GGG','0002-02-22','Canino','222','macho','222','0000-00-00'),(11,'as','df','w3234567890','juliooooooo','axc','0002-02-22','Canino','sxasx','macho','sxasxsa','2222-02-22'),(12,'v v ','dsc','c  c cc','d v',' v ','0022-02-22','Felino','xc c c','macho',' cx ','2025-01-02'),(13,'dcdscd','scdc','scdscdscds','dfdsf','dcdsc','2222-02-22','Aves','cdscdscdd','macho','cdscdsc','0000-00-00'),(14,'ss','ascs','111111ffsa','ss','ss','0002-02-22','Felino','ccdsc','macho','dcds','2222-02-22'),(15,'vdsvdsvds','vvds','vdvds456789876543','dvdsv','dsvds','0003-03-31','Felino','cdsc','macho','cdcdsc','2222-02-22'),(16,'fvds','fvdfv','666gr554','roberto fernades ','fdvdfv','0222-02-22','Canino','vvsd','macho','dsdsvsd','4444-04-04');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_visitas`
--

DROP TABLE IF EXISTS `historial_visitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_visitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `fecha_visita` date NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `mascota_id` (`mascota_id`),
  CONSTRAINT `historial_visitas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `historial_visitas_ibfk_2` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_visitas`
--

LOCK TABLES `historial_visitas` WRITE;
/*!40000 ALTER TABLE `historial_visitas` DISABLE KEYS */;
INSERT INTO `historial_visitas` VALUES (55,13,108,'2024-12-10','aaaa (Fecha: 2024-12-27)\r\nsdsdsa (Fecha: 0222-02-12)\r\n'),(60,2,113,'2024-12-10','dcdscdsc (Fecha: 0022-02-22)\r\n'),(61,3,114,'2024-12-10','dcsdcdsvdsvds (Fecha: 0222-02-22)\r\n'),(62,3,115,'2024-12-10','dcsdcdsvdsvds (Fecha: 0222-02-22)\r\naaaaaaaaaaaaaaaaaaaa (Fecha: 67555-05-31)\r\n'),(63,3,116,'2024-12-10','dcsdcdsvdsvds (Fecha: 0222-02-22)\r\naaaaaaaaaaaaaaaaaaaa (Fecha: 67555-05-31)\r\nqqqqqqqqqqqqqqqqqqqqqq\r\nq\r\nq\r\nqq (Fecha: 2024-12-25)\r\n'),(64,3,117,'2024-12-10','dcsdcdsvdsvds (Fecha: 0222-02-22)\r\naaaaaaaaaaaaaaaaaaaa (Fecha: 67555-05-31)\r\nqqqqqqqqqqqqqqqqqqqqqq\r\nq\r\nq\r\nqq (Fecha: 2024-12-25)\r\n'),(65,4,118,'2024-12-10','dcsdcdsvdsvds (Fecha: 0222-02-22)\r\naaaaaaaaaaaaaaaaaaaa (Fecha: 67555-05-31)\r\nqqqqqqqqqqqqqqqqqqqqqq\r\nq\r\nq\r\nqq (Fecha: 2024-12-25)\r\nholaaaa\r\na\r\na\r\na\r\na\r\na (Fecha: 56783-04-23)\r\n'),(66,5,119,'2024-12-10','vsvdsvdvdsvd (Fecha: 2024-12-15)\r\n'),(67,5,120,'2024-12-10','vsvdsvdvdsvd (Fecha: 2024-12-15)\r\ndfghjklñ{ñlkjhgfd (Fecha: 0022-02-22)\r\n'),(68,6,121,'2024-12-10','dsadasdsadas (Fecha: 0222-02-22)\r\n'),(69,6,122,'2024-12-10','dsadasdsadas (Fecha: 0222-02-22)\r\nppppppppppppppppppppppppppppppp\r\np\r\n*{\r\n/{*p*{{*{/**+++\r\n\r\n+\r\n+\r\n\r\n-\r\n\r\n\r\n (Fecha: 2222-02-22)\r\n'),(73,9,126,'2024-12-10','111\r\na\r\n\r\na\r\naaa (Fecha: 1111-11-11)\r\n'),(74,9,127,'2024-12-10','hola gente (Fecha: 2222-02-22)\r\n'),(75,9,128,'2024-12-10','secion2 con rapia  (Fecha: 2222-02-22)\r\n'),(79,10,130,'2024-12-13','222222222222222222222AA (Fecha: 2025-01-04)\r\n'),(80,10,131,'2024-12-13','222222222222222222222AA (Fecha: 2025-01-04)\r\n'),(93,11,132,'2024-12-15','sdfghjklñl,mjnhgfd (Fecha: 2222-02-22)\r\n'),(94,11,133,'2024-12-15','sdfghjklñl,mjnhgfd (Fecha: 2222-02-22)\r\n'),(97,12,134,'2025-01-02','ccxxc c (Fecha: 22222-02-22)\r\n'),(98,13,135,'2025-01-04','dcsdcdsc (Fecha: 2222-02-22)\r\n'),(99,14,136,'2025-01-04','dsdscdsc (Fecha: 2222-02-22)\r\n'),(100,15,137,'2025-01-04','sdfghjklñ{ñl (Fecha: 22222-02-22)\r\n'),(101,16,138,'2025-05-19','sdvsdvdsvds (Fecha: 4444-04-04)\r\n');
/*!40000 ALTER TABLE `historial_visitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mascotas`
--

DROP TABLE IF EXISTS `mascotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mascotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `especie` varchar(100) NOT NULL,
  `raza` varchar(100) NOT NULL,
  `sexo` enum('macho','hembra') NOT NULL,
  `color` varchar(100) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `propietario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `propietario_id` (`propietario_id`),
  CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`propietario_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mascotas`
--

LOCK TABLES `mascotas` WRITE;
/*!40000 ALTER TABLE `mascotas` DISABLE KEYS */;
INSERT INTO `mascotas` VALUES (108,'dasd','Felino','sdasdsad','','sadasd','2222-02-22',13),(113,'vsdvds','Felino','cscsd','','cdscdsc','0222-02-22',2),(114,'vdsv','Aves','dvdsv','','dsvdsv','0222-02-22',3),(115,'vdsv','Canino','dvdsv','','dsvdsv','0222-02-22',3),(116,'vdsv','Felino','dvdsv','','dsvdsv','0222-02-22',3),(117,'vdsv','Lagomorfos','dvdsv','','dsvdsv','0222-02-22',3),(118,'vdsv','Canino','dvdsv','','dsvdsv','0222-02-22',4),(119,'fdvdfvfd','Canino','vfdvfd','macho','vfdv','0002-02-22',5),(120,'fdvdfvfd','Felino','vfdvfd','hembra','vfdv','0002-02-22',5),(121,'fsdf','Canino','dscsd','hembra','cdscds','0222-02-22',6),(122,'fsdf','Felino','negro','macho','cdscds','0222-02-22',6),(126,'ana1','Canino','es julio','macho','es rey','0333-03-31',9),(127,'julio','Felino','es rey','hembra','es rey','1111-11-11',9),(128,'ana1','Canino','es rey','macho','es rey','0000-00-00',9),(129,'ana1','Felino','es julio','macho','es rey','0333-03-31',9),(130,'GGG','Canino','222','macho','222','0002-02-22',10),(131,'GGG','Canino','222','macho','222','0002-02-22',10),(132,'axc','Canino','sxasx','macho','sxasxsa','0002-02-22',11),(133,'axc','Felino','sxasx','hembra','sxasxsa','0002-02-22',11),(134,' v ','Felino','xc c c','macho',' cx ','0022-02-22',12),(135,'dcdsc','Aves','cdscdscdd','macho','cdscdsc','2222-02-22',13),(136,'ss','Felino','ccdsc','macho','dcds','0002-02-22',14),(137,'dsvds','Felino','cdsc','macho','cdcdsc','0003-03-31',15),(138,'fdvdfv','Canino','vvsd','macho','dsdsvsd','0222-02-22',16);
/*!40000 ALTER TABLE `mascotas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-07 12:37:34
