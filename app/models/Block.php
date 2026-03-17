<?php

class Block extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM blocks ORDER BY block_name ASC")->fetchAll();
    }

    public function create($name) {
        $stmt = $this->db->prepare("INSERT INTO blocks (block_name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM blocks WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
