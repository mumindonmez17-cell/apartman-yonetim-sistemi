<?php

class Resident extends Model {
    public function getAll($filters = []) {
        $sql = "SELECT residents.*, blocks.block_name, apartments.door_number 
                FROM residents 
                JOIN blocks ON residents.block_id = blocks.id 
                JOIN apartments ON residents.apartment_id = apartments.id ";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['name'])) {
            $where[] = "residents.name LIKE ?";
            $params[] = "%" . $filters['name'] . "%";
        }
        if (!empty($filters['block_id'])) {
            $where[] = "residents.block_id = ?";
            $params[] = $filters['block_id'];
        }
        if (!empty($filters['resident_type'])) {
            $where[] = "residents.resident_type = ?";
            $params[] = $filters['resident_type'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY blocks.block_name ASC, apartments.door_number ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO residents (name, phone, block_id, apartment_id, resident_type) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['phone'],
            $data['block_id'],
            $data['apartment_id'],
            $data['resident_type']
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM residents WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE residents SET name = ?, phone = ?, block_id = ?, apartment_id = ?, resident_type = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['phone'],
            $data['block_id'],
            $data['apartment_id'],
            $data['resident_type'],
            $id
        ]);
    }

    public function getRatio($apartment_id) {
        $stmt = $this->db->prepare("SELECT * FROM apartment_payment_ratio WHERE apartment_id = ?");
        $stmt->execute([$apartment_id]);
        return $stmt->fetch();
    }

    public function updateRatio($apartment_id, $tenant_ratio, $owner_ratio, $extra_tenant, $extra_owner) {
        $stmt = $this->db->prepare("INSERT INTO apartment_payment_ratio (apartment_id, tenant_ratio, owner_ratio, extra_tenant_ratio, extra_owner_ratio) 
                                    VALUES (?, ?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE 
                                        tenant_ratio = VALUES(tenant_ratio), 
                                        owner_ratio = VALUES(owner_ratio),
                                        extra_tenant_ratio = VALUES(extra_tenant_ratio),
                                        extra_owner_ratio = VALUES(extra_owner_ratio)");
        return $stmt->execute([$apartment_id, $tenant_ratio, $owner_ratio, $extra_tenant, $extra_owner]);
    }
}
