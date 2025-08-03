<?= $this->include('officer/layout/header') ?>

<!-- Officer Info Card -->
<div class="row">
    <div class="col-12">
        <div class="duty-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-user-shield me-2"></i>
                        <?= $officer_name ?>
                    </h4>
                    <p class="mb-0">
                        <strong>बॅज नं:</strong> <?= $officer_badge ?> | 
                        <strong>पद:</strong> <?= $officer_rank ?><br>
                        <strong>स्टेशन:</strong> <?= $officer_station ?>
                    </p>
                </div>
                <div class="text-end">
                    <div class="h5 mb-0"><?= date('d/m/Y') ?></div>
                    <div class="h6 mb-0"><?= date('H:i') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Duty Card -->
<div class="row">
    <div class="col-12">
        <?php if ($active_duty): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        सक्रिय ड्यूटी
                        <span class="badge bg-success ms-2">चालू आहे</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong><i class="fas fa-map-marker-alt me-2"></i>स्थान:</strong>
                                <div><?= $active_duty['point_name'] ?></div>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-clock me-2"></i>वेळ:</strong>
                                <div>
                                    <?= date('H:i', strtotime($active_duty['start_time'])) ?> - 
                                    <?= date('H:i', strtotime($active_duty['end_time'])) ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong><i class="fas fa-sun me-2"></i>शिफ्ट:</strong>
                                <span class="badge <?= $active_duty['shift'] == 'Day' ? 'bg-warning text-dark' : 'bg-info' ?>">
                                    <?= $active_duty['shift'] == 'Day' ? 'दिवस' : 'रात्र' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong><i class="fas fa-satellite-dish me-2"></i>स्थान ट्रॅकिंग:</strong>
                                <?php if ($active_duty['location_tracking_enabled']): ?>
                                    <span class="badge bg-success">सक्रिय</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">निष्क्रिय</span>
                                <?php endif; ?>
                                
                                <!-- Always include hidden inputs for JavaScript -->
                                <input type="hidden" id="locationTrackingEnabled" value="<?= !empty($active_duty) && $active_duty['location_tracking_enabled'] ? '1' : '0' ?>">
                                <input type="hidden" id="currentDutyId" value="<?= $active_duty['duty_id'] ?? '' ?>">
                                <input type="hidden" id="currentOfficerId" value="<?= session()->get('officer_id') ?? '' ?>">
                            </div>
                            
                            <?php if ($active_duty['location_tracking_enabled']): ?>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-success btn-sm" id="startTracking">
                                        <i class="fas fa-play me-2"></i>ट्रॅकिंग सुरू करा
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm d-none" id="stopTracking">
                                        <i class="fas fa-stop me-2"></i>ट्रॅकिंग बंद करा
                                    </button>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>
                                        स्थान ट्रॅकिंग सुरू करण्यासाठी ब्राउझरला परवानगी द्या. 
                                        आपले स्थान केवळ ड्यूटी दरम्यान ट्रॅक केले जाईल.
                                    </small>
                                </div>
                                
                                <!-- Location Status Display -->
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-map-marker-alt me-2"></i>स्थान स्थिती
                                        </h6>
                                        <div id="locationStatus" class="alert alert-secondary">
                                            <div id="locationStatusText">
                                                <i class="fas fa-question-circle me-2"></i>स्थान: अज्ञात
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Compliance Tracking Speedometer -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-tachometer-alt me-2"></i>
                                        अनुपालन ट्रॅकिंग (Compliance Tracking)
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Speedometer -->
                                            <div class="compliance-speedometer" id="complianceSpeedometer">
                                                <canvas id="speedometerCanvas" width="200" height="120"></canvas>
                                                <div class="speedometer-value" id="speedometerValue">
                                                    <span class="percentage">0%</span>
                                                    <small class="status">डेटा लोड होत आहे...</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Compliance Details -->
                                            <div class="compliance-details">
                                                <div class="row text-start">
                                                    <div class="col-6">
                                                        <div class="compliance-stat">
                                                            <i class="fas fa-clock text-primary"></i>
                                                            <small>एकूण वेळ</small>
                                                            <div class="stat-value" id="totalDutyTime">0 मिनिटे</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="compliance-stat">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <small>अनुपालन वेळ</small>
                                                            <div class="stat-value" id="compliantTime">0 मिनिटे</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="compliance-stat">
                                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                                            <small>गैर-अनुपालन</small>
                                                            <div class="stat-value" id="nonCompliantTime">0 मिनिटे</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="compliance-stat">
                                                            <i class="fas fa-wifi-slash text-danger"></i>
                                                            <small>ट्रॅकिंग बंद</small>
                                                            <div class="stat-value" id="trackingOffTime">0 मिनिटे</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="alert alert-sm" id="complianceAlert" style="display: none;">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <span id="alertMessage"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">सध्या कोणतीही सक्रिय ड्यूटी नाही</h5>
                    <p class="text-muted">आपली पुढील ड्यूटी खाली पहा</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Duties -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    अलीकडील ड्यूटी
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_duties)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>दिनांक</th>
                                    <th>वेळ</th>
                                    <th>स्थान</th>
                                    <th>शिफ्ट</th>
                                    <th>स्थिती</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_duties as $duty): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($duty['date'])) ?></td>
                                        <td>
                                            <small>
                                                <?= date('H:i', strtotime($duty['start_time'])) ?> - 
                                                <?= date('H:i', strtotime($duty['end_time'])) ?>
                                            </small>
                                        </td>
                                        <td><?= $duty['point_name'] ?></td>
                                        <td>
                                            <span class="badge <?= $duty['shift'] == 'Day' ? 'bg-warning text-dark' : 'bg-info' ?>">
                                                <?= $duty['shift'] == 'Day' ? 'दिवस' : 'रात्र' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $currentDate = date('Y-m-d');
                                            $currentTime = date('H:i:s');
                                            $dutyDate = $duty['date'];
                                            $startTime = $duty['start_time'];
                                            $endTime = $duty['end_time'];
                                            
                                            if ($dutyDate > $currentDate || ($dutyDate == $currentDate && $startTime > $currentTime)): ?>
                                                <span class="badge bg-primary">आगामी</span>
                                            <?php elseif ($dutyDate == $currentDate && $startTime <= $currentTime && $endTime >= $currentTime): ?>
                                                <span class="badge bg-success">सक्रिय</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">पूर्ण</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">कोणतीही ड्यूटी नोंद नाही</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Compliance History -->
