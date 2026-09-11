<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<?php
$isAdmin = (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) ||
           (isset($_SESSION['type']) && $_SESSION['type'] == 1);
?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Machine</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Machine Maintenance</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Machinery Maintenance</h4>

                    <?php if ($isAdmin) { ?>
                        <button type="button" class="btn btn-primary" id="btnAddMaintenance">
                            <i class="fa fa-plus mr-1"></i> Add Maintenance
                        </button>
                    <?php } ?>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblMaintenance" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="8%">Image</th>
                                <th>Machinery</th>
                                <th>Facility</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Date</th>
                                <th width="18%">Actions</th>
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

<!-- ================= ADD MAINTENANCE MODAL ================= -->
<div class="modal fade" id="addMaintenanceModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="mb-0 font-weight-bold">Add Maintenance</h5>
                    <small class="text-muted">Schedule maintenance for a machine</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Machine Type <span class="text-danger">*</span></label>
                        <select class="form-control single-select" id="machinery_type_id">
                            <option value="">Select Machine Type</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Machinery <span class="text-danger">*</span></label>
                        <select class="form-control single-select" id="machinery_id"></select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Facility <span class="text-danger">*</span></label>
                        <select class="form-control single-select" id="facility_id"></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Type <span class="text-danger">*</span></label>
                        <select class="form-control single-select" id="type">
                            <option value="">Select Type</option>
                            <option value="1">Preventive</option>
                            <option value="2">Corrective</option>
                            <option value="4">Emergency</option>
                            <option value="5">Inspection</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select class="form-control single-select" id="priority">
                            <option value="">Select Priority</option>
                            <option value="1">Low</option>
                            <option value="2">Medium</option>
                            <option value="3">High</option>
                            <option value="4">Critical</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Start Date</label>
                        <input type="datetime-local" class="form-control" id="start_date">
                    </div>
                    <div class="form-group col-md-6">
                        <label>End Date</label>
                        <input type="datetime-local" class="form-control" id="end_date">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Labor Cost (₱)</label>
                        <input type="number" class="form-control" id="labor_cost" value="0" min="0">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Parts Cost (₱)</label>
                        <input type="number" class="form-control" id="parts_cost" value="0" min="0">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Odometer Reading</label>
                        <input type="number" class="form-control" id="odometer" value="0" min="0">
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label>Description</label>
                    <textarea class="form-control" id="description" rows="3" placeholder="Enter maintenance description"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveMaintenanceBtn">
                    <i class="fa fa-save mr-1"></i> Save Maintenance
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= VIEW MAINTENANCE MODAL ================= -->
<div class="modal fade" id="viewMaintenanceModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="viewModalTitle">Maintenance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-5 mb-3 mb-md-0">
                        <div class="position-relative rounded-3 overflow-hidden bg-light" style="height:260px;display:flex;align-items:center;justify-content:center;">
                            <div id="viewMaintenancePlaceholder" class="d-flex align-items-center justify-content-center text-muted" style="height:100%;width:100%;">
                                <i class="fas fa-image me-2"></i> No image
                            </div>
                            <img id="viewMaintenanceImg" src="" alt="Machine Image" class="d-none w-100 h-100 object-fit-cover" style="position:relative;z-index:1;">
                        </div>
                    </div>

                    <div class="col-md-7">
                        <h5 class="fw-semibold mb-1" id="viewDetailName">—</h5>
                        <small class="text-muted d-block" id="viewDetailFacility"></small>

                        <div id="viewDetailRows" class="mt-3"></div>
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

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        var isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;

        let tbl = $('#tblMaintenance').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        loadMaintenance();

        /* =========================
           LOAD LIST
        ========================== */
        function loadMaintenance() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-machinery-maintenance.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MAINTENANCE"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 1) {
                        Swal.fire("Error", res.message || "Failed to load maintenance records.", "error");
                        return;
                    }

                    let data = res.data || [];
                    if (data.length === 0) {
                        tbl.draw(false);
                        return;
                    }

                    data.forEach(function(item, i) {

                        function machineThumbFallback() {
                            return '<div style="width:50px;height:50px;border-radius:10%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted"></i></div>';
                        }
                        window.machineThumbFallback = machineThumbFallback;

                        let machineryImage = item.image ?
                            `<img src="<?= $baseURL ?>assets/images/machinery/${item.image}" alt="Machine" style="width:50px;height:50px;border-radius:10%;object-fit:cover;" onerror="this.outerHTML=machineThumbFallback();">` :
                            machineThumbFallback();

                        let statusBadge =
                            item.status_label == 'Completed' ? '<span class="badge light badge-success badge-sm">Completed</span>' :
                            item.status_label == 'In Progress' ? '<span class="badge light badge-info badge-sm">In Progress</span>' :
                            '<span class="badge light badge-warning badge-sm">Scheduled</span>';

                        let priorityBadge =
                            item.priority_label == 'Low' ? '<span class="badge light badge-secondary badge-sm">Low</span>' :
                            item.priority_label == 'Medium' ? '<span class="badge light badge-info badge-sm">Medium</span>' :
                            item.priority_label == 'High' ? '<span class="badge light badge-warning badge-sm">High</span>' :
                            '<span class="badge light badge-danger badge-sm">Critical</span>';

                        let typeBadge = '<span class="badge light badge-primary badge-sm">' + (item.type_label || '-') + '</span>';

                        let cost = parseFloat(item.total_cost ?? (parseFloat(item.labor_cost) + parseFloat(item.parts_cost)));
                        let costHtml = isNaN(cost) ? '₱0.00' : '₱' + cost.toFixed(2);

                        let actionBtn = `
                        <button class="btn btn-info mr-2 viewBtn"
                            data-id="${item.id}"
                            data-machinery="${item.machinery_name || '-'}"
                            data-facility="${item.facility_name || '-'}"
                            data-type="${item.type_label || '-'}"
                            data-priority="${item.priority_label || '-'}"
                            data-status="${item.status_label || '-'}"
                            data-cost="${costHtml}"
                            data-start="${item.start_date || '-'}"
                            data-end="${item.end_date || '-'}"
                            data-odometer="${item.odometer_reading ?? '-'}"
                            data-description="${item.description || '-'}"
                            data-image="${item.image || ''}"
                            data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        `;

                        if (item.status == 3) {
                            actionBtn += `
                            <button class="btn btn-secondary" disabled>
                                Completed
                            </button>
                            `;
                        } else {
                            actionBtn += `
                            <button class="btn btn-success doneBtn"
                                data-id="${item.id}"
                                data-toggle="tooltip" title="Mark as Done">
                                <i class="fas fa-check"></i> Done
                            </button>
                            `;
                        }

                        tbl.row.add([
                            i + 1,
                            machineryImage,
                            item.machinery_name ?? '-',
                            item.facility_name ?? '-',
                            typeBadge,
                            priorityBadge,
                            statusBadge,
                            costHtml,
                            item.start_date ?? '-',
                            actionBtn
                        ]);
                    });

                    tbl.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_MAINTENANCE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load maintenance records.", "error");
                }
            });
        }

        /* =========================
           LOAD DROPDOWNS
        ========================== */
        function loadDropdowns(callback) {
            $.when(
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "LIST_MACHINERY_TYPE",
                        limit: 1000
                    })
                }),
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "LIST_MACHINERY"
                    })
                }),
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-facility.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "LIST_FACILITY",
                        limit: 1000
                    })
                })
            ).done(function(typeRes, machineryRes, facilityRes) {

                window.facilities = facilityRes[0]?.data?.items || [];

                let types = typeRes[0]?.data?.result || [];
                let typeHtml = `<option value="">Select Machine Type</option>`;
                types.forEach(t => {
                    typeHtml += `<option value="${t.id}">${t.name}</option>`;
                });
                $("#machinery_type_id").html(typeHtml);
                reinitSelect2("#machinery_type_id");

                let facilities = facilityRes[0]?.data?.items || [];
                window.facilities = facilities;

                let facilityHtml = `<option value="">Select Facility</option>`;
                facilities.forEach(f => {
                    facilityHtml += `<option value="${f.id}">${f.name}</option>`;
                });
                $("#facility_id").html(facilityHtml);
                reinitSelect2("#facility_id");

                loadMachineries();

                if (callback) callback();

            }).fail(function() {
                Swal.fire("Error", "Failed to load dropdown data.", "error");
            });
        }

        function loadMachineries(id) {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MACHINERY",
                    type_id: id || ''
                }),
                success: function(res) {
                    let machines = res.data || [];
                    let html = `<option value="">Select Machinery</option>`;
                    machines.forEach(m => {
                        html += `<option value="${m.id}" data-branch="${m.branch_id || ''}">${m.name}</option>`;
                    });
                    $("#machinery_id").html(html);
                    reinitSelect2("#machinery_id");
                },
                error: function(xhr) {
                    console.error("LIST_MACHINERY failed:", xhr.responseText);
                }
            });
        }

        $("#machinery_type_id").on("change", function() {
            $("#machinery_id").val('').trigger('change');
            loadMachineries($(this).val());
        });

        $("#machinery_id").on("change", function() {
            let branch = $(this).find(":selected").data("branch");
            if (!branch) return;
            let fac = (window.facilities || []).find(f => String(f.branch_id) === String(branch));
            if (fac) {
                $("#facility_id").val(fac.id).trigger('change');
            }
        });

        /* =========================
           ADD MAINTENANCE
        ========================== */
        $("#btnAddMaintenance").on("click", function() {
            $("#machinery_type_id").val('').trigger('change');
            $("#machinery_id").val('').trigger('change');
            $("#facility_id").val('').trigger('change');
            $("#type").val('').trigger('change');
            $("#priority").val('').trigger('change');
            $("#start_date").val('');
            $("#end_date").val('');
            $("#labor_cost").val(0);
            $("#parts_cost").val(0);
            $("#odometer").val(0);
            $("#description").val('');

            loadDropdowns(function() {
                $("#addMaintenanceModal").modal("show");
            });
        });

        $("#saveMaintenanceBtn").click(function() {

            if (
                !$("#machinery_id").val() ||
                !$("#facility_id").val() ||
                !$("#type").val() ||
                !$("#priority").val()
            ) {
                Swal.fire("Error", "Please complete required fields", "error");
                return;
            }

            showLoader("Saving...");
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machinery-maintenance.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_MAINTENANCE",
                    machinery_id: $("#machinery_id").val(),
                    facility_id: $("#facility_id").val(),
                    type: $("#type").val(),
                    priority: $("#priority").val(),
                    start_date: $("#start_date").val(),
                    end_date: $("#end_date").val(),
                    labor_cost: $("#labor_cost").val(),
                    parts_cost: $("#parts_cost").val(),
                    odometer_reading: $("#odometer").val(),
                    description: $("#description").val()
                }),

                success: function(res) {
                    closeLoader();
                    Swal.fire({
                        icon: res.code == 0 ? "success" : "error",
                        title: res.code == 0 ? "Success" : "Error",
                        text: res.message
                    });

                    if (res.code == 0) {
                        $("#addMaintenanceModal").modal("hide");
                        loadMaintenance();
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("ADD_MAINTENANCE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to add maintenance.", "error");
                }
            });
        });

        /* =========================
           VIEW
        ========================== */
        $(document).on("click", ".viewBtn", function() {
            let machinery = $(this).data("machinery") || '—';
            let facility = $(this).data("facility") || '—';
            let type = $(this).data("type") || '—';
            let priority = $(this).data("priority") || '—';
            let statusHtml = $(this).data("status") || '—';
            let cost = $(this).data("cost") || '—';
            let start = $(this).data("start") || '—';
            let end = $(this).data("end") || '—';
            let odometer = $(this).data("odometer") || '—';
            let description = $(this).data("description") || '—';
            let image = $(this).data("image") || '';

            let statusBadge =
                statusHtml == 'Completed' ? '<span class="badge light badge-success">Completed</span>' :
                statusHtml == 'In Progress' ? '<span class="badge light badge-info">In Progress</span>' :
                '<span class="badge light badge-warning">Scheduled</span>';

            let priorityBadge =
                priority == 'Low' ? '<span class="badge light badge-secondary">Low</span>' :
                priority == 'Medium' ? '<span class="badge light badge-info">Medium</span>' :
                priority == 'High' ? '<span class="badge light badge-warning">High</span>' :
                '<span class="badge light badge-danger">Critical</span>';

            $('#viewModalTitle').text('Maintenance Details');
            $('#viewDetailName').text(machinery);
            $('#viewDetailFacility').text(facility);

            $('#viewDetailRows').html(`
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Facility</span>
                    <span class="fw-semibold text-end">${facility}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Type</span>
                    <span class="fw-semibold text-end">${type}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Priority</span>
                    <span class="fw-semibold text-end">${priorityBadge}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Status</span>
                    <span class="fw-semibold text-end">${statusBadge}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Total Cost</span>
                    <span class="fw-semibold text-end">${cost}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Start Date</span>
                    <span class="fw-semibold text-end">${start}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">End Date</span>
                    <span class="fw-semibold text-end">${end}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Odometer</span>
                    <span class="fw-semibold text-end">${odometer}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 small">
                    <span class="text-muted">Description</span>
                    <span class="fw-semibold text-end">${description}</span>
                </div>
            `);

            const $img = $('#viewMaintenanceImg');
            const $ph = $('#viewMaintenancePlaceholder');
            $img.removeClass('d-block').addClass('d-none').removeAttr('src');

            if (image) {
                const base = "<?= $baseURL ?>assets/images/machinery/";
                const preload = new Image();
                preload.onload = function() {
                    $ph.removeClass('d-flex').addClass('d-none');
                    $img.attr('src', base + image).removeClass('d-none').addClass('d-block');
                };
                preload.onerror = function() {
                    $ph.removeClass('d-none').addClass('d-flex');
                };
                preload.src = base + image;
            } else {
                $ph.removeClass('d-none').addClass('d-flex');
            }

            $("#viewMaintenanceModal").modal("show");
        });

        /* =========================
           DONE
        ========================== */
        $(document).on("click", ".doneBtn", function() {

            let id = $(this).data("id");
            let btn = $(this);

            Swal.fire({
                title: "Mark as done?",
                text: "This will complete the maintenance and set the machine back to Available.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, complete"
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.prop("disabled", true);

                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-machinery-maintenance.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "UPDATE_MAINTENANCE_STATUS",
                        id: id,
                        status: 3
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Success", res.message, "success");
                            loadMaintenance();
                        } else {
                            btn.prop("disabled", false);
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        closeLoader();
                        btn.prop("disabled", false);
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            });
        });

    });
</script>

<?= endSection() ?>
