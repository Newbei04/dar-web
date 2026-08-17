<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Product Categories</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Product Categories</h4>

                    <button type="button" class="btn btn-primary" id="btnAddCategory">
                        <i class="fa fa-plus mr-1"></i> Add Category
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblCategory" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th>Name</th>
                                <th>Details</th>
                                <th width="10%">Status</th>
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

<!-- ================= ADD CATEGORY MODAL ================= -->
<div class="modal fade" id="addCategoryModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_name" placeholder="Enter category name" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_details" class="form-label">Details</label>
                        <textarea class="form-control" id="add_details" rows="3" placeholder="Short description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_status" class="form-label">Status</label>
                        <select class="form-control native-select" id="add_status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveCategoryBtn">
                        <i class="fa fa-save mr-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= EDIT CATEGORY MODAL ================= -->
<div class="modal fade" id="editCategoryModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id">

                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" placeholder="Enter category name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_details" class="form-label">Details</label>
                        <textarea class="form-control" id="edit_details" rows="3" placeholder="Short description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-control native-select" id="edit_status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updateCategoryBtn">
                        <i class="fa fa-save mr-1"></i> Update Category
                    </button>
                </div>
            </form>
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

        var tblData = $('#tblCategory').DataTable({
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
                url: "<?= $baseURL ?>controller/ctrl-product-category.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PRODUCT_CATEGORY"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load categories.", "error");
                        return;
                    }
                    var data = res.data || [];
                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(item, i) {

                        var status = item.status == 1 ?
                            '<span class="badge light badge-success">Active</span>' :
                            '<span class="badge light badge-danger">Inactive</span>';

                        var actions = `
                        <button class="btn btn-primary shadow editBtn" data-id="${item.id}" data-name="${item.name ? item.name.replace(/"/g, '&quot;') : ''}" data-details="${item.details ? item.details.replace(/"/g, '&quot;') : ''}" data-status="${item.status}" data-toggle="tooltip" title="Update Category">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                        <button class="btn btn-danger shadow deleteBtn" data-id="${item.id}" data-toggle="tooltip" title="Delete Category">
                            <i class="fas fa-trash"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            item.name ?? '-',
                            item.details ?? '-',
                            status,
                            item.created_at ?? '-',
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_PRODUCT_CATEGORY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load categories.", "error");
                }
            });
        }

        // ================= OPEN ADD MODAL =================
        $("#btnAddCategory").click(function() {
            $("#addCategoryForm")[0].reset();
            $("#addCategoryForm").find('select').val('1');
            $("#addCategoryModal").modal("show");
        });

        // ================= ADD CATEGORY =================
        $("#addCategoryForm").on("submit", function(e) {
            e.preventDefault();

            $("#saveCategoryBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Saving...');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-category.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_PRODUCT_CATEGORY",
                    name: $("#add_name").val(),
                    details: $("#add_details").val(),
                    status: $("#add_status").val()
                }),
                success: function(res) {
                    if (res.code == 0) {
                        $("#addCategoryModal").modal("hide");
                        Swal.fire("Success", res.message, "success");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    console.error("ADD_PRODUCT_CATEGORY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to save category.", "error");
                },
                complete: function() {
                    $("#saveCategoryBtn").prop("disabled", false).html('<i class="fa fa-save mr-1"></i> Save Category');
                }
            });
        });

        // ================= OPEN EDIT MODAL =================
        $(document).on("click", ".editBtn", function() {
            $("#edit_id").val($(this).data("id"));
            $("#edit_name").val($(this).data("name"));
            $("#edit_details").val($(this).data("details"));
            $("#edit_status").val($(this).data("status"));

            $("#editCategoryModal").modal("show");
        });

        // ================= UPDATE CATEGORY =================
        $("#editCategoryForm").on("submit", function(e) {
            e.preventDefault();

            $("#updateCategoryBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Updating...');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-category.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "UPDATE_PRODUCT_CATEGORY",
                    id: $("#edit_id").val(),
                    name: $("#edit_name").val(),
                    details: $("#edit_details").val(),
                    status: $("#edit_status").val()
                }),
                success: function(res) {
                    if (res.code == 0) {
                        $("#editCategoryModal").modal("hide");
                        Swal.fire("Success", res.message, "success");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    console.error("UPDATE_PRODUCT_CATEGORY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to update category.", "error");
                },
                complete: function() {
                    $("#updateCategoryBtn").prop("disabled", false).html('<i class="fa fa-save mr-1"></i> Update Category');
                }
            });
        });

        // ================= DELETE CATEGORY =================
        $(document).on("click", ".deleteBtn", function() {
            let id = $(this).data("id");

            Swal.fire({
                title: "Delete Category?",
                text: "This will mark the category as inactive.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete"
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-product-category.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "DELETE_PRODUCT_CATEGORY",
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
                    error: function(xhr) {
                        console.error("DELETE_PRODUCT_CATEGORY failed:", xhr.responseText);
                        Swal.fire("Error", "Failed to delete category.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>
