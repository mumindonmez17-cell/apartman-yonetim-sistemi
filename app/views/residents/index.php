<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Site Sakinleri</h2>
        <p class="text-muted small mb-0">Toplam <?php echo count($residents); ?> sakin kayıtlıdır.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        <?php 
        $export_params = $_GET;
        unset($export_params['url']);
        ?>
        <a href="<?php echo SITE_URL; ?>report/export?export_type=residents&<?php echo http_build_query($export_params); ?>" class="btn btn-outline-success border-2 shadow-sm flex-grow-1 flex-md-grow-0">
            <i class="fas fa-file-excel me-2"></i> Excel İndir
        </a>
        <button class="btn btn-outline-primary border-2 shadow-sm flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#ratioModal">
            <i class="fas fa-percent me-2"></i> Oranlar
        </button>
        <button class="btn btn-primary shadow-sm flex-grow-1 flex-md-grow-0 px-4" data-bs-toggle="modal" data-bs-target="#addResidentModal">
            <i class="fas fa-user-plus me-2"></i> Yeni Sakin
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
            <input type="hidden" name="url" value="resident">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="name" class="form-control border-start-0 ps-0" placeholder="İsim ile ara..." value="<?php echo e($filters['name']); ?>">
                </div>
            </div>
            <div class="col-md-3">
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
                <select name="resident_type" class="form-select">
                    <option value="">Tüm Tipler</option>
                    <option value="tenant" <?php echo $filters['resident_type'] == 'tenant' ? 'selected' : ''; ?>>Kiracı</option>
                    <option value="owner" <?php echo $filters['resident_type'] == 'owner' ? 'selected' : ''; ?>>Ev Sahibi</option>
                </select>
            </div>
            <div class="col-md-3">
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
            <div><?php echo e($_SESSION['success']); unset($_SESSION['success']); ?></div>
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
                        <th class="ps-4">Sakin Adı</th>
                        <th>Telefon</th>
                        <th>Blok / Daire</th>
                        <th>Sakin Tipi</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($residents)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <h5 class="fw-bold text-dark">Sakin Bulunamadı</h5>
                                    <p class="mb-0">Arama kriterlerinize uygun sakin kaydı bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($residents as $res): ?>
                            <tr>
                                <td class="ps-4" data-label="Sakin Adı">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($res['name']); ?>&background=random" class="rounded-circle me-3" width="35" alt="">
                                        <span class="fw-bold h6 mb-0"><?php echo htmlspecialchars($res['name']); ?></span>
                                    </div>
                                </td>
                                <td data-label="Telefon">
                                    <?php if($res['phone']): ?>
                                        <a href="tel:<?php echo $res['phone']; ?>" class="text-decoration-none text-muted small">
                                            <i class="fas fa-phone-alt me-2 text-primary opacity-50"></i><?php echo htmlspecialchars($res['phone']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Blok / Daire">
                                    <span class="premium-badge badge-block me-1"><?php echo htmlspecialchars($res['block_name']); ?></span>
                                    <span class="fw-bold text-dark small">No: <?php echo e($res['door_number']); ?></span>
                                </td>
                                <td data-label="Tip">
                                    <?php if($res['resident_type'] == 'tenant'): ?>
                                        <span class="premium-badge badge-tenant">Kiracı</span>
                                    <?php else: ?>
                                        <span class="premium-badge badge-owner">Ev Sahibi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4" data-label="İşlemler">
                                    <div class="btn-group shadow-sm rounded-pill bg-light p-1">
                                        <button class="btn btn-sm btn-white border-0 text-primary edit-resident-btn" 
                                                data-id="<?php echo $res['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($res['name']); ?>"
                                                data-phone="<?php echo htmlspecialchars($res['phone']); ?>"
                                                data-block="<?php echo $res['block_id']; ?>"
                                                data-apartment="<?php echo $res['apartment_id']; ?>"
                                                data-type="<?php echo $res['resident_type']; ?>"
                                                title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>resident/delete/<?php echo $res['id']; ?>" class="btn btn-sm btn-white border-0 text-danger" onclick="return confirm('Bu sakini silmek istediğinize emin misiniz?')" title="Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Resident Modal -->
<div class="modal fade" id="addResidentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>resident/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Sakin Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Blok</label>
                            <select name="block_id" class="form-select" id="modal_block_id" required>
                                <option value="">Seçiniz</option>
                                <?php foreach($blocks as $block): ?>
                                    <option value="<?php echo $block['id']; ?>"><?php echo $block['block_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Daire</label>
                            <select name="apartment_id" class="form-select" id="modal_apartment_id" required>
                                <option value="">Önce Blok Seçin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sakin Tipi</label>
                        <select name="resident_type" class="form-select" required>
                            <option value="tenant">Kiracı</option>
                            <option value="owner">Ev Sahibi</option>
                        </select>
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

<!-- Edit Resident Modal -->
<div class="modal fade" id="editResidentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editResidentForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Sakin Bilgilerini Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Blok</label>
                            <select name="block_id" class="form-select" id="edit_block_id" required>
                                <option value="">Seçiniz</option>
                                <?php foreach($blocks as $block): ?>
                                    <option value="<?php echo $block['id']; ?>"><?php echo $block['block_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Daire</label>
                            <select name="apartment_id" class="form-select" id="edit_apartment_id" required>
                                <option value="">Önce Blok Seçin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sakin Tipi</label>
                        <select name="resident_type" id="edit_resident_type" class="form-select" required>
                            <option value="tenant">Kiracı</option>
                            <option value="owner">Ev Sahibi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Ratio Modal -->
<div class="modal fade" id="ratioModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo SITE_URL; ?>resident/ratio" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Ödeme Oranlarını Belirle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Daire Seçin</label>
                        <select name="apartment_id" class="form-select" required>
                            <option value="">Seçiniz...</option>
                            <?php foreach($apartments as $apt): ?>
                                <option value="<?php echo $apt['id']; ?>"><?php echo $apt['block_name']; ?> - No: <?php echo $apt['door_number']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Aidat Ödeme Oranları</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Kiracı Payı (%)</label>
                            <input type="number" name="tenant_ratio" class="form-control" value="100" min="0" max="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Ev Sahibi Payı (%)</label>
                            <input type="number" name="owner_ratio" class="form-control" value="0" min="0" max="100" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Ekstra Borç Ödeme Oranları</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Kiracı Payı (%)</label>
                            <input type="number" name="extra_tenant_ratio" class="form-control" value="0" min="0" max="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Ev Sahibi Payı (%)</label>
                            <input type="number" name="extra_owner_ratio" class="form-control" value="100" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-2"></i> Bu oranlar, yeni aidat veya ekstra borç oluşturulduğunda tutarın sakinler arasında nasıl paylaştırılacağını belirler.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Oranları Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Dynamic apartment filtering for modal (simple version)
const apartments = <?php echo json_encode($apartments); ?>;
document.getElementById('modal_block_id').addEventListener('change', function() {
    const blockId = this.value;
    const aptSelect = document.getElementById('modal_apartment_id');
    aptSelect.innerHTML = '<option value="">Seçiniz</option>';
    
    apartments.filter(a => a.block_id == blockId).forEach(a => {
        const opt = document.createElement('option');
        opt.value = a.id;
        opt.text = 'No: ' + a.door_number;
        aptSelect.appendChild(opt);
    });
});

// Edit Resident Functionality
document.querySelectorAll('.edit-resident-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const phone = this.dataset.phone;
        const blockId = this.dataset.block;
        const apartmentId = this.dataset.apartment;
        const type = this.dataset.type;

        document.getElementById('editResidentForm').action = '<?php echo SITE_URL; ?>resident/update/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('edit_block_id').value = blockId;
        document.getElementById('edit_resident_type').value = type;

        // Trigger block change to populate apartments
        const editAptSelect = document.getElementById('edit_apartment_id');
        editAptSelect.innerHTML = '<option value="">Seçiniz</option>';
        apartments.filter(a => a.block_id == blockId).forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.text = 'No: ' + a.door_number;
            if(a.id == apartmentId) opt.selected = true;
            editAptSelect.appendChild(opt);
        });

        new bootstrap.Modal(document.getElementById('editResidentModal')).show();
    });
});

document.getElementById('edit_block_id').addEventListener('change', function() {
    const blockId = this.value;
    const aptSelect = document.getElementById('edit_apartment_id');
    aptSelect.innerHTML = '<option value="">Seçiniz</option>';
    apartments.filter(a => a.block_id == blockId).forEach(a => {
        const opt = document.createElement('option');
        opt.value = a.id;
        opt.text = 'No: ' + a.door_number;
        aptSelect.appendChild(opt);
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
