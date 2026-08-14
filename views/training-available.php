<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Training</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Available Training</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Available Training</h4>
                        <small class="text-muted">Browse approved trainings and enroll to reserve your slot</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblData" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Reference No</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Schedule</th>
                                    <th>Programs</th>
                                    <th>Enrolled</th>
                                    <th>Status</th>
                                    <th>Action</th>
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

<!-- ================= VIEW DETAILS MODAL ================= -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Training Details</h5>
                    <small class="text-muted" id="viewRefNo">—</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <img id="viewImage" src="" alt="Training Image"
                             class="w-100 bg-light"
                             style="max-height:200px; object-fit:cover; border-radius:8px; display:none;">
                        <div id="viewImageFallback" class="bg-light d-flex flex-column align-items-center justify-content-center text-muted"
                             style="height:200px; border-radius:8px;">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <small>No image</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5 class="mb-1" id="viewTitle">—</h5>
                        <div class="mt-2">
                            <div class="d-flex justify-content-between border-bottom py-1 small">
                                <span class="text-muted">Program Type</span>
                                <span class="fw-bold text-end" id="viewType">—</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-1 small">
                                <span class="text-muted">Accreditation No</span>
                                <span class="fw-bold text-end" id="viewAccredNo">—</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-1 small">
                                <span class="text-muted">Start Date</span>
                                <span class="fw-bold text-end" id="viewStart">—</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-1 small">
                                <span class="text-muted">End Date</span>
                                <span class="fw-bold text-end" id="viewEnd">—</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-1 small">
                                <span class="text-muted">Venue</span>
                                <span class="fw-bold text-end" id="viewVenue">—</span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-uppercase fw-semibold small text-muted mb-1">Summary</p>
                            <p class="small mb-3" id="viewSummary">—</p>
                            <p class="text-uppercase fw-semibold small text-muted mb-1">Objectives</p>
                            <p class="small mb-0" id="viewObjectives">—</p>
                        </div>
                    </div>
                </div>
                <hr>
                <p class="text-uppercase fw-semibold small text-muted mb-2">Program Schedule</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0" id="viewProgramsTbl">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Topic</th>
                                <th>Speaker</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endSection() ?>

<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>

