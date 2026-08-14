<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
    .inv-thumb {
        width: 50px;
        height: 50px;
        border-radius: 10%;
        object-fit: cover;
    }
    .facility-filter {
        min-width: 260px;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Inventory</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header flex-wrap">
                    <h4 class="card-title">Inventory List <small class="text-muted">(batch level)</small></h4>

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <select id="facilityFilter" class="form-control default-select facility-filter">
                            <option value="">All Facilities</option>
                        </select>

                        <a href="<?= $baseURL ?>stock-movements" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-exchange-alt mr-1"></i> Stock Movements
                        </a>
                        <a href="<?= $baseURL ?>low-stock" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Low Stock
                        </a>
                        <a href="<?= $baseURL ?>simulation" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-flask mr-1"></i> Simulation
                        </a>
                        <a href="<?= $baseURL ?>add-inventory" class="btn btn-primary">
                            <i class="fa fa-plus mr-1"></i> Receive Stock
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblInventory" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="7%">Image</th>
                                <th>Product</th>
                                <th>Facility</th>
                                <th>Batch</th>
                                <th>Received</th>
                                <th>Current</th>
                                <th>Reserved</th>
                                <th>Available</th>
                                <th>Reorder</th>
                                <th>Cost</th>
                                <th>Selling</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th width="8%">Actions</th>
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

<!-- ================= INVENTORY ACTION MODAL (Reserve/Release/Adjust/Transfer/Sell/Return/Damaged/Expired) ================= -->
<div class="modal fade" id="invActionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invActionModalLabel">Inventory Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="invActionId">
                <input type="hidden" id="invActionType">

                <div class="mb-3">
                    <label class="form-label fw-bold">Batch</label>
                    <div id="invActionBatchLabel" class="border rounded p-2 bg-light"></div>
                </div>

                <div class="mb-3" id="invActionSellInfo" style="display:none;">
                    <label class="form-label fw-bold">Facility Price</label>
                    <div id="invActionSellPrice" class="border rounded p-2 bg-light"></div>
                </div>

                <div class="mb-3" id="invActionDestGroup" style="display:none;">
                    <label class="form-label">Destination Facility <span class="text-danger">*</span></label>
                    <select id="invActionDest" class="form-control default-select">
                        <option value="">Select Facility</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" id="invActionQty" class="form-control" min="0" step="any">
                    <small class="form-text text-muted" id="invActionQtyHint"></small>
                </div>

                <div class="mb-0">
                    <label class="form-label">Remarks</label>
                    <input type="text" id="invActionRemarks" class="form-control" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnInvActionSave">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= INVENTORY LOGS MODAL ================= -->
<div class="modal fade" id="invLogsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Inventory Movements</h5>
                    <small class="text-muted" id="invLogsSubLabel"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="font-weight-bold">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Qty Changed</th>
                                <th>New Balance</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="invLogsBody"></tbody>
                    </table>
                </div>
                <div id="invLogsEmpty" class="text-center text-muted py-4" style="display:none;">No movements recorded for this batch.</div>
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

        var tblData = $('#tblInventory').DataTable({
            responsive: true,
            order: [[0, 'asc']],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        let inventoryRows = [];

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
            if (value === null || value === undefined || value === '') return '-';
            return '₱' + formatNumber(value);
        }

        // Fallback thumbnail when an image file is missing
        function productThumbFallback() {
            return '<div style="width:50px;height:50px;border-radius:10%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted"></i></div>';
        }
        window.productThumbFallback = productThumbFallback;

        function getStatusBadge(status) {
            return status == 1 ?
                '<span class="badge light badge-success">Active</span>' :
                '<span class="badge light badge-danger">Inactive</span>';
        }

        function getExpiryBadge(expiryDate) {
            if (!expiryDate) return '<span class="text-muted">-</span>';

            let today = new Date();
            today.setHours(0, 0, 0, 0);
            let exp = new Date(expiryDate);
            let diffDays = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));

            if (diffDays < 0) {
                return '<span class="badge light badge-danger">' + escapeHtml(expiryDate) + ' (Expired)</span>';
            }
            if (diffDays <= 60) {
                return '<span class="badge light badge-warning">' + escapeHtml(expiryDate) + ' (Expiring)</span>';
            }
            return escapeHtml(expiryDate);
        }

        function getStockCell(available, reorderLevel) {
            let cls = available <= 0 ? 'text-danger' : (available <= reorderLevel ? 'text-warning' : 'text-success');
            let low = available <= reorderLevel ? ' <span class="badge light badge-warning">LOW</span>' : '';
            return '<span class="' + cls + ' font-weight-bold">' + formatNumber(available) + '</span>' + low;
        }

        // ================= FACILITY FILTER =================
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-facility.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                trans: "LIST_FACILITY_BY_TYPE",
                facility_type: "1,4"
            }),
            success: function(res) {
                let opts = '<option value="">All Facilities</option>';
                (res.data || []).forEach(function(f) {
                    opts += `<option value="${f.id}">${escapeHtml(f.name)}</option>`;
                });
                $("#facilityFilter").html(opts);
                if ($.fn.select2) {
                    $("#facilityFilter").trigger('change');
                }
            }
        });

        $("#facilityFilter").change(function() {
            let val = $(this).val();
            tblData.column(3).search(val ? '^' + val + '$' : '', true, false).draw();
        });

        loadInventory();

        function loadInventory() {
            showLoader();
            tblData.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_INVENTORY",
                    employee_id: <?= (isset($_SESSION['type']) && $_SESSION['type'] == 1) ? 'null' : ($_SESSION['users_id'] ?? 'null') ?>
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load inventory.", "error");
                        return;
                    }
                    let data = Array.isArray(res.data) ? res.data : [];
                    inventoryRows = data;

                    if (!data.length) {
                        tblData.draw(false);
                        return;
                    }

                    data.forEach(function(item, index) {
                        let available = parseFloat(item.available_stock ?? 0);
                        let reorder = parseFloat(item.reorder_level ?? 0);

                        let imageHtml = item.primary_image ?
                            `<img src="<?= $baseURL ?>assets/images/product/${escapeHtml(item.primary_image)}" alt="Product" class="inv-thumb" onerror="this.outerHTML=productThumbFallback();">` :
                            productThumbFallback();

                        let productInfo =
                            `<span class="fas fa-box"></span> ` + escapeHtml(item.product_name) + `<br>` +
                            `<span class="fas fa-barcode"></span> <small>` + escapeHtml(item.sku) + `</small>`;

                        let actions = `
                        <div class="dropdown ms-auto text-end c-pointer">
                            <div class="btn-link" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                        <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                        <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                    </g>
                                </svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item viewProductBtn" href="javascript:void(0);" data-id="${item.product_id}">
                                    <i class="fas fa-eye me-1"></i> View Product
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="sell">
                                    <i class="fas fa-cart-plus me-1 text-success"></i> Sell
                                </a>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="return">
                                    <i class="fas fa-undo me-1 text-primary"></i> Return
                                </a>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="damaged">
                                    <i class="fas fa-times-circle me-1 text-danger"></i> Mark Damaged
                                </a>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="expired">
                                    <i class="fas fa-clock me-1 text-warning"></i> Mark Expired
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="reserve"><i class="fas fa-lock me-1"></i> Reserve</a>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="release"><i class="fas fa-unlock me-1"></i> Release</a>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="adjust"><i class="fas fa-sliders-h me-1"></i> Adjust</a>
                                <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="transfer"><i class="fas fa-paper-plane me-1"></i> Transfer</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item invStatusBtn" href="javascript:void(0);" data-id="${item.id}" data-status="${item.status}"><i class="fas fa-power-off me-1"></i> Toggle Status</a>
                                <a class="dropdown-item viewInvLogsBtn" href="javascript:void(0);" data-id="${item.id}"><i class="fas fa-list me-1"></i> View Movements</a>
                            </div>
                        </div>
                        `;

                        tblData.row.add([
                            index + 1,
                            imageHtml,
                            productInfo,
                            escapeHtml(item.facility_name ?? '-'),
                            '<strong>' + escapeHtml(item.batch_number || '-') + '</strong>',
                            formatNumber(item.received_stock),
                            formatNumber(item.current_stock),
                            formatNumber(item.reserved_stock),
                            getStockCell(available, reorder),
                            formatNumber(reorder),
                            formatMoney(item.cost_price),
                            formatMoney(item.facility_price !== null && item.facility_price !== undefined ? item.facility_price : item.selling_price),
                            getExpiryBadge(item.expiry_date),
                            getStatusBadge(item.status),
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_INVENTORY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load inventory.", "error");
                }
            });
        }

        // ================= VIEW PRODUCT =================
        $(document).on("click", ".viewProductBtn", function() {
            let id = $(this).data("id");
            window.location.href = "<?= $baseURL ?>product-details?id=" + id;
        });

        // ================= VIEW MOVEMENTS =================
        $(document).on("click", ".viewInvLogsBtn", function() {
            let id = $(this).data("id");
            $("#invLogsBody").html('');
            $("#invLogsEmpty").hide();
            $("#invLogsSubLabel").text('');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_INVENTORY_LOGS",
                    inventory_id: id
                }),
                success: function(res) {
                    let logs = Array.isArray(res.data) ? res.data : [];

                    if (!logs.length) {
                        $("#invLogsEmpty").show();
                        $("#invLogsModal").modal("show");
                        return;
                    }

                    $("#invLogsSubLabel").text(`${logs[0].product_name ?? ''} ${logs[0].sku ? '| ' + logs[0].sku : ''}`);

                    let html = '';

                    logs.forEach(function(log) {
                        let qty = parseFloat(log.quantity_changed || 0);
                        let qtyHtml = qty > 0 ?
                            `<span class="text-success font-weight-bold">+${formatNumber(qty)}</span>` :
                            qty < 0 ?
                            `<span class="text-danger font-weight-bold">${formatNumber(qty)}</span>` :
                            `<span class="text-muted">0</span>`;

                        let typeBadge = 'badge-light badge-secondary';
                        if (log.action_type == 1) typeBadge = 'badge-light badge-success';
                        else if (log.action_type == 2) typeBadge = 'badge-light badge-info';
                        else if (log.action_type == 3) typeBadge = 'badge-light badge-primary';
                        else if (log.action_type == 4) typeBadge = 'badge-light badge-warning';
                        else if (log.action_type == 5) typeBadge = 'badge-light badge-dark';
                        else if (log.action_type == 6) typeBadge = 'badge-light badge-danger';
                        else if (log.action_type == 7) typeBadge = 'badge-light badge-primary';
                        else if (log.action_type == 10) typeBadge = 'badge-light badge-secondary';

                        html += `
                            <tr>
                                <td>${escapeHtml(log.created_at ?? '-')}</td>
                                <td><span class="badge ${typeBadge}">${escapeHtml(log.action_label ?? '')}</span></td>
                                <td>${qtyHtml}</td>
                                <td>${formatNumber(log.new_balance)}</td>
                                <td>${escapeHtml(log.remarks ?? '')}</td>
                            </tr>
                        `;
                    });

                    $("#invLogsBody").html(html);
                    $("#invLogsModal").modal("show");
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Failed to load inventory movements", "error");
                }
            });
        });

        // ================= INVENTORY ACTIONS =================
        const actionMeta = {
            sell: {
                title: 'Sell Stock',
                hint: 'Max qty = available stock. Sold via FEFO.',
                btn: 'btn-success',
                trans: 'SELL_INVENTORY',
                qtyOptional: false
            },
            return: {
                title: 'Return Stock',
                hint: 'Quantity to add back to this batch',
                btn: 'btn-primary',
                trans: 'RETURN_INVENTORY',
                qtyOptional: false
            },
            damaged: {
                title: 'Mark Damaged',
                hint: 'Max qty = available stock',
                btn: 'btn-danger',
                trans: 'DAMAGE_INVENTORY',
                qtyOptional: false
            },
            expired: {
                title: 'Mark Expired',
                hint: 'Leave blank to remove all current stock of this batch',
                btn: 'btn-warning',
                trans: 'MARK_EXPIRED',
                qtyOptional: true
            },
            reserve: {
                title: 'Reserve Stock',
                hint: 'Max qty = available stock',
                btn: 'btn-primary',
                trans: 'RESERVE_INVENTORY',
                qtyOptional: false
            },
            release: {
                title: 'Release Reservation',
                hint: 'Max qty = reserved stock',
                btn: 'btn-primary',
                trans: 'RELEASE_RESERVATION',
                qtyOptional: false
            },
            adjust: {
                title: 'Adjust Stock',
                hint: 'Set new current stock (cannot be below reserved)',
                btn: 'btn-warning',
                trans: 'ADJUST_INVENTORY',
                qtyOptional: false
            },
            transfer: {
                title: 'Transfer Stock',
                hint: 'Max qty = available stock',
                btn: 'btn-info',
                trans: 'TRANSFER_INVENTORY',
                qtyOptional: false
            }
        };

        $("#invActionDest").selectpicker();

        function findBatch(id) {
            for (let i = 0; i < inventoryRows.length; i++) {
                if (inventoryRows[i].id == id) return inventoryRows[i];
            }
            return null;
        }

        $(document).on("click", ".invActionBtn", function() {
            let id = $(this).data("id");
            let action = $(this).data("action");
            let meta = actionMeta[action];
            if (!meta) return;

            let batch = findBatch(id);
            if (!batch) {
                Swal.fire("Error", "Batch information not found", "error");
                return;
            }

            let available = parseFloat(batch.available_stock ?? 0);
            let facilityPrice = batch.facility_price !== null && batch.facility_price !== undefined ? batch.facility_price : batch.selling_price;

            $("#invActionId").val(id);
            $("#invActionType").val(action);
            $("#invActionModalLabel").text(meta.title);
            $("#btnInvActionSave").removeClass('btn-primary btn-success btn-danger btn-warning btn-info').addClass(meta.btn);
            $("#invActionBatchLabel").text(
                `${batch.facility_name ?? '-'} | ${batch.batch_number || '-'} | Available: ${formatNumber(available)} | Reserved: ${formatNumber(batch.reserved_stock)}`
            );
            $("#invActionQtyHint").text(meta.hint);
            $("#invActionQty").val('');
            $("#invActionRemarks").val('');

            // Sell info panel
            if (action == 'sell') {
                let priceTxt = facilityPrice !== null && facilityPrice !== undefined
                    ? '₱' + formatNumber(facilityPrice) + ' / unit'
                    : '<span class="text-danger">No facility price set. Set it first before selling.</span>';
                $("#invActionSellPrice").html(priceTxt);
                $("#invActionSellInfo").show();
            } else {
                $("#invActionSellInfo").hide();
            }

            // populate destination facilities for transfer
            if (action == 'transfer') {
                $("#invActionDestGroup").show();
                let opts = '<option value="">Select Facility</option>';
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-facility.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "LIST_FACILITY_BY_TYPE",
                        facility_type: "1,4"
                    }),
                    success: function(res) {
                        (res.data || []).forEach(function(f) {
                            if (f.id != batch.facility_id) {
                                opts += `<option value="${f.id}">${escapeHtml(f.name)}</option>`;
                            }
                        });
                        $("#invActionDest").html(opts).selectpicker('refresh');
                    },
                    error: function() {
                        $("#invActionDest").html(opts).selectpicker('refresh');
                    }
                });
            } else {
                $("#invActionDestGroup").hide();
            }

            $("#invActionModal").modal("show");
        });

        $("#btnInvActionSave").click(function() {
            let id = $("#invActionId").val();
            let action = $("#invActionType").val();
            let qty = parseFloat($("#invActionQty").val());
            let remarks = $("#invActionRemarks").val().trim();
            let dest = $("#invActionDest").val();
            let meta = actionMeta[action];

            if (isNaN(qty)) qty = 0;

            if (!meta.qtyOptional && (isNaN(qty) || qty <= 0)) {
                Swal.fire("Validation", "Enter a valid quantity", "warning");
                return;
            }
            if (action == 'transfer' && !dest) {
                Swal.fire("Validation", "Select destination facility", "warning");
                return;
            }

            let batch = findBatch(id);

            let payload = {
                trans: meta.trans,
                remarks: remarks,
                users_id: <?= $_SESSION['users_id'] ?? 0 ?>
            };

            switch (action) {
                case 'sell':
                    payload.facility_id = batch.facility_id;
                    payload.product_id = batch.product_id;
                    payload.quantity = qty;
                    break;
                case 'return':
                case 'damaged':
                case 'expired':
                case 'reserve':
                case 'release':
                    payload.inventory_id = id;
                    payload.quantity = qty;
                    break;
                case 'adjust':
                    payload.inventory_id = id;
                    payload.new_stock = qty;
                    break;
                case 'transfer':
                    payload.source_inventory_id = id;
                    payload.destination_facility_id = dest;
                    payload.quantity = qty;
                    break;
            }

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(payload),
                success: function(res) {
                    $("#btnInvActionSave").prop('disabled', false).text('Confirm');
                    if (res.code == 0) {
                        $("#invActionModal").modal('hide');
                        Swal.fire("Success", res.message, "success");
                        loadInventory();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    $("#btnInvActionSave").prop('disabled', false).text('Confirm');
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Server error occurred", "error");
                }
            });
        });

        // ================= TOGGLE STATUS =================
        $(document).on("click", ".invStatusBtn", function() {
            let id = $(this).data("id");
            let currentStatus = parseInt($(this).data("status"));
            let newStatus = currentStatus == 1 ? 0 : 1;

            Swal.fire({
                icon: "warning",
                title: "Toggle inventory status?",
                text: "This changes the availability of this batch.",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, update"
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-inventory.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "EDIT_STATUS",
                        id: id,
                        status: newStatus
                    }),
                    success: function(res) {
                        if (res.code == 0) {
                            Swal.fire("Success", res.message, "success");
                            loadInventory();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire("Error", "Server error occurred", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>