<?php if (!empty($compliance_history)): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    अनुपालन इतिहास
                    <?php if ($average_compliance && $average_compliance['avg_compliance']): ?>
                        <span class="badge <?= $average_compliance['avg_compliance'] >= 80 ? 'bg-success' : ($average_compliance['avg_compliance'] >= 60 ? 'bg-warning' : 'bg-danger') ?> ms-2">
                            सरासरी: <?= round($average_compliance['avg_compliance'], 1) ?>%
                        </span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>दिनांक</th>
                                <th>स्थान</th>
                                <th>अनुपालन</th>
                                <th>कार्यप्रदर्शन</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($compliance_history as $compliance): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($compliance['date'])) ?></td>
                                    <td><?= $compliance['point_name'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 100px; height: 8px;">
                                                <div class="progress-bar <?= $compliance['compliance_percent'] >= 80 ? 'bg-success' : ($compliance['compliance_percent'] >= 60 ? 'bg-warning' : 'bg-danger') ?>" 
                                                     style="width: <?= $compliance['compliance_percent'] ?>%"></div>
                                            </div>
                                            <small><?= round($compliance['compliance_percent'], 1) ?>%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($compliance['compliance_percent'] >= 80): ?>
                                            <span class="badge bg-success">उत्कृष्ट</span>
                                        <?php elseif ($compliance['compliance_percent'] >= 60): ?>
                                            <span class="badge bg-warning">सामान्य</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">सुधारणा आवश्यक</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Location Status Indicator -->
<div class="location-status">
    <span class="status-indicator status-inactive"></span>
    स्थान: बंद
</div>

<style>
/* Compliance Speedometer Styles */
.compliance-speedometer {
    position: relative;
    display: inline-block;
    margin: 20px 0;
}

.speedometer-value {
    position: absolute;
    top: 70px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
}

.speedometer-value .percentage {
    font-size: 24px;
    font-weight: bold;
    display: block;
}

.speedometer-value .status {
    font-size: 12px;
    display: block;
    margin-top: 5px;
}

.compliance-stat {
    text-align: center;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
    background: rgba(0,0,0,0.05);
}

.compliance-stat i {
    font-size: 18px;
    margin-bottom: 5px;
    display: block;
}

.compliance-stat small {
    display: block;
    color: #666;
    margin-bottom: 5px;
}

.stat-value {
    font-weight: bold;
    font-size: 14px;
}

.alert-sm {
    padding: 8px 12px;
    font-size: 13px;
}

/* Compliance Alert Animation */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.alert-warning {
    animation: pulse 2s infinite;
}
</style>

<script>
// Compliance Tracking Functions
class ComplianceTracker {
    constructor() {
        this.canvas = document.getElementById('speedometerCanvas');
        this.ctx = this.canvas ? this.canvas.getContext('2d') : null;
        this.alertShown = false;
        this.lastAlertTime = 0;
        this.alertCount = 0;
    }
    
    drawSpeedometer(percentage, color = '#28a745') {
        if (!this.ctx) return;
        
        const centerX = 100;
        const centerY = 90;
        const radius = 70;
        
        // Clear canvas
        this.ctx.clearRect(0, 0, 200, 120);
        
        // Draw background arc
        this.ctx.beginPath();
        this.ctx.arc(centerX, centerY, radius, Math.PI, 2 * Math.PI);
        this.ctx.strokeStyle = '#e9ecef';
        this.ctx.lineWidth = 12;
        this.ctx.stroke();
        
        // Draw progress arc
        const endAngle = Math.PI + (percentage / 100) * Math.PI;
        this.ctx.beginPath();
        this.ctx.arc(centerX, centerY, radius, Math.PI, endAngle);
        this.ctx.strokeStyle = color;
        this.ctx.lineWidth = 12;
        this.ctx.lineCap = 'round';
        this.ctx.stroke();
        
        // Draw center circle
        this.ctx.beginPath();
        this.ctx.arc(centerX, centerY, 8, 0, 2 * Math.PI);
        this.ctx.fillStyle = color;
        this.ctx.fill();
        
        // Draw needle
        const needleAngle = Math.PI + (percentage / 100) * Math.PI;
        const needleLength = radius - 15;
        const needleX = centerX + Math.cos(needleAngle) * needleLength;
        const needleY = centerY + Math.sin(needleAngle) * needleLength;
        
        this.ctx.beginPath();
        this.ctx.moveTo(centerX, centerY);
        this.ctx.lineTo(needleX, needleY);
        this.ctx.strokeStyle = '#333';
        this.ctx.lineWidth = 3;
        this.ctx.stroke();
        
        // Draw scale marks
        for (let i = 0; i <= 100; i += 20) {
            const angle = Math.PI + (i / 100) * Math.PI;
            const startX = centerX + Math.cos(angle) * (radius - 5);
            const startY = centerY + Math.sin(angle) * (radius - 5);
            const endX = centerX + Math.cos(angle) * (radius + 5);
            const endY = centerY + Math.sin(angle) * (radius + 5);
            
            this.ctx.beginPath();
            this.ctx.moveTo(startX, startY);
            this.ctx.lineTo(endX, endY);
            this.ctx.strokeStyle = '#666';
            this.ctx.lineWidth = 2;
            this.ctx.stroke();
        }
    }
    
    updateCompliance(data) {
        const percentage = data.compliance_percent || 0;
        const color = data.color || '#dc3545';
        
        // Update speedometer
        this.drawSpeedometer(percentage, color);
        
        // Update values
        $('#speedometerValue .percentage').text(percentage + '%');
        $('#speedometerValue .status').text(data.message || 'डेटा उपलब्ध नाही');
        
        // Update statistics
        $('#totalDutyTime').text((data.total_duty_minutes || 0) + ' मिनिटे');
        $('#compliantTime').text((data.compliant_minutes || 0) + ' मिनिटे');
        $('#nonCompliantTime').text((data.non_compliant_minutes || 0) + ' मिनिटे');
        $('#trackingOffTime').text((data.tracking_off_minutes || 0) + ' मिनिटे');
    }
    
    checkAndShowAlert(dutyId, officerId) {
        // Check if alert is needed
        $.ajax({
            url: '<?= base_url('api/compliance/check-alert') ?>',
            method: 'POST',
            data: {
                officer_id: officerId,
                duty_id: dutyId
            },
            success: (response) => {
                if (response.needs_alert) {
                    this.showAlert(response.message, response.alert_type);
                    
                    // Record alert sent
                    $.ajax({
                        url: '<?= base_url('api/compliance/record-alert') ?>',
                        method: 'POST',
                        data: {
                            officer_id: officerId,
                            duty_id: dutyId,
                            alert_type: response.alert_type
                        }
                    });
                }
            }
        });
    }
    
    showAlert(message, type) {
        const alertDiv = $('#complianceAlert');
        const alertMessage = $('#alertMessage');
        
        // Set alert class based on type
        alertDiv.removeClass('alert-info alert-warning alert-danger');
        
        if (type === 'first_warning') {
            alertDiv.addClass('alert-warning');
        } else if (type === 'second_warning') {
            alertDiv.addClass('alert-danger');
        } else {
            alertDiv.addClass('alert-info');
        }
        
        alertMessage.text(message);
        alertDiv.show();
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            alertDiv.fadeOut();
        }, 10000);
    }
    
    loadComplianceData(dutyId, officerId) {
        console.log('Loading compliance data for duty:', dutyId, 'officer:', officerId);
        $.ajax({
            url: '<?= base_url('api/compliance/speedometer') ?>',
            method: 'POST',
            data: {
                officer_id: officerId,
                duty_id: dutyId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: (response) => {
                console.log('Compliance API response:', response);
                if (response.success) {
                    this.updateCompliance(response.data);
                } else {
                    console.error('Failed to load compliance data:', response.message);
                    // Show error message to user
                    $('#speedometerValue .status').text('डेटा लोड करण्यात त्रुटी');
                }
            },
            error: (xhr, status, error) => {
                console.error('Error loading compliance data:', error, xhr.responseText);
                $('#speedometerValue .status').text('API त्रुटी');
            }
        });
    }
}

