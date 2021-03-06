--
-- Table structure for table `form_counselor_comprehensive_assessment`
--

CREATE TABLE IF NOT EXISTS `form_counselor_comprehensive_assessment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date` varchar(100) DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,  
  `encounter` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,  
  `name` varchar(255) DEFAULT NULL,
  `type_cda` varchar(100) DEFAULT NULL,
  `cda_date` varchar(100) DEFAULT NULL,
  `medicaid` varchar(100) DEFAULT NULL,
  `age` varchar(10) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `ethnicity` varchar(100) DEFAULT NULL,
  `ethnicity_other` varchar(200) DEFAULT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `examiner` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `marital_status` varchar(20) DEFAULT NULL,
  `referral_source` varchar(100) DEFAULT NULL,
  `participation_status` varchar(100) DEFAULT NULL,
  `behave_treatment_history` text DEFAULT NULL,
  `behave_family_history` text DEFAULT NULL,
  `history_abuse` text DEFAULT NULL,
  `agency_past_year` varchar(100) DEFAULT NULL,
  `agency_past_year_place` varchar(255) DEFAULT NULL,
  `devt_history` varchar(100) DEFAULT NULL,
  `devt_history_details` text DEFAULT NULL,
  `adult_sexual_behavior` text DEFAULT NULL,
  `child_gadget_hours` varchar(100) DEFAULT NULL,
  `recipient_report` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `allergies_medication` text DEFAULT NULL,
  `family_medical_history` text DEFAULT NULL,
  `infectious_diseases` text DEFAULT NULL,
  `medication_history` text DEFAULT NULL,
  `name_physician` varchar(200) DEFAULT NULL,
  `obtain_medical_provider` varchar(100) DEFAULT NULL,
  `name_current_psychiatrist` varchar(150) DEFAULT NULL,
  `name_past_psychiatrist` varchar(150) DEFAULT NULL,
  `current_symptoms` text DEFAULT NULL,
  `cadic_completed` varchar(100) DEFAULT NULL,
  `cadic_completed_details` varchar(255) DEFAULT NULL,
  `child_depression` varchar(100) DEFAULT NULL,
  `child_depression_details` text DEFAULT NULL,
  `current_suicidal_ideation` varchar(100) DEFAULT NULL,
  `current_suicidal_ideation_details` text DEFAULT NULL,
  `current_homicidal_ideation` varchar(100) DEFAULT NULL,
  `current_homicidal_ideation_details` text DEFAULT NULL,
  `history_suicidal_attempts` text DEFAULT NULL,
  `safety_plan` text DEFAULT NULL,
  `legal_history` text DEFAULT NULL,
  `client_family_support` varchar(100) DEFAULT NULL,
  `client_lives_with` varchar(255) DEFAULT NULL,
  `adult_grew_with` varchar(255) DEFAULT NULL,
  `client_childhood` varchar(255) DEFAULT NULL,
  `childhood_punishment` varchar(255) DEFAULT NULL,
  `other_family_info` text DEFAULT NULL,
  `religious_preference` varchar(255) DEFAULT NULL,
  `assess_spiritual` text DEFAULT NULL,
  `assess_vocational` text DEFAULT NULL,
  `assess_cultural` text DEFAULT NULL,
  `assess_social` text DEFAULT NULL,
  `strength_identify` varchar(100) DEFAULT NULL,
  `client_readiness` varchar(100) DEFAULT NULL,
  `client_identify_evidence` text DEFAULT NULL,
  `client_struggle_evidence` text DEFAULT NULL,
  `activities_readiness` text DEFAULT NULL,
  `illegal_drug_current` varchar(10) DEFAULT NULL,
  `illegal_drug_past_12` varchar(10) DEFAULT NULL,
  `illegal_drug_lifetime` varchar(10) DEFAULT NULL,
  `prescriptive_drug_current` varchar(10) DEFAULT NULL,
  `prescriptive_drug_past_12` varchar(10) DEFAULT NULL,
  `prescriptive_drug_lifetime` varchar(10) DEFAULT NULL,
  `alcohol_drug_current` varchar(10) DEFAULT NULL,
  `alcohol_drug_past_12` varchar(10) DEFAULT NULL,
  `alcohol_drug_lifetime` varchar(10) DEFAULT NULL,

  `caffeine_age_first` varchar(10) DEFAULT NULL,
  `caffeine_age_last` varchar(10) DEFAULT NULL,
  `caffeine_frequency` varchar(100) DEFAULT NULL,
  `caffeine_amount` varchar(100) DEFAULT NULL,
  `caffeine_method` varchar(100) DEFAULT NULL,

  `tobacco_age_first` varchar(10) DEFAULT NULL,
  `tobacco_age_last` varchar(10) DEFAULT NULL,
  `tobacco_frequency` varchar(100) DEFAULT NULL,
  `tobacco_amount` varchar(100) DEFAULT NULL,
  `tobacco_method` varchar(100) DEFAULT NULL,

  `alcohol_age_first` varchar(10) DEFAULT NULL,
  `alcohol_age_last` varchar(10) DEFAULT NULL,
  `alcohol_frequency` varchar(100) DEFAULT NULL,
  `alcohol_amount` varchar(100) DEFAULT NULL,
  `alcohol_method` varchar(100) DEFAULT NULL,

  `prescription_drugs` varchar(255) DEFAULT NULL,
  `prescription_age_first` varchar(10) DEFAULT NULL,
  `prescription_age_last` varchar(10) DEFAULT NULL,
  `prescription_frequency` varchar(100) DEFAULT NULL,
  `prescription_amount` varchar(100) DEFAULT NULL,
  `prescription_method` varchar(100) DEFAULT NULL,


  `other1_age_first` varchar(10) DEFAULT NULL,
  `other1_age_last` varchar(10) DEFAULT NULL,
  `other1_frequency` varchar(100) DEFAULT NULL,
  `other1_amount` varchar(100) DEFAULT NULL,
  `other1_method` varchar(100) DEFAULT NULL,

  `other2_age_first` varchar(10) DEFAULT NULL,
  `other2_age_last` varchar(10) DEFAULT NULL,
  `other2_frequency` varchar(100) DEFAULT NULL,
  `other2_amount` varchar(100) DEFAULT NULL,
  `other2_method` varchar(100) DEFAULT NULL,

  `other3_age_first` varchar(10) DEFAULT NULL,
  `other3_age_last` varchar(10) DEFAULT NULL,
  `other3_frequency` varchar(100) DEFAULT NULL,
  `other3_amount` varchar(100) DEFAULT NULL,
  `other3_method` varchar(100) DEFAULT NULL,

  `referral_comm_services` text DEFAULT NULL,

  `appearance_weight` text DEFAULT NULL,
  `appearance_weight_other` text DEFAULT NULL,
  `appearance_hygiene` text DEFAULT NULL,
  `appearance_hygiene_other` text DEFAULT NULL,
  `appearance_dress` varchar(10) DEFAULT NULL,
  `appearance_dress_other` text DEFAULT NULL,

  `speech_rate` text DEFAULT NULL,
  `speech_volume` text DEFAULT NULL,
  `motor_activity` text DEFAULT NULL,
  `eye_contact` text DEFAULT NULL,
  `cooperativeness` text DEFAULT NULL,
  `cooperativeness_other` varchar(200) DEFAULT NULL,

  `thought_process` text DEFAULT NULL,
  `thought_content` text DEFAULT NULL,
  `thought_perception` text DEFAULT NULL,
  `thought_perception_other` varchar(200) DEFAULT NULL,

  `mood` text DEFAULT NULL,
  `affect` text DEFAULT NULL,
  `orientation` text DEFAULT NULL,
  `memory_immediate` text DEFAULT NULL,
  `memory_recent` text DEFAULT NULL,
  `memory_remote` text DEFAULT NULL,

  `insight` text DEFAULT NULL,
  `insight_awareness_symptoms` text DEFAULT NULL,
  `insight_awareness_need` text DEFAULT NULL,

  `vegetative_appetite` varchar(50) DEFAULT NULL,
  `vegetative_sleep` varchar(50) DEFAULT NULL,
  `vegetative_concentration` varchar(50) DEFAULT NULL,

  `dsm_5_code` text DEFAULT NULL,
  `dsm_5_code_disorder` text DEFAULT NULL,
  `dsm_5_type_education` text DEFAULT NULL,

  `service_type_1` varchar(150) DEFAULT NULL,
  `service_type_1_intencity` varchar(25) DEFAULT NULL,
  `service_type_1_intencity_other` varchar(100) DEFAULT NULL,
  `service_type_1_frequency` varchar(25) DEFAULT NULL,
  `service_type_1_frequency_other` varchar(100) DEFAULT NULL,
  `service_type_1_duration` varchar(25) DEFAULT NULL,
  `service_type_1_duration_other` varchar(100) DEFAULT NULL,

  `service_type_2` varchar(150) DEFAULT NULL,
  `service_type_2_intencity` varchar(25) DEFAULT NULL,
  `service_type_2_intencity_other` varchar(100) DEFAULT NULL,
  `service_type_2_frequency` varchar(25) DEFAULT NULL,
  `service_type_2_frequency_other` varchar(100) DEFAULT NULL,
  `service_type_2_duration` varchar(25) DEFAULT NULL,
  `service_type_2_duration_other` varchar(100) DEFAULT NULL,

  `service_type_3` varchar(150) DEFAULT NULL,
  `service_type_3_intencity` varchar(25) DEFAULT NULL,
  `service_type_3_intencity_other` varchar(100) DEFAULT NULL,
  `service_type_3_frequency` varchar(25) DEFAULT NULL,
  `service_type_3_frequency_other` varchar(100) DEFAULT NULL,
  `service_type_3_duration` varchar(25) DEFAULT NULL,
  `service_type_3_duration_other` varchar(100) DEFAULT NULL,

  `service_type_4` varchar(150) DEFAULT NULL,
  `service_type_4_intencity` varchar(25) DEFAULT NULL,
  `service_type_4_intencity_other` varchar(100) DEFAULT NULL,
  `service_type_4_frequency` varchar(25) DEFAULT NULL,
  `service_type_4_frequency_other` varchar(100) DEFAULT NULL,
  `service_type_4_duration` varchar(25) DEFAULT NULL,
  `service_type_4_duration_other` varchar(100) DEFAULT NULL,


  `service_type_5` varchar(150) DEFAULT NULL,
  `service_type_5_intencity` varchar(20) DEFAULT NULL,
  `service_type_5_intencity_other` varchar(100) DEFAULT NULL,
  `service_type_5_frequency` varchar(20) DEFAULT NULL,
  `service_type_5_frequency_other` varchar(100) DEFAULT NULL,
  `service_type_5_duration` varchar(20) DEFAULT NULL,
  `service_type_5_duration_other` varchar(100) DEFAULT NULL,

  `service_type_6` varchar(150) DEFAULT NULL,
  `service_type_6_intencity` varchar(20) DEFAULT NULL,
  `service_type_6_intencity_other` varchar(100) DEFAULT NULL,
  `service_type_6_frequency` varchar(25) DEFAULT NULL,
  `service_type_6_frequency_other` varchar(100) DEFAULT NULL,
  `service_type_6_duration` varchar(25) DEFAULT NULL,
  `service_type_6_duration_other` varchar(100) DEFAULT NULL,

  `service_criteria_1` varchar(150) DEFAULT NULL,
  `service_criteria_1_consequence` text DEFAULT NULL,
  `service_criteria_2` varchar(150) DEFAULT NULL,
  `service_criteria_2_consequence` text DEFAULT NULL,
  `service_criteria_3` varchar(150) DEFAULT NULL,
  `service_criteria_3_consequence` text DEFAULT NULL,
  `service_criteria_4` varchar(150) DEFAULT NULL,
  `service_criteria_4_consequence` text DEFAULT NULL,
  `service_criteria_5` varchar(150) DEFAULT NULL,
  `service_criteria_5_consequence` text DEFAULT NULL,
  `service_criteria_6` varchar(150) DEFAULT NULL,
  `service_criteria_6_consequence` text DEFAULT NULL,

  `annual_assessment` text DEFAULT NULL,

  `name_examiner` varchar(150) DEFAULT NULL,
  `date_examine` varchar(20) DEFAULT NULL, 

  `activity` TINYINT DEFAULT 1 NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;

