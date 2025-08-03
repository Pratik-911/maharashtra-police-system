<?= $this->include('admin/layout/header') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-satellite-dish me-2"></i>लाइव्ह ट्रॅकिंग</h2>
                <div class="btn-group">
                    <button class="btn btn-success" onclick="startLiveTracking()">
                        <i class="fas fa-play me-2"></i>ट्रॅकिंग सुरू करा
                    </button>
                    <button class="btn btn-danger" onclick="stopLiveTracking()">
                        <i class="fas fa-stop me-2"></i>ट्रॅकिंग थांबवा
                    </button>
                    <button class="btn btn-info" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>रिफ्रेश करा
                    </button>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-success">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x text-white mb-2"></i>
                            <h3 class="mb-1" id="active-officers-count"><?= count($active_officers) ?></h3>
                            <p class="text-white mb-0">सक्रिय अधिकारी</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-wifi fa-2x text-white mb-2"></i>
                            <h3 class="mb-1" id="online-officers-count"><?= count($online_officers ?? []) ?></h3>
                            <p class="text-white mb-0">ऑनलाइन अधिकारी</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-white mb-2"></i>
                            <h3 class="mb-1" id="low-compliance-count"><?= count($low_compliance_alerts ?? []) ?></h3>
                            <p class="text-white mb-0">कमी अनुपालन</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-info">
                        <div class="card-body text-center">
                            <i class="fas fa-sync-alt fa-2x text-white mb-2"></i>
                            <h3 class="mb-1" id="total-updates-count"><?= $total_updates ?? 0 ?></h3>
                            <p class="text-white mb-0">एकूण अपडेट्स</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>लाइव्ह लोकेशन ट्रॅकिंग
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="live-map" style="height: 400px; border-radius: 8px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>सक्रिय अधिकारी
                            </h5>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <?php if (!empty($active_officers)): ?>
                                <div id="officers-list">
                                    <?php foreach ($active_officers as $officer): ?>
                                        <div class="officer-item mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?= esc($officer['name']) ?></h6>
                                                    <small class="text-muted"><?= esc($officer['badge_no']) ?></small>
                                                    <br>
                                                    <small class="text-muted"><?= esc($officer['rank']) ?></small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success">ऑनलाइन</span>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= isset($officer['last_update']) ? date('H:i', strtotime($officer['last_update'])) : 'N/A' ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-success" 
                                                         style="width: <?= $officer['compliance'] ?? 0 ?>%"></div>
                                                </div>
                                                <small class="text-muted">
                                                    अनुपालन: <?= number_format($officer['compliance'] ?? 0, 1) ?>%
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                                    <h6 class="text-muted">कोणतेही सक्रिय अधिकारी नाहीत</h6>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>अलीकडील अपडेट्स
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="recent-updates">
                                <?php if (!empty($recent_updates)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="recent-updates">
                                            <thead>
                                                <tr>
                                                    <th>वेळ</th>
                                                    <th>अधिकारी</th>
                                                    <th>लोकेशन</th>
                                                    <th>अनुपालन</th>
                                                    <th>स्थिती</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_updates as $update): ?>
                                                    <tr>
                                                        <td><?= date('H:i:s', strtotime($update['timestamp'])) ?></td>
                                                        <td><?= esc($update['officer_name']) ?></td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?= number_format($update['latitude'], 4) ?>, 
                                                                <?= number_format($update['longitude'], 4) ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= ($update['compliance'] ?? 0) >= 70 ? 'bg-success' : 'bg-warning' ?>">
                                                                <?= number_format($update['compliance'] ?? 0, 1) ?>%
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">अपडेट केले</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3">
                                        <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">अलीकडील अपडेट्स उपलब्ध नाहीत</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCG-v1V6ty9GNVMB2D5VTF3HCpIhU0g8uo&callback=initMap" async defer></script>

<script>
let liveTrackingInterval;
let isTracking = false;
let map;
let officerMarkers = [];

