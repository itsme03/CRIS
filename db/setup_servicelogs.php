<?php
require_once 'config.php';

try {
    $db = db();
    $sql = <<<SQL
    CREATE TABLE IF NOT EXISTS `service_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `participant_id` INT NOT NULL,
        `worker_id` INT NOT NULL,
        `service_date` DATETIME NOT NULL,
        `ndis_line_item` VARCHAR(255),
        `duration_minutes` INT,
        `service_notes` TEXT,
        `billing_status` ENUM('Pending', 'Billed', 'Paid') DEFAULT 'Pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`participant_id`) REFERENCES `participants`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`worker_id`) REFERENCES `support_workers`(`id`) ON DELETE CASCADE
    );
SQL;
    $db->exec($sql);
    echo "Table `service_logs` created successfully." . PHP_EOL;
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
