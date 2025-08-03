<?= $this->include('station/layout/header') ?>

<div class="main-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marker-alt"></i> पॉइंट व्यवस्थापन
        </h1>
        <a href="<?= base_url('station/points/create') ?>" class="btn btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> नवीन पॉइंट जोडा
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Points Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card points">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                    <h3><?= count($points) ?></h3>
                    <p class="mb-0">एकूण पॉइंट्स</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card officers">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <h3><?= $station_code ?></h3>
                    <p class="mb-0">पोलीस स्टेशन</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Points List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i><?= $station_name ?> - पॉइंट्स यादी
            </h6>
        </div>
        <div class="card-body">
            <?php if (!empty($points)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th>अनुक्रमांक</th>
                                <th>पॉइंट नाव</th>
                                <th>स्थान</th>
                                <th>निर्देशांक</th>
                                <th>वर्णन</th>
                                <th>तयार केले</th>
                                <th>कृती</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($points as $point): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <strong><?= esc($point['name']) ?></strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt me-1"></i><?= esc($point['location']) ?>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-globe me-1"></i>
                                            <?= number_format($point['latitude'], 6) ?>, <?= number_format($point['longitude'], 6) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if (!empty($point['description'])): ?>
                                            <?= esc(substr($point['description'], 0, 50)) ?>
                                            <?= strlen($point['description']) > 50 ? '...' : '' ?>
                                        <?php else: ?>
                                            <span class="text-muted">वर्णन नाही</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($point['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('station/points/edit/' . $point['id']) ?>" 
                                               class="btn btn-sm btn-warning" title="संपादित करा">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="showOnMap(<?= $point['latitude'] ?>, <?= $point['longitude'] ?>, '<?= esc($point['name']) ?>')"
                                                    title="नकाशावर पहा">
                                                <i class="fas fa-map"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?= $point['id'] ?>, '<?= esc($point['name']) ?>')"
                                                    title="डिलीट करा">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-times fa-5x text-muted mb-4"></i>
                    <h4 class="text-muted">कोणतेही पॉइंट्स सापडले नाहीत</h4>
                    <p class="text-muted mb-4">आपल्या पोलीस स्टेशनमध्ये अद्याप कोणतेही पॉइंट्स जोडले गेले नाहीत.</p>
                    <a href="<?= base_url('station/points/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>पहिला पॉइंट जोडा
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">पॉइंट डिलीट करा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>आपण खात्री आहात की आपल्याला <strong id="pointName"></strong> हा पॉइंट डिलीट करायचा आहे?</p>
                <p class="text-danger"><small>ही क्रिया पूर्ववत करता येणार नाही.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करा</button>
                <a href="#" id="deleteLink" class="btn btn-danger">डिलीट करा</a>
            </div>
        </div>
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">पॉइंट स्थान - <span id="mapPointName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(pointId, pointName) {
    document.getElementById('pointName').textContent = pointName;
    document.getElementById('deleteLink').href = '<?= base_url('station/points/delete/') ?>' + pointId;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function showOnMap(lat, lng, name) {
    document.getElementById('mapPointName').textContent = name;
    
    var mapModal = new bootstrap.Modal(document.getElementById('mapModal'));
    mapModal.show();
    
    // Initialize map when modal is shown
    mapModal._element.addEventListener('shown.bs.modal', function () {
        if (typeof google !== 'undefined') {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: {lat: parseFloat(lat), lng: parseFloat(lng)}
            });
            
            var marker = new google.maps.Marker({
                position: {lat: parseFloat(lat), lng: parseFloat(lng)},
                map: map,
                title: name
            });
            
            var infoWindow = new google.maps.InfoWindow({
                content: '<div><strong>' + name + '</strong><br>Lat: ' + lat + '<br>Lng: ' + lng + '</div>'
            });
            
            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });
        } else {
            document.getElementById('map').innerHTML = '<div class="text-center py-5"><i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i><h5>Google Maps API उपलब्ध नाही</h5><p>निर्देशांक: ' + lat + ', ' + lng + '</p></div>';
        }
    });
}
</script>

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCG-v1V6ty9GNVMB2D5VTF3HCpIhU0g8uo&libraries=places"></script>

<?= $this->include('station/layout/footer') ?>
