<?= $this->include('admin/layout/header') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit me-2"></i>
        ड्यूटी पॉइंट संपादित करा
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/points') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>परत जा
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    पॉइंट तपशील संपादित करा
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/points/update/' . $point['point_id']) ?>" method="post" id="pointForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zone_id" class="form-label">
                                <i class="fas fa-layer-group me-2"></i>झोन आयडी *
                            </label>
                            <input type="text" class="form-control" id="zone_id" name="zone_id" 
                                   value="<?= old('zone_id', $point['zone_id']) ?>" required 
                                   placeholder="उदा. ZONE001">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="police_station_id" class="form-label">
                                <i class="fas fa-building me-2"></i>पोलीस स्टेशन आयडी *
                            </label>
                            <input type="text" class="form-control" id="police_station_id" name="police_station_id" 
                                   value="<?= old('police_station_id', $point['police_station_id']) ?>" required 
                                   placeholder="उदा. PS001">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-map-pin me-2"></i>पॉइंट नाव *
                        </label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name', $point['name']) ?>" required 
                               placeholder="उदा. मुख्य चौक">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">
                                <i class="fas fa-crosshairs me-2"></i>अक्षांश (Latitude) *
                            </label>
                            <input type="number" step="0.00000001" class="form-control" id="latitude" name="latitude" 
                                   value="<?= old('latitude', $point['latitude']) ?>" required 
                                   placeholder="उदा. 18.5204">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-crosshairs me-2"></i>रेखांश (Longitude) *
                            </label>
                            <input type="number" step="0.00000001" class="form-control" id="longitude" name="longitude" 
                                   value="<?= old('longitude', $point['longitude']) ?>" required 
                                   placeholder="उदा. 73.8567">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="radius" class="form-label">
                            <i class="fas fa-circle me-2"></i>त्रिज्या (मीटरमध्ये) *
                        </label>
                        <input type="number" class="form-control" id="radius" name="radius" 
                               value="<?= old('radius', $point['radius']) ?>" required 
                               placeholder="उदा. 100" min="10" max="1000">
                        <small class="form-text text-muted">ड्यूटी पॉइंटच्या आसपासची त्रिज्या (10-1000 मीटर)</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('admin/points') ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>रद्द करा
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>पॉइंट अपडेट करा
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
                        <li>अचूक GPS निर्देशांक वापरा</li>
                        <li>त्रिज्या योग्य ठेवा</li>
                        <li>पॉइंट नाव स्पष्ट ठेवा</li>
                        <li>झोन आणि स्टेशन आयडी तपासा</li>
                    </ul>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-map me-2"></i>वर्तमान स्थान
                        </h6>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getCurrentLocation()">
                            <i class="fas fa-location-arrow me-2"></i>वर्तमान स्थान मिळवा
                        </button>
                        <small class="form-text text-muted d-block mt-2">
                            तुमचे वर्तमान GPS निर्देशांक मिळवण्यासाठी क्लिक करा
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current location
    window.getCurrentLocation = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(8);
                alert('स्थान यशस्वीरित्या मिळवले!');
            }, function(error) {
                alert('स्थान मिळवण्यात त्रुटी: ' + error.message);
            });
        } else {
            alert('तुमचा ब्राउझर GPS स्थान समर्थित करत नाही');
        }
    };

    // Form validation
    const form = document.getElementById('pointForm');
    
    form.addEventListener('submit', function(e) {
        const latitude = parseFloat(document.getElementById('latitude').value);
        const longitude = parseFloat(document.getElementById('longitude').value);
        const radius = parseInt(document.getElementById('radius').value);

        // Validate coordinates for Maharashtra region (approximate bounds)
        if (latitude < 15.6 || latitude > 22.0 || longitude < 72.6 || longitude > 80.9) {
            e.preventDefault();
            alert('कृपया महाराष्ट्राच्या भौगोलिक सीमेतील निर्देशांक प्रविष्ट करा');
            return false;
        }

        // Validate radius
        if (radius < 10 || radius > 1000) {
            e.preventDefault();
            alert('त्रिज्या 10 ते 1000 मीटर दरम्यान असावी');
            return false;
        }

        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?= $this->include('admin/layout/footer') ?>
