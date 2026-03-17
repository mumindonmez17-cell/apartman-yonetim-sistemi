<?php

class DashboardController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        
        // 1. Expected for this month
        $stmt = $db->query("SELECT SUM(tenant_amount + owner_amount) FROM dues_assignments da JOIN dues d ON da.due_id = d.id WHERE d.month = MONTH(CURRENT_DATE()) AND d.year = YEAR(CURRENT_DATE())");
        $monthly_expected_dues = $stmt->fetchColumn() ?: 0;
        
        $stmt = $db->query("SELECT SUM(tenant_amount + owner_amount) FROM extra_charge_assignments ea JOIN extra_charges e ON ea.extra_charge_id = e.id WHERE e.month = MONTH(CURRENT_DATE()) AND e.year = YEAR(CURRENT_DATE())");
        $monthly_expected_extras = $stmt->fetchColumn() ?: 0;

        // 2. Collected this month (Payments)
        $stmt = $db->query("SELECT SUM(amount) FROM payments WHERE MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())");
        $monthly_collected = $stmt->fetchColumn() ?: 0;

        // 3. Unpaid for this month (Virtual Loss)
        $monthly_unpaid = max(0, ($monthly_expected_dues + $monthly_expected_extras) - $monthly_collected);

        // 4. Real Expenses this month
        $stmt = $db->query("SELECT SUM(amount) FROM expenses WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())");
        $real_monthly_expenses = $stmt->fetchColumn() ?: 0;

        // 5. Total site debt & general net status
        $total_assigned_dues = $db->query("SELECT SUM(tenant_amount + owner_amount) FROM dues_assignments")->fetchColumn() ?: 0;
        $total_assigned_extras = $db->query("SELECT SUM(tenant_amount + owner_amount) FROM extra_charge_assignments")->fetchColumn() ?: 0;
        $total_payments = $db->query("SELECT SUM(amount) FROM payments")->fetchColumn() ?: 0;
        $total_expenses = $db->query("SELECT SUM(amount) FROM expenses")->fetchColumn() ?: 0;
        
        $total_site_debt = ($total_assigned_dues + $total_assigned_extras) - $total_payments;
        
        // Net Status: Cash Basis vs Accrual Basis? 
        // User wants uncollected to count as expense. 
        // So total_expense becomes (Real Expenses + All Unpaid)
        $total_unpaid = max(0, ($total_assigned_dues + $total_assigned_extras) - $total_payments);
        $total_net_status = $total_payments - ($total_expenses + $total_unpaid);

        $data['metrics'] = [
            'monthly_dues' => $monthly_expected_dues,
            'monthly_collected' => $monthly_collected,
            'monthly_unpaid' => $monthly_unpaid,
            'real_monthly_expenses' => $real_monthly_expenses,
            'monthly_expenses' => $real_monthly_expenses + $monthly_unpaid,
            'total_site_debt' => $total_site_debt,
            'total_net_status' => $total_net_status
        ];
        
        $data['counts'] = [
            'apartments' => $db->query("SELECT COUNT(*) FROM apartments")->fetchColumn(),
            'residents' => $db->query("SELECT COUNT(*) FROM residents")->fetchColumn(),
            'tenants' => $db->query("SELECT COUNT(*) FROM residents WHERE resident_type = 'tenant'")->fetchColumn(),
            'owners' => $db->query("SELECT COUNT(*) FROM residents WHERE resident_type = 'owner'")->fetchColumn()
        ];

        // 5. Chart Data (Last 6 Months)
        $chart_data = [
            'labels' => [],
            'collected' => [],
            'expenses' => []
        ];

        $turkishMonths = [1=>'Oca', 2=>'Şub', 3=>'Mar', 4=>'Nis', 5=>'May', 6=>'Haz', 7=>'Tem', 8=>'Ağu', 9=>'Eyl', 10=>'Eki', 11=>'Kas', 12=>'Ara'];

        for ($i = 5; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify("-$i months");
            $m = (int)$date->format('m');
            $y = (int)$date->format('Y');
            
            $chart_data['labels'][] = $turkishMonths[$m];

            // Collections
            $stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE MONTH(payment_date) = ? AND YEAR(payment_date) = ?");
            $stmt->execute([$m, $y]);
            $collected = (float)($stmt->fetchColumn() ?: 0);
            $chart_data['collected'][] = $collected;

            // Expenses (Real + Unpaid Loss)
            $stmt = $db->prepare("SELECT SUM(amount) FROM expenses WHERE MONTH(date) = ? AND YEAR(date) = ?");
            $stmt->execute([$m, $y]);
            $real_exp = (float)($stmt->fetchColumn() ?: 0);

            // Expected for this specific month in the past
            $stmt = $db->prepare("SELECT SUM(tenant_amount + owner_amount) FROM dues_assignments da JOIN dues d ON da.due_id = d.id WHERE d.month = ? AND d.year = ?");
            $stmt->execute([$m, $y]);
            $exp_dues = (float)($stmt->fetchColumn() ?: 0);

            $stmt = $db->prepare("SELECT SUM(tenant_amount + owner_amount) FROM extra_charge_assignments ea JOIN extra_charges e ON ea.extra_charge_id = e.id WHERE e.month = ? AND e.year = ?");
            $stmt->execute([$m, $y]);
            $exp_extras = (float)($stmt->fetchColumn() ?: 0);

            $unpaid = max(0, ($exp_dues + $exp_extras) - $collected);
            $chart_data['expenses'][] = $real_exp + $unpaid;
        }
        $data['chart_data'] = $chart_data;

        $data['title'] = 'Dashboard';
        $this->view('dashboard/index', $data);
    }
}
