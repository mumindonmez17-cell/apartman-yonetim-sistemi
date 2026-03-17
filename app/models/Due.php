<?php

class Due extends Model {
    public function getAll() {
        return $this->db->query("SELECT * FROM dues ORDER BY year DESC, month DESC")->fetchAll();
    }

    public function create($year, $month, $amount, $description) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT INTO dues (year, month, amount, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$year, $month, $amount, $description]);
            $due_id = $this->db->lastInsertId();

            // Get all apartments and their ratios
            $apts = $this->db->query("SELECT a.id, COALESCE(r.tenant_ratio, 100) as t_ratio, COALESCE(r.owner_ratio, 0) as o_ratio 
                                    FROM apartments a 
                                    LEFT JOIN apartment_payment_ratio r ON a.id = r.apartment_id")->fetchAll();

            $stmtAssign = $this->db->prepare("INSERT INTO dues_assignments (due_id, apartment_id, tenant_amount, owner_amount) VALUES (?, ?, ?, ?)");
            foreach ($apts as $apt) {
                $tenant_amt = ($amount * $apt['t_ratio']) / 100;
                $owner_amt = ($amount * $apt['o_ratio']) / 100;
                $stmtAssign->execute([$due_id, $apt['id'], $tenant_amt, $owner_amt]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM dues WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
