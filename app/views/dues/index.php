<?php require_once __DIR__ . '/../layout/header.php'; 
$months = [1=>'Ocak', 2=>'Şubat', 3=>'Mart', 4=>'Nisan', 5=>'Mayıs', 6=>'Haziran', 7=>'Temmuz', 8=>'Ağustos', 9=>'Eylül', 10=>'Ekim', 11=>'Kasım', 12=>'Aralık'];
?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Aidat Yönetimi</h2>
        <p class="text-muted small mb-0">Aylık aidat tahakkuklarını buradan oluşturun ve takip edin.</p>
    </div>
    <button class="btn btn-primary shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#addDueModal">
        <i class="fas fa-plus me-2"></i> Yeni Aidat Oluştur
    </button>
</div>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-3 fs-4"></i>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Yıl / Ay</th>
                        <th>Tutar (Daire Başı)</th>
                        <th>Açıklama</th>
                        <th>Oluşturulma</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($dues)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <h5 class="fw-bold text-dark">Henüz Aidat Yok</h5>
                                    <p class="mb-0">Henüz hiçbir aidat tahakkuku oluşturulmamış.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($dues as $due): ?>
                            <tr>
                                <td class="ps-4" data-label="Yıl / Ay">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-3" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold h6 mb-0"><?php echo $months[$due['month']]; ?></span>
                                            <div class="text-muted small"><?php echo $due['year']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Tutar">
                                    <span class="fw-bold text-primary h5 mb-0">₺<?php echo number_format($due['amount'], 2); ?></span>
                                </td>
                                <td data-label="Açıklama">
                                    <span class="text-muted small"><?php echo $due['description'] ?: '-'; ?></span>
                                </td>
                                <td data-label="Oluşturulma">
                                    <span class="text-muted small"><?php echo date('d.m.Y', strtotime($due['created_at'])); ?></span>
                                </td>
                                <td class="text-end pe-4" data-label="İşlemler">
                                    <a href="<?php echo SITE_URL; ?>due/delete/<?php echo $due['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Bu aidatı silmek istediğinize emin misiniz? (Tüm ilgili borçlar da silinecektir)')" title="Sil">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Due Modal -->
<div class="modal fade" id="addDueModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>due/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Aidat Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Yıl</label>
                            <select name="year" class="form-select" required>
                                <?php for($y=2030; $y>=2023; $y--): ?>
                                    <option value="<?php echo $y; ?>" <?php echo $y == date('Y') ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ay</label>
                            <select name="month" class="form-select" required>
                                <?php foreach($months as $m => $name): ?>
                                    <option value="<?php echo $m; ?>" <?php echo $m == date('n') ? 'selected' : ''; ?>><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tutar (Daire Başı)</label>
                        <div class="input-group">
                            <span class="input-group-text">₺</span>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Örn: 500" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle me-2"></i> Bu işlem tüm dairelere otomatik olarak borç atayacaktır.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Aidatları Oluştur</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
