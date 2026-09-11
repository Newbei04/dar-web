<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/select2/css/select2.min.css">
<?= endSection() ?>
<?= startSection('content') ?>
<div class="container-fluid">
    <div class="card-deck">

        <div class="card shadow border-0 mt-4">

            <!-- HEADER -->
            <div class="card-header">
                <h3 class="mb-0 font-weight-bold">Edit Land Parcel</h3>
                <small class="text-muted">Update agricultural land information</small>
            </div>

            <div class="card-body">

                <form id="editLandParcelForm">
                    <input type="hidden" id="id">

                    <!-- SECTION 1 -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Parcel Information</h5>

                        <div class="form-row">

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-semibold">Beneficiary</label>
                                <select class="form-control select2" id="beneficiary_id">
                                    <option value="">Select beneficiary</option>
                                </select>
                                <small class="text-muted">Select the land owner / beneficiary</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-semibold">Title Number</label>
                                <input type="text"
                                    class="form-control"
                                    id="title_number"
                                    placeholder="e.g. TN-2026-00001">
                            </div>

                        </div>
                    </div>

                    <hr>

                    <!-- SECTION 2 -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Land Details</h5>

                        <div class="form-row">

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-semibold">Total Area</label>
                                <div class="input-group">
                                    <input type="number"
                                        step="0.01"
                                        class="form-control"
                                        id="total_area_hectares"
                                        placeholder="0.00">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Ha</span>
                                    </div>
                                </div>
                                <small class="text-muted">Enter area in hectares</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-semibold">Land Use Type</label>
                                <select class="form-control select2" id="land_use_type">
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

                    <!-- SECTION 3 -->
                    <div class="mb-2">
                        <h5 class="text-primary mb-3">Survey Information</h5>

                        <div class="form-row">

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-semibold">Last Survey Date</label>
                                <input type="date" class="form-control" id="last_survey_date">
                                <small class="text-muted">Optional but recommended</small>
                            </div>

                        </div>
                    </div>

                </form>

            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="<?= $baseURL ?>list-land-parcel" class="btn btn-outline-secondary mr-2">Cancel</a>
                <button type="submit" form="editLandParcelForm" class="btn btn-primary px-4" id="saveBtn">
                    <i class="fe fe-save mr-1"></i> Update Land Parcel
                </button>
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
        const urlParams = new URLSearchParams(window.location.search);
        const parcelId = urlParams.get('id');

        if (!parcelId) {
            Swal.fire("Error", "No parcel ID provided.", "error").then(() => {
                window.location.href = '<?= $baseURL ?>list-land-parcel';
            });
            return;
        }

        loadBeneficiary(() => {
            loadParcel();
        });

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

        function loadParcel() {
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_PARCEL_DETAIL",
                    id: parcelId
                }),
                success: function(res) {
                    if (res.code == 0 && res.data) {
                        const p = res.data.parcel;
                        $("#id").val(p.id);
                        $("#beneficiary_id").val(p.beneficiary_id || '').trigger('change');
                        $("#title_number").val(p.title_number || '');
                        $("#total_area_hectares").val(p.total_area_hectares || '');
                        $("#land_use_type").val(p.land_use_type || '').trigger('change');
                        $("#last_survey_date").val(p.last_survey_date || '');
                    } else {
                        Swal.fire("Error", res.message || "Parcel not found.", "error").then(() => {
                            window.location.href = '<?= $baseURL ?>list-land-parcel';
                        });
                    }
                },
                error: function() {
                    Swal.fire("Error", "Failed to load parcel details.", "error");
                }
            });
        }

        $('#editLandParcelForm').on('submit', function(e) {
            e.preventDefault();

            $("#saveBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');

            showLoader();
            $.ajax({
                url: API,
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                data: JSON.stringify({
                    trans: "EDIT_LAND_PARCEL",
                    id: $('#id').val(),
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
                        Swal.fire("Success", "Land parcel updated successfully!", "success").then(() => {
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
                    $("#saveBtn").prop("disabled", false).html('<i class="fe fe-save mr-1"></i> Update Land Parcel');
                }
            });
        });
    });
</script>
<?= endSection() ?>