<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Program</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Allocations</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Allocation</a></li>
        </ol>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-1">Edit Allocation</h4>
                    <p class="text-muted small mb-0">Update the allocation budget and per-beneficiary limits.</p>
                </div>
                <div class="card-body">
                    <input type="hidden" id="allocation_id">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Program</label>
                            <input type="text" id="program_name" class="form-control" disabled>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Branch</label>
                            <input type="text" id="branch_name" class="form-control" disabled>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Subsidy Type</label>
                            <input type="text" id="subsidy_type" class="form-control" disabled>
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
                            <input type="text" id="product_name" class="form-control" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Unit Subsidy</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" id="unit_subsidy_value" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- SUMMARY -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Distributed</label>
                            <p class="fw-bold mb-0" id="distributed_budget">0</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Reserved</label>
                            <p class="fw-bold mb-0" id="reserved_budget">0</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-uppercase fw-semibold small text-muted">Status</label>
                            <span id="status_badge"></span>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="<?= $basePath ?>/list-allocations" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button class="btn btn-primary px-4" id="saveBtn">
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

        var parts = window.location.pathname.split('/').filter(Boolean);
        var allocation_id = parts[parts.length - 1];
        $('#allocation_id').val(allocation_id);

        function loadData() {
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-allocation.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_ALLOCATION",
                    id: allocation_id
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Allocation not found.", "error");
                        return;
                    }
                    var d = res.data;
                    var isProduct = d.subsidy_type == '1' || parseFloat(d.unit_subsidy_value) > 0;

                    $('#program_name').val(d.program_name || '-');
                    $('#branch_name').val(d.branch_name || '-');
                    $('#subsidy_type').val(isProduct ? 'Product Allocation' : 'Money Allocation');

                    $('#allocation_budget').val(d.allocated_budget);
                    $('#max_per_beneficiary').val(d.max_per_beneficiary);
                    $('#unit_subsidy_value').val(d.unit_subsidy_value);
                    $('#distributed_budget').text(d.distributed_budget ?? 0);
                    $('#reserved_budget').text(d.reserved_budget ?? 0);

                    if (isProduct) {
                        $('#productSection').removeClass('d-none');
                        $('#product_name').val(d.product_name || '-');
                        $('#allocation_budget_label').text('Allocated Quantity');
                        $('#allocation_budget_prefix').hide();
                        $('#max_per_beneficiary_label').text('Max Quantity Per Beneficiary');
                        $('#max_per_beneficiary_prefix').hide();
                    } else {
                        $('#productSection').addClass('d-none');
                    }

                    var statusText = d.status == 0 ? 'warning' : d.status == 1 ? 'info' : d.status == 2 ? 'success' : 'secondary';
                    var statusLabel = d.status == 0 ? 'Pending' : d.status == 1 ? 'In-progress' : d.status == 2 ? 'Processed' : 'Unknown';
                    $('#status_badge').html('<span class="badge light badge-' + statusText + '">' + statusLabel + '</span>');
                },
                error: function() {
                    closeLoader();
                    Swal.fire("Error", "Failed to load allocation.", "error");
                }
            });
        }

        $('#saveBtn').click(function() {
            var allocated_budget = $('#allocation_budget').val();
            var max_per_beneficiary = $('#max_per_beneficiary').val();
            var unit_subsidy_value = $('#unit_subsidy_value').val() || 0;

            if (!allocated_budget || parseFloat(allocated_budget) <= 0) {
                Swal.fire("Warning", "Please enter a valid allocation amount/quantity.", "warning");
                return;
            }

            $('#saveBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-allocation.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "EDIT_ALLOCATION",
                    id: allocation_id,
                    allocated_budget: allocated_budget,
                    unit_subsidy_value: unit_subsidy_value,
                    max_per_beneficiary: max_per_beneficiary
                }),
                success: function(res) {
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success").then(function() {
                            window.location.href = "<?= $basePath ?>/list-allocations";
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Server error occurred.", "error");
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Allocation');
                }
            });
        });

        loadData();
    });
</script>
<?= endSection() ?>
