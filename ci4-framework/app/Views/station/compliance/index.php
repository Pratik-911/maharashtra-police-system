<?= $this->include('station/layout/header') ?>

<div class="main-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line"></i> अनुपालन ट्रॅकिंग
        </h1>
        <a href="<?= base_url('station/compliance/live') ?>" class="btn btn-info shadow-sm">
            <i class="fas fa-satellite-dish fa-sm text-white-50"></i> लाइव्ह ट्रॅकिंग
        </a>
    </div>

    <!-- Compliance Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card officers">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3><?= isset($compliance_stats['total_officers']) ? $compliance_stats['total_officers'] : count($officers) ?></h3>
                    <p class="mb-0">एकूण अधिकारी</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card duties">
                <div class="card-body text-center">
                    <i class="fas fa-wifi fa-3x mb-3"></i>
                    <h3><?= isset($compliance_stats['online_officers']) ? $compliance_stats['online_officers'] : 0 ?></h3>
                    <p class="mb-0">ऑनलाइन अधिकारी</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card points">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h3><?= isset($compliance_stats['compliant_officers']) ? $compliance_stats['compliant_officers'] : 0 ?></h3>
                    <p class="mb-0">अनुपालन करणारे</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-3x mb-3"></i>
                    <h3><?= isset($compliance_stats['compliance_rate']) ? $compliance_stats['compliance_rate'] : 0 ?>%</h3>
                    <p class="mb-0">अनुपालन दर</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Duties -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clock me-2"></i>आजच्या सक्रिय ड्यूटी
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($active_duties)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>अधिकाऱ्याचे नाव</th>
                                <th>पॉइंट</th>
                                <th>वेळ</th>
                                <th>स्थिती</th>
                                <th>शेवटचे अपडेट</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_duties as $duty): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($duty['officer_name']) ?></strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt me-1"></i><?= esc($duty['point_name']) ?>
                                    </td>
                                    <td>
                                        <?= date('H:i', strtotime($duty['start_time'])) ?> - 
                                        <?= date('H:i', strtotime($duty['end_time'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">सक्रिय</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('H:i') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">आज कोणतीही सक्रिय ड्यूटी नाही</h5>
                    <p class="text-muted">नवीन ड्यूटी वाटप करण्यासाठी "ड्यूटी वाटप" वर जा</p>
                    <a href="<?= base_url('station/duties/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>ड्यूटी वाटप करा
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Location Updates -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-map-marker-alt me-2"></i>अलीकडील स्थान अपडेट्स
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($recent_locations)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>अधिकारी</th>
                                <th>बॅज नंबर</th>
                                <th>स्थान</th>
                                <th>वेळ</th>
                                <th>स्थिती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($recent_locations, 0, 10) as $location): ?>
                                <?php
                                $timeDiff = time() - strtotime($location['timestamp']);
                                $isRecent = $timeDiff < 900; // 15 minutes
                                ?>
                                <tr>
                                    <td><?= esc($location['name']) ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= esc($location['badge_no']) ?></span>
                                    </td>
                                    <td>
                                        <small>
                                            <?= number_format($location['latitude'], 4) ?>, 
                                            <?= number_format($location['longitude'], 4) ?>
                                        </small>
                                    </td>
                                    <td><?= date('H:i:s', strtotime($location['timestamp'])) ?></td>
                                    <td>
                                        <span class="badge <?= $isRecent ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $isRecent ? 'ऑनलाइन' : 'ऑफलाइन' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= base_url('station/compliance/live') ?>" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i>सर्व लाइव्ह डेटा पहा
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">अलीकडील स्थान डेटा उपलब्ध नाही</h5>
                    <p class="text-muted">अधिकाऱ्यांनी मोबाइल अॅप वापरून स्थान अपडेट केले नाही</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Officers Status -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>अधिकारी स्थिती
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($officers)): ?>
                <div class="row">
                    <?php foreach ($officers as $officer): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?= esc($officer['name']) ?></h6>
                                            <small class="text-muted"><?= esc($officer['badge_no']) ?> - <?= esc($officer['rank']) ?></small>
                                            <br>
                                            <span class="badge bg-secondary">स्थिती अज्ञात</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">कोणतेही अधिकारी सापडले नाहीत</h5>
                    <a href="<?= base_url('station/officers/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>अधिकारी जोडा
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto refresh page every 30 seconds
setTimeout(function() {
    location.reload();
}, 30000);
</script>

<?= $this->include('station/layout/footer') ?>
