--
-- Table structure for table `form_counselor_progress_note`
--

CREATE TABLE IF NOT EXISTS `form_private_intake_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date` varchar(100) DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,  
  `encounter` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,  
  `name` varchar(255) DEFAULT NULL,

  `age` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL, 
  
  `presenting_problem` text DEFAULT NULL,
  `history_presenting_problem` text DEFAULT NULL,
  `relevent_social_history` text DEFAULT NULL,
  `family_history` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `prior_medical_history` text DEFAULT NULL,
  `drug_history` text DEFAULT NULL,
  `resources_strengths` text DEFAULT NULL,

  `dsm_5_code` text DEFAULT NULL,
  `dsm_5_code_disorder` text DEFAULT NULL,

  `type_services` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,

  `therapeutic_goals_1` text DEFAULT NULL,
  `therapeutic_goals_1_date_completion` varchar(255) DEFAULT NULL,
  `therapeutic_goals_2` text DEFAULT NULL,
  `therapeutic_goals_2_date_completion` varchar(255) DEFAULT NULL,
  `therapeutic_goals_3` text DEFAULT NULL,
  `therapeutic_goals_3_date_completion` varchar(255) DEFAULT NULL,

  `signature_provider` varchar(255) DEFAULT NULL,

  `activity` TINYINT DEFAULT 1 NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;

