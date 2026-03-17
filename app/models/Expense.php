<?php

class Expense extends Model {
    public function getAll($filters = []) {
        $sql = "SELECT * FROM expenses ";
        $where = [];
        $params = [];
        
        if (!empty($filters['month'])) {
            $where[] = "MONTH(date) = ?";
            $params[] = $filters['month'];
        }
        if (!empty($filters['year'])) {
            $where[] = "YEAR(date) = ?";
            $params[] = $filters['year'];
        }
        if (!empty($filters['category'])) {
            $where[] = "category = ?";
            $params[] = $filters['category'];
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

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO expenses (category, title, amount, date, description) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['category'],
            $data['title'],
            $data['amount'],
            $data['date'],
            $data['description']
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getMonthlyStats() {
        $sql = "SELECT category, SUM(amount) as total 
                FROM expenses 
                WHERE MONTH(date) = MONTH(CURRENT_DATE()) 
                AND YEAR(date) = YEAR(CURRENT_DATE()) 
                GROUP BY category";
        return $this->db->query($sql)->fetchAll();
    }
}
