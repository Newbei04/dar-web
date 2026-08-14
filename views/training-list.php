<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css">
<style>
    .tr-thumb {
        width: 46px;
        height: 46px;
        border-radius: 8px;
        flex-shrink: 0;
        overflow: hidden;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 18px;
    }

    .tr-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tr-title {
        font-weight: 600;
        color: #212529;
        line-height: 1.3;
    }

    .tr-sub {
        font-size: 11px;
        color: #adb5bd;
    }

    .badge-type {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
        background: #e7f1ff;
        color: #0d6efd;
    }

    .count-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 20px;
        background: #f1f5f9;
        color: #475569;
    }

    .count-pill i {
        font-size: 10px;
        color: #94a3b8;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Training</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Training List</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Training List</h4>
                    <a href="<?= $baseURL ?>add-training" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Training
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblTraining" class="display min-w850">
                            <thead class="fw-bold">
                                <tr>
                                    <th>No.</th>
                                    <th>Training</th>
                                    <th>Reference No</th>
                                    <th>Program Type</th>
                                    <th>Schedule</th>
                                    <th>Enrolled</th>
                                    <th>Programs</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PROGRAM MODAL -->
<div class="modal fade" id="programModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Training Programs</h5>
                    <small class="text-muted" id="progTrainingLabel">—</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- ADD / EDIT PROGRAM TOOLBAR -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0"><i class="fas fa-list me-1 text-muted"></i>Program Schedule</h6>
                    <button type="button" class="btn btn-primary btn-sm" id="addProgramToggleBtn">
                        <i class="fas fa-plus me-1"></i> Add Program
                    </button>
                </div>

                <!-- ADD / EDIT PROGRAM FORM (collapsible) -->
                <div class="collapse mb-3" id="programFormWrap">
                    <div class="border rounded p-3" style="background:#f8f9fa;">
                        <form id="programForm">
                            <input type="hidden" id="prog_id">
                            <input type="hidden" id="prog_training_id">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Topic <span class="text-danger">*</span></label>
                                    <input type="text" id="prog_topic" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Speaker</label>
                                    <input type="text" id="prog_speaker" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Time Start</label>
                                    <input type="time" id="prog_time_start" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Time End</label>
                                    <input type="time" id="prog_time_end" class="form-control">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-secondary btn-sm me-2" id="progCancelEdit" style="display:none;">Cancel Edit</button>
                                <button type="button" class="btn btn-light btn-sm me-2" id="progCancelAdd">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-sm" id="progSubmitBtn">Add Program</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- PROGRAM LIST -->
                <table class="table table-sm table-bordered" id="tblProgram">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Topic</th>
                            <th>Speaker</th>
                            <th>Time Start</th>
                            <th>Time End</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- VIEW TRAINING MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Training Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width:30%">Reference No</th>
                        <td id="view_reference_no"></td>
                    </tr>
                    <tr>
                        <th>Accreditation No</th>
                        <td id="view_accreditation_no"></td>
                    </tr>
                    <tr>
                        <th>Accreditation Date</th>
                        <td id="view_accreditation_date"></td>
                    </tr>
                    <tr>
                        <th>Title</th>
                        <td id="view_title"></td>
                    </tr>
                    <tr>
                        <th>Summary</th>
                        <td id="view_summary"></td>
                    </tr>
                    <tr>
                        <th>Objectives</th>
                        <td id="view_objectives"></td>
                    </tr>
                    <tr>
                        <th>Program Type</th>
                        <td id="view_program_type"></td>
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td id="view_start_at"></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td id="view_end_at"></td>
                    </tr>
                    <tr>
                        <th>Region</th>
                        <td id="view_region"></td>
                    </tr>
                    <tr>
                        <th>Province</th>
                        <td id="view_province"></td>
                    </tr>
                    <tr>
                        <th>City / Municipality</th>
                        <td id="view_city"></td>
                    </tr>
                    <tr>
                        <th>Barangay</th>
                        <td id="view_barangay"></td>
                    </tr>
                    <tr>
                        <th>Street</th>
                        <td id="view_street"></td>
                    </tr>
                    <tr>
                        <th>Promotional Image</th>
                        <td id="view_image"></td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td id="view_created_at"></td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td id="view_updated_at"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT TRAINING MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="row g-3">
                    <div class="form-group col-md-6">
                        <label>Reference No</label>
                        <input type="text" id="edit_reference_no" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Accreditation Number</label>
                        <input type="text" id="edit_accreditation_no" class="form-control">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6">
                        <label>Accreditation Date</label>
                        <input type="date" id="edit_accreditation_date" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Program Type</label>
                        <select id="edit_program_type" class="form-control">
                            <option value="">Select Program Type</option>
                            <option value="0">OTHER</option>
                            <option value="1">ONSITE TRAINING</option>
                            <option value="2">VIRTUAL LEARNING</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Title <span class="text-danger">*</span></label>
                    <input type="text" id="edit_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Summary</label>
                    <textarea id="edit_summary" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Objectives</label>
                    <textarea id="edit_objectives" class="form-control" rows="2"></textarea>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6">
                        <label>Start Date</label>
                        <input type="date" id="edit_start_at" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>End Date</label>
                        <input type="date" id="edit_end_at" class="form-control">
                    </div>
                </div>
                <h6 class="text-uppercase text-muted mb-3"><small>Address</small></h6>
                <div class="row g-3">
                    <div class="form-group col-md-6">
                        <label>Region</label>
                        <select class="form-control select2" id="edit_region">
                            <option value="">Select Region</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="edit_provinceField">
                        <label>Province</label>
                        <select class="form-control select2" id="edit_province" disabled>
                            <option value="">Select Province</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6">
                        <label>City / Municipality</label>
                        <select class="form-control select2" id="edit_city" disabled>
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="edit_submuniField" style="display:none;">
                        <label>District</label>
                        <select class="form-control select2" id="edit_submuni" disabled>
                            <option value="">Select District</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Barangay</label>
                        <select class="form-control select2" id="edit_barangay" disabled>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Street / Landmark</label>
                    <textarea id="edit_street" class="form-control" rows="2" placeholder="House no., street, landmark..."></textarea>
                </div>
                <hr>
                <div class="form-group">
                    <label>Current Promotional Image</label>
                    <div id="edit_current_image"></div>
                </div>
                <div class="form-group">
                    <label>Change Promotional Image</label>
                    <input type="file" id="edit_promotional_image" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Current Discussion File</label>
                    <div id="edit_current_discussion"></div>
                </div>
                <div class="form-group">
                    <label>Change Discussion File</label>
                    <input type="file" id="edit_discussion_file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="updateBtn">Update</button>
            </div>
        </div>
    </div>
</div>

<?= endSection() ?>
<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script>
    const MANILA_CODE = "133900000";
    const NCR_CODE = "130000000";
    const API_URL = "<?= $baseURL ?>controller/ctrl-location.php";
    let cascading = false;

    $(document).ready(function() {

        $('.select2').select2();

        const API = "<?= $baseURL ?>controller/ctrl-training.php";

        let tbl = $('#tblTraining').DataTable({
            responsive: true,
            order: [],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        // Close the row action dropdown after an item is clicked
        $(document).on('click', '#tblTraining .dropdown-item', function() {
            let toggle = $(this).closest('.dropdown').find('[data-bs-toggle="dropdown"]')[0];
            if (toggle && window.bootstrap && bootstrap.Dropdown) {
                bootstrap.Dropdown.getOrCreateInstance(toggle).hide();
            }
        });

        loadData();
        getRegionList();

        // ================= DATA TABLE =================

        function loadData() {
            tbl.clear().draw();
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_TRAINING"
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let data = res.data || [];
                        data.forEach(function(item, i) {
                            let programType = {
                                0: 'OTHER',
                                1: 'ONSITE TRAINING',
                                2: 'VIRTUAL LEARNING'
                            };
                            let approvalBadge = item.approval_status == 1 ? '<span class="badge light badge-success">Approved</span>' :
                                item.approval_status == 2 ? '<span class="badge light badge-danger">Rejected</span>' :
                                '<span class="badge light badge-warning">Pending</span>';
                            let statusBadge = item.status == 1 ? '<span class="badge light badge-primary ms-1">Started</span>' :
                                item.status == 2 ? '<span class="badge light badge-secondary ms-1">Closed</span>' :
                                '<span class="badge light badge-light ms-1">Not Started</span>';
                            let isAdmin = <?= isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1 ? 'true' : 'false' ?>;
                            let escTitle = (item.title || '').replace(/"/g, '&quot;');

                            let base = "<?= $baseURL ?>";
                            let thumbHtml = '<i class="fas fa-chalkboard-user"></i>';
                            if (item.promotional_image) {
                                thumbHtml = `<img src="${base}assets/images/training/${item.promotional_image}" onerror="this.outerHTML='<i class=fas fa-chalkboard-user></i>'">`;
                            }

                            let schedule = item.start_at && item.end_at ?
                                `${fmtDate(item.start_at)} &rarr; ${fmtDate(item.end_at)}` :
                                (item.start_at ? fmtDate(item.start_at) : '-');

                            let approvalItems = '';
                            if (isAdmin && item.approval_status == 0) {
                                approvalItems = `
                                    <a class="dropdown-item approveBtn" href="javascript:void(0);" data-id="${item.id}" data-title="${escTitle}">Approve</a>
                                    <a class="dropdown-item rejectBtn" href="javascript:void(0);" data-id="${item.id}" data-title="${escTitle}">Reject</a>`;
                            }
                            let startItem = '';
                            if (isAdmin && item.approval_status == 1 && item.status == 0) {
                                startItem = `<a class="dropdown-item startBtn" href="javascript:void(0);" data-id="${item.id}" data-title="${escTitle}">Start Training</a>`;
                            }
                            let closeItem = '';
                            if (isAdmin && item.approval_status == 1 && item.status == 1) {
                                closeItem = `<a class="dropdown-item closeBtn" href="javascript:void(0);" data-id="${item.id}" data-title="${escTitle}">Close Training</a>`;
                            }
                            let statusItems = approvalItems + startItem + closeItem;
                            let actionDivider = statusItems ? '<div class="dropdown-divider"></div>' : '';

                            tbl.row.add([
                                i + 1,
                                `<div class="d-flex align-items-center gap-2">
                                    <div class="tr-thumb">${thumbHtml}</div>
                                    <div>
                                        <div class="tr-title">${escTitle || '-'}</div>
                                        <div class="tr-sub">${item.created_at ?? ''}</div>
                                    </div>
                                </div>`,
                                `<span style="font-family:monospace;font-size:12px;">${item.reference_no ?? '-'}</span>`,
                                `<span class="badge-type">${programType[item.program_type] ?? '-'}</span>`,
                                schedule,
                                `<span class="count-pill"><i class="fas fa-users"></i> ${item.enrolled_count ?? 0}</span>`,
                                `<span class="count-pill"><i class="fas fa-list"></i> ${item.program_count ?? 0}</span>`,
                                approvalBadge + statusBadge,
                                `<div class="dropdown ms-auto text-end c-pointer">
                                    <div class="btn-link" data-bs-toggle="dropdown">
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
                                        ${statusItems}
                                        ${actionDivider}
                                        <a class="dropdown-item programBtn" href="javascript:void(0);" data-id="${item.id}" data-status="${item.status}">Programs</a>
                                        <a class="dropdown-item viewBtn" href="javascript:void(0);" data-id="${item.id}">View</a>
                                        <a class="dropdown-item editBtn" href="javascript:void(0);" data-id="${item.id}">Edit</a>
                                        <a class="dropdown-item deleteBtn" href="javascript:void(0);" data-id="${item.id}">Delete</a>
                                    </div>
                                </div>`
                            ]);
                        });
                        tbl.draw(false);
                    }
                }
            });
        }

        function fmtDate(d) {
            if (!d) return '-';
            const x = new Date(d);
            if (isNaN(x.getTime())) return d;
            return x.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // ================= LOCATION CASCADE =================

        $("#edit_region").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            $("#edit_submuniField").hide();
            if (!val) return;
            if (val === NCR_CODE) {
                $("#edit_province").val("").prop("disabled", true);
                $("#edit_provinceField").hide();
                getNCRCityList();
            } else {
                $("#edit_provinceField").show();
                getProvinceList(val);
            }
        });

        $("#edit_province").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            if (!val) return;
            getCityList(val);
        });

        $("#edit_city").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            if (!val) return;
            if (val === MANILA_CODE) {
                $("#edit_submuniField").show();
                getDistrictList();
            } else {
                $("#edit_submuniField").hide();
                getBarangayList(val);
            }
        });

        $("#edit_submuni").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            if (!val) return;
            getBarangayByDistrict(val);
        });

        function getRegionList(callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "region"
                }),
                success: function(res) {
                    let d = $("#edit_region");
                    d.html('<option value="">Select Region</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        }

        function getProvinceList(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "province",
                    code: code
                }),
                success: function(res) {
                    let d = $("#edit_province");
                    d.prop("disabled", false).html('<option value="">Select Province</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        }

        function getCityList(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "city",
                    code: code
                }),
                success: function(res) {
                    let d = $("#edit_city");
                    d.prop("disabled", false).html('<option value="">Select City</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        }

        function getNCRCityList(callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ncr_city"
                }),
                success: function(res) {
                    let d = $("#edit_city");
                    d.prop("disabled", false).html('<option value="">Select City</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        }

        function getDistrictList(callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "district"
                }),
                success: function(res) {
                    let d = $("#edit_submuni");
                    d.prop("disabled", false).html('<option value="">Select District</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        }

        function getBarangayList(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "barangay",
                    code: code
                }),
                success: function(res) {
                    let d = $("#edit_barangay");
                    d.prop("disabled", false).html('<option value="">Select Barangay</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        }

        function getBarangayByDistrict(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "barangay_district",
                    code: code
                }),
                success: function(res) {
                    let d = $("#edit_barangay");
                    d.prop("disabled", false).html('<option value="">Select Barangay</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    if (callback) callback();
                }
            });
        }

        // ================= PROGRAM MODAL =================

        let activeTrainingId = null;
        let programCache = {};

        function resetProgramForm() {
            $('#programForm')[0].reset();
            $('#prog_id').val('');
            $('#prog_training_id').val(activeTrainingId);
            $('#progSubmitBtn').text("Add Program");
            $('#progCancelEdit').hide();
        }

        $(document).on('click', '.programBtn', function() {
            let id = $(this).data('id');
            let status = parseInt($(this).data('status') || 0);
            activeTrainingId = id;
            $('#prog_training_id').val(id);
            resetProgramForm();
            $('#progTrainingLabel').text('Training ID: ' + id);
            $('#programFormWrap').collapse('hide');
            $('#addProgramToggleBtn').toggle(status == 0);
            loadPrograms(id);
            $('#programModal').modal('show');
        });

        // Add Program toggle (opens the collapsible form)
        $('#addProgramToggleBtn').on('click', function() {
            resetProgramForm();
            $('#programFormWrap').collapse('show');
            setTimeout(function() {
                $('#prog_topic').trigger('focus');
            }, 250);
        });

        function loadPrograms(training_id) {
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_TRAINING_PROGRAMS",
                    training_id: training_id
                }),
                success: function(res) {
                    let tbody = $('#tblProgram tbody');
                    tbody.empty();
                    programCache = {};
                    if (res.code == 0 && res.data.length > 0) {
                        res.data.forEach(function(p, i) {
                            programCache[p.id] = p;
                            tbody.append(`
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${p.topic}</td>
                                    <td>${p.speaker ?? '-'}</td>
                                    <td>${p.time_start ?? '-'}</td>
                                    <td>${p.time_end ?? '-'}</td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-sm btn-primary editProgramBtn me-1" data-id="${p.id}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger deleteProgramBtn" data-id="${p.id}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append(`<tr><td colspan="6" class="text-center text-muted">No programs yet</td></tr>`);
                    }
                }
            });
        }

        // ================= ADD / EDIT PROGRAM =================

        $(document).on('click', '.editProgramBtn', function() {
            let p = programCache[$(this).data('id')];
            if (!p) return;
            $('#prog_id').val(p.id);
            $('#prog_topic').val(p.topic);
            $('#prog_speaker').val(p.speaker ?? '');
            $('#prog_time_start').val(p.time_start ? p.time_start.slice(0, 5) : '');
            $('#prog_time_end').val(p.time_end ? p.time_end.slice(0, 5) : '');
            $('#progSubmitBtn').text("Update Program");
            $('#progCancelEdit').show();
            $('#programFormWrap').collapse('show');
        });

        $('#progCancelEdit').click(function() {
            resetProgramForm();
        });

        $('#progCancelAdd').click(function() {
            resetProgramForm();
            $('#programFormWrap').collapse('hide');
        });

        $('#programForm').submit(function(e) {
            e.preventDefault();
            let training_id = $('#prog_training_id').val();
            let prog_id = $('#prog_id').val();
            let isEdit = prog_id ? true : false;
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: isEdit ? "EDIT_PROGRAM" : "ADD_PROGRAM",
                    id: prog_id,
                    training_id: training_id,
                    topic: $('#prog_topic').val(),
                    speaker: $('#prog_speaker').val(),
                    time_start: $('#prog_time_start').val(),
                    time_end: $('#prog_time_end').val()
                }),
                beforeSend: function() {
                    $("button[type='submit']").prop("disabled", true).text("Saving...");
                },
                success: function(res) {
                    if (res.code == 0) {
                        resetProgramForm();
                        $('#prog_training_id').val(training_id);
                        $('#programFormWrap').collapse('hide');
                        loadPrograms(training_id);
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                complete: function() {
                    $("button[type='submit']").prop("disabled", false).text(isEdit ? "Update Program" : "Add Program");
                }
            });
        });

        // ================= DELETE PROGRAM =================

        $(document).on('click', '.deleteProgramBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: "Delete this program?",
                text: "This program will be deleted permanently",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_PROGRAM",
                            id: id
                        }),
                        success: function(res) {
                            if (res.code == 0) {
                                Swal.fire("Deleted!", res.message, "success");
                                loadPrograms(activeTrainingId);
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        }
                    });
                }
            });
        });

        // ================= VIEW =================

        $(document).on('click', '.viewBtn', function() {
            let id = $(this).data('id');
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_TRAINING",
                    id: id
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let d = res.data;
                        let base = "<?= $baseURL ?>";
                        $('#view_reference_no').text(d.reference_no ?? '-');
                        $('#view_accreditation_no').text(d.accreditation_no ?? '-');
                        $('#view_accreditation_date').text(d.accreditation_date ?? '-');
                        $('#view_title').text(d.title ?? '-');
                        $('#view_summary').text(d.summary ?? '-');
                        $('#view_objectives').text(d.objectives ?? '-');
                        let ptype = {
                            0: 'OTHER',
                            1: 'ONSITE TRAINING',
                            2: 'VIRTUAL LEARNING'
                        };
                        $('#view_program_type').text(ptype[d.program_type] ?? d.program_type ?? '-');
                        $('#view_start_at').text(d.start_at ?? '-');
                        $('#view_end_at').text(d.end_at ?? '-');
                        $('#view_street').text(d.street ?? '-');
                        $('#view_region').text(d.region_name ?? d.region_id ?? '-');
                        $('#view_province').text(d.province_name ?? d.province_id ?? '-');
                        $('#view_city').text(d.city_name ?? d.city_id ?? '-');
                        $('#view_barangay').text(d.barangay_name ?? d.barangay_id ?? '-');
                        $('#view_image').html(d.promotional_image ?
                            `<img src="${base}assets/images/training/${d.promotional_image}" width="120" style="border-radius:4px;">` :
                            '-');
                        $('#view_created_at').text(d.created_at ?? '-');
                        $('#view_updated_at').text(d.updated_at ?? '-');
                        $('#viewModal').modal('show');
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                }
            });
        });

        // ================= EDIT =================

        function setEditRegion(val, callback) {
            if (!val) {
                if (callback) callback();
                return;
            }
            $("#edit_region").val(val);
            $("#edit_submuniField").hide();
            if (val == NCR_CODE) {
                $("#edit_provinceField").hide();
                $("#edit_province").val("");
                getNCRCityList(callback);
            } else {
                $("#edit_provinceField").show();
                getProvinceList(val, callback);
            }
        }

        function setEditProvince(val, callback) {
            if ($("#edit_region").val() == NCR_CODE || !val) {
                if (callback) callback();
                return;
            }
            $("#edit_province").val(val);
            getCityList(val, callback);
        }

        function setEditCity(val, callback) {
            if (!val) {
                if (callback) callback();
                return;
            }
            $("#edit_city").val(val);
            if (val == MANILA_CODE) {
                $("#edit_submuniField").show();
                getDistrictList(callback);
            } else {
                $("#edit_submuniField").hide();
                getBarangayList(val, callback);
            }
        }

        function setEditSubmuni(val, callback) {
            if (!val) {
                if (callback) callback();
                return;
            }
            $("#edit_submuni").val(val);
            getBarangayByDistrict(val, callback);
        }

        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_TRAINING",
                    id: id
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let d = res.data;
                        let base = "<?= $baseURL ?>";
                        $('#edit_id').val(d.id);
                        $('#edit_reference_no').val(d.reference_no);
                        $('#edit_accreditation_no').val(d.accreditation_no);
                        $('#edit_accreditation_date').val(d.accreditation_date ? d.accreditation_date.slice(0, 10) : '');
                        $('#edit_title').val(d.title);
                        $('#edit_summary').val(d.summary);
                        $('#edit_objectives').val(d.objectives);
                        $('#edit_start_at').val(d.start_at ? d.start_at.slice(0, 10) : '');
                        $('#edit_end_at').val(d.end_at ? d.end_at.slice(0, 10) : '');
                        $('#edit_program_type').val(d.program_type);
                        $('#edit_street').val(d.street);
                        $('#edit_current_image').html(d.promotional_image ?
                            `<img src="${base}assets/images/training/${d.promotional_image}" width="100" class="mb-2">` :
                            '<span class="text-muted">No image</span>');
                        $('#edit_current_discussion').html(d.discussion ?
                            `<a href="${base}assets/files/training/${d.discussion}" target="_blank" class="btn btn-sm btn-outline-info">View Current File</a>` :
                            '<span class="text-muted">No file</span>');

                        // Pre-populate address cascade
                        cascading = true;
                        getRegionList(function() {
                            setEditRegion(d.region_id, function() {
                                setEditProvince(d.province_id, function() {
                                    setEditCity(d.city_id, function() {
                                        setEditSubmuni(d.district_id, function() {
                                            $("#edit_barangay").val(d.barangay_id);
                                            $("#edit_region, #edit_province, #edit_city, #edit_submuni, #edit_barangay").trigger('change.select2');
                                            cascading = false;
                                        });
                                    });
                                });
                            });
                        });

                        $('#editModal').modal('show');
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                }
            });
        });

        // ================= UPDATE =================

        $('#updateBtn').click(function() {
            let fd = new FormData();
            fd.append("trans", "EDIT_TRAINING");
            fd.append("id", $('#edit_id').val());
            fd.append("accreditation_no", $('#edit_accreditation_no').val());
            fd.append("accreditation_date", $('#edit_accreditation_date').val());
            fd.append("title", $('#edit_title').val());
            fd.append("summary", $('#edit_summary').val());
            fd.append("objectives", $('#edit_objectives').val());
            fd.append("start_at", $('#edit_start_at').val());
            fd.append("end_at", $('#edit_end_at').val());
            fd.append("program_type", $('#edit_program_type').val());
            fd.append("street", $('#edit_street').val());
            fd.append("barangay_id", $('#edit_barangay').val());
            fd.append("city_id", $('#edit_city').val());
            fd.append("province_id", $('#edit_province').val());
            fd.append("region_id", $('#edit_region').val());

            let imgFile = $('#edit_promotional_image')[0].files[0];
            if (imgFile) fd.append("promotional_image", imgFile);
            let docFile = $('#edit_discussion_file')[0].files[0];
            if (docFile) fd.append("discussion_file", docFile);

            $.ajax({
                url: API,
                type: "POST",
                data: fd,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    $('#updateBtn').prop("disabled", true).text("Updating...");
                },
                success: function(res) {
                    if (res.code == 0) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: res.message
                        });
                        $('#editModal').modal('hide');
                        loadData();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: res.message
                        });
                    }
                },
                complete: function() {
                    $('#updateBtn').prop("disabled", false).text("Update");
                }
            });
        });

        // ================= DELETE =================

        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: "Delete this training?",
                text: "This training will be deleted permanently",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_TRAINING",
                            id: id
                        }),
                        success: function(res) {
                            if (res.code == 0) {
                                Swal.fire("Deleted!", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        }
                    });
                }
            });
        });

        // ================= APPROVE =================
        $(document).on('click', '.approveBtn', function() {
            let id = $(this).data('id');
            let title = $(this).data('title');
            Swal.fire({
                title: 'Approve this training?',
                html: `<strong>${title}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Yes, Approve'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "APPROVE_TRAINING",
                            id: id,
                            reason: ""
                        }),
                        success: function(res) {
                            if (res.code == 0) {
                                Swal.fire("Approved!", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        }
                    });
                }
            });
        });

        // ================= REJECT =================
        $(document).on('click', '.rejectBtn', function() {
            let id = $(this).data('id');
            let title = $(this).data('title');
            Swal.fire({
                title: 'Reject this training?',
                html: `<strong>${title}</strong>`,
                input: 'text',
                inputPlaceholder: 'Reason (optional)',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Reject'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "REJECT_TRAINING",
                            id: id,
                            reason: result.value || ""
                        }),
                        success: function(res) {
                            if (res.code == 0) {
                                Swal.fire("Rejected!", res.message, "info");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        }
                    });
                }
            });
        });

        // ================= START TRAINING =================
        $(document).on('click', '.startBtn', function() {
            let id = $(this).data('id');
            let title = $(this).data('title');
            Swal.fire({
                title: 'Start this training?',
                html: `<strong>${title}</strong>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                confirmButtonText: 'Yes, Start'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "START_TRAINING",
                            id: id
                        }),
                        success: function(res) {
                            if (res.code == 0) {
                                Swal.fire("Started!", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        }
                    });
                }
            });
        });

        // ================= CLOSE TRAINING =================
        $(document).on('click', '.closeBtn', function() {
            let id = $(this).data('id');
            let title = $(this).data('title');
            Swal.fire({
                title: 'Close this training?',
                html: `<strong>${title}</strong><br><small>This will lock all attendance editing.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Close'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "CLOSE_TRAINING",
                            id: id
                        }),
                        success: function(res) {
                            if (res.code == 0) {
                                Swal.fire("Closed!", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        }
                    });
                }
            });
        });

    });
</script>
<?= endSection() ?>