<?php
define('BASEPATH', __DIR__);
require_once __DIR__ . '/app/core/Database.php';

$db = Database::getInstance()->getConnection();

try {
    echo "Checking tables...\n";
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('whatsapp_logs', $tables)) {
        $cols = $db->query("SHOW COLUMNS FROM whatsapp_logs")->fetchAll(PDO::FETCH_COLUMN);
        echo "whatsapp_logs columns: " . implode(', ', $cols) . "\n";
        
        if (!in_array('message_type', $cols)) {
            echo "Applying migration...\n";
            // Run SQL directly
            $db->exec("ALTER TABLE `whatsapp_logs` 
                ADD COLUMN `message_type` ENUM('reminder', 'test') DEFAULT 'reminder' AFTER `resident_id`,
                ADD COLUMN `http_code` INT NULL AFTER `status`,
                ADD COLUMN `normalized_phone` VARCHAR(20) NULL AFTER `phone`,
                ADD COLUMN `raw_response` TEXT NULL AFTER `error_message`
            ");
            
            // Re-check for whatsapp_settings columns
            $colsSettings = $db->query("SHOW COLUMNS FROM whatsapp_settings")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('last_run_at', $colsSettings)) {
                $db->exec("ALTER TABLE `whatsapp_settings` ADD COLUMN `last_run_at` DATETIME NULL");
            }
            
            echo "Migration applied successfully.\n";
        } else {
            echo "Migration already applied or columns exist.\n";
        }
    } else {
        echo "Error: whatsapp_logs table not found.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
