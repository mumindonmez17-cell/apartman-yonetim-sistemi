<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold mb-1">Ödeme İşlemleri</h2>
        <p class="text-muted small mb-0">Tahsilat işlemlerini buradan yapabilir ve geçmiş ödemeleri takip edebilirsiniz.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
        <?php 
        $export_params = $_GET;
        unset($export_params['url']);
        ?>
        <a href="<?php echo SITE_URL; ?>report/export?export_type=payments&<?php echo http_build_query($export_params); ?>" class="btn btn-outline-success border-2 shadow-sm flex-grow-1 flex-md-grow-0">
            <i class="fas fa-file-excel me-2"></i> Excel İndir
        </a>
        <button class="btn btn-primary shadow-sm flex-grow-1 flex-md-grow-0 px-4" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="fas fa-credit-card me-2"></i> Yeni Ödeme Al
        </button>
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

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0"><i class="fas fa-filter me-2 text-primary"></i> Filtrele</h6>
    </div>
    <div class="card-body px-4 pb-4">
        <form method="GET" class="row g-3">
            <input type="hidden" name="url" value="payment">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Sakin Ara</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="name" class="form-control border-start-0 ps-0" placeholder="İsim ile ara..." value="<?php echo e($filters['name']); ?>">
                </div>
            </div>
            <div class="col-md-2">
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
            <div class="col-md-2">
                <label class="form-label small fw-bold">Sakin Tipi</label>
                <select name="resident_type" class="form-select">
                    <option value="">Tüm Tipler</option>
                    <option value="tenant" <?php echo $filters['resident_type'] == 'tenant' ? 'selected' : ''; ?>>Kiracı</option>
                    <option value="owner" <?php echo $filters['resident_type'] == 'owner' ? 'selected' : ''; ?>>Ev Sahibi</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Ödeme Türü</label>
                <select name="type" class="form-select">
                    <option value="">Tüm Ödemeler</option>
                    <option value="due" <?php echo $filters['type'] == 'due' ? 'selected' : ''; ?>>Aidat</option>
                    <option value="extra" <?php echo $filters['type'] == 'extra' ? 'selected' : ''; ?>>Ekstra</option>
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

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">Son Ödemeler</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tarih</th>
                        <th>Sakin / Daire</th>
                        <th>Borç Türü</th>
                        <th>Paydaş</th>
                        <th class="text-end">Tutar</th>
                        <th class="text-end pe-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($payments)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-credit-card"></i>
                                    <h5 class="fw-bold text-dark">Ödeme Kaydı Yok</h5>
                                    <p class="mb-0">Henüz hiçbir tahsilat işlemi gerçekleştirilmemiş.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($payments as $p): ?>
                            <tr>
                                <td class="ps-4" data-label="Tarih">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-light text-muted me-3" style="width: 35px; height: 35px; font-size: 0.8rem; border-radius: 8px;">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <span class="text-muted small fw-bold"><?php echo date('d.m.Y', strtotime($p['payment_date'])); ?></span>
                                    </div>
                                </td>
                                <td data-label="Sakin / Daire">
                                    <div class="fw-bold h6 mb-0"><?php echo e($p['resident_name']); ?></div>
                                    <div class="d-flex align-items-center mt-1">
                                        <span class="premium-badge badge-block py-0 px-2 me-1" style="font-size: 0.6rem;"><?php echo e($p['block_name']); ?></span>
                                        <small class="text-muted">No: <?php echo e($p['door_number']); ?></small>
                                    </div>
                                </td>
                                <td data-label="Tür">
                                    <?php if($p['type'] == 'due'): ?>
                                        <span class="premium-badge badge-tenant">Aidat</span>
                                    <?php else: ?>
                                        <span class="premium-badge badge-owner" style="background: rgba(255, 193, 7, 0.1); color: #ffc107; border-color: rgba(255, 193, 7, 0.2);">Ekstra</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Ödeyen">
                                    <?php if($p['payment_type'] == 'tenant'): ?>
                                        <span class="text-info small fw-bold"><i class="fas fa-user me-1"></i> Kiracı</span>
                                    <?php else: ?>
                                        <span class="text-primary small fw-bold"><i class="fas fa-user-tie me-1"></i> Ev Sahibi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-success fw-bold h5 mb-0" data-label="Tutar">₺<?php echo number_format($p['amount'], 2); ?></td>
                                <td class="text-end pe-4" data-label="İşlemler">
                                    <div class="btn-group shadow-sm rounded-pill bg-light p-1">
                                        <button class="btn btn-sm btn-white border-0 text-primary edit-payment-btn" 
                                                data-id="<?php echo $p['id']; ?>" 
                                                data-amount="<?php echo $p['amount']; ?>"
                                                data-resident="<?php echo e($p['resident_name']); ?>"
                                                data-type="<?php echo $p['type'] == 'due' ? 'Aidat' : 'Ekstra'; ?>"
                                                title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?php echo SITE_URL; ?>payment/delete/<?php echo $p['id']; ?>" 
                                        class="btn btn-sm btn-white border-0 text-danger" 
                                        onclick="return confirm('Bu ödemeyi silmek istediğinize emin misiniz? Bu işlem ödenen tutarı borca geri ekleyecektir.')"
                                        title="Sil">
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

