-- ksf_FA_CodeMaster: common code master table
-- All ref codes across modules live here, namespaced by code_type.
-- Modules register their own code_type values (e.g. 'hrm.pay_grade', 'hrm.event_code',
-- 'crm.lead_status', etc.) and administer codes through this shared table.

CREATE TABLE IF NOT EXISTS `0_ksf_code_master` (
  `id`            INT(11) NOT NULL AUTO_INCREMENT,
  `code_type`     VARCHAR(50)  NOT NULL DEFAULT '' COMMENT 'Namespaced code type (e.g., hrm.pay_grade, hrm.event_code)',
  `code_value`    VARCHAR(50)  NOT NULL DEFAULT '' COMMENT 'The ref code itself (e.g., S5, OT, REG)',
  `code_name`     VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Human-readable name',
  `code_description` TEXT COMMENT 'Description of what this code means',
  `usage_instructions` TEXT COMMENT 'How/when to use this code',
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1 COMMENT 'Whether this code can be used',
  `sort_order`    INT(11)      NOT NULL DEFAULT 0 COMMENT 'Display sort order within type',
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code_type_value` (`code_type`, `code_value`),
  KEY `idx_code_type` (`code_type`),
  KEY `idx_code_value` (`code_value`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed common HRM event codes (replaces hardcoded INSERT in ksf_FA_HRM_TimeTracking)
INSERT IGNORE INTO `0_ksf_code_master` (`code_type`, `code_value`, `code_name`, `code_description`, `usage_instructions`, `is_active`, `sort_order`) VALUES
('hrm.event_code', 'REG', 'Regular Time', 'Standard work hours at base rate', 'Use for normal scheduled work hours', 1, 1),
('hrm.event_code', 'OT', 'Overtime', 'Overtime hours at 1.5x base rate', 'Use for hours beyond standard schedule requiring overtime pay', 1, 2),
('hrm.event_code', 'OT2', 'Double Overtime', 'Overtime hours at 2.0x base rate', 'Use for hours requiring double-time pay', 1, 3),
('hrm.event_code', 'HOL', 'Holiday', 'Holiday work at 2.0x base rate', 'Use for work performed on designated holidays', 1, 4),
('hrm.event_code', 'SICK', 'Sick Leave', 'Sick leave (paid from salary)', 'Use when employee is absent due to illness', 1, 5),
('hrm.event_code', 'VAC', 'Vacation', 'Vacation leave (paid from salary)', 'Use for approved vacation time off', 1, 6),
('hrm.event_code', 'COMM', 'Commission', 'Commission-based earnings', 'Use for commission earnings, not hourly-based', 1, 7),
('hrm.event_code', 'BEREAVE', 'Bereavement', 'Bereavement leave (paid)', 'Use for bereavement leave', 1, 8),
('hrm.event_code', 'JURY', 'Jury Duty', 'Jury duty (paid)', 'Use for jury duty service', 1, 9);

-- Seed common pay grade codes
INSERT IGNORE INTO `0_ksf_code_master` (`code_type`, `code_value`, `code_name`, `code_description`, `usage_instructions`, `is_active`, `sort_order`) VALUES
('hrm.pay_grade', 'E1', 'Entry Level', 'Entry-level professional grade', 'Grade for new hires with limited experience', 1, 1),
('hrm.pay_grade', 'E2', 'Experienced', 'Experienced professional grade', 'Grade for employees with solid experience', 1, 2),
('hrm.pay_grade', 'S3', 'Senior', 'Senior professional grade', 'Grade for senior individual contributors', 1, 3),
('hrm.pay_grade', 'S5', 'Lead', 'Lead/principal grade', 'Grade for technical leads and principals', 1, 4),
('hrm.pay_grade', 'M3', 'Manager', 'Manager grade', 'Grade for people managers', 1, 5),
('hrm.pay_grade', 'D1', 'Director', 'Director grade', 'Grade for directors', 1, 6);
