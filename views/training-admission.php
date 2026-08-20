<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Training</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Training Admission</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- TRAINING TABLE -->
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Training Admissions</h4>
                    <!-- <button class="btn btn-primary btn-sm" id="addAdmissionBtn">
                        <i class="fas fa-plus me-1"></i> Add Admission
                    </button> -->
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblTraining" class="display responsive nowrap w-100">
                            <thead class="fw-bold">
                                <tr>
                                    <th>No.</th>
                                    <th>Title</th>
                                    <th>Reference No</th>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Enrolled</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <!-- ENROLLED BENEFICIARIES (shown after selecting a training) -->
            <div id="enrolledSection" style="display:none;">
                <div class="card mt-4">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <h4 class="card-title me-auto mb-2 mb-md-0" id="enrolledTitle">Enrolled Beneficiaries</h4>
                        <button class="btn btn-primary btn-sm addEnrollBtn">
                            <i class="fas fa-plus me-1"></i> Add Enrollment
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tblEnrolled" class="display responsive nowrap w-100">
                                <thead class="fw-bold">
                                    <tr>
                                        <th>No.</th>
                                        <th>Beneficiary</th>
                                        <th>Document No</th>
                                        <th>Registration Date</th>
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
</div>

<!-- ADD MODAL (checkbox table) -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Enroll Beneficiaries</h5>
                    <small class="text-muted">Select a training then check beneficiaries to enroll</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="enroll_training_id">
                <div class="form-group mb-3">
                    <label>Training</label>
                    <!-- <select id="enroll_training_select" class="form-control">
                        <option value="">Select Training</option>
                    </select> -->
                    <p id="enroll_training_display" class="form-control-static mb-0" style="display:none;"></p>
                </div>
                <input type="text" id="searchBeneficiary" class="form-control mb-3" placeholder="Search beneficiary...">
                <div id="beneficiaryList" style="max-height: 350px; overflow-y: auto;">
                    <p class="text-muted text-center">Select a training first</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="confirmAddBtn">Enroll Selected</button>
            </div>
        </div>
    </div>
</div>

<!-- STATUS MODAL -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="update_admission_id">
                <p id="statusBenName" class="mb-2"></p>
                <div class="form-group mb-0">
                    <label>Status</label>
                    <select class="form-control" id="update_status">
                        <option value="0">Pending</option>
                        <option value="1">Present</option>
                        <option value="2">Absent</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary btn-sm" id="saveStatusBtn">
                    <i class="fas fa-check me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<?= endSection() ?>

