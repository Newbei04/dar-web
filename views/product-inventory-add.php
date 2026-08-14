<?= startSection('css') ?>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-inventory">Inventory</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Receive Stock</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Receive Stock</h4>
                    <a href="<?= $baseURL ?>list-inventory" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="submitInventory">
                        <!-- PRODUCT & FACILITY -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Product & Facility</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="facility_id" class="form-label">Facility <span class="text-danger">*</span></label>
                                    <select id="facility_id" class="form-control default-select">
                                        <option value="" disabled selected>Select Facility</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                    <select id="product_id" class="form-control default-select">
                                        <option value="" disabled selected>Select Product</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="batch_number" class="form-label">Batch Number</label>
                                    <input type="text" class="form-control" id="batch_number" placeholder="e.g. BATCH-SEED-001">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control" id="expiry_date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="storage_location" class="form-label">Storage Location</label>
                                    <input type="text" class="form-control" id="storage_location" placeholder="e.g. Rack A-01">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- QUANTITIES & PRICING -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Quantities & Pricing</small></h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="current_stock" class="form-label">Quantity Received <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="current_stock" min="0" step="any" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reserved_stock" class="form-label">Reserved Stock</label>
                                    <input type="number" class="form-control" id="reserved_stock" min="0" step="any" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="available_stock" class="form-label">Available Stock</label>
                                    <input type="text" class="form-control bg-light" id="available_stock" readonly value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reorder_level" class="form-label">Reorder Level</label>
                                    <input type="number" class="form-control" id="reorder_level" min="0" step="any" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cost_price" class="form-label">Cost Price (₱)</label>
                                    <input type="number" class="form-control" id="cost_price" min="0" step="0.01" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="selling_price" class="form-label">Facility Selling Price (₱)</label>
                                    <input type="number" class="form-control bg-light" id="selling_price" min="0" step="0.01" readonly>
                                    <small class="form-text text-muted" id="sellingPriceHint">Auto-loaded from Facility Prices once facility + product are selected.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="receive_remarks" class="form-label">Remarks</label>
                                    <input type="text" class="form-control" id="receive_remarks" placeholder="Optional note for the movement log">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= $baseURL ?>list-inventory" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fa fa-box-open me-1"></i> Receive Stock
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

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function(c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
            });
        }

        // ================= AUTO-COMPUTE AVAILABLE =================
        function computeAvailable() {
            let current = parseFloat($("#current_stock").val()) || 0;
            let reserved = parseFloat($("#reserved_stock").val()) || 0;
            $("#available_stock").val(current - reserved);
        }

        $("#current_stock, #reserved_stock").on("keyup change", computeAvailable);

        // ================= LOAD FACILITIES =================
        function loadFacilities() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-facility.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_FACILITY_BY_TYPE",
                    facility_type: "1,4"
                }),
                success: function(res) {
                    $('#facility_id')
                        .empty()
                        .append('<option value="" disabled selected>Select Facility</option>');
                    (res.data || []).forEach(function(f) {
                        $('#facility_id').append(`<option value="${f.id}">${escapeHtml(f.name)}</option>`);
                    });
                    $('#facility_id').selectpicker('refresh');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        // ================= LOAD PRODUCTS =================
        function loadProducts() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-products.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PRODUCT"
                }),
                success: function(res) {
                    $('#product_id')
                        .empty()
                        .append('<option value="" disabled selected>Select Product</option>');
                    (res.data || []).forEach(function(p) {
                        if (p.status != 1) return;
                        $('#product_id').append(
                            `<option value="${p.id}">${escapeHtml(p.name)}${p.sku ? ' (' + escapeHtml(p.sku) + ')' : ''}</option>`
                        );
                    });
                    $('#product_id').selectpicker('refresh');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        loadFacilities();
        loadProducts();

        // ================= AUTO-LOAD FACILITY PRICE =================
        function loadFacilityPrice() {
            let facility_id = $("#facility_id").val();
            let product_id = $("#product_id").val();

            if (!facility_id || !product_id) {
                $("#selling_price").val('');
                $("#sellingPriceHint").html('Auto-loaded from Facility Prices once facility + product are selected.');
                return;
            }

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_FACILITY_PRICE",
                    product_id: product_id,
                    facility_id: facility_id
                }),
                success: function(res) {
                    let price = res && res.data ? res.data.selling_price : null;
                    if (price !== null && price !== undefined) {
                        $("#selling_price").val(price);
                        $("#sellingPriceHint").html('Selling price from Facility Prices.');
                    } else {
                        $("#selling_price").val('');
                        $("#sellingPriceHint").html('<span class="text-warning">No facility price set. Set it under Pricing &gt; Facility Prices before selling.</span>');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $("#facility_id, #product_id").on("change", loadFacilityPrice);

        // ================= SUBMIT =================
        $("#submitInventory").submit(function(e) {
            e.preventDefault();

            let facility_id = $("#facility_id").val();
            let product_id = $("#product_id").val();
            let current_stock = parseFloat($("#current_stock").val()) || 0;
            let reserved_stock = parseFloat($("#reserved_stock").val()) || 0;

            if (!facility_id) {
                Swal.fire("Validation", "Please select a facility", "warning");
                return;
            }
            if (!product_id) {
                Swal.fire("Validation", "Please select a product", "warning");
                return;
            }
            if (current_stock < 0) {
                Swal.fire("Validation", "Current stock is invalid", "warning");
                return;
            }
            if (reserved_stock < 0) {
                Swal.fire("Validation", "Reserved stock is invalid", "warning");
                return;
            }
            if (reserved_stock > current_stock) {
                Swal.fire("Validation", "Reserved stock cannot exceed current stock", "warning");
                return;
            }

            showLoader();

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_INVENTORY",
                    facility_id: facility_id,
                    product_id: product_id,
                    quantity: current_stock,
                    current_stock: current_stock,
                    reserved_stock: reserved_stock,
                    reorder_level: parseFloat($("#reorder_level").val()) || 0,
                    cost_price: parseFloat($("#cost_price").val()) || 0,
                    selling_price: parseFloat($("#selling_price").val()) || 0,
                    batch_number: $("#batch_number").val().trim(),
                    expiry_date: $("#expiry_date").val(),
                    storage_location: $("#storage_location").val().trim() || null,
                    remarks: $("#receive_remarks").val().trim(),
                    users_id: <?= $_SESSION['users_id'] ?? 0 ?>
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success").then(() => window.location.href = "<?= $baseURL ?>list-inventory");
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Something went wrong. Please try again.", "error");
                }
            });
        });

    });
</script>
<?= endSection() ?>
