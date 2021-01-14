--
-- Table structure for table `counselor_treatment_plan`
--

CREATE TABLE IF NOT EXISTS `form_counselor_treatment_plan_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date` varchar(100) DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,  
  `encounter` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,  
  `name` varchar(255) DEFAULT NULL,

  `day_review` varchar(50) DEFAULT NULL,
  `initial_plan_date` varchar(50) DEFAULT NULL,
  `medicaid` varchar(100) DEFAULT NULL,
  `diagnosis_code` varchar(100) DEFAULT NULL,
  `type_service` varchar(100) DEFAULT NULL,
  `examiner` varchar(100) DEFAULT NULL,
  `dob` varchar(50) DEFAULT NULL,

  `problem_area` varchar(150) DEFAULT NULL,
  `describe_comprehensive_assessment` text DEFAULT NULL,

  `overall_goal` text DEFAULT NULL,

  `overall_obj1` varchar(200) DEFAULT NULL,
  `overall_obj1_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj1_frequency` varchar(100) DEFAULT NULL,
  `overall_obj1_duration` varchar(100) DEFAULT NULL,
  `overall_obj1_place` varchar(100) DEFAULT NULL,

  `overall_obj1_90_review` varchar(200) DEFAULT NULL,
  `overall_obj1_90_review_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj1_90_review_frequency` varchar(100) DEFAULT NULL,
  `overall_obj1_90_review_duration` varchar(100) DEFAULT NULL,
  `overall_obj1_90_review_place` varchar(100) DEFAULT NULL,

  `overall_obj1_180_review` varchar(200) DEFAULT NULL,
  `overall_obj1_180_review_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj1_180_review_frequency` varchar(100) DEFAULT NULL,
  `overall_obj1_180_review_duration` varchar(100) DEFAULT NULL,
  `overall_obj1_180_review_place` varchar(100) DEFAULT NULL,

  `overall_obj1_270_review` varchar(200) DEFAULT NULL,
  `overall_obj1_270_review_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj1_270_review_frequency` varchar(100) DEFAULT NULL,
  `overall_obj1_270_review_duration` varchar(100) DEFAULT NULL,
  `overall_obj1_270_review_place` varchar(100) DEFAULT NULL,


  `overall_obj2` varchar(200) DEFAULT NULL,
  `overall_obj2_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj2_frequency` varchar(100) DEFAULT NULL,
  `overall_obj2_duration` varchar(100) DEFAULT NULL,
  `overall_obj2_place` varchar(100) DEFAULT NULL,

  `overall_obj2_90_review` varchar(200) DEFAULT NULL,
  `overall_obj2_90_review_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj2_90_review_frequency` varchar(100) DEFAULT NULL,
  `overall_obj2_90_review_duration` varchar(100) DEFAULT NULL,
  `overall_obj2_90_review_place` varchar(100) DEFAULT NULL,

  `overall_obj2_180_review` varchar(200) DEFAULT NULL,
  `overall_obj2_180_review_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj2_180_review_frequency` varchar(100) DEFAULT NULL,
  `overall_obj2_180_review_duration` varchar(100) DEFAULT NULL,
  `overall_obj2_180_review_place` varchar(100) DEFAULT NULL,

  `overall_obj2_270_review` varchar(200) DEFAULT NULL,
  `overall_obj2_270_review_task_modality` varchar(100) DEFAULT NULL,  
  `overall_obj2_270_review_frequency` varchar(100) DEFAULT NULL,
  `overall_obj2_270_review_duration` varchar(100) DEFAULT NULL,
  `overall_obj2_270_review_place` varchar(100) DEFAULT NULL,

  `short_term_goal` text DEFAULT NULL,

  `short_term_obj1` varchar(200) DEFAULT NULL,
  `short_term_obj1_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj1_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj1_duration` varchar(100) DEFAULT NULL,
  `short_term_obj1_place` varchar(100) DEFAULT NULL,

  `short_term_obj1_90_review` varchar(200) DEFAULT NULL,
  `short_term_obj1_90_review_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj1_90_review_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj1_90_review_duration` varchar(100) DEFAULT NULL,
  `short_term_obj1_90_review_place` varchar(100) DEFAULT NULL,

  `short_term_obj1_180_review` varchar(200) DEFAULT NULL,
  `short_term_obj1_180_review_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj1_180_review_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj1_180_review_duration` varchar(100) DEFAULT NULL,
  `short_term_obj1_180_review_place` varchar(100) DEFAULT NULL,

  `short_term_obj1_270_review` varchar(200) DEFAULT NULL,
  `short_term_obj1_270_review_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj1_270_review_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj1_270_review_duration` varchar(100) DEFAULT NULL,
  `short_term_obj1_270_review_place` varchar(100) DEFAULT NULL,


  `short_term_obj2` varchar(200) DEFAULT NULL,
  `short_term_obj2_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj2_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj2_duration` varchar(100) DEFAULT NULL,
  `short_term_obj2_place` varchar(100) DEFAULT NULL,

  `short_term_obj2_90_review` varchar(200) DEFAULT NULL,
  `short_term_obj2_90_review_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj2_90_review_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj2_90_review_duration` varchar(100) DEFAULT NULL,
  `short_term_obj2_90_review_place` varchar(100) DEFAULT NULL,

  `short_term_obj2_180_review` varchar(200) DEFAULT NULL,
  `short_term_obj2_180_review_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj2_180_review_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj2_180_review_duration` varchar(100) DEFAULT NULL,
  `short_term_obj2_180_review_place` varchar(100) DEFAULT NULL,

  `short_term_obj2_270_review` varchar(200) DEFAULT NULL,
  `short_term_obj2_270_review_task_modality` varchar(100) DEFAULT NULL,  
  `short_term_obj2_270_review_frequency` varchar(100) DEFAULT NULL,
  `short_term_obj2_270_review_duration` varchar(100) DEFAULT NULL,
  `short_term_obj2_270_review_place` varchar(100) DEFAULT NULL,

  `target_date_goal` varchar(20) DEFAULT NULL,
  `who_responsible` varchar(20) DEFAULT NULL, 
  `who_responsible_other` varchar(100) DEFAULT NULL,  

  `individual_present` varchar(200) DEFAULT NULL,  
  `services_coordinated` varchar(200) DEFAULT NULL, 
  `linkage_service` varchar(200) DEFAULT NULL, 

  `progress_90_review` text DEFAULT NULL,
  `progress_180_review` text DEFAULT NULL,
  `progress_270_review` text DEFAULT NULL,

  `changes_90_review` text DEFAULT NULL,
  `changes_180_review` text DEFAULT NULL,
  `changes_270_review` text DEFAULT NULL,

  `aftercare_plan` text DEFAULT NULL,

  `activity` TINYINT DEFAULT 1 NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;

