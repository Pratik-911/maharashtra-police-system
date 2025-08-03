<?= $this->include('station/layout/header') ?>

<div class="main-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus"></i> नवीन अधिकारी जोडा
        </h1>
        <a href="<?= base_url('station/officers') ?>" class="btn btn-secondary shadow-sm">
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-plus me-2"></i>अधिकारी माहिती
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('station/officers/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1"></i>अधिकाऱ्याचे नाव <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="badge_no" class="form-label">
                                <i class="fas fa-id-badge me-1"></i>बॅज नंबर <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="badge_no" name="badge_no" 
                                   value="<?= old('badge_no') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="rank" class="form-label">
                                <i class="fas fa-star me-1"></i>पद <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="rank" name="rank" required>
                                <option value="">पद निवडा</option>
                                <option value="पोलीस कॉन्स्टेबल" <?= old('rank') == 'पोलीस कॉन्स्टेबल' ? 'selected' : '' ?>>पोलीस कॉन्स्टेबल</option>
                                <option value="हेड कॉन्स्टेबल" <?= old('rank') == 'हेड कॉन्स्टेबल' ? 'selected' : '' ?>>हेड कॉन्स्टेबल</option>
                                <option value="असिस्टंट सब इन्स्पेक्टर" <?= old('rank') == 'असिस्टंट सब इन्स्पेक्टर' ? 'selected' : '' ?>>असिस्टंट सब इन्स्पेक्टर</option>
                                <option value="सब इन्स्पेक्टर" <?= old('rank') == 'सब इन्स्पेक्टर' ? 'selected' : '' ?>>सब इन्स्पेक्टर</option>
                                <option value="इन्स्पेक्टर" <?= old('rank') == 'इन्स्पेक्टर' ? 'selected' : '' ?>>इन्स्पेक्टर</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mobile" class="form-label">
                                <i class="fas fa-phone me-1"></i>मोबाइल नंबर <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" 
                                   value="<?= old('mobile') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>पासवर्ड <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">किमान 6 अक्षरांचा पासवर्ड वापरा</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="police_station" class="form-label">
                                <i class="fas fa-building me-1"></i>पोलीस स्टेशन
                            </label>
                            <input type="text" class="form-control" value="<?= $station_name ?>" readonly>
                            <div class="form-text">हे अधिकारी आपल्या स्टेशनमध्ये जोडले जातील</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('station/officers') ?>" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>रद्द करा
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>अधिकारी जतन करा
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const mobileInput = document.getElementById('mobile');
    
    // Mobile number validation
    mobileInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 15) {
            this.value = this.value.slice(0, 15);
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        const mobile = mobileInput.value;
        if (mobile.length < 10) {
            e.preventDefault();
            alert('मोबाइल नंबर किमान 10 अंकांचा असावा');
            mobileInput.focus();
        }
    });
});
</script>

<?= $this->include('station/layout/footer') ?>
