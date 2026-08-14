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
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Inventory</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Inventory List</h4>

                    <a href="<?= $baseURL ?>add-inventory" class="btn btn-primary">
                        <i class="fa fa-plus mr-1"></i> Add Inventory
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblInventory" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="8%">Image</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Facilities</th>
                                <th width="8%">Current</th>
                                <th width="8%">Reserved</th>
                                <th width="8%">Available</th>
                                <th width="10%">Batches</th>
                                <th width="8%">Status</th>
                                <th width="10%">Actions</th>
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

<!-- ================= BATCH INFO MODAL ================= -->
<div class="modal fade" id="batchInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Batch Information</h5>
                    <small class="text-muted" id="batchProductLabel"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>Facility</th>
                                <th>Batch</th>
                                <th>Current</th>
                                <th>Reserved</th>
                                <th>Available</th>
                                <th>Cost</th>
                                <th>Selling</th>
                                <th>Expiry</th>
                                <th>Storage</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="batchInfoBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= INVENTORY ACTION MODAL ================= -->
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
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        let inventoryGroups = {};

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

        function getStatusBadge(status) {
            return status == 1 ?
                '<span class="badge light badge-success">Active</span>' :
                '<span class="badge light badge-danger">Inactive</span>';
        }

        function buildInventoryGroups(data) {
            inventoryGroups = {};

            data.forEach(function(item) {
                let key = item.product_id || 'unknown';

                if (!inventoryGroups[key]) {
                    inventoryGroups[key] = {
                        product_id: item.product_id,
                        product_name: item.product_name ?? '-',
                        category_name: item.category_name ?? '-',
                        sku: item.sku ?? '-',
                        primary_image: item.primary_image ?? null,
                        current_stock: 0,
                        reserved_stock: 0,
                        available_stock: 0,
                        active_count: 0,
                        facilities: new Set(),
                        batches: []
                    };
                }

                inventoryGroups[key].current_stock += parseFloat(item.current_stock ?? 0);
                inventoryGroups[key].reserved_stock += parseFloat(item.reserved_stock ?? 0);
                inventoryGroups[key].available_stock += parseFloat(item.available_stock ?? 0);

                if (item.status == 1) {
                    inventoryGroups[key].active_count++;
                }

                if (item.facility_name) {
                    inventoryGroups[key].facilities.add(item.facility_name);
                }

                inventoryGroups[key].batches.push(item);
            });

            return Object.values(inventoryGroups);
        }

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
                    let groupedData = buildInventoryGroups(data);

                    if (!groupedData.length) {
                        tblData.draw(false);
                        return;
                    }

                    groupedData.forEach(function(item, index) {
                        let available = parseFloat(item.available_stock ?? 0);

                        let stockColor =
                            available <= 0 ? 'text-danger' :
                            available <= 10 ? 'text-warning' :
                            'text-success';

                        let status = item.active_count > 0 ?
                            '<span class="badge light badge-success">Active</span>' :
                            '<span class="badge light badge-danger">Inactive</span>';

                        let facilities = Array.from(item.facilities);
                        let facilityText = facilities.length > 0 ? facilities.join(', ') : '-';

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
                                <a class="dropdown-item batchBtn" href="javascript:void(0);" data-product_id="${item.product_id}">
                                    <i class="fas fa-layer-group me-1"></i> View Batches (${item.batches.length})
                                </a>
                            </div>
                        </div>
                        `;

                        tblData.row.add([
                            index + 1,
                            imageHtml,
                            productInfo,
                            escapeHtml(item.category_name),
                            escapeHtml(facilityText),
                            formatNumber(item.current_stock),
                            formatNumber(item.reserved_stock),
                            `<span class="${stockColor} font-weight-bold">${formatNumber(available)}</span>`,
                            `<button class="btn btn-outline-info btn-sm batchBtn" data-product_id="${item.product_id}">
                                <i class="fas fa-layer-group mr-1"></i> ${item.batches.length} Batch${item.batches.length > 1 ? 'es' : ''}
                            </button>`,
                            status,
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

        // ================= BATCH INFO =================
        $(document).on("click", ".batchBtn", function() {
            let productId = $(this).data("product_id");
            let group = inventoryGroups[productId];

            if (!group) {
                Swal.fire("Error", "Batch information not found", "error");
                return;
            }

            $("#batchProductLabel").text(`${group.product_name} | ${group.sku}`);

            let html = '';

            group.batches.forEach(function(item, index) {
                let available = parseFloat(item.available_stock ?? 0);
                let stockColor =
                    available <= 0 ? "text-danger" :
                    available <= 10 ? "text-warning" :
                    "text-success";

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(item.facility_name ?? '-')}</td>
                        <td><strong>${escapeHtml(item.batch_number || '-')}</strong></td>
                        <td>${formatNumber(item.current_stock)}</td>
                        <td>${formatNumber(item.reserved_stock)}</td>
                        <td><span class="${stockColor} font-weight-bold">${formatNumber(item.available_stock)}</span></td>
                        <td>${item.cost_price != null ? '₱' + formatNumber(item.cost_price) : '-'}</td>
                        <td>${item.selling_price != null ? '₱' + formatNumber(item.selling_price) : '-'}</td>
                        <td>${escapeHtml(item.expiry_date ?? '-')}</td>
                        <td>${escapeHtml(item.storage_location ?? '-')}</td>
                        <td>${getStatusBadge(item.status)}</td>
                        <td>
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
                                    <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="reserve"><i class="fas fa-lock me-1"></i> Reserve</a>
                                    <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="release"><i class="fas fa-unlock me-1"></i> Release</a>
                                    <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="adjust"><i class="fas fa-sliders-h me-1"></i> Adjust</a>
                                    <a class="dropdown-item invActionBtn" href="javascript:void(0);" data-id="${item.id}" data-action="transfer"><i class="fas fa-paper-plane me-1"></i> Transfer</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item invStatusBtn" href="javascript:void(0);" data-id="${item.id}" data-status="${item.status}"><i class="fas fa-power-off me-1"></i> Toggle Status</a>
                                    <a class="dropdown-item viewInvLogsBtn" href="javascript:void(0);" data-id="${item.id}"><i class="fas fa-list me-1"></i> View Movements</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });

            $("#batchInfoBody").html(html);
            $("#batchInfoModal").modal("show");
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
                        else if (log.action_type == 4) typeBadge = 'badge-light badge-warning';
                        else if (log.action_type == 7) typeBadge = 'badge-light badge-primary';

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

        // ================= INVENTORY ACTIONS (Reserve/Release/Adjust/Transfer) =================
        const actionMeta = {
            reserve: {
                title: 'Reserve Stock',
                hint: 'Max qty = available stock',
                btn: 'btn-primary',
                trans: 'RESERVE_INVENTORY'
            },
            release: {
                title: 'Release Reservation',
                hint: 'Max qty = reserved stock',
                btn: 'btn-primary',
                trans: 'RELEASE_RESERVATION'
            },
            adjust: {
                title: 'Adjust Stock',
                hint: 'Set new current stock (cannot be below reserved)',
                btn: 'btn-warning',
                trans: 'ADJUST_INVENTORY'
            },
            transfer: {
                title: 'Transfer Stock',
                hint: 'Max qty = available stock',
                btn: 'btn-info',
                trans: 'TRANSFER_INVENTORY'
            }
        };

        $("#invActionDest").selectpicker();

        $(document).on("click", ".invActionBtn", function() {
            let id = $(this).data("id");
            let action = $(this).data("action");
            let meta = actionMeta[action];
            if (!meta) return;

            // find batch info from the open group
            let batch = null;
            Object.values(inventoryGroups).forEach(function(g) {
                g.batches.forEach(function(b) {
                    if (b.id == id) batch = b;
                });
            });

            if (!batch) {
                Swal.fire("Error", "Batch information not found", "error");
                return;
            }

            $("#invActionId").val(id);
            $("#invActionType").val(action);
            $("#invActionModalLabel").text(meta.title);
            $("#btnInvActionSave").removeClass('btn-primary btn-warning btn-info').addClass(meta.btn);
            $("#invActionBatchLabel").text(
                `${batch.facility_name ?? '-'} | ${batch.batch_number || '-'} | Available: ${formatNumber(batch.available_stock)} | Reserved: ${formatNumber(batch.reserved_stock)}`
            );
            $("#invActionQtyHint").text(meta.hint);
            $("#invActionQty").val('');
            $("#invActionRemarks").val('');

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
                        facility_type: 3
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

            if (isNaN(qty) || qty <= 0) {
                Swal.fire("Validation", "Enter a valid quantity", "warning");
                return;
            }
            if (action == 'transfer' && !dest) {
                Swal.fire("Validation", "Select destination facility", "warning");
                return;
            }

            let payload = {
                trans: actionMeta[action].trans,
                inventory_id: id,
                source_inventory_id: id,
                remarks: remarks,
                users_id: <?= $_SESSION['users_id'] ?? 0 ?>
            };
            if (action === 'adjust') {
                payload.new_stock = qty;
            } else {
                payload.quantity = qty;
            }
            if (action === 'transfer') {
                payload.destination_facility_id = dest;
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
                        $("#batchInfoModal").modal('hide');
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
                            $("#batchInfoModal").modal('hide');
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