// Initialize Google Map
function initMap() {
    // Default center (Maharashtra, India)
    const maharashtra = { lat: 19.7515, lng: 75.7139 };
    
    map = new google.maps.Map(document.getElementById('live-map'), {
        zoom: 8,
        center: maharashtra,
        mapTypeId: 'roadmap'
    });
    
    // Add officer markers
    addOfficerMarkers();
    
    // Auto-start live tracking
    startLiveTracking();
}

// Add markers for all active officers
function addOfficerMarkers() {
    // Clear existing markers
    officerMarkers.forEach(marker => marker.setMap(null));
    officerMarkers = [];
    
    // Officer data from backend
    const officers = <?= json_encode($active_officers ?? []) ?>;
    const recentUpdates = <?= json_encode($recent_updates ?? []) ?>;
    
    console.log('Recent updates:', recentUpdates);
    
    if (!recentUpdates || recentUpdates.length === 0) {
        // Show message when no location data is available - don't replace map, just show info
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 15px; text-align: center;">
                    <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i><br>
                    <strong>कोणतेही अलीकडील लोकेशन अपडेट्स नाहीत</strong><br>
                    <small>अधिकाऱ्यांनी लोकेशन ट्रॅकिंग सुरू केल्यानंतर त्यांचे स्थान येथे दिसेल</small>
                </div>
            `,
            position: map.getCenter()
        });
        infoWindow.open(map);
        return;
    }
    
    // Add markers for officers with recent location updates
    recentUpdates.forEach(update => {
        if (update.latitude && update.longitude) {
            const position = {
                lat: parseFloat(update.latitude),
                lng: parseFloat(update.longitude)
            };
            
            // Create custom marker icon based on compliance
            const compliance = update.compliance || 0;
            let iconColor = '#dc3545'; // Red for low compliance
            if (compliance >= 80) iconColor = '#28a745'; // Green for high compliance
            else if (compliance >= 60) iconColor = '#ffc107'; // Yellow for medium compliance
            
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: update.officer_name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: iconColor,
                    fillOpacity: 0.8,
                    strokeColor: '#ffffff',
                    strokeWeight: 2
                }
            });
            
            // Info window with officer details
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h6><strong>${update.officer_name}</strong></h6>
                        <p class="mb-1"><small>अनुपालन: ${compliance.toFixed(1)}%</small></p>
                        <p class="mb-1"><small>शेवटचे अपडेट: ${new Date(update.timestamp).toLocaleTimeString('mr-IN')}</small></p>
                        <p class="mb-0"><small>स्थान: ${position.lat.toFixed(4)}, ${position.lng.toFixed(4)}</small></p>
                    </div>
                `
            });
            
            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
            
            officerMarkers.push(marker);
        }
    });
    
    // Fit map to show all markers
    if (officerMarkers.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        officerMarkers.forEach(marker => {
            bounds.extend(marker.getPosition());
        });
        map.fitBounds(bounds);
    }
}

function startLiveTracking() {
    if (!isTracking) {
        isTracking = true;
        liveTrackingInterval = setInterval(refreshData, 30000); // Refresh every 30 seconds
        showAlert('लाइव्ह ट्रॅकिंग सुरू केले', 'success');
    }
}

function stopLiveTracking() {
    if (isTracking) {
        isTracking = false;
        clearInterval(liveTrackingInterval);
        showAlert('लाइव्ह ट्रॅकिंग थांबवले', 'info');
    }
}

function refreshData() {
    // Fetch live data from backend
    fetch('<?= base_url('admin/compliance/live-data') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDisplay(data);
                showAlert('डेटा रिफ्रेश केला', 'info');
            } else {
                showAlert('डेटा रिफ्रेश करताना त्रुटी', 'error');
            }
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
            showAlert('डेटा रिफ्रेश करताना त्रुटी', 'error');
        });
}

