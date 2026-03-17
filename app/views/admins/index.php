<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Yönetici Ayarları</h2>
        <p class="text-muted small mb-0">Sistem yöneticilerini buradan yönetebilirsiniz.</p>
    </div>
    <button class="btn btn-primary shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#addAdminModal">
        <i class="fas fa-user-plus me-2"></i> Yeni Yönetici Ekle
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

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-3 fs-4"></i>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
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
                        <th class="ps-4">Yönetici Bilgisi</th>
                        <th>Kullanıcı Adı</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($admins as $admin): ?>
                        <tr>
                            <td class="ps-4" data-label="Yönetici">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box me-3">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['username']); ?>&background=random&color=fff&size=40" class="rounded-circle shadow-sm" alt="">
                                    </div>
                                    <div>
                                        <span class="fw-bold h6 mb-0"><?php echo e($admin['username']); ?></span>
                                        <?php if($admin['id'] == $_SESSION['admin_id']): ?>
                                            <span class="premium-badge badge-tenant py-0 px-2 ms-2" style="font-size: 0.6rem;">Oturum Açık</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Kullanıcı Adı">
                                <code class="bg-light px-2 py-1 rounded text-primary fw-bold"><?php echo e($admin['username']); ?></code>
                            </td>
                            <td class="text-end pe-4" data-label="İşlemler">
                                <?php if($admin['id'] != $_SESSION['admin_id']): ?>
                                    <a href="<?php echo SITE_URL; ?>admin/delete/<?php echo $admin['id']; ?>" 
                                       class="btn btn-sm btn-white border-0 text-danger shadow-sm rounded-circle" 
                                       onclick="return confirm('Bu yöneticiyi silmek istediğinize emin misiniz?')"
                                       title="Sil">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border py-2 px-3 rounded-pill" title="Kendi hesabınızı silemezsiniz">
                                        <i class="fas fa-lock me-1"></i> Aktif Liste
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>admin/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Yönetici Hesabı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control" required placeholder="Giriş yapılacak isim">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" name="password" class="form-control" required placeholder="Karmaşık bir şifre seçin">
                        </div>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle me-2"></i> Bu kullanıcı sistemdeki tüm verilere erişebilir ve işlem yapabilir.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-modal="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Yöneticiyi Oluştur</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
