<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Gider Yönetimi</h2>
        <p class="text-muted small mb-0">Sitenizin harcamalarını ve faturalarını buradan takip edebilirsiniz.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        <?php 
        $export_params = $_GET;
        unset($export_params['url']);
        ?>
        <a href="<?php echo SITE_URL; ?>report/export?export_type=expense&<?php echo http_build_query($export_params); ?>" class="btn btn-outline-success border-2 shadow-sm flex-grow-1 flex-md-grow-0">
            <i class="fas fa-file-excel me-2"></i> Excel İndir
        </a>
        <button class="btn btn-primary shadow-sm flex-grow-1 flex-md-grow-0 px-4" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="fas fa-plus me-2"></i> Yeni Gider Ekle
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
            <input type="hidden" name="url" value="expense">
            <div class="col-md-2">
                <label class="form-label small fw-bold">Ay</label>
                <select name="month" class="form-select">
                    <option value="">Tümü</option>
                    <?php 
                    $months = [1=>'Ocak', 2=>'Şubat', 3=>'Mart', 4=>'Nisan', 5=>'Mayıs', 6=>'Haziran', 7=>'Temmuz', 8=>'Ağustos', 9=>'Eylül', 10=>'Ekim', 11=>'Kasım', 12=>'Aralık'];
                    foreach($months as $num => $name): ?>
                        <option value="<?php echo $num; ?>" <?php echo ($filters['month'] ?? '') == $num ? 'selected' : ''; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Yıl</label>
                <select name="year" class="form-select">
                    <option value="">Tümü</option>
                    <?php 
                    $currentYear = date('Y');
                    for($y=$currentYear-2; $y<=$currentYear+2; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($filters['year'] ?? '') == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Kategori</label>
                <select name="category" class="form-select">
                    <option value="">Tüm Kategoriler</option>
                    <option value="Peyzaj" <?php echo ($filters['category'] ?? '') == 'Peyzaj' ? 'selected' : ''; ?>>Peyzaj</option>
                    <option value="Elektrik" <?php echo ($filters['category'] ?? '') == 'Elektrik' ? 'selected' : ''; ?>>Elektrik</option>
                    <option value="Su" <?php echo ($filters['category'] ?? '') == 'Su' ? 'selected' : ''; ?>>Su</option>
                    <option value="Temizlik" <?php echo ($filters['category'] ?? '') == 'Temizlik' ? 'selected' : ''; ?>>Temizlik</option>
                    <option value="Asansör" <?php echo ($filters['category'] ?? '') == 'Asansör' ? 'selected' : ''; ?>>Asansör</option>
                    <option value="Tamirat" <?php echo ($filters['category'] ?? '') == 'Tamirat' ? 'selected' : ''; ?>>Tamirat</option>
                    <option value="Diğer" <?php echo ($filters['category'] ?? '') == 'Diğer' ? 'selected' : ''; ?>>Diğer</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Ara</label>
                <input type="text" name="q" class="form-control" placeholder="Başlık veya açıklama..." value="<?php echo htmlspecialchars($filters['q'] ?? ''); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
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
                        <th class="ps-4">Tarih</th>
                        <th>Kategori</th>
                        <th>Başlık</th>
                        <th class="text-end">Tutar</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($expenses)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    <h5 class="fw-bold text-dark">Gider Kaydı Yok</h5>
                                    <p class="mb-0">Arama kriterlerinize uygun gider kaydı bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($expenses as $exp): ?>
                            <tr>
                                <td class="ps-4" data-label="Tarih">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-light text-muted me-3" style="width: 35px; height: 35px; font-size: 0.8rem; border-radius: 8px;">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <span class="text-muted small fw-bold"><?php echo date('d.m.Y', strtotime($exp['date'])); ?></span>
                                    </div>
                                </td>
                                <td data-label="Kategori">
                                    <span class="premium-badge badge-block"><?php echo $exp['category']; ?></span>
                                </td>
                                <td data-label="Başlık">
                                    <div class="fw-bold h6 mb-0"><?php echo e($exp['title']); ?></div>
                                    <?php if($exp['description']): ?>
                                        <small class="text-muted d-block" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo e($exp['description']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-danger fw-bold h6" data-label="Tutar">₺<?php echo number_format($exp['amount'], 2); ?></td>
                                <td class="text-end pe-4" data-label="İşlemler">
                                    <a href="<?php echo SITE_URL; ?>expense/delete/<?php echo $exp['id']; ?>" class="btn btn-sm btn-white border-0 text-danger shadow-sm rounded-circle" onclick="return confirm('Bu gider kaydını silmek istediğinize emin misiniz?')" title="Sil">
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

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>expense/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Gider Kaydı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Gider Başlığı</label>
                        <input type="text" name="title" class="form-control" placeholder="Örn: Elektrik Faturası, Asansör Bakımı" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select" required>
                            <option value="Peyzaj">Peyzaj</option>
                            <option value="Elektrik">Elektrik</option>
                            <option value="Su">Su</option>
                            <option value="Temizlik">Temizlik</option>
                            <option value="Asansör">Asansör</option>
                            <option value="Tamirat">Tamirat</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tutar</label>
                            <div class="input-group">
                                <span class="input-group-text">₺</span>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
