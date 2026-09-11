<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Program</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Allocations</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Beneficiaries</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Program Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-muted mb-1">Program</label>
                                <p class="fw-bold mb-0" id="info_program">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-muted mb-1">Code</label>
                                <p class="fw-bold mb-0" id="info_code">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-muted mb-1">Branch</label>
                                <p class="fw-bold mb-0" id="info_facility">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="text-muted mb-1">Subsidy Type</label>
                                <p class="fw-bold mb-0" id="info_subsidy_type">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="text-muted mb-1">Allocated</label>
                                <p class="fw-bold mb-0" id="info_allocated">0</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="text-muted mb-1">Distributed</label>
                                <p class="fw-bold mb-0" id="info_distributed">0</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="text-muted mb-1">Remaining</label>
                                <p class="fw-bold mb-0" id="info_reserved">0</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- CARD HEADER -->
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h5 class="card-title me-auto mb-2 mb-md-0">Program Beneficiaries</h5>
                    <button class="btn btn-primary btn-sm" id="addNewBtn">
                        <i class="fas fa-plus me-1"></i> Add beneficiary
                    </button>
                </div>

                <!-- CARD BODY -->
                <div class="card-body d-flex flex-column">
                    <div class="table-responsive flex-grow-1">
                        <table id="tblData" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Program</th>
                                    <th>Beneficiary</th>
                                    <th>Status</th>
                                    <th>Date Enrolled</th>
                                    <th>Date Received</th>
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
<!-- STATUS MODAL -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="pb_id">
                <input type="hidden" id="pb_action">
                <p id="modalText">Are you sure you want to proceed?</p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="confirmActionBtn">Confirm</button>
            </div>

        </div>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Beneficiaries</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchBeneficiary" class="form-control" placeholder="Search by name or document number...">
                </div>
                <div id="beneficiaryList" style="max-height: 350px; overflow-y: auto;">
                    <p class="text-muted text-center">Loading...</p>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Available beneficiaries for this allocation</small>
                    <span class="badge light badge-primary" id="selectedCount">0 selected</span>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="confirmAddBtn">Add Selected</button>
            </div>

        </div>
    </div>
