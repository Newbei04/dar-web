<?= startSection('css') ?>
<style>
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
            <li class="breadcrumb-item"><a href="javascript:void(0)">Inventory</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Buy / Sell Simulation</a></li>
        </ol>
    </div>

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
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">New Simulation</h4>
                </div>
                <div class="card-body">
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
            </div>
        </div>

        <!-- PREVIEW -->
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Preview</h4>
                    <span class="badge light badge-secondary" id="simPreviewBadge">Not previewed</span>
                </div>
                <div class="card-body">
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
        </div>
    </div>

    <!-- HISTORY -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title">Simulation History <small class="text-muted">(session only — temporary)</small></h4>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearSim">
                        <i class="fas fa-eraser me-1"></i> Clear Simulation
                    </button>
                </div>
                <div class="card-body">
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
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {

        let previewData = null;

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
                    closeLoader();
                    console.log(trans + " failed:", xhr.responseText);
                    Swal.fire("Error", "Server error occurred", "error");
                }
            });
        }

        function applyStats(stats) {
            stats = stats || {};
            $('#stTotal').text(stats.total || 0);
            $('#stBuy').text(stats.buy || 0);
            $('#stSell').text(stats.sell || 0);
            $('#stUnitsBought').text(formatNumber(stats.units_bought));
            $('#stUnitsSold').text(formatNumber(stats.units_sold));
            $('#stSalesValue').text(formatMoney(stats.sales_value));
        }

        function renderHistory(history) {
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
                        <td>
                            <span class="badge light ${isBuy ? 'badge-success' : 'badge-danger'}">${escapeHtml(h.type)}</span>
                        </td>
                        <td>${escapeHtml(h.facility_name || '-')}</td>
                        <td>${escapeHtml(h.product_name || '-')}</td>
                        <td>${formatNumber(h.quantity)}</td>
                        <td>${h.unit_price !== null && h.unit_price !== undefined ? formatMoney(h.unit_price) : '-'}</td>
                        <td>${formatMoney(h.total)}</td>
                        <td>${statusBadge}</td>
                        <td>${escapeHtml(h.reason || '-')}</td>
                        <td>${escapeHtml(h.created_at || '-')}</td>
                    </tr>
                `;
            });

            $('#simHistoryBody').html(html);
        }

        function loadState() {
            simRequest('SIM_GET_STATE', {}, function(res) {
                if (res.code != 0) return;
                applyStats(res.data.stats);
                renderHistory(res.data.history);
            });
        }

        // ================= LOAD FACILITIES & PRODUCTS =================
        function loadFacilities() {
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

        function loadProducts() {
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

        // ================= TYPE TOGGLE =================
        $('input[name="simType"]').on('change', function() {
            const isBuy = $(this).val() === 'BUY';
            $('#simBuyFields').toggle(isBuy);
        });

        // ================= PREVIEW =================
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

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Previewing...');

            showLoader();
            simRequest('SIM_PREVIEW', extra, function(res) {
                closeLoader();
                $('#btnSimPreview').prop('disabled', false).html('<i class="fas fa-eye me-1"></i> Preview Simulation');
                if (res.code != 0) {
                    Swal.fire("Error", res.message, "error");
                    return;
                }

                previewData = res.data;
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
                            </tr>
                        `;
                    });
                    $('#simAllocationBody').html(rows);
                    $('#simAllocationWrap').show();
                } else {
                    $('#simAllocationWrap').hide();
                }
            });
        });

        // ================= EXECUTE =================
        $('#btnSimExecute').on('click', function() {
            if (!previewData) return;

            const type = $('input[name="simType"]:checked').val();
            const extra = {
                type: type,
                facility_id: previewData.facility_id,
                product_id: previewData.product_id,
                quantity: previewData.quantity
            };
            if (type === 'BUY') extra.unit_price = $('#simUnitPrice').val() || 0;

            Swal.fire({
                title: 'Confirm simulation?',
                text: 'This only records to session history. It does NOT change real stock.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, simulate',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Recording...');

                showLoader();
                simRequest('SIM_EXECUTE', extra, function(res) {
                    closeLoader();
                    $('#btnSimExecute').prop('disabled', false).html('<i class="fas fa-play me-1"></i> Confirm &amp; Record Simulation');
                    applyStats(res.data.stats);
                    renderHistory(res.data.history);
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

        // ================= CLEAR =================
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
                    applyStats(res.data.stats);
                    renderHistory(res.data.history);
                    $('#simPreviewEmpty').show();
                    $('#simPreviewBody').hide();
                    $('#simPreviewBadge').text('Not previewed');
                    Swal.fire("Cleared", "Simulation history cleared.", "success");
                });
            });
        });

        // ================= INIT =================
        loadFacilities();
        loadProducts();
        loadState();
    });
</script>
<?= endSection() ?>
