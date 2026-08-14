<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Program</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Program List</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Program</a></li>
        </ol>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Add Program</h4>
                        <small class="text-muted">Create a new assistance program for beneficiaries</small>
                    </div>
                    <a href="<?= $basePath ?>/list-programs" class="btn btn-sm btn-outline-secondary ms-auto">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="programForm" novalidate>

                        <!-- PROGRAM INFORMATION -->
                        <p class="text-uppercase fw-semibold small text-muted border-bottom pb-1 mb-3">Program Information</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted" for="name">Program Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" class="form-control" placeholder="e.g. Rice Assistance Program" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted" for="agency_id">Agency <span class="text-danger">*</span></label>
                                <select id="agency_id" class="form-control select2" required>
                                    <option value="">Select Agency</option>
                                </select>
                            </div>
                        </div>

                        <!-- BUDGET & DISTRIBUTION -->
                        <p class="text-uppercase fw-semibold small text-muted border-bottom pb-1 mb-3 mt-4">Budget &amp; Distribution</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label id="total_budget_label" class="form-label text-uppercase fw-semibold small text-muted" for="total_budget">Total Budget <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="total_budget_prefix">₱</span>
                                    <input type="number" id="total_budget" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted d-block">Distribution Type <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center pt-2">
                                    <span id="cashLabel" class="fw-bold">Cash (₱)</span>
                                    <div class="form-check form-switch mx-3 mb-0">
                                        <input type="checkbox"
                                            class="form-check-input"
                                            id="asset_switch"
                                            name="asset_type"
                                            value="1">
                                        <label class="form-check-label" for="asset_switch"></label>
                                    </div>
                                    <span id="unitLabel" class="text-muted">Units (Qty)</span>
                                </div>
                            </div>
                        </div>

                        <!-- PRODUCT (only for Units) -->
                        <div class="row g-3 mt-1 d-none" id="productSection">
                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted" for="product_id">Product <span class="text-danger">*</span></label>
                                <select id="product_id" class="form-control select2">
                                    <option value="">Select Product</option>
                                </select>
                            </div>
                        </div>

                        <!-- SCHEDULE -->
                        <p class="text-uppercase fw-semibold small text-muted border-bottom pb-1 mb-3 mt-4">Schedule</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted" for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted" for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control">
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end">
                            <a href="<?= $basePath ?>/list-programs" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" id="saveBtn">
                                <i class="fas fa-save me-1"></i> Save Program
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
<script>
    $(document).ready(function() {

        const API = "<?= $baseURL ?>/controller/ctrl-program.php";
        const AGENCY_API = "<?= $baseURL ?>/controller/ctrl-agency.php";
        const PRODUCT_API = "<?= $baseURL ?>/controller/ctrl-products.php";

        $('.select2').select2();

        // ================= LOAD AGENCY =================
        function loadAgency() {
            $.ajax({
                url: AGENCY_API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_AGENCY"
                }),
                success: function(res) {
                    let html = `<option value="">Select Agency</option>`;
                    if (res.code == 0) {
                        res.data.forEach(a => {
                            html += `<option value="${a.id}">${a.name}</option>`;
                        });
                    }
                    $("#agency_id").html(html).trigger('change');
                }
            });
        }

        loadAgency();

        // ================= LOAD PRODUCT =================
        function loadProduct() {
            $.ajax({
                url: PRODUCT_API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PRODUCT"
                }),
                success: function(res) {
                    let html = `<option value="">Select Product</option>`;
                    if (res.code == 0) {
                        res.data.forEach(p => {
                            html += `<option value="${p.id}">${p.name}</option>`;
                        });
                    }
                    $("#product_id").html(html).trigger('change');
                }
            });
        }

        loadProduct();

        // ================= DISTRIBUTION TYPE SWITCH =================
        $('#asset_switch').on('change', function() {
            let isQuantity = $(this).is(':checked');

            $('#cashLabel').toggleClass('fw-bold text-dark', !isQuantity);
            $('#cashLabel').toggleClass('text-muted', isQuantity);
            $('#unitLabel').toggleClass('fw-bold text-dark', isQuantity);
            $('#unitLabel').toggleClass('text-muted', !isQuantity);

            if (isQuantity) {
                $('#total_budget_label').text('Total Quantity');
                $('#total_budget_prefix').text('Qty');
                $('#total_budget').attr('placeholder', '0');
                $('#total_budget').removeAttr('step');
                $('#productSection').removeClass('d-none');
                $('#product_id').prop('required', true);
            } else {
                $('#total_budget_label').text('Total Budget');
                $('#total_budget_prefix').text('₱');
                $('#total_budget').attr('placeholder', '0.00');
                $('#total_budget').attr('step', '0.01');
                $('#productSection').addClass('d-none');
                $('#product_id').val('').prop('required', false).trigger('change');
            }

            $('#total_budget').val('');
        });

        // ================= SUBMIT =================
        $("#programForm").submit(function(e) {
            e.preventDefault();

            const isQuantity = $('#asset_switch').is(':checked');
            const totalBudget = parseFloat($('#total_budget').val()) || 0;
            const productId = $('#product_id').val();

            if (!isQuantity && totalBudget <= 0) {
                Swal.fire("Warning", "Please enter a valid total budget.", "warning");
                return;
            }
            if (isQuantity && totalBudget <= 0) {
                Swal.fire("Warning", "Please enter a valid total quantity.", "warning");
                return;
            }
            if (isQuantity && !productId) {
                Swal.fire("Warning", "Please select a product.", "warning");
                return;
            }

            $("#saveBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_PROGRAM",
                    name: $("#name").val(),
                    agency_id: $("#agency_id").val(),
                    total_budget: totalBudget,
                    start_date: $("#start_date").val(),
                    end_date: $("#end_date").val(),
                    asset_type: isQuantity ? 1 : 0,
                    product_id: isQuantity ? productId : null
                }),
                success: function(res) {
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success").then(() => {
                            window.location.href = "<?= $baseURL ?>list-programs";
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Server error occurred.", "error");
                },
                complete: function() {
                    $("#saveBtn").prop("disabled", false).html('<i class="fas fa-save me-1"></i> Save Program');
                }
            });
        });

    });
</script>
<?= endSection() ?>
