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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Land Records</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Land Records List</h4>
                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                        <a href="<?= $baseURL ?>add-land-records" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Land Record
                        </a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblData" class="display responsive nowrap w-100">
                            <thead class="fw-bold">
                                <tr>
                                    <th>No.</th>
                                    <th>Title Number</th>
                                    <th>Beneficiary</th>
                                    <th>Credit Limit</th>
                                    <th>Certificate Status</th>
                                    <th>Issued Date</th>
                                    <th>Expiry Date</th>
                                    <th>Encrypted Signature</th>
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

<!-- ================= RECORD DETAIL MODAL ================= -->
<div class="modal fade" id="recordDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="fas fa-file-alt me-2 text-primary"></i>Land Record Details</h5>
                    <small class="text-muted" id="recordDetailSub"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-uppercase text-muted fw-bold mb-2"><small>Record Information</small></h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <tbody id="recordDetailInfo"></tbody>
                    </table>
                </div>
                <h6 class="text-uppercase text-muted fw-bold mb-2"><small>Digital Signature</small></h6>
                <div id="recordDetailSig"><p class="text-muted mb-0">No signature.</p></div>
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

        const API = '<?= $baseURL ?>controller/ctrl-land-records.php';
        const isAdmin = <?= isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1 ? 'true' : 'false' ?>;
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
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_RECORDS",
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let data = res.data || [];
                        tbl.clear();

                        data.forEach(function(item, i) {

                            let row = [
                                i + 1,
                                item.title_number ?? '-',
                                item.beneficiary_name ?? '-',
                                '\u20b1' + (item.credit_limit ?? '0'),
                                (item.certificate_status || '').toLowerCase() == 'pending' ? '<span class="badge light badge-warning">Pending</span>' :
                                (item.certificate_status || '').toLowerCase() == 'active' ? '<span class="badge light badge-success">Active</span>' :
                                (item.certificate_status || '').toLowerCase() == 'approved' ? '<span class="badge light badge-primary">Approved</span>' :
                                (item.certificate_status || '').toLowerCase() == 'released' ? '<span class="badge light badge-success">Released</span>' :
                                (item.certificate_status || '').toLowerCase() == 'expired' ? '<span class="badge light badge-danger">Expired</span>' :
                                `<span class="badge light badge-secondary">${item.certificate_status ?? '-'}</span>`,
                                item.issued_date ?? '-',
                                item.expiry_date ?? '-',
                                item.encrypted_signature ?? '-',
                            ];

                            let actions = `<button class="btn btn-info mr-2 viewRecordBtn" data-id="${item.id}" data-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>`;

                            if (isAdmin) {
                                actions += ` <button class="btn btn-primary shadow editRecordBtn" data-id="${item.id}" data-toggle="tooltip" title="Update Details">
                                    <span class="fas fa-pencil-alt"></span>
                                </button>`;
                                actions += ` <button class="btn btn-danger shadow deleteRecordBtn" data-id="${item.id}" data-toggle="tooltip" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>`;
                            }

                            row.push(actions);

                            tbl.row.add(row);

                        });
                        tbl.draw();

                    } else {
                        console.error("LIST_RECORDS error:", res.message);
                    }

                },
                error: function(xhr, status, error) {
                    console.error("LIST_RECORDS request failed:", status, error);
                }
            });
        }

        // ================= VIEW RECORD =================
        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function(c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
            });
        }

        function certBadge(status) {
            let s = (status || '').toLowerCase();
            let cls = { pending: 'warning', active: 'success', approved: 'primary', released: 'success', expired: 'danger' }[s] || 'secondary';
            return `<span class="badge light badge-${cls}">${escapeHtml(status) || '-'}</span>`;
        }

        $(document).on('click', '.viewRecordBtn', function() {
            let id = $(this).data('id');

            $('#recordDetailInfo').html('<tr><td colspan="2" class="text-center text-muted py-3">Loading...</td></tr>');
            $('#recordDetailSig').html('<p class="text-muted mb-0">Loading...</p>');
            $('#recordDetailSub').text('');

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_RECORD_DETAIL",
                    id: id
                }),
                success: function(res) {
                    if (res.code != 0 || !res.data) {
                        Swal.fire("Error", res.message || "Failed to load record details.", "error");
                        return;
                    }
                    const d = res.data;

                    $('#recordDetailSub').text(escapeHtml(d.title_number) || '');

                    let rows = `
                        <tr><th style="width:32%;">Title Number</th><td>${escapeHtml(d.title_number) || '-'}</td></tr>
                        <tr><th>Beneficiary</th><td>${escapeHtml(d.beneficiary_name) || '-'}</td></tr>
                        <tr><th>Certificate Status</th><td>${certBadge(d.certificate_status)}</td></tr>
                        <tr><th>Credit Limit</th><td>&#8369; ${escapeHtml(d.credit_limit) ?? '-'}</td></tr>
                        <tr><th>Issued Date</th><td>${escapeHtml(d.issued_date) || '-'}</td></tr>
                        <tr><th>Expiry Date</th><td>${escapeHtml(d.expiry_date) || '-'}</td></tr>
                        <tr><th>Created At</th><td>${escapeHtml(d.created_at) || '-'}</td></tr>
                        <tr><th>Updated At</th><td>${escapeHtml(d.updated_at) || '-'}</td></tr>
                    `;
                    $('#recordDetailInfo').html(rows);

                    $('#recordDetailSig').html(d.encrypted_signature
                        ? `<pre class="mb-0 p-2 bg-light rounded" style="white-space:pre-wrap;word-break:break-all;max-height:160px;overflow:auto;">${escapeHtml(d.encrypted_signature)}</pre>`
                        : '<p class="text-muted mb-0">No signature.</p>');

                    $('#recordDetailModal').modal('show');
                },
                error: function(xhr) {
                    console.error("GET_RECORD_DETAIL failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load record details.", "error");
                }
            });
        });

        // ================= EDIT RECORD =================
        $(document).on('click', '.editRecordBtn', function() {
            let id = $(this).data('id');
            window.location.href = '<?= $baseURL ?>edit-land-records?id=' + id;
        });

        // ================= DELETE =================
        $(document).on('click', '.deleteRecordBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This record will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_RECORD",
                            id: id
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                Swal.fire("Deleted!", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function() {
                            closeLoader();
                        }
                    });
                }
            });
        });

    });
</script>
<?= endSection() ?>