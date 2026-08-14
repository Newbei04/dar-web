<?= startSection('css') ?>
<style>
    .training-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 18px;
        padding: 8px 0;
    }

    .training-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s, transform 0.15s;
    }

    .training-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.09);
        transform: translateY(-2px);
    }

    .card-thumb {
        height: 120px;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 44px;
        color: #adb5bd;
        position: relative;
        overflow: hidden;
    }

    .card-body {
        padding: 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .card-name {
        font-size: 14px;
        font-weight: 600;
        color: #212529;
        margin: 0;
        line-height: 1.35;
    }

    .card-ref {
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
        margin-top: 2px;
    }

    .badge-type {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
        background: #e7f1ff;
        color: #0d6efd;
    }

    .card-info {
        font-size: 11px;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .card-info i {
        width: 14px;
        text-align: center;
        color: #adb5bd;
    }

    .card-foot {
        margin-top: auto;
        padding-top: 8px;
    }

    .enrolled-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 12px;
    }

    .enrolled-row .en-num {
        font-weight: 700;
        color: #212529;
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

    .toolbar .form-select {
        max-width: 180px;
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
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Booking</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Enroll in Training</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Available Trainings</h4>
                </div>

                <div class="card-body">
                    <div class="toolbar">
                        <input type="text" class="form-control" id="searchBox" placeholder="Search training title, reference no…">
                        <select class="form-select" id="filterType">
                            <option value="">All Types</option>
                            <option value="Other">Other</option>
                            <option value="Onsite Training">Onsite Training</option>
                            <option value="Virtual Learning">Virtual Learning</option>
                        </select>
                    </div>

                    <div id="loadingState">
                        <i class="fas fa-spinner fa-spin"></i> Loading trainings…
                    </div>

                    <div class="training-grid" id="trainingGrid"></div>

                    <div id="emptyState" style="display:none;">
                        <i class="fas fa-chalkboard-user"></i>
                        No available trainings found
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= VIEW DETAILS MODAL ================= -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        const baseURL = '<?= $baseURL ?>';
        const isBeneficiary = <?= (($_SESSION['role_id'] ?? 0) == 3) ? 'true' : 'false' ?>;
        const sessionBeneficiaryId = "<?= $_SESSION['profile']['id'] ?? '' ?>";

        let allTrainings = [];
        let currentItems = [];

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

        // ================= POPULATE TYPE FILTER =================
        function populateTypeFilter() {
            const types = [...new Set(allTrainings.map(function(t) {
                return t.program_type_label;
            }).filter(Boolean))].sort();
            const cur = $('#filterType').val();
            let html = '<option value="">All Types</option>';
            types.forEach(function(t) {
                html += `<option value="${t}">${t}</option>`;
            });
            $('#filterType').html(html);
            if (cur) $('#filterType').val(cur);
        }

        // ================= RENDER CARDS =================
        function renderCards() {
            const q = $('#searchBox').val().toLowerCase();
            const type = $('#filterType').val();

            const filtered = allTrainings.filter(function(t) {
                if (q && !((t.title || '').toLowerCase().includes(q)) &&
                    !((t.reference_no || '').toLowerCase().includes(q))) return false;
                if (type && t.program_type_label !== type) return false;
                return true;
            });

            currentItems = filtered;

            const grid = $('#trainingGrid');

            if (!filtered.length) {
                grid.html('');
                $('#emptyState').show();
                return;
            }

            $('#emptyState').hide();

            let html = '';
            filtered.forEach(function(item, index) {
                let thumbHtml = '<i class="fas fa-chalkboard-user"></i>';
                if (item.promotional_image) {
                    thumbHtml = `<img src="${baseURL}assets/images/training/${escapeHtml(item.promotional_image)}" alt="Training" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;" onerror="this.remove()">`;
                }

                const schedule = item.start_at && item.end_at ?
                    `${fmtDate(item.start_at)} &rarr; ${fmtDate(item.end_at)}` :
                    (item.start_at ? fmtDate(item.start_at) : '-');

                const enrolledBadge = item.is_enrolled == 1
                    ? '<span class="badge light badge-primary">Enrolled</span>'
                    : `<span class="en-num">${item.enrolled_count ?? 0}</span> <span class="text-muted">enrolled</span>`;

                const statusBadge = item.status == 1
                    ? '<span class="badge light badge-success">Started</span>'
                    : '<span class="badge light badge-warning">Open</span>';

                let actionBtn = '';
                if (isBeneficiary) {
                    actionBtn = item.is_enrolled == 1
                        ? `<button class="btn btn-secondary btn-sm flex-fill" disabled>
                            <i class="fas fa-check me-1"></i> Enrolled
                           </button>`
                        : `<button class="btn btn-primary btn-sm flex-fill enrollBtn" data-idx="${index}">
                            <i class="fas fa-user-plus me-1"></i> Enroll
                           </button>`;
                }

                html += `
                <div class="training-card">
                    <div class="card-thumb">${thumbHtml}</div>
                    <div class="card-body">
                        <p class="card-name">${escapeHtml(item.title ?? '—')}</p>
                        <p class="card-ref">${escapeHtml(item.reference_no ?? '—')}</p>

                        <div class="card-meta">
                            ${item.program_type_label ? `<span class="badge-type">${escapeHtml(item.program_type_label)}</span>` : ''}
                            ${statusBadge}
                        </div>

                        <p class="card-info"><i class="fas fa-calendar"></i>${schedule}</p>

                        <div class="enrolled-row">
                            <span class="text-muted"><i class="fas fa-users me-1"></i>Slots</span>
                            <span>${enrolledBadge}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-outline-info btn-sm flex-fill viewBtn" data-idx="${index}">
                            <i class="fas fa-eye me-1"></i> Details
                        </button>
                        ${actionBtn}
                    </div>
                </div>`;
            });

            grid.html(html);
        }

        // ================= LOAD TRAININGS =================
        function loadTrainings() {
            $('#loadingState').show();
            $('#trainingGrid').html('');
            $('#emptyState').hide();

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
                    $('#loadingState').hide();
                    if (res.code != 0) {
                        Swal.fire('Error', res.message || 'Failed to load trainings.', 'error');
                        return;
                    }
                    allTrainings = Array.isArray(res.data) ? res.data : [];
                    populateTypeFilter();
                    renderCards();
                },
                error: function(xhr) {
                    $('#loadingState').hide();
                    console.error('LIST_AVAILABLE_TRAINING failed:', xhr.responseText);
                    Swal.fire('Error', 'Failed to load trainings.', 'error');
                }
            });
        }

        $('#searchBox').on('input', renderCards);
        $('#filterType').on('change', renderCards);

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
            const item = currentItems[$(this).data('idx')];
            if (!item) return;
            const id = item.id;

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
                data: JSON.stringify({ trans: "GET_TRAINING", id: id }),
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

            $.ajax({
                url: baseURL + 'controller/ctrl-training.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "GET_TRAINING_PROGRAMS", training_id: id }),
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
            const item = currentItems[$(this).data('idx')];
            if (!item) return;

            if (!sessionBeneficiaryId) {
                Swal.fire('Validation', 'Enrollment is only available for beneficiary accounts.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Enroll in this training?',
                html: `<strong>${escapeHtml(item.title ?? '')}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                confirmButtonText: 'Yes, Enroll'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                showLoader('Enrolling...');
                $.ajax({
                    url: baseURL + 'controller/ctrl-training.php',
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "ENROLL_TRAINING",
                        training_id: item.id,
                        beneficiary_id: sessionBeneficiaryId
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire('Success', res.message, 'success');
                            loadTrainings();
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

        // ================= INIT =================
        loadTrainings();

    });
</script>
<?= endSection() ?>
