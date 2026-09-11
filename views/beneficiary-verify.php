<!-- Start the content for this page -->
<?= startSection('css') ?>
<style>
    body[data-theme-version="dark"] .bv-verify-page .text-dark {
        color: #e9edf3 !important;
    }
    body[data-theme-version="dark"] .bv-verify-page .text-muted {
        color: #9aa4b2 !important;
    }
    body[data-theme-version="dark"] .bv-verify-page .bg-light {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }
    body[data-theme-version="dark"] .bv-verify-page .border-white {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    body[data-theme-version="dark"] .bv-verify-page .bg-soft-primary,
    body[data-theme-version="dark"] .bv-verify-page .bg-soft-success {
        background-color: rgba(255, 255, 255, 0.07) !important;
    }
    body[data-theme-version="dark"] .bv-verify-page .img-thumbnail {
        background-color: #1b2430;
        border-color: #2a3545;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<script>
    function beneficiaryAvatarFallback(el) {
        el.onerror = null;
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="140" height="140">' +
            '<rect width="140" height="140" rx="70" fill="#e9ecef"/>' +
            '<circle cx="70" cy="55" r="24" fill="#adb5bd"/>' +
            '<path d="M22 120c4-26 24-38 48-38s44 12 48 38z" fill="#adb5bd"/></svg>';
        el.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
    }
</script>
<div class="row justify-content-center mb-5 bv-verify-page">
    <div class="col-12 col-xl-10">

        <div class="row mb-4">
            <div class="col-12 text-center text-md-start">
                <h2 class="h3 mb-1 text-primary"><i class="fas fa-shield-alt"></i> Identity Verification</h2>
                <p class="text-muted">Review beneficiary information and perform biometric card or face checks.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center p-4">
                        <div class="position-relative d-inline-block mb-3">
                            <img id="avatarImg" class="rounded-circle img-thumbnail shadow-sm bg-light"
                                style="height: 140px; width: 140px; object-fit: cover;"
                                src="" alt="Profile Avatar" onerror="beneficiaryAvatarFallback(this);">
                        </div>

                        <h3 class="h4 mb-0 text-dark font-weight-bold" id="name">Loading Name...</h3>
                        <p class="text-muted small mb-4" id="doc_num">ID: --</p>

                        <hr class="my-4 border-light">

                        <div class="text-start bg-light rounded p-3">
                            <div class="d-flex align-items-center mb-2 border-bottom border-white">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-calendar me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Date of Birth</small>
                                </div>
                                <strong class="text-dark " id="birthday">--</strong>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-white">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-venus-mars me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Gender</small>
                                </div>
                                <strong class="text-dark" id="gender">--</strong>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-white">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-heart me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Marital Status</small>
                                </div>
                                <strong class="text-dark" id="marital">--</strong>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-white">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-at me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Email Address</small>
                                </div>
                                <strong class="text-dark" id="email">--</strong>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-white">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-phone me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Mobile Number</small>
                                </div>
                                <strong class="text-dark" id="mobile">--</strong>
                            </div>
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-white">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-map-marker-alt me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Residential Address</small>
                                </div>
                                <strong class="text-dark" id="address">--</strong>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-home me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Assigned Facility</small>
                                </div>
                                <strong class="text-dark" id="facility">--</strong>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <span class="fas fa-info-circle me-3 text-primary h5 mb-0"></span>
                                    <small class="text-muted">Verification Status</small>
                                </div>
                                <strong class="text-dark" id="verification-status">--</strong>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="d-flex flex-column h-100 justify-content-between">

                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-soft-primary p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <i class="fas fa-user-check h2 text-primary m-0"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="card-title h5 mb-1 text-dark">1. Facial Biometric Verification</h4>
                                    <p class="text-muted small">Capture a live picture of the beneficiary using an active camera peripheral to analyze and match real-time facial points with system documents.</p>
                                    <button id="start-facial-scan" class="btn btn-outline-primary mt-2" type="button">
                                        <i class="fas fa-fingerprint me-1"></i> Launch Facial Scan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-soft-success p-3 rounded me-3 text-center" style="min-width: 60px;">
                                    <i class="fas fa-credit-card h2 text-success m-0"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="card-title h5 mb-1 text-dark">2. Hi-Card NFC Chip Verification</h4>
                                    <p class="text-muted small">Place the physical dynamic chip ID Smart Card over the linked reader device interface to securely fetch validation records.</p>
                                    <button id="start-consultation" class="btn btn-primary mt-2" type="button">
                                        <i class="fas fa-rss me-1"></i> Tap & Scan Hi-Card
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 bg-dark text-white">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h5 class="mb-0 text-white"><i class="fas fa-heartbeat me-1 text-warning"></i> Session Progress</h5>
                                <small class="text-muted">Conclude process when checks pass.</small>
                            </div>
                            <div>
                                <button id="end-consultation" class="btn btn-danger px-4" type="button">
                                    <i class="fas fa-power-off me-1"></i> Terminate Session
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Facial Scan Camera Modal -->
<div class="modal fade" id="facialScanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-fingerprint me-1"></i> Facial Scan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="facialVideo" class="w-100 rounded bg-dark" autoplay muted playsinline
                    style="height: 320px; object-fit: cover;"></video>
                <canvas id="facialCanvas" class="w-100 rounded d-none"></canvas>
                <div id="facialStatus" class="alert alert-info small mt-3 mb-0">
                    Align your face within the frame, then press Capture Photo.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="facialCaptureBtn">
                    <i class="fas fa-camera me-1"></i> Capture Photo
                </button>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>


<?= startSection('scripts-dashboard') ?>
<script>
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    function formatDocNum(d) {
        if (!d || d.length !== 9) return d || '-';
        return d.substring(0, 2) + '-' + d.substring(2, 8) + '-' + d.substring(8, 9);
    }

    function getStatusLabel(s) {
        if (s == 0) return 'For Verification';
        if (s == 1) return 'Verified';
        if (s == 2) return 'Inactive';
        if (s == 3) return 'Deactivated';
        return 'Unknown';
    }

    $(document).ready(function() {
        /* ================= LOAD DATA ================= */
        loadBeneficiary();

        function loadBeneficiary() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_BENEFICIARY",
                    id: getUrlParameter('id')
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let p = res.data;

                        $("#name").text(p.name);
                        $("#doc_num").text("ID / Doc Num: " + formatDocNum(p.doc_num));
                        $("#birthday").text(p.birthday);
                        $("#gender").text(p.gender_label);
                        $("#marital").text(p.marital_label);

                        $("#email").text(p.email);
                        $("#mobile").text(p.mobile);
                        $("#address").text(p.address);
                        $("#facility").text(p.facility || '-');
                        $("#verification-status").text(getStatusLabel(p.status));

                        if (p.profile) {
                            $("#avatarImg").attr("src", "<?= $baseURL ?>assets/images/profile/" + p.profile);
                        }
                    } else {
                        Swal.fire("Error", res.message || "Beneficiary not found.", "error");
                    }
                },
                error: function(xhr) {
                    console.error("GET_BENEFICIARY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load beneficiary details.", "error");
                }
            });
        }

        /* ================= BIOMETRIC / FACIAL EVENT HANDLERS ================= */
        let facialStream = null;
        let facialCaptured = false;

        function stopFacialCamera() {
            if (facialStream) {
                facialStream.getTracks().forEach(function(t) {
                    t.stop();
                });
                facialStream = null;
            }
        }

        async function startFacialCamera() {
            var video = document.getElementById('facialVideo');
            var canvas = document.getElementById('facialCanvas');
            var status = document.getElementById('facialStatus');
            var capBtn = document.getElementById('facialCaptureBtn');

            canvas.classList.add('d-none');
            video.classList.remove('d-none');
            capBtn.innerHTML = '<i class="fas fa-camera me-1"></i> Capture Photo';
            status.className = 'alert alert-info small mt-3 mb-0';
            status.textContent = 'Align your face within the frame, then press Capture Photo.';

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                status.className = 'alert alert-danger small mt-3 mb-0';
                status.textContent = 'Camera is not supported in this browser. Use a secure context (localhost or HTTPS) with Chrome/Edge.';
                return;
            }

            try {
                facialStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                video.srcObject = facialStream;
                await video.play();
            } catch (err) {
                status.className = 'alert alert-danger small mt-3 mb-0';
                status.textContent = 'Unable to access the camera. ' + (err.message || err.name);
            }
        }

        $('#start-facial-scan').on('click', function(e) {
            e.preventDefault();
            facialCaptured = false;
            startFacialCamera();
            $('#facialScanModal').modal('show');
        });

        $('#facialCaptureBtn').on('click', function(e) {
            e.preventDefault();
            var video = document.getElementById('facialVideo');
            var canvas = document.getElementById('facialCanvas');
            var status = document.getElementById('facialStatus');

            if (!facialCaptured) {
                if (!facialStream) {
                    status.className = 'alert alert-danger small mt-3 mb-0';
                    status.textContent = 'Camera is not running. Please launch the scan again.';
                    return;
                }
                canvas.width = video.videoWidth || video.clientWidth;
                canvas.height = video.videoHeight || video.clientHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.classList.remove('d-none');
                video.classList.add('d-none');
                $('#facialCaptureBtn').html('<i class="fas fa-redo me-1"></i> Retake');
                status.className = 'alert alert-success small mt-3 mb-0';
                status.textContent = 'Photo captured. Retake if needed, or close the dialog when done.';
                facialCaptured = true;
            } else {
                canvas.classList.add('d-none');
                video.classList.remove('d-none');
                $('#facialCaptureBtn').html('<i class="fas fa-camera me-1"></i> Capture Photo');
                status.className = 'alert alert-info small mt-3 mb-0';
                status.textContent = 'Align your face within the frame, then press Capture Photo.';
                facialCaptured = false;
            }
        });

        $('#facialScanModal').on('hidden.bs.modal', function() {
            stopFacialCamera();
            facialCaptured = false;
        });

        /* ================= NFC SMART CARD LOGIC CONTINUITY ================= */
        let cardReader;
        let isConnected = false;

        connectReader();

        function connectReader() {
            cardReader = new WebSocket('ws://' + location.hostname + ':8347');
            cardReader.onopen = () => {
                isConnected = true;
            };
            cardReader.onclose = () => {
                isConnected = false;
            };
            cardReader.onerror = (error) => {};
            cardReader.onmessage = (event) => {
                console.log('Response: ' + event.data);
                const response = JSON.parse(event.data);
                if (response.code == "0") {
                    submitRequest(response);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.title,
                        text: response.desc,
                        confirmButtonText: 'OKAY',
                        confirmButtonColor: "#0f766e",
                    });
                }
            };
        }

        $('#start-consultation').on('click', function(e) {
            e.preventDefault();
            if (typeof showLoader === 'function') {
                showLoader("Scanning Hi-Card", "Please wait while we are scanning your Hi-Card!");
            }
            if (isConnected) {
                const command = JSON.stringify({
                    command: "READ_PATIENT"
                });
                cardReader.send(command);
            } else {
                connectReader();
                Swal.fire({
                    icon: 'error',
                    title: 'Manila App Connections Failed!',
                    text: 'We cannot connect to Manila Resident App while processing your request. Please start the app!',
                    confirmButtonText: 'OKAY',
                    confirmButtonColor: "#0f766e",
                });
            }
        });

        function submitRequest(data) {
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-patient.php',
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    trans: "PATIENT_SCAN",
                    result: data
                }),
                success: function(response) {
                    if (response.code == "0") {
                        $('#start-consultation').prop("disabled", true);
                        $('#end-consultation').prop("disabled", false);
                        Swal.fire({
                            icon: 'success',
                            title: response.title,
                            text: response.desc,
                            confirmButtonText: 'OKAY',
                            confirmButtonColor: "#0f766e",
                            timer: 3000,
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.title,
                            text: response.desc,
                            confirmButtonText: 'OKAY',
                            confirmButtonColor: "#0f766e",
                        });
                    }
                }
            });
        }

        $('#end-consultation').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'question',
                title: "End Verification Session",
                text: "Are you sure you want to end this identity verification cycle?",
                showCancelButton: true,
                confirmButtonText: 'Yes, End',
                confirmButtonColor: "#d33",
                cancelButtonColor: "#0f766e"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= $baseURL ?>beneficiary-verify-list';
                }
            });
        });
    });
</script>
<?= endSection() ?>
