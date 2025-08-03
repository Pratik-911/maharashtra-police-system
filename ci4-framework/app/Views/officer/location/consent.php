<?= $this->include('officer/layout/header') ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    स्थान ट्रॅकिंग परवानगी
                </h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-shield-alt fa-4x text-primary mb-3"></i>
                    <h5>आपली गोपनीयता महत्वाची आहे</h5>
                </div>

                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>स्थान ट्रॅकिंग माहिती:</h6>
                    <ul class="mb-0">
                        <li>आपले स्थान केवळ सक्रिय ड्यूटी दरम्यान ट्रॅक केले जाईल</li>
                        <li>डेटा सुरक्षित आणि एन्क्रिप्टेड स्वरूपात संग्रहीत केला जाईल</li>
                        <li>केवळ अधिकृत प्रशासक आपले स्थान पाहू शकतील</li>
                        <li>आपण कधीही ट्रॅकिंग बंद करू शकता</li>
                    </ul>
                </div>

                <div class="duty-info bg-light p-3 rounded mb-4">
                    <h6><i class="fas fa-calendar me-2"></i>सध्याची ड्यूटी:</h6>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>स्थान:</strong> <?= $duty['point_name'] ?><br>
                            <strong>दिनांक:</strong> <?= date('d/m/Y', strtotime($duty['date'])) ?>
                        </div>
                        <div class="col-sm-6">
                            <strong>वेळ:</strong> <?= date('H:i', strtotime($duty['start_time'])) ?> - <?= date('H:i', strtotime($duty['end_time'])) ?><br>
                            <strong>शिफ्ट:</strong> <?= $duty['shift'] == 'Day' ? 'दिवस' : 'रात्र' ?>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>महत्वाचे:</h6>
                    <p class="mb-0">
                        स्थान ट्रॅकिंग सुरू करण्यासाठी आपल्या ब्राउझरमध्ये स्थान परवानगी द्या. 
                        हे आपल्या ड्यूटी अनुपालनाच्या मोजमापासाठी आवश्यक आहे.
                    </p>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" id="grantConsent">
                        <i class="fas fa-check me-2"></i>
                        मी सहमत आहे - स्थान ट्रॅकिंग सुरू करा
                    </button>
                    <a href="<?= base_url('officer/dashboard') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        डॅशबोर्डवर परत जा
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('grantConsent').addEventListener('click', function() {
    const button = this;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>परवानगी मिळवत आहे...';
    
    if (!navigator.geolocation) {
        alert('आपला ब्राउझर स्थान ट्रॅकिंग समर्थित नाही');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check me-2"></i>मी सहमत आहे - स्थान ट्रॅकिंग सुरू करा';
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            // Location permission granted, now grant consent in backend
            $.ajax({
                url: '<?= base_url('officer/location/grant-consent') ?>',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('स्थान ट्रॅकिंग यशस्वीरित्या सुरू केले गेले!');
                        window.location.href = '<?= base_url('officer/dashboard') ?>';
                    } else {
                        alert('त्रुटी: ' + response.message);
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-check me-2"></i>मी सहमत आहे - स्थान ट्रॅकिंग सुरू करा';
                    }
                },
                error: function() {
                    alert('सर्व्हर त्रुटी. कृपया पुन्हा प्रयत्न करा.');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-check me-2"></i>मी सहमत आहे - स्थान ट्रॅकिंग सुरू करा';
                }
            });
        },
        function(error) {
            let errorMsg = 'स्थान परवानगी आवश्यक आहे';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg = 'स्थान परवानगी नाकारली गेली. कृपया ब्राउझर सेटिंग्जमध्ये परवानगी द्या आणि पृष्ठ रीफ्रेश करा.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg = 'स्थान माहिती उपलब्ध नाही. कृपया GPS चालू करा.';
                    break;
                case error.TIMEOUT:
                    errorMsg = 'स्थान मिळवण्यात वेळ संपला. कृपया पुन्हा प्रयत्न करा.';
                    break;
            }
            
            alert(errorMsg);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-check me-2"></i>मी सहमत आहे - स्थान ट्रॅकिंग सुरू करा';
        }
    );
});
</script>

<?= $this->include('officer/layout/footer') ?>
