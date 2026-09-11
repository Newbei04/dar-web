<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Facility</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List Facility</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Facility List</h4>

                    <a href="<?= $baseURL ?>add-facility" class="btn btn-primary">
                        <i class="fa fa-plus mr-1"></i> Add Facility
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblFacility" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th width="10%">Status</th>
                                <th width="15%">Actions</th>
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
            <div class="modal-header">
                <h5 class="modal-title">Facility Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="35%" class="text-muted">Name</th>
                        <td id="view_name">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Branch</th>
                        <td id="view_branch">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Facility Type</th>
                        <td id="view_type">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Phone</th>
                        <td id="view_phone">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email</th>
                        <td id="view_email">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Address</th>
                        <td id="view_address">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Street</th>
                        <td id="view_street">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Operating Hours</th>
                        <td id="view_hours">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td id="view_status">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Created At</th>
                        <td id="view_created_at">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Updated At</th>
                        <td id="view_updated_at">-</td>
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

        var tblData = $('#tblFacility').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
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
                            item.status == 1 ? '<span class="badge light badge-success">Active</span>' :
                            item.status == 2 ? '<span class="badge light badge-danger">Not Active</span>' :
                            '<span class="badge light badge-warning">Pending</span>';

                        var actions = `
                        <button class="btn btn-info mr-2 viewBtn" data-id="${item.id}" data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary shadow editBtn" data-id="${item.id}" data-toggle="tooltip" title="Update Details">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-danger shadow deleteBtn" data-id="${item.id}" data-toggle="tooltip" title="Delete Facility">
                            <i class="fas fa-trash"></i>
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            item.name || "-",
                            item.facility_types || "-",
                            item.phone || "-",
                            item.email || "-",
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
                    $('#view_branch').text(d.branch_name || '-');
                    $('#view_type').text(d.facility_types || '-');
                    $('#view_phone').text(d.phone || '-');
                    $('#view_email').text(d.email || '-');
                    $('#view_address').text(d.address || '-');
                    $('#view_street').text(d.street || '-');
                    $('#view_hours').text(d.operating_hours || '-');
                    $('#view_status').html(
                        d.status == 1 ? '<span class="badge light badge-success">Active</span>' :
                        (d.status == 2 ? '<span class="badge light badge-danger">Not Active</span>' :
                            '<span class="badge light badge-warning">Pending</span>')
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
