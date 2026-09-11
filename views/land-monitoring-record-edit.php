<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/select2/css/select2.min.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-land-records">Land</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Land Record</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Land Record</h4>
                    <a href="<?= $baseURL ?>list-land-records" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="editRecordForm">
                        <input type="hidden" id="id">

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
                                        <option value="Pending">Pending</option>
                                        <option value="Active">Active</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Released">Released</option>
                                        <option value="Expired">Expired</option>
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
                            <button type="submit" class="btn btn-primary px-4" id="saveBtn">
                                <i class="fa fa-save me-1"></i> Update Record
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

        const API = "<?= $baseURL ?>controller/ctrl-land-records.php";
        const urlParams = new URLSearchParams(window.location.search);
        const recordId = urlParams.get('id');

        if (!recordId) {
            Swal.fire("Error", "No record ID provided.", "error").then(() => {
                window.location.href = '<?= $baseURL ?>list-land-records';
            });
            return;
        }

        loadLandParcels(() => {
            loadBeneficiary(() => {
                loadRecord();
            });
        });

        // ======================
        // LOAD LAND PARCELS
        // ======================
        function loadLandParcels(cb) {
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
                            let label = p.title_number;
                            if (p.beneficiary_name) label += ` &mdash; ${p.beneficiary_name}`;
                            html += `<option value="${p.id}">${label}</option>`;
                        });
                    }
                    $("#land_parcel_id").html(html);
                    reinitSelect2('#land_parcel_id');
                    cb && cb();
                }
            });
        }

        // ======================
        // LOAD BENEFICIARIES
        // ======================
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
                    reinitSelect2('#beneficiary_id');
                    cb && cb();
                }
            });
        }

        // Auto-derive beneficiary when the parcel changes
        $("#land_parcel_id").on("change", function() {
            let pid = $(this).val();
            if (!pid) {
                $("#beneficiary_id").val("").trigger("change");
                return;
            }
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-land-parcel.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "GET_PARCEL_DETAIL", id: pid }),
                success: function(res) {
                    if (res.code == 0 && res.data?.parcel) {
                        $("#beneficiary_id").val(String(res.data.parcel.beneficiary_id ?? "")).trigger("change");
                    }
                }
            });
        });

        // Select a saved value case-insensitively; if nothing matches, append the
        // stored value as an option so the field always reflects the data.
        function setSelectValue($sel, value, fallbackLabel) {
            if (value === null || value === undefined || value === '') {
                $sel.val('').trigger('change');
                return;
            }
            let matched = false;
            $sel.find('option').each(function() {
                if (String($(this).val()).trim().toLowerCase() === String(value).trim().toLowerCase()) {
                    $(this).prop('selected', true);
                    matched = true;
                    return false;
                }
            });
            if (!matched) {
                $sel.append(new Option(fallbackLabel || value, value, true, true));
                reinitSelect2($sel);
            }
            $sel.trigger('change');
        }

        // ======================
        // LOAD RECORD DATA
        // ======================
        function loadRecord() {
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_RECORD_DETAIL",
                    id: recordId
                }),
                success: function(res) {
                    if (res.code == 0 && res.data) {
                        const r = res.data;
                        $("#id").val(r.id);
                        setSelectValue($("#land_parcel_id"), r.land_parcel_id);
                        setSelectValue($("#beneficiary_id"), r.beneficiary_id, r.beneficiary_name);
                        setSelectValue($("#certificate_status"), r.certificate_status);
                        $("#credit_limit").val(r.credit_limit || '');
                        $("#issued_date").val(r.issued_date || '');
                        $("#expiry_date").val(r.expiry_date || '');
                        $("#encrypted_signature").val(r.encrypted_signature || '');
                    } else {
                        Swal.fire("Error", res.message || "Record not found.", "error").then(() => {
                            window.location.href = '<?= $baseURL ?>list-land-records';
                        });
                    }
                },
                error: function() {
                    Swal.fire("Error", "Failed to load record details.", "error");
                }
            });
        }

        // ======================
        // SUBMIT FORM
        // ======================
        $('#editRecordForm').on('submit', function(e) {
            e.preventDefault();

            if (!$('#land_parcel_id').val() || !$('#certificate_status').val()) {
                Swal.fire("Error", "Please complete the required fields.", "error");
                return;
            }

            $("#saveBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');

            showLoader();
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "EDIT_RECORD",
                    id: $('#id').val(),
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
                            title: "Updated!",
                            text: res.message || "Record updated successfully!"
                        }).then(() => {
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
                    $("#saveBtn").prop("disabled", false).html('<i class="fa fa-save me-1"></i> Update Record');
                }
            });
        });

    });
</script>
<?= endSection() ?>