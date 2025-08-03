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
                                    <input type="hidden" id="locationTrackingEnabled" value="1">
                                    <input type="hidden" id="currentDutyId" value="<?= $active_duty['duty_id'] ?>">
                                <?php else: ?>
                                    <span class="badge bg-secondary">निष्क्रिय</span>
                                <?php endif; ?>
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
                            <?php endif; ?>
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

<script>
$(document).ready(function() {
    let trackingInterval;
    
    $('#startTracking').on('click', function() {
        if (navigator.geolocation) {
            // Request permission first
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Permission granted, start tracking
                    const dutyId = $('#currentDutyId').val();
                    startLocationTracking(dutyId);
                    
                    $('#startTracking').addClass('d-none');
                    $('#stopTracking').removeClass('d-none');
                    
                    // Send initial location
                    sendLocationUpdate(position.coords.latitude, position.coords.longitude);
                    
                    // Set up interval for regular updates (every 2 minutes)
                    trackingInterval = setInterval(function() {
                        navigator.geolocation.getCurrentPosition(
                            function(pos) {
                                sendLocationUpdate(pos.coords.latitude, pos.coords.longitude);
                            },
                            function(error) {
                                console.error('Location error:', error);
                            }
                        );
                    }, 120000); // 2 minutes
                },
                function(error) {
                    let errorMsg = 'स्थान परवानगी आवश्यक आहे';
                    if (error.code === error.PERMISSION_DENIED) {
                        errorMsg = 'स्थान परवानगी नाकारली गेली. कृपया ब्राउझर सेटिंग्जमध्ये परवानगी द्या.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errorMsg = 'स्थान माहिती उपलब्ध नाही';
                    } else if (error.code === error.TIMEOUT) {
                        errorMsg = 'स्थान मिळवण्यात वेळ संपला';
                    }
                    alert(errorMsg);
                }
            );
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
    if ($('#locationTrackingEnabled').val() === '1' && $('#currentDutyId').length) {
        // Auto-start after 3 seconds to allow page to load
        setTimeout(function() {
            if (confirm('स्थान ट्रॅकिंग स्वयंचलितपणे सुरू करायची?')) {
                $('#startTracking').click();
            }
        }, 3000);
    }
});
</script>

<?= $this->include('officer/layout/footer') ?>
