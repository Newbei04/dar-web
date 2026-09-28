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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Products List</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card user-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-box me-2 text-primary"></i>Products List
                    </h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge-status badge-neutral" id="productCount">0 total</span>
                        <a href="<?= $baseURL ?>add-product" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Add Product
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblProduct" class="display responsive nowrap w-100 user-table">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th width="6%">Image</th>
                                <th>Product Name</th>
                                <th width="14%">Category</th>
                                <th width="10%">Facilities</th>
                                <th width="10%">Total Stock</th>
                                <th width="12%">Status</th>
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
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            bFilter: false,
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6">>rtip',
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            order: [[2, 'asc']]
        });

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function formatNumber(value) {
            return parseFloat(value ?? 0).toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        function refreshTooltips() {
            if (!window.bootstrap || !bootstrap.Tooltip) return;
            var $tips = $('#tblProduct tbody [data-bs-toggle="tooltip"]');
            $tips.each(function() {
                var inst = bootstrap.Tooltip.getInstance(this);
                if (inst) inst.dispose();
            });
            $tips.each(function() {
                new bootstrap.Tooltip(this);
            });
        }

        // Fallback thumbnail when an image file is missing
        function productThumbFallback() {
            return '<span class="product-thumb-fallback"><i class="fas fa-image"></i></span>';
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
                        $('#productCount').text('0 total');
                        tblData.draw(false);
                        refreshTooltips();
                        return;
                    }
                    data.forEach(function(item, i) {

                        var imagePrimary = item.image ?
                            `<img src="<?= $baseURL ?>assets/images/product/${encodeURIComponent(item.image)}" alt="Product" class="product-thumb" onerror="this.outerHTML=productThumbFallback();">` :
                            productThumbFallback();

                        var productInfo = `
                            <div class="user-name-cell">${escapeHtml(item.name || '-')}</div>
                            <div class="user-contact-cell"><i class="fas fa-barcode"></i><span>${escapeHtml(item.sku || '-')}</span></div>
                            ${item.unit ? `<div class="user-contact-cell"><i class="fas fa-ruler"></i><span>${escapeHtml(item.unit)}</span></div>` : ''}
                        `;

                        var status = item.status == 1 ?
                            '<span class="badge-status badge-active"><i class="fas fa-check-circle me-1"></i>Active</span>' :
                            '<span class="badge-status badge-inactive"><i class="fas fa-ban me-1"></i>Archived</span>';

                        var actions = `
                        <button class="btn-action btn-view viewBtn" data-id="${item.id}" data-bs-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit editBtn" data-id="${item.id}" data-bs-toggle="tooltip" title="Update Product">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete archiveBtn" data-id="${item.id}" data-status="${item.status}" data-bs-toggle="tooltip" title="${item.status == 1 ? 'Archive product' : 'Restore product'}">
                            <i class="fas fa-${item.status == 1 ? 'trash-alt' : 'rotate-left'}"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            imagePrimary,
                            productInfo,
                            item.category_name ? `<span class="batch-cell">${escapeHtml(item.category_name)}</span>` : '<span class="text-muted">—</span>',
                            `<span class="num-cell">${item.facility_count ?? 0}</span>`,
                            `<span class="stock-cell">${formatNumber(item.total_stock ?? 0)}</span>`,
                            status,
                            actions
                        ]);
                    });

                    $('#productCount').text(data.length + ' total');
                    tblData.draw(false);
                    refreshTooltips();
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_PRODUCT failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load products.", "error");
                }
            });
        }

        // Bootstrap 5 tooltips must be re-bound after every DataTable redraw
        refreshTooltips();

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