// Update display with fresh data
function updateDisplay(data) {
    // Update summary cards
    if (data.active_officers !== undefined) {
        document.querySelector('#active-officers-count').textContent = data.active_officers.length || 0;
    }
    if (data.online_officers !== undefined) {
        document.querySelector('#online-officers-count').textContent = data.online_officers.length || 0;
    }
    if (data.low_compliance_alerts !== undefined) {
        document.querySelector('#low-compliance-count').textContent = data.low_compliance_alerts.length || 0;
    }
    if (data.total_updates !== undefined) {
        document.querySelector('#total-updates-count').textContent = data.total_updates || 0;
    }
    
    // Update recent updates table
    if (data.recent_updates) {
        updateRecentUpdatesTable(data.recent_updates);
    }
    
    // Update map markers
    if (map && data.recent_updates) {
        updateMapMarkers(data.recent_updates);
    }
}

// Update recent updates table
function updateRecentUpdatesTable(updates) {
    const tbody = document.querySelector('#recent-updates tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (updates.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-3">
                    <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                    <p class="text-muted">अलीकडील अपडेट्स उपलब्ध नाहीत</p>
                </td>
            </tr>
        `;
        return;
    }
    
    updates.forEach(update => {
        const compliance = update.compliance || 0;
        const badgeClass = compliance >= 70 ? 'bg-success' : 'bg-warning';
        
        const row = `
            <tr>
                <td>${new Date(update.timestamp).toLocaleTimeString('mr-IN')}</td>
                <td>${update.officer_name}</td>
                <td>
                    <small class="text-muted">
                        ${parseFloat(update.latitude).toFixed(4)}, 
                        ${parseFloat(update.longitude).toFixed(4)}
                    </small>
                </td>
                <td>
                    <span class="badge ${badgeClass}">
                        ${compliance.toFixed(1)}%
                    </span>
                </td>
                <td>
                    <span class="badge bg-info">अपडेट केले</span>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

// Update map markers with fresh data
function updateMapMarkers(updates) {
    // Clear existing markers
    officerMarkers.forEach(marker => marker.setMap(null));
    officerMarkers = [];
    
    if (!updates || updates.length === 0) {
        // Show info window when no data available
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 15px; text-align: center;">
                    <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i><br>
                    <strong>कोणतेही अलीकडील लोकेशन अपडेट्स नाहीत</strong><br>
                    <small>अधिकाऱ्यांनी लोकेशन ट्रॅकिंग सुरू केल्यानंतर त्यांचे स्थान येथे दिसेल</small>
                </div>
            `,
            position: map.getCenter()
        });
        infoWindow.open(map);
        return;
    }
    
    // Add new markers
    updates.forEach(update => {
        if (update.latitude && update.longitude) {
            const position = {
                lat: parseFloat(update.latitude),
                lng: parseFloat(update.longitude)
            };
            
            // Create custom marker icon based on compliance
            const compliance = update.compliance || 0;
            let iconColor = '#dc3545'; // Red for low compliance
            if (compliance >= 80) iconColor = '#28a745'; // Green for high compliance
            else if (compliance >= 60) iconColor = '#ffc107'; // Yellow for medium compliance
            
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: update.officer_name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    fillColor: iconColor,
                    fillOpacity: 0.8,
                    strokeColor: '#ffffff',
                    strokeWeight: 2
                }
            });
            
            // Info window with officer details
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px;">
                        <h6><strong>${update.officer_name}</strong></h6>
                        <p class="mb-1"><small>अनुपालन: ${compliance.toFixed(1)}%</small></p>
                        <p class="mb-1"><small>शेवटचे अपडेट: ${new Date(update.timestamp).toLocaleTimeString('mr-IN')}</small></p>
                        <p class="mb-0"><small>स्थान: ${position.lat.toFixed(4)}, ${position.lng.toFixed(4)}</small></p>
                    </div>
                `
            });
            
            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
            
            officerMarkers.push(marker);
        }
    });
    
    // Fit map to show all markers if there are any
    if (officerMarkers.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        officerMarkers.forEach(marker => {
            bounds.extend(marker.getPosition());
        });
        map.fitBounds(bounds);
    }
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}

// Auto-start live tracking when page loads
document.addEventListener('DOMContentLoaded', function() {
    startLiveTracking();
});
</script>

<?= $this->include('admin/layout/footer') ?>
