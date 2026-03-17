<?php

class Report extends Model {
    public function getDuesReport($filters = []) {
        $timeWhere = "";
        $timeParams = [];
        if (!empty($filters['month'])) {
            $timeWhere .= " AND d.month = ?";
            $timeParams[] = $filters['month'];
        }
        if (!empty($filters['year'])) {
            $timeWhere .= " AND d.year = ?";
            $timeParams[] = $filters['year'];
        }

        $sql = "SELECT r.id as resident_id, r.name, r.resident_type, b.block_name, a.door_number, a.id as apartment_id,
                       (SELECT COALESCE(SUM(da.tenant_amount), 0) FROM dues_assignments da JOIN dues d ON da.due_id = d.id WHERE da.apartment_id = a.id AND r.resident_type = 'tenant' $timeWhere) as total_tenant,
                       (SELECT COALESCE(SUM(da.owner_amount), 0) FROM dues_assignments da JOIN dues d ON da.due_id = d.id WHERE da.apartment_id = a.id AND r.resident_type = 'owner' $timeWhere) as total_owner,
                       (SELECT COALESCE(SUM(p.amount), 0) FROM payments p JOIN dues_assignments da ON p.target_id = da.id JOIN dues d ON da.due_id = d.id WHERE p.resident_id = r.id AND p.type = 'due' $timeWhere) as total_paid
                FROM residents r
                JOIN apartments a ON r.apartment_id = a.id
                JOIN blocks b ON a.block_id = b.id";
        
        return $this->processFinancialReport($sql, $filters, $timeParams);
    }

    public function getExtraReport($filters = []) {
        $timeWhere = "";
        $timeParams = [];
        if (!empty($filters['month'])) {
            $timeWhere .= " AND e.month = ?";
            $timeParams[] = $filters['month'];
        }
        if (!empty($filters['year'])) {
            $timeWhere .= " AND e.year = ?";
            $timeParams[] = $filters['year'];
        }

        $sql = "SELECT r.id as resident_id, r.name, r.resident_type, b.block_name, a.door_number, a.id as apartment_id,
                       (SELECT COALESCE(SUM(ea.tenant_amount), 0) FROM extra_charge_assignments ea JOIN extra_charges e ON ea.extra_charge_id = e.id WHERE ea.apartment_id = a.id AND r.resident_type = 'tenant' $timeWhere) as total_tenant,
                       (SELECT COALESCE(SUM(ea.owner_amount), 0) FROM extra_charge_assignments ea JOIN extra_charges e ON ea.extra_charge_id = e.id WHERE ea.apartment_id = a.id AND r.resident_type = 'owner' $timeWhere) as total_owner,
                       (SELECT COALESCE(SUM(p.amount), 0) FROM payments p JOIN extra_charge_assignments ea ON p.target_id = ea.id JOIN extra_charges e ON ea.extra_charge_id = e.id WHERE p.resident_id = r.id AND p.type = 'extra' $timeWhere) as total_paid
                FROM residents r
                JOIN apartments a ON r.apartment_id = a.id
                JOIN blocks b ON a.block_id = b.id";
        
        return $this->processFinancialReport($sql, $filters, $timeParams);
    }

    private function processFinancialReport($baseSql, $filters, $timeParams = []) {
        $where = [];
        $whereParams = [];
        
        if (!empty($filters['block_id'])) {
            $where[] = "a.block_id = ?";
            $whereParams[] = $filters['block_id'];
        }
        if (!empty($filters['resident_type'])) {
            $where[] = "r.resident_type = ?";
            $whereParams[] = $filters['resident_type'];
        }
        if (!empty($filters['q'])) {
            $where[] = "r.name LIKE ?";
            $whereParams[] = "%" . $filters['q'] . "%";
        }

        if ($where) {
            $baseSql .= " WHERE " . implode(" AND ", $where);
        }

        // Subqueries use timeParams (X3), Outer query uses whereParams
        $stmt = $this->db->prepare($baseSql);
        $stmt->execute(array_merge($timeParams, $timeParams, $timeParams, $whereParams));
        $results = $stmt->fetchAll();

        $filteredResults = [];
        foreach ($results as $row) {
            $row['total_amount'] = ($row['resident_type'] == 'tenant' ? $row['total_tenant'] : $row['total_owner']);
            $row['balance'] = $row['total_amount'] - $row['total_paid'];
            
            // Skip if no responsibility and no payment activity in this period
            if ($row['total_amount'] <= 0 && $row['total_paid'] <= 0) continue;

            // Apply status filter
            if (!empty($filters['status'])) {
                if ($filters['status'] == 'paid' && $row['balance'] > 0) continue;
                if ($filters['status'] == 'unpaid' && $row['balance'] <= 0) continue;
            }
            
            $filteredResults[] = $row;
        }

        return $filteredResults;
    }

