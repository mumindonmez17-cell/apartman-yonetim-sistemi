<?php

class WhatsAppService {
    /**
     * Telefon numarasını normalize eder.
     * Kurallar: 
     * - Boşluk, parantez, tire, + temizle
     * - 0 ile başlıyorsa 90 ekle (0'ı kaldır)
     * - 5 ile başlıyorsa 90 ekle
     * - 90 ile başlıyorsa dokunma
     */
    /**
     * Telefon numarasını normalize eder (Meta için pure digits).
     */
    public static function formatPhoneNumber($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strpos($phone, '0') === 0) {
            $phone = '90' . substr($phone, 1);
        } elseif (strpos($phone, '5') === 0 && strlen($phone) == 10) {
            $phone = '90' . $phone;
        }
        
        return $phone;
    }

    public static function validateSettings($settings) {
        if (empty($settings['meta_access_token'])) return "Meta Access Token eksik.";
        if (empty($settings['meta_phone_number_id'])) return "Meta Phone Number ID eksik.";
        if (!$settings['is_active']) return "WhatsApp sistemi pasif.";
        return true;
    }

    public static function validateMessage($phone, $message) {
        if (empty($phone) || strlen($phone) < 10) return "Geçersiz telefon numarası.";
        if (empty($message)) return "Mesaj içeriği boş.";
        return true;
    }

    public static function formatMessage($template, $data) {
        require_once __DIR__ . '/../models/SiteSettings.php';
        $siteSettings = (new SiteSettings())->getSettings();
        $siteName = $siteSettings['site_name'] ?? 'Apartman Yönetim Sistemi';

        $placeholders = [
            '{ad_soyad}' => $data['name'] ?? '',
            '{daire_no}' => $data['door_number'] ?? '',
            '{borc}' => number_format($data['amount'], 2, ',', '.') ?? '0,00',
            '{donem}' => $data['period'] ?? '',
            '{site_adi}' => $siteName,
            '{son_odeme_tarihi}' => $data['due_date'] ?? date('t.m.Y')
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Meta Cloud API üzerinden mesaj gönderir.
     */
    public static function sendMessage($phone, $message, $settings, $type = 'reminder') {
        if ($type === 'test') {
            return self::sendTextMessage($phone, $message, $settings);
        } else {
            require_once __DIR__ . '/../models/SiteSettings.php';
            $siteSettings = (new SiteSettings())->getSettings();
            $siteName = $siteSettings['site_name'] ?? 'Apartman Yönetim Sistemi';

            // Hatırlatma mesajları template üzerinden gider
            return self::sendTemplateMessage($phone, $settings, [
                $settings['message_template_data']['name'] ?? '',
                $siteName,
                $settings['message_template_data']['period'] ?? '',
                number_format($settings['message_template_data']['amount'] ?? 0, 2, ',', '.'),
                $settings['message_template_data']['due_date'] ?? '',
                $settings['message_template_data']['door_number'] ?? ''
            ]);
        }
    }

    /**
     * Text mesajı gönderir (Test için).
     */
    public static function sendTextMessage($phone, $message, $settings) {
        $normalizedPhone = self::formatPhoneNumber($phone);
        $apiUrl = "https://graph.facebook.com/v23.0/" . $settings['meta_phone_number_id'] . "/messages";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $normalizedPhone,
            'type' => 'text',
            'text' => ['body' => $message]
        ];

        return self::executeCurl($apiUrl, $payload, $settings['meta_access_token'], $normalizedPhone);
    }

    /**
     * Template mesajı gönderir (Otomatik hatırlatma için).
     */
    public static function sendTemplateMessage($phone, $settings, $params = []) {
        $normalizedPhone = self::formatPhoneNumber($phone);
        $apiUrl = "https://graph.facebook.com/v23.0/" . $settings['meta_phone_number_id'] . "/messages";
        
        $components = [];
        if (!empty($params)) {
            $parameters = [];
            foreach ($params as $param) {
                $parameters[] = ['type' => 'text', 'text' => (string)$param];
            }
            $components[] = [
                'type' => 'body',
                'parameters' => $parameters
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $normalizedPhone,
            'type' => 'template',
            'template' => [
                'name' => $settings['template_name'],
                'language' => ['code' => $settings['language_code'] ?? 'tr'],
                'components' => $components
            ]
        ];

        return self::executeCurl($apiUrl, $payload, $settings['meta_access_token'], $normalizedPhone);
    }

    private static function executeCurl($url, $payload, $token, $normalizedPhone) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);

        $logData = "Request: " . json_encode($payload) . " | Response: " . $response;

        if ($err) {
            return [
                'status' => 'failed',
                'http_code' => $httpCode,
                'error_message' => "cURL Error: " . $err,
                'normalized_phone' => $normalizedPhone,
                'raw_response' => $logData
            ];
        }

        $resData = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($resData['messages'][0]['id'])) {
            return [
                'status' => 'success',
                'http_code' => $httpCode,
                'message_id' => $resData['messages'][0]['id'],
                'normalized_phone' => $normalizedPhone,
                'raw_response' => $logData
            ];
        } else {
            $errorMsg = $resData['error']['message'] ?? 'Bilinmeyen Meta Hatası';
            return [
                'status' => 'failed',
                'http_code' => $httpCode,
                'error_message' => $errorMsg,
                'normalized_phone' => $normalizedPhone,
                'raw_response' => $logData
            ];
        }
    }
}
