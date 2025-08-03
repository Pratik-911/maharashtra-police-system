<?= $this->include('admin/layout/header') ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus"></i> <?= esc($title) ?>
        </h1>
        <a href="/admin/officers" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-plus me-2"></i>नवीन अधिकारी जोडा
            </h6>
        </div>
        <div class="card-body">
            <form action="/admin/officers/store" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user me-1"></i>अधिकाऱ्याचे नाव <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="badge_no">
                                <i class="fas fa-id-badge me-1"></i>बॅज नंबर <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="badge_no" name="badge_no" 
                                   value="<?= old('badge_no') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rank">
                                <i class="fas fa-star me-1"></i>पद <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="rank" name="rank" required>
                                <option value="">पद निवडा</option>
                                <option value="पोलीस कॉन्स्टेबल" <?= old('rank') == 'पोलीस कॉन्स्टेबल' ? 'selected' : '' ?>>पोलीस कॉन्स्टेबल</option>
                                <option value="हेड कॉन्स्टेबल" <?= old('rank') == 'हेड कॉन्स्टेबल' ? 'selected' : '' ?>>हेड कॉन्स्टेबल</option>
                                <option value="Assistant Sub Inspector" <?= old('rank') == 'Assistant Sub Inspector' ? 'selected' : '' ?>>Assistant Sub Inspector</option>
                                <option value="Sub Inspector" <?= old('rank') == 'Sub Inspector' ? 'selected' : '' ?>>Sub Inspector</option>
                                <option value="Inspector" <?= old('rank') == 'Inspector' ? 'selected' : '' ?>>Inspector</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="police_station">
                                <i class="fas fa-building me-1"></i>पोलीस स्टेशन <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="police_station" name="police_station" required>
                                <option value="">पोलीस स्टेशन निवडा</option>
                                <?php if (isset($police_stations) && !empty($police_stations)): ?>
                                    <?php foreach ($police_stations as $station): ?>
                                        <option value="<?= esc($station['station_id']) ?>" <?= old('police_station') == $station['station_id'] ? 'selected' : '' ?>>
                                            <?= esc($station['station_name']) ?> (<?= esc($station['station_id']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Fallback options if police_stations not loaded -->
                                    <option value="PS001" <?= old('police_station') == 'PS001' ? 'selected' : '' ?>>कोल्हापूर पोलीस स्टेशन (PS001)</option>
                                    <option value="PS002" <?= old('police_station') == 'PS002' ? 'selected' : '' ?>>सांगली पोलीस स्टेशन (PS002)</option>
                                    <option value="PS003" <?= old('police_station') == 'PS003' ? 'selected' : '' ?>>सातारा पोलीस स्टेशन (PS003)</option>
                                    <option value="Nagpur Police" <?= old('police_station') == 'Nagpur Police' ? 'selected' : '' ?>>Nagpur Police</option>
                                    <option value="Mumbai Police" <?= old('police_station') == 'Mumbai Police' ? 'selected' : '' ?>>Mumbai Police</option>
                                    <option value="Pune Police" <?= old('police_station') == 'Pune Police' ? 'selected' : '' ?>>Pune Police</option>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>अधिकारी या पोलीस स्टेशनशी संबंधित असेल
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile">
                                <i class="fas fa-phone me-1"></i>मोबाइल नंबर <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" 
                                   value="<?= old('mobile') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock me-1"></i>पासवर्ड <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="form-text text-muted">किमान 6 अक्षरांचा पासवर्ड वापरा</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>महत्वाची माहिती:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>अधिकारी निवडलेल्या पोलीस स्टेशनशी संबंधित असेल</li>
                                    <li>फक्त त्या स्टेशनचे लॉगिन हे अधिकारी व्यवस्थापित करू शकतील</li>
                                    <li>बॅज नंबर युनिक असावा</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <a href="/admin/officers" class="btn btn-secondary">
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

    <!-- Police Stations Info -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-building me-2"></i>उपलब्ध पोलीस स्टेशन
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">PS001</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">कोल्हापूर पोलीस स्टेशन</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">PS002</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">सांगली पोलीस स्टेशन</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">PS003</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">सातारा पोलीस स्टेशन</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>टीप:</strong> अधिकारी निवडलेल्या पोलीस स्टेशनशी संबंधित असेल आणि फक्त त्या स्टेशनचे लॉगिन त्यांना व्यवस्थापित करू शकतील.
                </small>
            </div>
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

<?= $this->include('admin/layout/footer') ?>
