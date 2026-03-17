<?php

class DueController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $dueModel = $this->model('Due');
        $data['dues'] = $dueModel->getAll();
        $data['title'] = 'Aidat Aç';
        $this->view('dues/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = $_POST['year'];
            $month = $_POST['month'];
            $amount = $_POST['amount'];
            $description = $_POST['description'] ?? '';
            
            $dueModel = $this->model('Due');
            if ($dueModel->create($year, $month, $amount, $description)) {
                $_SESSION['success'] = 'Aidat başarıyla oluşturuldu ve tüm dairelere borç olarak yansıtıldı.';
            }
        }
        $this->redirect('due');
    }

    public function delete($id) {
        $dueModel = $this->model('Due');
        if ($dueModel->delete($id)) {
            $_SESSION['success'] = 'Aidat silindi.';
        }
        $this->redirect('due');
    }
}
