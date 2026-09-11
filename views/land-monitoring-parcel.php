<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Land</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Land Parcels</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Land Parcels List</h4>
                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                        <a href="<?= $baseURL ?>add-land-parcel" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Land Parcel
                        </a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblData" class="display responsive nowrap w-100">
                            <thead class="fw-bold">
                                <tr>
                                    <th>No.</th>
                                    <th>Beneficiary</th>
                                    <th>Title Number</th>
                                    <th>Total Area (Ha)</th>
                                    <th>Land Use Type</th>
                                    <th>Last Survey Date</th>
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

<!-- ================= PARCEL DETAIL MODAL ================= -->
<div class="modal fade" id="parcelDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Land Parcel Details</h5>
                    <small class="text-muted" id="parcelDetailSub"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-uppercase text-muted fw-bold mb-2"><small>Parcel Information</small></h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <tbody id="parcelDetailInfo"></tbody>
                    </table>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2"><small>Certificates / Records</small></h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Credit Limit</th>
                                <th>Status</th>
                                <th>Issued</th>
                                <th>Expiry</th>
                            </tr>
                        </thead>
                        <tbody id="parcelDetailCerts">
                            <tr><td colspan="5" class="text-center text-muted py-3">No records.</td></tr>
                        </tbody>
                    </table>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2"><small>Monitoring Logs</small></h6>
                <div id="parcelDetailLogs"></div>
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

        // Initialize DataTable once
        let tbl = $('#tblData').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        loadData();

        // ================= LOAD LIST =================
        function loadData() {

            tbl.clear().draw();

            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-land-parcel.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_LAND_PARCEL",
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let data = res.data || [];
                        tbl.clear();

                        data.forEach(function(item, i) {

                            let actions = `<button class="btn btn-info mr-2 viewDetailBtn" data-id="${item.id}" data-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>`;

                            if ("<?= $_SESSION['role_id'] ?? '' ?>" == 1) {
                                actions += ` <button class="btn btn-primary shadow editParcelBtn" data-id="${item.id}" data-toggle="tooltip" title="Update Details">
                                    <span class="fas fa-pencil-alt"></span>
                                </button>`;
                            }

                            let row = [
                                i + 1,
                                item.beneficiary_name ?? '-',
                                item.title_number ?? '-',
                                item.total_area_hectares ?? '-',
                                item.land_use_type ?? '-',
                                item.last_survey_date ?? '-',
                                actions
                            ];

                            tbl.row.add(row);
                        });
                        tbl.draw();

                    } else {
                        console.error("LIST_LAND_PARCEL error:", res.message);
                    }

                },
                error: function(xhr, status, error) {
                    console.error("LIST_LAND_PARCEL request failed:", status, error);
                }
            });
        }

        // ================= VIEW DETAIL =================
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

        const LOG_TYPE_LABELS = { 0: 'Site Visit', 1: 'Photo Report', 2: 'Soil Test', 3: 'Pest Control', 4: 'Others' };

        $(document).on('click', '.viewDetailBtn', function() {
            let id = $(this).data('id');

            $('#parcelDetailInfo').html('');
            $('#parcelDetailCerts').html('<tr><td colspan="5" class="text-center text-muted py-3">Loading...</td></tr>');
            $('#parcelDetailLogs').html('<p class="text-muted text-center py-2">Loading...</p>');
            $('#parcelDetailSub').text('');

            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-land-parcel.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_PARCEL_DETAIL",
                    id: id
                }),
                success: function(res) {
                    if (res.code != 0 || !res.data) {
                        Swal.fire("Error", res.message || "Failed to load parcel details.", "error");
                        return;
                    }

                    const d = res.data;
                    const p = d.parcel || {};

                    $('#parcelDetailSub').text(escapeHtml(p.title_number || ''));

                    let infoRows = `
                        <tr><th style="width:32%;">Title Number</th><td>${escapeHtml(p.title_number) || '-'}</td></tr>
                        <tr><th>Beneficiary</th><td>${escapeHtml(p.beneficiary_name) || '-'}</td></tr>
                        <tr><th>Total Area (Ha)</th><td>${escapeHtml(p.total_area_hectares) ?? '-'}</td></tr>
                        <tr><th>Land Use Type</th><td>${escapeHtml(p.land_use_type) || '-'}</td></tr>
                        <tr><th>Productivity Score</th><td>${escapeHtml(p.productivity_score) ?? '-'}</td></tr>
                        <tr><th>Last Survey Date</th><td>${escapeHtml(p.last_survey_date) || '-'}</td></tr>
                        <tr><th>Latitude</th><td>${escapeHtml(p.latitude) ?? '-'}</td></tr>
                        <tr><th>Longitude</th><td>${escapeHtml(p.longitude) ?? '-'}</td></tr>
                        <tr><th>Created At</th><td>${escapeHtml(p.created_at) || '-'}</td></tr>
                    `;
                    $('#parcelDetailInfo').html(infoRows);

                    const certs = d.certificates || [];
                    if (certs.length) {
                        let cHtml = '';
                        certs.forEach(function(c, i) {
                            cHtml += `
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${escapeHtml(c.credit_limit) ?? '-'}</td>
                                    <td>${escapeHtml(c.certificate_status) || '-'}</td>
                                    <td>${escapeHtml(c.issued_date) || '-'}</td>
                                    <td>${escapeHtml(c.expiry_date) || '-'}</td>
                                </tr>`;
                        });
                        $('#parcelDetailCerts').html(cHtml);
                    } else {
                        $('#parcelDetailCerts').html('<tr><td colspan="5" class="text-center text-muted py-3">No records.</td></tr>');
                    }

                    const logs = d.monitoring_logs || [];
                    if (logs.length) {
                        let lHtml = '';
                        logs.forEach(function(l) {
                            let imgs = '';
                            (l.images || []).forEach(function(img) {
                                imgs += `
                                    <img src="<?= $baseURL ?>assets/images/land/${escapeHtml(img.name)}" alt="${escapeHtml(img.name)}"
                                        style="width:64px;height:64px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;"
                                        onerror="this.style.display='none'">`;
                            });
                            lHtml += `
                                <div class="border rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <strong>${escapeHtml(l.title) || 'Untitled'}</strong>
                                        <span class="badge light badge-secondary">${escapeHtml(LOG_TYPE_LABELS[l.log_type] ?? l.log_type)}</span>
                                    </div>
                                    <small class="text-muted d-block mb-1">By: ${escapeHtml(l.employee_name) || '-'} &middot; ${escapeHtml(l.created_at) || ''}</small>
                                    ${l.notes ? `<p class="mb-1">${escapeHtml(l.notes)}</p>` : ''}
                                    <div class="d-flex gap-1 flex-wrap">${imgs}</div>
                                </div>`;
                        });
                        $('#parcelDetailLogs').html(lHtml);
                    } else {
                        $('#parcelDetailLogs').html('<p class="text-muted text-center py-2">No monitoring logs.</p>');
                    }

                    $('#parcelDetailModal').modal('show');
                },
                error: function(xhr) {
                    console.error("GET_PARCEL_DETAIL failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load parcel details.", "error");
                }
            });
        });

        // ================= EDIT PARCEL =================
        $(document).on('click', '.editParcelBtn', function() {
            let id = $(this).data('id');
            window.location.href = '<?= $baseURL ?>edit-land-parcel?id=' + id;
        });

    });
</script>
<?= endSection() ?>