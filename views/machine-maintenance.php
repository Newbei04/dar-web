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
                    <div class="form-group col-md-6">
                        <label>Machinery <span class="text-danger">*</span></label>
                        <select class="form-control" id="machinery_id"></select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Facility <span class="text-danger">*</span></label>
                        <select class="form-control" id="facility_id"></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="type">
                            <option value="">Select Type</option>
                            <option value="1">Preventive</option>
                            <option value="2">Corrective</option>
                            <option value="4">Emergency</option>
                            <option value="5">Inspection</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select class="form-control" id="priority">
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Maintenance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="35%" class="text-muted">Machinery</th>
                        <td id="view_machinery">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Facility</th>
                        <td id="view_facility">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Type</th>
                        <td id="view_type">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Priority</th>
                        <td id="view_priority">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td id="view_status">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Cost</th>
                        <td id="view_cost">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Start Date</th>
                        <td id="view_start">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">End Date</th>
                        <td id="view_end">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Odometer</th>
                        <td id="view_odometer">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Description</th>
                        <td id="view_description">-</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
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
                    if (res.code != 1) {
                        Swal.fire("Error", res.message || "Failed to load maintenance records.", "error");
                        return;
                    }

                    let data = res.data || [];
                    if (data.length === 0) {
                        tbl.draw(false);
                        return;
                    }

                    data.forEach(function(item, i) {

                        let machineryImage = item.image ?
                            `<img src="<?= $baseURL ?>assets/images/machinery/${item.image}" alt="Machine" style="width:50px;height:50px;border-radius:10%;object-fit:cover;">` :
                            `<div style="width:50px;height:50px;border-radius:10%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted"></i></div>`;

                        let statusBadge =
                            item.status_label == 'Completed' ? '<span class="badge light badge-success">Completed</span>' :
                            item.status_label == 'In Progress' ? '<span class="badge light badge-info">In Progress</span>' :
                            '<span class="badge light badge-warning">Scheduled</span>';

                        let priorityBadge =
                            item.priority_label == 'Low' ? '<span class="badge light badge-secondary">Low</span>' :
                            item.priority_label == 'Medium' ? '<span class="badge light badge-info">Medium</span>' :
                            item.priority_label == 'High' ? '<span class="badge light badge-warning">High</span>' :
                            '<span class="badge light badge-danger">Critical</span>';

                        let typeBadge = '<span class="badge light badge-primary">' + (item.type_label || '-') + '</span>';

                        let cost = parseFloat(item.total_cost ?? (parseFloat(item.labor_cost) + parseFloat(item.parts_cost)));
                        let costHtml = isNaN(cost) ? '₱0.00' : '₱' + cost.toFixed(2);

                        let actionBtn = `
                        <button class="btn btn-info mr-2 viewBtn"
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
            ).done(function(machineryRes, facilityRes) {

                let machines = machineryRes[0]?.data || [];
                let machineryHtml = `<option value="">Select Machinery</option>`;
                machines.forEach(m => {
                    machineryHtml += `<option value="${m.id}">${m.name}</option>`;
                });
                $("#machinery_id").html(machineryHtml);

                let facilities = facilityRes[0]?.data?.items || [];
                let facilityHtml = `<option value="">Select Facility</option>`;
                facilities.forEach(f => {
                    facilityHtml += `<option value="${f.id}">${f.name}</option>`;
                });
                $("#facility_id").html(facilityHtml);

                if (callback) callback();

            }).fail(function() {
                Swal.fire("Error", "Failed to load dropdown data.", "error");
            });
        }

        /* =========================
           ADD MAINTENANCE
        ========================== */
        $("#btnAddMaintenance").on("click", function() {
            $("#machinery_id").val('');
            $("#facility_id").val('');
            $("#type").val('');
            $("#priority").val('');
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
                        icon: res.code == 1 ? "success" : "error",
                        title: res.code == 1 ? "Success" : "Error",
                        text: res.message
                    });

                    if (res.code == 1) {
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
            $("#view_machinery").text($(this).data("machinery") || '-');
            $("#view_facility").text($(this).data("facility") || '-');
            $("#view_type").text($(this).data("type") || '-');
            $("#view_priority").text($(this).data("priority") || '-');
            $("#view_status").text($(this).data("status") || '-');
            $("#view_cost").text($(this).data("cost") || '-');
            $("#view_start").text($(this).data("start") || '-');
            $("#view_end").text($(this).data("end") || '-');
            $("#view_odometer").text($(this).data("odometer") || '-');
            $("#view_description").text($(this).data("description") || '-');
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
                        if (res.code == 1) {
                            Swal.fire("Success", res.message, "success");
                            loadMaintenance();
                        } else {
                            btn.prop("disabled", false);
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        btn.prop("disabled", false);
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            });
        });

    });
</script>

<?= endSection() ?>
