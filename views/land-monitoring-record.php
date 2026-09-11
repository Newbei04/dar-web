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
        $(document).on('click', '.viewRecordBtn', function() {
            let id = $(this).data('id');
            window.location.href = '<?= $baseURL ?>record-detail?id=' + id;
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

    });
</script>
<?= endSection() ?>