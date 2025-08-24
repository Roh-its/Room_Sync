CREATE DATABASE  IF NOT EXISTS `myhostel` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `myhostel`;
-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: myhostel
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adminlogin`
--

DROP TABLE IF EXISTS `adminlogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adminlogin` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `MID` int NOT NULL,
  PRIMARY KEY (`username`),
  KEY `MID` (`MID`),
  CONSTRAINT `adminlogin_ibfk_1` FOREIGN KEY (`MID`) REFERENCES `manager` (`MID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminlogin`
--

LOCK TABLES `adminlogin` WRITE;
/*!40000 ALTER TABLE `adminlogin` DISABLE KEYS */;
INSERT INTO `adminlogin` VALUES ('manager.at1@gmail.com','f4cba934d76a80a7381ba45e1142bdd9',1234),('manager.at2@gmail.com','41d10fdbfcb7298747ce6c6c04b52011',1235);
/*!40000 ALTER TABLE `adminlogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `care_taker`
--

DROP TABLE IF EXISTS `care_taker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `care_taker` (
  `CTID` int NOT NULL,
  `CTName` varchar(50) NOT NULL,
  `CT_FName` varchar(50) NOT NULL,
  `CTAddress` varchar(50) NOT NULL,
  `CTPhone` int NOT NULL,
  `CTEmail` varchar(50) NOT NULL,
  `CTGender` varchar(6) NOT NULL,
  `CTCnic` int NOT NULL,
  `CTHID` varchar(10) NOT NULL,
  PRIMARY KEY (`CTID`),
  KEY `CTHID` (`CTHID`),
  CONSTRAINT `care_taker_ibfk_1` FOREIGN KEY (`CTHID`) REFERENCES `hostel` (`HID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `care_taker`
--

