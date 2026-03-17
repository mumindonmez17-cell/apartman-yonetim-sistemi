<?php

class ApartmentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $apartmentModel = $this->model('Apartment');
        $blockModel = $this->model('Block');
        
        $data['apartments'] = $apartmentModel->getAll();
        $data['blocks'] = $blockModel->getAll();
        $data['title'] = 'Daireler';
        $this->view('apartments/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $block_id = $_POST['block_id'] ?? '';
            $door_number = $_POST['door_number'] ?? '';
            
            if ($block_id && $door_number) {
                $apartmentModel = $this->model('Apartment');
                if ($apartmentModel->create($block_id, $door_number)) {
                    $_SESSION['success'] = 'Daire başarıyla eklendi.';
                }
            }
        }
        $this->redirect('apartment');
    }

    public function delete($id) {
        $apartmentModel = $this->model('Apartment');
        if ($apartmentModel->delete($id)) {
            $_SESSION['success'] = 'Daire başarıyla silindi.';
        }
        $this->redirect('apartment');
    }
}
