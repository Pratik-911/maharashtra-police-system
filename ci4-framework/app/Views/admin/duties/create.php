<!-- TEST-RENDER-CREATE-VIEW -->
<?= $this->include('admin/layout/header') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus-circle me-2"></i>
        नवी ड्यूटी वाटप करा
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
                    <i class="fas fa-calendar-plus me-2"></i>
                    ड्यूटी तपशील
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/duties/store') ?>" method="post" id="dutyForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">
                                <i class="fas fa-calendar me-2"></i>दिनांक *
                            </label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="<?= old('date', date('Y-m-d')) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="shift" class="form-label">
                                <i class="fas fa-sun me-2"></i>शिफ्ट *
                            </label>
                            <select class="form-select" id="shift" name="shift" required>
                                <option value="">शिफ्ट निवडा</option>
                                <option value="Day" <?= old('shift') == 'Day' ? 'selected' : '' ?>>दिवस</option>
                                <option value="Night" <?= old('shift') == 'Night' ? 'selected' : '' ?>>रात्र</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">
                                <i class="fas fa-clock me-2"></i>सुरुवातीची वेळ *
                            </label>
                            <input type="time" class="form-control" id="start_time" name="start_time" 
                                   value="<?= old('start_time') ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">
                                <i class="fas fa-clock me-2"></i>समाप्तीची वेळ *
                            </label>
                            <input type="time" class="form-control" id="end_time" name="end_time" 
                                   value="<?= old('end_time') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="free_time_start" class="form-label">
                                <i class="fas fa-coffee me-2"></i>मोकळ्या वेळेची सुरुवात
                            </label>
                            <input type="time" class="form-control" id="free_time_start" name="free_time_start" 
                                   value="<?= old('free_time_start') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="free_time_end" class="form-label">
                                <i class="fas fa-coffee me-2"></i>मोकळ्या वेळेची समाप्ती
                            </label>
                            <input type="time" class="form-control" id="free_time_end" name="free_time_end" 
                                   value="<?= old('free_time_end') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="weekly_holiday" class="form-label">
                                <i class="fas fa-calendar-times me-2"></i>साप्ताहिक सुट्टी
                            </label>
                            <select class="form-select" id="weekly_holiday" name="weekly_holiday">
                                <option value="">निवडा</option>
                                <option value="Sunday" <?= old('weekly_holiday') == 'Sunday' ? 'selected' : '' ?>>रविवार</option>
                                <option value="Monday" <?= old('weekly_holiday') == 'Monday' ? 'selected' : '' ?>>सोमवार</option>
                                <option value="Tuesday" <?= old('weekly_holiday') == 'Tuesday' ? 'selected' : '' ?>>मंगळवार</option>
                                <option value="Wednesday" <?= old('weekly_holiday') == 'Wednesday' ? 'selected' : '' ?>>बुधवार</option>
                                <option value="Thursday" <?= old('weekly_holiday') == 'Thursday' ? 'selected' : '' ?>>गुरुवार</option>
                                <option value="Friday" <?= old('weekly_holiday') == 'Friday' ? 'selected' : '' ?>>शुक्रवार</option>
                                <option value="Saturday" <?= old('weekly_holiday') == 'Saturday' ? 'selected' : '' ?>>शनिवार</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="point_id" class="form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>ड्यूटी पॉइंट *
                            </label>
                            <select class="form-select" id="point_id" name="point_id" required>
                                <option value="">पॉइंट निवडा</option>
                                <?php foreach ($points as $point): ?>
                                    <option value="<?= $point['point_id'] ?>" 
                                            <?= old('point_id') == $point['point_id'] ? 'selected' : '' ?>>
                                        <?= $point['name'] ?> (<?= $point['zone_id'] ?>)
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
                                  placeholder="कोणतीही अतिरिक्त माहिती..."><?= old('comment') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="location_tracking_enabled" 
                                   name="location_tracking_enabled" value="1" 
                                   <?= old('location_tracking_enabled') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="location_tracking_enabled">
                                <i class="fas fa-satellite-dish me-2"></i>
                                <strong>स्थान ट्रॅकिंग सक्षम करा</strong>
                                <small class="text-muted d-block">अधिकाऱ्यांचे लाइव्ह स्थान ट्रॅक करा</small>
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-outline-secondary me-md-2" onclick="history.back()">
                            <i class="fas fa-times me-2"></i>रद्द करा
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>ड्यूटी वाटप करा
                        </button>
                    </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    अधिकारी निवड
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="officerSearch" 
                           placeholder="अधिकारी शोधा...">
                </div>
                
                <div class="officer-selection" style="max-height: 400px; overflow-y: auto;">
                    <?php if (!empty($officers)): ?>
                        <?php foreach ($officers as $officer): ?>
                            <div class="officer-item border rounded p-2 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="officers[]" value="<?= $officer['id'] ?>" 
                                           id="officer_<?= $officer['id'] ?>">
                                    <label class="form-check-label w-100" for="officer_<?= $officer['id'] ?>">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold"><?= $officer['name'] ?></div>
                                                <small class="text-muted">
                                                    बॅज: <?= $officer['badge_no'] ?><br>
                                                    पद: <?= $officer['rank'] ?><br>
                                                    स्टेशन: <?= $officer['police_station'] ?><br>
                                                    मोबाइल: <?= $officer['mobile'] ?>
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">कोणतेही अधिकारी उपलब्ध नाहीत</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                        <i class="fas fa-check-double me-1"></i>सर्व निवडा
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAll">
                        <i class="fas fa-times me-1"></i>सर्व साफ करा
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