<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script>
    function formatDocNum(d) {
        if (!d || d.length !== 9) return d || '-';
        return d.substring(0, 2) + '-' + d.substring(2, 8) + '-' + d.substring(8, 9);
    }

    $(document).ready(function() {

        const API = "<?= $baseURL ?>controller/ctrl-training.php";
        let tblTraining = $('#tblTraining').DataTable({
            responsive: true,
            order: [],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });
        let tblEnrolled = $('#tblEnrolled').DataTable({
            responsive: true,
            order: [],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });
        let currentTrainingId = null;
        let currentTrainingStatus = null;

        loadTrainings();
        loadTrainingOptions();

        // ================= TRAINING OPTIONS (for Add modal) =================
        function loadTrainingOptions() {
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_TRAINING_ADMISSIONS"
                }),
                success: function(res) {
                    let html = '<option value="">Select Training</option>';
                    if (res.code == 0) {
                        (res.data || []).forEach(function(item) {
                            html += `<option value="${item.id}">${item.reference_no ?? ''} - ${item.title ?? ''}</option>`;
                        });
                    }
                    $('#enroll_training_select').html(html);
                }
            });
        }

        // ================= TRAINING LIST =================
        function loadTrainings() {
            tblTraining.clear().draw();

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_TRAINING_ADMISSIONS"
                }),
                success: function(res) {
                    if (res.code == 0) {
                        (res.data || []).forEach(function(item, i) {
                            let statusBadge = item.status == 1 ?
                                '<span class="badge light badge-success">Started</span>' :
                                (item.status == 2 ?
                                    '<span class="badge light badge-danger">Closed</span>' :
                                    '<span class="badge light badge-warning">Pending</span>');

                            let startStr = item.start_at ?
                                new Date(item.start_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                }) :
                                '-';
                            let endStr = item.end_at ?
                                new Date(item.end_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                }) :
                                '-';

                            tblTraining.row.add([
                                i + 1,
                                item.title ?? '-',
                                item.reference_no ?? '-',
                                item.program_type_label ?? '-',
                                startStr,
                                endStr,
                                statusBadge,
                                `<span class="badge light badge-info">${item.admission_count}</span>`,
                                `<button class="btn btn-outline-primary btn-sm viewEnrolledBtn" data-id="${item.id}" data-status="${item.status}" data-title="${item.title ?? ''}">
                                    <i class="fas fa-eye"></i> View
                                </button>`
                            ]);
                        });
                        tblTraining.draw(false);
                    }
                }
            });
        }

        // ================= ADD ADMISSION (header button) =================
        $('#addAdmissionBtn').click(function() {
            $('#enroll_training_id').val('');
            $('#enroll_training_select').val('').prop('disabled', false).show();
            $('#enroll_training_display').hide().text('');
            $('#beneficiaryList').html('<p class="text-muted text-center">Select a training first</p>');
            $('#searchBeneficiary').val('');
            $('#addModal').modal('show');
        });

        // When a training is picked in the Add modal, load its beneficiaries
        $('#enroll_training_select').on('change', function() {
            let trainingId = $(this).val();
            $('#enroll_training_id').val(trainingId);
            $('#searchBeneficiary').val('');
            if (trainingId) {
                loadBeneficiaryList();
            } else {
                $('#beneficiaryList').html('<p class="text-muted text-center">Select a training first</p>');
            }
        });

        // ================= VIEW ENROLLED =================
        $(document).on('click', '.viewEnrolledBtn', function() {
            currentTrainingId = $(this).data('id');
            currentTrainingStatus = $(this).data('status');
            let title = $(this).data('title');
            $('#enrolledTitle').text('Enrolled Beneficiaries - ' + title);
            $('.addEnrollBtn').data('title', title);

            if (currentTrainingStatus == 2) {
                $('.addEnrollBtn').hide();
            } else {
                $('.addEnrollBtn').show();
            }

            $('#enrolledSection').show();
            $('#enrolledSection')[0].scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            loadEnrolled();
        });

        function loadEnrolled() {
            tblEnrolled.clear().draw();

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_TRAINING_ADMISSIONS",
                    training_id: currentTrainingId
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let admissions = res.data || [];

                        if (admissions.length === 0) {
                            tblEnrolled.draw(false);
                            return;
                        }

                        admissions.forEach(function(a, i) {
                            let statusBadge = a.status == 1 ?
                                '<span class="badge light badge-success">Present</span>' :
                                (a.status == 2 ?
                                    '<span class="badge light badge-danger">Absent</span>' :
                                    '<span class="badge light badge-warning">Pending</span>');

                            let statusBtn = currentTrainingStatus == 2 ?
                                '<span class="text-muted">Locked</span>' :
                                `<button class="btn btn-outline-warning btn-sm statusBtn"
                                    data-id="${a.id}"
                                    data-name="${a.beneficiary_name || ''}"
                                    data-status="${a.status}"
                                    title="Update Status">
                                    <i class="fas fa-edit"></i>
                                </button>`;

                            tblEnrolled.row.add([
                                i + 1,
                                a.beneficiary_name || '-',
                                a.beneficiary_doc_num || a.doc_num ? formatDocNum(a.beneficiary_doc_num || a.doc_num) : '-',
                                a.registration_at || '-',
                                statusBadge,
                                statusBtn
                            ]);
                        });
                        tblEnrolled.draw(false);
                    }
                }
            });
        }

        // ================= ADD ENROLLMENT (checkbox modal) =================
        $(document).on('click', '.addEnrollBtn', function() {
            $('#enroll_training_id').val(currentTrainingId);
            $('#enroll_training_select').val(currentTrainingId).hide();
            $('#enroll_training_display').show().text($(this).data('title') || '');
            $('#searchBeneficiary').val('');
            loadBeneficiaryList();
            $('#addModal').modal('show');
        });

        function loadBeneficiaryList() {
            $('#beneficiaryList').html('<p class="text-muted text-center">Loading...</p>');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BENEFICIARY"
                }),
                success: function(res) {
                    $('#beneficiaryList').empty();

                    if (res.code == 0 && res.data && res.data.length > 0) {
                        let table = `
                            <table class="table table-hover table-sm mb-0">
                                <thead class="fw-bold" style="background-color:#0f766e; color:#fff;">
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th>Name</th>
                                        <th>Document No</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        res.data.forEach(function(item) {
                            let name = `${item.fname ?? ''} ${item.mname ?? ''} ${item.lname ?? ''}`.trim();
                            table += `
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input beneficiaryCheck"
                                                id="bene_${item.id}" value="${item.id}">
                                            <label class="form-check-label" for="bene_${item.id}"></label>
                                        </div>
                                    </td>
                                    <td>${name}</td>
                                    <td>${formatDocNum(item.doc_num)}</td>
                                </tr>
                            `;
                        });

                        table += `</tbody></table>`;
                        $('#beneficiaryList').html(table);
                    } else {
                        $('#beneficiaryList').html('<p class="text-muted text-center">No beneficiaries found.</p>');
                    }
                }
            });
        }

        $('#searchBeneficiary').on('keyup', function() {
            let val = $(this).val().toLowerCase();
            $('#beneficiaryList tbody tr').each(function() {
                let name = $(this).find('td:nth-child(2)').text().toLowerCase();
                let doc = $(this).find('td:nth-child(3)').text().toLowerCase();
                $(this).toggle(name.includes(val) || doc.includes(val));
            });
            syncSelectAll();
        });

        $(document).on('change', '#selectAll', function() {
            $('.beneficiaryCheck:visible').prop('checked', $(this).is(':checked'));
        });

        $(document).on('change', '.beneficiaryCheck', function() {
            syncSelectAll();
        });

        function syncSelectAll() {
            let total = $('.beneficiaryCheck:visible').length;
            let checked = $('.beneficiaryCheck:visible:checked').length;
            $('#selectAll').prop('checked', total > 0 && total === checked);
            $('#selectAll').prop('indeterminate', checked > 0 && checked < total);
        }

        $('#confirmAddBtn').click(function() {
            let selected = [];
            $('.beneficiaryCheck:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                Swal.fire("Warning", "Please select at least one beneficiary.", "warning");
                return;
            }

            let trainingId = $('#enroll_training_id').val();
            if (!trainingId) {
                Swal.fire("Warning", "Please select a training.", "warning");
                return;
            }

            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_ADMISSIONS",
                    training_id: trainingId,
                    beneficiary_ids: selected
                }),
                success: function(res) {
                    $('#addModal').modal('hide');
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        loadEnrolled();
                        loadTrainings();
                        loadTrainingOptions();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                complete: function() {
                    $('#confirmAddBtn').prop('disabled', false).html('Enroll Selected');
                }
            });
        });

        // ================= UPDATE STATUS =================
        $(document).on('click', '.statusBtn', function() {
            $('#update_admission_id').val($(this).data('id'));
            $('#statusBenName').html('<strong>' + $(this).data('name') + '</strong>');
            $('#update_status').val($(this).data('status'));
            $('#statusModal').modal('show');
        });

        $(document).on('click', '#saveStatusBtn', function() {
            let id = $('#update_admission_id').val();
            let status = $('#update_status').val();

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "UPDATE_ADMISSION_STATUS",
                    id: id,
                    status: status
                }),
                success: function(res) {
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $('#statusModal').modal('hide');
                        loadEnrolled();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                }
            });
        });

    });
</script>
<?= endSection() ?>