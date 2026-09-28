<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
    .branch-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
    .branch-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eee;
        padding: 1.25rem 1.5rem;
    }
    .branch-card .card-body {
        padding: 1rem 1.5rem 1.5rem;
    }

    #tblBranch thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 12px 15px;
        white-space: nowrap;
    }
    #tblBranch tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    #tblBranch tbody tr:hover {
        background-color: #f8f9fc;
    }
    #tblBranch tbody tr:last-child td {
        border-bottom: none;
    }

    .branch-code-badge {
        display: inline-block;
        background: rgba(102,126,234,0.12);
        color: #667eea;
        font-weight: 600;
        font-family: monospace;
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }
    .branch-name-cell {
        font-weight: 600;
        color: #2d3436;
    }
    .branch-details-cell {
        color: #636e72;
        font-size: 0.9rem;
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        margin: 0 2px;
    }
    .btn-action:hover {
        transform: scale(1.1);
    }
    .btn-view {
        background: rgba(102,126,234,0.12);
        color: #667eea;
        border: none;
    }
    .btn-view:hover { background: #667eea; color: #fff; }
    .btn-edit {
        background: rgba(17,153,142,0.12);
        color: #11998e;
        border: none;
    }
    .btn-edit:hover { background: #11998e; color: #fff; }
    .btn-delete {
        background: rgba(245,87,108,0.12);
        color: #f5576c;
        border: none;
    }
    .btn-delete:hover { background: #f5576c; color: #fff; }

    .modal-header-muted {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #eef0f4;
        align-items: center;
    }
    .modal-header-muted .modal-title {
        font-weight: 600;
    }
    .modal-header-muted small {
        display: block;
        color: #6c757d;
    }
    .modal-body-sub {
        padding: 1.5rem;
    }
    .view-info-item {
        padding: 10px 0;
        border-bottom: 1px solid #f1f2f6;
    }
    .view-info-item:last-child {
        border-bottom: none;
    }
    .view-info-label {
        font-size: 0.8rem;
        color: #a4a4a4;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    .view-info-value {
        font-weight: 500;
        color: #2d3436;
        margin: 0;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Branch</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List Branch</a></li>
        </ol>
    </div>

    <!-- Branch Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card branch-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-list-ul me-2 text-primary"></i>Branch List
                    </h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="btnAddBranch">
                            <i class="fa fa-plus me-1"></i> Add Branch
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblBranch" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th width="12%">Code</th>
                                    <th>Name</th>
                                    <th>Details</th>
                                    <th width="15%">Created</th>
                                    <th width="12%">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- Loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= ADD BRANCH MODAL ================= -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-muted">
                <div>
                    <h5 class="modal-title">Add Branch</h5>
                    <small>Create a new branch</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-sub">
                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Code</label>
                    <input type="text" id="add_code" class="form-control" placeholder="Auto-generated on save" readonly>
                    <small class="form-text text-muted">Branch code will be generated automatically (e.g. B001).</small>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" id="add_name" class="form-control" placeholder="Enter branch name">
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-semibold">Details</label>
                    <textarea id="add_details" class="form-control" rows="3" placeholder="Enter branch details"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveAdd">
                    <i class="fa fa-plus mr-1"></i> Add Branch
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= VIEW BRANCH MODAL ================= -->
<div class="modal fade" id="viewModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-muted">
                <div>
                    <h5 class="modal-title">Branch Details</h5>
                    <small id="view_subtitle">View branch information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-sub">
                <div class="view-info-item">
                    <p class="view-info-label">Code</p>
                    <p class="view-info-value" id="view_code">-</p>
                </div>
                <div class="view-info-item">
                    <p class="view-info-label">Branch Name</p>
                    <p class="view-info-value" id="view_name">-</p>
                </div>
                <div class="view-info-item">
                    <p class="view-info-label">Details</p>
                    <p class="view-info-value" id="view_details">-</p>
                </div>

                <hr class="my-3">

                <h6 class="text-muted text-uppercase mb-2" style="font-size:0.8rem; letter-spacing:1px;">Timestamps</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Created</p>
                            <p class="view-info-value" id="view_created_at">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Updated</p>
                            <p class="view-info-value" id="view_updated_at">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= EDIT BRANCH MODAL ================= -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-muted">
                <div>
                    <h5 class="modal-title">Edit Branch</h5>
                    <small>Update branch information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-sub">
                <input type="hidden" id="edit_id">

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Code</label>
                    <input type="text" id="edit_code" class="form-control" readonly>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" id="edit_name" class="form-control">
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-semibold">Details</label>
                    <textarea id="edit_details" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveEdit">
                    <i class="fa fa-save mr-1"></i> Save Changes
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

        var tblData = $('#tblBranch').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            bFilter: false,
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            order: [[1, 'asc']]
        });

        loadData();

        function loadData() {
            showLoader();
            tblData.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-branch.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BRANCH"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load branches.", "error");
                        return;
                    }
                    var data = res.data || [];

                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(row, i) {

                        var details = row.details && row.details.trim() ?
                            '<span class="branch-details-cell">' + row.details + '</span>' :
                            '<span class="text-muted">—</span>';

                        var actions = `
                        <button class="btn-action btn-view viewBtn"
                            data-id="${row.id}"
                            data-code="${row.code || '-'}"
                            data-name="${row.name || '-'}"
                            data-details="${row.details || '-'}"
                            data-created_at="${row.created_at || '-'}"
                            data-updated_at="${row.updated_at || '-'}"
                            data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit editBtn"
                            data-id="${row.id}"
                            data-code="${row.code || '-'}"
                            data-name="${row.name || '-'}"
                            data-details="${row.details || '-'}"
                            data-toggle="tooltip" title="Edit Branch">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete deleteBtn" data-id="${row.id}" data-toggle="tooltip" title="Delete Branch">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            '<span class="branch-code-badge">' + (row.code || "-") + '</span>',
                            '<span class="branch-name-cell">' + (row.name || "-") + '</span>',
                            details,
                            row.created_at || "-",
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },

                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_BRANCH failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load branches.", "error");
                }
            });
        }

        /* ---------- ADD ---------- */
        $("#btnAddBranch").on("click", function() {
            $("#add_code").val('');
            $("#add_name").val('');
            $("#add_details").val('');
            $("#addModal").modal("show");
        });

        $("#btnSaveAdd").on("click", function() {
            let data = {
                trans: "ADD_BRANCH",
                name: $("#add_name").val().trim(),
                details: $("#add_details").val().trim()
            };

            if (!data.name) {
                Swal.fire("Required", "Branch name is required.", "warning");
                return;
            }

            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-branch.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(data),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $("#addModal").modal("hide");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("ADD_BRANCH failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to add branch.", "error");
                }
            });
        });

        /* ---------- VIEW ---------- */
        $(document).on("click", ".viewBtn", function() {
            var name = $(this).data("name") || '-';
            var code = $(this).data("code") || '-';
            $("#view_name").text(name);
            $("#view_subtitle").text(code || 'View branch information');
            $("#view_code").text(code);
            $("#view_details").text($(this).data("details") || '-');
            $("#view_created_at").text($(this).data("created_at") || '-');
            $("#view_updated_at").text($(this).data("updated_at") || '-');
            $("#viewModal").modal("show");
        });

        /* ---------- EDIT ---------- */
        $(document).on("click", ".editBtn", function() {
            $("#edit_id").val($(this).data("id"));
            $("#edit_code").val($(this).data("code"));
            $("#edit_name").val($(this).data("name"));
            $("#edit_details").val($(this).data("details"));
            $("#editModal").modal("show");
        });

        $("#btnSaveEdit").on("click", function() {
            let data = {
                trans: "EDIT_BRANCH",
                id: $("#edit_id").val(),
                code: $("#edit_code").val(),
                name: $("#edit_name").val().trim(),
                details: $("#edit_details").val().trim()
            };

            if (!data.name) {
                Swal.fire("Required", "Branch name is required.", "warning");
                return;
            }

            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-branch.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(data),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $("#editModal").modal("hide");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("EDIT_BRANCH failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to update branch.", "error");
                }
            });
        });

        /* ---------- DELETE ---------- */
        $(document).on("click", ".deleteBtn", function() {
            let id = $(this).data("id");
            Swal.fire({
                title: "Delete Branch?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete"
            }).then((result) => {
                if (!result.isConfirmed) return;
                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-branch.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "DELETE_BRANCH",
                        id: id
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Deleted", res.message, "success");
                            loadData();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        closeLoader();
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>