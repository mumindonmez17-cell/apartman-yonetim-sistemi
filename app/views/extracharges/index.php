<?php require_once __DIR__ . '/../layout/header.php'; 
$months = [1=>'Ocak', 2=>'Şubat', 3=>'Mart', 4=>'Nisan', 5=>'Mayıs', 6=>'Haziran', 7=>'Temmuz', 8=>'Ağustos', 9=>'Eylül', 10=>'Ekim', 11=>'Kasım', 12=>'Aralık'];
?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Ekstra Borçlandırma</h2>
        <p class="text-muted small mb-0">Demirbaş, onarım veya diğer ortak giderler için ek borç tanımlayın.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        <?php 
        $export_params = $_GET;
        unset($export_params['url']);
        ?>
        <a href="<?php echo SITE_URL; ?>report/export?export_type=extra_charges&<?php echo http_build_query($export_params); ?>" class="btn btn-outline-success border-2 shadow-sm flex-grow-1 flex-md-grow-0">
            <i class="fas fa-file-excel me-2"></i> Excel İndir
        </a>
        <button class="btn btn-primary shadow-sm flex-grow-1 flex-md-grow-0 px-4" data-bs-toggle="modal" data-bs-target="#addExtraModal">
            <i class="fas fa-plus me-2"></i> Yeni Ekstra Borç Aç
        </button>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0"><i class="fas fa-filter me-2 text-primary"></i> Filtrele</h6>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" class="row g-3">
            <input type="hidden" name="url" value="extracharge">
            <div class="col-md-5">
                <label class="form-label small fw-bold">Borç Başlığı Ara</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="title" class="form-control border-start-0 ps-0" placeholder="Anahtar kelime..." value="<?php echo htmlspecialchars($filters['title']); ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Yıl</label>
                <select name="year" class="form-select">
                    <option value="">Tümü</option>
                    <?php for($y=2030; $y>=2024; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $filters['year'] == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Ay</label>
                <select name="month" class="form-select">
                    <option value="">Tümü</option>
                    <?php foreach($months as $m => $name): ?>
                        <option value="<?php echo $m; ?>" <?php echo $filters['month'] == $m ? 'selected' : ''; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100 shadow-sm">
                    Filtrele
                </button>
            </div>
        </form>
    </div>
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
                        <th class="ps-4">Borç Başlığı</th>
                        <th>Yıl / Ay</th>
                        <th class="text-end">Tutar (Daire Başı)</th>
                        <th>Açıklama</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($extras)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-plus-circle"></i>
                                    <h5 class="fw-bold text-dark">Ekstra Borç Yok</h5>
                                    <p class="mb-0">Henüz hiçbir ekstra borçlandırma kaydı bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($extras as $extra): ?>
                            <tr>
                                <td class="ps-4" data-label="Başlık">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-warning bg-opacity-10 text-warning me-3" style="width: 40px; height: 40px; font-size: 1rem; border-radius: 10px;">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <span class="fw-bold h6 mb-0"><?php echo e($extra['title']); ?></span>
                                    </div>
                                </td>
                                <td data-label="Yıl / Ay">
                                    <span class="text-muted small fw-bold"><?php echo $months[$extra['month']] . ' ' . $extra['year']; ?></span>
                                </td>
                                <td class="text-end text-danger fw-bold h6 mb-0" data-label="Tutar">₺<?php echo number_format($extra['amount'], 2); ?></td>
                                <td data-label="Açıklama">
                                    <span class="text-muted small"><?php echo e($extra['description']) ?: '-'; ?></span>
                                </td>
                                <td class="text-end pe-4" data-label="İşlemler">
                                    <a href="<?php echo SITE_URL; ?>extracharge/delete/<?php echo $extra['id']; ?>" class="btn btn-sm btn-white border-0 text-danger shadow-sm rounded-circle" onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')" title="Sil">
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

<!-- Add Extra Modal -->
<div class="modal fade" id="addExtraModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>extracharge/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Ekstra Borç Aç</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Borç Başlığı</label>
                        <input type="text" name="title" class="form-control" placeholder="Örn: Çatı Tamiri, Boya vb." required>
                    </div>
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
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Borçlandır</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
