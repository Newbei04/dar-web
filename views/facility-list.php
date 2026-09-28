<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
    .facility-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
    .facility-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eee;
        padding: 1.25rem 1.5rem;
    }
    .facility-card .card-body {
        padding: 1rem 1.5rem 1.5rem;
    }

    #tblFacility thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 12px 15px;
        white-space: nowrap;
    }
    #tblFacility tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    #tblFacility tbody tr:hover {
        background-color: #f8f9fc;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    .badge-active {
        background: rgba(17,153,142,0.12);
        color: #11998e;
    }
    .badge-inactive {
        background: rgba(245,87,108,0.12);
        color: #f5576c;
    }
    .badge-pending-status {
        background: rgba(240,147,251,0.12);
        color: #d63384;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        margin: 0 2px;
    }
    .btn-action:hover {
        transform: scale(1.1);
    }
    .btn-view {
        background: rgba(102,126,234,0.12);
        color: #667eea;
        border: none;
    }
    .btn-view:hover { background: #667eea; color: #fff; }
    .btn-edit {
        background: rgba(17,153,142,0.12);
        color: #11998e;
        border: none;
    }
    .btn-edit:hover { background: #11998e; color: #fff; }
    .btn-delete {
        background: rgba(245,87,108,0.12);
        color: #f5576c;
        border: none;
    }
    .btn-delete:hover { background: #f5576c; color: #fff; }

    .facility-name-cell {
        font-weight: 600;
        color: #2d3436;
    }
    .facility-contact-cell {
        color: #636e72;
        font-size: 0.9rem;
    }

    .modal-header-muted {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #eef0f4;
        align-items: center;
    }
    .modal-header-muted .modal-title {
        font-weight: 600;
    }
    .modal-header-muted small {
        display: block;
        color: #6c757d;
    }
    .modal-body-sub {
        padding: 1.5rem;
    }
    .view-info-item {
        padding: 10px 0;
        border-bottom: 1px solid #f1f2f6;
    }
    .view-info-item:last-child {
        border-bottom: none;
    }
    .view-info-label {
        font-size: 0.8rem;
        color: #a4a4a4;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    .view-info-value {
        font-weight: 500;
        color: #2d3436;
        margin: 0;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Facility</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List Facility</a></li>
        </ol>
    </div>

    <!-- Facility Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card facility-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-list-ul me-2 text-primary"></i>Facility List
                    </h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="<?= $baseURL ?>add-facility" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Add Facility
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblFacility" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th>Facility Name</th>
                                    <th>Type</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th width="10%">Status</th>
                                    <th width="12%">Actions</th>
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

<!-- ================= VIEW FACILITY MODAL ================= -->
<div class="modal fade" id="viewModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-muted">
                <div>
                    <h5 class="modal-title">Facility Details</h5>
                    <small id="view_subtitle">View facility information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-sub">
                <div class="row">
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Facility Name</p>
                            <p class="view-info-value" id="view_name">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Branch</p>
                            <p class="view-info-value" id="view_branch">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Facility Type</p>
                            <p class="view-info-value" id="view_type">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Status</p>
                            <p class="view-info-value" id="view_status">-</p>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <h6 class="text-muted text-uppercase mb-2" style="font-size:0.8rem; letter-spacing:1px;">Contact Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Phone</p>
                            <p class="view-info-value" id="view_phone">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Email</p>
                            <p class="view-info-value" id="view_email">-</p>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <h6 class="text-muted text-uppercase mb-2" style="font-size:0.8rem; letter-spacing:1px;">Location & Schedule</h6>
                <div class="row">
                    <div class="col-md-8">
                        <div class="view-info-item">
                            <p class="view-info-label">Address</p>
                            <p class="view-info-value" id="view_address">-</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="view-info-item">
                            <p class="view-info-label">Street</p>
                            <p class="view-info-value" id="view_street">-</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Operating Hours</p>
                            <p class="view-info-value" id="view_hours">-</p>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <h6 class="text-muted text-uppercase mb-2" style="font-size:0.8rem; letter-spacing:1px;">Timestamps</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Created At</p>
                            <p class="view-info-value" id="view_created_at">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="view-info-item">
                            <p class="view-info-label">Updated At</p>
                            <p class="view-info-value" id="view_updated_at">-</p>
                        </div>
                    </div>
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
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        var tblData = $('#tblFacility').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            bFilter: false,
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6">>rtip',
            columnDefs: [
                { orderable: false, targets: [6] }
            ],
            order: [[1, 'asc']]
        });

        loadData();

        function loadData() {
            showLoader();
            tblData.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-facility.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_FACILITY",
                    limit: 100000
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load facilities.", "error");
                        return;
                    }
                    var data = res.data?.items || [];

                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(item, i) {

                        var status =
                            item.status == 1 ? '<span class="badge-status badge-active"><i class="fas fa-check-circle me-1"></i>Active</span>' :
                            item.status == 2 ? '<span class="badge-status badge-inactive"><i class="fas fa-times-circle me-1"></i>Inactive</span>' :
                            '<span class="badge-status badge-pending-status"><i class="fas fa-clock me-1"></i>Pending</span>';

                        var contact = '';
                        if (item.phone && item.email) {
                            contact = '<div>' + item.phone + '</div><div class="text-muted small">' + item.email + '</div>';
                        } else if (item.phone) {
                            contact = item.phone;
                        } else if (item.email) {
                            contact = item.email;
                        } else {
                            contact = '<span class="text-muted">—</span>';
                        }

                        var actions = `
                        <button class="btn-action btn-view viewBtn" data-id="${item.id}" data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit editBtn" data-id="${item.id}" data-toggle="tooltip" title="Edit Facility">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete deleteBtn" data-id="${item.id}" data-toggle="tooltip" title="Delete Facility">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            '<span class="facility-name-cell">' + (item.name || "-") + '</span>',
                            item.facility_types || "-",
                            '<div class="facility-contact-cell">' + contact + '</div>',
                            item.address || "-",
                            status,
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_FACILITY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load facilities.", "error");
                }
            });
        }

        // ================= VIEW =================
        $(document).on('click', '.viewBtn', function() {
            let id = $(this).data('id');

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-facility.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_FACILITY",
                    id: id
                }),
                success: function(res) {
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Facility not found.", "error");
                        return;
                    }
                    let d = res.data;
                    $('#view_name').text(d.name || '-');
                    $('#view_subtitle').text(d.name || 'View facility information');
                    $('#view_branch').text(d.branch_name || '-');
                    $('#view_type').text(d.facility_types || '-');
                    $('#view_phone').text(d.phone || '-');
                    $('#view_email').text(d.email || '-');
                    $('#view_address').text(d.address || '-');
                    $('#view_street').text(d.street || '-');
                    $('#view_hours').text(d.operating_hours || '-');
                    $('#view_status').html(
                        d.status == 1 ? '<span class="badge-status badge-active"><i class="fas fa-check-circle me-1"></i>Active</span>' :
                        (d.status == 2 ? '<span class="badge-status badge-inactive"><i class="fas fa-times-circle me-1"></i>Inactive</span>' :
                            '<span class="badge-status badge-pending-status"><i class="fas fa-clock me-1"></i>Pending</span>')
                    );
                    $('#view_created_at').text(d.created_at || '-');
                    $('#view_updated_at').text(d.updated_at || '-');
                    $('#viewModal').modal('show');
                },
                error: function() {
                    Swal.fire("Error", "Failed to load facility details.", "error");
                }
            });
        });

        // ================= EDIT =================
        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');
            window.location.href = "<?= $baseURL ?>edit-facility?id=" + id;
        });

        // ================= DELETE =================
        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Delete this facility?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete"
            }).then((result) => {
                if (!result.isConfirmed) return;

                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-facility.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "DELETE_FACILITY",
                        id: id
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Deleted", res.message, "success");
                            loadData();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        closeLoader();
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>
