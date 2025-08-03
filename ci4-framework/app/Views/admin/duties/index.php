<?= $this->include('admin/layout/header') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-clipboard-list me-2"></i>ड्यूटी व्यवस्थापन</h2>
                <a href="<?= base_url('admin/duties/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>नवीन ड्यूटी नियुक्त करा
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">सर्व ड्यूटी</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($duties)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ड्यूटी ID</th>
                                        <th>तारीख</th>
                                        <th>शिफ्ट</th>
                                        <th>पॉइंट</th>
                                        <th>अधिकारी संख्या</th>
                                        <th>स्थिती</th>
                                        <th>कृती</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($duties as $duty): ?>
                                        <tr>
                                            <td><?= esc($duty['duty_id']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($duty['date'])) ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= esc($duty['shift']) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($duty['point_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= esc($duty['officer_count'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $duty['status'] ?? 'scheduled';
                                                $statusClass = match($status) {
                                                    'active' => 'bg-success',
                                                    'completed' => 'bg-primary',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-warning'
                                                };
                                                $statusText = match($status) {
                                                    'active' => 'सक्रिय',
                                                    'completed' => 'पूर्ण',
                                                    'cancelled' => 'रद्द',
                                                    default => 'नियोजित'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('admin/duties/view/' . $duty['duty_id']) ?>" 
                                                       class="btn btn-outline-info" title="तपशील पहा">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('admin/duties/edit/' . $duty['duty_id']) ?>" 
                                                       class="btn btn-outline-warning" title="संपादित करा">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="confirmDelete(<?= $duty['duty_id'] ?>)" 
                                                            title="हटवा">
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
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">कोणत्याही ड्यूटी सापडल्या नाहीत</h5>
                            <p class="text-muted">नवीन ड्यूटी नियुक्त करण्यासाठी वरील बटण दाबा</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(dutyId) {
    if (confirm('तुम्हाला खरोखर ही ड्यूटी हटवायची आहे का?')) {
        window.location.href = '<?= base_url('admin/duties/delete/') ?>' + dutyId;
    }
}
</script>

<?= $this->include('admin/layout/footer') ?>
