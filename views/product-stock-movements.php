<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
    .badge-receive   { background: #e8f5e9; color: #2e7d32; }
    .badge-sale      { background: #fce4ec; color: #c62828; }
    .badge-return    { background: #e3f2fd; color: #1565c0; }
    .badge-adjust    { background: #fff3e0; color: #e65100; }
    .badge-expired   { background: #f3e5f5; color: #6a1b9a; }
    .badge-damaged   { background: #ffebee; color: #b71c1c; }
    .badge-price     { background: #e8eaf6; color: #283593; }
    .badge-reserve   { background: #f1f8e9; color: #33691e; }
    .badge-release   { background: #e0f7fa; color: #00695c; }
    .badge-transfer  { background: #fce4ec; color: #ad1457; }
</style>
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
            <div class="card">
                <div class="card-header flex-wrap">
                    <h4 class="card-title">Stock Movements / Logs</h4>
                    <div class="d-flex align-items-center gap-2">
                        <select id="facilityFilter" class="form-control default-select" style="min-width:220px;">
                            <option value="">All Facilities</option>
                        </select>
                        <select id="actionFilter" class="form-control default-select" style="min-width:160px;">
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
                        <table id="tblMovements" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Facility</th>
                                    <th>Batch</th>
                                    <th>Type</th>
                                    <th>Qty Change</th>
                                    <th>New Balance</th>
                                    <th>Reference</th>
                                    <th>By</th>
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

        var badgeMap = {
            'Receive': 'badge-receive',
            'Sale': 'badge-sale',
            'Return': 'badge-return',
            'Adjustment': 'badge-adjust',
            'Expired': 'badge-expired',
            'Damaged': 'badge-damaged',
            'Price Change': 'badge-price',
            'Reserve': 'badge-reserve',
            'Release': 'badge-release',
            'Transfer': 'badge-transfer'
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
                        const badgeClass = badgeMap[item.action_label] || 'badge-secondary';
                        const qtyColor = qty < 0 ? 'text-danger' : 'text-success';

                        tbl.row.add([
                            index + 1,
                            escapeHtml(item.created_at || '-'),
                            escapeHtml(item.product_name) + '<br><small>' + escapeHtml(item.sku) + '</small>',
                            escapeHtml(item.facility_name || '-'),
                            '<strong>' + escapeHtml(item.batch_number || '-') + '</strong>',
                            '<span class="badge light ' + badgeClass + '">' + escapeHtml(item.action_label) + '</span>',
                            '<span class="' + qtyColor + ' fw-bold">' + formatNumber(qty) + '</span>',
                            formatNumber(item.new_balance),
                            escapeHtml(item.reference_id || '-'),
                            escapeHtml(item.created_by || '-'),
                            escapeHtml(item.remarks || '-')
                        ]);
                    });
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
