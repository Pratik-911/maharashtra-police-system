<?= $this->include('admin/layout/header') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus-circle me-2"></i>
        नवा ड्यूटी पॉइंट जोडा
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
                    पॉइंट तपशील
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/points/store') ?>" method="post" id="pointForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zone_id" class="form-label">
                                <i class="fas fa-layer-group me-2"></i>झोन आयडी *
                            </label>
                            <input type="text" class="form-control" id="zone_id" name="zone_id" 
                                   value="<?= old('zone_id') ?>" placeholder="उदा: ZONE1" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="police_station_id" class="form-label">
                                <i class="fas fa-building me-2"></i>पोलीस स्टेशन आयडी *
                            </label>
                            <input type="text" class="form-control" id="police_station_id" name="police_station_id" 
                                   value="<?= old('police_station_id') ?>" placeholder="उदा: PS001" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-tag me-2"></i>पॉइंटचे नाव *
                        </label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name') ?>" placeholder="उदा: शिवाजीनगर चौकी" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">
                                <i class="fas fa-globe me-2"></i>अक्षांश (Latitude) *
                            </label>
                            <input type="number" step="0.00000001" class="form-control" id="latitude" name="latitude" 
                                   value="<?= old('latitude') ?>" placeholder="उदा: 18.5204" required>
                            <div class="form-text">-90 ते 90 दरम्यान मूल्य प्रविष्ट करा</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-globe me-2"></i>रेखांश (Longitude) *
                            </label>
                            <input type="number" step="0.00000001" class="form-control" id="longitude" name="longitude" 
                                   value="<?= old('longitude') ?>" placeholder="उदा: 73.8567" required>
                            <div class="form-text">-180 ते 180 दरम्यान मूल्य प्रविष्ट करा</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="radius" class="form-label">
                            <i class="fas fa-circle-notch me-2"></i>त्रिज्या (मीटरमध्ये) *
                        </label>
                        <input type="number" class="form-control" id="radius" name="radius" 
                               value="<?= old('radius', 100) ?>" min="1" max="5000" required>
                        <div class="form-text">अधिकारी या त्रिज्येत असल्यास त्यांना अनुपालनात मानले जाईल (1-5000 मीटर)</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-outline-secondary me-md-2" onclick="history.back()">
                            <i class="fas fa-times me-2"></i>रद्द करा
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>पॉइंट जतन करा
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
                    <i class="fas fa-info-circle me-2"></i>
                    मार्गदर्शन
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-lightbulb me-2"></i>स्थान मिळवण्याचे मार्ग:</h6>
                    <ul class="mb-0">
                        <li>Google Maps वापरा</li>
                        <li>GPS डिव्हाइस वापरा</li>
                        <li>मोबाइल अॅप्स वापरा</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>महत्वाचे:</h6>
                    <ul class="mb-0">
                        <li>अचूक स्थान प्रविष्ट करा</li>
                        <li>त्रिज्या योग्य ठेवा</li>
                        <li>डुप्लिकेट पॉइंट्स टाळा</li>
                    </ul>
                </div>

                <div class="card bg-light">
                    <div class="card-body">
                        <h6><i class="fas fa-map me-2"></i>वर्तमान स्थान मिळवा</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="getCurrentLocation">
                            <i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा
                        </button>
                        <small class="text-muted d-block mt-2">
                            आपले वर्तमान स्थान स्वयंचलितपणे भरण्यासाठी
                        </small>
                    </div>
                </div>

                <div class="mt-3">
                    <h6><i class="fas fa-list me-2"></i>महाराष्ट्रातील प्रमुख शहरे:</h6>
                    <div class="d-grid gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation(19.0760, 72.8777, 'मुंबई')">मुंबई</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation(18.5204, 73.8567, 'पुणे')">पुणे</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation(21.1458, 79.0882, 'नागपूर')">नागपूर</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation(19.8762, 75.3433, 'औरंगाबाद')">औरंगाबाद</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current location
    document.getElementById('getCurrentLocation').addEventListener('click', function() {
        const button = this;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>स्थान मिळवत आहे...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                    document.getElementById('longitude').value = position.coords.longitude.toFixed(8);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-check me-2"></i>स्थान मिळाले!';
                    
                    setTimeout(function() {
                        button.innerHTML = '<i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा';
                    }, 2000);
                },
                function(error) {
                    alert('स्थान मिळवण्यात त्रुटी: ' + error.message);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा';
                }
            );
        } else {
            alert('आपला ब्राउझर स्थान सेवा समर्थित नाही');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा';
        }
    });
    
    // Form validation
    document.getElementById('pointForm').addEventListener('submit', function(e) {
        const latitude = parseFloat(document.getElementById('latitude').value);
        const longitude = parseFloat(document.getElementById('longitude').value);
        const radius = parseInt(document.getElementById('radius').value);
        
        if (latitude < -90 || latitude > 90) {
            e.preventDefault();
            alert('अक्षांश -90 ते 90 दरम्यान असावा');
            return false;
        }
        
        if (longitude < -180 || longitude > 180) {
            e.preventDefault();
            alert('रेखांश -180 ते 180 दरम्यान असावा');
            return false;
        }
        
        if (radius < 1 || radius > 5000) {
            e.preventDefault();
            alert('त्रिज्या 1 ते 5000 मीटर दरम्यान असावी');
            return false;
        }
    });
});

function setLocation(lat, lng, cityName) {
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
    
    // Optionally set point name if empty
    const nameField = document.getElementById('name');
    if (!nameField.value) {
        nameField.value = cityName + ' चौकी';
    }
}
</script>

<?= $this->include('admin/layout/footer') ?>
