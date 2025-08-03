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

                    <!-- Google Maps Integration -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-map me-2"></i>Google Maps वरून स्थान निवडा
                        </label>
                        <div class="card">
                            <div class="card-body p-0">
                                <div id="map" style="height: 400px; width: 100%;"></div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-info btn-sm" id="getCurrentLocation">
                                            <i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="clearSelection">
                                            <i class="fas fa-times me-2"></i>निवड साफ करा
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            नकाशावर क्लिक करून पॉइंट निवडा
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">
                                <i class="fas fa-globe me-2"></i>अक्षांश (Latitude) *
                            </label>
                            <input type="number" step="0.00000001" class="form-control" id="latitude" name="latitude" 
                                   value="<?= old('latitude') ?>" placeholder="नकाशावरून निवडा" required readonly>
                            <div class="form-text">Google Maps वरून स्वयंचलितपणे भरले जाईल</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-globe me-2"></i>रेखांश (Longitude) *
                            </label>
                            <input type="number" step="0.00000001" class="form-control" id="longitude" name="longitude" 
                                   value="<?= old('longitude') ?>" placeholder="नकाशावरून निवडा" required readonly>
                            <div class="form-text">Google Maps वरून स्वयंचलितपणे भरले जाईल</div>
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

<!-- Google Maps API -->
<script>
// Load Google Maps API asynchronously
function loadGoogleMapsAPI() {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCG-v1V6ty9GNVMB2D5VTF3HCpIhU0g8uo&libraries=geometry&callback=initGoogleMaps&loading=async';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        console.error('Failed to load Google Maps API');
        document.getElementById('map').innerHTML = '<div class="alert alert-danger">Google Maps API लोड करण्यात अयशस्वी. कृपया API key तपासा.</div>';
    };
    document.head.appendChild(script);
}
</script>

<script>
// Google Maps Integration for Point Creation
class PointCreationMap {
    constructor() {
        this.map = null;
        this.marker = null;
        this.circle = null;
        this.defaultCenter = { lat: 18.5204, lng: 73.8567 }; // Pune, Maharashtra
        this.isInitialized = false;
    }
    
