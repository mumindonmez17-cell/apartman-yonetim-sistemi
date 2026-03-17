<?php

class BlockController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $blockModel = $this->model('Block');
        $data['blocks'] = $blockModel->getAll();
        $data['title'] = 'Blok Yönetimi';
        $this->view('blocks/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['block_name'] ?? '';
            if ($name) {
                $blockModel = $this->model('Block');
                if ($blockModel->create($name)) {
                    $_SESSION['success'] = 'Blok başarıyla eklendi.';
                }
            }
        }
        $this->redirect('block');
    }

    public function delete($id) {
        $blockModel = $this->model('Block');
        if ($blockModel->delete($id)) {
            $_SESSION['success'] = 'Blok başarıyla silindi.';
        }
        $this->redirect('block');
    }
}
