<?= $this->include('station/layout/header') ?>

<div class="main-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-plus"></i> नवीन ड्यूटी वाटप करा
        </h1>
        <a href="<?= base_url('station/duties') ?>" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> परत जा
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>त्रुटी!</strong> कृपया खालील समस्या सोडवा:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-plus me-2"></i>ड्यूटी तपशील
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('station/duties/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="officer_id" class="form-label">
                                <i class="fas fa-user me-1"></i>अधिकारी निवडा <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="officer_id" name="officer_id" required>
                                <option value="">अधिकारी निवडा</option>
                                <?php foreach ($officers as $officer): ?>
                                    <option value="<?= $officer['id'] ?>" <?= old('officer_id') == $officer['id'] ? 'selected' : '' ?>>
                                        <?= esc($officer['name']) ?> (<?= esc($officer['badge_no']) ?>) - <?= esc($officer['rank']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="point_id" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>पॉइंट निवडा <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="point_id" name="point_id" required>
                                <option value="">पॉइंट निवडा</option>
                                <?php foreach ($points as $point): ?>
                                    <option value="<?= $point['id'] ?>" <?= old('point_id') == $point['id'] ? 'selected' : '' ?>>
                                        <?= esc($point['name']) ?> - <?= esc($point['location']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date" class="form-label">
                                <i class="fas fa-calendar me-1"></i>तारीख <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="<?= old('date', date('Y-m-d')) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="start_time" class="form-label">
                                <i class="fas fa-clock me-1"></i>सुरुवातीची वेळ <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control" id="start_time" name="start_time" 
                                   value="<?= old('start_time') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="end_time" class="form-label">
                                <i class="fas fa-clock me-1"></i>समाप्तीची वेळ <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control" id="end_time" name="end_time" 
                                   value="<?= old('end_time') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-building me-1"></i>पोलीस स्टेशन
                            </label>
                            <input type="text" class="form-control" value="<?= $station_name ?>" readonly>
                            <div class="form-text">ही ड्यूटी आपल्या स्टेशनसाठी वाटप केली जाईल</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-info-circle me-1"></i>सूचना
                            </label>
                            <div class="alert alert-info mb-0">
                                <small>
                                    <i class="fas fa-lightbulb me-1"></i>
                                    समाप्तीची वेळ सुरुवातीच्या वेळेनंतर असावी आणि अधिकाऱ्याची त्या वेळेत दुसरी ड्यूटी नसावी.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('station/duties') ?>" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>रद्द करा
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>ड्यूटी वाटप करा
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>उपलब्ध अधिकारी
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($officers)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>नाव</th>
                                        <th>बॅज नंबर</th>
                                        <th>पद</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($officers, 0, 5) as $officer): ?>
                                        <tr>
                                            <td><?= esc($officer['name']) ?></td>
                                            <td><span class="badge bg-primary"><?= esc($officer['badge_no']) ?></span></td>
                                            <td><?= esc($officer['rank']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($officers) > 5): ?>
                            <small class="text-muted">आणि <?= count($officers) - 5 ?> अधिक अधिकारी...</small>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-user-times fa-3x text-muted mb-2"></i>
                            <p class="text-muted">कोणतेही अधिकारी उपलब्ध नाहीत</p>
                            <a href="<?= base_url('station/officers/create') ?>" class="btn btn-sm btn-primary">
                                अधिकारी जोडा
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt me-2"></i>उपलब्ध पॉइंट्स
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($points)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>नाव</th>
                                        <th>स्थान</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($points, 0, 5) as $point): ?>
                                        <tr>
                                            <td><?= esc($point['name']) ?></td>
                                            <td><?= esc($point['location']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($points) > 5): ?>
                            <small class="text-muted">आणि <?= count($points) - 5 ?> अधिक पॉइंट्स...</small>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-map-marker-times fa-3x text-muted mb-2"></i>
                            <p class="text-muted">कोणतेही पॉइंट्स उपलब्ध नाहीत</p>
                            <a href="<?= base_url('station/points/create') ?>" class="btn btn-sm btn-primary">
                                पॉइंट जोडा
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation and enhancements
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const dateInput = document.getElementById('date');
    
    // Set minimum date to today
    dateInput.min = new Date().toISOString().split('T')[0];
    
    // Time validation
    function validateTime() {
        if (startTimeInput.value && endTimeInput.value) {
            if (startTimeInput.value >= endTimeInput.value) {
                endTimeInput.setCustomValidity('समाप्तीची वेळ सुरुवातीच्या वेळेनंतर असावी');
            } else {
                endTimeInput.setCustomValidity('');
            }
        }
    }
    
    startTimeInput.addEventListener('change', validateTime);
    endTimeInput.addEventListener('change', validateTime);
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        validateTime();
        if (!endTimeInput.checkValidity()) {
            e.preventDefault();
            alert('कृपया योग्य वेळ प्रविष्ट करा');
        }
    });
});
</script>

<?= $this->include('station/layout/footer') ?>
