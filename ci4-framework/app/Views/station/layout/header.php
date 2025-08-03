<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?><?= session()->get('station_name') ?></title>
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
            transform: translateY(-2px);
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
        .btn-success {
            background: linear-gradient(45deg, #2E8B57, #228B22);
            border: none;
        }
        .btn-success:hover {
            background: linear-gradient(45deg, #228B22, #2E8B57);
        }
        .table-dark {
            background-color: #2E8B57;
        }
        .text-primary {
            color: #2E8B57 !important;
        }
        .border-left-primary {
            border-left: 0.25rem solid #2E8B57 !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('station/dashboard') ?>">
                <i class="fas fa-shield-alt me-2"></i><?= session()->get('station_name') ?>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?= session()->get('station_code') ?>
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
                        <a class="nav-link <?= (current_url() == base_url('station/dashboard')) ? 'active' : '' ?>" 
                           href="<?= base_url('station/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>डॅशबोर्ड
                        </a>
                        <a class="nav-link <?= (strpos(current_url(), 'station/officers') !== false) ? 'active' : '' ?>" 
                           href="<?= base_url('station/officers') ?>">
                            <i class="fas fa-users me-2"></i>अधिकारी व्यवस्थापन
                        </a>
                        <a class="nav-link <?= (strpos(current_url(), 'station/duties') !== false) ? 'active' : '' ?>" 
                           href="<?= base_url('station/duties') ?>">
                            <i class="fas fa-calendar-alt me-2"></i>ड्यूटी वाटप
                        </a>
                        <a class="nav-link <?= (strpos(current_url(), 'station/points') !== false) ? 'active' : '' ?>" 
                           href="<?= base_url('station/points') ?>">
                            <i class="fas fa-map-marker-alt me-2"></i>पॉइंट व्यवस्थापन
                        </a>
                        <a class="nav-link <?= (strpos(current_url(), 'station/compliance') !== false) ? 'active' : '' ?>" 
                           href="<?= base_url('station/compliance') ?>">
                            <i class="fas fa-chart-line me-2"></i>अनुपालन ट्रॅकिंग
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10"
