<?php

class Apartment extends Model {
    public function getAll() {
        $sql = "SELECT apartments.*, blocks.block_name 
                FROM apartments 
                JOIN blocks ON apartments.block_id = blocks.id 
                ORDER BY blocks.block_name ASC, apartments.door_number ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function create($block_id, $door_number) {
        $stmt = $this->db->prepare("INSERT INTO apartments (block_id, door_number) VALUES (?, ?)");
        return $stmt->execute([$block_id, $door_number]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM apartments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
