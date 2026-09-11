<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Branch</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List Branch</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Branch List</h4>

                    <button type="button" class="btn btn-primary" id="btnAddBranch">
                        <i class="fa fa-plus mr-1"></i> Add Branch
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblBranch" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="10%">Code</th>
                                <th>Name</th>
                                <th>Details</th>
                                <th width="15%">Created</th>
                                <th width="15%">Actions</th>
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
            <div class="modal-header">
                <h5 class="modal-title">Add Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Code</label>
                    <input type="text" id="add_code" class="form-control" placeholder="Auto-generated on save" readonly>
                    <small class="form-text text-muted">Branch code will be generated automatically (e.g. B001).</small>
                </div>

                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" id="add_name" class="form-control" placeholder="Enter branch name">
                </div>

                <div class="form-group mb-0">
                    <label>Details</label>
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
            <div class="modal-header">
                <h5 class="modal-title">Branch Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="35%" class="text-muted">Code</th>
                        <td id="view_code">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Name</th>
                        <td id="view_name">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Details</th>
                        <td id="view_details">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Created</th>
                        <td id="view_created_at">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Updated</th>
                        <td id="view_updated_at">-</td>
                    </tr>
                </table>
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
            <div class="modal-header">
                <h5 class="modal-title">Edit Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">

                <div class="form-group">
                    <label>Code</label>
                    <input type="text" id="edit_code" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" id="edit_name" class="form-control">
                </div>

                <div class="form-group mb-0">
                    <label>Details</label>
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
            }
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

                        var actions = `
                        <button class="btn btn-info mr-2 viewBtn" data-id="${row.id}" data-code="${row.code || '-'}" data-name="${row.name || '-'}" data-details="${row.details || '-'}" data-created_at="${row.created_at || '-'}" data-updated_at="${row.updated_at || '-'}" data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary shadow editBtn" data-id="${row.id}" data-code="${row.code || '-'}" data-name="${row.name || '-'}" data-details="${row.details || '-'}" data-toggle="tooltip" title="Update Details">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                        <button class="btn btn-danger shadow deleteBtn" data-id="${row.id}" data-toggle="tooltip" title="Delete Branch">
                            <i class="fas fa-trash"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            row.code || "-",
                            row.name || "-",
                            row.details || "-",
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
            $("#view_code").text($(this).data("code") || '-');
            $("#view_name").text($(this).data("name") || '-');
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