// Initialize compliance tracker
let complianceTracker;

// Diagnostic function to check all location tracking prerequisites
function runLocationTrackingDiagnostics() {
    console.log('=== LOCATION TRACKING DIAGNOSTICS ===');
    
    const diagnostics = {
        geolocationSupported: !!navigator.geolocation,
        locationEnabled: $('#locationTrackingEnabled').val(),
        currentDutyId: $('#currentDutyId').val(),
        officerId: <?= session()->get('officer_id') ?? 0 ?>,
        csrfToken: '<?= csrf_hash() ?>',
        csrfTokenName: '<?= csrf_token() ?>',
        baseUrl: '<?= base_url() ?>',
        apiUrl: '<?= base_url('api/location/log') ?>',
        hasStartButton: $('#startTracking').length > 0,
        hasStopButton: $('#stopTracking').length > 0,
        hasLocationStatus: $('#locationStatus').length > 0
    };
    
    console.table(diagnostics);
    
    // Check for common issues
    const issues = [];
    if (!diagnostics.geolocationSupported) issues.push('Geolocation not supported');
    if (!diagnostics.currentDutyId) issues.push('No current duty ID');
    if (!diagnostics.officerId) issues.push('No officer ID in session');
    if (diagnostics.locationEnabled !== '1') issues.push('Location tracking not enabled');
    
    if (issues.length > 0) {
        console.error('LOCATION TRACKING ISSUES:', issues);
        return false;
    }
    
    console.log('✅ All prerequisites met for location tracking');
    return true;
}

