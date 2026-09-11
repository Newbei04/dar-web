<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/select2/css/select2.min.css">
<?= endSection() ?>
<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-land-parcel">Land</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Land Parcel</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Add Land Parcel</h4>
                    <a href="<?= $baseURL ?>list-land-parcel" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="addLandParcelForm">
                        <!-- PARCEL INFORMATION -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Parcel Information</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="beneficiary_id" class="form-label">Beneficiary <span class="text-danger">*</span></label>
                                    <select id="beneficiary_id" class="form-control select2" required>
                                        <option value="">Select beneficiary</option>
                                    </select>
                                    <small class="text-muted">Select the land owner / beneficiary</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title_number" class="form-label">Title Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title_number" placeholder="e.g. TN-2026-00001" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- LAND DETAILS -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Land Details</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_area_hectares" class="form-label">Total Area <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" id="total_area_hectares" placeholder="0.00" required>
                                        <span class="input-group-text">Ha</span>
                                    </div>
                                    <small class="text-muted">Enter area in hectares</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="land_use_type" class="form-label">Land Use Type <span class="text-danger">*</span></label>
                                    <select id="land_use_type" class="form-control select2" required>
                                        <option value="">Select type</option>
                                        <option value="agricultural">Agricultural</option>
                                        <option value="residential">Residential</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="industrial">Industrial</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- SURVEY INFORMATION -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Survey Information</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_survey_date" class="form-label">Last Survey Date</label>
                                    <input type="date" class="form-control" id="last_survey_date">
                                    <small class="text-muted">Optional but recommended</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= $baseURL ?>list-land-parcel" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-save me-1"></i> Save Land Parcel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>
<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/select2/js/select2.full.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
        });

        const API = "<?= $baseURL ?>controller/ctrl-land-parcel.php";

        loadBeneficiary();

        function loadBeneficiary() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-users.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_USER_ROLE",
                    role_id: 3
                }),
                success: function(res) {

                    let html = `<option value="">Select Beneficiary</option>`;

                    if (res.code == 0) {
                        res.data.forEach(p => {
                            if (!p.profile) return;
                            let name = `${p.profile?.fname || ''} ${p.profile?.mname || ''} ${p.profile?.lname || ''}`.trim();
                            html += `<option value="${p.profile.id}">${name}</option>`;
                        });
                    }

                    $("#beneficiary_id").html(html);
                }
            });
        }

        $('#addLandParcelForm').on('submit', function(e) {
            e.preventDefault();

            $("#addLandParcelForm button[type='submit']").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            showLoader();
            $.ajax({
                url: API,
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                data: JSON.stringify({
                    trans: "ADD_LAND_PARCEL",
                    beneficiary_id: $('#beneficiary_id').val(),
                    title_number: $('#title_number').val(),
                    total_area_hectares: $('#total_area_hectares').val(),
                    latitude: null,
                    longitude: null,
                    land_use_type: $('#land_use_type').val(),
                    productivity_score: null,
                    last_survey_date: $('#last_survey_date').val()
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire({
                            icon: "success",
                            title: "Saved!",
                            text: res.message || "Saved successfully!"
                        }).then(function() {
                            window.location.href = '<?= $baseURL ?>list-land-parcel';
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    closeLoader();
                },
                complete: function() {
                    $("#addLandParcelForm button[type='submit']").prop("disabled", false).html('<i class="fa fa-save me-1"></i> Save Land Parcel');
                }
            });
        });
    });
</script>
<?= endSection() ?>