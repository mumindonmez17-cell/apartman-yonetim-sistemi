<?php

class SettingsController extends Controller {
    public function index() {
        if (!isset($_SESSION['admin_user'])) {
            $this->redirect('auth/login');
        }

        $settingsModel = $this->model('SiteSettings');
        $data['settings'] = $settingsModel->getSettings();
        $data['title'] = 'Genel Ayarlar';

        $this->view('settings/index', $data);
    }

    public function update() {
        if (!isset($_SESSION['admin_user'])) {
            $this->redirect('auth/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $siteName = $_POST['site_name'];
            $settingsModel = $this->model('SiteSettings');
            
            if ($settingsModel->updateSiteName($siteName)) {
                $_SESSION['success'] = 'Site ayarları başarıyla güncellendi.';
            } else {
                $_SESSION['error'] = 'Ayarlar güncellenirken bir hata oluştu.';
            }
        }
        $this->redirect('settings');
    }
}
