<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Program</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Program List</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Programs List</h4>
                    <?php if ($_SESSION["role_id"] == 1) : ?>
                        <a href="<?= $basePath ?>/add-program" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> New Program
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblProgram" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Agency</th>
                                    <th>Budget</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <?php if ($_SESSION["role_id"] == 1) : ?>
                                        <th>Action</th>
                                    <?php endif; ?>
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

<!-- ================= EDIT MODAL ================= -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="mb-0 fw-bold">Edit Program</h5>
                    <small class="text-muted">Update program information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">

                <!-- Program Information -->
                <h6 class="text-muted text-uppercase mb-3">Program Information</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" id="edit_code" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" id="edit_name" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Agency</label>
                    <select id="edit_agency_id" class="form-control select2"></select>
                </div>

                <hr>

                <!-- Budget & Dates -->
                <h6 class="text-muted text-uppercase mb-3">Budget &amp; Schedule</h6>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Total Budget</label>
                            <input type="number" id="edit_total_budget" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Remaining Budget</label>
                            <input type="number" id="edit_remaining_budget" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Used Budget</label>
                            <input type="text" id="edit_used_budget" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" id="edit_start_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" id="edit_end_date" class="form-control">
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Settings -->
                <h6 class="text-muted text-uppercase mb-3">Settings</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="d-block">Distribution Type</label>
                            <div class="d-flex align-items-center gap-2">
                                <span id="editCashLabel" class="fw-bold">Cash (₱)</span>
                                <div class="form-check form-switch mx-2">
                                    <input type="checkbox"
                                        class="form-check-input"
                                        id="edit_asset_switch"
                                        name="asset_type"
                                        value="1">
                                    <label class="form-check-label" for="edit_asset_switch"></label>
                                </div>
                                <span id="editUnitLabel">Units (Qty)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select id="edit_status" class="form-control">
                                <option value="1">Active</option>
                                <option value="2">Closed</option>
                                <option value="3">Paused</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="updateBtn">Update</button>
            </div>

        </div>
    </div>
</div>

<!-- ================= VIEW MODAL ================= -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalTitle">Program Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h5 class="mb-1" id="viewName">—</h5>
                <small class="text-muted" id="viewCode" style="font-family:monospace;font-size:12px;"></small>
                <div class="mt-3">
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Agency</span>
                        <span class="fw-bold text-end" id="viewAgency">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Distribution Type</span>
                        <span class="fw-bold text-end" id="viewDistType">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Total Budget</span>
                        <span class="fw-bold text-end" id="viewTotalBudget">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Remaining</span>
                        <span class="fw-bold text-end" id="viewRemaining">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Used</span>
                        <span class="fw-bold text-end" id="viewUsed">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Start Date</span>
                        <span class="fw-bold text-end" id="viewStart">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">End Date</span>
                        <span class="fw-bold text-end" id="viewEnd">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Status</span>
                        <span class="fw-bold text-end" id="viewStatus">—</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small">
                        <span class="text-muted">Created</span>
                        <span class="fw-bold text-end" id="viewCreated">—</span>
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

