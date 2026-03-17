<?php

class Controller {
    public function __construct() {
        // Validate CSRF token for all POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die("HATA: CSRF doğrulaması başarısız! Lütfen sayfayı yenileyip tekrar deneyin.");
            }
        }
    }

    protected function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    protected function view($view, $data = []) {
        if (file_exists(__DIR__ . '/../views/' . $view . '.php')) {
            // Load Site Settings into every view
            require_once __DIR__ . '/../models/SiteSettings.php';
            $siteSettingsModel = new SiteSettings();
            $siteSettings = $siteSettingsModel->getSettings();
            
            $globalData = [
                'site_name' => $siteSettings['site_name'] ?? 'Apartman Yönetim Sistemi',
                'site_settings' => $siteSettings
            ];
            
            extract($globalData);
            extract($data);
            require_once __DIR__ . '/../views/' . $view . '.php';
        } else {
            die("View does not exist.");
        }
    }

    protected function redirect($url) {
        header("Location: " . SITE_URL . $url);
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