<script>
console.log('Script loaded');
document.addEventListener('DOMContentLoaded', function() {
    // Officer search functionality
    const searchInput = document.getElementById('officerSearch');
    const officerItems = document.querySelectorAll('.officer-item');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        officerItems.forEach(function(item) {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Select all / Clear all functionality
    document.getElementById('selectAll').addEventListener('click', function() {
        const visibleCheckboxes = document.querySelectorAll('.officer-item:not([style*="display: none"]) input[type="checkbox"]');
        visibleCheckboxes.forEach(cb => cb.checked = true);
    });
    
    document.getElementById('clearAll').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="officers[]"]');
        checkboxes.forEach(cb => cb.checked = false);
    });
    
    // Form validation
    document.getElementById('dutyForm').addEventListener('submit', function(e) {
        console.log('=== DUTY FORM SUBMIT HANDLER TRIGGERED ===');
        console.log('Form element:', this);
        console.log('Form action:', this.action);
        console.log('Form method:', this.method);
        
        const selectedOfficers = document.querySelectorAll('input[name="officers[]"]:checked');
        console.log('Selected officers count:', selectedOfficers.length);
        
        if (selectedOfficers.length === 0) {
            console.log('VALIDATION FAILED: No officers selected');
            e.preventDefault();
            alert('कृपया किमान एक अधिकारी निवडा');
            return false;
        }
        
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        console.log('Start time:', startTime, 'End time:', endTime);
        
        if (startTime && endTime && startTime >= endTime) {
            console.log('VALIDATION FAILED: Invalid time range');
            e.preventDefault();
            alert('समाप्तीची वेळ सुरुवातीच्या वेळेपेक्षा जास्त असावी');
            return false;
        }
        
        const freeStart = document.getElementById('free_time_start').value;
        const freeEnd = document.getElementById('free_time_end').value;
        console.log('Free start:', freeStart, 'Free end:', freeEnd);
        
        if (freeStart && freeEnd && freeStart >= freeEnd) {
            console.log('VALIDATION FAILED: Invalid free time range');
            e.preventDefault();
            alert('मोकळ्या वेळेची समाप्ती सुरुवातीपेक्षा जास्त असावी');
            return false;
        }
        
        console.log('=== ALL VALIDATIONS PASSED - FORM SHOULD SUBMIT ===');
        console.log('Form data will be submitted to:', this.action);
        
        // DEBUG: Capture all form data before submission
        const formData = new FormData(this);
        console.log('=== FORM DATA BEING SUBMITTED ===');
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        // DEBUG: Add a timestamp to track submission
        console.log('=== FORM SUBMISSION TIMESTAMP ===', new Date().toISOString());
        
        // DEBUG: Try to detect if form submission is actually happening
        setTimeout(() => {
            console.log('=== 2 SECONDS AFTER FORM SUBMIT - CHECK IF PAGE CHANGED ===');
            console.log('Current URL:', window.location.href);
        }, 2000);
    });
});
</script>

<?= $this->include('admin/layout/footer') ?>