// Global variables for location tracking debouncing
let lastLocationUpdateTime = 0;
const LOCATION_UPDATE_DEBOUNCE = 2000; // 2 seconds minimum between updates (prevents rapid duplicates but allows 30s intervals)

$(document).ready(function() {
    let trackingInterval;
    
    // Run diagnostics on page load
    $('#startTracking').on('click', function() {
        console.log('Start tracking button clicked');
        
        // Clear any existing watch first
        if (locationWatchId) {
            console.log('Clearing existing location watch');
            navigator.geolocation.clearWatch(locationWatchId);
            locationWatchId = null;
        }
        
        // Run diagnostics before starting
        if (!runLocationTrackingDiagnostics()) {
            console.error('Location tracking prerequisites not met');
            return;
        }
        
        if (navigator.geolocation) {
            console.log('🔍 Starting continuous location tracking with watchPosition...');
            
            const dutyId = $('#currentDutyId').val();
            startLocationTracking(dutyId);
            
            $('#startTracking').addClass('d-none');
            $('#stopTracking').removeClass('d-none');
            
            // Use watchPosition for continuous, reliable location tracking
            const watchOptions = {
                enableHighAccuracy: false, // Use network location (faster, more reliable)
                timeout: 30000,           // 30 seconds timeout
                maximumAge: 60000         // Allow 1-minute cached location
            };
            
            locationWatchId = navigator.geolocation.watchPosition(
                function(position) {
                    // Success callback - continuous location updates
                    const { latitude, longitude, accuracy } = position.coords;
                    const timestamp = Date.now();
                    
                    console.log('📍 Location update from watchPosition:', {
                        latitude, longitude, accuracy, timestamp: new Date(timestamp).toISOString()
                    });
                    
                    // Frequency control: only send pings every 30 seconds
                    if (lastLocationPingTime && (timestamp - lastLocationPingTime) < LOCATION_PING_INTERVAL) {
                        console.log('🔄 Skipping ping - too frequent (last ping was', (timestamp - lastLocationPingTime)/1000, 'seconds ago)');
                        // Still update UI, but don't send to server
                        updateLocationDisplay(position);
                        return;
                    }
                    
                    // Update last ping time
                    lastLocationPingTime = timestamp;
                    
                    // Send location update to server
                    sendLocationUpdate(latitude, longitude);
                    checkLocationStatus(latitude, longitude);
                },
                function(error) {
                    // Error callback - handle geolocation errors
                    console.error('❌ Geolocation error in watchPosition:', {
                        code: error.code,
                        message: error.message,
                        PERMISSION_DENIED: error.code === 1,
                        POSITION_UNAVAILABLE: error.code === 2,
                        TIMEOUT: error.code === 3
                    });
                    handleGeolocationError(error);
                },
                watchOptions
            );
            
            console.log('🔍 Location watch started with ID:', locationWatchId);
        } else {
            alert('आपला ब्राउझर स्थान ट्रॅकिंग समर्थित नाही');
        }
    });
    
    $('#stopTracking').on('click', function() {
        stopLocationTracking();
        if (trackingInterval) {
            clearInterval(trackingInterval);
        }
        
        $('#stopTracking').addClass('d-none');
        $('#startTracking').removeClass('d-none');
    });
    
    // Auto-start tracking if duty is active and location tracking is enabled
    // Initialize compliance tracker
    complianceTracker = new ComplianceTracker();
    
    // Load initial compliance data if active duty exists
    if ($('#currentDutyId').length) {
        const dutyId = $('#currentDutyId').val();
        const officerId = <?= session()->get('officer_id') ?? 0 ?>;
        
        if (dutyId && officerId) {
            // Load compliance data
            complianceTracker.loadComplianceData(dutyId, officerId);
            
            // Set up compliance monitoring (every 30 seconds)
            setInterval(function() {
                complianceTracker.loadComplianceData(dutyId, officerId);
                complianceTracker.checkAndShowAlert(dutyId, officerId);
            }, 30000);
        }
    }
    
    // Auto-start location tracking for active duties
    const locationEnabled = $('#locationTrackingEnabled').val();
    const currentDutyId = $('#currentDutyId').val();
    const officerId = <?= session()->get('officer_id') ?? 0 ?>;
    
    console.log('Auto-start check:', {
        locationEnabled: locationEnabled,
        currentDutyId: currentDutyId,
        officerId: officerId,
        hasCurrentDutyElement: $('#currentDutyId').length > 0
    });
    
    if (locationEnabled === '1' && currentDutyId && officerId) {
        console.log('✅ Auto-starting location tracking for duty:', currentDutyId);
        // Auto-start after 2 seconds to allow page to load
        setTimeout(function() {
            console.log('🔍 Auto-start safety check:', {
                trackingInterval: trackingInterval,
                startButtonVisible: $('#startTracking').is(':visible'),
                startButtonExists: $('#startTracking').length > 0,
                buttonClasses: $('#startTracking').attr('class')
            });
            
            // Safety check: only auto-start if not already tracking
            if (!trackingInterval && $('#startTracking').is(':visible')) {
                console.log('🚀 Attempting to start location tracking automatically');
                $('#startTracking').click();
            } else {
                console.log('⚠️ Auto-start skipped:', {
                    reason: !trackingInterval ? 'button not visible' : 'tracking already active',
                    trackingInterval: !!trackingInterval,
                    buttonVisible: $('#startTracking').is(':visible')
                });
            }
        }, 2000);
    } else {
        console.log('Auto-start conditions not met:', {
            locationEnabled: locationEnabled,
            currentDutyId: currentDutyId,
            officerId: officerId
        });
    }
});

