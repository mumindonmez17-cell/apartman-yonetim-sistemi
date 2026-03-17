<?php include '../app/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4"><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp Hatırlatma Ayarları</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Sistem Yapılandırması</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>whatsapp/update" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Meta Access Token</label>
                            <input type="password" name="meta_access_token" class="form-control" value="<?php echo e($settings['meta_access_token']); ?>" placeholder="EAABw..." required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Meta Phone Number ID</label>
                            <input type="text" name="meta_phone_number_id" class="form-control" value="<?php echo e($settings['meta_phone_number_id']); ?>" placeholder="106..." required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Meta Business Account ID</label>
                            <input type="text" name="meta_waba_id" class="form-control" value="<?php echo e($settings['meta_waba_id']); ?>" placeholder="108..." required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Webhook Verify Token</label>
                            <input type="text" name="webhook_verify_token" class="form-control" value="<?php echo e($settings['webhook_verify_token']); ?>" placeholder="Herhangi bir gizli metin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Template Name (Meta)</label>
                            <input type="text" name="template_name" class="form-control" value="<?php echo e($settings['template_name']); ?>" placeholder="aidat_hatirlatma">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Language Code</label>
                            <input type="text" name="language_code" class="form-control" value="<?php echo e($settings['language_code']); ?>" placeholder="tr">
                        </div>
                    </div>

                    <div class="row align-items-center mb-4">
                        <div class="col-md-4">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo $settings['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="isActive">WhatsApp Sistemi Aktif</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Gönderim Günü</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                <input type="number" name="send_day" class="form-control" min="1" max="31" value="<?php echo e($settings['send_day']); ?>" required>
                                <span class="input-group-text">Her ayın ... günü</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Gönderim Saati</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                <input type="time" name="send_time" class="form-control" value="<?php echo e($settings['send_time']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Mesaj Şablonu</label>
                        <textarea name="message_template" class="form-control" rows="5" required><?php echo e($settings['message_template']); ?></textarea>
                        <div class="form-text mt-2">
                            <strong>Desteklenen değişkenler:</strong> 
                            <span class="badge bg-light text-dark border me-1">{ad_soyad}</span>
                            <span class="badge bg-light text-dark border me-1">{daire_no}</span>
                            <span class="badge bg-light text-dark border me-1">{borc}</span>
                            <span class="badge bg-light text-dark border me-1">{donem}</span>
                            <span class="badge bg-light text-dark border me-1">{site_adi}</span>
                            <span class="badge bg-light text-dark border">{son_odeme_tarihi}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Ayarları Kaydet
                        </button>
                        <button type="button" id="sendTestMsg" class="btn btn-outline-success px-4">
                            <i class="fab fa-whatsapp me-2"></i> Test Mesajı Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Son Gönderim Kayıtları (Log)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Tip</th>
                                <th>Sakin</th>
                                <th>Telefon / Normal</th>
                                <th>Dönem</th>
                                <th>Mesaj</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo date('d.m.Y H:i', strtotime($log['sent_at'])); ?></td>
                                    <td>
                                        <?php if ($log['message_type'] === 'test'): ?>
                                            <span class="badge bg-info">Test</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Habrl.</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($log['resident_name'] ?? 'Bilinmeyen'); ?></td>
                                    <td>
                                        <div class="text-muted small"><?php echo e($log['phone']); ?></div>
                                        <div class="fw-bold"><?php echo e($log['normalized_phone'] ?? '-'); ?></div>
                                    </td>
                                    <td><?php echo e($log['period']); ?></td>
                                    <td>
                                        <small class="text-muted" title="<?php echo htmlspecialchars($log['message']); ?>">
                                            <?php echo mb_substr($log['message'], 0, 40) . '...'; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($log['status'] === 'success'): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i> Başarılı 
                                                <?php if($log['http_code']): ?>[<?php echo $log['http_code']; ?>]<?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($log['error_message'] . "\n\nResponse: " . $log['raw_response']); ?>">
                                                <i class="fas fa-times me-1"></i> Hata 
                                                <?php if($log['http_code']): ?>[<?php echo $log['http_code']; ?>]<?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Henüz kayıt bulunmamaktadır.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sendTestMsg').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Gönderiliyor...';

    fetch('<?php echo SITE_URL; ?>whatsapp/test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'csrf_token=<?php echo csrf_token(); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('İşlem sırasında bir hata oluştu.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fab fa-whatsapp me-2"></i> Test Mesajı Gönder';
    });
});
</script>

<?php include '../app/views/layout/footer.php'; ?>
