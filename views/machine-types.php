<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
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
            <li class="breadcrumb-item"><a href="javascript:void(0)">Machine</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Machine Types</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-list-ul me-2 text-primary"></i>Machinery Type List
                    </h4>

                    <button type="button" class="btn btn-primary" id="btnAddType">
                        <i class="fa fa-plus mr-1"></i> Add Machinery Type
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblData" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th>Name</th>
                                <th>Details</th>
                                <th width="15%">Created</th>
                                <th width="15%">Updated</th>
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

<!-- ================= ADD MACHINERY TYPE MODAL ================= -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Machinery Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="add_name" placeholder="Enter name">
                </div>

                <div class="form-group mb-0">
                    <label>Details</label>
                    <textarea class="form-control" id="add_details" rows="3" placeholder="Enter details"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveAdd">
                    <i class="fa fa-plus mr-1"></i> Add Machinery Type
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= VIEW MACHINERY TYPE MODAL ================= -->
<div class="modal fade" id="viewModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-muted">
                <div>
                    <h5 class="modal-title">Machinery Type Details</h5>
                    <small id="view_subtitle">View machine type information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-sub">
                <div class="view-info-item">
                    <p class="view-info-label">ID</p>
                    <p class="view-info-value" id="view_id">-</p>
                </div>
                <div class="view-info-item">
                    <p class="view-info-label">Name</p>
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

<!-- ================= EDIT MACHINERY TYPE MODAL ================= -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Machinery Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">

                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit_name" placeholder="Enter name">
                </div>

                <div class="form-group mb-0">
                    <label>Details</label>
                    <textarea class="form-control" id="edit_details" rows="3" placeholder="Enter details"></textarea>
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

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        let tbl = $('#tblData').DataTable({
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
            tbl.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machine-type.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MACHINERY_TYPE",
                    limit: 1000
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load machine types.", "error");
                        return;
                    }
                    let data = res.data?.result || [];
                    if (data.length === 0) {
                        tbl.draw(false);
                        return;
                    }
                    data.forEach(function(item, i) {
                        let actions = `
                        <button class="btn btn-info mr-2 viewBtn"
                            data-id="${item.id}"
                            data-name="${item.name || ''}"
                            data-details="${item.details || ''}"
                            data-created_at="${item.created_at || ''}"
                            data-updated_at="${item.updated_at || ''}"
                            data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary shadow editBtn"
                            data-id="${item.id}"
                            data-name="${item.name || ''}"
                            data-details="${item.details || ''}"
                            data-toggle="tooltip" title="Update Details">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-danger shadow deleteBtn"
                            data-id="${item.id}"
                            data-toggle="tooltip" title="Delete Type">
                            <i class="fas fa-trash"></i>
                        </button>
                        `;

                        tbl.row.add([
                            i + 1,
                            item.name || "-",
                            item.details || "-",
                            item.created_at || "-",
                            item.updated_at || "-",
                            actions
                        ]);
                    });

                    tbl.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_MACHINERY_TYPE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load machine types.", "error");
                }
            });
        }

        /* ---------- ADD ---------- */
        $("#btnAddType").on("click", function() {
            $("#add_name").val('');
            $("#add_details").val('');
            $("#addModal").modal("show");
        });

        $("#btnSaveAdd").on("click", function() {
            let data = {
                trans: "ADD_MACHINERY_TYPE",
                name: $("#add_name").val().trim(),
                details: $("#add_details").val().trim()
            };

            if (!data.name) {
                Swal.fire("Required", "Name is required.", "warning");
                return;
            }

            showLoader("Saving...");
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machine-type.php",
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
                    console.error("ADD_MACHINERY_TYPE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to add machine type.", "error");
                }
            });
        });

        /* ---------- VIEW ---------- */
        $(document).on("click", ".viewBtn", function() {
            var id = $(this).data("id");
            var name = $(this).data("name") || '-';
            var details = $(this).data("details") || '-';
            $("#view_id").text(id);
            $("#view_subtitle").text(id || 'View machine type information');
            $("#view_name").text(name);
            $("#view_details").text(details);
            $("#view_created_at").text($(this).data("created_at") || '-');
            $("#view_updated_at").text($(this).data("updated_at") || '-');
            $("#viewModal").modal("show");
        });

        /* ---------- EDIT ---------- */
        $(document).on("click", ".editBtn", function() {
            $("#edit_id").val($(this).data("id"));
            $("#edit_name").val($(this).data("name"));
            $("#edit_details").val($(this).data("details"));
            $("#editModal").modal("show");
        });

        $("#btnSaveEdit").on("click", function() {
            let data = {
                trans: "EDIT_MACHINERY_TYPE",
                id: $("#edit_id").val(),
                name: $("#edit_name").val().trim(),
                details: $("#edit_details").val().trim()
            };

            if (!data.name) {
                Swal.fire("Required", "Name is required.", "warning");
                return;
            }

            showLoader("Saving...");
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machine-type.php",
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
                    console.error("EDIT_MACHINERY_TYPE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to update machine type.", "error");
                }
            });
        });

        /* ---------- DELETE ---------- */
        $(document).on("click", ".deleteBtn", function() {
            let id = $(this).data("id");

            Swal.fire({
                title: "Delete this record?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete"
            }).then((result) => {
                if (!result.isConfirmed) return;
                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-machine-type.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "DELETE_MACHINERY_TYPE",
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
