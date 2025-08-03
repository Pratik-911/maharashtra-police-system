<?= $this->include('station/layout/header') ?>

<div class="main-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marker-plus"></i> नवीन पॉइंट जोडा
        </h1>
        <a href="<?= base_url('station/points') ?>" class="btn btn-secondary shadow-sm">
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
                <i class="fas fa-map-marker-plus me-2"></i>पॉइंट तपशील
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('station/points/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>पॉइंटचे नाव <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="radius" class="form-label">
                                <i class="fas fa-circle me-1"></i>त्रिज्या (मीटर) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="radius" name="radius" 
                                   value="<?= old('radius', 100) ?>" min="10" max="5000" required>
                            <div class="form-text">डिफॉल्ट: 100 मीटर</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="latitude" class="form-label">
                                <i class="fas fa-globe me-1"></i>अक्षांश (Latitude) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="any" class="form-control" id="latitude" name="latitude" 
                                   value="<?= old('latitude') ?>" required>
                            <div class="form-text">उदा: 16.7050</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-globe me-1"></i>रेखांश (Longitude) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="any" class="form-control" id="longitude" name="longitude" 
                                   value="<?= old('longitude') ?>" required>
                            <div class="form-text">उदा: 74.2433</div>
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
                            <div class="form-text">हा पॉइंट आपल्या स्टेशनमध्ये जोडला जाईल</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-map me-1"></i>नकाशा सहाय्य
                            </label>
                            <button type="button" class="btn btn-info w-100" onclick="getCurrentLocation()">
                                <i class="fas fa-crosshairs me-1"></i>माझे वर्तमान स्थान वापरा
                            </button>
                            <div class="form-text">GPS वापरून वर्तमान निर्देशांक मिळवा</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('station/points') ?>" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>रद्द करा
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>पॉइंट जतन करा
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Map Preview -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-map me-2"></i>स्थान पूर्वावलोकन
            </h6>
        </div>
        <div class="card-body">
            <div id="map" style="height: 300px; width: 100%;"></div>
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    निर्देशांक प्रविष्ट केल्यानंतर नकाशावर स्थान दिसेल
                </small>
            </div>
        </div>
    </div>
</div>

<script>
let map;
let marker;

// Initialize map
function initMap() {
    // Default to Kolhapur coordinates
    const defaultLocation = {lat: 16.7050, lng: 74.2433};
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: defaultLocation
    });
    
    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true,
        title: 'पॉइंट स्थान'
    });
    
    // Update coordinates when marker is dragged
    marker.addListener('dragend', function() {
        const position = marker.getPosition();
        document.getElementById('latitude').value = position.lat().toFixed(6);
        document.getElementById('longitude').value = position.lng().toFixed(6);
    });
    
    // Update marker when coordinates are entered manually
    document.getElementById('latitude').addEventListener('change', updateMarker);
    document.getElementById('longitude').addEventListener('change', updateMarker);
}

function updateMarker() {
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    
    if (!isNaN(lat) && !isNaN(lng)) {
        const position = {lat: lat, lng: lng};
        marker.setPosition(position);
        map.setCenter(position);
    }
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            
            const currentPosition = {lat: lat, lng: lng};
            marker.setPosition(currentPosition);
            map.setCenter(currentPosition);
            map.setZoom(16);
            
            alert('वर्तमान स्थान यशस्वीरित्या मिळाले!');
        }, function() {
            alert('स्थान मिळवता आले नाही. कृपया निर्देशांक मॅन्युअली प्रविष्ट करा.');
        });
    } else {
        alert('आपला ब्राउझर GPS सपोर्ट करत नाही.');
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        
        if (isNaN(lat) || isNaN(lng)) {
            e.preventDefault();
            alert('कृपया योग्य अक्षांश आणि रेखांश प्रविष्ट करा');
        }
        
        if (lat < -90 || lat > 90) {
            e.preventDefault();
            alert('अक्षांश -90 ते 90 च्या दरम्यान असावा');
        }
        
        if (lng < -180 || lng > 180) {
            e.preventDefault();
            alert('रेखांश -180 ते 180 च्या दरम्यान असावा');
        }
    });
});
</script>

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCG-v1V6ty9GNVMB2D5VTF3HCpIhU0g8uo&libraries=places&callback=initMap"></script>

<?= $this->include('station/layout/footer') ?>
