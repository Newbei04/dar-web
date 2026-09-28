<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/css/user-table.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/css/product-table.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Inventory</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Stock Movements</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card user-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>Stock Movements / Logs
                    </h4>
                    <div class="table-toolbar">
                        <span class="badge-status badge-neutral" id="movementCount">0 records</span>
                        <select id="facilityFilter" class="form-control native-select movements-filter-facility">
                            <option value="">All Facilities</option>
                        </select>
                        <select id="actionFilter" class="form-control native-select movements-filter-action">
                            <option value="">All Types</option>
                            <option value="1">Receive</option>
                            <option value="2">Sale</option>
                            <option value="3">Return</option>
                            <option value="4">Adjustment</option>
                            <option value="5">Expired</option>
                            <option value="6">Damaged</option>
                            <option value="7">Price Change</option>
                            <option value="8">Reserve</option>
                            <option value="9">Release</option>
                            <option value="10">Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblMovements" class="display responsive nowrap w-100 user-table">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th width="12%">Date</th>
                                    <th>Product</th>
                                    <th width="11%">Facility</th>
                                    <th width="10%">Batch</th>
                                    <th width="9%">Type</th>
                                    <th width="8%">Qty Change</th>
                                    <th width="8%">New Balance</th>
                                    <th width="9%">Reference</th>
                                    <th width="10%">By</th>
                                    <th>Remarks</th>
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

        var tbl = $('#tblMovements').DataTable({
            responsive: true,
            order: [[1, 'desc']],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            bFilter: false,
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6">>rtip'
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

        // Maps action_type to a badge class. Mirrors $action_labels in ctrl-inventory.php.
        var badgeMap = {
            1: 'badge-receive',
            2: 'badge-sale',
            3: 'badge-return',
            4: 'badge-adjust',
            5: 'badge-expired',
            6: 'badge-damaged',
            7: 'badge-price',
            8: 'badge-reserve',
            9: 'badge-release',
            10: 'badge-transfer'
        };

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

        $('#facilityFilter, #actionFilter').change(function() {
            loadMovements();
        });

        loadMovements();

        function loadMovements() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_INVENTORY_LOGS",
                    facility_id: $('#facilityFilter').val() || 0,
                    transaction_type: $('#actionFilter').val() || 0,
                    limit: 500
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load movements.", "error");
                        return;
                    }
                    let data = Array.isArray(res.data) ? res.data : [];
                    data.forEach(function(item, index) {
                        const qty = parseFloat(item.quantity_changed);
                        const badgeClass = badgeMap[parseInt(item.action_type, 10)] || 'badge-neutral';
                        const qtyCell = qty > 0
                            ? '<span class="qty-up">+' + formatNumber(qty) + '</span>'
                            : qty < 0
                            ? '<span class="qty-down">' + formatNumber(qty) + '</span>'
                            : '<span class="text-muted">0</span>';

                        const productInfo = `
                            <div class="user-name-cell">${escapeHtml(item.product_name)}</div>
                            <div class="user-contact-cell"><i class="fas fa-barcode"></i><span>${escapeHtml(item.sku)}</span></div>
                        `;

                        const by = item.created_by
                            ? `<div class="user-name-cell">${escapeHtml(item.created_by)}</div>`
                            : '<span class="text-muted">-</span>';

                        tbl.row.add([
                            index + 1,
                            escapeHtml(item.created_at || '-'),
                            productInfo,
                            `<span class="batch-cell">${escapeHtml(item.facility_name || '-')}</span>`,
                            `<span class="batch-cell">${escapeHtml(item.batch_number || '-')}</span>`,
                            '<span class="badge-status ' + badgeClass + '">' + escapeHtml(item.action_label) + '</span>',
                            qtyCell,
                            '<span class="num-cell">' + formatNumber(item.new_balance) + '</span>',
                            item.reference_id
                                ? `<span class="batch-cell">${escapeHtml(item.reference_id)}</span>`
                                : '<span class="text-muted">-</span>',
                            by,
                            item.remarks
                                ? `<div class="user-contact-cell"><span>${escapeHtml(item.remarks)}</span></div>`
                                : '<span class="text-muted">-</span>'
                        ]);
                    });

                    $('#movementCount').text(data.length + ' record' + (data.length === 1 ? '' : 's'));
                    tbl.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Failed to load movements.", "error");
                }
            });
        }
    });
</script>
<?= endSection() ?>
