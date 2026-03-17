<?php

class PaymentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $paymentModel = $this->model('Payment');
        $residentModel = $this->model('Resident');
        $blockModel = $this->model('Block');
        
        $filters = [
            'name' => $_GET['name'] ?? '',
            'block_id' => $_GET['block_id'] ?? '',
            'resident_type' => $_GET['resident_type'] ?? '',
            'type' => $_GET['type'] ?? ''
        ];

        $data['payments'] = $paymentModel->getAll($filters);
        $data['residents'] = $residentModel->getAll();
        $data['blocks'] = $blockModel->getAll();
        $data['pending'] = $paymentModel->getPendingAssignments();
        $data['filters'] = $filters;
        $data['title'] = 'Ödeme Gir';
        
        // Pre-fill for shortcuts
        $data['prefill'] = [
            'resident_id' => $_GET['resident_id'] ?? '',
            'debt_type' => $_GET['debt_type'] ?? ''
        ];
        
        $this->view('payments/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'resident_id' => $_POST['resident_id'],
                'type' => $_POST['assignment_type'], // 'due' or 'extra'
                'target_id' => $_POST['assignment_id'],
                'amount' => $_POST['amount'],
                'payment_date' => $_POST['payment_date'],
                'payment_type' => $_POST['payment_type'] // 'tenant' or 'owner'
            ];
            
            $paymentModel = $this->model('Payment');
            if ($paymentModel->create($data)) {
                $_SESSION['success'] = 'Ödeme başarıyla kaydedildi.';
            }
        }
        $this->redirect('payment');
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = $_POST['amount'];
            $paymentModel = $this->model('Payment');
            if ($paymentModel->updateAmount($id, $amount)) {
                $_SESSION['success'] = 'Ödeme tutarı güncellendi.';
            } else {
                $_SESSION['error'] = 'Ödeme güncellenirken hata oluştu.';
            }
        }
    }

    public function delete($id) {
        $paymentModel = $this->model('Payment');
        if ($paymentModel->delete($id)) {
            $_SESSION['success'] = 'Ödeme kaydı silindi ve borç bakiyesi geri yüklendi.';
        } else {
            $_SESSION['error'] = 'Ödeme silinirken hata oluştu.';
        }
        $this->redirect('payment');
    }
}
