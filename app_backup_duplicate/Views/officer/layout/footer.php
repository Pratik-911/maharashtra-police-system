    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Location tracking functionality
        let locationWatchId = null;
        let isTracking = false;
        
        function startLocationTracking(dutyId) {
            if (!navigator.geolocation) {
                alert('आपला ब्राउझर स्थान ट्रॅकिंग समर्थित नाही');
                return;
            }
            
            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 30000
            };
            
            locationWatchId = navigator.geolocation.watchPosition(
                function(position) {
                    updateLocationStatus('सक्रिय', 'success');
                    sendLocationUpdate(position.coords.latitude, position.coords.longitude);
                },
                function(error) {
                    console.error('Location error:', error);
                    updateLocationStatus('त्रुटी', 'danger');
                },
                options
            );
            
            isTracking = true;
            updateLocationStatus('सुरू करत आहे...', 'warning');
        }
        
        function stopLocationTracking() {
            if (locationWatchId) {
                navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = null;
            }
            isTracking = false;
            updateLocationStatus('बंद', 'secondary');
        }
        
        function sendLocationUpdate(latitude, longitude) {
            $.ajax({
                url: '<?= base_url('officer/location/update') ?>',
                method: 'POST',
                data: {
                    latitude: latitude,
                    longitude: longitude,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (!response.success) {
                        console.error('Location update failed:', response.message);
                        if (response.message.includes('संपली आहे')) {
                            stopLocationTracking();
                        }
                    }
                },
                error: function() {
                    console.error('Location update request failed');
                }
            });
        }
        
        function updateLocationStatus(status, type) {
            const statusElement = $('.location-status');
            if (statusElement.length) {
                statusElement.html(`
                    <span class="status-indicator status-${type === 'success' ? 'active' : 'inactive'}"></span>
                    स्थान: ${status}
                `);
            }
        }
        
        // Request location permission on page load if needed
        $(document).ready(function() {
            if ($('#locationTrackingEnabled').length && $('#locationTrackingEnabled').val() === '1') {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            console.log('Location permission granted');
                        },
                        function(error) {
                            if (error.code === error.PERMISSION_DENIED) {
                                alert('कृपया स्थान परवानगी द्या आणि पृष्ठ रीफ्रेश करा');
                            }
                        }
                    );
                }
            }
        });
    </script>
</body>
</html>
