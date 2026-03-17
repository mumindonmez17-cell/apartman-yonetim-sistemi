<?php

class ReportController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $reportModel = $this->model('Report');
        $blockModel = $this->model('Block');
        
        $filters = [
            'block_id' => $_GET['block_id'] ?? '',
            'resident_type' => $_GET['resident_type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'month' => $_GET['month'] ?? '',
            'year' => $_GET['year'] ?? '',
            'income_category' => $_GET['income_category'] ?? '',
            'category' => $_GET['category'] ?? '',
            'q' => $_GET['q'] ?? '',
            'type' => $_GET['type'] ?? 'dues'
        ];

        if ($filters['type'] == 'expense') {
            $data['results'] = $reportModel->getExpenseReport($filters);
        } elseif ($filters['type'] == 'extra') {
            $data['results'] = $reportModel->getExtraReport($filters);
        } elseif ($filters['type'] == 'summary') {
            $data['summary'] = $reportModel->getSummaryReport($filters);
        } else {
            $data['results'] = $reportModel->getDuesReport($filters);
        }

        $data['blocks'] = $blockModel->getAll();
        $data['filters'] = $filters;
        $data['title'] = 'Raporlar';
        
        $this->view('reports/index', $data);
    }

    public function export() {
        $type = $_GET['export_type'] ?? $_GET['type'] ?? 'dues';
        $reportModel = $this->model('Report');
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Native Excel (.xls) headers using HTML Table format
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=rapor_' . $type . '_' . date('Y-m-d') . '.xls');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"></head><body>';
        echo '<table border="1">';

        $months = [1=>'Ocak', 2=>'Şubat', 3=>'Mart', 4=>'Nisan', 5=>'Mayıs', 6=>'Haziran', 7=>'Temmuz', 8=>'Ağustos', 9=>'Eylül', 10=>'Ekim', 11=>'Kasım', 12=>'Aralık'];

        if ($type == 'expense') {
            $this->renderExcelRow(['Tarih', 'Kategori', 'Başlık', 'Tutar', 'Açıklama'], true);
            $results = $reportModel->getExpenseReport($_GET);
            foreach($results as $row) {
                $this->renderExcelRow([$row['date'], $row['category'], $row['title'], $row['amount'], $row['description']]);
            }
        } elseif ($type == 'payments') {
            $this->renderExcelRow(['Tarih', 'Sakin', 'Blok/Daire', 'Tür', 'Ödeyen', 'Tutar'], true);
            $results = $reportModel->getPaymentsReport($_GET);
            foreach($results as $row) {
                $p_type = ($row['type'] == 'due' ? 'Aidat' : 'Ekstra');
                $resident_type = ($row['payment_type'] == 'tenant' ? 'Kiracı' : 'Ev Sahibi');
                $this->renderExcelRow([$row['payment_date'], $row['resident_name'], $row['block_name'].'-'.$row['door_number'], $p_type, $resident_type, $row['amount']]);
            }
        } elseif ($type == 'extra_charges') {
            $this->renderExcelRow(['Yıl', 'Ay', 'Başlık', 'Tutar (Daire Başı)', 'Açıklama'], true);
            $results = $reportModel->getExtraChargeDefinitions($_GET);
            foreach($results as $row) {
                $this->renderExcelRow([$row['year'], $months[$row['month']], $row['title'], $row['amount'], $row['description']]);
            }
        } elseif ($type == 'summary') {
            $this->renderExcelRow(['Tür', 'Kategori / Detay', 'Tutar'], true);
            $summary = $reportModel->getSummaryReport($_GET);
            
            $this->renderExcelRow(['GELİR', '', ''], false, 'background-color: #d1fae5; font-weight: bold;');
            if (isset($summary['income_details'])) {
                foreach($summary['income_details'] as $inc) {
                    $label = ($inc['type'] == 'due' ? 'Aidat Ödemeleri' : 'Ekstra Borç Ödemeleri');
                    $this->renderExcelRow(['', $label, $inc['total']]);
                }
            }
            $this->renderExcelRow(['', 'TOPLAM GELİR', $summary['total_income'] ?? 0], false, 'font-weight: bold;');
            
            $this->renderExcelRow(['', '', '']);
            $this->renderExcelRow(['GİDER', '', ''], false, 'background-color: #fee2e2; font-weight: bold;');
            if (isset($summary['expense_details'])) {
                foreach($summary['expense_details'] as $exp) {
                    $this->renderExcelRow(['', $exp['category'], $exp['total']]);
                }
            }
            $this->renderExcelRow(['', 'TOPLAM GİDER', $summary['total_expense'] ?? 0], false, 'font-weight: bold;');
            
            $this->renderExcelRow(['', '', '']);
            $income = $summary['total_income'] ?? 0;
            $expense = $summary['total_expense'] ?? 0;
            $this->renderExcelRow(['NET DURUM', 'KAR / ZARAR', $income - $expense], false, 'background-color: #dbeafe; font-weight: bold;');

        } elseif ($type == 'residents') {
            $this->renderExcelRow(['Ad Soyad', 'Telefon', 'Blok', 'Daire No', 'Tip'], true);
            $residentModel = $this->model('Resident');
            $results = $residentModel->getAll($_GET);
            foreach($results as $row) {
                $r_type = ($row['resident_type'] == 'tenant' ? 'Kiracı' : 'Ev Sahibi');
                $this->renderExcelRow([$row['name'], $row['phone'], $row['block_name'], $row['door_number'], $r_type]);
            }
        } elseif ($type == 'extra') {
            $this->renderExcelRow(['Sakin', 'Blok/Daire', 'Tip', 'Ekstra Borç Sorumluluğu', 'Ödenen', 'Kalan'], true);
            $results = $reportModel->getExtraReport($_GET);
            foreach($results as $row) {
                $r_type = ($row['resident_type'] == 'tenant' ? 'Kiracı' : 'Ev Sahibi');
                $this->renderExcelRow([$row['name'], $row['block_name'].'-'.$row['door_number'], $r_type, $row['total_amount'], $row['total_paid'], $row['balance']]);
            }
        } else {
            $this->renderExcelRow(['Sakin', 'Blok/Daire', 'Tip', 'Aidat Sorumluluğu', 'Ödenen', 'Kalan'], true);
            $results = $reportModel->getDuesReport($_GET);
            foreach($results as $row) {
                $r_type = ($row['resident_type'] == 'tenant' ? 'Kiracı' : 'Ev Sahibi');
                $this->renderExcelRow([$row['name'], $row['block_name'].'-'.$row['door_number'], $r_type, $row['total_amount'], $row['total_paid'], $row['balance']]);
            }
        }
        
        echo '</table></body></html>';
        exit;
    }

    private function renderExcelRow($data, $isHeader = false, $style = '') {
        echo '<tr style="' . $style . '">';
        foreach($data as $value) {
            $tag = $isHeader ? 'th' : 'td';
            $val = is_numeric($value) ? str_replace('.', ',', $value) : $value;
            echo '<' . $tag . '>' . htmlspecialchars($val) . '</' . $tag . '>';
        }
        echo '</tr>';
    }
}
