<?php
require_once 'config.php';

try {
    $db = db();
    $sql = <<<SQL
    CREATE TABLE IF NOT EXISTS `participants` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `full_legal_name` VARCHAR(255) NOT NULL,
        `ndis_participant_number` VARCHAR(50),
        `date_of_birth` DATE,
        `preferred_contact_method` VARCHAR(100),
        `primary_phone` VARCHAR(50),
        `email` VARCHAR(255),
        `address` TEXT,
        `emergency_contact_name` VARCHAR(255),
        `emergency_contact_phone` VARCHAR(50),
        `ndis_plan_start_date` DATE,
        `ndis_plan_end_date` DATE,
        `plan_manager_name` VARCHAR(255),
        `plan_manager_contact` VARCHAR(255),
        `ndis_funding_budget_total` DECIMAL(10, 2),
        `primary_disability` TEXT,
        `support_needs_summary` TEXT,
        `communication_aids_methods` TEXT,
        `behaviours_of_concern` TEXT,
        `risk_assessment_summary` TEXT,
        `safety_plan` TEXT,
        `consent_for_info_sharing` BOOLEAN DEFAULT FALSE
    );
SQL;
    $db->exec($sql);
    echo "Table `participants` created successfully." . PHP_EOL;
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
