<?php
require_once 'config.php';

try {
    $db = db();
    $sql = <<<SQL
    CREATE TABLE IF NOT EXISTS `care_staff` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `full_name` VARCHAR(255) NOT NULL,
        `contact_info` TEXT,
        `ndis_worker_screening_number` VARCHAR(100),
        `ndis_worker_screening_expiry` DATE,
        `first_aid_expiry` DATE,
        `qualifications` TEXT,
        `hourly_rate` DECIMAL(10, 2)
    );
SQL;
    $db->exec($sql);
    echo "Table `care_staff` created successfully." . PHP_EOL;
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
