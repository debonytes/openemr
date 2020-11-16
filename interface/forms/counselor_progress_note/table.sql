--
-- Table structure for table `form_counselor_progress_note`
--

CREATE TABLE IF NOT EXISTS `form_counselor_progress_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date` varchar(100) DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,  
  `encounter` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,  
  `name` varchar(255) DEFAULT NULL,

  `counselor` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `starttime` varchar(255) DEFAULT NULL,
  `endtime` varchar(255) DEFAULT NULL,

  `session_number` varchar(255) DEFAULT NULL,
  `goal_1` varchar(255) DEFAULT NULL,
  `goal_1_answer` varchar(255) DEFAULT NULL,
  `goal_2` varchar(100) DEFAULT NULL,
  `goal_2_answer` varchar(100) DEFAULT NULL,
  `goal_3` varchar(100) DEFAULT NULL,
  `goal_3_answer` varchar(100) DEFAULT NULL,
  `goal_4` varchar(100) DEFAULT NULL,
  `goal_4_answer` varchar(100) DEFAULT NULL,

  `icd_code` varchar(100) DEFAULT NULL,
  `session_type` varchar(100) DEFAULT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `plan_review_90` varchar(255) DEFAULT NULL,
  `plan_review_180` varchar(255) DEFAULT NULL,
  `plan_review_270` varchar(255) DEFAULT NULL,

  `risk_self_harm` text DEFAULT NULL,
  `risk_suicidality` text DEFAULT NULL,
  `risk_homicidality` text DEFAULT NULL,

  `symptoms_orientation` text DEFAULT NULL,
  `symptoms_speech` text DEFAULT NULL,
  `symptoms_mood` text DEFAULT NULL,
  `symptoms_mood_other` varchar(255) DEFAULT NULL,
  `symptoms_thought_content` text DEFAULT NULL,
  `symptoms_thought_content_other` varchar(255) DEFAULT NULL,
  `symptoms_hygiene` text DEFAULT NULL,
  `symptoms_motor` text DEFAULT NULL,
  `symptoms_affect` text DEFAULT NULL,
  `symptoms_affect_other` varchar(255) DEFAULT NULL,
  `symptoms_perception` text DEFAULT NULL,
  `symptoms_thought_process` text DEFAULT NULL,
  `symptoms_other` text DEFAULT NULL,
  `symptoms_other_other` varchar(255) DEFAULT NULL,

  `session_focus` text DEFAULT NULL,

  `meet_again_date` varchar(255) DEFAULT NULL,
  `meet_again_time` varchar(255) DEFAULT NULL,  
  `activity` TINYINT DEFAULT 1 NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;