<script>
    $(document).ready(function() {

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        const baseURL = '<?= $baseURL ?>';
        const isBeneficiary = <?= (($_SESSION['role_id'] ?? 0) == 3) ? 'true' : 'false' ?>;
        const sessionBeneficiaryId = "<?= $_SESSION['profile']['id'] ?? '' ?>";

        let tbl = $('#tblData').DataTable({
            responsive: true,
            order: [],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        loadData();

        // ================= LOAD DATA =================
        function loadData() {
            tbl.clear().draw();
            showLoader('Loading trainings...');
            $.ajax({
                url: baseURL + 'controller/ctrl-training.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_AVAILABLE_TRAINING",
                    beneficiary_id: sessionBeneficiaryId || 0
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire('Error', res.message || 'Failed to load trainings.', 'error');
                        return;
                    }
                    (res.data || []).forEach(function(item, i) {
                        let schedule = item.start_at && item.end_at ?
                            fmtDate(item.start_at) + ' to ' + fmtDate(item.end_at) :
                            (item.start_at ? fmtDate(item.start_at) : '-');

                        let enrolledBadge = item.is_enrolled == 1 ?
                            '<span class="badge light badge-primary">Enrolled</span>' :
                            `<span class="badge light badge-info">${item.enrolled_count ?? 0}</span>`;

                        let statusBadge = item.status == 1 ?
                            '<span class="badge light badge-success">Started</span>' :
                            '<span class="badge light badge-warning">Open</span>';

                        let action = '';
                        if (isBeneficiary) {
                            action = item.is_enrolled == 1 ?
                                `<button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-check me-1"></i> Enrolled
                                </button>` :
                                `<button class="btn btn-primary btn-sm enrollBtn"
                                    data-id="${item.id}"
                                    data-title="${(item.title || '').replace(/"/g, '&quot;')}">
                                    <i class="fas fa-user-plus me-1"></i> Enroll
                                </button>`;
                        }

                        tbl.row.add([
                            i + 1,
                            item.reference_no ?? '-',
                            item.title ?? '-',
                            item.program_type_label ?? '-',
                            schedule,
                            item.program_count ?? 0,
                            enrolledBadge,
                            statusBadge,
                            `<div class="d-flex gap-1">
                                <button class="btn btn-outline-info btn-sm viewBtn" data-id="${item.id}" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${action}
                            </div>`
                        ]);
                    });
                    tbl.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error('LIST_AVAILABLE_TRAINING failed:', xhr.responseText);
                    Swal.fire('Error', 'Failed to load trainings.', 'error');
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

        // ================= VIEW DETAILS =================
        function setViewImage(src) {
            const $img = $('#viewImage');
            const $fb = $('#viewImageFallback');
            if (!src) {
                $img.hide();
                $fb.show();
                return;
            }
            $img.off('error load').on('error', function() {
                $(this).hide();
                $fb.show();
            }).on('load', function() {
                $(this).show();
                $fb.hide();
            }).attr('src', src);
        }

        $(document).on('click', '.viewBtn', function() {
            const id = $(this).data('id');
            $('#viewRefNo').text('Loading...');
            $('#viewTitle').text('—');
            $('#viewImage').hide();
            $('#viewImageFallback').show();
            $('#viewProgramsTbl tbody').empty();

            $.ajax({
                url: baseURL + 'controller/ctrl-training.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_TRAINING",
                    id: id
                }),
                success: function(res) {
                    if (res.code != 0) {
                        Swal.fire('Error', res.message, 'error');
                        return;
                    }
                    const d = res.data;
                    const ptype = {
                        0: 'Other',
                        1: 'Onsite Training',
                        2: 'Virtual Learning'
                    };
                    const venue = [d.street, d.barangay_name, d.city_name, d.province_name, d.region_name]
                        .filter(v => v).join(', ') || '-';

                    $('#viewRefNo').text(d.reference_no ?? '—');
                    $('#viewTitle').text(d.title ?? '—');
                    $('#viewType').text(ptype[d.program_type] ?? 'Other');
                    $('#viewAccredNo').text(d.accreditation_no ?? '—');
                    $('#viewStart').text(fmtDate(d.start_at));
                    $('#viewEnd').text(fmtDate(d.end_at));
                    $('#viewVenue').text(venue);
                    $('#viewSummary').text(d.summary || '—');
                    $('#viewObjectives').text(d.objectives || '—');
                    setViewImage(d.promotional_image ? baseURL + 'assets/images/training/' + d.promotional_image : '');
                }
            });

            // Load program schedule
            $.ajax({
                url: baseURL + 'controller/ctrl-training.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_TRAINING_PROGRAMS",
                    training_id: id
                }),
                success: function(res) {
                    let tbody = $('#viewProgramsTbl tbody');
                    tbody.empty();
                    if (res.code == 0 && res.data && res.data.length > 0) {
                        res.data.forEach(function(p, i) {
                            tbody.append(`
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${p.topic ?? '-'}</td>
                                    <td>${p.speaker ?? '-'}</td>
                                    <td>${p.time_start ?? '-'}</td>
                                    <td>${p.time_end ?? '-'}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="5" class="text-center text-muted">No programs scheduled</td></tr>');
                    }
                }
            });

            $('#viewModal').modal('show');
        });

        // ================= ENROLL =================
        $(document).on('click', '.enrollBtn', function() {
            const id = $(this).data('id');
            const title = $(this).data('title');

            if (!sessionBeneficiaryId) {
                Swal.fire('Validation', 'Enrollment is only available for beneficiary accounts.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Enroll in this training?',
                html: `<strong>${title}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                confirmButtonText: 'Yes, Enroll'
            }).then((result) => {
                if (!result.isConfirmed) return;

                showLoader('Enrolling...');
                $.ajax({
                    url: baseURL + 'controller/ctrl-training.php',
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "ENROLL_TRAINING",
                        training_id: id,
                        beneficiary_id: sessionBeneficiaryId
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire('Success', res.message, 'success');
                            loadData();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        closeLoader();
                        console.error('ENROLL_TRAINING failed:', xhr.responseText);
                        Swal.fire('Error', 'Failed to enroll.', 'error');
                    }
                });
            });
        });

    });
</script>

<?= endSection() ?>