    initMap() {
        try {
            // Check if Google Maps is loaded
            if (typeof google === 'undefined' || !google.maps) {
                console.error('Google Maps API not loaded');
                return false;
            }
            
            // Check if map container exists
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found');
                return false;
            }
            
            // Initialize map centered on Maharashtra
            this.map = new google.maps.Map(mapContainer, {
            zoom: 12,
            center: this.defaultCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                streetViewControl: false,
                mapTypeControl: true,
                fullscreenControl: true,
                zoomControl: true
            });
            
            // Add click listener to map
            this.map.addListener('click', (event) => {
                this.setMarker(event.latLng);
            });
            
            // Update circle when radius changes
            const radiusInput = document.getElementById('radius');
            if (radiusInput) {
                radiusInput.addEventListener('input', () => {
                    this.updateCircle();
                });
            }
            
            this.setupEventListeners();
            this.isInitialized = true;
            console.log('Google Maps initialized successfully');
            return true;
            
        } catch (error) {
            console.error('Error initializing Google Maps:', error);
            return false;
        }
    }
    
    setMarker(location) {
        // Remove existing marker and circle
        if (this.marker) {
            this.marker.setMap(null);
        }
        if (this.circle) {
            this.circle.setMap(null);
        }
        
        // Create new marker
        this.marker = new google.maps.Marker({
            position: location,
            map: this.map,
            title: 'निवडलेला ड्यूटी पॉइंट',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(32, 32)
            }
        });
        
        // Update form fields - handle both LatLng objects and plain objects
        const lat = typeof location.lat === 'function' ? location.lat() : location.lat;
        const lng = typeof location.lng === 'function' ? location.lng() : location.lng;
        
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        
        // Create radius circle
        this.updateCircle();
        
        // Show success message
        this.showMessage('स्थान निवडले गेले!', 'success');
    }
    
    updateCircle() {
        if (!this.marker) return;
        
        const radius = parseInt(document.getElementById('radius').value) || 100;
        
        // Remove existing circle
        if (this.circle) {
            this.circle.setMap(null);
        }
        
        // Create new circle
        this.circle = new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.15,
            map: this.map,
            center: this.marker.getPosition(),
            radius: radius // radius in meters
        });
        
        // Adjust map bounds to show the circle
        const bounds = this.circle.getBounds();
        this.map.fitBounds(bounds);
        
        // Show radius info
        this.showRadiusInfo(radius);
    }
    
    getCurrentLocation() {
        const button = document.getElementById('getCurrentLocation');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>स्थान मिळवत आहे...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const location = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    this.map.setCenter(location);
                    this.map.setZoom(16);
                    this.setMarker(location);
                    
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-check me-2"></i>स्थान मिळाले!';
                    
                    setTimeout(() => {
                        button.innerHTML = '<i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा';
                    }, 2000);
                },
                (error) => {
                    this.showMessage('स्थान मिळवण्यात त्रुटी: ' + error.message, 'error');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा';
                }
            );
        } else {
            this.showMessage('आपला ब्राउझर स्थान सेवा समर्थित नाही', 'error');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-crosshairs me-2"></i>माझे स्थान वापरा';
        }
    }
    
    clearSelection() {
        if (this.marker) {
            this.marker.setMap(null);
            this.marker = null;
        }
        if (this.circle) {
            this.circle.setMap(null);
            this.circle = null;
        }
        
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        
        this.showMessage('निवड साफ केली गेली', 'info');
    }
    
    setLocation(lat, lng, name) {
        const location = { lat: lat, lng: lng };
        this.map.setCenter(location);
        this.map.setZoom(14);
        this.setMarker(location);
        this.showMessage(name + ' स्थान निवडले गेले', 'success');
    }
    
    showMessage(message, type) {
        // Create or update message element
        let messageEl = document.getElementById('mapMessage');
        if (!messageEl) {
            messageEl = document.createElement('div');
            messageEl.id = 'mapMessage';
            messageEl.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 10px 15px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
                z-index: 1000;
                transition: opacity 0.3s;
            `;
            document.body.appendChild(messageEl);
        }
        
        // Set message and color based on type
        messageEl.textContent = message;
        switch(type) {
            case 'success':
                messageEl.style.backgroundColor = '#28a745';
                break;
            case 'error':
                messageEl.style.backgroundColor = '#dc3545';
                break;
            case 'info':
                messageEl.style.backgroundColor = '#17a2b8';
                break;
            default:
                messageEl.style.backgroundColor = '#6c757d';
        }
        
        messageEl.style.opacity = '1';
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            messageEl.style.opacity = '0';
        }, 3000);
    }
    
    showRadiusInfo(radius) {
        const area = Math.PI * radius * radius; // Area in square meters
        const areaText = area > 10000 ? 
            (area / 10000).toFixed(2) + ' हेक्टर' : 
            area.toFixed(0) + ' चौ.मी.';
            
        this.showMessage(`त्रिज्या: ${radius}मी, क्षेत्रफळ: ${areaText}`, 'info');
    }
    
    setupEventListeners() {
        // Get current location button
        const getCurrentBtn = document.getElementById('getCurrentLocation');
        if (getCurrentBtn) {
            getCurrentBtn.addEventListener('click', () => {
                this.getCurrentLocation();
            });
        }
        
        // Clear selection button
        const clearBtn = document.getElementById('clearSelection');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearSelection();
            });
        }
    }
}

// Global callback function for Google Maps API
function initGoogleMaps() {
    console.log('Google Maps API loaded, initializing map...');
    try {
        // Wait a bit for DOM to be fully ready
        setTimeout(() => {
            window.pointMap = new PointCreationMap();
            const success = window.pointMap.initMap();
            if (success) {
                console.log('Google Maps initialized successfully');
            } else {
                throw new Error('Map initialization failed');
            }
        }, 100);
    } catch (error) {
        console.error('Error in Google Maps callback:', error);
        // Fallback: still allow form to work without maps
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.innerHTML = '<div class="alert alert-warning p-4 text-center"><i class="fas fa-exclamation-triangle me-2"></i>Google Maps लोड करण्यात त्रुटी. कृपया पुन्हा प्रयत्न करा.<br><small class="text-muted mt-2">फॉर्म अजूनही काम करेल - अक्षांश आणि रेखांश मॅन्युअली भरा.</small></div>';
        }
    }
}

// Global functions for city buttons
function setLocation(lat, lng, name) {
    if (window.pointMap && window.pointMap.isInitialized) {
        window.pointMap.setLocation(lat, lng, name);
    } else {
        // Fallback if maps not loaded
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        alert(name + ' स्थान निवडले गेले');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing point creation page...');
    
    // Load Google Maps API
    loadGoogleMapsAPI();
    
    // Form validation (independent of Google Maps)
    const pointForm = document.getElementById('pointForm');
    if (!pointForm) {
        console.error('Point form not found');
        return;
    }
    
    console.log('Point form found, setting up validation...');
    
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
</script>

<?= $this->include('admin/layout/footer') ?>
