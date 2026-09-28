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
            <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Product Categories</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card user-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-tags me-2 text-primary"></i>Product Categories
                    </h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge-status badge-neutral" id="categoryCount">0 total</span>
                        <button type="button" class="btn btn-primary" id="btnAddCategory">
                            <i class="fa fa-plus me-1"></i> Add Category
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblCategory" class="display responsive nowrap w-100 user-table">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th width="22%">Name</th>
                                <th>Details</th>
                                <th width="12%">Status</th>
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
            <div class="modal-header modal-header-muted">
                <h5 class="modal-title">Add Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body modal-body-sub">
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
                        <i class="fa fa-save me-1"></i> Save Category
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
            <div class="modal-header modal-header-muted">
                <h5 class="modal-title">Edit Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm">
                <div class="modal-body modal-body-sub">
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
                        <i class="fa fa-save me-1"></i> Update Category
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
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            bFilter: false,
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6">>rtip',
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            order: [[1, 'asc']]
        });

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function escapeAttr(value) {
            return $('<div>').text(value ?? '').html().replace(/"/g, '&quot;');
        }

        function refreshTooltips() {
            if (!window.bootstrap || !bootstrap.Tooltip) return;
            var $tips = $('#tblCategory tbody [data-bs-toggle="tooltip"]');
            $tips.each(function() {
                var inst = bootstrap.Tooltip.getInstance(this);
                if (inst) inst.dispose();
            });
            $tips.each(function() {
                new bootstrap.Tooltip(this);
            });
        }

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
                        $('#categoryCount').text('0 total');
                        tblData.draw(false);
                        refreshTooltips();
                        return;
                    }
                    data.forEach(function(item, i) {

                        var status = item.status == 1 ?
                            '<span class="badge-status badge-active"><i class="fas fa-check-circle me-1"></i>Active</span>' :
                            '<span class="badge-status badge-inactive"><i class="fas fa-ban me-1"></i>Inactive</span>';

                        var actions = `
                        <button class="btn-action btn-edit editBtn" data-id="${item.id}" data-name="${escapeAttr(item.name || '')}" data-details="${escapeAttr(item.details || '')}" data-status="${item.status}" data-bs-toggle="tooltip" title="Update Category">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete deleteBtn" data-id="${item.id}" data-bs-toggle="tooltip" title="Delete Category">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            `<span class="user-name-cell">${escapeHtml(item.name ?? '-')}</span>`,
                            item.details ?
                                `<div class="user-contact-cell"><span>${escapeHtml(item.details)}</span></div>` :
                                '<span class="text-muted">—</span>',
                            status,
                            item.created_at ?? '-',
                            actions
                        ]);
                    });

                    $('#categoryCount').text(data.length + ' total');
                    tblData.draw(false);
                    refreshTooltips();
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

            $("#saveCategoryBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            showLoader();
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
                    closeLoader();
                    if (res.code == 0) {
                        $("#addCategoryModal").modal("hide");
                        Swal.fire("Success", res.message, "success");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("ADD_PRODUCT_CATEGORY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to save category.", "error");
                },
                complete: function() {
                    $("#saveCategoryBtn").prop("disabled", false).html('<i class="fa fa-save me-1"></i> Save Category');
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

            $("#updateCategoryBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');

            showLoader();
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
                    closeLoader();
                    if (res.code == 0) {
                        $("#editCategoryModal").modal("hide");
                        Swal.fire("Success", res.message, "success");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("UPDATE_PRODUCT_CATEGORY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to update category.", "error");
                },
                complete: function() {
                    $("#updateCategoryBtn").prop("disabled", false).html('<i class="fa fa-save me-1"></i> Update Category');
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

                showLoader();
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
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Deleted", res.message, "success");
                            loadData();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        closeLoader();
                        console.error("DELETE_PRODUCT_CATEGORY failed:", xhr.responseText);
                        Swal.fire("Error", "Failed to delete category.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>