<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editPaymentForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Ödeme Tutarı Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3" id="edit_payment_info"></p>
                    <div class="mb-3">
                        <label class="form-label">Tutar</label>
                        <div class="input-group">
                            <span class="input-group-text">₺</span>
                            <input type="number" step="0.01" name="amount" id="edit_payment_amount" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?php echo SITE_URL; ?>payment/store" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Yeni Ödeme Girişi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sakin Seçin</label>
                            <select name="resident_id" class="form-select" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach($residents as $res): ?>
                                    <option value="<?php echo $res['id']; ?>" data-apartment-id="<?php echo $res['apartment_id']; ?>" data-resident-type="<?php echo $res['resident_type']; ?>">
                                        <?php echo $res['name']; ?> (<?php echo $res['block_name']; ?> - <?php echo $res['door_number']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Borç Türü</label>
                        <select id="debt_type_select" class="form-select">
                            <option value="">Borç Türü Seçin</option>
                            <option value="due">Aidat</option>
                            <option value="extra">Ekstra Borç</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ödeme Kalemi (Borç Seçin)</label>
                        <select name="assignment_id_raw" class="form-select" id="assignment_select" required>
                            <option value="">Önce Borç Kalemi Seçin</option>
                            <?php 
                            $months = [1=>'Ocak', 2=>'Şubat', 3=>'Mart', 4=>'Nisan', 5=>'Mayıs', 6=>'Haziran', 7=>'Temmuz', 8=>'Ağustos', 9=>'Eylül', 10=>'Ekim', 11=>'Kasım', 12=>'Aralık'];
                            foreach($pending as $item): 
                                $remaining_tenant = $item['tenant_amount'] - $item['paid_tenant'];
                                $remaining_owner = $item['owner_amount'] - $item['paid_owner'];
                            ?>
                                <option value="<?php echo $item['type']; ?>|<?php echo $item['assignment_id']; ?>|<?php echo $remaining_tenant; ?>|<?php echo $remaining_owner; ?>" 
                                        data-apartment-id="<?php echo $item['apartment_id']; ?>" 
                                        data-type="<?php echo $item['type']; ?>">
                                    [<?php echo $item['block_name']; ?>-<?php echo $item['door_number']; ?>] 
                                    <?php echo ($item['type'] == 'due' ? 'Aidat' : 'Ekstra'); ?>: 
                                    <?php echo $months[$item['month']]; ?> <?php echo $item['year']; ?> 
                                    (K: ₺<?php echo $remaining_tenant; ?> | E: ₺<?php echo $remaining_owner; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="assignment_type" id="assignment_type">
                        <input type="hidden" name="assignment_id" id="assignment_id">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kim Ödüyor?</label>
                            <select name="payment_type" class="form-select" id="payment_type" required>
                                <option value="tenant">Kiracı</option>
                                <option value="owner">Ev Sahibi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tutar</label>
                            <div class="input-group">
                                <span class="input-group-text">₺</span>
                                <input type="number" step="0.01" name="amount" id="payment_amount" class="form-control" required>
                            </div>
                            <small class="text-muted" id="balance_hint"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Ödemeyi Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const residentSelect = document.querySelector('select[name="resident_id"]');
const debtTypeSelect = document.getElementById('debt_type_select');
const assignmentSelect = document.getElementById('assignment_select');
const allAssignments = Array.from(assignmentSelect.options);

const prefill = <?php echo json_encode($prefill); ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (prefill.resident_id) {
        residentSelect.value = prefill.resident_id;
        if (prefill.debt_type) {
            debtTypeSelect.value = prefill.debt_type;
        }
        
        filterAssignments();
        
        const myModal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
        myModal.show();
    }
});

