<?php

class ExpenseController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $expenseModel = $this->model('Expense');
        
        $filters = [
            'month' => $_GET['month'] ?? '',
            'year' => $_GET['year'] ?? '',
            'category' => $_GET['category'] ?? '',
            'q' => $_GET['q'] ?? ''
        ];

        $data['expenses'] = $expenseModel->getAll($filters);
        $data['filters'] = $filters;
        $data['title'] = 'Gider Yönetimi';
        $this->view('expenses/index', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category' => $_POST['category'],
                'title' => $_POST['title'],
                'amount' => $_POST['amount'],
                'date' => $_POST['date'],
                'description' => $_POST['description'] ?? ''
            ];
            
            $expenseModel = $this->model('Expense');
            if ($expenseModel->create($data)) {
                $_SESSION['success'] = 'Gider kaydı başarıyla eklendi.';
            }
        }
        $this->redirect('expense');
    }

    public function delete($id) {
        $expenseModel = $this->model('Expense');
        if ($expenseModel->delete($id)) {
            $_SESSION['success'] = 'Gider kaydı silindi.';
        }
        $this->redirect('expense');
    }
}
