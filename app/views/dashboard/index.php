<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="row g-3 g-md-4 mb-4">
    <!-- Quick Metrics -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);">
            <div class="card-body p-3">
                <div class="d-flex flex-column h-100">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 12px;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h6 class="text-muted mb-1 small fw-bold">Aidat (Ay)</h6>
                    <a href="<?php echo SITE_URL; ?>report?type=dues" class="text-decoration-none">
                        <h5 class="mb-0 fw-bold text-dark">₺<?php echo number_format($metrics['monthly_dues'], 2); ?></h5>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #ffffff 0%, #f8fff9 100%);">
            <div class="card-body p-3">
                <div class="d-flex flex-column h-100">
                    <div class="icon-box bg-success bg-opacity-10 text-success mb-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 12px;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h6 class="text-muted mb-1 small fw-bold">Tahsilat (Ay)</h6>
                    <a href="<?php echo SITE_URL; ?>payment" class="text-decoration-none">
                        <h5 class="mb-0 fw-bold text-success">₺<?php echo number_format($metrics['monthly_collected'], 2); ?></h5>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #ffffff 0%, #fff8f8 100%);">
            <div class="card-body p-3">
                <div class="d-flex flex-column h-100">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger mb-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 12px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h6 class="text-muted mb-1 small fw-bold">Borç (Toplam)</h6>
                    <a href="<?php echo SITE_URL; ?>report?type=dues" class="text-decoration-none">
                        <h5 class="mb-0 fw-bold text-danger">₺<?php echo number_format($metrics['total_site_debt'], 2); ?></h5>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg-2">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #ffffff 0%, #fffcf5 100%); border-left: 4px solid #ffc107 !important;">
            <div class="card-body p-3">
                <div class="d-flex flex-column h-100">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mb-2" style="width: 35px; height: 35px; font-size: 0.9rem; border-radius: 10px;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h6 class="text-muted mb-1 small fw-bold">Gider (Ay)</h6>
                    <h5 class="mb-0 fw-bold text-warning" style="font-size: 1.1rem;">₺<?php echo number_format($metrics['monthly_expenses'], 2); ?></h5>
                    <div class="mt-2 pt-2 border-top border-warning border-opacity-10" style="font-size: 0.65rem; color: #888;">
                        <div class="d-flex justify-content-between"><span>Masraf:</span> <span class="fw-bold">₺<?php echo number_format($metrics['real_monthly_expenses'], 2); ?></span></div>
                        <div class="d-flex justify-content-between"><span>Bekleyen:</span> <span class="fw-bold">₺<?php echo number_format($metrics['monthly_unpaid'], 2); ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-2 small fw-bold text-uppercase">Net Durum (Bakiye)</h6>
                        <h2 class="mb-1 fw-bold">₺<?php echo number_format($metrics['total_net_status'], 2); ?></h2>
                        <p class="mb-0 text-white-50" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle me-1"></i> Tüm alacaklar ve borçlar dahil
                        </p>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <div class="badge bg-white bg-opacity-20 py-2 px-3 rounded-pill" style="font-size: 0.7rem;">
                        Tahsilat Odaklı
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Graphs -->
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title fw-bold">Aylık Tahsilat ve Gider Grafiği</h5>
            </div>
            <div class="card-body" style="height: 250px;">
                <canvas id="financeChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title fw-bold">Daire Tipleri</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="height: 250px;">
                <canvas id="residentTypeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-3">
    <!-- Widgets -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: #fff;">
            <div class="card-body p-4 text-center">
                <div class="icon-box mx-auto mb-3" style="background: rgba(67, 97, 238, 0.1); color: #4361ee; width: 60px; height: 60px; border-radius: 15px;">
                    <i class="fas fa-building fa-lg"></i>
                </div>
                <h3 class="fw-bold mb-1"><?php echo $counts['apartments']; ?></h3>
                <p class="text-muted mb-0 small fw-bold uppercase">Toplam Daire</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: #fff;">
            <div class="card-body p-4 text-center">
                <div class="icon-box mx-auto mb-3" style="background: rgba(76, 201, 240, 0.1); color: #4cc9f0; width: 60px; height: 60px; border-radius: 15px;">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <h3 class="fw-bold mb-1"><?php echo $counts['residents']; ?></h3>
                <p class="text-muted mb-0 small fw-bold uppercase">Toplam Sakin</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: #fff;">
            <div class="card-body p-4 text-center">
                <div class="icon-box mx-auto mb-3" style="background: rgba(108, 117, 125, 0.1); color: #6c757d; width: 60px; height: 60px; border-radius: 15px;">
                    <i class="fas fa-user-tie fa-lg"></i>
                </div>
                <h3 class="fw-bold mb-1"><?php echo $counts['tenants']; ?></h3>
                <p class="text-muted mb-0 small fw-bold uppercase">Kiracı Sayısı</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: #fff;">
            <div class="card-body p-4 text-center">
                <div class="icon-box mx-auto mb-3" style="background: rgba(40, 167, 69, 0.1); color: #28a745; width: 60px; height: 60px; border-radius: 15px;">
                    <i class="fas fa-house-user fa-lg"></i>
                </div>
                <h3 class="fw-bold mb-1"><?php echo $counts['owners']; ?></h3>
                <p class="text-muted mb-0 small fw-bold uppercase">Ev Sahibi Sayısı</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Finance Chart
    const ctx = document.getElementById('financeChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_data['labels']); ?>,
            datasets: [{
                label: 'Tahsilat',
                data: <?php echo json_encode($chart_data['collected']); ?>,
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Gider',
                data: <?php echo json_encode($chart_data['expenses']); ?>,
                borderColor: '#f72585',
                backgroundColor: 'rgba(247, 37, 133, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            layout: {
                padding: {
                    left: 5,
                    right: 5,
                    top: 10,
                    bottom: 10
                }
            }
        }
    });

    // Resident Type Chart
    const ctx2 = document.getElementById('residentTypeChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Kiracı', 'Ev Sahibi'],
            datasets: [{
                data: [<?php echo $counts['tenants']; ?>, <?php echo $counts['owners']; ?>],
                backgroundColor: ['#4cc9f0', '#4361ee'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%',
            layout: {
                padding: {
                    bottom: 20
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
