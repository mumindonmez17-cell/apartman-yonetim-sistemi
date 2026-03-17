<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Blok Yönetimi</h2>
        <p class="text-muted small mb-0">Sitenizdeki blokları buradan yönetebilirsiniz.</p>
    </div>
    <button class="btn btn-primary shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#addBlockModal">
        <i class="fas fa-plus me-2"></i> Yeni Blok Ekle
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
                        <th class="ps-4">ID</th>
                        <th>Blok Adı</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($blocks)): ?>
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <i class="fas fa-cubes"></i>
                                    <h5 class="fw-bold text-dark">Henüz Blok Yok</h5>
                                    <p class="mb-0">Sisteme henüz bir blok tanımlanmamış.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($blocks as $block): ?>
                            <tr>
                                <td class="ps-4" data-label="ID">
                                    <span class="text-muted fw-bold">#<?php echo $block['id']; ?></span>
                                </td>
                                <td data-label="Blok Adı">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-3" style="width: 40px; height: 40px; font-size: 1rem;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <span class="fw-bold h6 mb-0"><?php echo e($block['block_name']); ?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4" data-label="İşlemler">
                                    <a href="<?php echo SITE_URL; ?>block/delete/<?php echo $block['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Bu bloğu silmek istediğinize emin misiniz? (Bağlı daireler de silinecektir)')" title="Sil">
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

<!-- Add Block Modal -->
<div class="modal fade" id="addBlockModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>block/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Blok Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Blok Adı</label>
                        <input type="text" name="block_name" class="form-control" placeholder="Örn: A Blok" required>
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