LOCK TABLES `care_taker` WRITE;
/*!40000 ALTER TABLE `care_taker` DISABLE KEYS */;
/*!40000 ALTER TABLE `care_taker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaints` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CDate` date NOT NULL,
  `CType` varchar(50) NOT NULL,
  `CDescription` varchar(50) NOT NULL,
  `Rno` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Rno` (`Rno`),
  CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`Rno`) REFERENCES `room` (`RNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hostel`
--

DROP TABLE IF EXISTS `hostel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hostel` (
  `HID` varchar(10) NOT NULL,
  `HName` varchar(50) NOT NULL,
  `HCapacity` int NOT NULL,
  PRIMARY KEY (`HID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hostel`
--

LOCK TABLES `hostel` WRITE;
/*!40000 ALTER TABLE `hostel` DISABLE KEYS */;
INSERT INTO `hostel` VALUES ('1','Attar Hostels Block 1',400),('2','MU HOSTEL',400);
/*!40000 ALTER TABLE `hostel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice` (
  `IID` int NOT NULL AUTO_INCREMENT,
  `IType` varchar(20) NOT NULL,
  `IDueDate` date NOT NULL,
  `IAmount` int NOT NULL,
  `CMS` int NOT NULL,
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`IID`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice`
--

LOCK TABLES `invoice` WRITE;
/*!40000 ALTER TABLE `invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager`
--

DROP TABLE IF EXISTS `manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manager` (
  `MID` int NOT NULL,
  `MName` varchar(50) NOT NULL,
  `M_FName` varchar(50) NOT NULL,
  `MAddress` varchar(50) NOT NULL,
  `MPhone` varchar(11) NOT NULL,
  `MEmail` varchar(50) NOT NULL,
  `MGender` varchar(6) NOT NULL,
  `MCnic` varchar(15) NOT NULL,
  `MHID` varchar(10) NOT NULL,
  PRIMARY KEY (`MID`),
  KEY `MHID` (`MHID`),
  CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`MHID`) REFERENCES `hostel` (`HID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager`
--

LOCK TABLES `manager` WRITE;
/*!40000 ALTER TABLE `manager` DISABLE KEYS */;
INSERT INTO `manager` VALUES (1234,'Muhammad Aftab','...','...','...','manager.at1@gmail.com','Male','...','1'),(1235,'Muhammad Ali','...','...','...','manager.at2@gmail.com','Male','...','2');
/*!40000 ALTER TABLE `manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!50001 DROP VIEW IF EXISTS `menu`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `menu` AS SELECT 
 1 AS `Day`,
 1 AS `BREAKFAST`,
 1 AS `LUNCH`,
 1 AS `DINNER`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `mess_off`
--

DROP TABLE IF EXISTS `mess_off`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mess_off` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `MSDate` date NOT NULL,
  `MEDate` date NOT NULL,
  `MReason` varchar(50) NOT NULL,
  `CMS` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `mess_off_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mess_off`
--

LOCK TABLES `mess_off` WRITE;
/*!40000 ALTER TABLE `mess_off` DISABLE KEYS */;
/*!40000 ALTER TABLE `mess_off` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mess_weekly_menu`
--

DROP TABLE IF EXISTS `mess_weekly_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mess_weekly_menu` (
  `Day` varchar(10) NOT NULL,
  `BREAKFAST` varchar(50) NOT NULL,
  `LUNCH` varchar(50) NOT NULL,
  `DINNER` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mess_weekly_menu`
--

LOCK TABLES `mess_weekly_menu` WRITE;
/*!40000 ALTER TABLE `mess_weekly_menu` DISABLE KEYS */;
INSERT INTO `mess_weekly_menu` VALUES ('Monday','Paratha, Omelate, Tea','veg','Fish/Kabab, Daal Channa, Roti'),('Tuesday','Naan Chany, Tea','Kari Pakora, Roti/Rice','Chicken Korma, Roti'),('Wednesday','Paratha, Aloo Andy, Tea','Daal Mash, Roti','Chicken Manchurian with Fried Egg Rice/ Chana Pula'),('Thursday','French Toast, Tea','Aloo Palak, Roti','Biryani, Cold Drinks'),('Friday','Paratha, Aloo Bhujya, Tea','Rice, Dall Mong/Masar','Chicken Kofta, Roti'),('Saturday','Aloo Paratha, Tea','veg','Chapli Kabab, Roti'),('Sunday','Halwa Puri, Chany, Tea','Red Beans, roti','Chicken Pulao');
/*!40000 ALTER TABLE `mess_weekly_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parcel`
--

DROP TABLE IF EXISTS `parcel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parcel` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `PDate` date NOT NULL,
  `PType` varchar(50) NOT NULL,
  `PStatus` varchar(50) NOT NULL,
  `CMS` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `parcel_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parcel`
--

LOCK TABLES `parcel` WRITE;
/*!40000 ALTER TABLE `parcel` DISABLE KEYS */;
/*!40000 ALTER TABLE `parcel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room` (
  `RNo` int NOT NULL,
  `RCapacity` int NOT NULL,
  `RHID` varchar(10) NOT NULL,
  PRIMARY KEY (`RNo`),
  KEY `RHID` (`RHID`),
  CONSTRAINT `room_ibfk_1` FOREIGN KEY (`RHID`) REFERENCES `hostel` (`HID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room`
--

LOCK TABLES `room` WRITE;
/*!40000 ALTER TABLE `room` DISABLE KEYS */;
INSERT INTO `room` VALUES (1,3,'1'),(2,3,'1'),(3,3,'1'),(4,3,'1'),(5,3,'2'),(6,3,'1'),(7,3,'1'),(8,3,'1'),(9,3,'1'),(10,3,'1'),(11,3,'1'),(12,3,'1'),(13,3,'1'),(14,3,'1'),(15,3,'1'),(16,3,'1'),(17,3,'1'),(18,3,'1'),(19,3,'1'),(20,3,'1'),(21,3,'1'),(22,3,'1'),(23,3,'1'),(24,3,'1'),(25,3,'1'),(26,3,'1'),(27,3,'1'),(28,3,'1'),(29,3,'1'),(30,3,'1'),(31,3,'1'),(32,3,'1'),(33,3,'1'),(34,3,'1'),(35,3,'1'),(36,3,'1'),(37,3,'1'),(38,3,'1'),(39,3,'1'),(40,3,'1'),(41,3,'1'),(42,3,'1'),(43,3,'1'),(44,3,'1'),(45,3,'1'),(46,3,'1'),(47,3,'1'),(48,3,'1'),(49,3,'1'),(50,3,'1'),(51,3,'1'),(52,3,'1'),(53,3,'1'),(54,3,'1'),(55,3,'1'),(101,3,'2'),(102,3,'2'),(103,3,'2'),(104,3,'2'),(105,3,'2'),(106,3,'2'),(107,3,'2'),(108,3,'2'),(109,3,'2'),(110,3,'2'),(111,3,'2'),(112,3,'2'),(113,3,'2'),(114,3,'2'),(115,3,'2'),(116,3,'2'),(117,3,'2'),(118,3,'2'),(119,3,'2'),(120,3,'2'),(121,3,'2'),(122,3,'2'),(123,3,'2'),(124,3,'2'),(125,3,'2'),(126,3,'2'),(127,3,'2'),(128,3,'2'),(129,3,'2'),(130,3,'2'),(131,3,'2'),(132,3,'2'),(133,3,'2'),(134,3,'2'),(135,3,'2'),(136,3,'2'),(137,3,'2'),(138,3,'2'),(139,3,'2'),(140,3,'2'),(141,3,'2'),(142,3,'2'),(143,3,'2'),(144,3,'2'),(145,3,'2'),(146,3,'2'),(147,3,'2'),(148,3,'2'),(149,3,'2'),(150,3,'2'),(151,3,'2'),(152,3,'2'),(153,3,'2'),(154,3,'2'),(155,3,'2'),(156,3,'2'),(157,3,'2'),(158,3,'2'),(159,3,'2'),(160,3,'2'),(161,3,'2'),(162,3,'2'),(163,3,'2'),(164,3,'2'),(165,3,'2'),(166,3,'2'),(167,3,'2'),(168,3,'2'),(169,3,'2'),(170,3,'2'),(171,3,'2'),(172,3,'2'),(327,3,'2');
/*!40000 ALTER TABLE `room` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `SID` int NOT NULL,
  `SName` varchar(50) NOT NULL,
  `S_FName` varchar(50) NOT NULL,
  `SAddress` varchar(50) NOT NULL,
  `SPhone` int NOT NULL,
  `SGender` varchar(6) NOT NULL,
  `SCnic` int NOT NULL,
  `SPosition` varchar(50) NOT NULL,
  `SHID` varchar(10) NOT NULL,
  PRIMARY KEY (`SID`),
  KEY `SHID` (`SHID`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`SHID`) REFERENCES `hostel` (`HID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_attendance`
--

DROP TABLE IF EXISTS `staff_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_attendance` (
  `StAID` int NOT NULL,
  `StADate` date NOT NULL,
  `Status` varchar(10) NOT NULL,
  `SID` int NOT NULL,
  PRIMARY KEY (`StAID`),
  KEY `SID` (`SID`),
  CONSTRAINT `staff_attendance_ibfk_1` FOREIGN KEY (`SID`) REFERENCES `staff` (`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_attendance`
--

LOCK TABLES `staff_attendance` WRITE;
/*!40000 ALTER TABLE `staff_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_leave`
--

DROP TABLE IF EXISTS `staff_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_leave` (
  `SLID` int NOT NULL,
  `SLDate` date NOT NULL,
  `SLEDate` date NOT NULL,
  `SLReason` varchar(50) NOT NULL,
  `SID` int NOT NULL,
  PRIMARY KEY (`SLID`),
  KEY `SID` (`SID`),
  CONSTRAINT `staff_leave_ibfk_1` FOREIGN KEY (`SID`) REFERENCES `staff` (`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_leave`
--

LOCK TABLES `staff_leave` WRITE;
/*!40000 ALTER TABLE `staff_leave` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_salary`
--

DROP TABLE IF EXISTS `staff_salary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_salary` (
  `SSID` int NOT NULL,
  `SSDate` date NOT NULL,
  `SSAmount` int NOT NULL,
  `Status` varchar(50) NOT NULL,
  `SID` int NOT NULL,
  PRIMARY KEY (`SSID`),
  KEY `SID` (`SID`),
  CONSTRAINT `staff_salary_ibfk_1` FOREIGN KEY (`SID`) REFERENCES `staff` (`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_salary`
--

LOCK TABLES `staff_salary` WRITE;
/*!40000 ALTER TABLE `staff_salary` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_salary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_violation`
--

DROP TABLE IF EXISTS `staff_violation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_violation` (
  `StVID` int NOT NULL,
  `StVDate` date NOT NULL,
  `StVType` varchar(50) NOT NULL,
  `SID` int NOT NULL,
  PRIMARY KEY (`StVID`),
  KEY `SID` (`SID`),
  CONSTRAINT `staff_violation_ibfk_1` FOREIGN KEY (`SID`) REFERENCES `staff` (`SID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_violation`
--

LOCK TABLES `staff_violation` WRITE;
/*!40000 ALTER TABLE `staff_violation` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_violation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `CMS` int NOT NULL,
  `SName` varchar(50) NOT NULL,
  `S_FName` varchar(50) NOT NULL,
  `SAddress` varchar(50) NOT NULL,
  `SEmail` varchar(50) NOT NULL,
  `SPhone` varchar(11) NOT NULL,
  `SCnic` varchar(15) NOT NULL,
  `SGender` varchar(6) NOT NULL,
  `Dept` varchar(50) NOT NULL,
  `SHID` varchar(10) NOT NULL,
  `SRNo` int NOT NULL,
  PRIMARY KEY (`CMS`),
  KEY `SRNo` (`SRNo`),
  KEY `student_ibfk_4` (`SHID`),
  CONSTRAINT `student_ibfk_3` FOREIGN KEY (`SRNo`) REFERENCES `room` (`RNo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `student_ibfk_4` FOREIGN KEY (`SHID`) REFERENCES `hostel` (`HID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (366885,'Muhammad Danish Azeem','Muhammad Tariq Naveed','Habibabad','muhammaddanish1458@gmail.com','03135692472','3510344164163','Male','SEECS','1',1),(371303,'Muhammad Zaeem','Khalid','Rawalpindi','zaeem@gmail.com','03004445719','42501','Male','SEECS','1',2),(425648,'Muhammad Luqman Ahmad','Noor Muhammad','Chakwal','luqman@gmail.com','03001234567','4256','Male','S3H','1',1);
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_attendance`
--

DROP TABLE IF EXISTS `student_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_attendance` (
  `SAID` int NOT NULL AUTO_INCREMENT,
  `SADate` date NOT NULL,
  `Status` varchar(10) NOT NULL,
  `CMS` int NOT NULL,
  PRIMARY KEY (`SAID`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `student_attendance_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_attendance`
--

LOCK TABLES `student_attendance` WRITE;
/*!40000 ALTER TABLE `student_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_violation`
--

DROP TABLE IF EXISTS `student_violation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_violation` (
  `VID` int NOT NULL,
  `VDate` date NOT NULL,
  `VType` varchar(50) NOT NULL,
  `CMS` int NOT NULL,
  PRIMARY KEY (`VID`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `student_violation_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_violation`
--

LOCK TABLES `student_violation` WRITE;
/*!40000 ALTER TABLE `student_violation` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_violation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studentin_out`
--

DROP TABLE IF EXISTS `studentin_out`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studentin_out` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CMS` int NOT NULL,
  `LeaveDate` date NOT NULL,
  `ReturnDate` date NOT NULL,
  `Reason` varchar(50) NOT NULL,
  `City` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `id` (`ID`),
  KEY `studentin_out_ibfk_1` (`CMS`),
  CONSTRAINT `studentin_out_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studentin_out`
--

LOCK TABLES `studentin_out` WRITE;
/*!40000 ALTER TABLE `studentin_out` DISABLE KEYS */;
/*!40000 ALTER TABLE `studentin_out` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studentlogin`
--

DROP TABLE IF EXISTS `studentlogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studentlogin` (
  `email` varchar(50) NOT NULL,
  `PASSWORD` varchar(50) NOT NULL,
  `CMS` int NOT NULL,
  PRIMARY KEY (`email`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `studentlogin_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studentlogin`
--

LOCK TABLES `studentlogin` WRITE;
/*!40000 ALTER TABLE `studentlogin` DISABLE KEYS */;
INSERT INTO `studentlogin` VALUES ('luqman@gmail.com','4781c13b4bf9b7288a343fd274ff0310',425648),('muhammaddanish1458@gmail.com','5b119a961fcb523c81c25e8f79de2380',366885),('zaeem@gmail.com','fd829f4e69e7b91e89c1d1aa31effb98',371303);
/*!40000 ALTER TABLE `studentlogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suggestions`
--

DROP TABLE IF EXISTS `suggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suggestions` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SUGDate` date NOT NULL,
  `SUGType` varchar(50) NOT NULL,
  `SUGDescription` varchar(50) NOT NULL,
  `CMS` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `suggestions_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suggestions`
--

LOCK TABLES `suggestions` WRITE;
/*!40000 ALTER TABLE `suggestions` DISABLE KEYS */;
/*!40000 ALTER TABLE `suggestions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor`
--

DROP TABLE IF EXISTS `visitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitor` (
  `VID` int NOT NULL AUTO_INCREMENT,
  `VName` varchar(50) NOT NULL,
  `VRelation` varchar(50) NOT NULL,
  `VPhone` varchar(11) NOT NULL,
  `VCnic` varchar(15) NOT NULL,
  `VDate` date NOT NULL,
  `VReason` varchar(50) NOT NULL,
  `CMS` int NOT NULL,
  PRIMARY KEY (`VID`),
  KEY `CMS` (`CMS`),
  CONSTRAINT `visitor_ibfk_1` FOREIGN KEY (`CMS`) REFERENCES `student` (`CMS`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor`
--

LOCK TABLES `visitor` WRITE;
/*!40000 ALTER TABLE `visitor` DISABLE KEYS */;
/*!40000 ALTER TABLE `visitor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'myhostel'
--
/*!50003 DROP PROCEDURE IF EXISTS `changepwd` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `changepwd`(IN `CMS` INT, IN `np` VARCHAR(50))
BEGIN
    update studentlogin set password=np where cms=CMS;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `comp` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `comp`(IN `id` INT)
BEGIN
    
        SELECT * FROM complaints,room WHERE room.RHID = id and complaints.Rno=room.RNo;


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `deletestudent` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `deletestudent`(in ms int)
BEGIN
    
    DELETE FROM studentlogin WHERE cms=ms;
    
    DELETE from student where cms=ms;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `free` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `free`(IN `id` INT)
BEGIN
    
        SELECT rno FROM room,student where rhid=id and shid=id GROUP by rno,rcapacity HAVING count(rno)<=rcapacity;


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `get_visitors` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_visitors`(in id int)
BEGIN
Select * from visitor,student where visitor.cms=student.cms and student.shid=id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `in_out` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `in_out`(IN `hid` INT)
BEGIN
Select * from studentin_out as i,student as s where i.cms=s.cms and s.shid=hid;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `manager` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `manager`(IN `id` INT)
BEGIN
    
        SELECT * FROM manager,hostel WHERE MID = id and MHID = HID;


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `mess_off` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `mess_off`(IN `hid` INT)
BEGIN
Select * from mess_off as i,student as s where i.cms=s.cms and s.shid=hid;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `register` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `register`(IN `CMS` INT, IN `Name` VARCHAR(50), IN `FName` VARCHAR(50), IN `Address` VARCHAR(50), IN `mail` VARCHAR(50), IN `Phone` VARCHAR(11), IN `Cnic` VARCHAR(15), IN `Gender` VARCHAR(6), IN `sDept` VARCHAR(50), IN `HID` VARCHAR(10), IN `RNo` INT, IN `pwd` VARCHAR(50))
BEGIN
INSERT INTO `student` (`CMS`, `SName`, `S_FName`, `SAddress`, `SEmail`, `SPhone`, `SCnic`, `SGender`, `Dept`, `SHID`, `SRNo`) VALUES (CMS, Name, FName, Address, mail, Phone, Cnic, Gender, sDept, HID, RNo);
        INSERT INTO `studentlogin` (`email`, `PASSWORD`, `CMS`) VALUES (mail, pwd, CMS);

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `StudentDetails` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `StudentDetails`(IN `CMS` INT)
BEGIN

SELECT * FROM Student,hostel,room WHERE Student.CMS = CMS and SRno = RNo and Student.SHID=HID;
	
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `menu`
--

/*!50001 DROP VIEW IF EXISTS `menu`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `menu` AS select `mess_weekly_menu`.`Day` AS `Day`,`mess_weekly_menu`.`BREAKFAST` AS `BREAKFAST`,`mess_weekly_menu`.`LUNCH` AS `LUNCH`,`mess_weekly_menu`.`DINNER` AS `DINNER` from `mess_weekly_menu` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-24 23:44:10
