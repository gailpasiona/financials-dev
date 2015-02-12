CREATE DATABASE  IF NOT EXISTS `diffsigm_mibint` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `diffsigm_mibint`;
-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: diffsigm_mibint
-- ------------------------------------------------------
-- Server version	5.5.37-0+wheezy1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_tbl_PO_header`
--

DROP TABLE IF EXISTS `_tbl_PO_header`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_tbl_PO_header` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `po_number` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_date` date NOT NULL,
  `po_total_amount` decimal(12,2) NOT NULL,
  `po_paymentterms` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NA',
  `po_downpayment` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NA',
  `requestor` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `requestor_dept_det` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `po_fullyreceived` tinyint(1) NOT NULL DEFAULT '0',
  `po_status` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FOR APPROVAL',
  `created_by` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `approved_by` varchar(128) COLLATE utf8_unicode_ci DEFAULT 'NONE',
  `po_remarks` text COLLATE utf8_unicode_ci,
  `cancelled` tinyint(1) NOT NULL DEFAULT '0',
  `sync` tinyint(1) DEFAULT NULL,
  `apvbedit` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `apvcredit` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`,`company_id`),
  UNIQUE KEY `po_number_UNIQUE` (`po_number`),
  KEY `fk__tbl_PO_header_bp` (`supplier_id`),
  CONSTRAINT `fk__tbl_PO_header_bp` FOREIGN KEY (`supplier_id`) REFERENCES `_tbl_business_partner` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_tbl_PO_header`
--

LOCK TABLES `_tbl_PO_header` WRITE;
/*!40000 ALTER TABLE `_tbl_PO_header` DISABLE KEYS */;
INSERT INTO `_tbl_PO_header` VALUES (1,2,'11111',3,'2014-10-14',3000.00,'','','IT','',0,'','Juan Mambo','Pedro Rico','',0,0,NULL,'',NULL,'2014-12-03 00:59:04'),(2,1,'11112',2,'2014-09-24',10500.00,'NA','NA','Accounting','',0,'FOR APPROVAL','Ramon Pepano','Rogelio Padre',NULL,0,0,NULL,'','2014-10-15 06:19:42','2014-12-03 00:59:05'),(3,2,'11113',3,'2014-05-29',100000.00,'NA','NA','Purchasing','',0,'FOR APPROVAL','Paula Maldita','Paulo Chobibo',NULL,0,0,NULL,'','2014-10-15 06:21:25','2014-12-03 00:59:06'),(4,3,'11114',4,'2014-05-30',50500.00,'NA','NA','Admin','',0,'FOR APPROVAL','Raff Padilla','Rey Pahuyo',NULL,0,0,NULL,'','2014-10-15 06:30:28','2014-12-10 19:40:19'),(5,3,'11115',5,'2014-06-30',300000.00,'NA','NA','COB','',0,'','Jensen Jansen','John Doe',NULL,0,0,NULL,'','2014-10-15 06:36:56','2014-12-03 00:59:06');
/*!40000 ALTER TABLE `_tbl_PO_header` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_tbl_business_partner`
--

DROP TABLE IF EXISTS `_tbl_business_partner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_tbl_business_partner` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `supplier_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `payment_term` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `contact_person` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `contact_number` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `contact_email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `sync` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`,`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_tbl_business_partner`
--

LOCK TABLES `_tbl_business_partner` WRITE;
/*!40000 ALTER TABLE `_tbl_business_partner` DISABLE KEYS */;
INSERT INTO `_tbl_business_partner` VALUES (1,1,'Supplier 1','Supplier 1','','Supplier 1','Supplier 1','supplier1@email.com',0,NULL,'2014-12-03 00:58:54'),(2,1,'Supplier 2','Supplier 1','','Supplier 2','Supplier 2','supplier2@email.com',0,NULL,'2014-12-03 00:58:55'),(3,2,'Supplier 3','Supplier 3','','Supplier 3','Supplier 3','supplier3@email.com',0,NULL,'2014-12-03 00:58:57'),(4,3,'Supplier 4','Supplier 4','','Supplier 4','Supplier 4','supplier4@email.com',0,NULL,'2014-11-10 21:38:41'),(5,3,'Supplier 5','Supplier 5','','Supplier 5','Supplier 5','supplier5@email.com',0,NULL,'2014-12-03 00:59:00');
/*!40000 ALTER TABLE `_tbl_business_partner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_tbl_payroll_summary_entry`
--

DROP TABLE IF EXISTS `_tbl_payroll_summary_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_tbl_payroll_summary_entry` (
  `ps_entry_no` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  `ps_glAccount` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `ps_amount` double NOT NULL,
  `ps_memo` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `ps_detachment` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sync` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ps_entry_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_tbl_payroll_summary_entry`
--

LOCK TABLES `_tbl_payroll_summary_entry` WRITE;
/*!40000 ALTER TABLE `_tbl_payroll_summary_entry` DISABLE KEYS */;
INSERT INTO `_tbl_payroll_summary_entry` VALUES ('1234 SMA',3,'glacct1',200000,'payroll for december','SMART',0,'0000-00-00 00:00:00','2014-11-11 00:23:36'),('1235 GLB',2,'glacct2',400000,'payroll for november','GLOBE',0,'2014-11-11 04:38:20','2014-12-10 19:41:26');
/*!40000 ALTER TABLE `_tbl_payroll_summary_entry` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-11 11:50:08
