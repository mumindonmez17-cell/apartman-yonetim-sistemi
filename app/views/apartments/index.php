<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Daireler</h2>
        <p class="text-muted small mb-0">Sitenizdeki tüm daireleri buradan listeleyebilir ve yönetebilirsiniz.</p>
    </div>
    <button class="btn btn-primary shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#addApartmentModal">
        <i class="fas fa-plus me-2"></i> Yeni Daire Ekle
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
                        <th class="ps-4">Blok</th>
                        <th>Daire No</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($apartments)): ?>
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <i class="fas fa-door-closed"></i>
                                    <h5 class="fw-bold text-dark">Henüz Daire Yok</h5>
                                    <p class="mb-0">Henüz hiçbir daire kaydı bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($apartments as $apt): ?>
                            <tr>
                                <td class="ps-4" data-label="Blok">
                                    <div class="d-flex align-items-center">
                                        <span class="premium-badge badge-block me-2"><?php echo e($apt['block_name']); ?></span>
                                    </div>
                                </td>
                                <td data-label="Daire No">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-secondary bg-opacity-10 text-secondary me-2" style="width: 32px; height: 32px; font-size: 0.8rem; border-radius: 8px;">
                                            <i class="fas fa-hashtag"></i>
                                        </div>
                                        <span class="fw-bold"><?php echo e($apt['door_number']); ?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4" data-label="İşlemler">
                                    <a href="<?php echo SITE_URL; ?>apartment/delete/<?php echo $apt['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Bu daireyi silmek istediğinize emin misiniz?')" title="Sil">
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

<!-- Add Apartment Modal -->
<div class="modal fade" id="addApartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>apartment/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Daire Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Blok Seçin</label>
                        <select name="block_id" class="form-select" required>
                            <option value="">Seçiniz...</option>
                            <?php foreach($blocks as $block): ?>
                                <option value="<?php echo $block['id']; ?>"><?php echo $block['block_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Daire No</label>
                        <input type="text" name="door_number" class="form-control" placeholder="Örn: 10" required>
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
