<?= $this->include('admin/layout/header') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt me-2"></i>
        प्रशासक डॅशबोर्ड
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= base_url('admin/duties/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>नवी ड्यूटी जोडा
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="stats-number"><?= $total_officers ?></div>
                    <div class="stats-label">एकूण अधिकारी</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="stats-number"><?= $total_points ?></div>
                    <div class="stats-label">ड्यूटी पॉइंट्स</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-map-marker-alt fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="stats-number"><?= $total_duties ?></div>
                    <div class="stats-label">एकूण ड्यूटी</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-tasks fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="stats-number"><?= $active_duties ?></div>
                    <div class="stats-label">सक्रिय ड्यूटी</div>
                </div>
                <div class="flex-shrink-0">
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Duties -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day me-2"></i>
                    आजच्या ड्यूटी
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($todays_duties)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>वेळ</th>
                                    <th>पॉइंट</th>
                                    <th>शिफ्ट</th>
                                    <th>ट्रॅकिंग</th>
                                    <th>कृती</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todays_duties as $duty): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($duty['start_time'])) ?> - 
                                                <?= date('H:i', strtotime($duty['end_time'])) ?>
                                            </small>
                                        </td>
                                        <td><?= $duty['point_name'] ?></td>
                                        <td>
                                            <span class="badge <?= $duty['shift'] == 'Day' ? 'bg-warning' : 'bg-info' ?>">
                                                <?= $duty['shift'] == 'Day' ? 'दिवस' : 'रात्र' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($duty['location_tracking_enabled']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-satellite-dish me-1"></i>सक्रिय
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">निष्क्रिय</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/duties/edit/' . $duty['duty_id']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('admin/compliance/duty/' . $duty['duty_id']) ?>" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">आज कोणतीही ड्यूटी नाही</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Compliance Alerts -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    कमी अनुपालन अलर्ट
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($low_compliance_alerts)): ?>
                    <?php foreach (array_slice($low_compliance_alerts, 0, 5) as $alert): ?>
                        <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                            <div class="flex-shrink-0">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <small><?= round($alert['compliance_percent']) ?>%</small>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold"><?= $alert['name'] ?></div>
                                <small class="text-muted">
                                    <?= $alert['point_name'] ?> - <?= date('d/m/Y', strtotime($alert['date'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center">
                        <a href="<?= base_url('admin/compliance') ?>" class="btn btn-sm btn-outline-primary">
                            सर्व पहा
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">कोणतेही अलर्ट नाहीत</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Compliance Summary -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    साप्ताहिक अनुपालन सारांश
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($compliance_summary)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped data-table">
                            <thead>
                                <tr>
                                    <th>अधिकारी</th>
                                    <th>बॅज नं.</th>
                                    <th>एकूण ड्यूटी</th>
                                    <th>चांगली ड्यूटी</th>
                                    <th>सरासरी अनुपालन</th>
                                    <th>कार्यप्रदर्शन</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($compliance_summary as $summary): ?>
                                    <tr>
                                        <td><?= $summary['name'] ?></td>
                                        <td><?= $summary['badge_no'] ?></td>
                                        <td><?= $summary['total_duties'] ?></td>
                                        <td><?= $summary['good_duties'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar <?= $summary['avg_compliance'] >= 80 ? 'bg-success' : ($summary['avg_compliance'] >= 60 ? 'bg-warning' : 'bg-danger') ?>" 
                                                         style="width: <?= $summary['avg_compliance'] ?>%"></div>
                                                </div>
                                                <small><?= round($summary['avg_compliance'], 1) ?>%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($summary['avg_compliance'] >= 80): ?>
                                                <span class="badge bg-success">उत्कृष्ट</span>
                                            <?php elseif ($summary['avg_compliance'] >= 60): ?>
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
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">अनुपालन डेटा उपलब्ध नाही</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/layout/footer') ?>
