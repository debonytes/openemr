CREATE TABLE IF NOT EXISTS `form_icans_note` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pid` bigint(20) DEFAULT NULL,  
  `encounter` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,

  `name` varchar(255) DEFAULT NULL,
  `examiner` varchar(255) DEFAULT NULL,
  `session_number` varchar(255) DEFAULT NULL,
  `billing_code` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `dateofservice` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `service_note` text DEFAULT NULL, 
  
  `activity` TINYINT DEFAULT 1 NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
