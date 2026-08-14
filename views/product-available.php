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
        color: #166534;
    }

    .cart-info .cart-total {
        font-weight: 700;
        color: #14532d;
    }

    .cart-actions {
        display: flex;
        gap: 8px;
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
                            <select class="form-select default-select" id="checkoutBeneficiary">
                                <option value="">Select beneficiary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="checkoutPayment">
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
                }
            });
        });

        $('#btnOpenCheckout').on('click', openCheckout);

        $(document).on('click', '.cartRemoveBtn', function() {
            delete cart[$(this).data('id')];
            renderCart();
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
                    ? `<button class="btn btn-success btn-sm flex-fill buyBtn" data-idx="${index}">
                           <i class="fas fa-cart-plus me-1"></i> Buy
                       </button>`
                    : `<button class="btn btn-success btn-sm flex-fill" disabled title="${!hasPrice ? 'No facility price set' : 'Out of stock'}">
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
                <div class="card-footer">
                    <button class="btn btn-outline-primary btn-sm flex-fill viewProductBtn" data-idx="${index}">
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

        // ================= INITIAL LOAD =================
        loadInventory();

    });
</script>
<?= endSection() ?>