// Function to check if officer is inside or outside duty point radius
function checkLocationStatus(latitude, longitude) {
    const dutyId = $('#currentDutyId').val();
    const officerId = <?= session()->get('officer_id') ?? 0 ?>;
    
    if (!dutyId || !officerId) {
        console.log('No duty or officer ID available for location check');
        return;
    }
    
    console.log('Checking location status for:', latitude, longitude);
    
    $.ajax({
        url: '<?= base_url('api/location/check-radius') ?>',
        method: 'POST',
        data: {
            officer_id: officerId,
            duty_id: dutyId,
            latitude: latitude,
            longitude: longitude,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Location status response:', response);
            if (response.success) {
                updateLocationStatus(response.data);
            } else {
                console.error('Location status check failed:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking location status:', error, xhr.responseText);
        }
    });
}

// Function to update location status display
function updateLocationStatus(data) {
    const statusElement = $('#locationStatus');
    const statusText = $('#locationStatusText');
    
    if (!statusElement.length || !statusText.length) {
        console.log('Location status elements not found in DOM');
        return;
    }
    
    if (data.inside_radius) {
        statusElement.removeClass('bg-danger').addClass('bg-success');
        statusText.html('<i class="fas fa-check-circle me-2"></i>ड्यूटी पॉइंटच्या आत');
    } else {
        statusElement.removeClass('bg-success').addClass('bg-danger');
        statusText.html('<i class="fas fa-exclamation-triangle me-2"></i>ड्यूटी पॉइंटच्या बाहेर');
    }
    
    // Update distance info if available
    if (data.distance) {
        const distanceText = data.distance > 1000 ? 
            (data.distance / 1000).toFixed(2) + ' किमी' : 
            Math.round(data.distance) + ' मीटर';
        statusText.append('<br><small>अंतर: ' + distanceText + '</small>');
    }
}

// Function to start location tracking
function startLocationTracking(dutyId) {
    console.log('Starting location tracking for duty:', dutyId);
    
    // Update UI to show tracking is active
    $('#locationStatus #locationStatusText').html('<i class="fas fa-satellite-dish me-2"></i>स्थान ट्रॅकिंग सुरू...');
    $('#locationStatus').removeClass('alert-secondary').addClass('alert-info');
}

// Function to stop location tracking
function stopLocationTracking() {
    console.log('Stopping location tracking');
    
    // Update UI to show tracking is stopped
    $('#locationStatus #locationStatusText').html('<i class="fas fa-stop-circle me-2"></i>स्थान ट्रॅकिंग बंद');
    $('#locationStatus').removeClass('alert-success alert-danger alert-info').addClass('alert-secondary');
}

// Function to send location update to server
function sendLocationUpdate(latitude, longitude) {
    const currentTime = Date.now();
    
    // Debounce: prevent rapid duplicate calls
    if (currentTime - lastLocationUpdateTime < LOCATION_UPDATE_DEBOUNCE) {
        console.log('Location update debounced - too soon since last update');
        return;
    }
    
    const dutyId = $('#currentDutyId').val();
    const officerId = <?= session()->get('officer_id') ?? 0 ?>;
    
    if (!dutyId || !officerId) {
        console.error('Missing duty ID or officer ID for location update');
        showLocationError('सत्र डेटा गहाळ आहे. कृपया पुन्हा लॉगिन करा.', 'SESSION_ERROR');
        return;
    }
    
    // Update last update time
    lastLocationUpdateTime = currentTime;
    
    console.log('Sending location update:', {
        latitude: latitude,
        longitude: longitude,
        dutyId: dutyId,
        officerId: officerId,
        timestamp: new Date().toISOString()
    });
    
    // Show loading state
    $('#locationStatus #locationStatusText').html('<i class="fas fa-spinner fa-spin me-2"></i>स्थान अपडेट करत आहे...');
    $('#locationStatus').removeClass('alert-success alert-danger').addClass('alert-info');
    
    $.ajax({
        url: '<?= base_url('api/location/log') ?>',
        method: 'POST',
        contentType: 'application/json',
        timeout: 10000, // 10 second timeout
        data: JSON.stringify({
            officer_id: parseInt(officerId),
            duty_id: parseInt(dutyId),
            latitude: parseFloat(latitude),
            longitude: parseFloat(longitude),
            timestamp: new Date().toISOString()
        }),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Location update successful:', response);
            if (response.success) {
                // Update last location timestamp in UI
                const now = new Date();
                const timeStr = now.toLocaleTimeString('hi-IN');
                $('#locationStatus #locationStatusText').html('<i class="fas fa-check-circle me-2"></i>स्थान यशस्वीरित्या अपडेट केले<br><small>शेवटचे अपडेट: ' + timeStr + '</small>');
                $('#locationStatus').removeClass('alert-info alert-danger').addClass('alert-success');
                
                // Clear any previous error notifications
                clearLocationErrorNotification();
            } else {
                console.error('Location update failed:', response.message);
                showLocationError(response.message || 'स्थान अपडेट अयशस्वी', response.error_code || 'UPDATE_FAILED');
            }
        },
        error: function(xhr, status, error) {
            console.error('Location update error:', error, xhr.responseText);
            
            let errorMessage = 'स्थान अपडेट त्रुटी';
            let errorCode = 'NETWORK_ERROR';
            
            if (status === 'timeout') {
                errorMessage = 'नेटवर्क टाइमआउट - कृपया आपले इंटरनेट कनेक्शन तपासा';
                errorCode = 'TIMEOUT';
            } else if (xhr.status === 0) {
                errorMessage = 'नेटवर्क कनेक्शन नाही - कृपया इंटरनेट कनेक्शन तपासा';
                errorCode = 'NO_CONNECTION';
            } else if (xhr.status === 404) {
                errorMessage = 'API एंडपॉइंट सापडला नाही';
                errorCode = 'API_NOT_FOUND';
            } else if (xhr.status === 500) {
                errorMessage = 'सर्व्हर त्रुटी - कृपया प्रशासकाशी संपर्क साधा';
                errorCode = 'SERVER_ERROR';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
                errorCode = xhr.responseJSON.error_code || 'API_ERROR';
            }
            
            showLocationError(errorMessage, errorCode);
        }
    });
}

