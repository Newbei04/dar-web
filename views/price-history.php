<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Products</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Price History</a></li>
        </ol>
    </div>

    <div class="alert alert-info d-flex align-items-center py-2 px-3" style="font-size:13px;">
        <i class="fas fa-history me-2"></i>
        Append-only audit log of selling price changes. Historical prices are never overwritten.
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header flex-wrap">
                    <h4 class="card-title">Price Change Log</h4>
                    <select id="facilityFilter" class="form-control default-select" style="min-width:240px;">
                        <option value="">All Facilities</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblPriceHistory" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Facility</th>
                                    <th>Type</th>
                                    <th>Old Price</th>
                                    <th>New Price</th>
                                    <th>Effective</th>
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

        var tbl = $('#tblPriceHistory').DataTable({
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
            if (value === null || value === undefined || value === '') return '-';
            return parseFloat(value).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

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
            loadHistory();
        });

        loadHistory();

        function loadHistory() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_PRICE_HISTORY",
                    facility_id: $('#facilityFilter').val() || 0,
                    limit: 500
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load history.", "error");
                        return;
                    }
                    let data = Array.isArray(res.data) ? res.data : [];
                    data.forEach(function(item, index) {
                        tbl.row.add([
                            index + 1,
                            escapeHtml(item.created_at || '-'),
                            escapeHtml(item.product_name) + '<br><small>' + escapeHtml(item.sku) + '</small>',
                            escapeHtml(item.facility_name || '-'),
                            '<span class="badge light badge-primary">' + escapeHtml(item.price_type) + '</span>',
                            item.old_price !== null ? '₱' + formatNumber(item.old_price) : '-',
                            '<strong class="text-success">₱' + formatNumber(item.new_price) + '</strong>',
                            escapeHtml(item.effective_date || '-'),
                            escapeHtml(item.created_by || '-'),
                            escapeHtml(item.remarks || '-')
                        ]);
                    });
                    tbl.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Failed to load history.", "error");
                }
            });
        }
    });
</script>
<?= endSection() ?>
