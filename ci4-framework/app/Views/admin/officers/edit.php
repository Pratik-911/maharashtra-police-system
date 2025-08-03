<?= $this->include('admin/layout/header') ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit"></i> <?= esc($title) ?>
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
                <i class="fas fa-user-edit me-2"></i>अधिकारी माहिती संपादित करा
            </h6>
        </div>
        <div class="card-body">
            <form action="/admin/officers/update/<?= $officer['id'] ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user me-1"></i>अधिकाऱ्याचे नाव <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name', $officer['name']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="badge_no">
                                <i class="fas fa-id-badge me-1"></i>बॅज नंबर <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="badge_no" name="badge_no" 
                                   value="<?= old('badge_no', $officer['badge_no']) ?>" required>
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
                                <option value="पोलीस कॉन्स्टेबल" <?= old('rank', $officer['rank']) == 'पोलीस कॉन्स्टेबल' ? 'selected' : '' ?>>पोलीस कॉन्स्टेबल</option>
                                <option value="हेड कॉन्स्टेबल" <?= old('rank', $officer['rank']) == 'हेड कॉन्स्टेबल' ? 'selected' : '' ?>>हेड कॉन्स्टेबल</option>
                                <option value="Assistant Sub Inspector" <?= old('rank', $officer['rank']) == 'Assistant Sub Inspector' ? 'selected' : '' ?>>Assistant Sub Inspector</option>
                                <option value="Sub Inspector" <?= old('rank', $officer['rank']) == 'Sub Inspector' ? 'selected' : '' ?>>Sub Inspector</option>
                                <option value="Inspector" <?= old('rank', $officer['rank']) == 'Inspector' ? 'selected' : '' ?>>Inspector</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="police_station">
                                <i class="fas fa-building me-1"></i>पोलीस स्टेशन <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="police_station" name="police_station" 
                                   value="<?= old('police_station', $officer['police_station']) ?>" required>
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
                                   value="<?= old('mobile', $officer['mobile']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock me-1"></i>नवीन पासवर्ड
                            </label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">पासवर्ड बदलायचा नसेल तर रिकामे ठेवा</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar me-1"></i>तयार केले
                            </label>
                            <input type="text" class="form-control" 
                                   value="<?= date('d/m/Y H:i', strtotime($officer['created_at'])) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-edit me-1"></i>शेवटचे अपडेट
                            </label>
                            <input type="text" class="form-control" 
                                   value="<?= date('d/m/Y H:i', strtotime($officer['updated_at'])) ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <a href="/admin/officers" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>रद्द करा
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>बदल जतन करा
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Officer Details Summary -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>अधिकारी तपशील
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
                        <h5><?= esc($officer['name']) ?></h5>
                        <p class="text-muted"><?= esc($officer['rank']) ?></p>
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>बॅज नंबर:</strong></td>
                            <td><span class="badge badge-primary"><?= esc($officer['badge_no']) ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>मोबाइल:</strong></td>
                            <td><i class="fas fa-phone me-1"></i><?= esc($officer['mobile']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>पोलीस स्टेशन:</strong></td>
                            <td><i class="fas fa-building me-1"></i><?= esc($officer['police_station']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>स्थिती:</strong></td>
                            <td><span class="badge badge-success">सक्रिय</span></td>
                        </tr>
                    </table>
                </div>
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