// Helper function to show location errors with better user feedback
function showLocationError(message, errorCode) {
    console.error('Location error [' + errorCode + ']:', message);
    
    // Update location status display
    $('#locationStatus #locationStatusText').html('<i class="fas fa-exclamation-triangle me-2"></i>' + message);
    $('#locationStatus').removeClass('alert-success alert-info').addClass('alert-danger');
    
    // Show persistent notification for critical errors
    if (['SESSION_ERROR', 'DUTY_NOT_FOUND', 'OFFICER_NOT_FOUND'].includes(errorCode)) {
        showPersistentNotification(message, 'error', errorCode);
    } else if (['NO_CONNECTION', 'TIMEOUT'].includes(errorCode)) {
        showTemporaryNotification(message, 'warning', 5000);
    } else {
        showTemporaryNotification(message, 'error', 3000);
    }
}

// Helper function to clear location error notifications
function clearLocationErrorNotification() {
    $('.location-error-notification').fadeOut(500, function() {
        $(this).remove();
    });
}

// Helper function to show persistent notifications
function showPersistentNotification(message, type, errorCode) {
    const alertClass = type === 'error' ? 'alert-danger' : 'alert-warning';
    const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-exclamation-triangle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show location-error-notification" role="alert" data-error-code="${errorCode}">
            <i class="${icon} me-2"></i>
            <strong>स्थान ट्रॅकिंग त्रुटी:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    // Remove existing notifications of the same type
    $(`.location-error-notification[data-error-code="${errorCode}"]`).remove();
    
    // Add to top of page
    $('.container-fluid').prepend(notification);
}

