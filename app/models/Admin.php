<?php

class Admin extends Model {
    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM admin_users ORDER BY id DESC")->fetchAll();
    }

    public function create($username, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        return $stmt->execute([$username, $hashed]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM admin_users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
