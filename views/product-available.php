<?= startSection('css') ?>
<style>
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 18px;
        padding: 8px 0;
    }

    .inventory-card {
        background: var(--card, #fff);
        border: 1px solid var(--border, #e9ecef);
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
        background: linear-gradient(135deg, var(--body-bg, #f0f4ff) 0%, var(--card, #e8f0fe) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: var(--text-gray, #adb5bd);
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
        color: var(--text-dark, #212529);
        margin: 0;
        line-height: 1.35;
    }

    .card-sku {
        font-size: 11px;
        color: var(--text-gray, #adb5bd);
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
        background: var(--rgba-primary-1, #e7f1ff);
        color: var(--primary, #0d6efd);
    }

    .stock-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--body-bg, #f8f9fa);
        border-radius: 8px;
        padding: 8px 10px;
        margin-top: 6px;
    }

    .stock-item {
        text-align: center;
    }

    .stock-item .s-label {
        font-size: 9px;
        color: var(--text-gray, #adb5bd);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 2px;
    }

    .stock-item .s-value {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-dark, #212529);
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
        background: var(--body-bg, #f8f9fa);
        border-radius: 8px;
        padding: 6px 8px;
        text-align: center;
    }

    .price-box .p-label {
        font-size: 9px;
        color: var(--text-gray, #adb5bd);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: block;
    }

    .price-box .p-value {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-dark, #212529);
    }

    .card-info {
        font-size: 11px;
        color: var(--text-gray, #6c757d);
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .card-expiry-warn {
        font-size: 11px;
        color: var(--text-dark, #856404);
        background: #fff3cd;
        border-radius: 6px;
        padding: 3px 8px;
        margin-top: 2px;
        display: inline-block;
    }

    .card-date {
        font-size: 11px;
        color: var(--text-gray, #adb5bd);
        margin-top: auto;
        padding-top: 6px;
    }

    .card-footer {
        border-top: 1px solid var(--border, #f1f3f5);
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
        color: var(--text-gray, #adb5bd);
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
        color: var(--text-gray, #adb5bd);
    }

    .cart-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .cart-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--text-dark, #166534);
    }

    .cart-info .cart-total {
        font-weight: 700;
        color: var(--text-dark, #14532d);
    }

    .cart-actions {
        display: flex;
        gap: 8px;
    }

    [data-theme-version="dark"] .stock-item .s-value.available {
        color: #4ade80;
    }

    [data-theme-version="dark"] .stock-item .s-value.reserved {
        color: #fbbf24;
    }

    [data-theme-version="dark"] .card-expiry-warn {
        background: rgba(255, 193, 7, 0.15);
        color: #fbbf24;
    }

    [data-theme-version="dark"] .cart-bar {
        background: rgba(40, 167, 69, 0.12);
        border-color: rgba(40, 167, 69, 0.35);
    }

    .sim-card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
    }

    .sim-stat {
        text-align: center;
        padding: 16px 10px;
    }

    .sim-stat .sim-stat-value {
        font-size: 26px;
        font-weight: 700;
        line-height: 1.2;
    }

    .sim-stat .sim-stat-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .sim-flow {
        display: flex;
        align-items: stretch;
        gap: 12px;
        flex-wrap: wrap;
    }

    .sim-flow .sim-stage {
        flex: 1;
        min-width: 180px;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        padding: 12px;
        background: #f8f9fa;
    }

    .sim-flow .sim-stage .sim-stage-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .sim-flow .sim-stage .sim-stage-value {
        font-size: 20px;
        font-weight: 700;
    }

    .sim-flow .sim-arrow {
        display: flex;
        align-items: center;
        color: #adb5bd;
        font-size: 18px;
    }

    .sim-temp-note {
        background: #fff7e6;
        border: 1px solid #ffd591;
        color: #874d00;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
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

    <!-- ================= BUY/SELL SIMULATION (session only) ================= -->
    <div class="card mb-3">
        <div class="card-header" role="button" data-bs-toggle="collapse" data-bs-target="#simPanel" aria-expanded="false" style="cursor:pointer;">
            <h4 class="card-title mb-0"><i class="fas fa-flask me-1"></i> Buy / Sell Simulation
                <small class="text-muted fs-12">(session-only — does not change real stock)</small>
            </h4>
        </div>
        <div class="collapse" id="simPanel">
            <div class="card-body">
                <div class="sim-temp-note mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Important:</strong> This simulator is <strong>temporary and session-only</strong>. It never
                    modifies actual stock, inventory batches, or movement logs. History is cleared when your browser
                    session ends or when you press <em>Clear Simulation</em>.
                </div>

                <!-- STATS -->
                <div class="row mb-3">
                    <div class="col-6 col-md-2">
                        <div class="sim-card sim-stat"><div class="sim-stat-value" id="stTotal">0</div><div class="sim-stat-label">Total</div></div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="sim-card sim-stat"><div class="sim-stat-value text-success" id="stBuy">0</div><div class="sim-stat-label">BUY</div></div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="sim-card sim-stat"><div class="sim-stat-value text-danger" id="stSell">0</div><div class="sim-stat-label">SELL</div></div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="sim-card sim-stat"><div class="sim-stat-value" id="stUnitsBought">0</div><div class="sim-stat-label">Units Bought</div></div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="sim-card sim-stat"><div class="sim-stat-value" id="stUnitsSold">0</div><div class="sim-stat-label">Units Sold</div></div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="sim-card sim-stat"><div class="sim-stat-value text-primary" id="stSalesValue">₱0.00</div><div class="sim-stat-label">Sales Value</div></div>
                    </div>
                </div>

                <div class="row">
                    <!-- CONTROLS -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="simType" id="simTypeBuy" value="BUY" checked>
                                <label class="btn btn-outline-success" for="simTypeBuy"><i class="fas fa-arrow-down me-1"></i> BUY (Receive)</label>
                                <input type="radio" class="btn-check" name="simType" id="simTypeSell" value="SELL">
                                <label class="btn btn-outline-danger" for="simTypeSell"><i class="fas fa-arrow-up me-1"></i> SELL (Distribute)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Facility</label>
                            <select class="form-control native-select" id="simFacility">
                                <option value="">Select Facility</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <select class="form-control native-select" id="simProduct">
                                <option value="">Select Product</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="simQty" min="1" step="1" value="10">
                        </div>

                        <div id="simBuyFields" class="mb-3">
                            <label class="form-label">Unit Price (₱)</label>
                            <input type="number" class="form-control" id="simUnitPrice" min="0" step="0.01">
                            <small class="form-text text-muted">Informational only — BUY does not apply facility pricing.</small>
                        </div>

                        <button type="button" class="btn btn-primary w-100" id="btnSimPreview">
                            <i class="fas fa-eye me-1"></i> Preview Simulation
                        </button>
                    </div>

                    <!-- PREVIEW -->
                    <div class="col-xl-8 col-lg-7">
                        <span class="badge light badge-secondary mb-2" id="simPreviewBadge">Not previewed</span>
                        <div id="simPreviewEmpty" class="text-center text-muted py-4">
                            <i class="fas fa-flask fa-2x mb-2 d-block"></i>
                            Configure the simulation above and click <strong>Preview</strong>.
                        </div>
                        <div id="simPreviewBody" style="display:none;">
                            <div class="sim-flow mb-3">
                                <div class="sim-stage">
                                    <div class="sim-stage-title">Current Stock</div>
                                    <div class="sim-stage-value" id="pvCurrent">0</div>
                                    <small class="text-muted">Current</small>
                                </div>
                                <div class="sim-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                <div class="sim-stage">
                                    <div class="sim-stage-title">Simulation</div>
                                    <div class="sim-stage-value" id="pvDelta">±0</div>
                                    <small class="text-muted" id="pvUnitPrice">-</small>
                                </div>
                                <div class="sim-arrow"><i class="fas fa-long-arrow-alt-right"></i></div>
                                <div class="sim-stage">
                                    <div class="sim-stage-title">Projected Stock</div>
                                    <div class="sim-stage-value" id="pvProjected">0</div>
                                    <small class="text-muted" id="pvProjectedSub">-</small>
                                </div>
                            </div>

                            <div id="simFailure" class="alert alert-danger py-2 px-3" style="display:none;"></div>
                            <div id="simLowStock" class="alert alert-warning py-2 px-3" style="display:none;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Projected available stock reaches the reorder level. This batch would trigger <strong>LOW STOCK</strong>.
                            </div>

                            <div id="simAllocationWrap">
                                <h6 class="fw-bold mb-2"><i class="fas fa-layer-group me-1"></i> FEFO Allocation</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-2">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Batch</th>
                                                <th>Expiry</th>
                                                <th>Available</th>
                                                <th>Take</th>
                                            </tr>
                                        </thead>
                                        <tbody id="simAllocationBody"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="alert alert-success py-2 px-3 mb-3" style="font-size:15px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong id="pvTotalLabel">Total</strong></span>
                                    <strong id="pvTotal">₱0.00</strong>
                                </div>
                            </div>

                            <button type="button" class="btn btn-success w-100" id="btnSimExecute">
                                <i class="fas fa-play me-1"></i> Confirm &amp; Record Simulation
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- HISTORY -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <h6 class="fw-bold mb-0">Simulation History <small class="text-muted">(session only — temporary)</small></h6>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearSim">
                        <i class="fas fa-eraser me-1"></i> Clear Simulation
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Facility</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody id="simHistoryBody">
                            <tr><td colspan="10" class="text-center text-muted py-3">No simulations recorded yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
                        <select class="form-control native-select" id="filterCat" style="max-width:180px;">
                            <option value="">All Categories</option>
                        </select>
                        <select class="form-control native-select" id="filterStock" style="max-width:160px;">
                            <option value="">All Stock Levels</option>
                            <option value="ok">In Stock</option>
                            <option value="low">Low Stock</option>
                            <option value="zero">Out of Stock</option>
                        </select>
                    </div>

                    <!-- CART BAR -->
                    <div class="cart-bar" id="cartBar" style="display:none;">
                        <div class="cart-info">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span id="cartSummary">0 items</span>
                            <span class="cart-total">₱0.00</span>
                        </div>
                        <div class="cart-actions">
                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearCart">
                                <i class="fas fa-trash me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btnOpenCheckout">
                                <i class="fas fa-credit-card me-1"></i> Checkout
                            </button>
                        </div>
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

<!-- ================= BUY MODAL ================= -->
<div class="modal fade" id="buyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="fas fa-cart-plus me-2 text-success"></i>Add to Cart</h5>
                    <small class="text-muted" id="buyProductSub"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="buyIdx">
                <div class="alert alert-light border py-2 px-3 mb-3" style="font-size:13px;">
                    <i class="fas fa-cube me-1"></i>
                    <span id="buyBatchLabel">-</span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="buyQty" min="1" step="1" value="1">
                    <small class="form-text text-muted" id="buyQtyHint"></small>
                </div>
                <div class="mb-0">
                    <label class="form-label">Remarks (optional)</label>
                    <input type="text" class="form-control" id="buyRemarks" placeholder="Optional note">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnAddToCart"><i class="fas fa-cart-plus me-1"></i> Add to Cart</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= CHECKOUT MODAL ================= -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="fas fa-credit-card me-2 text-primary"></i>Checkout</h5>
                    <small class="text-muted">Complete the sale for the beneficiary.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">Beneficiary <span class="text-danger">*</span></label>
                            <select class="form-select native-select" id="checkoutBeneficiary">
                                <option value="">Select beneficiary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select native-select" id="checkoutPayment">
                                <option value="1">Cash</option>
                                <option value="2">Card / POS</option>
                                <option value="3">Wallet</option>
                            </select>
                        </div>
                        <div class="alert alert-success py-2 px-3" style="font-size:15px;">
                            <div class="d-flex justify-content-between">
                                <span>Grand Total</span>
                                <strong id="checkoutGrandTotal">₱0.00</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Facility</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="checkoutCartBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnPlaceCheckout"><i class="fas fa-check me-1"></i> Place Checkout</button>
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
        let cart = {};
        let cartCount = 0;
        let beneficiariesLoaded = false;

        const CART_STORAGE_KEY = 'dar_product_cart';

        function loadCart() {
            try {
                const saved = localStorage.getItem(CART_STORAGE_KEY);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                        cart = parsed;
                    }
                }
            } catch (e) {
                cart = {};
            }
        }

        function saveCart() {
            try {
                if (Object.keys(cart).length) {
                    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
                } else {
                    localStorage.removeItem(CART_STORAGE_KEY);
                }
            } catch (e) {}
        }

        loadCart();
        renderCart();

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

        // ================= CART =================
        function renderCart() {
            const ids = Object.keys(cart);
            cartCount = 0;
            let total = 0;

            ids.forEach(function(id) {
                const line = cart[id];
                cartCount += line.qty;
                total += line.qty * line.unit_price;
            });

            if (!ids.length) {
                $('#cartBar').hide();
                $('#cartSummary').text('0 items');
                $('.cart-total').text('₱0.00');
                return;
            }

            $('#cartBar').show();
            $('#cartSummary').text(cartCount + ' item' + (cartCount > 1 ? 's' : ''));
            $('.cart-total').text('₱' + formatNumber(total));
        }

        function addToCart(idx) {
            const item = currentItems[idx];
            const unitPrice = item.facility_price !== null && item.facility_price !== undefined ? item.facility_price : item.selling_price;
            const qty = parseInt($('#buyQty').val(), 10);
            const available = parseFloat(item.available_stock || 0);

            if (!qty || qty <= 0) {
                Swal.fire("Validation", "Enter a valid quantity", "warning");
                return;
            }
            if (qty > available) {
                Swal.fire("Validation", "Only " + formatNumber(available) + " available", "warning");
                return;
            }

            const id = String(item.id);
            if (cart[id]) {
                const newQty = cart[id].qty + qty;
                if (newQty > available) {
                    Swal.fire("Validation", "Cart quantity exceeds available stock (" + formatNumber(available) + ")", "warning");
                    return;
                }
                cart[id].qty = newQty;
            } else {
                cart[id] = {
                    inventory_id: item.id,
                    product_id: item.product_id,
                    product_name: item.product_name,
                    facility_name: item.facility_name,
                    unit_price: unitPrice,
                    qty: qty,
                    available: available
                };
            }

            renderCart();
            saveCart();
            $('#buyModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Added to cart',
                text: item.product_name + ' x ' + qty,
                timer: 1200,
                showConfirmButton: false
            });
        }

        // ================= LOAD BENEFICIARIES =================
        function loadBeneficiaries() {
            if (beneficiariesLoaded) return;
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "LIST_BENEFICIARY", status: "VERIFIED" }),
                success: function(res) {
                    beneficiariesLoaded = true;
                    let opts = '<option value="">Select beneficiary</option>';
                    (res.data || []).forEach(function(b) {
                        opts += `<option value="${b.users_id}">${escapeHtml(b.name || 'Beneficiary')}${b.facility ? ' (' + escapeHtml(b.facility) + ')' : ''}</option>`;
                    });
                    $('#checkoutBeneficiary').html(opts);
                },
                error: function(xhr) {
                    console.log("LIST_BENEFICIARY failed:", xhr.responseText);
                }
            });
        }

        function openCheckout() {
            const ids = Object.keys(cart);
            if (!ids.length) return;

            loadBeneficiaries();

            let rows = '';
            let total = 0;
            ids.forEach(function(id) {
                const line = cart[id];
                const sub = line.qty * line.unit_price;
                total += sub;
                rows += `
                    <tr>
                        <td>${escapeHtml(line.product_name)}<br><small class="text-muted">${escapeHtml(line.facility_name || '')}</small></td>
                        <td>${escapeHtml(line.facility_name || '-')}</td>
                        <td>${formatNumber(line.qty)}</td>
                        <td>₱${formatNumber(line.unit_price)}</td>
                        <td>₱${formatNumber(sub)}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger cartRemoveBtn" data-id="${id}"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                `;
            });

            $('#checkoutCartBody').html(rows);
            $('#checkoutGrandTotal').text('₱' + formatNumber(total));
            $('#checkoutModal').modal('show');
        }

        // ================= EVENTS =================
        $(document).on('click', '.buyBtn', function() {
            const idx = $(this).data('idx');
            const item = currentItems[idx];
            if (!item) return;

            const unitPrice = item.facility_price !== null && item.facility_price !== undefined ? item.facility_price : item.selling_price;

            $('#buyIdx').val(idx);
            $('#buyProductSub').text(item.product_name + (item.sku ? ' | ' + item.sku : ''));
            $('#buyBatchLabel').html(
                `<strong>${escapeHtml(item.batch_number || '-')}</strong> · ${escapeHtml(item.facility_name || '-')} · Available: ${formatNumber(item.available_stock)} · Price: ₱${formatNumber(unitPrice)}`
            );
            $('#buyQty').val(1).attr('max', item.available_stock);
            $('#buyQtyHint').text('Max ' + formatNumber(item.available_stock) + ' units');
            $('#buyRemarks').val('');
            $('#buyModal').modal('show');
        });

        $('#btnAddToCart').on('click', function() {
            addToCart(parseInt($('#buyIdx').val(), 10));
        });

        $('#btnClearCart').on('click', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Clear cart?',
                text: 'All items will be removed.',
                showCancelButton: true,
                confirmButtonText: 'Yes, clear'
            }).then(function(result) {
                if (result.isConfirmed) {
                    cart = {};
                    renderCart();
                    saveCart();
                }
            });
        });

        $('#btnOpenCheckout').on('click', openCheckout);

        $(document).on('click', '.cartRemoveBtn', function() {
            delete cart[$(this).data('id')];
            renderCart();
            saveCart();
            if (Object.keys(cart).length) openCheckout();
            else $('#checkoutModal').modal('hide');
        });

        $('#btnPlaceCheckout').on('click', function() {
            const beneficiaryId = $('#checkoutBeneficiary').val();
            if (!beneficiaryId) {
                Swal.fire("Validation", "Select a beneficiary", "warning");
                return;
            }

            const items = Object.keys(cart).map(function(id) {
                const line = cart[id];
                return { inventory_id: line.inventory_id, product_id: line.product_id, qty: line.qty };
            });

            if (!items.length) {
                Swal.fire("Validation", "Cart is empty", "warning");
                return;
            }

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-checkout.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "PLACE_CHECKOUT",
                    beneficiary_id: beneficiaryId,
                    cooperative_id: <?= $_SESSION['users_id'] ?? 0 ?>,
                    users_id: <?= $_SESSION['users_id'] ?? 0 ?>,
                    payment_method: $('#checkoutPayment').val(),
                    items: items
                }),
                success: function(res) {
                    $('#btnPlaceCheckout').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Place Checkout');
                    if (res.code == 0) {
                        const d = res.data || {};
                        const sum = d.summary || {};
                        let lines = '';
                        (d.items || []).forEach(function(it) {
                            lines += `<tr><td>${escapeHtml(it.product_name || '')}</td><td>${formatNumber(it.qty)}</td><td>₱${formatNumber(it.subtotal)}</td></tr>`;
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Checkout successful',
                            html: `
                                <div class="text-start small">
                                    <p class="mb-1"><strong>Reference:</strong> ${escapeHtml(d.reference_id)}</p>
                                    <p class="mb-1"><strong>Beneficiary:</strong> ${escapeHtml((d.beneficiary || {}).name || '-')}</p>
                                    <p class="mb-1"><strong>Facility:</strong> ${escapeHtml((d.facility || {}).name || '-')}</p>
                                    <p class="mb-2"><strong>Grand Total:</strong> ₱${formatNumber(sum.grand_total)}</p>
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead><tr><th>Item</th><th>Qty</th><th>Subtotal</th></tr></thead>
                                        <tbody>${lines}</tbody>
                                    </table>
                                </div>
                            `,
                            confirmButtonText: 'Done'
                        }).then(function() {
                            cart = {};
                            renderCart();
                            saveCart();
                            $('#checkoutModal').modal('hide');
                            loadInventory();
                        });
                    } else {
                        Swal.fire("Error", res.message || "Checkout failed", "error");
                    }
                },
                error: function(xhr) {
                    $('#btnPlaceCheckout').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Place Checkout');
                    console.log("PLACE_CHECKOUT failed:", xhr.responseText);
                    Swal.fire("Error", "Server error during checkout", "error");
                }
            });
        });

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

                let unitPrice = item.facility_price !== null && item.facility_price !== undefined ? item.facility_price : item.selling_price;
                let hasPrice = unitPrice !== null && unitPrice !== undefined && unitPrice > 0;
                let priceHtml = hasPrice
                    ? `<div class="price-box"><span class="p-label">Facility Price</span><span class="p-value">₱${formatNumber(unitPrice)}</span></div>`
                    : `<div class="price-box"><span class="p-label">Facility Price</span><span class="p-value text-muted">Not set</span></div>`;

                let buyBtn = (item.available_stock > 0 && hasPrice)
                    ? `<button class="btn btn-success btn-sm w-100 buyBtn" data-idx="${index}">
                           <i class="fas fa-cart-plus me-1"></i> Buy
                       </button>`
                    : `<button class="btn btn-success btn-sm w-100" disabled title="${!hasPrice ? 'No facility price set' : 'Out of stock'}">
                           <i class="fas fa-cart-plus me-1"></i> Buy
                       </button>`;

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

                    <div class="card-price-row">${priceHtml}</div>

                    ${expiryHtml}

                    ${item.facility_name ? `<p class="card-info"><i class="fas fa-building me-1"></i>${escapeHtml(item.facility_name)}</p>` : ''}
                </div>
                <div class="card-footer" style="flex-direction:column;">
                    <button class="btn btn-outline-primary btn-sm w-100 viewProductBtn" data-idx="${index}">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                    ${buyBtn}
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

            let unitPrice = item.facility_price !== null && item.facility_price !== undefined ? item.facility_price : item.selling_price;

            $("#viewProductPrice").html(`
                <div class="price-box">
                    <span class="p-label">Facility Price</span>
                    <span class="p-value">${unitPrice !== null && unitPrice !== undefined ? '₱' + formatNumber(unitPrice) : 'Not set'}</span>
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

        // ================= BUY/SELL SIMULATION (embedded, session only) =================
        let simPreviewData = null;

        function formatMoney(value) {
            return '₱' + formatNumber(value);
        }

        function simRequest(trans, extra, cb) {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-simulation.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(Object.assign({ trans: trans }, extra || {})),
                success: cb,
                error: function(xhr) {
                    console.log(trans + " failed:", xhr.responseText);
                    Swal.fire("Error", "Server error occurred", "error");
                }
            });
        }

        function applySimStats(stats) {
            stats = stats || {};
            $('#stTotal').text(stats.total || 0);
            $('#stBuy').text(stats.buy || 0);
            $('#stSell').text(stats.sell || 0);
            $('#stUnitsBought').text(formatNumber(stats.units_bought));
            $('#stUnitsSold').text(formatNumber(stats.units_sold));
            $('#stSalesValue').text(formatMoney(stats.sales_value));
        }

        function renderSimHistory(history) {
            history = history || [];
            if (!history.length) {
                $('#simHistoryBody').html('<tr><td colspan="10" class="text-center text-muted py-3">No simulations recorded yet.</td></tr>');
                return;
            }
            let html = '';
            history.forEach(function(h, i) {
                const isBuy = h.type === 'BUY';
                const statusBadge = h.status === 'SUCCESS'
                    ? '<span class="badge light badge-success">Success</span>'
                    : '<span class="badge light badge-danger">Failed</span>';
                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td><span class="badge light ${isBuy ? 'badge-success' : 'badge-danger'}">${escapeHtml(h.type)}</span></td>
                        <td>${escapeHtml(h.facility_name || '-')}</td>
                        <td>${escapeHtml(h.product_name || '-')}</td>
                        <td>${formatNumber(h.quantity)}</td>
                        <td>${h.unit_price !== null && h.unit_price !== undefined ? formatMoney(h.unit_price) : '-'}</td>
                        <td>${formatMoney(h.total)}</td>
                        <td>${statusBadge}</td>
                        <td>${escapeHtml(h.reason || '-')}</td>
                        <td>${escapeHtml(h.created_at || '-')}</td>
                    </tr>`;
            });
            $('#simHistoryBody').html(html);
        }

        function loadSimState() {
            simRequest('SIM_GET_STATE', {}, function(res) {
                if (res.code != 0) return;
                applySimStats(res.data.stats);
                renderSimHistory(res.data.history);
            });
        }

        function loadSimFacilities() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-facility.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "LIST_FACILITY_BY_TYPE", facility_type: "1,4" }),
                success: function(res) {
                    let opts = '<option value="">Select Facility</option>';
                    (res.data || []).forEach(function(f) {
                        opts += `<option value="${f.id}">${escapeHtml(f.name)}</option>`;
                    });
                    $('#simFacility').html(opts);
                },
                error: function(xhr) { console.log(xhr.responseText); }
            });
        }

        function loadSimProducts() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-products.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "LIST_PRODUCT" }),
                success: function(res) {
                    let opts = '<option value="">Select Product</option>';
                    (res.data || []).forEach(function(p) {
                        if (p.status != 1) return;
                        opts += `<option value="${p.id}">${escapeHtml(p.name)}${p.sku ? ' (' + escapeHtml(p.sku) + ')' : ''}</option>`;
                    });
                    $('#simProduct').html(opts);
                },
                error: function(xhr) { console.log(xhr.responseText); }
            });
        }

        $('input[name="simType"]').on('change', function() {
            const isBuy = $(this).val() === 'BUY';
            $('#simBuyFields').toggle(isBuy);
        });

        $('#btnSimPreview').on('click', function() {
            const type = $('input[name="simType"]:checked').val();
            const facility_id = $('#simFacility').val();
            const product_id = $('#simProduct').val();
            const quantity = $('#simQty').val();

            if (!facility_id || !product_id || !quantity || parseFloat(quantity) <= 0) {
                Swal.fire("Validation", "Facility, product and a valid quantity are required", "warning");
                return;
            }

            const extra = {
                type: type,
                facility_id: facility_id,
                product_id: product_id,
                quantity: quantity
            };
            if (type === 'BUY') extra.unit_price = $('#simUnitPrice').val() || 0;

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Previewing...');

            simRequest('SIM_PREVIEW', extra, function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-eye me-1"></i> Preview Simulation');
                if (res.code != 0) {
                    Swal.fire("Error", res.message, "error");
                    return;
                }

                simPreviewData = res.data;
                const d = res.data;

                $('#simPreviewEmpty').hide();
                $('#simPreviewBody').show();
                $('#simPreviewBadge').text(d.type + ' · ' + d.product_name);

                $('#pvCurrent').text(formatNumber(d.current_stock) + ' (avail ' + formatNumber(d.available_stock) + ')');
                $('#pvDelta').text((d.type === 'BUY' ? '+' : '-') + formatNumber(d.quantity));
                $('#pvUnitPrice').text(d.unit_price !== null && d.unit_price !== undefined ? 'Unit ' + formatMoney(d.unit_price) : 'No price');
                $('#pvProjected').text(formatNumber(d.projected_current));
                $('#pvProjectedSub').text('avail ' + formatNumber(d.projected_available));

                $('#pvTotalLabel').text(d.type === 'BUY' ? 'Total Cost' : 'Total Sales Value');
                $('#pvTotal').text(formatMoney(d.total));

                if (d.failure) {
                    $('#simFailure').text(d.failure).show();
                } else {
                    $('#simFailure').hide();
                }

                $('#simLowStock').toggle(!!d.low_stock_after);

                if (d.type === 'SELL' && d.allocation.length) {
                    let rows = '';
                    d.allocation.forEach(function(a) {
                        rows += `
                            <tr>
                                <td>${escapeHtml(a.batch_number || 'Batch #' + a.inventory_id)}</td>
                                <td>${escapeHtml(a.expiry_date || '-')}</td>
                                <td>${formatNumber(a.available_stock)}</td>
                                <td><span class="fw-bold">${formatNumber(a.quantity)}</span></td>
                            </tr>`;
                    });
                    $('#simAllocationBody').html(rows);
                    $('#simAllocationWrap').show();
                } else {
                    $('#simAllocationWrap').hide();
                }
            });
        });

        $('#btnSimExecute').on('click', function() {
            if (!simPreviewData) return;

            const type = $('input[name="simType"]:checked').val();
            const extra = {
                type: type,
                facility_id: simPreviewData.facility_id,
                product_id: simPreviewData.product_id,
                quantity: simPreviewData.quantity
            };
            if (type === 'BUY') extra.unit_price = $('#simUnitPrice').val() || 0;

            const $btn = $(this);

            Swal.fire({
                title: 'Confirm simulation?',
                text: 'This only records to session history. It does NOT change real stock.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, simulate',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Recording...');
                simRequest('SIM_EXECUTE', extra, function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-play me-1"></i> Confirm &amp; Record Simulation');
                    applySimStats(res.data.stats);
                    renderSimHistory(res.data.history);
                    if (res.code == 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Simulation recorded',
                            text: res.data.record.id,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                });
            });
        });

        $('#btnClearSim').on('click', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Clear simulation history?',
                text: 'This removes all simulated records from this session.',
                showCancelButton: true,
                confirmButtonText: 'Yes, clear'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                simRequest('SIM_CLEAR', {}, function(res) {
                    applySimStats(res.data.stats);
                    renderSimHistory(res.data.history);
                    $('#simPreviewEmpty').show();
                    $('#simPreviewBody').hide();
                    $('#simPreviewBadge').text('Not previewed');
                    Swal.fire("Cleared", "Simulation history cleared.", "success");
                });
            });
        });

        // ================= INITIAL LOAD =================
        loadInventory();
        loadSimFacilities();
        loadSimProducts();
        loadSimState();

    });
</script>
<?= endSection() ?>