// Helper function to show temporary notifications
function showTemporaryNotification(message, type, duration) {
    const alertClass = type === 'error' ? 'alert-danger' : 'alert-warning';
    const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-exclamation-triangle';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show location-error-notification" role="alert">
            <i class="${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    // Add to top of page
    $('.container-fluid').prepend(notification);
    
    // Auto-remove after duration
    setTimeout(() => {
        notification.fadeOut(500, function() {
            $(this).remove();
        });
    }, duration);
}

// Enhanced error handling for geolocation failures
function handleGeolocationError(error) {
    let errorMsg = 'स्थान परवानगी आवश्यक आहे';
    let errorCode = 'GEOLOCATION_ERROR';
    
    if (error.code === error.PERMISSION_DENIED) {
        errorMsg = 'स्थान परवानगी नाकारली गेली. कृपया ब्राउझर सेटिंग्जमध्ये परवानगी द्या.';
        errorCode = 'PERMISSION_DENIED';
    } else if (error.code === error.POSITION_UNAVAILABLE) {
        errorMsg = 'स्थान माहिती उपलब्ध नाही. कृपया GPS चालू करा.';
        errorCode = 'POSITION_UNAVAILABLE';
    } else if (error.code === error.TIMEOUT) {
        errorMsg = 'स्थान मिळवण्यात वेळ संपला. कृपया पुन्हा प्रयत्न करा.';
        errorCode = 'GEOLOCATION_TIMEOUT';
    }
    
    showLocationError(errorMsg, errorCode);
    
    // Show detailed help for permission issues
    if (error.code === error.PERMISSION_DENIED) {
        showPersistentNotification(
            'स्थान ट्रॅकिंग कार्य करण्यासाठी, ब्राउझरमध्ये स्थान परवानगी द्या. Address bar मधील location icon वर क्लिक करा आणि "Allow" निवडा.',
            'warning',
            'PERMISSION_HELP'
        );
    }
    
    // Stop tracking on persistent errors
    if (['PERMISSION_DENIED', 'POSITION_UNAVAILABLE'].includes(errorCode)) {
        $('#stopTracking').click();
    }
}

