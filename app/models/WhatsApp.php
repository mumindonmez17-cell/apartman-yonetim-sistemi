<?php

class WhatsApp extends Model {
    public function getSettings() {
        return $this->db->query("SELECT * FROM whatsapp_settings WHERE id = 1")->fetch();
    }

    public function updateSettings($data) {
        $stmt = $this->db->prepare("UPDATE whatsapp_settings SET is_active = ?, send_day = ?, send_time = ?, message_template = ?, meta_access_token = ?, meta_phone_number_id = ?, meta_waba_id = ?, webhook_verify_token = ?, template_name = ?, language_code = ? WHERE id = 1");
        return $stmt->execute([
            $data['is_active'],
            $data['send_day'],
            $data['send_time'],
            $data['message_template'],
            $data['meta_access_token'],
            $data['meta_phone_number_id'],
            $data['meta_waba_id'],
            $data['webhook_verify_token'],
            $data['template_name'],
            $data['language_code']
        ]);
    }

    public function logMessage($data) {
        $stmt = $this->db->prepare("INSERT INTO whatsapp_logs (resident_id, message_type, phone, normalized_phone, message, period, status, http_code, error_message, raw_response) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['resident_id'],
            $data['message_type'] ?? 'reminder',
            $data['phone'],
            $data['normalized_phone'] ?? null,
            $data['message'],
            $data['period'],
            $data['status'],
            $data['http_code'] ?? null,
            $data['error_message'] ?? null,
            $data['raw_response'] ?? null
        ]);
    }

    public function hasSentReminder($residentId, $period) {
        $stmt = $this->db->prepare("SELECT id FROM whatsapp_logs WHERE resident_id = ? AND period = ? AND status = 'success' AND message_type = 'reminder' LIMIT 1");
        $stmt->execute([$residentId, $period]);
        return $stmt->fetch();
    }

    public function updateLastRunAt() {
        return $this->db->query("UPDATE whatsapp_settings SET last_run_at = NOW() WHERE id = 1");
    }

    public function getLogs($limit = 100) {
        return $this->db->query("SELECT wl.*, r.name as resident_name 
                                FROM whatsapp_logs wl 
                                LEFT JOIN residents r ON wl.resident_id = r.id 
                                ORDER BY wl.sent_at DESC 
                                LIMIT " . (int)$limit)->fetchAll();
    }

    public function checkAlreadySent($resident_id, $period) {
        return $this->hasSentReminder($resident_id, $period);
    }

    public function getUnpaidResidents($year, $month) {
        // Bu sorgu, belirli bir ay için aidatı ödenmemiş sakinleri bulur.
        $sql = "SELECT r.id as resident_id, r.name, r.phone, a.door_number, b.block_name, 
                       da.tenant_amount, da.owner_amount, da.paid_tenant, da.paid_owner,
                       (da.tenant_amount - da.paid_tenant) as debt_tenant,
                       (da.owner_amount - da.paid_owner) as debt_owner,
                       r.resident_type
                FROM residents r
                JOIN apartments a ON r.apartment_id = a.id
                JOIN blocks b ON a.block_id = b.id
                JOIN dues_assignments da ON a.id = da.apartment_id
                JOIN dues d ON da.due_id = d.id
                WHERE d.year = ? AND d.month = ?
                AND (
                    (r.resident_type = 'tenant' AND da.tenant_amount > da.paid_tenant)
                    OR 
                    (r.resident_type = 'owner' AND da.owner_amount > da.paid_owner)
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$year, $month]);
        return $stmt->fetchAll();
    }
}
