<?= $this->include('admin/layout/header') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-map-marker-alt me-2"></i>
        ड्यूटी पॉइंट व्यवस्थापन
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/points/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>नवा पॉइंट जोडा
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            सर्व ड्यूटी पॉइंट्स
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($points)): ?>
            <div class="table-responsive">
                <table class="table table-striped data-table">
                    <thead>
                        <tr>
                            <th>आयडी</th>
                            <th>नाव</th>
                            <th>झोन</th>
                            <th>पोलीस स्टेशन</th>
                            <th>अक्षांश</th>
                            <th>रेखांश</th>
                            <th>त्रिज्या (मी)</th>
                            <th>शेवटचे अपडेट</th>
                            <th>कृती</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($points as $point): ?>
                            <tr>
                                <td><?= $point['point_id'] ?></td>
                                <td>
                                    <strong><?= $point['name'] ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $point['zone_id'] ?></span>
                                </td>
                                <td><?= $point['police_station_id'] ?></td>
                                <td>
                                    <small class="text-muted"><?= $point['latitude'] ?></small>
                                </td>
                                <td>
                                    <small class="text-muted"><?= $point['longitude'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $point['radius'] ?>m</span>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($point['last_updated'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/points/edit/' . $point['point_id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="संपादित करा">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="showOnMap(<?= $point['latitude'] ?>, <?= $point['longitude'] ?>, '<?= $point['name'] ?>')" 
                                                title="नकाशावर पहा">
                                            <i class="fas fa-map"></i>
                                        </button>
                                        <a href="<?= base_url('admin/points/delete/' . $point['point_id']) ?>" 
                                           class="btn btn-sm btn-outline-danger delete-btn" title="डिलीट करा">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">कोणतेही ड्यूटी पॉइंट्स नाहीत</h5>
                <p class="text-muted">नवा ड्यूटी पॉइंट जोडण्यासाठी वरील बटण दाबा</p>
                <a href="<?= base_url('admin/points/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>पहिला पॉइंट जोडा
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map me-2"></i>
                    <span id="modalTitle">पॉइंट स्थान</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px; border-radius: 10px;"></div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>अक्षांश:</strong> <span id="modalLat"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>रेखांश:</strong> <span id="modalLng"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
                <a id="openInGoogleMaps" href="#" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-2"></i>Google Maps मध्ये उघडा
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showOnMap(lat, lng, name) {
    document.getElementById('modalTitle').textContent = name;
    document.getElementById('modalLat').textContent = lat;
    document.getElementById('modalLng').textContent = lng;
    document.getElementById('openInGoogleMaps').href = `https://www.google.com/maps?q=${lat},${lng}`;
    
    // Initialize map (using OpenStreetMap as a free alternative)
    const mapDiv = document.getElementById('map');
    mapDiv.innerHTML = `
        <iframe 
            width="100%" 
            height="400" 
            frameborder="0" 
            scrolling="no" 
            marginheight="0" 
            marginwidth="0" 
            src="https://www.openstreetmap.org/export/embed.html?bbox=${lng-0.01},${lat-0.01},${lng+0.01},${lat+0.01}&layer=mapnik&marker=${lat},${lng}"
            style="border-radius: 10px;">
        </iframe>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('mapModal'));
    modal.show();
}

// Enhanced delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const pointName = this.closest('tr').querySelector('td:nth-child(2) strong').textContent;
            if (confirm(`आपल्याला खात्री आहे की आपण "${pointName}" हा पॉइंट डिलीट करू इच्छिता?\n\nहे कृती पूर्ववत केले जाऊ शकत नाही.`)) {
                window.location.href = this.href;
            }
        });
    });
});
</script>

<?= $this->include('admin/layout/footer') ?>
