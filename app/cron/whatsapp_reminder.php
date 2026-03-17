<?php
/**
 * WhatsApp Aidat Hatırlatma Cron Job Scripti
 * Her gün saat başı çalışacak şekilde kurulabilir.
 */

define('BASEPATH', __DIR__);
date_default_timezone_set('Europe/Istanbul');
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/models/WhatsApp.php';
require_once __DIR__ . '/../../app/services/WhatsAppService.php';

// Mocking session and auth for background tasks if needed
$db = Database::getInstance()->getConnection();

class CronTask {
    protected $db;
    public function __construct($db) { $this->db = $db; }
}
// Note: This structure depends on your autoloader or manual includes. 
// Assuming a simplified direct access for cron.

try {
    $whatsappModel = new WhatsApp($db);
    $settings = $whatsappModel->getSettings();

    if (!$settings['is_active']) {
        exit("WhatsApp sistemi pasif.\n");
    }

    $currentDay = (int)date('d');
    $currentTime = date('H:i');
    $currentDate = date('Y-m-d');
    $scheduledTime = date('H:i', strtotime($settings['send_time']));
    $lastRunDate = $settings['last_run_at'] ? date('Y-m-d', strtotime($settings['last_run_at'])) : '';

    // LOGGING FOR DEBUGGING
    file_put_contents(__DIR__ . '/../../storage/logs/cron_whatsapp.log', date('Y-m-d H:i:s') . " - Day:$currentDay SettingsDay:{$settings['send_day']} Time:$currentTime Scheduled:$scheduledTime LastRunDate:$lastRunDate\n", FILE_APPEND);

    // 1. Gün kontrolü
    if ($currentDay != $settings['send_day']) {
        exit("Bugün gönderim günü değil ({$currentDay} != {$settings['send_day']}).\n");
    }

    // 2. Saat kontrolü (Ayarlanan saatten sonra mı?)
    if ($currentTime < $scheduledTime) {
        exit("Gönderim saati gelmedi ({$currentTime} < {$scheduledTime}).\n");
    }

    // 3. Bugün zaten çalıştı mı kontrolü
    if ($lastRunDate == $currentDate) {
        // Eğer manuel olarak zorlamak isterseniz burayı bypass edebilirsiniz.
        exit("Bugün için gönderim kontrolü zaten yapıldı.\n");
    }

    // Son çalışma zamanını güncelle (yarım kalırsa en azından o dakika tekrar basmasın)
    $whatsappModel->updateLastRunAt();

    $year = date('Y');
    $month = date('m');
    $period = "$year-$month";

    $unpaidResidents = $whatsappModel->getUnpaidResidents($year, $month);

    echo count($unpaidResidents) . " borçlu sakin bulundu.\n";

    foreach ($unpaidResidents as $resident) {
        // 4. Zaten başarılı bir hatırlatma gönderilmiş mi kontrol et (resident_id + period + status=success)
        if ($whatsappModel->hasSentReminder($resident['resident_id'], $period)) {
            echo "Atlanıyor (Zaten gönderildi): {$resident['name']}\n";
            continue;
        }

        $debt = ($resident['resident_type'] == 'tenant') ? $resident['debt_tenant'] : $resident['debt_owner'];
        
        // 5. Borç sıfır veya altındaysa gönderme
        if ($debt <= 0) {
            continue;
        }

        $settings['message_template_data'] = [
            'name' => $resident['name'],
            'door_number' => $resident['door_number'],
            'amount' => $debt,
            'period' => "$month/$year",
            'due_date' => date('t.m.Y', strtotime("$year-$month-01"))
        ];

        // Meta Cloud API uses templates for reminders
        $result = WhatsAppService::sendMessage($resident['phone'], '', $settings, 'reminder');

        $logData = [
            'resident_id' => $resident['resident_id'],
            'message_type' => 'reminder',
            'phone' => $resident['phone'],
            'normalized_phone' => $result['normalized_phone'] ?? null,
            'message' => "Template: " . $settings['template_name'],
            'period' => $period,
            'status' => $result['status'],
            'http_code' => $result['http_code'] ?? null,
            'error_message' => $result['error_message'] ?? null,
            'raw_response' => $result['raw_response'] ?? null
        ];

        $whatsappModel->logMessage($logData);
        echo "İşlem: {$resident['name']} ({$result['status']})\n";
    }

    echo "İşlem tamamlandı.\n";

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/../../storage/logs/whatsapp_error.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo "Hata: " . $e->getMessage() . "\n";
}
