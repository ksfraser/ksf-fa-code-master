-- ksf_FA_CodeMaster: common code master table
-- All ref codes across modules live here, namespaced by code_type.
-- Modules register their own code_type values (e.g. 'hrm.pay_grade', 'hrm.activity_code',
-- 'hrm.event_code', 'crm.lead_status', etc.) and administer codes through this shared table.

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

-- Seed common HRM activity codes (replaces hardcoded INSERT in ksf_FA_HRM_TimeTracking)
INSERT IGNORE INTO `0_ksf_code_master` (`code_type`, `code_value`, `code_name`, `code_description`, `usage_instructions`, `is_active`, `sort_order`) VALUES
('hrm.activity_code', 'REG', 'Regular Time', 'Standard work hours at base rate', 'Use for normal scheduled work hours', 1, 1),
('hrm.activity_code', 'MEET', 'Meeting', 'Meeting attendance', 'Use for internal or external meetings', 1, 2),
('hrm.activity_code', 'TRNG', 'Training', 'Training and skill development', 'Use for training sessions, workshops, courses', 1, 3),
('hrm.activity_code', 'ADMIN', 'Administrative', 'Administrative tasks', 'Use for administrative work not tied to a specific project activity', 1, 4),
('hrm.activity_code', 'CODING', 'Software Development/Coding', 'Software development and coding', 'Use for writing code, implementation', 1, 5),
('hrm.activity_code', 'DESIGN', 'Software Design', 'Software design and architecture', 'Use for system design, architecture, planning', 1, 6),
('hrm.activity_code', 'TEST', 'Testing/QA', 'Testing and quality assurance', 'Use for test execution, QA, bug verification', 1, 7),
('hrm.activity_code', 'CODE_REVIEW', 'Code Review', 'Code review and peer review', 'Use for reviewing code changes', 1, 8),
('hrm.activity_code', 'DEBUG', 'Debugging', 'Debugging and troubleshooting', 'Use for bug investigation and fixing', 1, 9),
('hrm.activity_code', 'DOCUMENT', 'Documentation', 'Documentation and technical writing', 'Use for writing docs, specs, comments', 1, 10),
('hrm.activity_code', 'DEPLOY', 'Deployment/Release', 'Deployment and release activities', 'Use for deploying code, releases', 1, 11);

-- Seed common HRM bank types (used by ksf_FA_HRMBanks for PTO, VAC, STAT, SICK,
-- education allowance, travel allowance, extended health care, etc.)
INSERT IGNORE INTO `0_ksf_code_master` (`code_type`, `code_value`, `code_name`, `code_description`, `usage_instructions`, `is_active`, `sort_order`) VALUES
('hrm.bank_type', 'PTO', 'Paid Time Off', 'General paid time off bank', 'Use for general paid time off', 1, 1),
('hrm.bank_type', 'VAC', 'Vacation', 'Vacation leave bank', 'Use for approved vacation time off', 1, 2),
('hrm.bank_type', 'STAT', 'Statutory Holiday', 'Statutory holiday bank', 'Use for statutory holiday time off', 1, 3),
('hrm.bank_type', 'SICK', 'Sick Leave', 'Sick leave bank', 'Use for sick leave', 1, 4),
('hrm.bank_type', 'EDU', 'Education Allowance', 'Education/training allowance bank', 'Use for education and training allowances', 1, 5),
('hrm.bank_type', 'TRV', 'Travel Allowance', 'Travel allowance bank', 'Use for travel allowances', 1, 6),
('hrm.bank_type', 'EHC', 'Extended Health Care', 'Extended health care allowance bank', 'Use for extended health care allowances', 1, 7);

-- Seed common bank transaction types
INSERT IGNORE INTO `0_ksf_code_master` (`code_type`, `code_value`, `code_name`, `code_description`, `usage_instructions`, `is_active`, `sort_order`) VALUES
('hrm.bank_transaction_type', 'ACCRUE', 'Accrue', 'Accrue balance to bank', 'Use when accruing balance to the bank', 1, 1),
('hrm.bank_transaction_type', 'USE', 'Use', 'Use balance from bank', 'Use when using balance from the bank', 1, 2),
('hrm.bank_transaction_type', 'ADJUST', 'Adjustment', 'Manual adjustment', 'Use for manual adjustments', 1, 3),
('hrm.bank_transaction_type', 'EXPIRE', 'Expire', 'Expired balance', 'Use when balance expires', 1, 4);

-- Seed common pay grade codes
INSERT IGNORE INTO `0_ksf_code_master` (`code_type`, `code_value`, `code_name`, `code_description`, `usage_instructions`, `is_active`, `sort_order`) VALUES
('hrm.pay_grade', 'E1', 'Entry Level', 'Entry-level professional grade', 'Grade for new hires with limited experience', 1, 1),
('hrm.pay_grade', 'E2', 'Experienced', 'Experienced professional grade', 'Grade for employees with solid experience', 1, 2),
('hrm.pay_grade', 'S3', 'Senior', 'Senior professional grade', 'Grade for senior individual contributors', 1, 3),
('hrm.pay_grade', 'S5', 'Lead', 'Lead/principal grade', 'Grade for technical leads and principals', 1, 4),
('hrm.pay_grade', 'M3', 'Manager', 'Manager grade', 'Grade for people managers', 1, 5),
('hrm.pay_grade', 'D1', 'Director', 'Director grade', 'Grade for directors', 1, 6);
