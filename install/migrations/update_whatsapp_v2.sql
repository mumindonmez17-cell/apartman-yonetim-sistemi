-- WhatsApp Modülü Production Güncellemesi
-- whatsapp_logs tablosunu genişlet
ALTER TABLE `whatsapp_logs` 
ADD COLUMN `message_type` ENUM('reminder', 'test') DEFAULT 'reminder' AFTER `resident_id`,
ADD COLUMN `http_code` INT NULL AFTER `status`,
ADD COLUMN `normalized_phone` VARCHAR(20) NULL AFTER `phone`,
ADD COLUMN `raw_response` TEXT NULL AFTER `error_message`;

-- whatsapp_settings tablosuna son çalışma zamanı ekle
ALTER TABLE `whatsapp_settings` 
ADD COLUMN `last_run_at` DATETIME NULL;
