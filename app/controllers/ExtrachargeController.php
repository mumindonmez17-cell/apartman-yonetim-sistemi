<?php

class ExtrachargeController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $extraModel = $this->model('ExtraCharge');
        $filters = [
            'year' => $_GET['year'] ?? '',
            'month' => $_GET['month'] ?? '',
            'title' => $_GET['title'] ?? ''
        ];
        $data['extras'] = $extraModel->getAll($filters);
        $data['filters'] = $filters;
        $data['title'] = 'Ekstra Borç';
        $this->view('extracharges/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = $_POST['year'];
            $month = $_POST['month'];
            $title = $_POST['title'];
            $amount = $_POST['amount'];
            $description = $_POST['description'] ?? '';
            
            $extraModel = $this->model('ExtraCharge');
            if ($extraModel->create($year, $month, $title, $amount, $description)) {
                $_SESSION['success'] = 'Ekstra ödeme başarıyla oluşturuldu ve tüm dairelere borç olarak yansıtıldı.';
            }
        }
        $this->redirect('extracharge');
    }

    public function delete($id) {
        $extraModel = $this->model('ExtraCharge');
        if ($extraModel->delete($id)) {
            $_SESSION['success'] = 'Ekstra ödeme silindi.';
        }
        $this->redirect('extracharge');
    }
}
