<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Settings</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">All Logs</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title">All Logs</h4>
                    <select class="form-control native-select" id="filterModule" style="max-width:220px;">
                        <option value="">All Modules</option>
                        <option value="Booking">Booking</option>
                        <option value="Inventory">Inventory</option>
                        <option value="Pricing">Pricing</option>
                        <option value="Wallet">Wallet</option>
                        <option value="Review">Review</option>
                        <option value="Store">Store</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblLogs" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Date / Time</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Performed By</th>
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
        let tbl = $('#tblLogs').DataTable({
            responsive: true,
            order: [
                [1, 'desc']
            ],
            pageLength: 25,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        const baseURL = '<?= $baseURL ?>';

        const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[m]);

        function moduleBadge(module) {
            const map = {
                'Booking': 'badge-info',
                'Inventory': 'badge-primary',
                'Pricing': 'badge-warning',
                'Wallet': 'badge-success',
                'Review': 'badge-secondary',
                'Store': 'badge-dark'
            };
            const cls = map[module] || 'badge-secondary';
            return `<span class="badge light ${cls}">${escapeHtml(module)}</span>`;
        }

        function fmtDateTime(val) {
            if (!val) return '-';
            const d = new Date(String(val).replace(' ', 'T'));
            if (isNaN(d.getTime())) return val;
            return d.toLocaleString('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        loadData();

        function loadData() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: baseURL + 'controller/ctrl-logs.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_LOGS"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load logs.", "error");
                        return;
                    }
                    (res.data || []).forEach(function(item, i) {
                        tbl.row.add([
                            i + 1,
                            fmtDateTime(item.created_at),
                            moduleBadge(item.module),
                            escapeHtml(item.action),
                            escapeHtml(item.details),
                            escapeHtml(item.user_name)
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_LOGS failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load logs.", "error");
                }
            });
        }

        $('#filterModule').on('change', function() {
            const module = $(this).val();
            tbl.column(2).search(module ? '^' + module + '$' : '', true, false).draw();
        });
    });
</script>

<?= endSection() ?>
