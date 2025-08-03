<?= $this->include('admin/layout/header') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-line me-2"></i>अनुपालन ट्रॅकिंग</h2>
                <div class="btn-group">
                    <a href="<?= base_url('admin/compliance/live') ?>" class="btn btn-success">
                        <i class="fas fa-satellite-dish me-2"></i>लाइव्ह ट्रॅकिंग
                    </a>
                    <a href="<?= base_url('admin/compliance/reports') ?>" class="btn btn-info">
                        <i class="fas fa-file-alt me-2"></i>रिपोर्ट्स
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">एकूण ड्यूटी</h6>
                                    <h3><?= $total_duties ?? 0 ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">उच्च अनुपालन</h6>
                                    <h3><?= $high_compliance ?? 0 ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">कमी अनुपालन</h6>
                                    <h3><?= $low_compliance ?? 0 ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">सरासरी अनुपालन</h6>
                                    <h3><?= number_format($average_compliance ?? 0, 1) ?>%</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-percentage fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">अनुपालन तपशील</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($compliance_data)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>अधिकारी</th>
                                        <th>बॅज नंबर</th>
                                        <th>ड्यूटी तारीख</th>
                                        <th>पॉइंट</th>
                                        <th>अनुपालन %</th>
                                        <th>एकूण लॉग्स</th>
                                        <th>अनुपालित लॉग्स</th>
                                        <th>स्थिती</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($compliance_data as $record): ?>
                                        <tr>
                                            <td><?= esc($record['officer_name']) ?></td>
                                            <td><?= esc($record['badge_no']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($record['duty_date'])) ?></td>
                                            <td><?= esc($record['point_name']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 20px;">
                                                        <div class="progress-bar <?= ($record['compliance_percentage'] >= 80) ? 'bg-success' : (($record['compliance_percentage'] >= 60) ? 'bg-warning' : 'bg-danger') ?>" 
                                                             style="width: <?= $record['compliance_percentage'] ?>%"></div>
                                                    </div>
                                                    <span class="badge <?= ($record['compliance_percentage'] >= 80) ? 'bg-success' : (($record['compliance_percentage'] >= 60) ? 'bg-warning' : 'bg-danger') ?>">
                                                        <?= number_format($record['compliance_percentage'], 1) ?>%
                                                    </span>
                                                </div>
                                            </td>
                                            <td><?= $record['total_logs'] ?></td>
                                            <td><?= $record['compliant_logs'] ?></td>
                                            <td>
                                                <?php
                                                $status = $record['compliance_percentage'] >= 80 ? 'उत्कृष्ट' : 
                                                         ($record['compliance_percentage'] >= 60 ? 'चांगले' : 'सुधारणा आवश्यक');
                                                $statusClass = $record['compliance_percentage'] >= 80 ? 'bg-success' : 
                                                              ($record['compliance_percentage'] >= 60 ? 'bg-warning' : 'bg-danger');
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= $status ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">अनुपालन डेटा उपलब्ध नाही</h5>
                            <p class="text-muted">ड्यूटी पूर्ण झाल्यानंतर अनुपालन डेटा येथे दिसेल</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/layout/footer') ?>
