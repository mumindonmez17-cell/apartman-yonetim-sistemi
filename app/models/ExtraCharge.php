<?php

class ExtraCharge extends Model {
    public function getAll($filters = []) {
        $sql = "SELECT * FROM extra_charges";
        $where = [];
        $params = [];

        if (!empty($filters['year'])) {
            $where[] = "year = ?";
            $params[] = $filters['year'];
        }
        if (!empty($filters['month'])) {
            $where[] = "month = ?";
            $params[] = $filters['month'];
        }
        if (!empty($filters['title'])) {
            $where[] = "title LIKE ?";
            $params[] = "%" . $filters['title'] . "%";
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY year DESC, month DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($year, $month, $title, $amount, $description) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT INTO extra_charges (year, month, title, amount, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$year, $month, $title, $amount, $description]);
            $extra_id = $this->db->lastInsertId();

            // Distribute among all apartments (100% owner by default or based on ratio? 
            // The request says "all apartments", usually extra charges are owner's responsibility.
            // But I'll use the same ratio logic for consistency or as requested.
            $apts = $this->db->query("SELECT a.id, 
                                            COALESCE(r.tenant_ratio, 100) as t_ratio, 
                                            COALESCE(r.owner_ratio, 0) as o_ratio,
                                            COALESCE(r.extra_tenant_ratio, 0) as et_ratio,
                                            COALESCE(r.extra_owner_ratio, 100) as eo_ratio
                                    FROM apartments a 
                                    LEFT JOIN apartment_payment_ratio r ON a.id = r.apartment_id")->fetchAll();

            $stmtAssign = $this->db->prepare("INSERT INTO extra_charge_assignments (extra_charge_id, apartment_id, tenant_amount, owner_amount) VALUES (?, ?, ?, ?)");
            foreach ($apts as $apt) {
                $tenant_amt = ($amount * $apt['et_ratio']) / 100;
                $owner_amt = ($amount * $apt['eo_ratio']) / 100;
                $stmtAssign->execute([$extra_id, $apt['id'], $tenant_amt, $owner_amt]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM extra_charges WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
