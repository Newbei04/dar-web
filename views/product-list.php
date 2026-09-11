<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>


<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Products List</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Products List</h4>

                    <a href="<?= $baseURL ?>add-product" class="btn btn-primary">
                        <i class="fa fa-plus mr-1"></i> Add Product
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblProduct" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="8%">Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th width="8%"># Facilities</th>
                                <th width="8%">Total Stock</th>
                                <th width="10%">Status</th>
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

<?= endSection() ?>


<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        var tblData = $('#tblProduct').DataTable({
            responsive: true,
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

        // Fallback thumbnail when an image file is missing
        function productThumbFallback() {
            return '<div style="width:50px;height:50px;border-radius:10%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted"></i></div>';
        }
        window.productThumbFallback = productThumbFallback;

        loadProducts();

        function loadProducts() {
            showLoader();
            tblData.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-products.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PRODUCT"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load products.", "error");
                        return;
                    }
                    var data = Array.isArray(res.data) ? res.data : [];
                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(item, i) {

                        var imagePrimary = item.image ?
                            `<img src="<?= $baseURL ?>assets/images/product/${item.image}" alt="Product" style="width:50px;height:50px;border-radius:10%;object-fit:cover;" onerror="this.outerHTML=productThumbFallback();">` :
                            productThumbFallback();

                        var productInfo =
                            `<span class="fas fa-box"></span> ` + escapeHtml(item.name || '-') + `<br>` +
                            `<span class="fas fa-barcode"></span> <small>` + escapeHtml(item.sku || '-') + `</small><br>` +
                            (item.unit ? `<span class="fas fa-ruler"></span> <small>` + escapeHtml(item.unit) + `</small>` : '');

                        var status = item.status == 1 ?
                            '<span class="badge light badge-success">Active</span>' :
                            '<span class="badge light badge-danger">Archived</span>';

                        var actions = `
                        <button class="btn btn-info mr-2 viewBtn" data-id="${item.id}" data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary shadow editBtn" data-id="${item.id}" data-toggle="tooltip" title="Update Product">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                        <button class="btn btn-danger shadow archiveBtn" data-id="${item.id}" data-status="${item.status}" data-toggle="tooltip" title="${item.status == 1 ? 'Archive product' : 'Restore product'}">
                            <i class="fas fa-trash"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            imagePrimary,
                            productInfo,
                            item.category_name ?? '-',
                            item.facility_count ?? 0,
                            formatNumber(item.total_stock ?? 0),
                            status,
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_PRODUCT failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load products.", "error");
                }
            });
        }

        // ================= VIEW (navigate to details page) =================
        $(document).on("click", ".viewBtn", function() {
            const id = $(this).data("id");
            window.location.href = "<?= $baseURL ?>product-details?id=" + id;
        });

        // ================= EDIT (navigate to edit page) =================
        $(document).on("click", ".editBtn", function() {
            let id = $(this).data("id");
            window.location.href = "<?= $baseURL ?>edit-product?id=" + id;
        });

        // ================= ARCHIVE / RESTORE PRODUCT =================
        $(document).on("click", ".archiveBtn", function() {
            let id = $(this).data("id");
            let currentStatus = parseInt($(this).data("status"));
            let newStatus = currentStatus == 1 ? 0 : 1;
            let action = newStatus == 1 ? "restore" : "archive";

            Swal.fire({
                icon: "warning",
                title: `${action.charAt(0).toUpperCase() + action.slice(1)} product?`,
                text: "This toggles the product status. Inventory records are preserved.",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: `Yes, ${action}`
            }).then(function(result) {
                if (!result.isConfirmed) return;

                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-products.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "UPDATE_PRODUCT",
                        id: id,
                        status: newStatus
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Success", res.message, "success");
                            loadProducts();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        closeLoader();
                        console.error(xhr.responseText);
                        Swal.fire("Error", "Server error occurred", "error");
                    }
                });
            });
        });

    });
</script>

<?= endSection() ?>