    public function getExpenseReport($filters = []) {
        $sql = "SELECT * FROM expenses ";
        $where = [];
        $params = [];
        
        if (!empty($filters['start_date'])) {
            $where[] = "date >= ?";
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = "date <= ?";
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['category'])) {
            $where[] = "category = ?";
            $params[] = $filters['category'];
        }
        if (!empty($filters['month'])) {
            $where[] = "MONTH(date) = ?";
            $params[] = $filters['month'];
        }
        if (!empty($filters['year'])) {
            $where[] = "YEAR(date) = ?";
            $params[] = $filters['year'];
        }
        if (!empty($filters['q'])) {
            $where[] = "(title LIKE ? OR description LIKE ?)";
            $params[] = "%" . $filters['q'] . "%";
            $params[] = "%" . $filters['q'] . "%";
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getSummaryReport($filters = []) {
        $month = !empty($filters['month']) ? $filters['month'] : null;
        $year = !empty($filters['year']) ? $filters['year'] : null;

        // Total Income (Payments received)
        $incomeSql = "SELECT type, SUM(amount) as total FROM payments WHERE 1=1";
        $incomeParams = [];
        if ($month) { $incomeSql .= " AND MONTH(payment_date) = ?"; $incomeParams[] = $month; }
        if ($year) { $incomeSql .= " AND YEAR(payment_date) = ?"; $incomeParams[] = $year; }
        if (!empty($filters['income_category'])) {
            $incomeSql .= " AND type = ?";
            $incomeParams[] = $filters['income_category'];
        }
        $incomeSql .= " GROUP BY type";
        
        $stmt = $this->db->prepare($incomeSql);
        $stmt->execute($incomeParams);
        $incomeDetails = $stmt->fetchAll();
        
        // Actual Expenses
        $expenseSql = "SELECT category, SUM(amount) as total FROM expenses WHERE 1=1";
        $expenseParams = [];
        if ($month) { $expenseSql .= " AND MONTH(date) = ?"; $expenseParams[] = $month; }
        if ($year) { $expenseSql .= " AND YEAR(date) = ?"; $expenseParams[] = $year; }
        if (!empty($filters['category'])) {
            $expenseSql .= " AND category = ?";
            $expenseParams[] = $filters['category'];
        }
        $expenseSql .= " GROUP BY category";

        $stmt = $this->db->prepare($expenseSql);
        $stmt->execute($expenseParams);
        $expenseDetails = $stmt->fetchAll();

        // Calculate UNPAID (Expected - Collected) as a "Gider" (Loss)
        // Dues
        if (empty($filters['category']) || $filters['category'] == 'Gelmeyen Aidatlar') {
            $expectedDuesSql = "SELECT SUM(CASE WHEN r.resident_type = 'tenant' THEN da.tenant_amount ELSE da.owner_amount END) as total 
                                FROM dues_assignments da JOIN dues d ON da.due_id = d.id 
                                LEFT JOIN residents r ON da.apartment_id = r.apartment_id WHERE 1=1";
            $expectedParams = [];
            if ($month) { $expectedDuesSql .= " AND d.month = ?"; $expectedParams[] = $month; }
            if ($year) { $expectedDuesSql .= " AND d.year = ?"; $expectedParams[] = $year; }
            $expectedDues = $this->db->prepare($expectedDuesSql);
            $expectedDues->execute($expectedParams);
            $totalExpectedDues = $expectedDues->fetch()['total'] ?? 0;

            $collectedDues = 0;
            foreach ($incomeDetails as $inc) if ($inc['type'] == 'due') $collectedDues = $inc['total'];
            $unpaidDues = $totalExpectedDues - $collectedDues;
            if ($unpaidDues > 0) {
                $expenseDetails[] = ['category' => 'Gelmeyen Aidatlar', 'total' => $unpaidDues];
            }
        }

        // Extra Charges
        if (empty($filters['category']) || $filters['category'] == 'Gelmeyen Ekstra Borçlar') {
            $expectedExtraSql = "SELECT SUM(CASE WHEN r.resident_type = 'tenant' THEN ea.tenant_amount ELSE ea.owner_amount END) as total 
                                 FROM extra_charge_assignments ea JOIN extra_charges e ON ea.extra_charge_id = e.id 
                                 LEFT JOIN residents r ON ea.apartment_id = r.apartment_id WHERE 1=1";
            $expectedExtraParams = [];
            if ($month) { $expectedExtraSql .= " AND e.month = ?"; $expectedExtraParams[] = $month; }
            if ($year) { $expectedExtraSql .= " AND e.year = ?"; $expectedExtraParams[] = $year; }
            $expectedExtra = $this->db->prepare($expectedExtraSql);
            $expectedExtra->execute($expectedExtraParams);
            $totalExpectedExtra = $expectedExtra->fetch()['total'] ?? 0;

            $collectedExtra = 0;
            foreach ($incomeDetails as $inc) if ($inc['type'] == 'extra') $collectedExtra = $inc['total'];
            $unpaidExtra = $totalExpectedExtra - $collectedExtra;
            if ($unpaidExtra > 0) {
                $expenseDetails[] = ['category' => 'Gelmeyen Ekstra Borçlar', 'total' => $unpaidExtra];
            }
        }

        return [
            'income_details' => $incomeDetails,
            'expense_details' => $expenseDetails,
            'total_income' => array_sum(array_column($incomeDetails, 'total')),
            'total_expense' => array_sum(array_column($expenseDetails, 'total'))
        ];
    }
    public function getPaymentsReport($filters = []) {
        $sql = "SELECT p.*, r.name as resident_name, b.block_name, a.door_number 
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
        if (!empty($filters['type'])) {
            $where[] = "p.type = ?";
            $params[] = $filters['type'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY p.payment_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getExtraChargeDefinitions($filters = []) {
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
}