<script>
    $(document).ready(function() {

        let table = $("#tblProgram").DataTable({
            responsive: true,
            order: [],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        const canManage = <?= ($_SESSION["role_id"] ?? 0) == 1 ? 'true' : 'false' ?>;

        const API = "<?= $baseURL ?>/controller/ctrl-program.php";
        const AGENCY_API = "<?= $baseURL ?>/controller/ctrl-agency.php";

        let agencyOptions = "";

        $('.select2').select2();

        function loadAgency(callback) {
            $.ajax({
                url: AGENCY_API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_AGENCY"
                }),
                success: function(res) {
                    agencyOptions = `<option value="">Select Agency</option>`;
                    if (res.code == 0) {
                        res.data.forEach(a => {
                            agencyOptions += `<option value="${a.id}">${a.name}</option>`;
                        });
                    }

                    if (callback) callback();
                }
            });
        }

        function loadData() {
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PROGRAM"
                }),
                success: function(res) {
                    table.clear();
                    if (res.code == 0) {
                        res.data.forEach((p, i) => {
                            const badgeMap = {
                                1: '<span class="badge light badge-success">Active</span>',
                                2: '<span class="badge light badge-danger">Closed</span>',
                                3: '<span class="badge light badge-warning">Paused</span>'
                            };
                            const statusBadge = badgeMap[p.status] || '<span class="badge light badge-secondary">' + p.status_text + '</span>';

                            let cells = [
                                i + 1,
                                p.code,
                                p.name,
                                p.agency_name ?? '-',
                                p.total_budget,
                                statusBadge,
                                p.created_at
                            ];

                            if (canManage) {
                                cells.push(`
                            <button class="btn btn-sm btn-info viewBtn"
                                data-id="${p.id}"
                                data-code="${p.code}"
                                data-name="${p.name}"
                                data-agency="${p.agency_name ?? '-'}"
                                data-total="${p.total_budget}"
                                data-remaining="${p.remaining_budget}"
                                data-start="${p.start_date}"
                                data-end="${p.end_date}"
                                data-status="${p.status}"
                                data-status-text="${p.status_text}"
                                data-asset="${p.asset_type ?? 0}"
                                data-created="${p.created_at}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary editBtn"
                                data-id="${p.id}"
                                data-code="${p.code}"
                                data-name="${p.name}"
                                data-agency="${p.agency_id}"
                                data-total="${p.total_budget}"
                                data-remaining="${p.remaining_budget}"
                                data-start="${p.start_date}"
                                data-end="${p.end_date}"
                                data-asset="${p.asset_type ?? 0}"
                                data-status="${p.status}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger deleteBtn"
                                data-id="${p.id}"
                                data-name="${p.name}">
                                <i class="fas fa-trash"></i>
                            </button>
                            `);
                            }

                            table.row.add(cells);

                        });

                        table.draw();
                    }
                }
            });
        }

        loadAgency(loadData);

        // Edit asset switch toggle
        $('#edit_asset_switch').on('change', function() {
            const isQty = $(this).is(':checked');
            $('#editCashLabel').toggleClass('fw-bold', !isQty);
            $('#editCashLabel').toggleClass('text-muted', isQty);
            $('#editUnitLabel').toggleClass('fw-bold', isQty);
            $('#editUnitLabel').toggleClass('text-muted', !isQty);
        });

        // Auto-calculate used budget
        function calcUsed() {
            const total = parseFloat($('#edit_total_budget').val()) || 0;
            const remaining = parseFloat($('#edit_remaining_budget').val()) || 0;
            $('#edit_used_budget').val((total - remaining).toFixed(2));
        }
        $('#edit_total_budget, #edit_remaining_budget').on('input', calcUsed);

        $(document).on("click", ".editBtn", function() {
            const asset = parseInt($(this).data("asset")) || 0;

            $("#edit_id").val($(this).data("id"));
            $("#edit_code").val($(this).data("code"));
            $("#edit_name").val($(this).data("name"));
            $("#edit_total_budget").val($(this).data("total"));
            $("#edit_remaining_budget").val($(this).data("remaining"));
            $("#edit_start_date").val($(this).data("start"));
            $("#edit_end_date").val($(this).data("end"));
            $("#edit_status").val($(this).data("status"));

            // Set asset switch
            $("#edit_asset_switch").prop('checked', asset === 1).trigger('change');

            calcUsed();

            $("#edit_agency_id").html(agencyOptions);
            $("#edit_agency_id").val($(this).data("agency")).trigger('change');

            $("#editModal").modal("show");
        });

        $("#updateBtn").click(function() {

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "EDIT_PROGRAM",
                    id: $("#edit_id").val(),
                    code: $("#edit_code").val(),
                    name: $("#edit_name").val(),
                    agency_id: $("#edit_agency_id").val(),
                    total_budget: $("#edit_total_budget").val(),
                    remaining_budget: $("#edit_remaining_budget").val(),
                    start_date: $("#edit_start_date").val(),
                    end_date: $("#edit_end_date").val(),
                    status: $("#edit_status").val(),
                    asset_type: $("#edit_asset_switch").is(":checked") ? 1 : 0
                }),
                success: function(res) {

                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $("#editModal").modal("hide");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }

                }
            });

        });

        $(document).on("click", ".deleteBtn", function() {
            const id = $(this).data("id");
            const name = $(this).data("name") || 'this program';

            Swal.fire({
                title: 'Delete "' + name + '"?',
                text: "This action cannot be undone",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Yes, delete it"
            }).then((res) => {

                if (res.isConfirmed) {

                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_PROGRAM",
                            id: id
                        }),
                        success: function(res) {

                            if (res.code == 0) {
                                Swal.fire("Deleted", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }

                        }
                    });

                }

            });

        });

        // ================= VIEW =================
        $(document).on("click", ".viewBtn", function() {
            const id = $(this).data("id");
            const code = $(this).data("code") || '—';
            const name = $(this).data("name") || '—';
            const agency = $(this).data("agency") || '—';
            const total = $(this).data("total") || 0;
            const remaining = $(this).data("remaining") || 0;
            const start = $(this).data("start") || '—';
            const end = $(this).data("end") || '—';
            const statusText = $(this).data("status-text") || '—';
            const statusVal = $(this).data("status");
            const asset = parseInt($(this).data("asset"));
            const created = $(this).data("created") || '—';

            const assetLabel = asset === 1 ? 'Units (Qty)' : 'Cash (₱)';
            const budgetUsed = parseFloat(total) - parseFloat(remaining);

            const badgeMap = {
                1: '<span class="badge light badge-success">Active</span>',
                2: '<span class="badge light badge-danger">Closed</span>',
                3: '<span class="badge light badge-warning">Paused</span>'
            };
            const statusBadge = badgeMap[statusVal] || '<span class="badge light badge-secondary">' + statusText + '</span>';

            $('#viewModalTitle').text('Program #' + id);
            $('#viewName').text(name);
            $('#viewCode').text(code);
            $('#viewAgency').text(agency);
            $('#viewDistType').text(assetLabel);
            $('#viewTotalBudget').text(total);
            $('#viewRemaining').text(remaining);
            $('#viewUsed').text(budgetUsed.toFixed(2));
            $('#viewStart').text(start);
            $('#viewEnd').text(end);
            $('#viewStatus').html(statusBadge);
            $('#viewCreated').text(created);

            $('#viewModal').modal('show');
        });

    });
</script>

<?= endSection() ?>