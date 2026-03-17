<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold"><i class="fas fa-cog text-primary me-2"></i> Genel Ayarlar</h2>
            <p class="text-secondary">Sistem genelindeki temel ayarları buradan yönetebilirsiniz.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold">Site Kimliği</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo SITE_URL; ?>settings/update" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label class="form-label text-secondary fw-semibold small mb-2">SİTE / APARTMAN ADI</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-light-subtle"><i class="fas fa-building text-primary"></i></span>
                                <input type="text" name="site_name" class="form-control border-light-subtle bg-light-soft" 
                                       value="<?php echo htmlspecialchars($settings['site_name']); ?>" placeholder="Örn: Apartman Yönetim Sistemi" required>
                            </div>
                            <div class="form-text small mt-2">Bu isim giriş sayfasında, panel başlıklarında ve mesajlarda kullanılacaktır.</div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i> Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-primary text-white overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <i class="fas fa-info-circle position-absolute end-0 top-0 m-4 opacity-25" style="font-size: 5rem;"></i>
                    <h5 class="fw-bold mb-3">Bilgilendirme</h5>
                    <p class="mb-0 opacity-75 small">Site adını değiştirdiğinizde, sistemdeki tüm başlıklar ve alıcılara giden WhatsApp mesajlarındaki site adı otomatik olarak güncellenir.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
