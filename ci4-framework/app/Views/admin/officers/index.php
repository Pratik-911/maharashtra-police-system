<?= $this->include('admin/layout/header') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> <?= esc($title) ?>
        </h1>
        <a href="/admin/officers/create" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> नवीन अधिकारी जोडा
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Officers Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                एकूण अधिकारी</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($officers) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                सक्रिय अधिकारी</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($officers) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Officers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">अधिकारी यादी</h6>
        </div>
        <div class="card-body">
            <?php if (empty($officers)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500">अद्याप कोणतेही अधिकारी जोडलेले नाहीत</p>
                    <a href="/admin/officers/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> पहिला अधिकारी जोडा
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>नाव</th>
                                <th>बॅज नंबर</th>
                                <th>पद</th>
                                <th>पोलीस स्टेशन</th>
                                <th>मोबाइल</th>
                                <th>तारीख</th>
                                <th>क्रिया</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($officers as $officer): ?>
                                <tr>
                                    <td><?= esc($officer['name']) ?></td>
                                    <td><span class="badge badge-info"><?= esc($officer['badge_no']) ?></span></td>
                                    <td><?= esc($officer['rank']) ?></td>
                                    <td><?= esc($officer['police_station']) ?></td>
                                    <td><?= esc($officer['mobile']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($officer['created_at'])) ?></td>
                                    <td>
                                        <a href="/admin/officers/edit/<?= $officer['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="संपादित करा">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->include('admin/layout/footer') ?>
