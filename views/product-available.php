<?= startSection('css') ?>
<style>
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 18px;
        padding: 8px 0;
    }

    .inventory-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s, transform 0.15s;
    }

    .inventory-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.09);
        transform: translateY(-2px);
    }

    .card-thumb {
        height: 110px;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: #adb5bd;
        position: relative;
        overflow: hidden;
    }

    .stock-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .stock-ok {
        background: #28a745;
    }

    .stock-low {
        background: #ffc107;
    }

    .stock-zero {
        background: #dc3545;
    }

    .card-body {
        padding: 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .card-name {
        font-size: 14px;
        font-weight: 600;
        color: #212529;
        margin: 0;
        line-height: 1.35;
    }

    .card-sku {
        font-size: 11px;
        color: #adb5bd;
        margin: 0;
        font-family: monospace;
        letter-spacing: 0.5px;
    }

    .card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 4px;
    }

    .badge-cat {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
        background: #e7f1ff;
        color: #0d6efd;
    }

    .stock-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 8px 10px;
        margin-top: 6px;
    }

    .stock-item {
        text-align: center;
    }

    .stock-item .s-label {
        font-size: 9px;
        color: #adb5bd;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 2px;
    }

    .stock-item .s-value {
        font-size: 14px;
        font-weight: 700;
        color: #212529;
    }

    .stock-item .s-value.available {
        color: #0f6e40;
    }

    .stock-item .s-value.reserved {
        color: #856404;
    }

    .card-price-row {
        display: flex;
        gap: 8px;
        margin-top: 4px;
    }

    .price-box {
        flex: 1;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 6px 8px;
        text-align: center;
    }

    .price-box .p-label {
        font-size: 9px;
        color: #adb5bd;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: block;
    }

    .price-box .p-value {
        font-size: 13px;
        font-weight: 600;
        color: #212529;
    }

    .card-info {
        font-size: 11px;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .card-expiry-warn {
        font-size: 11px;
        color: #856404;
        background: #fff3cd;
        border-radius: 6px;
        padding: 3px 8px;
        margin-top: 2px;
        display: inline-block;
    }

    .card-date {
        font-size: 11px;
        color: #adb5bd;
        margin-top: auto;
        padding-top: 6px;
    }

    .card-footer {
        border-top: 1px solid #f1f3f5;
        padding: 10px 14px;
        display: flex;
        gap: 8px;
    }

    .toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .toolbar .form-control {
        flex: 1;
        min-width: 160px;
        font-size: 13px;
        height: 38px;
    }

    #emptyState {
        text-align: center;
        padding: 4rem 0;
        color: #adb5bd;
        font-size: 15px;
    }

    #emptyState i {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
    }

    #loadingState {
        text-align: center;
        padding: 3rem 0;
        color: #adb5bd;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Available Products</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Available Products</h4>
                </div>

                <div class="card-body">
                    <!-- TOOLBAR: Search + Filters -->
                    <div class="toolbar">
                        <input type="text" class="form-control" id="searchBox" placeholder="Search product name, SKU, batch…">
                        <select class="form-control" id="filterCat" style="max-width:180px;">
                            <option value="">All Categories</option>
                        </select>
                        <select class="form-control" id="filterStock" style="max-width:160px;">
                            <option value="">All Stock Levels</option>
                            <option value="ok">In Stock</option>
                            <option value="low">Low Stock</option>
                            <option value="zero">Out of Stock</option>
                        </select>
                    </div>

                    <!-- LOADING STATE -->
                    <div id="loadingState">
                        <i class="fas fa-spinner fa-spin"></i> Loading inventory…
                    </div>

                    <!-- INVENTORY GRID -->
                    <div class="inventory-grid" id="inventoryGrid"></div>

                    <!-- EMPTY STATE -->
                    <div id="emptyState" style="display:none;">
                        <i class="fas fa-box-open"></i>
                        No inventory items found
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= VIEW PRODUCT MODAL ================= -->
<div class="modal fade" id="viewProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Product Details</h5>
                    <small class="text-muted" id="viewProductSub"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card-thumb" id="viewProductImg" style="height:200px; font-size:56px; border-radius:12px;"></div>
                    </div>
                    <div class="col-md-8">
                        <p class="card-name" style="font-size:16px;" id="viewProductName"></p>
                        <div class="card-meta" id="viewProductMeta"></div>

                        <div class="stock-row" id="viewProductStock"></div>

                        <div class="card-price-row" id="viewProductPrice"></div>

                        <div class="mt-2" id="viewProductInfo"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {

        let allInventory = [];
        let currentItems = [];

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

        // ================= STOCK LEVEL HELPER =================
        function getStockLevel(item) {
            const avail = parseFloat(item.available_stock) || 0;
            if (avail <= 0) return 'zero';
            if (avail <= 10) return 'low';
            return 'ok';
        }

        // ================= EXPIRY HELPER (within 90 days) =================
        function isExpiringSoon(expiryDate) {
            if (!expiryDate) return false;
            const diff = (new Date(expiryDate) - new Date()) / (1000 * 60 * 60 * 24);
            return diff >= 0 && diff <= 90;
        }

        function isExpired(expiryDate) {
            if (!expiryDate) return false;
            return new Date(expiryDate) < new Date();
        }

        // ================= POPULATE CATEGORY FILTER =================
        function populateCategoryFilter() {
            const cats = [...new Set(allInventory.map(p => p.category_name).filter(Boolean))].sort();
            const cur = $('#filterCat').val();
            let html = `<option value="">All Categories</option>`;
            cats.forEach(function(c) {
                html += `<option value="${c}">${c}</option>`;
            });
            $('#filterCat').html(html);
            if (cur) $('#filterCat').val(cur);
        }

        // ================= RENDER INVENTORY CARDS =================
        function renderCards() {
            const q = $('#searchBox').val().toLowerCase();
            const cat = $('#filterCat').val();
            const stock = $('#filterStock').val();

            const filtered = allInventory.filter(function(p) {
                if (q && !((p.product_name || '').toLowerCase().includes(q)) &&
                    !((p.sku || '').toLowerCase().includes(q)) &&
                    !((p.batch_number || '').toLowerCase().includes(q))) return false;
                if (cat && p.category_name !== cat) return false;
                if (stock && getStockLevel(p) !== stock) return false;
                return true;
            });

            currentItems = filtered;

            const grid = $('#inventoryGrid');

            if (!filtered.length) {
                grid.html('');
                $('#emptyState').show();
                return;
            }

            $('#emptyState').hide();

            let html = '';
            filtered.forEach(function(item, index) {
                const stockLevel = getStockLevel(item);
                const stockDot = stockLevel === 'ok' ? 'stock-ok' : stockLevel === 'low' ? 'stock-low' : 'stock-zero';
                const stockLabel = stockLevel === 'ok' ? 'In Stock' : stockLevel === 'low' ? 'Low Stock' : 'Out of Stock';

                let thumbHtml = `<i class="fas fa-box"></i>`;
                if (item.primary_image) {
                    thumbHtml = `<img src="<?= $baseURL ?>assets/images/product/${escapeHtml(item.primary_image)}" alt="Product" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;" onerror="this.remove()">`;
                }

                let expiryHtml = '';
                if (item.expiry_date) {
                    if (isExpired(item.expiry_date)) {
                        expiryHtml = `<span class="card-expiry-warn"><i class="fas fa-exclamation-triangle me-1"></i>Expired: ${item.expiry_date}</span>`;
                    } else if (isExpiringSoon(item.expiry_date)) {
                        expiryHtml = `<span class="card-expiry-warn"><i class="fas fa-hourglass-half me-1"></i>Expires: ${item.expiry_date}</span>`;
                    } else {
                        expiryHtml = `<p class="card-info"><i class="fas fa-calendar me-1"></i>Exp: ${item.expiry_date}</p>`;
                    }
                }

                let availColor = stockLevel === 'ok' ? 'text-success' : stockLevel === 'low' ? 'text-warning' : 'text-danger';

                html += `
            <div class="inventory-card">
                <div class="card-thumb">
                    ${thumbHtml}
                    <span class="stock-indicator ${stockDot}" title="${stockLabel}"></span>
                </div>
                <div class="card-body">
                    <p class="card-name">${escapeHtml(item.product_name ?? '—')}</p>
                    <p class="card-sku">${escapeHtml(item.sku ?? '—')}</p>

                    <div class="card-meta">
                        ${item.category_name ? `<span class="badge-cat">${escapeHtml(item.category_name)}</span>` : ''}
                    </div>

                    <div class="stock-row">
                        <div class="stock-item">
                            <span class="s-label">Available</span>
                            <span class="s-value ${availColor}">${formatNumber(item.available_stock)}</span>
                        </div>
                        <div class="stock-item">
                            <span class="s-label">Status</span>
                            <span class="s-value">${stockLabel}</span>
                        </div>
                    </div>

                    ${expiryHtml}

                    ${item.facility_name ? `<p class="card-info"><i class="fas fa-building me-1"></i>${escapeHtml(item.facility_name)}</p>` : ''}
                </div>
                <div class="card-footer">
                    <button class="btn btn-outline-primary btn-sm flex-fill viewProductBtn" data-idx="${index}">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                </div>
            </div>`;
            });

            grid.html(html);
        }

        // ================= VIEW PRODUCT (MODAL) =================
        $(document).on("click", ".viewProductBtn", function() {
            let item = currentItems[$(this).data("idx")];
            if (!item) {
                Swal.fire("Error", "Product not found", "error");
                return;
            }

            $("#viewProductSub").text(
                item.product_name ? `${item.product_name}${item.sku ? ' | ' + item.sku : ''}` : ''
            );

            let imgHtml = `<i class="fas fa-box"></i>`;
            if (item.primary_image) {
                imgHtml = `<img src="<?= $baseURL ?>assets/images/product/${escapeHtml(item.primary_image)}" alt="Product" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;" onerror="this.remove()">`;
            }
            $("#viewProductImg").html(imgHtml);

            $("#viewProductName").text(item.product_name ?? '—');

            let meta = '';
            if (item.category_name) meta += `<span class="badge-cat">${escapeHtml(item.category_name)}</span>`;
            $("#viewProductMeta").html(meta);

            let stockLabel = item.available_stock > 0 ? 'In Stock' : 'Out of Stock';

            $("#viewProductStock").html(`
                <div class="stock-item">
                    <span class="s-label">Available</span>
                    <span class="s-value available">${formatNumber(item.available_stock)}</span>
                </div>
                <div class="stock-item">
                    <span class="s-label">Status</span>
                    <span class="s-value">${stockLabel}</span>
                </div>
            `);

            $("#viewProductPrice").html(`
                <div class="price-box">
                    <span class="p-label">Selling</span>
                    <span class="p-value">₱${formatNumber(item.selling_price)}</span>
                </div>
            `);

            let info = '';
            if (item.facility_name) info += `<p class="card-info"><i class="fas fa-building me-1"></i>${escapeHtml(item.facility_name)}</p>`;
            if (item.expiry_date) info += `<p class="card-info"><i class="fas fa-calendar me-1"></i>Expiry: ${escapeHtml(item.expiry_date)}</p>`;
            if (item.updated_at) info += `<p class="card-info"><i class="fas fa-clock me-1"></i>Updated: ${escapeHtml(item.updated_at)}</p>`;
            $("#viewProductInfo").html(info);

            $("#viewProductModal").modal("show");
        });

        // ================= LOAD INVENTORY =================
        function loadInventory() {
            $('#loadingState').show();
            $('#inventoryGrid').html('');
            $('#emptyState').hide();

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_INVENTORY"
                }),
                success: function(res) {
                    $('#loadingState').hide();
                    allInventory = Array.isArray(res.data) ? res.data : [];
                    populateCategoryFilter();
                    renderCards();
                },
                error: function(xhr) {
                    $('#loadingState').hide();
                    console.log("Inventory error:", xhr.responseText);
                    Swal.fire("Error", "Failed to load inventory", "error");
                }
            });
        }

        // ================= LIVE SEARCH & FILTER =================
        $('#searchBox').on('input', renderCards);
        $('#filterCat').on('change', renderCards);
        $('#filterStock').on('change', renderCards);

        // ================= INITIAL LOAD =================
        loadInventory();

    });
</script>
<?= endSection() ?>
