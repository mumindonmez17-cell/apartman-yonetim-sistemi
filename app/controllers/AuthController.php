<?php

class AuthController extends Controller {
    public function login() {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $adminModel = $this->model('Admin');
            $user = $adminModel->login($username, $password);

            if ($user) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_user'] = $user['username'];
                $this->redirect('dashboard');
            } else {
                $data['error'] = 'Hatalı kullanıcı adı veya şifre!';
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('auth/login');
    }
}
