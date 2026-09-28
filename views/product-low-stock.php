<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/css/user-table.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/css/product-table.css" rel="stylesheet">
<style>
    .low-stock-note {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        padding: 0.625rem 1rem;
        border-radius: 12px;
        background: rgba(240, 165, 0, 0.12);
        color: #b07d00;
        border: 1px solid rgba(240, 165, 0, 0.28);
    }

    [data-theme-version="dark"] .low-stock-note {
        background: rgba(240, 165, 0, 0.16);
        color: #ffc107;
    }

    .low-stock-filters {
        min-width: 240px;
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

    <div class="low-stock-note">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Batches where <strong>available stock (current &minus; reserved) &le; reorder level</strong> are flagged as LOW STOCK.</span>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card user-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock Batches
                    </h4>
                    <div class="table-toolbar">
                        <span class="badge-status badge-pending-status" id="lowStockCount">0 low</span>
                        <select id="facilityFilter" class="form-control native-select low-stock-filters">
                            <option value="">All Facilities</option>
                        </select>
                        <a href="<?= $baseURL ?>add-inventory" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Receive Stock
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblLowStock" class="display responsive nowrap w-100 user-table">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th>Product</th>
                                    <th width="12%">Facility</th>
                                    <th width="11%">Batch</th>
                                    <th width="10%">Available</th>
                                    <th width="9%">Reorder</th>
                                    <th width="10%">Cost</th>
                                    <th width="10%">Selling</th>
                                    <th width="12%">Expiry</th>
                                    <th width="10%">Storage</th>
                                    <th width="10%">Actions</th>
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
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            bFilter: false,
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6">>rtip',
            columnDefs: [
                { orderable: false, targets: [10] }
            ]
        });

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
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
            if (diff < 0) return '<span class="badge-status badge-inactive"><i class="fas fa-ban me-1"></i>' + escapeHtml(expiryDate) + ' (Expired)</span>';
            if (diff <= 60) return '<span class="badge-status badge-pending-status"><i class="fas fa-clock me-1"></i>' + escapeHtml(expiryDate) + ' (Expiring)</span>';
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
                        let productInfo = `
                            <div class="user-name-cell">${escapeHtml(item.product_name)}</div>
                            <div class="user-contact-cell"><i class="fas fa-barcode"></i><span>${escapeHtml(item.sku)}</span></div>
                        `;

                        let available = parseFloat(item.available_stock || 0);
                        let reorder = parseFloat(item.reorder_level || 0);
                        let availableCell = available <= 0
                            ? '<span class="stock-cell stock-out">' + formatNumber(available) + '</span>'
                            : '<span class="stock-cell stock-low">' + formatNumber(available) + '</span>';

                        tbl.row.add([
                            index + 1,
                            productInfo,
                            `<span class="batch-cell">${escapeHtml(item.facility_name || '-')}</span>`,
                            `<span class="batch-cell">${escapeHtml(item.batch_number || '-')}</span>`,
                            availableCell,
                            '<span class="num-cell">' + formatNumber(reorder) + '</span>',
                            '<span class="money-cell">' + formatMoney(item.cost_price) + '</span>',
                            '<span class="money-cell">' + formatMoney(item.facility_price) + '</span>',
                            getExpiryBadge(item.expiry_date),
                            `<div class="user-contact-cell"><i class="fas fa-warehouse"></i><span>${escapeHtml(item.storage_location || '-')}</span></div>`,
                            `<a href="<?= $baseURL ?>add-inventory" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus me-1"></i> Receive</a>`
                        ]);
                    });

                    $('#lowStockCount').text(data.length + ' low');
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
