<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Program</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Allocations</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Program Allocation List</h4>
                    <a href="<?= $basePath ?>/add-allocation" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Allocation
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblAllocation" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Program</th>
                                    <th>Product</th>
                                    <th>Branch</th>
                                    <th>Allocated</th>
                                    <th>Distributed</th>
                                    <th>Reserved</th>
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

<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Allocation Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_status_id">
                <div class="form-group">
                    <label class="fw-semibold">Status</label>
                    <select class="form-control" id="edit_status_val">
                        <option value="0">Pending</option>
                        <option value="1">In-progress</option>
                        <option value="2">Processed</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveStatusBtn">Save</button>
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

        let tbl = $('#tblAllocation').DataTable({
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

        function loadData() {

            tbl.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-allocation.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_ALLOCATION",
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let data = res.data || [];

                        data.forEach(function(item, i) {

                            let badgeClass = item.status == 0 ? 'warning' : item.status == 1 ? 'info' : item.status == 2 ? 'success' : 'secondary';

                            tbl.row.add([
                                i + 1,
                                item.program_name ?? '-',
                                item.product_name || 'Cash',
                                item.branch_name ?? '-',
                                item.allocated_budget ?? 0,
                                item.distributed_budget ?? 0,
                                item.reserved_budget ?? 0,
                                `<span class="badge light badge-${badgeClass}">${item.status_text}</span>`,
                                `<a href="<?= $basePath ?>/list-program-beneficiary/${item.id}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= $basePath ?>/edit-allocation/${item.id}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-primary editStatusBtn" data-id="${item.id}" data-status="${item.status}">
                                    <i class="fas fa-cog"></i>
                                </button>`
                            ]).draw(false);

                        });
                    }
                }
            });

        }

        // ================= EDIT STATUS MODAL =================
        $(document).on('click', '.editStatusBtn', function() {
            $('#edit_status_id').val($(this).data('id'));
            $('#edit_status_val').val($(this).data('status'));
            $('#editStatusModal').modal('show');
        });

        $('#saveStatusBtn').on('click', function() {
            const id = $('#edit_status_id').val();
            const status = $('#edit_status_val').val();

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            $.ajax({
                url: "<?= $baseURL ?>/controller/ctrl-allocation.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "EDIT_STATUS",
                    id: id,
                    status: status
                }),
                success: function(res) {
                    $('#saveStatusBtn').prop('disabled', false).text('Save');
                    if (res.code == 0) {
                        $('#editStatusModal').modal('hide');
                        loadData();
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    $('#saveStatusBtn').prop('disabled', false).text('Save');
                }
            });
        });

    });
</script>

<?= endSection() ?>