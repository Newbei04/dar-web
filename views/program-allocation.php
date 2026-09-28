<?= startSection('css') ?>
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css">
<style>
    .allocation-avatar {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(102, 126, 234, 0.12);
        color: #667eea;
        font-weight: 600;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 10px;
    }

    .table-responsive table.display .statusBtn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.8rem;
        line-height: 1;
        margin: 0 2px;
        transition: all 0.2s ease;
        background: rgba(108, 117, 125, 0.12);
        color: #6c757d;
        border: 1px solid transparent;
    }

    .table-responsive table.display .statusBtn:hover {
        background: #6c757d;
        color: #fff;
        border-color: #6c757d;
        transform: scale(1.1);
    }
</style>
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

                            let progName = item.program_name || '-';
                            let pInitials = progName.replace(/-/g, '').trim().split(/\s+/)
                                .map(w => w.charAt(0).toUpperCase()).slice(0, 2).join('') || 'P';
                            let programCell = `
                                <div class="d-flex align-items-center">
                                    <span class="allocation-avatar">${pInitials}</span>
                                    <div>
                                        <div class="fw-bold">${progName}</div>
                                        <small class="text-muted">Allocation #${item.id}</small>
                                    </div>
                                </div>
                            `;

                            let allocBadge = (item.allocation_type === 'PRODUCT')
                                ? '<span class="badge light badge-info">Product</span>'
                                : '<span class="badge light badge-success">Cash</span>';
                            let productCell = `
                                <div class="fw-semibold">${item.product_name || 'Cash'}</div>
                                <small>${allocBadge}</small>
                            `;

                            tbl.row.add([
                                i + 1,
                                programCell,
                                productCell,
                                item.branch_name ?? '-',
                                item.allocated_budget ?? 0,
                                item.distributed_budget ?? 0,
                                item.reserved_budget ?? 0,
                                `<span class="badge light badge-${badgeClass}">${item.status_text}</span>`,
                                `<a href="<?= $basePath ?>/list-program-beneficiary/${item.id}" class="viewBtn" title="View Beneficiaries">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= $basePath ?>/edit-allocation/${item.id}" class="editBtn" title="Edit Allocation">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="statusBtn editStatusBtn" data-id="${item.id}" data-status="${item.status}" title="Edit Status">
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