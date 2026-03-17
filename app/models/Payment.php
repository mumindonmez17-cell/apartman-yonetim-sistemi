<?php

class Payment extends Model {
    public function getPendingAssignments($resident_id = null) {
        // This is complex. We need to find assignments where (tenant_amount > paid_tenant OR owner_amount > paid_owner)
        // AND link them to the specific resident.
        
        $sql = "SELECT 'due' as type, da.id as assignment_id, d.year, d.month, d.description as title, 
                       da.tenant_amount, da.owner_amount, da.paid_tenant, da.paid_owner,
                       a.door_number, b.block_name, a.id as apartment_id
                FROM dues_assignments da
                JOIN dues d ON da.due_id = d.id
                JOIN apartments a ON da.apartment_id = a.id
                JOIN blocks b ON a.block_id = b.id
                WHERE (da.tenant_amount > da.paid_tenant OR da.owner_amount > da.paid_owner)
                
                UNION ALL
                
                SELECT 'extra' as type, ea.id as assignment_id, e.year, e.month, e.title, 
                       ea.tenant_amount, ea.owner_amount, ea.paid_tenant, ea.paid_owner,
                       a.door_number, b.block_name, a.id as apartment_id
                FROM extra_charge_assignments ea
                JOIN extra_charges e ON ea.extra_charge_id = e.id
                JOIN apartments a ON ea.apartment_id = a.id
                JOIN blocks b ON a.block_id = b.id
                WHERE (ea.tenant_amount > ea.paid_tenant OR ea.owner_amount > ea.paid_owner)
                ORDER BY year DESC, month DESC";
                
        return $this->db->query($sql)->fetchAll();
    }

    public function create($data) {
        $this->db->beginTransaction();
        try {
            // 1. Insert payment record
            $stmt = $this->db->prepare("INSERT INTO payments (resident_id, type, target_id, amount, payment_date, payment_type) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['resident_id'],
                $data['type'],
                $data['target_id'],
                $data['amount'],
                $data['payment_date'],
                $data['payment_type']
            ]);

            // 2. Update assignment paid amount
            if ($data['type'] == 'due') {
                $field = ($data['payment_type'] == 'tenant') ? 'paid_tenant' : 'paid_owner';
                $stmtUpdate = $this->db->prepare("UPDATE dues_assignments SET $field = $field + ? WHERE id = ?");
                $stmtUpdate->execute([$data['amount'], $data['target_id']]);
            } else {
                $field = ($data['payment_type'] == 'tenant') ? 'paid_tenant' : 'paid_owner';
                $stmtUpdate = $this->db->prepare("UPDATE extra_charge_assignments SET $field = $field + ? WHERE id = ?");
                $stmtUpdate->execute([$data['amount'], $data['target_id']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAll($filters = []) {
        $sql = "SELECT p.*, r.name as resident_name, r.resident_type, b.block_name, a.door_number 
                FROM payments p
                JOIN residents r ON p.resident_id = r.id
                JOIN apartments a ON r.apartment_id = a.id
                JOIN blocks b ON a.block_id = b.id";
        
        $where = [];
        $params = [];

        if (!empty($filters['name'])) {
            $where[] = "r.name LIKE ?";
            $params[] = "%" . $filters['name'] . "%";
        }
        if (!empty($filters['block_id'])) {
            $where[] = "a.block_id = ?";
            $params[] = $filters['block_id'];
        }
        if (!empty($filters['resident_type'])) {
            $where[] = "r.resident_type = ?";
            $params[] = $filters['resident_type'];
        }
        if (!empty($filters['type'])) {
            $where[] = "p.type = ?";
            $params[] = $filters['type'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY p.payment_date DESC, p.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateAmount($id, $newAmount) {
        $this->db->beginTransaction();
        try {
            // 1. Get old payment info
            $stmt = $this->db->prepare("SELECT * FROM payments WHERE id = ?");
            $stmt->execute([$id]);
            $oldPayment = $stmt->fetch();
            if (!$oldPayment) throw new Exception("Ödeme kaydı bulunamadı.");

            $diff = $newAmount - $oldPayment['amount'];

            // 2. Update payment record
            $stmtUpdatePayment = $this->db->prepare("UPDATE payments SET amount = ? WHERE id = ?");
            $stmtUpdatePayment->execute([$newAmount, $id]);

            // 3. Adjust assignment balance
            $table = ($oldPayment['type'] == 'due') ? 'dues_assignments' : 'extra_charge_assignments';
            $field = ($oldPayment['payment_type'] == 'tenant') ? 'paid_tenant' : 'paid_owner';

            $stmtUpdateAssignment = $this->db->prepare("UPDATE $table SET $field = $field + ? WHERE id = ?");
            $stmtUpdateAssignment->execute([$diff, $oldPayment['target_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete($id) {
        $this->db->beginTransaction();
        try {
            // 1. Get payment info before deleting
            $stmt = $this->db->prepare("SELECT * FROM payments WHERE id = ?");
            $stmt->execute([$id]);
            $payment = $stmt->fetch();
            if (!$payment) throw new Exception("Ödeme kaydı bulunamadı.");

            // 2. Reverse assignment balance
            $table = ($payment['type'] == 'due') ? 'dues_assignments' : 'extra_charge_assignments';
            $field = ($payment['payment_type'] == 'tenant') ? 'paid_tenant' : 'paid_owner';

            $stmtUpdate = $this->db->prepare("UPDATE $table SET $field = $field - ? WHERE id = ?");
            $stmtUpdate->execute([$payment['amount'], $payment['target_id']]);

            // 3. Delete payment record
            $stmtDelete = $this->db->prepare("DELETE FROM payments WHERE id = ?");
            $stmtDelete->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
