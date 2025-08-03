<?= $this->include('station/layout/header') ?>

<div class="main-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt"></i> ड्यूटी व्यवस्थापन
        </h1>
        <a href="<?= base_url('station/duties/create') ?>" class="btn btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> नवीन ड्यूटी वाटप करा
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

    <!-- Duties Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card duties">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x mb-3"></i>
                    <h3><?= count($active_duties) ?></h3>
                    <p class="mb-0">आजच्या सक्रिय ड्यूटी</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card officers">
                <div class="card-body text-center">
                    <i class="fas fa-calendar fa-3x mb-3"></i>
                    <h3><?= count($duties) ?></h3>
                    <p class="mb-0">एकूण ड्यूटी</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card points">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <h3><?= $station_code ?></h3>
                    <p class="mb-0">पोलीस स्टेशन</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Duties Today -->
    <?php if (!empty($active_duties)): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-clock me-2"></i>आजच्या सक्रिय ड्यूटी
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>अधिकाऱ्याचे नाव</th>
                                <th>पॉइंट</th>
                                <th>सुरुवातीची वेळ</th>
                                <th>समाप्तीची वेळ</th>
                                <th>स्थिती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_duties as $duty): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($duty['officer_name']) ?></strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt me-1"></i><?= esc($duty['point_name']) ?>
                                    </td>
                                    <td><?= date('H:i', strtotime($duty['start_time'])) ?></td>
                                    <td><?= date('H:i', strtotime($duty['end_time'])) ?></td>
                                    <td>
                                        <span class="badge bg-success">सक्रिय</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- All Duties -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i><?= $station_name ?> - ड्यूटी यादी
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($duties)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th>अनुक्रमांक</th>
                                <th>अधिकाऱ्याचे नाव</th>
                                <th>पॉइंट</th>
                                <th>तारीख</th>
                                <th>वेळ</th>
                                <th>स्थिती</th>
                                <th>कृती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($duties as $duty): ?>
                                <?php
                                $currentDate = date('Y-m-d');
                                $currentTime = date('H:i:s');
                                $isActive = ($duty['date'] == $currentDate && 
                                           $duty['start_time'] <= $currentTime && 
                                           $duty['end_time'] >= $currentTime);
                                $isPast = ($duty['date'] < $currentDate || 
                                          ($duty['date'] == $currentDate && $duty['end_time'] < $currentTime));
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <strong><?= esc($duty['officer_name']) ?></strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt me-1"></i><?= esc($duty['point_name']) ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($duty['date'])) ?></td>
                                    <td>
                                        <?= date('H:i', strtotime($duty['start_time'])) ?> - 
                                        <?= date('H:i', strtotime($duty['end_time'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($isActive): ?>
                                            <span class="badge bg-success">सक्रिय</span>
                                        <?php elseif ($isPast): ?>
                                            <span class="badge bg-secondary">पूर्ण</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">नियोजित</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('station/duties/edit/' . $duty['id']) ?>" 
                                               class="btn btn-sm btn-warning" title="संपादित करा">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?= $duty['id'] ?>, '<?= esc($duty['officer_name']) ?>', '<?= esc($duty['point_name']) ?>')"
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
                    <i class="fas fa-calendar-times fa-5x text-muted mb-4"></i>
                    <h4 class="text-muted">कोणत्याही ड्यूटी सापडल्या नाहीत</h4>
                    <p class="text-muted mb-4">आपल्या पोलीस स्टेशनमध्ये अद्याप कोणत्याही ड्यूटी वाटप केल्या गेल्या नाहीत.</p>
                    <a href="<?= base_url('station/duties/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>पहिली ड्यूटी वाटप करा
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
                <h5 class="modal-title">ड्यूटी डिलीट करा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>आपण खात्री आहात की आपल्याला <strong id="officerName"></strong> ची <strong id="pointName"></strong> वरील ड्यूटी डिलीट करायची आहे?</p>
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
function confirmDelete(dutyId, officerName, pointName) {
    document.getElementById('officerName').textContent = officerName;
    document.getElementById('pointName').textContent = pointName;
    document.getElementById('deleteLink').href = '<?= base_url('station/duties/delete/') ?>' + dutyId;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<?= $this->include('station/layout/footer') ?>
