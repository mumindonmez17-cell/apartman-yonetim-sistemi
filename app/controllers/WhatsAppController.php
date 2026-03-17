<?php

require_once __DIR__ . '/../services/WhatsAppService.php';

class WhatsAppController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $whatsappModel = $this->model('WhatsApp');
        $data['settings'] = $whatsappModel->getSettings();
        $data['logs'] = $whatsappModel->getLogs();
        $data['title'] = 'WhatsApp Ayarları';
        $this->view('whatsapp/index', $data);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'send_day' => $_POST['send_day'],
                'send_time' => $_POST['send_time'],
                'message_template' => $_POST['message_template'],
                'meta_access_token' => $_POST['meta_access_token'],
                'meta_phone_number_id' => $_POST['meta_phone_number_id'],
                'meta_waba_id' => $_POST['meta_waba_id'],
                'webhook_verify_token' => $_POST['webhook_verify_token'],
                'template_name' => $_POST['template_name'],
                'language_code' => $_POST['language_code']
            ];

            $whatsappModel = $this->model('WhatsApp');
            if ($whatsappModel->updateSettings($data)) {
                $_SESSION['success'] = 'Meta WhatsApp ayarları başarıyla güncellendi.';
            } else {
                $_SESSION['error'] = 'Ayarlar güncellenirken bir hata oluştu.';
            }
        }
        $this->redirect('whatsapp');
    }

    public function test() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $whatsappModel = $this->model('WhatsApp');
            $residentModel = $this->model('Resident');
            
            // Test için bir sakin seçelim
            $residents = $residentModel->getAll();
            if (empty($residents)) {
                echo json_encode(['status' => 'error', 'message' => 'Sistemde kayıtlı site sakini bulunamadı.']);
                return;
            }

            $resident = $residents[0];
            $settings = $whatsappModel->getSettings();
            
            $testMessage = "Merhaba " . $resident['name'] . ", bu bir Meta WhatsApp Cloud API test mesajıdır.";
            
            // Meta Text Message Test
            $result = WhatsAppService::sendTextMessage($resident['phone'], $testMessage, $settings);

            // Log the test message
            $whatsappModel->logMessage([
                'resident_id' => $resident['id'],
                'message_type' => 'test',
                'phone' => $resident['phone'],
                'normalized_phone' => $result['normalized_phone'] ?? null,
                'message' => $testMessage,
                'period' => 'TEST-' . date('Ym'),
                'status' => $result['status'],
                'http_code' => $result['http_code'] ?? null,
                'error_message' => $result['error_message'] ?? null,
                'raw_response' => $result['raw_response'] ?? null
            ]);

            if ($result['status'] === 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Meta test mesajı başarıyla gönderildi: ' . ($result['normalized_phone'] ?? $resident['phone'])]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Meta mesaj gönderilemedi: ' . ($result['error_message'] ?? 'Bilinmeyen hata')]);
            }
        }
    }

    public function webhook() {
        // Meta Webhook Verification (GET)
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $whatsappModel = $this->model('WhatsApp');
            $settings = $whatsappModel->getSettings();
            
            $mode = $_GET['hub_mode'] ?? '';
            $token = $_GET['hub_verify_token'] ?? '';
            $challenge = $_GET['hub_challenge'] ?? '';
            
            if ($mode === 'subscribe' && $token === $settings['webhook_verify_token']) {
                echo $challenge;
                exit;
            }
            http_response_code(403);
            exit;
        }

        // Meta Webhook Status Updates (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (isset($data['entry'][0]['changes'][0]['value']['statuses'][0])) {
                $status = $data['entry'][0]['changes'][0]['value']['statuses'][0];
                $messageId = $status['id'];
                $statusStr = $status['status']; // delivered, read, failed
                
                // İsterseniz burada whatsapp_logs tablosundaki durumu güncelleyebilirsiniz.
                // Şimdilik sadece debug için loglayalım
                file_put_contents(__DIR__ . '/../../webhook_debug.log', date('Y-m-d H:i:s') . " - ID:$messageId Status:$statusStr\n", FILE_APPEND);
            }
            
            http_response_code(200);
            exit;
        }
    }
}
