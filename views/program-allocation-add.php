<?= startSection('css') ?>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Program</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Allocations</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Allocation</a></li>
        </ol>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-1">Add Allocation</h4>
                    <p class="text-muted small mb-0">Select a type to configure allocation details.</p>
                </div>
                <div class="card-body">
                    <!-- COMMON SECTION -->
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Program <span class="text-danger">*</span></label>
                            <select id="program_id" class="form-control select2">
                                <option value="">Select Program</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Branch <span class="text-danger">*</span></label>
                            <select id="branch_id" class="form-control select2">
                                <option value="">Select Branch</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Subsidy Type <span class="text-danger">*</span></label>
                            <select id="subsidy_type" class="form-control select2">
                                <!-- <option value="">Select Type</option> -->
                                <option value="0">Money Allocation</option>
                                <option value="1">Product Allocation</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label id="allocation_budget_label"
                                class="form-label text-uppercase fw-semibold small text-muted">
                                Allocation Amount
                            </label>

                            <div class="input-group">
                                <span class="input-group-text" id="allocation_budget_prefix">₱</span>
                                <input type="number" id="allocation_budget" class="form-control" placeholder="0">
                            </div>

                            <small id="allocation_limit" class="text-muted"></small>
                        </div>

                        <div class="col-md-6">
                            <label id="max_per_beneficiary_label" class="form-label text-uppercase fw-semibold small text-muted">Max Per Beneficiary</label>
                            <div class="input-group">
                                <span class="input-group-text" id="max_per_beneficiary_prefix">₱</span>
                                <input type="number" id="max_per_beneficiary" class="form-control" placeholder="0">
                            </div>
                        </div>

                    </div>

                    <!-- PRODUCT SECTION -->
                    <div id="productSection" class="row g-3 mt-2 d-none">
                        <div class="col-12">
                            <p class="text-uppercase fw-semibold small text-muted mb-2 border-bottom pb-1">Product Details</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Product</label>
                            <select id="product_id" class="form-control select2">
                                <option value="">Select Product</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Unit Subsidy</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" id="unit_subsidy_value" class="form-control" placeholder="0.00">
                            </div>
                        </div>

                    </div>

                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="<?= $basePath ?>/list-allocations" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button class="btn btn-primary px-4" id="saveBtn" disabled>
                            <i class="fas fa-save me-1"></i> Save Allocation
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/select2/js/select2.full.min.js"></script>
<script>
    $('.select2').select2();

    $(document).ready(function() {

        /* ================= INIT ================= */
        $('#productSection').addClass('d-none');
        $('#saveBtn').prop('disabled', true);

        /* ================= LOAD PROGRAM ================= */
        function loadProgram() {
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-program.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PROGRAM"
                }),
                success: function(res) {
                    let html = `<option value="">Select Program</option>`;

                    if (res.code == 0) {

                        programs = res.data;

                        res.data.forEach(p => {
                            html += `<option value="${p.id}">${p.name}</option>`;
                        });
                    }

                    $("#program_id").html(html).trigger('change');
                }
            });
        }

        $('#program_id').on('change', function() {

            let program_id = $(this).val();

            let program = programs.find(p => p.id == program_id);

            if (!program) {
                $('#allocation_limit').text('');
                return;
            }

            // Auto-select subsidy type based on asset_type and disable
            if (program.asset_type == "1") {
                $('#subsidy_type').val("1").trigger('change');
                $('#subsidy_type').prop('disabled', true);

                // Auto-set product from program
                if (program.product_id) {
                    let $prod = $('#product_id');
                    if ($prod.find(`option[value="${program.product_id}"]`).length) {
                        $prod.val(program.product_id).trigger('change');
                    } else {
                        $prod.append(`<option value="${program.product_id}" selected>${program.product_name || 'Product #' + program.product_id}</option>`).trigger('change');
                    }
                    $prod.prop('disabled', true);
                }
            } else {
                $('#subsidy_type').val("0").trigger('change');
                $('#subsidy_type').prop('disabled', true);
                $('#product_id').val('').prop('disabled', false).trigger('change');
            }

            // Display max budget/quantity
            if (program.asset_type == "1") {
                $('#allocation_limit').text(
                    `Max. ${parseFloat(program.total_budget).toLocaleString()} units`
                );
            } else {
                $('#allocation_limit').text(
                    `Max. ₱${parseFloat(program.total_budget).toLocaleString()}`
                );
            }

            $('#allocation_budget').attr('max', program.total_budget);

        });

        /* ================= LOAD BRANCH ================= */
        function loadBranch() {
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-branch.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BRANCH"
                }),
                success: function(res) {

                    let html = `<option value="">Select Branch</option>`;

                    if (res.code == 0) {
                        res.data.forEach(f => {
                            html += `<option value="${f.id}">${f.name}</option>`;
                        });
                    }

                    $("#branch_id").html(html).trigger('change');
                }
            });
        }

        // $('#branch_id').on('change', function() {
        //     let branch_id = $(this).val();

        //     loadProduct(branch_id);
        // });

        /* ================= LOAD PRODUCT ================= */
        function loadProduct() {
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-products.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PRODUCT",
                    // branch_id: branch_id
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

        loadProgram();
        loadProduct();
        loadBranch();

        /* ================= TYPE SWITCH ================= */
        $('#subsidy_type').on('change', function() {
            let subsidy_type = $(this).val();

            if (subsidy_type === "0") {
                $('#productSection').addClass('d-none');
                $('#allocation_budget_label').text('Allocation Amount');
                $('#allocation_budget_prefix').text('₱').show();
                $('#max_per_beneficiary_label').text('Max Amount Per Beneficiary');
                $('#max_per_beneficiary_prefix').text('₱').show();
            } else if (subsidy_type === "1") {
                $('#productSection').removeClass('d-none');
                $('#allocation_budget_label').text('Allocated Quantity');
                $('#allocation_budget_prefix').hide();
                $('#max_per_beneficiary_label').text('Max Quantity Per Beneficiary');
                $('#max_per_beneficiary_prefix').hide();
            } else {
                $('#productSection').addClass('d-none');
            }

            $('#saveBtn').prop('disabled', subsidy_type === '');
        });

        /* ================= SAVE ================= */
        $("#saveBtn").click(function() {

            let subsidy_type = $("#subsidy_type").val();
            let program_id = $("#program_id").val();
            let branch_id = $("#branch_id").val();
            let allocation_budget = $("#allocation_budget").val();
            let max_per_beneficiary = $("#max_per_beneficiary").val();
            let product_id = $("#product_id").val();
            let unit_subsidy_value = $("#unit_subsidy_value").val();

            /* ================= VALIDATION ================= */
            if (!program_id || !subsidy_type || !branch_id) {
                Swal.fire("Warning", "Program, Branch and Type are required", "warning");
                return;
            }

            if (!allocation_budget) {
                Swal.fire("Warning", subsidy_type === "0" ? "Please enter allocation amount" : "Please enter allocated quantity", "warning");
                return;
            }

            if (subsidy_type === "1") {
                if (!product_id) {
                    Swal.fire("Warning", "Product is required", "warning");
                    return;
                }
                if (!unit_subsidy_value) {
                    Swal.fire("Warning", "Unit subsidy value is required", "warning");
                    return;
                }
            }

            /* ================= SEND ================= */
            $("#saveBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-allocation.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_ALLOCATION",
                    subsidy_type: subsidy_type,
                    program_id: program_id,
                    branch_id: branch_id,
                    allocation_budget: subsidy_type === "0" ? allocation_budget : null,
                    allocated_quantity: subsidy_type === "1" ? allocation_budget : null,
                    max_per_beneficiary: max_per_beneficiary,
                    unit_subsidy_value: subsidy_type === "1" ? unit_subsidy_value : null,
                    product_id: subsidy_type === "1" ? product_id : null,
                }),
                success: function(res) {
                    if (res.code == 0) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: res.message,
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "<?= $basePath ?>/list-allocations";
                            }
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Server error occurred.", "error");
                },
                complete: function() {
                    $("#saveBtn").prop("disabled", false).html('<i class="fas fa-save me-1"></i> Save Allocation');
                }
            });

        });

    });
</script>
<?= endSection() ?>