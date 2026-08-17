<?= startSection('css') ?>
<style>
    .img-thumb-wrap {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .img-thumb-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .img-thumb-wrap .img-remove {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 22px;
        height: 22px;
        border: none;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.9);
        color: #fff;
        font-size: 12px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .img-thumb-wrap .img-primary {
        position: absolute;
        bottom: 2px;
        left: 2px;
        font-size: 10px;
        background: rgba(13, 110, 253, 0.9);
        color: #fff;
        padding: 1px 6px;
        border-radius: 8px;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-products">Product</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Product</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Product</h4>
                    <a href="<?= $baseURL ?>list-products" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="editProductForm">
                        <input type="hidden" id="id">

                        <!-- PRODUCT INFORMATION -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Product Information</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select id="category_id" class="form-control native-select" required>
                                        <option value="" disabled selected>Select Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" placeholder="Enter product name" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control" id="sku" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="barcode" class="form-label">Barcode</label>
                                    <input type="text" class="form-control" id="barcode" placeholder="Optional">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <input type="text" class="form-control" id="unit" placeholder="e.g. pcs, kg">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" class="form-control native-select">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label d-block">Hazardous</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_hazardous">
                                        <label class="form-check-label" for="is_hazardous">This product is hazardous</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- DETAILS -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Details</small></h6>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="details" class="form-label">Description</label>
                                    <textarea class="form-control" id="details" rows="3" placeholder="Short description of the product"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- PRODUCT IMAGES -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Product Images</small></h6>

                        <div class="row" id="currentImages"></div>

                        <div class="mb-3 mt-3">
                            <input type="file" class="form-control" id="images" accept="image/*" multiple>
                            <small class="text-muted">Upload new images to add to this product.</small>
                        </div>
                        <div class="d-flex flex-wrap gap-2" id="imagePreview" style="gap:.5rem;"></div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= $baseURL ?>list-products" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-save me-1"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {

        let imageFiles = [];
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        // Fallback when an existing image file is missing
        window.productEditImgFallback = function(el) {
            const fallback = document.createElement('div');
            fallback.style.cssText = 'width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#e9ecef;';
            fallback.innerHTML = '<i class="fas fa-image text-muted"></i>';
            el.replaceWith(fallback);
        };

        if (!productId) {
            Swal.fire("Error", "No product ID provided.", "error").then(function() {
                window.location.href = "<?= $baseURL ?>list-products";
            });
            return;
        }

        // ================= LOAD CATEGORIES =================
        function loadCategories(selectedId) {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-category.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PRODUCT_CATEGORY"
                }),
                success: function(res) {
                    let html = `<option value="" disabled selected>Select Category</option>`;
                    if (res.code == 0 && Array.isArray(res.data)) {
                        res.data.forEach(function(item) {
                            let selected = (selectedId && item.id == selectedId) ? 'selected' : '';
                            html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                        });
                    }
                    $("#category_id").html(html);
                },
                error: function(xhr) {
                    console.error("Category error:", xhr.responseText);
                }
            });
        }

        // ================= LOAD PRODUCT =================
        function loadProduct() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-products.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_PRODUCT_DETAILS",
                    product_id: productId
                }),
                success: function(res) {
                    if (res.code != 0 || !res.data || !res.data.product) {
                        Swal.fire("Error", res.message || "Product not found.", "error").then(function() {
                            window.location.href = "<?= $baseURL ?>list-products";
                        });
                        return;
                    }

                    const p = res.data.product;

                    $("#id").val(p.id);
                    $("#name").val(p.name || '');
                    $("#sku").val(p.sku || '');
                    $("#barcode").val(p.barcode || '');
                    $("#unit").val(p.unit || '');
                    $("#details").val(p.details || '');
                    $("#status").val(p.status || 1);
                    $("#is_hazardous").prop('checked', p.is_hazardous == 1);

                    loadCategories(p.category_id);

                    // Current images
                    const images = res.data.images || [];
                    const $box = $("#currentImages").empty();
                    if (!images.length) {
                        $box.html('<div class="col-12"><small class="text-muted">No images yet.</small></div>');
                    }
                    images.forEach(function(img) {
                        const primary = img.is_primary == 1 ? '<span class="img-primary">Primary</span>' : '';
                        $box.append(`
                            <div class="col-md-3 mb-3">
                                <div class="img-thumb-wrap">
                                    <img src="<?= $baseURL ?>assets/images/product/${img.name}" alt="product image" onerror="productEditImgFallback(this)">
                                    ${primary}
                                </div>
                            </div>
                        `);
                    });
                },
                error: function(xhr) {
                    console.error("GET_PRODUCT_DETAILS failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load product.", "error");
                }
            });
        }

        // ================= IMAGE PREVIEW =================
        function renderImagePreview() {
            const $box = $("#imagePreview").empty();
            imageFiles.forEach(function(file, i) {
                const url = URL.createObjectURL(file);
                $box.append(`
                    <div class="img-thumb-wrap" data-index="${i}">
                        <img src="${url}" alt="preview ${i+1}">
                        <button type="button" class="img-remove" data-index="${i}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
            });
        }

        $("#images").on("change", function() {
            const newFiles = Array.from(this.files || []);
            if (newFiles.length + imageFiles.length > 10) {
                Swal.fire("Warning", "You can only upload up to 10 images.", "warning");
                this.value = "";
                return;
            }
            imageFiles = imageFiles.concat(newFiles);
            renderImagePreview();
        });

        $(document).on("click", ".img-remove", function() {
            const idx = parseInt($(this).data("index"));
            imageFiles.splice(idx, 1);
            $("#images").val("");
            renderImagePreview();
        });

        function fileToBase64(file) {
            return new Promise(function(resolve, reject) {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }

        // ================= SUBMIT =================
        $("#editProductForm").on("submit", async function(e) {
            e.preventDefault();

            if (!$("#category_id").val()) {
                Swal.fire("Warning", "Please select a category.", "warning");
                return;
            }
            if (!$("#name").val()) {
                Swal.fire("Warning", "Product name is required.", "warning");
                return;
            }

            const $btn = $("#editProductForm button[type='submit']");
            $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Updating...');

            const base64Array = [];
            for (const file of imageFiles) {
                base64Array.push(await fileToBase64(file));
            }

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-products.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "UPDATE_PRODUCT",
                    id: $("#id").val(),
                    category_id: $("#category_id").val(),
                    name: $("#name").val(),
                    barcode: $("#barcode").val(),
                    unit: $("#unit").val(),
                    details: $("#details").val(),
                    is_hazardous: $("#is_hazardous").is(":checked") ? 1 : 0,
                    status: $("#status").val(),
                    images: base64Array
                }),
                success: function(res) {
                    if (res.code == 0) {
                        Swal.fire({
                            icon: "success",
                            title: "Updated!",
                            text: res.message
                        }).then(function() {
                            window.location.href = "<?= $baseURL ?>list-products";
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    console.error("UPDATE_PRODUCT failed:", xhr.responseText);
                    Swal.fire("Error", "Server error. Please try again.", "error");
                },
                complete: function() {
                    $btn.prop("disabled", false).html('<i class="fa fa-save me-1"></i> Update Product');
                }
            });
        });

        loadProduct();
    });
</script>
<?= endSection() ?>
