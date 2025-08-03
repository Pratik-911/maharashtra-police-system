<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $station_name ?> - डॅशबोर्ड</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(45deg, #2E8B57, #228B22) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,.08);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.officers {
            background: linear-gradient(135deg, #2E8B57 0%, #228B22 100%);
        }
        .stat-card.duties {
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
        }
        .stat-card.points {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar {
            background: white;
            min-height: calc(100vh - 76px);
            box-shadow: 2px 0 5px rgba(0,0,0,.1);
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #2E8B57;
            color: white;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i><?= $station_name ?>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?= $station_code ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('station/logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>लॉगआउट
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <nav class="nav flex-column py-3">
                        <a class="nav-link active" href="<?= base_url('station/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>डॅशबोर्ड
                        </a>
                        <a class="nav-link" href="<?= base_url('station/officers') ?>">
                            <i class="fas fa-users me-2"></i>अधिकारी व्यवस्थापन
                        </a>
                        <a class="nav-link" href="<?= base_url('station/duties') ?>">
                            <i class="fas fa-calendar-alt me-2"></i>ड्यूटी वाटप
                        </a>
                        <a class="nav-link" href="<?= base_url('station/points') ?>">
                            <i class="fas fa-map-marker-alt me-2"></i>पॉइंट व्यवस्थापन
                        </a>
                        <a class="nav-link" href="<?= base_url('station/compliance') ?>">
                            <i class="fas fa-chart-line me-2"></i>अनुपालन ट्रॅकिंग
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Welcome Message -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2 class="mb-3">स्वागत आहे, <?= $station_name ?>!</h2>
                            <p class="text-muted">आजची तारीख: <?= date('d/m/Y') ?> | वेळ: <?= date('H:i') ?></p>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card officers">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h3><?= $total_officers ?></h3>
                                    <p class="mb-0">एकूण अधिकारी</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card duties">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                    <h3><?= count($active_duties) ?></h3>
                                    <p class="mb-0">सक्रिय ड्यूटी</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card points">
                                <div class="card-body text-center">
                                    <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                                    <h3><?= $total_points ?></h3>
                                    <p class="mb-0">एकूण पॉइंट्स</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Duties -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-clock me-2"></i>आजच्या सक्रिय ड्यूटी
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($active_duties)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>पॉइंट नाव</th>
                                                        <th>सुरुवातीची वेळ</th>
                                                        <th>समाप्तीची वेळ</th>
                                                        <th>स्थिती</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($active_duties as $duty): ?>
                                                        <tr>
                                                            <td><?= esc($duty['point_name']) ?></td>
                                                            <td><?= date('H:i', strtotime($duty['start_time'])) ?></td>
                                                            <td><?= date('H:i', strtotime($duty['end_time'])) ?></td>
                                                            <td>
                                                                <span class="badge bg-success">सक्रिय</span>
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
                                            <p class="text-muted">नवीन ड्यूटी वाटप करण्यासाठी "ड्यूटी वाटप" वर क्लिक करा</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Duties -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-history me-2"></i>अलीकडील ड्यूटी
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($recent_duties)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>तारीख</th>
                                                        <th>पॉइंट नाव</th>
                                                        <th>वेळ</th>
                                                        <th>स्थिती</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recent_duties as $duty): ?>
                                                        <tr>
                                                            <td><?= date('d/m/Y', strtotime($duty['date'])) ?></td>
                                                            <td><?= esc($duty['point_name']) ?></td>
                                                            <td><?= date('H:i', strtotime($duty['start_time'])) ?> - <?= date('H:i', strtotime($duty['end_time'])) ?></td>
                                                            <td>
                                                                <?php
                                                                $currentDate = date('Y-m-d');
                                                                $currentTime = date('H:i:s');
                                                                $isActive = ($duty['date'] == $currentDate && 
                                                                           $duty['start_time'] <= $currentTime && 
                                                                           $duty['end_time'] >= $currentTime);
                                                                ?>
                                                                <span class="badge <?= $isActive ? 'bg-success' : 'bg-secondary' ?>">
                                                                    <?= $isActive ? 'सक्रिय' : 'पूर्ण' ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">अलीकडील ड्यूटी उपलब्ध नाहीत</h5>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
