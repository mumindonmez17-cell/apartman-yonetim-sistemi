<?php

class ResidentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $residentModel = $this->model('Resident');
        $blockModel = $this->model('Block');
        $apartmentModel = $this->model('Apartment');
        
        $filters = [
            'name' => $_GET['name'] ?? '',
            'block_id' => $_GET['block_id'] ?? '',
            'resident_type' => $_GET['resident_type'] ?? ''
        ];

        $data['residents'] = $residentModel->getAll($filters);
        $data['blocks'] = $blockModel->getAll();
        $data['apartments'] = $apartmentModel->getAll();
        $data['filters'] = $filters;
        $data['title'] = 'Site Sakinleri';
        
        $this->view('residents/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'block_id' => $_POST['block_id'],
                'apartment_id' => $_POST['apartment_id'],
                'resident_type' => $_POST['resident_type']
            ];
            
            $residentModel = $this->model('Resident');
            if ($residentModel->create($data)) {
                $_SESSION['success'] = 'Site sakini başarıyla eklendi.';
            }
        }
        $this->redirect('resident');
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'block_id' => $_POST['block_id'],
                'apartment_id' => $_POST['apartment_id'],
                'resident_type' => $_POST['resident_type']
            ];
            
            $residentModel = $this->model('Resident');
            if ($residentModel->update($id, $data)) {
                $_SESSION['success'] = 'Site sakini bilgileri güncellendi.';
            }
        }
        $this->redirect('resident');
    }

    public function delete($id) {
        $residentModel = $this->model('Resident');
        if ($residentModel->delete($id)) {
            $_SESSION['success'] = 'Site sakini başarıyla silindi.';
        }
        $this->redirect('resident');
    }

    public function ratio() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $apartment_id = $_POST['apartment_id'];
            $tenant_ratio = $_POST['tenant_ratio'];
            $owner_ratio = $_POST['owner_ratio'];
            $extra_tenant = $_POST['extra_tenant_ratio'];
            $extra_owner = $_POST['extra_owner_ratio'];
            
            $residentModel = $this->model('Resident');
            if ($residentModel->updateRatio($apartment_id, $tenant_ratio, $owner_ratio, $extra_tenant, $extra_owner)) {
                $_SESSION['success'] = 'Ödeme oranları güncellendi.';
            }
        }
        $this->redirect('resident');
    }
}
