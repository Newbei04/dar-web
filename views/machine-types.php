<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
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
                    <h4 class="card-title">Machinery Type List</h4>

                    <button type="button" class="btn btn-primary" id="btnAddType">
                        <i class="fa fa-plus mr-1"></i> Add Machinery Type
                    </button>
                </div>

                <div class="card-body">
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
            <div class="modal-header">
                <h5 class="modal-title">Machinery Type Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>ID</label>
                    <input type="text" class="form-control" id="view_id" readonly>
                </div>

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="view_name" readonly>
                </div>

                <div class="form-group mb-0">
                    <label>Details</label>
                    <textarea class="form-control" id="view_details" rows="3" readonly></textarea>
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
            $("#view_id").val($(this).data("id"));
            $("#view_name").val($(this).data("name"));
            $("#view_details").val($(this).data("details"));
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
                        if (res.code == 0) {
                            Swal.fire("Deleted", res.message, "success");
                            loadData();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>
