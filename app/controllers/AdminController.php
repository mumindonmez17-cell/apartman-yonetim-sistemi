<?php

class AdminController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $adminModel = $this->model('Admin');
        $data['admins'] = $adminModel->getAll();
        $data['title'] = 'Yönetici Ayarları';
        $this->view('admins/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            $adminModel = $this->model('Admin');
            
            // Check if username already exists
            if ($adminModel->getByUsername($username)) {
                $_SESSION['error'] = 'Bu kullanıcı adı zaten kullanımda!';
            } else {
                if ($adminModel->create($username, $password)) {
                    $_SESSION['success'] = 'Yeni yönetici başarıyla eklendi.';
                } else {
                    $_SESSION['error'] = 'Yönetici eklenirken bir hata oluştu.';
                }
            }
        }
        $this->redirect('admin');
    }

    public function delete($id) {
        // Prevent deleting yourself
        if ($id == $_SESSION['admin_id']) {
            $_SESSION['error'] = 'Kendi hesabınızı silemezsiniz!';
            $this->redirect('admin');
            return;
        }

        $adminModel = $this->model('Admin');
        if ($adminModel->delete($id)) {
            $_SESSION['success'] = 'Yönetici başarıyla silindi.';
        }
        $this->redirect('admin');
    }
}
