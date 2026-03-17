<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Gelişmiş Raporlar</h2>
        <p class="text-muted small mb-0">Sitenizin finansal durumunu ve sakin borçlarını detaylıca analiz edin.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        <?php 
        $export_params = $_GET;
        unset($export_params['url']);
        ?>
        <a href="<?php echo SITE_URL; ?>report/export?export_type=<?php echo $filters['type']; ?>&<?php echo http_build_query($export_params); ?>" class="btn btn-success border-2 shadow-sm flex-grow-1 flex-md-grow-0 px-4">
            <i class="fas fa-file-excel me-2"></i> Excel İndir
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0"><i class="fas fa-filter me-2 text-primary"></i> Rapor Kriterleri</h6>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" class="row g-3">
            <input type="hidden" name="url" value="report">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Rapor Türü</label>
                <select name="type" class="form-select border-primary border-opacity-25" onchange="this.form.submit()">
                    <option value="dues" <?php echo $filters['type'] == 'dues' ? 'selected' : ''; ?>>Aidat Raporu</option>
                    <option value="extra" <?php echo $filters['type'] == 'extra' ? 'selected' : ''; ?>>Ekstra Borç Raporu</option>
                    <option value="expense" <?php echo $filters['type'] == 'expense' ? 'selected' : ''; ?>>Gider Raporu</option>
                    <option value="summary" <?php echo $filters['type'] == 'summary' ? 'selected' : ''; ?>>Özet Kar-Zarar Raporu</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold">Ay</label>
                <select name="month" class="form-select">
                    <option value="">Tümü</option>
                    <?php 
                    $months = [1=>'Ocak', 2=>'Şubat', 3=>'Mart', 4=>'Nisan', 5=>'Mayıs', 6=>'Haziran', 7=>'Temmuz', 8=>'Ağustos', 9=>'Eylül', 10=>'Ekim', 11=>'Kasım', 12=>'Aralık'];
                    foreach($months as $num => $name): ?>
                        <option value="<?php echo e($num); ?>" <?php echo $filters['month'] == $num ? 'selected' : ''; ?>><?php echo e($name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Yıl</label>
                <select name="year" class="form-select">
                    <option value="">Tümü</option>
                    <?php for($y=2024; $y<=2030; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $filters['year'] == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="col-md-5">
                <label class="form-label small fw-bold"><?php echo in_array($filters['type'], ['dues', 'extra']) ? 'Sakin Ara' : 'Başlık/Açıklama Ara'; ?></label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Anahtar kelime..." value="<?php echo e($filters['q']); ?>">
                </div>
            </div>

            <?php if (in_array($filters['type'], ['dues', 'extra'])): ?>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Blok</label>
                    <select name="block_id" class="form-select">
                        <option value="">Tüm Bloklar</option>
                        <?php foreach($blocks as $block): ?>
                            <option value="<?php echo $block['id']; ?>" <?php echo $filters['block_id'] == $block['id'] ? 'selected' : ''; ?>>
                                <?php echo e($block['block_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Sakin Tipi</label>
                    <select name="resident_type" class="form-select">
                        <option value="">Tüm Tipler</option>
                        <option value="tenant" <?php echo $filters['resident_type'] == 'tenant' ? 'selected' : ''; ?>>Kiracı</option>
                        <option value="owner" <?php echo $filters['resident_type'] == 'owner' ? 'selected' : ''; ?>>Ev Sahibi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Ödeme Durumu</label>
                    <select name="status" class="form-select">
                        <option value="">Tümü</option>
                        <option value="paid" <?php echo $filters['status'] == 'paid' ? 'selected' : ''; ?>>Tamamı Ödeyenler</option>
                        <option value="unpaid" <?php echo $filters['status'] == 'unpaid' ? 'selected' : ''; ?>>Borcu Olanlar</option>
                    </select>
                </div>
            <?php elseif ($filters['type'] == 'expense'): ?>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Gider Kategorisi</label>
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
            <?php elseif ($filters['type'] == 'summary'): ?>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Gelir Kategorisi</label>
                    <select name="income_category" class="form-select">
                        <option value="">Tüm Gelirler</option>
                        <option value="due" <?php echo ($filters['income_category'] ?? '') == 'due' ? 'selected' : ''; ?>>Aidat Ödemeleri</option>
                        <option value="extra" <?php echo ($filters['income_category'] ?? '') == 'extra' ? 'selected' : ''; ?>>Ekstra Borç Ödemeleri</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Gider Kategorisi</label>
                    <select name="category" class="form-select">
                        <option value="">Tüm Giderler</option>
                        <option value="Peyzaj" <?php echo ($filters['category'] ?? '') == 'Peyzaj' ? 'selected' : ''; ?>>Peyzaj</option>
                        <option value="Elektrik" <?php echo ($filters['category'] ?? '') == 'Elektrik' ? 'selected' : ''; ?>>Elektrik</option>
                        <option value="Su" <?php echo ($filters['category'] ?? '') == 'Su' ? 'selected' : ''; ?>>Su</option>
                        <option value="Temizlik" <?php echo ($filters['category'] ?? '') == 'Temizlik' ? 'selected' : ''; ?>>Temizlik</option>
                        <option value="Asansör" <?php echo ($filters['category'] ?? '') == 'Asansör' ? 'selected' : ''; ?>>Asansör</option>
                        <option value="Tamirat" <?php echo ($filters['category'] ?? '') == 'Tamirat' ? 'selected' : ''; ?>>Tamirat</option>
                        <option value="Gelmeyen Aidatlar" <?php echo ($filters['category'] ?? '') == 'Gelmeyen Aidatlar' ? 'selected' : ''; ?>>Gelmeyen Aidatlar (Açık)</option>
                        <option value="Gelmeyen Ekstra Borçlar" <?php echo ($filters['category'] ?? '') == 'Gelmeyen Ekstra Borçlar' ? 'selected' : ''; ?>>Gelmeyen Ekstra Borçlar (Açık)</option>
                        <option value="Diğer" <?php echo ($filters['category'] ?? '') == 'Diğer' ? 'selected' : ''; ?>>Diğer</option>
                    </select>
                </div>
            <?php endif; ?>
            
            <div class="<?php echo ($filters['type'] == 'expense' || $filters['type'] == 'summary') ? 'col-md-6' : 'col-md-3'; ?> d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 shadow-sm py-2">
                    <i class="fas fa-search me-2"></i> Raporu Güncelle
                </button>
            </div>

        </form>
    </div>
</div>

<?php if ($filters['type'] == 'summary'): ?>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 small fw-bold mb-2">TOPLAM GELİR</h6>
                            <h2 class="fw-bold mb-0">₺<?php echo number_format($summary['total_income'], 2); ?></h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-20 text-white">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 small fw-bold mb-2">TOPLAM GİDER</h6>
                            <h2 class="fw-bold mb-0">₺<?php echo number_format($summary['total_expense'], 2); ?></h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-20 text-white">
                            <i class="fas fa-arrow-trend-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <?php $profit = $summary['total_income'] - $summary['total_expense']; ?>
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 small fw-bold mb-2">NET DURUM (KAR/ZARAR)</h6>
                            <h2 class="fw-bold mb-0">₺<?php echo number_format($profit, 2); ?></h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-20 text-white">
                            <i class="fas fa-scale-balanced"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4"><h6 class="fw-bold mb-0 text-success"><i class="fas fa-hand-holding-usd me-2"></i>Gelir Detayları</h6></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <tbody>
                            <?php foreach($summary['income_details'] as $inc): ?>
                                <tr>
                                    <td class="ps-4 py-3 fw-medium"><?php echo $inc['type'] == 'due' ? 'Aidat Ödemeleri' : 'Ekstra Borç Ödemeleri'; ?></td>
                                    <td class="text-end pe-4 py-3 fw-bold text-success">₺<?php echo number_format($inc['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($summary['income_details'])): ?>
                                <tr><td colspan="2" class="text-center py-5 text-muted">Kayıt yok</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4"><h6 class="fw-bold mb-0 text-danger"><i class="fas fa-file-invoice-dollar me-2"></i>Gider Detayları</h6></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <tbody>
                            <?php foreach($summary['expense_details'] as $exp): ?>
                                <tr>
                                    <td class="ps-4 py-3 fw-medium"><?php echo e($exp['category']); ?></td>
                                    <td class="text-end pe-4 py-3 fw-bold text-danger">₺<?php echo number_format($exp['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($summary['expense_details'])): ?>
                                <tr><td colspan="2" class="text-center py-5 text-muted">Kayıt yok</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <?php if (in_array($filters['type'], ['dues', 'extra'])): ?>
                            <tr>
                                <th class="ps-4">Sakin Bilgisi</th>
                                <th>Blok / Daire</th>
                                <th>Tip</th>
                                <th class="text-end">Tahakkuk</th>
                                <th class="text-end">Ödenen</th>
                                <th class="text-end">Kalan Borç</th>
                                <th class="text-end pe-4">İşlemler</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th class="ps-4">Tarih</th>
                                <th>Kategori</th>
                                <th>Başlık</th>
                                <th class="text-end pe-4">Tutar</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-search"></i>
                                        <h5 class="fw-bold text-dark">Kayıt Bulunamadı</h5>
                                        <p class="mb-0">Seçili kriterlere uygun herhangi bir veri bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php if (in_array($filters['type'], ['dues', 'extra'])): ?>
                                <?php foreach($results as $row): ?>
                                    <tr>
                                        <td class="ps-4" data-label="Sakin">
                                            <div class="fw-bold h6 mb-0"><?php echo e($row['name']); ?></div>
                                        </td>
                                        <td data-label="Blok / Daire">
                                            <span class="premium-badge badge-block"><?php echo e($row['block_name']); ?> - <?php echo e($row['door_number']); ?></span>
                                        </td>
                                        <td data-label="Tip">
                                            <?php if($row['resident_type'] == 'tenant'): ?>
                                                <span class="premium-badge badge-tenant">Kiracı</span>
                                            <?php else: ?>
                                                <span class="premium-badge badge-owner">Ev Sahibi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-medium" data-label="Tahakkuk">₺<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td class="text-end text-success fw-medium" data-label="Ödenen">₺<?php echo number_format($row['total_paid'], 2); ?></td>
                                        <td class="text-end" data-label="Kalan Borç">
                                            <span class="fw-bold <?php echo $row['balance'] > 0 ? 'text-danger' : 'text-success'; ?>">
                                                ₺<?php echo number_format($row['balance'], 2); ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4" data-label="İşlemler">
                                            <?php if ($row['balance'] > 0): ?>
                                                <a href="<?php echo SITE_URL; ?>payment?resident_id=<?php echo $row['resident_id']; ?>&debt_type=<?php echo $filters['type']; ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                                    <i class="fas fa-plus me-1"></i> Ödeme Al
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success py-2 px-3 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i> Borç Yok
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach($results as $row): ?>
                                    <tr>
                                        <td class="ps-4" data-label="Tarih">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-light text-muted me-3" style="width: 35px; height: 35px; font-size: 0.8rem; border-radius: 8px;">
                                                    <i class="fas fa-calendar-day"></i>
                                                </div>
                                                <span class="text-muted small fw-bold"><?php echo date('d.m.Y', strtotime($row['date'])); ?></span>
                                            </div>
                                        </td>
                                        <td data-label="Kategori">
                                            <span class="premium-badge badge-block"><?php echo e($row['category']); ?></span>
                                        </td>
                                        <td data-label="Başlık" class="fw-bold"><?php echo e($row['title']); ?></td>
                                        <td class="text-end pe-4 text-danger fw-bold h6" data-label="Tutar">₺<?php echo number_format($row['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
