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

                    <!-- STEP 1: PROGRAM & BRANCH -->
                    <p class="text-uppercase fw-semibold small text-muted border-bottom pb-1 mb-3">1. Program &amp; Branch</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Program <span class="text-danger">*</span></label>
                            <select id="program_id" class="form-control select2">
                                <option value="">Select Program</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Branch <span class="text-danger">*</span></label>
                            <select id="branch_id" class="form-control select2">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                    </div>

                    <!-- PROGRAM BUDGET INFO BAR -->
                    <div id="budgetBar" class="row g-3 mt-1 d-none">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center flex-wrap gap-3 mb-0 py-2">
                                <span class="small mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong id="budget_type_label">Cash</strong>
                                </span>
                                <span class="small mb-0 ms-3">Total Budget: <strong id="budget_total">—</strong></span>
                                <span class="small mb-0 ms-3">Remaining: <strong id="budget_remaining">—</strong></span>
                                <span class="small mb-0 ms-3">Max this allocation: <strong id="budget_max">—</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: ALLOCATION DETAILS -->
                    <p class="text-uppercase fw-semibold small text-muted border-bottom pb-1 mb-3 mt-4">2. Allocation Details</p>
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Subsidy Type <span class="text-danger">*</span></label>
                            <select id="subsidy_type" class="form-control select2" disabled>
                                <option value="">Select Type</option>
                                <option value="0">Money Allocation</option>
                                <option value="1">Product Allocation</option>
                            </select>
                            <small class="text-muted">Automatically set by the selected program.</small>
                        </div>

                        <div class="col-md-4">
                            <label id="allocation_budget_label"
                                class="form-label text-uppercase fw-semibold small text-muted">
                                Allocation Amount <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-text" id="allocation_budget_prefix">₱</span>
                                <input type="number" id="allocation_budget" class="form-control" placeholder="0" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label id="max_per_beneficiary_label" class="form-label text-uppercase fw-semibold small text-muted">Max Per Beneficiary</label>
                            <div class="input-group">
                                <span class="input-group-text" id="max_per_beneficiary_prefix">₱</span>
                                <input type="number" id="max_per_beneficiary" class="form-control" placeholder="0" min="0" step="0.01">
                            </div>
                        </div>

                    </div>

                    <!-- STEP 3: PRODUCT DETAILS (product allocations only) -->
                    <div id="productSection" class="d-none">
                        <p class="text-uppercase fw-semibold small text-muted border-bottom pb-1 mb-3 mt-4">3. Product Details</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted">Product <span class="text-danger">*</span></label>
                                <select id="product_id" class="form-control select2">
                                    <option value="">Select Product</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-uppercase fw-semibold small text-muted">Unit Subsidy Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" id="unit_subsidy_value" class="form-control" placeholder="0.00" min="0" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LIVE BUDGET CHECKER -->
                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <small id="allocation_limit" class="text-muted d-block mb-1"></small>
                            <div id="budgetAlert" class="alert alert-danger d-none mb-0 py-2"></div>
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
<script>
    $(document).ready(function() {

        let programs = [];

        /* ================= SELECT2 HELPER ================= */
        function refreshSelect($el, html) {
            $el.html(html);
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
                $el.select2();
            }
            $el.trigger('change');
        }

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

                    refreshSelect($("#program_id"), html);
                }
            });
        }

        function getSelectedProgram() {
            let program_id = $("#program_id").val();
            return programs.find(p => p.id == program_id) || null;
        }

        $('#program_id').on('change', function() {
            let program = getSelectedProgram();

            // Reset dependent fields
            $('#allocation_budget').val('');
            $('#max_per_beneficiary').val('');
            $('#unit_subsidy_value').val('');
            $('#product_id').val('').prop('disabled', false);

            if (!program) {
                $('#budgetBar').addClass('d-none');
                $('#budgetAlert').addClass('d-none').html('');
                $('#allocation_limit').text('');
                $('#subsidy_type').val('').prop('disabled', true).trigger('change');
                $('#saveBtn').prop('disabled', true);
                return;
            }

            const isQty = String(program.asset_type) === "1";
            const remaining = parseFloat(program.remaining_budget != null ? program.remaining_budget : program.total_budget) || 0;
            const total = parseFloat(program.total_budget) || 0;
            const label = isQty ? 'units' : '₱';

            // Subsidy type is auto-detected from the program
            $('#subsidy_type').val(isQty ? "1" : "0").prop('disabled', true).trigger('change');

            // Budget info bar
            $('#budget_type_label').text(isQty ? 'Product' : 'Cash');
            $('#budget_total').text(isQty ? total.toLocaleString() + ' units' : '₱' + total.toLocaleString());
            $('#budget_remaining').text(isQty ? remaining.toLocaleString() + ' units' : '₱' + remaining.toLocaleString());
            $('#budget_max').text(isQty ? remaining.toLocaleString() + ' units' : '₱' + remaining.toLocaleString());
            $('#budgetBar').removeClass('d-none');

            // Auto-select product bound to the program
            if (isQty && program.product_id) {
                let $prod = $('#product_id');
                if ($prod.find(`option[value="${program.product_id}"]`).length) {
                    $prod.val(program.product_id).trigger('change');
                } else {
                    $prod.append(`<option value="${program.product_id}" selected>${program.product_name || 'Product #' + program.product_id}</option>`).trigger('change');
                }
                $prod.prop('disabled', true);
            }

            runBudgetCheck();
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
                    refreshSelect($("#branch_id"), html);
                }
            });
        }

        /* ================= LOAD PRODUCT ================= */
        function loadProduct() {
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-products.php",
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
                    refreshSelect($("#product_id"), html);
                }
            });
        }

        loadProgram();
        loadProduct();
        loadBranch();

        /* ================= TYPE SWITCH (field labels) ================= */
        $('#subsidy_type').on('change', function() {
            let subsidy_type = $(this).val();

            if (subsidy_type === "0") {
                $('#productSection').addClass('d-none');
                $('#allocation_budget_label').html('Allocation Amount <span class="text-danger">*</span>');
                $('#allocation_budget_prefix').text('₱').show();
                $('#max_per_beneficiary_label').text('Max Amount Per Beneficiary');
                $('#max_per_beneficiary_prefix').text('₱').show();
            } else if (subsidy_type === "1") {
                $('#productSection').removeClass('d-none');
                $('#allocation_budget_label').html('Allocated Quantity <span class="text-danger">*</span>');
                $('#allocation_budget_prefix').hide();
                $('#max_per_beneficiary_label').text('Max Quantity Per Beneficiary');
                $('#max_per_beneficiary_prefix').hide();
            } else {
                $('#productSection').addClass('d-none');
            }

            runBudgetCheck();
        });

        /* ================= LIVE BUDGET CHECKER ================= */
        $('#allocation_budget, #max_per_beneficiary, #unit_subsidy_value, #branch_id, #product_id').on('change input', runBudgetCheck);

        function runBudgetCheck() {
            const program = getSelectedProgram();
            const type = $('#subsidy_type').val();
            const amount = parseFloat($('#allocation_budget').val()) || 0;
            const max = parseFloat($('#max_per_beneficiary').val()) || 0;
            const $alert = $('#budgetAlert');
            const $save = $('#saveBtn');
            const $limit = $('#allocation_limit');

            if (!program || !type) {
                $alert.addClass('d-none').html('');
                $limit.text('');
                $save.prop('disabled', true);
                return;
            }

            const isQty = type === "1";
            const remaining = parseFloat(program.remaining_budget != null ? program.remaining_budget : program.total_budget) || 0;
            const moneyFmt = n => '₱' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const qtyFmt = n => n.toLocaleString() + ' units';

            let errors = [];

            if (amount <= 0) errors.push(isQty ? 'Enter a valid allocated quantity.' : 'Enter a valid allocation amount.');
            if (amount > remaining) errors.push(isQty ? `Quantity exceeds available units (max ${qtyFmt(remaining)}).` : `Amount exceeds available budget (max ${moneyFmt(remaining)}).`);
            if (max > 0 && max > amount) errors.push('Max per beneficiary cannot exceed the allocation amount.');
            if (isQty && !$('#product_id').val()) errors.push('Select a product.');
            if (isQty && !($('#unit_subsidy_value').val() > 0)) errors.push('Enter a unit subsidy value.');

            const after = remaining - amount;
            $limit.text(after >= 0 && amount > 0
                ? `After this allocation: ${isQty ? qtyFmt(after) : moneyFmt(after)} remaining.`
                : `Available: ${isQty ? qtyFmt(remaining) : moneyFmt(remaining)}.`);

            if (errors.length) {
                $alert.removeClass('alert-success').addClass('alert-danger').removeClass('d-none')
                    .html('<i class="fas fa-exclamation-circle me-1"></i>' + errors.join('<br><i class="fas fa-exclamation-circle me-1"></i>'));
            } else {
                $alert.addClass('d-none').html('');
            }

            $save.prop('disabled', errors.length > 0 || !program || !type || !$('#branch_id').val());
        }

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

            if (!allocation_budget || parseFloat(allocation_budget) <= 0) {
                Swal.fire("Warning", subsidy_type === "0" ? "Please enter a valid allocation amount" : "Please enter a valid allocated quantity", "warning");
                return;
            }

            if (subsidy_type === "1") {
                if (!product_id) {
                    Swal.fire("Warning", "Product is required", "warning");
                    return;
                }
                if (!unit_subsidy_value || parseFloat(unit_subsidy_value) <= 0) {
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