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

    #currentImages .position-relative { border-radius: 8px; overflow: hidden; }
    #currentImages img { width: 100%; height: 120px; object-fit: cover; display: block; }

    .upload-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.45);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
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

                        <label class="font-weight-semibold">Current Images <span id="imageCount" class="badge badge-secondary ms-1" style="font-size:11px;">0</span></label>
                        <div id="currentImages" class="row mb-2 mt-1"></div>

                        <hr class="my-3">
                        <label class="font-weight-semibold">Add New Images</label>
                        <div class="mb-2 mt-1">
                            <input type="file" class="form-control" id="images" accept="image/*" multiple>
                            <small class="text-muted">You can select multiple images. They will be appended to this product.</small>
                        </div>
                        <div class="row" id="imagePreview"></div>

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
                    renderEditImages(res.data.images || [], productId);
                },
                error: function(xhr) {
                    console.error("GET_PRODUCT_DETAILS failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load product.", "error");
                }
            });
        }

        // ================= LOAD EDIT IMAGES =================
        function renderEditImages(images, pid) {
            const $container = $("#currentImages").empty();
            $("#imageCount").text(images.length);

            if (!images.length) {
                $container.html(`
                    <div class="col-12 border rounded-3 bg-light text-center text-muted py-4">
                        <i class="fas fa-image" style="font-size:1.5rem;"></i>
                        <p class="mb-0 mt-1 small">No images uploaded</p>
                    </div>
                `);
                return;
            }

            images.forEach(function(img) {
                const primaryBadge = img.is_primary == 1
                    ? '<span class="badge badge-success position-absolute top-0 start-0 m-1" style="font-size:10px;">Primary</span>'
                    : '';
                $container.append(`
                    <div class="col-md-3 col-6 mb-2">
                        <div class="position-relative rounded-3 overflow-hidden border" data-img-id="${img.id}" data-img-name="${img.name}">
                            <img src="<?= $baseURL ?>assets/images/product/${img.name}" alt="${img.name}" class="w-100 object-fit-cover d-block" style="height:120px;" onerror="productEditImgFallback(this)">
                            ${primaryBadge}
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center prod-img-delete" style="width:24px;height:24px;padding:0;" title="Remove image">
                                <i class="fas fa-times" style="font-size:10px;"></i>
                            </button>
                        </div>
                    </div>
                `);
            });
        }

        // ================= DELETE SINGLE IMAGE =================
        $(document).on("click", ".prod-img-delete", function(e) {
            e.stopPropagation();
            const $wrap = $(this).closest(".position-relative");
            const imgId = $wrap.data("img-id");
            const imgName = $wrap.data("img-name");

            Swal.fire({
                title: "Remove image?",
                text: "This image will be permanently deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Yes, remove it"
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.ajax({
                        url: "<?= $baseURL ?>controller/ctrl-products.php",
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_PRODUCT_IMAGE",
                            id: imgId,
                            name: imgName,
                            product_id: productId
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                renderEditImages([], productId);
                                loadProduct();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function() {
                            closeLoader();
                            Swal.fire("Error", "Failed to delete image.", "error");
                        }
                    });
                }
            });
        });

        // ================= FILE -> BASE64 =================
        function filesToBase64(files) {
            const promises = files.map(function(file) {
                return new Promise(function(resolve, reject) {
                    const reader = new FileReader();
                    reader.onload = function() { resolve(reader.result); };
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
            });
            return Promise.all(promises);
        }

        // ================= UPLOAD PREVIEW =================
        function renderUploadPreviews(containerId, files) {
            $("#" + containerId).empty();
            files.forEach(function(file, idx) {
                const url = URL.createObjectURL(file);
                const $item = $(
                    '<div class="col-6 col-md-3 mt-2" data-preview-idx="' + idx + '">' +
                    '<div class="position-relative rounded-3 overflow-hidden border" style="height:90px;">' +
                    '<img src="' + url + '" alt="' + (file.name||'').replace(/"/g,'') + '"' +
                    ' class="w-100 h-100 d-block" style="object-fit:cover;">' +
                    '<div class="upload-overlay">' +
                    '<span class="spinner-border spinner-border-sm mb-1"></span>' +
                    '<small class="font-weight-semibold">Uploading...</small>' +
                    '</div></div></div>'
                );
                $item.find("img").on("load", function() { URL.revokeObjectURL(url); });
                $("#" + containerId).append($item);
            });
        }

        function setUploadPreviewState(containerId, state) {
            $("#" + containerId + " .upload-overlay").each(function() {
                const $s = $(this);
                if (state === "uploading") {
                    $s.html('<span class="spinner-border spinner-border-sm mb-1"></span><small class="font-weight-semibold">Uploading...</small>');
                } else if (state === "done") {
                    $s.html('<i class="fas fa-check-circle" style="font-size:1.15rem;color:#7CFC00;"></i><small class="font-weight-semibold">Uploaded</small>');
                } else if (state === "failed") {
                    $s.html('<i class="fas fa-times-circle" style="font-size:1.15rem;color:#ff6b6b;"></i><small class="font-weight-semibold">Failed</small>');
                }
            });
        }

        // ================= PREVIEW NEW FILES =================
        $("#images").on("change", function() {
            const newFiles = Array.from(this.files || []);
            if (newFiles.length + imageFiles.length > 10) {
                Swal.fire("Warning", "You can only upload up to 10 images.", "warning");
                this.value = "";
                return;
            }
            imageFiles = imageFiles.concat(newFiles);
            renderUploadPreviews("imagePreview", newFiles);
        });

        $(document).on("click", ".img-remove", function() {
            const idx = parseInt($(this).data("index"));
            imageFiles.splice(idx, 1);
            $("#images").val("");
            renderUploadPreviews("imagePreview", imageFiles);
        });

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

            showLoader("Uploading images...");
            const base64Array = await filesToBase64(imageFiles);
            setUploadPreviewState("imagePreview", "uploading");

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
                    closeLoader();
                    if (res.code == 0) {
                        setUploadPreviewState("imagePreview", "done");
                        Swal.fire({
                            icon: "success",
                            title: "Updated!",
                            text: res.message
                        }).then(function() {
                            window.location.href = "<?= $baseURL ?>list-products";
                        });
                    } else {
                        setUploadPreviewState("imagePreview", "failed");
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    setUploadPreviewState("imagePreview", "failed");
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
