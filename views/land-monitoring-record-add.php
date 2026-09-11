<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/select2/css/select2.min.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-land-records">Land</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Land Record</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Add Land Record</h4>
                    <a href="<?= $baseURL ?>list-land-records" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="addRecordForm">
                        <!-- LAND & BENEFICIARY -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Land & Beneficiary</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="land_parcel_id" class="form-label">Land Parcel <span class="text-danger">*</span></label>
                                    <select id="land_parcel_id" class="form-control select2" required>
                                        <option value="">Select land parcel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="beneficiary_id" class="form-label">Beneficiary <span class="text-danger">*</span></label>
                                    <select id="beneficiary_id" class="form-control select2" required>
                                        <option value="">Select beneficiary</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- CERTIFICATE DETAILS -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Certificate Details</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="certificate_status" class="form-label">Certificate Status <span class="text-danger">*</span></label>
                                    <select id="certificate_status" class="form-control select2" required>
                                        <option value="">Select status</option>
                                        <option value="pending">Pending</option>
                                        <option value="active">Active</option>
                                        <option value="approved">Approved</option>
                                        <option value="released">Released</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="credit_limit" class="form-label">Credit Limit</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="credit_limit" placeholder="0.00" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="issued_date" class="form-label">Issued Date</label>
                                    <input type="date" class="form-control" id="issued_date">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control" id="expiry_date">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- DIGITAL SIGNATURE -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Digital Signature</small></h6>

                        <div class="mb-3">
                            <label for="encrypted_signature" class="form-label">Encrypted Signature</label>
                            <textarea class="form-control" id="encrypted_signature" rows="4" placeholder="Paste or generate encrypted signature here..."></textarea>
                            <small class="text-muted">
                                This is used for verification and should not be manually edited unless necessary.
                            </small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= $baseURL ?>list-land-records" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-save me-1"></i> Save Record
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
            width: '100%'
        });

        const API = "<?= $baseURL ?>controller/ctrl-land-records.php";

        // ======================
        // LOAD LAND PARCELS
        // ======================
        loadLandParcels();

        function loadLandParcels() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-land-parcel.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_LAND_PARCEL"
                }),
                success: function(res) {

                    let html = `<option value="">Select Land Parcel</option>`;

                    if (res.code == 0) {
                        res.data.forEach(p => {
                            html += `<option value="${p.id}">${p.title_number}</option>`;
                        });
                    }

                    $("#land_parcel_id").html(html);
                }
            });
        }

        loadBeneficiary();

        function loadBeneficiary(cb) {
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
                    cb && cb();
                }
            });
        }

        $("#land_parcel_id").on("change", function() {
            let pid = $(this).val();
            if (!pid) return;
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-land-parcel.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "GET_PARCEL_DETAIL", id: pid }),
                success: function(res) {
                    if (res.code == 0 && res.data?.parcel) {
                        $("#beneficiary_id").val(res.data.parcel.beneficiary_id || '').trigger('change');
                    }
                }
            });
        });

        // ======================
        // SUBMIT FORM (JSON.stringify ONLY)
        // ======================
        $('#addRecordForm').on('submit', function(e) {
            e.preventDefault();

            $("#addRecordForm button[type='submit']").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            showLoader();
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_RECORD",
                    land_parcel_id: $('#land_parcel_id').val(),
                    beneficiary_id: $('#beneficiary_id').val(),
                    credit_limit: $('#credit_limit').val() || 0,
                    certificate_status: $('#certificate_status').val(),
                    issued_date: $('#issued_date').val(),
                    expiry_date: $('#expiry_date').val(),
                    encrypted_signature: $('#encrypted_signature').val()
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire({
                            icon: "success",
                            title: "Saved!",
                            text: res.message || "Record saved successfully!"
                        }).then(function() {
                            window.location.href = '<?= $baseURL ?>list-land-records';
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    closeLoader();
                },
                complete: function() {
                    $("#addRecordForm button[type='submit']").prop("disabled", false).html('<i class="fa fa-save me-1"></i> Save Record');
                }
            });
        });

    });
</script>
<?= endSection() ?>