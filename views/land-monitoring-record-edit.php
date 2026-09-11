<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/select2/css/select2.min.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header">
            <h3 class="card-title">Edit Land Record</h3>
            <small class="text-muted">Update land record and certificate details</small>
        </div>
        <div class="card-body">
            <form id="editRecordForm">
                <input type="hidden" id="id">

                <div class="mb-4">
                    <h5 class="mb-3">Land & Beneficiary</h5>
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Land Parcel</label>
                            <select class="form-control select2" id="land_parcel_id">
                                <option value="">Select land parcel</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <h5 class="mb-3">Certificate Details</h5>

                    <div class="form-row">

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Certificate Status</label>
                            <select class="form-control select2" id="certificate_status">
                                <option value="">Select status</option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="approved">Approved</option>
                                <option value="released">Released</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="font-weight-semibold">Credit Limit</label>
                            <div class="input-group">
                                <span class="input-group-text">&#8369;</span>
                                <input type="number" class="form-control" id="credit_limit" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="font-weight-semibold">Issued Date</label>
                            <input type="date" class="form-control" id="issued_date">
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h5 class="mb-3">Digital Signature</h5>

                    <div class="form-group">
                        <label class="font-weight-semibold">Encrypted Signature</label>
                        <textarea class="form-control"
                            id="encrypted_signature"
                            rows="4"
                            placeholder="Paste or generate encrypted signature here..."></textarea>

                        <small class="text-muted">
                            This is used for verification and should not be manually edited unless necessary.
                        </small>
                    </div>
                </div>

            </form>

        </div>

        <hr>
        <div class="d-flex justify-content-end">
            <a href="<?= $baseURL ?>list-land-records" class="btn btn-outline-secondary mr-2">Cancel</a>
            <button type="submit" form="editRecordForm" class="btn btn-primary px-4" id="saveBtn">
                <i class="fe fe-save mr-1"></i> Update Record
            </button>
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
        const urlParams = new URLSearchParams(window.location.search);
        const recordId = urlParams.get('id');

        if (!recordId) {
            Swal.fire("Error", "No record ID provided.", "error").then(() => {
                window.location.href = '<?= $baseURL ?>list-land-records';
            });
            return;
        }

        loadLandParcels(() => {
            loadRecord();
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
                            html += `<option value="${p.id}">${p.title_number}</option>`;
                        });
                    }
                    $("#land_parcel_id").html(html);
                    cb && cb();
                }
            });
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
                        $("#land_parcel_id").val(r.land_parcel_id || '').trigger('change');
                        $("#certificate_status").val(r.certificate_status || '').trigger('change');
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
                    credit_limit: $('#credit_limit').val() || 0,
                    certificate_status: $('#certificate_status').val(),
                    issued_date: $('#issued_date').val(),
                    expiry_date: $('#expiry_date').val(),
                    encrypted_signature: $('#encrypted_signature').val()
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", "Record updated successfully!", "success").then(() => {
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
                    $("#saveBtn").prop("disabled", false).html('<i class="fe fe-save mr-1"></i> Update Record');
                }
            });
        });

    });
</script>
<?= endSection() ?>