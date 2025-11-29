<?php
require_once 'config.php';

try {
    $db = db();
    $sql = <<<SQL
    CREATE TABLE IF NOT EXISTS `bookings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `client_id` INT NOT NULL,
        `staff_id` INT NOT NULL,
        `service_date` DATETIME NOT NULL,
        `ndis_line_item` VARCHAR(255),
        `duration_minutes` INT,
        `service_notes` TEXT,
        `billing_status` ENUM('Pending', 'Billed', 'Paid') DEFAULT 'Pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`staff_id`) REFERENCES `care_staff`(`id`) ON DELETE CASCADE
    );
SQL;
    $db->exec($sql);
    echo "Table `bookings` created successfully." . PHP_EOL;
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
