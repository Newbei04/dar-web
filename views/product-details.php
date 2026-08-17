<?= startSection('css') ?>
<style>
    .barcode-cell {
        min-width: 180px;
        display: inline-block;
    }

    .barcode-cell svg {
        display: block;
        max-width: 180px;
        height: 48px;
    }

    .barcode-value {
        display: block;
        font-size: 11px;
        line-height: 1.2;
        margin-top: 3px;
        letter-spacing: 1px;
    }

    .gallery-main {
        position: relative;
        background: var(--body-bg);
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gallery-main img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: zoom-in;
        transition: transform 0.2s;
    }

    .gallery-main img:hover {
        transform: scale(1.03);
    }

    .gallery-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: var(--body-bg);
        color: var(--text-gray);
        font-size: 13px;
    }

    .gallery-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #333;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 5;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .gallery-main:hover .gallery-nav-btn {
        opacity: 1;
    }

    .gallery-nav-btn:hover {
        background: #fff;
        color: #0f766e;
    }

    .gallery-nav-btn.prev {
        left: 8px;
    }

    .gallery-nav-btn.next {
        right: 8px;
    }

    .gallery-counter {
        position: absolute;
        bottom: 8px;
        right: 10px;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        font-size: 12px;
        padding: 2px 10px;
        border-radius: 10px;
    }

    .gallery-thumbs {
        display: flex;
        gap: 6px;
        margin-top: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none;
    }

    .gallery-thumbs::-webkit-scrollbar {
        display: none;
    }

    .g-thumb {
        min-width: 60px;
        height: 60px;
        border-radius: 6px;
        border: 2px solid transparent;
        overflow: hidden;
        cursor: pointer;
        flex-shrink: 0;
        transition: border-color 0.15s;
        background: var(--body-bg);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .g-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .g-thumb.active {
        border-color: #0f766e;
    }

    .g-thumb:hover {
        border-color: #5eead4;
    }

    .detail-name {
        font-size: 20px;
        font-weight: 600;
        line-height: 1.4;
    }

    .pd-info-card {
        background: var(--body-bg);
        border-color: var(--border);
    }

    .pd-info-card .info-value {
        color: var(--text-dark);
    }

    .img-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.92);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .img-overlay.active {
        display: flex;
    }

    .img-overlay img {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 4px;
    }

    .ov-close {
        position: absolute;
        top: 20px;
        right: 30px;
        background: none;
        border: none;
        color: #fff;
        font-size: 36px;
        cursor: pointer;
        opacity: 0.7;
    }

    .ov-close:hover {
        opacity: 1;
    }

    .ov-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        font-size: 28px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s;
    }

    .ov-nav:hover {
        opacity: 1;
    }

    .ov-nav.ov-prev {
        left: 20px;
    }

    .ov-nav.ov-next {
        right: 20px;
    }

    .ov-counter {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
        background: rgba(0, 0, 0, 0.5);
        padding: 4px 16px;
        border-radius: 12px;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-products">Product</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Product Details</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="pdTitle">Product Details</h4>
                    <div>
                        <a href="<?= $baseURL ?>edit-product?id=" class="btn btn-primary btn-sm me-1" id="pdEditBtn" style="display:none;">
                            <i class="fas fa-pencil-alt me-1"></i> Edit
                        </a>
                        <button class="btn btn-success btn-sm me-1" id="pdReceiveBtn" style="display:none;" data-bs-toggle="modal" data-bs-target="#pdReceiveStockModal">
                            <i class="fas fa-box-open me-1"></i> Receive Stock
                        </button>
                        <button class="btn btn-sm me-1" id="pdArchiveBtn" style="display:none;">
                            <i class="fas fa-archive me-1"></i>
                        </button>
                        <a href="<?= $baseURL ?>list-products" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body" id="pdBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2 mb-0">Loading product details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receive Stock Modal -->
<div class="modal fade" id="pdReceiveStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="pdReceiveStockForm">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-box-open me-2 text-success"></i>Receive Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pdReceiveProductId">
                    <div class="mb-3">
                        <label class="form-label">Facility</label>
                        <select class="form-select native-select" id="pdReceiveFacility" required>
                            <option value="">Select facility</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="pdReceiveQty" min="1" step="any" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch Number</label>
                        <input type="text" class="form-control" id="pdReceiveBatch" placeholder="e.g. BATCH-SEED-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cost Price</label>
                        <input type="number" class="form-control" id="pdReceiveCost" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Facility Selling Price</label>
                        <input type="number" class="form-control bg-light" id="pdReceiveSelling" min="0" step="0.01" readonly>
                        <small class="form-text text-muted" id="pdReceivePriceHint">Auto-loaded from Facility Prices.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="pdReceiveExpiry">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Storage Location</label>
                        <input type="text" class="form-control" id="pdReceiveLocation" placeholder="e.g. Rack 1">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="pdReceiveRemarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Receive</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batch Action Modal -->
<div class="modal fade" id="pdInvActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="pdInvActionForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdInvActionTitle">Batch Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pdInvActionId">
                    <input type="hidden" id="pdInvActionName">
                    <input type="hidden" id="pdInvActionFacilityId">
                    <input type="hidden" id="pdInvActionProductId">
                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:13px;">
                        <i class="fas fa-cube me-1"></i>
                        <span id="pdInvActionSummary">-</span>
                    </div>
                    <div class="mb-3" id="pdInvQtyWrap">
                        <label class="form-label" id="pdInvQtyLabel">Quantity</label>
                        <input type="number" class="form-control" id="pdInvQty" step="1" required>
                    </div>
                    <div class="mb-3" id="pdInvRemarksWrap">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="pdInvRemarks" rows="2"></textarea>
                    </div>
                    <div class="mb-3" id="pdInvFacilityWrap" style="display:none;">
                        <label class="form-label">Transfer To Facility</label>
                        <select class="form-select native-select" id="pdInvTargetFacility">
                            <option value="">Select facility</option>
                        </select>
                    </div>
                    <div class="mb-0" id="pdInvStatusWrap" style="display:none;">
                        <label class="form-label">Status</label>
                        <select class="form-select native-select" id="pdInvStatus">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="pdInvActionSubmit"><i class="fas fa-check me-1"></i>Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inventory Logs Modal -->
<div class="modal fade" id="pdLogsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history me-2 text-info"></i>Inventory Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Qty</th>
                                <th>Balance</th>
                                <th>Facility</th>
                                <th>Remarks</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody id="pdLogsBody">
                            <tr><td colspan="7" class="text-center text-muted py-3">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Set Facility Price Modal -->
<div class="modal fade" id="pdPriceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="pdPriceForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdPriceTitle"><i class="fas fa-tags me-2 text-primary"></i>Set Facility Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pdPriceId">
                    <div class="mb-3">
                        <label class="form-label">Facility</label>
                        <select class="form-select native-select" id="pdPriceFacility" required>
                            <option value="">Select facility</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Selling Price</label>
                        <input type="number" class="form-control" id="pdPriceSelling" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Price</label>
                        <input type="number" class="form-control" id="pdPriceMin" min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Maximum Price</label>
                        <input type="number" class="form-control" id="pdPriceMax" min="0" step="0.01">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" id="pdPriceRemarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fullscreen Image Overlay -->
<div class="img-overlay" id="viewImgOverlay">
    <button class="ov-close" id="viewOvClose">&times;</button>
    <button class="ov-nav ov-prev" id="viewOvPrev"><i class="fas fa-chevron-left"></i></button>
    <img id="viewOvImg" src="" alt="">
    <button class="ov-nav ov-next" id="viewOvNext"><i class="fas fa-chevron-right"></i></button>
    <div class="ov-counter" id="viewOvCounter"></div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {

        let productId = new URLSearchParams(window.location.search).get('id');

        if (!productId) {
            Swal.fire("Error", "No product ID provided.", "error").then(function() {
                window.location.href = "<?= $baseURL ?>list-products";
            });
            return;
        }

        $("#pdEditBtn").attr("href", "<?= $baseURL ?>edit-product?id=" + productId);

        let currentDetails = null;
        let facilityOptions = [];

        function loadFacilities(target, selectedFacilityId) {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "LIST_FACILITY_BY_TYPE", facility_type: "1,4" }),
                success: function(res) {
                    const opts = res.code == 0 && res.data ? res.data : [];
                    facilityOptions = opts;
                    const $sel = $('#' + target);
                    let html = '<option value="">Select facility</option>';
                    opts.forEach(function(f) {
                        const sel = selectedFacilityId && String(f.id) == String(selectedFacilityId) ? ' selected' : '';
                        html += `<option value="${escapeHtml(f.id)}"${sel}>${escapeHtml(f.name || ('Facility ' + f.id))}</option>`;
                    });
                    $sel.html(html);
                },
                error: function(xhr) {
                    console.error("LIST_FACILITY_BY_TYPE failed:", xhr.responseText);
                }
            });
        }

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

        function renderBarcode(value) {
            value = String(value ?? '').toUpperCase();

            if (!value) return '';

            const patterns = {
                '0': '101001101101',
                '1': '110100101011',
                '2': '101100101011',
                '3': '110110010101',
                '4': '101001101011',
                '5': '110100110101',
                '6': '101100110101',
                '7': '101001011011',
                '8': '110100101101',
                '9': '101100101101',
                'A': '110101001011',
                'B': '101101001011',
                'C': '110110100101',
                'D': '101011001011',
                'E': '110101100101',
                'F': '101101100101',
                'G': '101010011011',
                'H': '110101001101',
                'I': '101101001101',
                'J': '101011001101',
                'K': '110101010011',
                'L': '101101010011',
                'M': '110110101001',
                'N': '101011010011',
                'O': '110101101001',
                'P': '101101101001',
                'Q': '101010110011',
                'R': '110101011001',
                'S': '101101011001',
                'T': '101011011001',
                'U': '110010101011',
                'V': '100110101011',
                'W': '110011010101',
                'X': '100101101011',
                'Y': '110010110101',
                'Z': '100110110101',
                '-': '100101011011',
                '.': '110010101101',
                ' ': '100110101101',
                '*': '100101101101'
            };

            const encoded = `*${value.replace(/[^0-9A-Z .-]/g, '')}*`;
            let x = 0,
                bars = '';
            const barWidth = 2,
                gapWidth = 1,
                height = 42;

            encoded.split('').forEach(function(char) {
                const pattern = patterns[char];
                if (!pattern) return;
                pattern.split('').forEach(function(bit) {
                    if (bit === '1') {
                        bars += `<rect x="${x}" y="0" width="${barWidth}" height="${height}" fill="#111"></rect>`;
                    }
                    x += barWidth;
                });
                x += gapWidth;
            });

            return `
                <div class="barcode-cell">
                    <svg viewBox="0 0 ${x} ${height}" preserveAspectRatio="none" aria-label="Barcode ${escapeHtml(value)}">
                        ${bars}
                    </svg>
                    <span class="barcode-value">${escapeHtml(value)}</span>
                </div>
            `;
        }

        function productImgFallback(el) {
            const fallback = document.createElement('div');
            fallback.style.cssText = 'width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;background:var(--body-bg,#f1f5f9);color:var(--text-gray,#adb5bd);font-size:13px;';
            fallback.innerHTML = '<i class="fas fa-image fa-3x"></i><span>No image</span>';
            el.replaceWith(fallback);
        }

        /* ============================================================
           GALLERY STATE
           ============================================================ */
        const ViewGallery = {
            images: [],
            current: 0,

            init(urls, startIndex) {
                this.images = urls || [];
                this.current = startIndex || 0;
                this._renderMain();
                this._renderThumbs();
            },

            go(index) {
                if (index < 0 || index >= this.images.length) return;
                this.current = index;
                this._renderMain();
                this._syncThumbs();
            },

            prev() {
                this.go(this.current - 1);
            },
            next() {
                this.go(this.current + 1);
            },

            _renderMain() {
                const url = this.images[this.current] || '';
                $('#viewGalleryMainImg').attr('src', url);
                const total = this.images.length || 0;
                const cur = total ? this.current + 1 : 0;
                $('#viewGalleryCounter').text(`${cur} / ${total}`);
                $('#viewGalleryPrev').prop('disabled', this.current === 0);
                $('#viewGalleryNext').prop('disabled', this.current === this.images.length - 1);
                if ($('#viewImgOverlay').hasClass('active')) {
                    ViewOverlay.go(this.current);
                }
            },

            _renderThumbs() {
                const $strip = $('#viewGalleryThumbs').empty();
                if (!this.images.length) {
                    $strip.html('<small class="text-muted">No images</small>');
                    return;
                }
                this.images.forEach((url, i) => {
                    $(`<div class="g-thumb ${i === this.current ? 'active' : ''}">
                        <img src="${url}" alt="thumb ${i+1}" onerror="productImgFallback(this)">
                    </div>`)
                        .on('click', () => this.go(i))
                        .appendTo($strip);
                });
            },

            _syncThumbs() {
                $('#viewGalleryThumbs .g-thumb').removeClass('active').eq(this.current).addClass('active');
            }
        };

        const ViewOverlay = {
            open(index) {
                this.go(index);
                $('#viewImgOverlay').addClass('active');
            },

            close() {
                $('#viewImgOverlay').removeClass('active');
            },

            go(index) {
                if (index < 0 || index >= ViewGallery.images.length) return;
                ViewGallery.current = index;
                $('#viewOvImg').attr('src', ViewGallery.images[index]);
                $('#viewOvCounter').text(`${index + 1} / ${ViewGallery.images.length}`);
                ViewGallery._syncThumbs();
                $('#viewGalleryMainImg').attr('src', ViewGallery.images[index]);
                const total = ViewGallery.images.length || 0;
                const cur = total ? index + 1 : 0;
                $('#viewGalleryCounter').text(`${cur} / ${total}`);
            }
        };

        // Overlay binds
        $('#viewGalleryMainImg').on('click', () => ViewOverlay.open(ViewGallery.current));
        $('#viewOvClose').on('click', () => ViewOverlay.close());
        $('#viewOvPrev').on('click', () => ViewOverlay.go(ViewGallery.current - 1));
        $('#viewOvNext').on('click', () => ViewOverlay.go(ViewGallery.current + 1));
        $('#viewImgOverlay').on('click', function(e) {
            if (e.target === this) ViewOverlay.close();
        });

        $('#viewGalleryPrev').on('click', () => ViewGallery.prev());
        $('#viewGalleryNext').on('click', () => ViewGallery.next());

        $(document).on('keydown', function(e) {
            if ($('#viewImgOverlay').hasClass('active')) {
                if (e.key === 'ArrowLeft') ViewOverlay.go(ViewGallery.current - 1);
                if (e.key === 'ArrowRight') ViewOverlay.go(ViewGallery.current + 1);
                if (e.key === 'Escape') ViewOverlay.close();
            }
        });

        /* ============================================================
           LOAD PRODUCT DETAILS
           ============================================================ */
        function loadProduct() {
            showLoader();
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
                    closeLoader();
                    if (res.code != 0 || !res.data || !res.data.product) {
                        $('#pdBody').html('<p class="text-center text-muted py-5 mb-0">Product not found.</p>');
                        return;
                    }

                    const p = res.data.product;
                    const images = res.data.images || [];
                    const inventory = res.data.inventory || [];
                    const prices = res.data.prices || [];

                    $('#pdTitle').text(p.name || 'Product Details');

                    const statusMap = {
                        0: '<span class="badge light badge-danger">Archived</span>',
                        1: '<span class="badge light badge-success">Active</span>'
                    };

                    let badges = '';
                    if (p.sku) badges += `<span class="badge light badge-secondary" style="font-family:monospace;">SKU: ${escapeHtml(p.sku)}</span>`;
                    if (p.is_hazardous == 1) badges += '<span class="badge light badge-warning"><i class="fas fa-triangle-exclamation me-1"></i>Hazardous</span>';

                    const totalStock = inventory.reduce(function(sum, inv) {
                        return sum + (parseFloat(inv.current_stock) || 0);
                    }, 0);
                    const totalAvailable = inventory.reduce(function(sum, inv) {
                        return sum + (parseFloat(inv.available_stock) || 0);
                    }, 0);

                    const infoCards = [
                        { icon: 'fa-tag', label: 'Category', value: p.category_name || '-' },
                        { icon: 'fa-ruler', label: 'Unit', value: p.unit || '-' },
                        { icon: 'fa-calendar', label: 'Created', value: p.created_at || '-' },
                        { icon: 'fa-hashtag', label: 'Barcode', value: p.barcode || '-' }
                    ].map(function(c) {
                        return `
                            <div class="col-6 col-md-3">
                                <div class="p-2 rounded border pd-info-card">
                                    <small class="text-muted d-block"><i class="fas ${c.icon} me-1"></i>${c.label}</small>
                                    <span class="fw-semibold info-value">${escapeHtml(c.value)}</span>
                                </div>
                            </div>
                        `;
                    }).join('');

                    let inventoryRows = '<tr><td colspan="13" class="text-center text-muted py-3">No inventory records.</td></tr>';
                    if (inventory.length) {
                        inventoryRows = inventory.map(function(inv) {
                            const invStatus = inv.status == 1 ?
                                '<span class="badge light badge-success">Active</span>' :
                                '<span class="badge light badge-secondary">Inactive</span>';

                            const available = parseFloat(inv.available_stock ?? 0);
                            const reorder = parseFloat(inv.reorder_level ?? 0);
                            let availCell = `<span class="${available <= 0 ? 'text-danger' : (available <= reorder ? 'text-warning' : 'text-success')} fw-semibold">${formatNumber(available)}</span>`;
                            if (available <= reorder) availCell += ' <span class="badge light badge-warning">LOW</span>';

                            let expiryCell = escapeHtml(inv.expiry_date || '-');
                            if (inv.expiry_date) {
                                const today = new Date(); today.setHours(0, 0, 0, 0);
                                const exp = new Date(inv.expiry_date);
                                const diff = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));
                                if (diff < 0) expiryCell = '<span class="badge light badge-danger">' + escapeHtml(inv.expiry_date) + ' (Expired)</span>';
                                else if (diff <= 60) expiryCell = '<span class="badge light badge-warning">' + escapeHtml(inv.expiry_date) + ' (Expiring)</span>';
                            }

                            const sellingVal = inv.facility_price !== null && inv.facility_price !== undefined ? inv.facility_price : inv.selling_price;

                            return `
                                <tr>
                                    <td>${escapeHtml(inv.facility_name || '-')}</td>
                                    <td>${escapeHtml(inv.batch_number || '-')}</td>
                                    <td>${formatNumber(inv.received_stock)}</td>
                                    <td>${formatNumber(inv.current_stock)}</td>
                                    <td>${formatNumber(inv.reserved_stock)}</td>
                                    <td>${availCell}</td>
                                    <td>${formatNumber(reorder)}</td>
                                    <td>${escapeHtml(inv.cost_price != null ? '₱' + formatNumber(inv.cost_price) : '-')}</td>
                                    <td>${escapeHtml(sellingVal != null && sellingVal !== undefined ? '₱' + formatNumber(sellingVal) : '-')}</td>
                                    <td>${expiryCell}</td>
                                    <td>${escapeHtml(inv.storage_location || '-')}</td>
                                    <td>${invStatus}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                                <i class="fas fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="sell"><i class="fas fa-cart-plus me-1 text-success"></i>Sell</a></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="return"><i class="fas fa-undo me-1 text-primary"></i>Return</a></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="damaged"><i class="fas fa-times-circle me-1 text-danger"></i>Mark Damaged</a></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="expired"><i class="fas fa-clock me-1 text-warning"></i>Mark Expired</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="reserve"><i class="fas fa-lock me-1 text-warning"></i>Reserve</a></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="release"><i class="fas fa-unlock me-1 text-info"></i>Release Reservation</a></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="adjust"><i class="fas fa-sliders me-1 text-primary"></i>Adjust Quantity</a></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="transfer"><i class="fas fa-right-left me-1 text-secondary"></i>Transfer</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item inv-action" href="javascript:void(0)" data-id="${escapeHtml(inv.id)}" data-name="${escapeHtml(inv.batch_number || 'Batch')}" data-action="status"><i class="fas fa-toggle-on me-1 text-success"></i>Change Status</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    }

                    let pricingRows = '<tr><td colspan="7" class="text-center text-muted py-3">No pricing records.</td></tr>';
                    if (prices.length) {
                        pricingRows = prices.map(function(pr) {
                            const prStatus = pr.status == 1 ?
                                '<span class="badge light badge-success">Active</span>' :
                                '<span class="badge light badge-secondary">Inactive</span>';
                            return `
                                <tr>
                                    <td>${escapeHtml(pr.facility_name || '-')}</td>
                                    <td>${escapeHtml(pr.selling_price != null ? '₱' + formatNumber(pr.selling_price) : '-')}</td>
                                    <td>${escapeHtml(pr.minimum_price != null ? '₱' + formatNumber(pr.minimum_price) : '-')}</td>
                                    <td>${escapeHtml(pr.maximum_price != null ? '₱' + formatNumber(pr.maximum_price) : '-')}</td>
                                    <td>${prStatus}</td>
                                    <td>${escapeHtml(pr.updated_at || '-')}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-price" data-facility="${escapeHtml(pr.facility_id)}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    }

                    // GALLERY urls
                    const base = "<?= $baseURL ?>assets/images/product/";
                    const urls = images.map(function(img) { return base + img.name; });

                    $('#pdBody').html(`
                        <div class="row">
                            <div class="col-md-5 mb-3 mb-md-0">
                                <div class="gallery-main" id="viewGalleryMain">
                                    <img id="viewGalleryMainImg" src="" alt="Product Image" onerror="productImgFallback(this)">
                                    <button class="gallery-nav-btn prev" id="viewGalleryPrev">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="gallery-nav-btn next" id="viewGalleryNext">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <span class="gallery-counter" id="viewGalleryCounter">0 / 0</span>
                                </div>
                                <div class="gallery-thumbs" id="viewGalleryThumbs"></div>
                            </div>
                            <div class="col-md-7">
                                <div class="d-flex justify-content-between align-items-start">
                                    <p class="detail-name mb-0">${escapeHtml(p.name || '—')}</p>
                                    ${statusMap[p.status] || '<span class="badge light badge-secondary">Unknown</span>'}
                                </div>
                                <div class="d-flex flex-wrap mt-2" style="gap:.4rem;" id="pdBadges"></div>

                                <div class="alert alert-info d-flex align-items-center py-2 px-3 mt-3 mb-2" style="font-size:13px;">
                                    <i class="fas fa-boxes-stacked me-2"></i>
                                    <span><strong>${formatNumber(totalStock)}</strong> total stock across <strong>${inventory.length}</strong> facility(ies) · <strong>${formatNumber(totalAvailable)}</strong> available</span>
                                </div>

                                <div class="row gx-3 mt-3">${infoCards}</div>

                                <div class="mt-3">${renderBarcode(p.barcode)}</div>
                            </div>
                        </div>

                        <!-- TABS -->
                        <ul class="nav nav-tabs mt-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#pdTabInventory" role="tab">
                                    <i class="fas fa-warehouse me-1"></i> Inventory
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#pdTabPricing" role="tab">
                                    <i class="fas fa-tags me-1"></i> Pricing
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#pdTabPriceHistory" role="tab" id="pdPriceHistoryTab">
                                    <i class="fas fa-clock-rotate-left me-1"></i> Price History
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#pdTabLogs" role="tab" id="pdLogsTab">
                                    <i class="fas fa-history me-1"></i> Inventory Logs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#pdTabDetails" role="tab">
                                    <i class="fas fa-align-left me-1"></i> Details
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content pt-3">
                            <div class="tab-pane fade show active" id="pdTabInventory" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Facility</th>
                                                <th>Batch #</th>
                                                <th>Received</th>
                                                <th>Current</th>
                                                <th>Reserved</th>
                                                <th>Available</th>
                                                <th>Reorder</th>
                                                <th>Cost</th>
                                                <th>Selling</th>
                                                <th>Expiry</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>${inventoryRows}</tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pdTabPricing" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Set or update per-facility selling price.</span>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#pdPriceModal" id="pdAddPriceBtn">
                                        <i class="fas fa-plus me-1"></i> Set Facility Price
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Facility</th>
                                                <th>Selling Price</th>
                                                <th>Minimum</th>
                                                <th>Maximum</th>
                                                <th>Status</th>
                                                <th>Updated</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>${pricingRows}</tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pdTabPriceHistory" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Facility</th>
                                                <th>Old Price</th>
                                                <th>New Price</th>
                                                <th>Effective</th>
                                                <th>Updated By</th>
                                                <th>When</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pdPriceHistoryBody">
                                            <tr><td colspan="6" class="text-center text-muted py-3">Loading...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pdTabLogs" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Action</th>
                                                <th>Qty</th>
                                                <th>Balance</th>
                                                <th>Facility</th>
                                                <th>Remarks</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pdLogsBody">
                                            <tr><td colspan="7" class="text-center text-muted py-3">Loading...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pdTabDetails" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <p class="text-muted mb-0">${escapeHtml(p.details || 'No description.')}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="fw-semibold mb-2">Gallery</h6>
                                        <div class="d-flex flex-wrap" style="gap:.5rem;">
                                            ${urls.map(function(url, i) {
                                                const isPrimary = images[i] && images[i].is_primary == 1;
                                                return `
                                                    <div style="width:84px;height:84px;border-radius:6px;overflow:hidden;position:relative;border:2px solid ${isPrimary ? '#0f766e' : 'var(--border,#e9ecef)'};" class="detail-thumb" data-url="${url}" data-image-id="${escapeHtml(images[i].id)}" data-index="${i}">
                                                        <img src="${url}" alt="img ${i+1}" style="width:100%;height:100%;object-fit:cover;" onerror="productImgFallback(this)">
                                                        ${isPrimary ? '<span style="position:absolute;top:2px;left:2px;background:#0f766e;color:#fff;font-size:9px;padding:1px 4px;border-radius:3px;">Primary</span>' : ''}
                                                    </div>
                                                `;
                                            }).join('') || '<small class="text-muted">No images.</small>'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                    $('#pdBadges').html(badges);
                    $('#pdEditBtn').show();
                    $('#pdReceiveBtn').show();

                    const archived = p.status == 0;
                    $('#pdArchiveBtn')
                        .removeClass('btn-warning btn-secondary')
                        .addClass(archived ? 'btn-secondary' : 'btn-warning')
                        .html(archived ? '<i class="fas fa-undo me-1"></i> Restore' : '<i class="fas fa-archive me-1"></i> Archive');

                    currentDetails = { product: p, images: images, inventory: inventory, prices: prices };

                    // Re-bind gallery after DOM injection
                    $('#viewGalleryMainImg').on('click', () => ViewOverlay.open(ViewGallery.current));
                    $('#viewGalleryPrev').on('click', () => ViewGallery.prev());
                    $('#viewGalleryNext').on('click', () => ViewGallery.next());

                    // Facility-dependent loads
                    loadFacilities('pdPriceFacility');
                    loadFacilities('pdReceiveFacility');
                    loadFacilities('pdInvTargetFacility');
                    loadPriceHistory();
                    loadInventoryLogs();

                    // Init gallery
                    ViewGallery.init(urls, 0);
                    if (!urls.length) {
                        $('#viewGalleryMainImg').attr('src', '').hide();
                        $('#viewGalleryMain').css('min-height', '200px');
                        $('#viewGalleryMain').append('<div class="gallery-no-image"><i class="fas fa-image fa-3x"></i><span>No image</span></div>');
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("GET_PRODUCT_DETAILS failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load product details.", "error");
                }
            });
        }

        /* ============================================================
           PRICE HISTORY
           ============================================================ */
        function loadPriceHistory() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_PRICE_HISTORY",
                    product_id: productId
                }),
                success: function(res) {
                    const rows = res.code == 0 && res.data ? res.data : [];
                    if (!rows.length) {
                        $('#pdPriceHistoryBody').html('<tr><td colspan="6" class="text-center text-muted py-3">No price history yet.</td></tr>');
                        return;
                    }
                    const html = rows.map(function(h) {
                        const user = h.created_by_name || h.created_by_username || '-';
                        const oldP = h.old_price != null && h.old_price != '' ? '₱' + formatNumber(h.old_price) : '-';
                        const newP = h.new_price != null && h.new_price != '' ? '₱' + formatNumber(h.new_price) : '-';
                        const eff = h.effective_date || '-';
                        const when = h.created_at || '-';
                        return `
                            <tr>
                                <td>${escapeHtml(h.facility_name || '-')}</td>
                                <td>${escapeHtml(oldP)}</td>
                                <td><span class="fw-semibold">${escapeHtml(newP)}</span></td>
                                <td>${escapeHtml(eff)}</td>
                                <td>${escapeHtml(user)}</td>
                                <td>${escapeHtml(when)}</td>
                            </tr>
                        `;
                    }).join('');
                    $('#pdPriceHistoryBody').html(html);
                },
                error: function(xhr) {
                    console.error("GET_PRICE_HISTORY failed:", xhr.responseText);
                    $('#pdPriceHistoryBody').html('<tr><td colspan="6" class="text-center text-muted py-3">Failed to load.</td></tr>');
                }
            });
        }

        /* ============================================================
           INVENTORY LOGS
           ============================================================ */
        function loadInventoryLogs() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_INVENTORY_LOGS",
                    product_id: productId,
                    limit: 500
                }),
                success: function(res) {
                    const rows = res.code == 0 && res.data ? res.data : [];
                    if (!rows.length) {
                        $('#pdLogsBody').html('<tr><td colspan="7" class="text-center text-muted py-3">No inventory activity yet.</td></tr>');
                        return;
                    }
                    const badgeMap = {
                        1: 'success', 2: 'info', 3: 'warning', 4: 'warning', 5: 'secondary',
                        6: 'secondary', 7: 'primary', 8: 'primary', 9: 'info', 10: 'dark'
                    };
                    const html = rows.map(function(log) {
                        const qty = parseFloat(log.quantity_changed) || 0;
                        const qtyHtml = qty > 0
                            ? `<span class="text-success">+${formatNumber(qty)}</span>`
                            : (qty < 0 ? `<span class="text-danger">${formatNumber(qty)}</span>` : '0');
                        const badge = badgeMap[log.action_type] || 'secondary';
                        const user = log.created_by || '-';
                        return `
                            <tr>
                                <td>${escapeHtml(log.created_at || '-')}</td>
                                <td><span class="badge light badge-${badge}">${escapeHtml(log.action_label || ('Action ' + log.action_type))}</span></td>
                                <td>${qtyHtml}</td>
                                <td>${formatNumber(log.new_balance)}</td>
                                <td>${escapeHtml(log.facility_name || '-')}</td>
                                <td>${escapeHtml(log.remarks || '-')}</td>
                                <td>${escapeHtml(user)}</td>
                            </tr>
                        `;
                    }).join('');
                    $('#pdLogsBody').html(html);
                },
                error: function(xhr) {
                    console.error("GET_INVENTORY_LOGS failed:", xhr.responseText);
                    $('#pdLogsBody').html('<tr><td colspan="7" class="text-center text-muted py-3">Failed to load.</td></tr>');
                }
            });
        }

        /* ============================================================
           PRICE SET / EDIT
           ============================================================ */
        $('#pdAddPriceBtn').on('click', function() {
            $('#pdPriceForm')[0].reset();
            $('#pdPriceId').val('');
            $('#pdPriceTitle').html('<i class="fas fa-tags me-2 text-primary"></i>Set Facility Price');
            $('#pdPriceFacility').prop('disabled', false);
            if (facilityOptions.length) {
                let html = '<option value="">Select facility</option>';
                facilityOptions.forEach(function(f) {
                    html += `<option value="${escapeHtml(f.id)}">${escapeHtml(f.name || ('Facility ' + f.id))}</option>`;
                });
                $('#pdPriceFacility').html(html);
            }
        });

        $(document).on('click', '.edit-price', function() {
            const facilityId = $(this).data('facility');
            const pr = (currentDetails.prices || []).find(function(x) {
                return String(x.facility_id) == String(facilityId);
            });
            if (!pr) return;
            $('#pdPriceForm')[0].reset();
            $('#pdPriceId').val(facilityId);
            $('#pdPriceTitle').html('<i class="fas fa-tags me-2 text-primary"></i>Edit Facility Price');
            $('#pdPriceFacility').prop('disabled', true);
            if (facilityOptions.length) {
                let html = '<option value="">Select facility</option>';
                facilityOptions.forEach(function(f) {
                    const sel = String(f.id) == String(facilityId) ? ' selected' : '';
                    html += `<option value="${escapeHtml(f.id)}"${sel}>${escapeHtml(f.name || ('Facility ' + f.id))}</option>`;
                });
                $('#pdPriceFacility').html(html);
            }
            $('#pdPriceSelling').val(pr.selling_price ?? '');
            $('#pdPriceMin').val(pr.minimum_price ?? '');
            $('#pdPriceMax').val(pr.maximum_price ?? '');
            $('#pdPriceRemarks').val('');
            $('#pdPriceModal').modal('show');
        });

        $('#pdPriceForm').on('submit', function(e) {
            e.preventDefault();
            const facilityId = $('#pdPriceFacility').val();
            if (!facilityId) {
                Swal.fire("Error", "Please select a facility.", "error");
                return;
            }
            const payload = {
                trans: "SET_FACILITY_PRICE",
                product_id: productId,
                facility_id: facilityId,
                selling_price: $('#pdPriceSelling').val() || null,
                minimum_price: $('#pdPriceMin').val() || null,
                maximum_price: $('#pdPriceMax').val() || null,
                remarks: $('#pdPriceRemarks').val(),
                created_by: "<?= $_SESSION['users_id'] ?? 0 ?>"
            };
            Swal.fire({
                title: "Saving price...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(payload),
                success: function(res) {
                    Swal.close();
                    if (res.code == 0) {
                        Swal.fire("Saved", "Facility price updated.", "success").then(function() {
                            $('#pdPriceModal').modal('hide');
                            loadProduct();
                        });
                    } else {
                        Swal.fire("Error", res.message || "Failed to save price.", "error");
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error("SET_FACILITY_PRICE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to save price.", "error");
                }
            });
        });

        /* ============================================================
           RECEIVE STOCK
           ============================================================ */
        $('#pdReceiveBtn').on('click', function() {
            $('#pdReceiveStockForm')[0].reset();
            $('#pdReceiveProductId').val(productId);
            $('#pdReceiveSelling').val('');
            $('#pdReceivePriceHint').html('Auto-loaded from Facility Prices.');
            if (facilityOptions.length) {
                let html = '<option value="">Select facility</option>';
                facilityOptions.forEach(function(f) {
                    html += `<option value="${escapeHtml(f.id)}">${escapeHtml(f.name || ('Facility ' + f.id))}</option>`;
                });
                $('#pdReceiveFacility').html(html);
            }
        });

        $('#pdReceiveFacility').on('change', function() {
            const facilityId = $(this).val();
            if (!facilityId) {
                $('#pdReceiveSelling').val('');
                $('#pdReceivePriceHint').html('Auto-loaded from Facility Prices.');
                return;
            }
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-product-price.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_FACILITY_PRICE",
                    product_id: productId,
                    facility_id: facilityId
                }),
                success: function(res) {
                    const price = res && res.data ? res.data.selling_price : null;
                    if (price !== null && price !== undefined) {
                        $('#pdReceiveSelling').val(price);
                        $('#pdReceivePriceHint').html('Selling price from Facility Prices.');
                    } else {
                        $('#pdReceiveSelling').val('');
                        $('#pdReceivePriceHint').html('<span class="text-warning">No facility price set. Set it under Pricing before selling.</span>');
                    }
                },
                error: function(xhr) {
                    console.error("GET_FACILITY_PRICE failed:", xhr.responseText);
                }
            });
        });

        $('#pdReceiveStockForm').on('submit', function(e) {
            e.preventDefault();
            const facilityId = $('#pdReceiveFacility').val();
            if (!facilityId) {
                Swal.fire("Error", "Please select a facility.", "error");
                return;
            }
            const payload = {
                trans: "RECEIVE_INVENTORY",
                product_id: productId,
                facility_id: facilityId,
                quantity: $('#pdReceiveQty').val(),
                batch_number: $('#pdReceiveBatch').val().trim(),
                cost_price: $('#pdReceiveCost').val(),
                selling_price: $('#pdReceiveSelling').val() || 0,
                expiry_date: $('#pdReceiveExpiry').val() || null,
                storage_location: $('#pdReceiveLocation').val(),
                remarks: $('#pdReceiveRemarks').val(),
                created_by: "<?= $_SESSION['users_id'] ?? 0 ?>"
            };
            Swal.fire({
                title: "Receiving stock...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(payload),
                success: function(res) {
                    Swal.close();
                    if (res.code == 0) {
                        Swal.fire("Received", "Stock added.", "success").then(function() {
                            $('#pdReceiveStockModal').modal('hide');
                            loadProduct();
                        });
                    } else {
                        Swal.fire("Error", res.message || "Failed to receive stock.", "error");
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error("RECEIVE_INVENTORY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to receive stock.", "error");
                }
            });
        });

        /* ============================================================
           BATCH ACTIONS (reserve / release / adjust / transfer / status)
           ============================================================ */
        $(document).on('click', '.inv-action', function() {
            const id = $(this).data('id');
            const action = $(this).data('action');
            const name = $(this).data('name');
            const inv = (currentDetails.inventory || []).find(function(x) {
                return String(x.id) == String(id);
            });
            const labels = {
                sell: { title: 'Sell Stock', btn: 'Sell', qtyLabel: 'Quantity to sell', icon: 'fa-cart-plus', cls: 'btn-success' },
                return: { title: 'Return Stock', btn: 'Return', qtyLabel: 'Quantity to return', icon: 'fa-undo', cls: 'btn-primary' },
                damaged: { title: 'Mark Damaged', btn: 'Mark Damaged', qtyLabel: 'Quantity damaged', icon: 'fa-times-circle', cls: 'btn-danger' },
                expired: { title: 'Mark Expired', btn: 'Mark Expired', qtyLabel: 'Quantity to remove (blank = all)', icon: 'fa-clock', cls: 'btn-warning' },
                reserve: { title: 'Reserve Stock', btn: 'Reserve', qtyLabel: 'Quantity to reserve', icon: 'fa-lock', cls: 'btn-warning' },
                release: { title: 'Release Reservation', btn: 'Release', qtyLabel: 'Quantity to release', icon: 'fa-unlock', cls: 'btn-info' },
                adjust: { title: 'Adjust Quantity', btn: 'Adjust', qtyLabel: 'New quantity', icon: 'fa-sliders', cls: 'btn-primary' },
                transfer: { title: 'Transfer Stock', btn: 'Transfer', qtyLabel: 'Quantity to transfer', icon: 'fa-right-left', cls: 'btn-secondary' },
                status: { title: 'Change Status', btn: 'Update', qtyLabel: '', icon: 'fa-toggle-on', cls: 'btn-success' }
            };
            const cfg = labels[action] || labels.status;

            $('#pdInvActionId').val(id);
            $('#pdInvActionName').val(action);
            $('#pdInvActionFacilityId').val(inv ? inv.facility_id : '');
            $('#pdInvActionProductId').val(inv ? inv.product_id : '');
            $('#pdInvActionTitle').html(`<i class="fas ${cfg.icon} me-2 text-primary"></i>${cfg.title}`);

            let summary = `Batch <strong>${escapeHtml(name)}</strong>`;
            if (inv) {
                summary += ` · Facility: ${escapeHtml(inv.facility_name || '-')} · Current: ${formatNumber(inv.current_stock)} · Available: ${formatNumber(inv.available_stock)}`;
                if (action === 'sell') {
                    const sp = inv.facility_price !== null && inv.facility_price !== undefined ? inv.facility_price : inv.selling_price;
                    summary += ` · Selling: <strong>₱${formatNumber(sp)}</strong>`;
                }
            }
            $('#pdInvActionSummary').html(summary);

            const isTransfer = action === 'transfer';
            const isStatus = action === 'status';
            $('#pdInvQtyWrap').toggle(!isStatus);
            $('#pdInvQtyLabel').text(cfg.qtyLabel);
            $('#pdInvFacilityWrap').toggle(isTransfer);
            $('#pdInvStatusWrap').toggle(isStatus);
            $('#pdInvRemarksWrap').toggle(!isStatus);
            $('#pdInvQty').val('');
            $('#pdInvQty').prop('required', action !== 'expired');
            $('#pdInvRemarks').val('');
            $('#pdInvStatus').val(inv && inv.status == 1 ? '1' : '0');
            $('#pdInvActionSubmit')
                .removeClass('btn-primary btn-warning btn-info btn-secondary btn-success btn-danger')
                .addClass(cfg.cls)
                .html(`<i class="fas fa-check me-1"></i>${cfg.btn}`);

            $('#pdInvActionModal').modal('show');
        });

        $('#pdInvActionForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#pdInvActionId').val();
            const action = $('#pdInvActionName').val();
            const transMap = {
                sell: 'SELL_INVENTORY',
                return: 'RETURN_INVENTORY',
                damaged: 'DAMAGE_INVENTORY',
                expired: 'MARK_EXPIRED',
                reserve: 'RESERVE_INVENTORY',
                release: 'RELEASE_RESERVATION',
                adjust: 'ADJUST_INVENTORY',
                transfer: 'TRANSFER_INVENTORY',
                status: 'EDIT_STATUS'
            };
            const payload = {
                trans: transMap[action] || action,
                inventory_id: id,
                created_by: "<?= $_SESSION['users_id'] ?? 0 ?>",
                remarks: $('#pdInvRemarks').val()
            };
            if (action === 'sell') {
                payload.facility_id = $('#pdInvActionFacilityId').val();
                payload.product_id = $('#pdInvActionProductId').val();
                payload.quantity = $('#pdInvQty').val();
            } else if (action !== 'status') {
                payload.quantity = $('#pdInvQty').val();
            }
            if (action === 'transfer') payload.destination_facility_id = $('#pdInvTargetFacility').val();
            if (action === 'status') payload.status = $('#pdInvStatus').val();

            Swal.fire({
                title: "Processing...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(payload),
                success: function(res) {
                    Swal.close();
                    if (res.code == 0) {
                        Swal.fire("Done", res.message || "Inventory updated.", "success").then(function() {
                            $('#pdInvActionModal').modal('hide');
                            loadProduct();
                        });
                    } else {
                        Swal.fire("Error", res.message || "Failed to update inventory.", "error");
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error(action + " failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to update inventory.", "error");
                }
            });
        });

        /* ============================================================
           SET PRIMARY IMAGE
           ============================================================ */
        $(document).on('click', '.detail-thumb', function() {
            const imageId = $(this).data('image-id');
            if (!imageId) return;
            Swal.fire({
                title: 'Set as primary image?',
                text: 'This will change the main gallery image.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, set primary',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-products.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "SET_PRIMARY_IMAGE",
                        image_id: imageId,
                        product_id: productId
                    }),
                    success: function(res) {
                        if (res.code == 0) {
                            Swal.fire("Updated", "Primary image updated.", "success").then(function() {
                                loadProduct();
                            });
                        } else {
                            Swal.fire("Error", res.message || "Failed to set primary image.", "error");
                        }
                    },
                    error: function(xhr) {
                        console.error("SET_PRIMARY_IMAGE failed:", xhr.responseText);
                        Swal.fire("Error", "Failed to set primary image.", "error");
                    }
                });
            });
        });

        /* ============================================================
           ARCHIVE / RESTORE
           ============================================================ */
        $('#pdArchiveBtn').on('click', function() {
            if (!currentDetails) return;
            const archived = currentDetails.product.status == 0;
            Swal.fire({
                title: archived ? 'Restore this product?' : 'Archive this product?',
                text: archived ? 'The product will become active again.' : 'The product will no longer be visible to beneficiaries.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: archived ? '#28a745' : '#ffc107',
                confirmButtonText: archived ? 'Yes, restore' : 'Yes, archive',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-products.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "UPDATE_PRODUCT_STATUS",
                        product_id: productId,
                        status: archived ? 1 : 0,
                        updated_by: "<?= $_SESSION['users_id'] ?? 0 ?>"
                    }),
                    success: function(res) {
                        if (res.code == 0) {
                            Swal.fire("Updated", archived ? "Product restored." : "Product archived.", "success").then(function() {
                                loadProduct();
                            });
                        } else {
                            Swal.fire("Error", res.message || "Failed to update status.", "error");
                        }
                    },
                    error: function(xhr) {
                        console.error("UPDATE_PRODUCT_STATUS failed:", xhr.responseText);
                        Swal.fire("Error", "Failed to update status.", "error");
                    }
                });
            });
        });

        loadProduct();
    });
</script>
<?= endSection() ?>
