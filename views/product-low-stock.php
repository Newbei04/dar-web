<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
    .inv-thumb {
        width: 40px;
        height: 40px;
        border-radius: 10%;
        object-fit: cover;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Inventory</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Low Stock</a></li>
        </ol>
    </div>

    <div class="alert alert-warning d-flex align-items-center py-2 px-3" style="font-size:13px;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Batches where <strong>available stock (current − reserved) ≤ reorder level</strong> are flagged as LOW STOCK.
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header flex-wrap">
                    <h4 class="card-title">Low Stock Batches</h4>
                    <div class="d-flex align-items-center gap-2">
                        <select id="facilityFilter" class="form-control default-select" style="min-width:240px;">
                            <option value="">All Facilities</option>
                        </select>
                        <a href="<?= $baseURL ?>add-inventory" class="btn btn-primary">
                            <i class="fa fa-plus mr-1"></i> Receive Stock
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblLowStock" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Facility</th>
                                    <th>Batch</th>
                                    <th>Available</th>
                                    <th>Reorder</th>
                                    <th>Cost</th>
                                    <th>Selling</th>
                                    <th>Expiry</th>
                                    <th>Storage</th>
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
<?= endSection() ?>

<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        var tbl = $('#tblLowStock').DataTable({
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
            return parseFloat(value ?? 0).toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        function formatMoney(value) {
            if (value === null || value === undefined || value === '') return '-';
            return '₱' + formatNumber(value);
        }

        function getExpiryBadge(expiryDate) {
            if (!expiryDate) return '<span class="text-muted">-</span>';
            const today = new Date(); today.setHours(0, 0, 0, 0);
            const exp = new Date(expiryDate);
            const diff = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));
            if (diff < 0) return '<span class="badge light badge-danger">' + escapeHtml(expiryDate) + ' (Expired)</span>';
            if (diff <= 60) return '<span class="badge light badge-warning">' + escapeHtml(expiryDate) + ' (Expiring)</span>';
            return escapeHtml(expiryDate);
        }

        // Facility filter
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-facility.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "LIST_FACILITY_BY_TYPE", facility_type: "1,4" }),
            success: function(res) {
                let opts = '<option value="">All Facilities</option>';
                (res.data || []).forEach(function(f) {
                    opts += `<option value="${f.id}">${escapeHtml(f.name)}</option>`;
                });
                $('#facilityFilter').html(opts);
            }
        });

        $('#facilityFilter').change(function() {
            loadLowStock();
        });

        loadLowStock();

        function loadLowStock() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_LOW_STOCK",
                    facility_id: $('#facilityFilter').val() || 0
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load low stock.", "error");
                        return;
                    }
                    let data = Array.isArray(res.data) ? res.data : [];
                    data.forEach(function(item, index) {
                        let productInfo =
                            `<span class="fas fa-box"></span> ${escapeHtml(item.product_name)}<br>` +
                            `<span class="fas fa-barcode"></span> <small>${escapeHtml(item.sku)}</small>`;

                        tbl.row.add([
                            index + 1,
                            productInfo,
                            escapeHtml(item.facility_name || '-'),
                            '<strong>' + escapeHtml(item.batch_number || '-') + '</strong>',
                            '<span class="text-danger font-weight-bold">' + formatNumber(item.available_stock) + '</span>',
                            formatNumber(item.reorder_level),
                            formatMoney(item.cost_price),
                            formatMoney(item.facility_price),
                            getExpiryBadge(item.expiry_date),
                            escapeHtml(item.storage_location || '-'),
                            `<a href="<?= $baseURL ?>add-inventory" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus me-1"></i> Receive</a>`
                        ]);
                    });
                    tbl.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Failed to load low stock.", "error");
                }
            });
        }
    });
</script>
<?= endSection() ?>
