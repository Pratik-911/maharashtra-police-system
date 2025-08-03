<?= $this->include('admin/layout/header') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit me-2"></i>
        ड्यूटी संपादित करा
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/duties') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>परत जा
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-edit me-2"></i>
                    ड्यूटी तपशील संपादित करा
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/duties/update/' . $duty['duty_id']) ?>" method="post" id="dutyForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">
                                <i class="fas fa-calendar me-2"></i>दिनांक *
                            </label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="<?= old('date', $duty['date']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="shift" class="form-label">
                                <i class="fas fa-sun me-2"></i>शिफ्ट *
                            </label>
                            <select class="form-select" id="shift" name="shift" required>
                                <option value="">शिफ्ट निवडा</option>
                                <option value="Day" <?= old('shift', $duty['shift']) == 'Day' ? 'selected' : '' ?>>दिवस</option>
                                <option value="Night" <?= old('shift', $duty['shift']) == 'Night' ? 'selected' : '' ?>>रात्र</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">
                                <i class="fas fa-clock me-2"></i>सुरुवातीची वेळ *
                            </label>
                            <input type="time" class="form-control" id="start_time" name="start_time" 
                                   value="<?= old('start_time', $duty['start_time']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">
                                <i class="fas fa-clock me-2"></i>समाप्तीची वेळ *
                            </label>
                            <input type="time" class="form-control" id="end_time" name="end_time" 
                                   value="<?= old('end_time', $duty['end_time']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="free_time_start" class="form-label">
                                <i class="fas fa-coffee me-2"></i>मोकळ्या वेळेची सुरुवात
                            </label>
                            <input type="time" class="form-control" id="free_time_start" name="free_time_start" 
                                   value="<?= old('free_time_start', $duty['free_time_start']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="free_time_end" class="form-label">
                                <i class="fas fa-coffee me-2"></i>मोकळ्या वेळेची समाप्ती
                            </label>
                            <input type="time" class="form-control" id="free_time_end" name="free_time_end" 
                                   value="<?= old('free_time_end', $duty['free_time_end']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="weekly_holiday" class="form-label">
                                <i class="fas fa-calendar-times me-2"></i>साप्ताहिक सुट्टी
                            </label>
                            <input type="text" class="form-control" id="weekly_holiday" name="weekly_holiday" 
                                   value="<?= old('weekly_holiday', $duty['weekly_holiday']) ?>" 
                                   placeholder="उदा. रविवार">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="point_id" class="form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>ठिकाण *
                            </label>
                            <select class="form-select" id="point_id" name="point_id" required>
                                <option value="">ठिकाण निवडा</option>
                                <?php foreach ($points as $point): ?>
                                    <option value="<?= $point['point_id'] ?>" 
                                            <?= old('point_id', $duty['point_id']) == $point['point_id'] ? 'selected' : '' ?>>
                                        <?= esc($point['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">
                            <i class="fas fa-comment me-2"></i>टिप्पणी
                        </label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" 
                                  placeholder="ड्यूटी संबंधी कोणतीही विशेष सूचना..."><?= old('comment', $duty['comment']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="location_tracking_enabled" 
                                   name="location_tracking_enabled" value="1" 
                                   <?= old('location_tracking_enabled', $duty['location_tracking_enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="location_tracking_enabled">
                                <i class="fas fa-map-marked-alt me-2"></i>स्थान ट्रॅकिंग सक्षम करा
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-users me-2"></i>अधिकारी निवड *
                        </label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($officers as $officer): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="officer_<?= $officer['id'] ?>" 
                                           name="officers[]" 
                                           value="<?= $officer['id'] ?>"
                                           <?= in_array($officer['id'], $assigned_officers) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="officer_<?= $officer['id'] ?>">
                                        <strong><?= esc($officer['name']) ?></strong>
                                        <small class="text-muted">(<?= esc($officer['badge_no']) ?> - <?= esc($officer['rank']) ?>)</small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="form-text text-muted">कमीतकमी एक अधिकारी निवडा</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('admin/duties') ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>रद्द करा
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>ड्यूटी अपडेट करा
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>सूचना
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>लक्षात ठेवा:</strong>
                    <ul class="mb-0 mt-2">
                        <li>सर्व आवश्यक फील्ड भरा</li>
                        <li>कमीतकमी एक अधिकारी निवडा</li>
                        <li>वेळेची तपासणी करा</li>
                        <li>ठिकाण योग्य निवडा</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('dutyForm');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const freeTimeStart = document.getElementById('free_time_start');
    const freeTimeEnd = document.getElementById('free_time_end');

    // Validate time logic
    function validateTimes() {
        if (startTime.value && endTime.value) {
            if (startTime.value >= endTime.value) {
                endTime.setCustomValidity('समाप्तीची वेळ सुरुवातीच्या वेळेपेक्षा जास्त असावी');
            } else {
                endTime.setCustomValidity('');
            }
        }

        if (freeTimeStart.value && freeTimeEnd.value) {
            if (freeTimeStart.value >= freeTimeEnd.value) {
                freeTimeEnd.setCustomValidity('मोकळ्या वेळेची समाप्ती सुरुवातीपेक्षा जास्त असावी');
            } else {
                freeTimeEnd.setCustomValidity('');
            }
        }
    }

    startTime.addEventListener('change', validateTimes);
    endTime.addEventListener('change', validateTimes);
    freeTimeStart.addEventListener('change', validateTimes);
    freeTimeEnd.addEventListener('change', validateTimes);

    // Validate officer selection
    form.addEventListener('submit', function(e) {
        const checkedOfficers = document.querySelectorAll('input[name="officers[]"]:checked');
        if (checkedOfficers.length === 0) {
            e.preventDefault();
            alert('कृपया कमीतकमी एक अधिकारी निवडा');
            return false;
        }
        
        validateTimes();
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?= $this->include('admin/layout/footer') ?>
