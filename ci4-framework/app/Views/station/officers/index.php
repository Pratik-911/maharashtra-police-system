<?= $this->include('station/layout/header') ?>

<div class="main-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> अधिकारी व्यवस्थापन
        </h1>
        <a href="<?= base_url('station/officers/create') ?>" class="btn btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> नवीन अधिकारी जोडा
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Officers Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card officers">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3><?= count($officers) ?></h3>
                    <p class="mb-0">एकूण अधिकारी</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card duties">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <h3><?= $station_code ?></h3>
                    <p class="mb-0">पोलीस स्टेशन</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Officers List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i><?= $station_name ?> - अधिकारी यादी
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($officers)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th>अनुक्रमांक</th>
                                <th>नाव</th>
                                <th>बॅज नंबर</th>
                                <th>पद</th>
                                <th>मोबाइल</th>
                                <th>तयार केले</th>
                                <th>कृती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($officers as $officer): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <strong><?= esc($officer['name']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?= esc($officer['badge_no']) ?></span>
                                    </td>
                                    <td><?= esc($officer['rank']) ?></td>
                                    <td>
                                        <i class="fas fa-phone me-1"></i><?= esc($officer['mobile']) ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($officer['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('station/officers/edit/' . $officer['id']) ?>" 
                                               class="btn btn-sm btn-warning" title="संपादित करा">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?= $officer['id'] ?>, '<?= esc($officer['name']) ?>')"
                                                    title="डिलीट करा">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-5x text-muted mb-4"></i>
                    <h4 class="text-muted">कोणतेही अधिकारी सापडले नाहीत</h4>
                    <p class="text-muted mb-4">आपल्या पोलीस स्टेशनमध्ये अद्याप कोणतेही अधिकारी जोडले गेले नाहीत.</p>
                    <a href="<?= base_url('station/officers/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>पहिला अधिकारी जोडा
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">अधिकारी डिलीट करा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>आपण खात्री आहात की आपल्याला <strong id="officerName"></strong> हा अधिकारी डिलीट करायचा आहे?</p>
                <p class="text-danger"><small>ही क्रिया पूर्ववत करता येणार नाही.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करा</button>
                <a href="#" id="deleteLink" class="btn btn-danger">डिलीट करा</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(officerId, officerName) {
    document.getElementById('officerName').textContent = officerName;
    document.getElementById('deleteLink').href = '<?= base_url('station/officers/delete/') ?>' + officerId;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<?= $this->include('station/layout/footer') ?>