</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Beneficiary Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Full Name</p>
                        <p id="view_name" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Document Number</p>
                        <p id="view_doc_num" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Card Number</p>
                        <p id="view_card_num" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Email</p>
                        <p id="view_email" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Mobile</p>
                        <p id="view_mobile" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Program</p>
                        <p id="view_program" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Status</p>
                        <p id="view_status" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Date Enrolled</p>
                        <p id="view_date_enrolled" class="mb-3">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted small text-uppercase mb-1">Date Received</p>
                        <p id="view_date_received" class="mb-3">-</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        var allocation_id = window.location.pathname.split('/').pop();
        var tblData = $('#tblData').DataTable({
            order: [],
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });
        loadProgramInfo();
        loadData();

        function loadProgramInfo() {

            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-program-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_PROGRAM_ALLOCATION",
                    allocation_id: allocation_id
                }),
                success: function(response) {

                    if (response.code === 0) {
                        let data = response.data;

                        $("#info_program").text(data.program_name);
                        $("#info_code").text(data.program_code);
                        $("#info_facility").text(data.branch_name ?? "-");
                        $("#info_subsidy_type").text(
                            data.asset_type == 0 ? "Currency" :
                            data.asset_type == 1 ? "Product" :
                            "-"
                        );

                        $("#info_allocated").text(data.allocated_budget ?? 0);
                        $("#info_distributed").text(data.distributed_budget ?? 0);
                        $("#info_reserved").text(data.reserved_budget ?? 0);
                    }
                }
            });

        }

        function loadData() {
            showLoader();
            tblData.clear().draw();

            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-program-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_PROGRAM_BENEFICIARY",
                    allocation_id: allocation_id
                }),
                success: function(res) {
                    closeLoader();

                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load data.", "error");
                        return;
                    }
                    var data = res.data || [];

                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(row, i) {
                        let status = '';
                        switch (parseInt(row.status)) {
                            case 0:
                                status = '<span class="badge light badge-warning">FOR RELEASE</span>';
                                break;
                            case 1:
                                status = '<span class="badge light badge-info">RELEASED</span>';
                                break;
                            case 2:
                                status = '<span class="badge light badge-success">RECEIVED</span>';
                                break;
                            case 3:
                                status = '<span class="badge light badge-danger">CANCELLED</span>';
                                break;
                            default:
                                status = '<span class="badge light badge-secondary">UNKNOWN</span>';
                        }

                        let actions = "";

                        if (row.status == 0) {
                            actions += `
                                <button class="btn btn-success btn-sm actionBtn" data-id="${row.id}" data-action="RELEASE">
                                    <i class="fas fa-check"></i> Release
                                </button>
                                <button class="btn btn-danger btn-sm actionBtn" data-id="${row.id}" data-action="CANCEL">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            `;
                        } else if (row.status == 1) {
                            actions += `
                                <button class="btn btn-primary btn-sm actionBtn" data-id="${row.id}" data-action="RECEIVE">
                                    <i class="fas fa-box"></i> Mark Received
                                </button>
                            `;
                        } else {
                            actions += `
                                <button class="btn btn-info btn-sm viewBtn" data-id="${row.id}">
                                    <i class="fas fa-eye"></i> 
                                </button>
                            `;
                        }
                        tblData.row.add([
                            i + 1,
                            row.program_name || '-',
                            row.beneficiary_name || '-',
                            status,
                            row.date_enrolled || '-',
                            row.date_received || 'Not yet received',
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },
                error: function(xhr) {
                    closeLoader();
                    console.error(xhr.responseText);
                    Swal.fire("Error", "Failed to load data.", "error");
                }
            });
        }

        function loadARBUsers() {
            $('#beneficiaryList').html('<p class="text-muted text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Loading...</p>');

            $.ajax({
                url: '<?= $baseURL ?>/controller/ctrl-program-beneficiary.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BENEFICIARY",
                    allocation_id: allocation_id
                }),
                success: function(res) {
                    $('#beneficiaryList').empty();

                    if (res.code == 0 && res.data.length > 0) {

                        let table = `
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th>Name</th>
                                        <th>Doc #</th>
                                        <th>Card #</th>
                                        <th>Mobile</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        res.data.forEach(function(item) {
                            let name = (item.profile.fname ?? '') + ' ' + (item.profile.lname ?? '');
                            table += `
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input beneficiaryCheck"
                                                id="bene_${item.id}" value="${item.id}">
                                            <label class="form-check-label" for="bene_${item.id}"></label>
                                        </div>
                                    </td>
                                    <td class="fw-bold">${name}</td>
                                    <td>${formatDocNum(item.profile.doc_num)}</td>
                                    <td>${item.profile.card_num || '-'}</td>
                                    <td>${item.profile.mobile || '-'}</td>
                                </tr>
                            `;
                        });

                        table += `</tbody></table>`;

                        $('#beneficiaryList').html(table);

                    } else {
                        $('#beneficiaryList').html('<p class="text-muted text-center py-4"><i class="fas fa-user-slash me-2"></i>No beneficiaries available for this allocation.</p>');
                    }
                },
                error: function() {
                    $('#beneficiaryList').html('<p class="text-danger text-center py-4"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load beneficiaries.</p>');
                }
            });
        }

        $('#addNewBtn').click(function() {
            loadARBUsers();
            $('#searchBeneficiary').val('');
            $('#addModal').modal('show');
        });

        $('#addModal').on('hidden.bs.modal', function() {
            $('#searchBeneficiary').val('');
            $('#beneficiaryList').html('<p class="text-muted text-center">Loading...</p>');
            $('#selectedCount').text('0 selected');
        });

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
            syncSelectAll();
        });

        $(document).on('change', '.beneficiaryCheck', function() {
            syncSelectAll();
        });

        function syncSelectAll() {
            let total = $('.beneficiaryCheck:visible').length;
            let checked = $('.beneficiaryCheck:visible:checked').length;
            $('#selectAll').prop('checked', total > 0 && total === checked);
            $('#selectAll').prop('indeterminate', checked > 0 && checked < total);
            $('#selectedCount').text(checked + ' selected');
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

            showLoader();

            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-program-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_PROGRAM_BENEFICIARY",
                    allocation_id: allocation_id,
                    beneficiary_ids: selected
                }),
                success: function(res) {
                    closeLoader();
                    $('#addModal').modal('hide');

                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    closeLoader();
                    Swal.fire("Error", "Server error occurred.", "error");
                }
            });
        });

        $(document).on('click', '.viewBtn', function() {
            let id = $(this).data('id');
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-program-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_PROGRAM_BENEFICIARY",
                    id: id
                }),
                success: function(res) {
                    closeLoader();

                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load data.", "error");
                        return;
                    }

                    let d = res.data;

                    $('#view_name').text(d.beneficiary_name || '-');
                    $('#view_doc_num').text(formatDocNum(d.doc_num));
                    $('#view_card_num').text(d.card_num || '-');
                    $('#view_email').text(d.email || '-');
                    $('#view_mobile').text(d.mobile || '-');
                    $('#view_program').text(d.program_name || '-');
                    const statuses = {
                        0: '<span class="badge light badge-warning">FOR RELEASE</span>',
                        1: '<span class="badge light badge-info">RELEASED</span>',
                        2: '<span class="badge light badge-success">RECEIVED</span>',
                        3: '<span class="badge light badge-danger">CANCELLED</span>'
                    };

                    $('#view_status').html(statuses[d.status] || '<span class="badge light badge-secondary">UNKNOWN</span>');

                    $('#view_date_enrolled').text(d.date_enrolled || '-');
                    $('#view_date_received').text(d.date_received || '-');

                    $('#viewModal').modal('show');
                },
                error: function() {
                    closeLoader();
                    Swal.fire("Error", "Failed to load data.", "error");
                }
            });
        });

        $(document).on('click', '.actionBtn', function() {
            let id = $(this).data('id');
            let action = $(this).data('action');

            $('#pb_id').val(id);
            $('#pb_action').val(action);

            let message = "";
            if (action == "RELEASE") message = "Release this beneficiary?";
            if (action == "RECEIVE") message = "Mark this as received?";
            if (action == "CANCEL") message = "Cancel this enrollment?";

            $('#modalText').text(message);
            $('#statusModal').modal('show');
        });

        $('#confirmActionBtn').click(function() {
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-program-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "UPDATE_PROGRAM_BENEFICIARY_STATUS",
                    id: $('#pb_id').val(),
                    action: $('#pb_action').val()
                }),
                success: function(res) {
                    closeLoader();
                    $('#statusModal').modal('hide');

                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    closeLoader();
                    Swal.fire("Error", "Server error occurred", "error");
                }
            });
        });

        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Delete?",
                text: "This cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete"
            }).then((result) => {
                if (!result.isConfirmed) return;

                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>/controller/ctrl-program-beneficiary.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "DELETE_PROGRAM_BENEFICIARY",
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