// Enhanced sendLocationUpdate with background tracking integration
function sendLocationUpdateWithBackground(latitude, longitude) {
    const currentTime = Date.now();
    const officerId = <?= session()->get('officer_id') ?? 0 ?>;
    const dutyId = $('#currentDutyId').val();
    
    // Always queue for background sync (handles offline/background scenarios)
    if (window.backgroundLocationTracker) {
        window.backgroundLocationTracker.addLocation(latitude, longitude, officerId, dutyId);
    }
    
    // Also try immediate send if page is active
    if (!document.hidden) {
        sendLocationUpdate(latitude, longitude);
    } else {
        console.log('📱 Page in background - location queued for background sync');
    }
}

</script>

<!-- Background Location Tracking Script -->
<script src="<?= base_url('js/background-location.js') ?>"></script>

<script>
// Initialize background location tracking on page load
$(document).ready(function() {
    // Wait for background tracker to initialize
    setTimeout(function() {
        if (window.backgroundLocationTracker) {
            console.log('✅ Background location tracking initialized');
            
            // Load any queued locations from previous sessions
            window.backgroundLocationTracker.loadQueueFromStorage();
            
            // Sync any pending locations
            window.backgroundLocationTracker.syncQueuedLocations();
        }
    }, 1000);
});
</script>

<?= $this->include('officer/layout/footer') ?>