residentSelect.addEventListener('change', function() {
    filterAssignments();
});

debtTypeSelect.addEventListener('change', function() {
    filterAssignments();
});

// Initial call to hide everything if no resident selected
filterAssignments();

function filterAssignments() {
    const selectedOption = residentSelect.options[residentSelect.selectedIndex];
    const apartmentId = selectedOption.dataset.apartmentId;
    const residentType = selectedOption.dataset.residentType;
    const selectedDebtType = debtTypeSelect.value;
    
    // Auto-set "Kim Ödüyor?"
    if (residentType) {
        document.getElementById('payment_type').value = residentType;
    }
    
    // Reset assignment select
    let emptyText = 'Önce Sakin Seçin';
    if (apartmentId) {
        emptyText = selectedDebtType ? 'Borç Kalemi Seçin' : 'Borç Türü Seçin';
    }
    assignmentSelect.innerHTML = '<option value="">' + emptyText + '</option>';
    
    if (apartmentId && selectedDebtType) {
        allAssignments.forEach(opt => {
            if (opt.dataset.apartmentId === apartmentId && opt.dataset.type === selectedDebtType) {
                // Check responsibility for this resident type
                const parts = opt.value.split('|');
                const remainingTenant = parseFloat(parts[2]);
                const remainingOwner = parseFloat(parts[3]);

                if (residentType === 'tenant' && remainingTenant <= 0) return;
                if (residentType === 'owner' && remainingOwner <= 0) return;

                assignmentSelect.appendChild(opt.cloneNode(true));
            }
        });
    }
    
    // Clear hint and amount
    document.getElementById('balance_hint').innerText = '';
    document.getElementById('payment_amount').value = '';
}

assignmentSelect.addEventListener('change', function() {
    const val = this.value;
    if (!val) {
        document.getElementById('balance_hint').innerText = '';
        document.getElementById('payment_amount').value = '';
        return;
    }
    
    const parts = val.split('|');
    document.getElementById('assignment_type').value = parts[0];
    document.getElementById('assignment_id').value = parts[1];
    
    updateBalanceHint();
});

document.getElementById('payment_type').addEventListener('change', updateBalanceHint);

function updateBalanceHint() {
    const select = document.getElementById('assignment_select');
    if (!select.value) return;
    
    const parts = select.value.split('|');
    const type = document.getElementById('payment_type').value;
    const balance = (type === 'tenant') ? parts[2] : parts[3];
    
    document.getElementById('balance_hint').innerText = 'Kalan Borç: ₺' + balance;
    document.getElementById('payment_amount').value = balance;
}

// Edit Payment Logic
window.addEventListener('load', function() {
    const editModalEl = document.getElementById('editPaymentModal');
    if (!editModalEl) return;
    
    const editModal = new bootstrap.Modal(editModalEl);
    const editForm = document.getElementById('editPaymentForm');
    const editInfo = document.getElementById('edit_payment_info');
    const editAmountInput = document.getElementById('edit_payment_amount');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-payment-btn');
        if (btn) {
            const id = btn.dataset.id;
            const amount = btn.dataset.amount;
            const resident = btn.dataset.resident;
            const type = btn.dataset.type;

            editInfo.innerText = resident + ' - ' + type + ' ödemesi';
            editAmountInput.value = amount;
            editForm.action = '<?php echo SITE_URL; ?>payment/update/' + id;
            
            editModal.show();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
