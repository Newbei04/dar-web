<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Products</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Facility Prices</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title">Facility Selling Prices</h4>
                    <div class="d-flex align-items-center gap-2">
                        <select id="facilityFilter" class="form-control default-select" style="min-width:240px;">
                            <option value="">All Facilities</option>
                        </select>
                        <button type="button" class="btn btn-primary" id="btnOpenPriceModal">
                            <i class="fas fa-tag me-1"></i> Set Facility Price
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblPrices" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Facility</th>
                                    <th>Selling Price</th>
                                    <th>Minimum</th>
                                    <th>Maximum</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============ SET / EDIT FACILITY PRICE MODAL ============ -->
<div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tag me-2 text-primary"></i><span id="priceModalTitle">Set Facility Price</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="priceRowId">
                <div class="mb-3">
                    <label class="form-label">Facility <span class="text-danger">*</span></label>
                    <select class="form-control default-select" id="priceFacility">
                        <option value="">Select Facility</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select class="form-control default-select" id="priceProduct">
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="priceSelling" min="0" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Effective Date</label>
                        <input type="date" class="form-control" id="priceEffective" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Minimum Price</label>
                        <input type="number" class="form-control" id="priceMin" min="0" step="0.01" placeholder="Optional">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Maximum Price</label>
                        <input type="number" class="form-control" id="priceMax" min="0" step="0.01" placeholder="Optional">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" id="priceRemarks" rows="2" placeholder="Optional note"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSavePrice">
                    <i class="fas fa-save me-1"></i> Save Price
                </button>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        var tbl = $('#tblPrices').DataTable({
            responsive: true,
            order: [[0, 'asc']],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function(char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                } [char];
            });
        }

        function formatNumber(value) {
            if (value === null || value === undefined || value === '') return '-';
            return parseFloat(value).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Facilities (types 1,4)
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-facility.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "LIST_FACILITY_BY_TYPE", facility_type: "1,4" }),
            success: function(res) {
                let opts = '<option value="">Select Facility</option>';
                (res.data || []).forEach(function(f) {
                    opts += `<option value="${f.id}">${escapeHtml(f.name)}</option>`;
                });
                $('#priceFacility').html(opts);

                let opts2 = '<option value="">All Facilities</option>';
                (res.data || []).forEach(function(f) {
                    opts2 += `<option value="${f.id}">${escapeHtml(f.name)}</option>`;
                });
                $('#facilityFilter').html(opts2);
            }
        });

        // Products
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-products.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "LIST_PRODUCT" }),
            success: function(res) {
                let opts = '<option value="">Select Product</option>';
                (res.data || []).forEach(function(p) {
                    if (p.status != 1) return;
                    opts += `<option value="${p.id}">${escapeHtml(p.name)}${p.sku ? ' (' + escapeHtml(p.sku) + ')' : ''}</option>`;
                });
                $('#priceProduct').html(opts);
            }
        });

        $('#facilityFilter').change(function() {
            loadPrices();
        });

        loadPrices();

        var priceRows = [];

        function loadPrices() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_FACILITY_PRICES",
                    facility_id: $('#facilityFilter').val() || 0
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load prices.", "error");
                        return;
                    }
                    priceRows = Array.isArray(res.data) ? res.data : [];
                    priceRows.forEach(function(item, index) {
                        tbl.row.add([
                            index + 1,
                            escapeHtml(item.product_name) + '<br><small>' + escapeHtml(item.sku) + '</small>',
                            escapeHtml(item.facility_name || '-'),
                            '<strong class="text-primary">₱' + formatNumber(item.selling_price) + '</strong>',
                            item.minimum_price !== null ? '₱' + formatNumber(item.minimum_price) : '-',
                            item.maximum_price !== null ? '₱' + formatNumber(item.maximum_price) : '-',
                            item.status == 1
                                ? '<span class="badge light badge-success">Active</span>'
                                : '<span class="badge light badge-secondary">Inactive</span>',
                            escapeHtml(item.updated_at || '-'),
                            '<button type="button" class="btn btn-outline-primary btn-sm editPriceBtn" data-index="' + index + '"><i class="fas fa-edit me-1"></i> Edit</button>'
                        ]);
                    });
                    tbl.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Failed to load prices.", "error");
                }
            });
        }

        function openPriceModal() {
            $('#priceModalTitle').text('Set Facility Price');
            $('#priceRowId').val('');
            $('#priceSelling').val('');
            $('#priceMin').val('');
            $('#priceMax').val('');
            $('#priceRemarks').val('');
            $('#priceEffective').val('<?= date('Y-m-d') ?>');
            $('#priceFacility').val('').trigger('change');
            $('#priceProduct').val('').trigger('change');
            $('#priceModal').modal('show');
        }

        $('#btnOpenPriceModal').on('click', openPriceModal);

        $(document).on('click', '.editPriceBtn', function() {
            const idx = $(this).data('index');
            const item = priceRows[idx];
            if (!item) return;

            $('#priceModalTitle').text('Edit Facility Price');
            $('#priceRowId').val(item.id);
            $('#priceFacility').val(String(item.facility_id)).trigger('change');
            $('#priceProduct').val(String(item.product_id)).trigger('change');
            $('#priceSelling').val(item.selling_price);
            $('#priceMin').val(item.minimum_price !== null ? item.minimum_price : '');
            $('#priceMax').val(item.maximum_price !== null ? item.maximum_price : '');
            $('#priceRemarks').val('');
            $('#priceEffective').val('<?= date('Y-m-d') ?>');
            $('#priceModal').modal('show');
        });

        $('#btnSavePrice').on('click', function() {
            const facility_id = $('#priceFacility').val();
            const product_id = $('#priceProduct').val();
            const selling_price = $('#priceSelling').val();

            if (!facility_id || !product_id || selling_price === '' || parseFloat(selling_price) < 0) {
                Swal.fire("Validation", "Facility, Product and a valid Selling Price are required", "warning");
                return;
            }

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "SET_FACILITY_PRICE",
                    product_id: product_id,
                    facility_id: facility_id,
                    selling_price: selling_price,
                    minimum_price: $('#priceMin').val(),
                    maximum_price: $('#priceMax').val(),
                    effective_date: $('#priceEffective').val(),
                    remarks: $('#priceRemarks').val(),
                    created_by: <?= $_SESSION['users_id'] ?? 0 ?>
                }),
                success: function(res) {
                    $('#btnSavePrice').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Price');
                    if (res.code == 0) {
                        Swal.fire("Saved", res.message + ' (old: ₱' + formatNumber(res.data.old_price) + ' → new: ₱' + formatNumber(res.data.new_price) + ')', "success");
                        $('#priceModal').modal('hide');
                        loadPrices();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    $('#btnSavePrice').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Price');
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Server error while saving price.", "error");
                }
            });
        });
    });
</script>
<?= endSection() ?>
