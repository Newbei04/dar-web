<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Booking</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">All Bookings</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Bookings</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblBookings" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Booking No</th>
                                    <th>Machinery</th>
                                    <th>Beneficiary</th>
                                    <th>Branch</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th>Created</th>
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

<!-- VIEW BOOKING DETAILS MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th style="width:30%">Booking No</th>
                        <td id="view_booking_num"></td>
                    </tr>
                    <tr>
                        <th>Machinery</th>
                        <td id="view_machinery"></td>
                    </tr>
                    <tr>
                        <th>Machinery Type</th>
                        <td id="view_machinery_type"></td>
                    </tr>
                    <tr>
                        <th>Beneficiary</th>
                        <td id="view_beneficiary"></td>
                    </tr>
                    <tr>
                        <th>Branch</th>
                        <td id="view_branch"></td>
                    </tr>
                    <tr>
                        <th>Total Days</th>
                        <td id="view_total_days"></td>
                    </tr>
                    <tr>
                        <th>Unit Price</th>
                        <td id="view_unit_price"></td>
                    </tr>
                    <tr>
                        <th>Total Cost</th>
                        <td id="view_total_cost"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="view_status"></td>
                    </tr>
                    <tr>
                        <th>Rated</th>
                        <td id="view_rated"></td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td id="view_created_at"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- VIEW REVIEWS MODAL -->
<div class="modal fade" id="reviewsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-star text-warning me-1"></i>
                    Reviews <small class="text-muted fs-13" id="reviewsSub"></small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviewsBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        let tbl = $('#tblBookings').DataTable({
            responsive: true,
            order: [
                [0, 'desc']
            ],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        const baseURL = '<?= $baseURL ?>';

        let allBookings = [];

        const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[m]);

        const starsHtml = (val) => {
            const filled = Math.round(parseFloat(val || 0));
            let html = '';
            for (let i = 1; i <= 5; i++) {
                html += `<i class="fa fa-star ${i <= filled ? 'text-warning' : 'text-muted'}"></i>`;
            }
            return html;
        };

        function statusBadge(status) {
            switch (String(status)) {
                case '0':
                    return `<span class="badge light badge-warning">Pending</span>`;
                case '1':
                    return `<span class="badge light badge-info">Approved</span>`;
                case '2':
                    return `<span class="badge light badge-primary">Checked Out</span>`;
                case '3':
                    return `<span class="badge light badge-success">Returned</span>`;
                case '4':
                    return `<span class="badge light badge-danger">Declined</span>`;
                default:
                    return `<span class="badge light badge-secondary">Unknown</span>`;
            }
        }

        loadData();

        function loadData() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BOOKING"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load bookings.", "error");
                        return;
                    }
                    allBookings = res.data || [];
                    allBookings.forEach(function(item, i) {
                        tbl.row.add([
                            i + 1,
                            item.booking_num ?? '-',
                            item.machinery_name ?? '-',
                            item.beneficiary_name ?? '-',
                            item.branch_name ?? '-',
                            (item.total_cost ?? 0).toLocaleString('en-PH', {
                                style: 'currency',
                                currency: 'PHP'
                            }),
                            statusBadge(item.status),
                            item.created_at ?? '-',
                            `
                            <button class="btn btn-info btn-sm viewBtn"
                                data-id="${item.id}" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${item.machinery_id ? `
                            <button class="btn btn-outline-warning btn-sm reviewsBtn"
                                data-machinery-id="${item.machinery_id}"
                                data-name="${escapeHtml(item.machinery_name || '')}" title="View Reviews">
                                <i class="fa fa-star"></i>
                            </button>` : ''}
                            `
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_BOOKING failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load bookings.", "error");
                }
            });
        }

        /* ================= VIEW DETAILS ================= */
        $(document).on("click", ".viewBtn", function() {
            let id = $(this).data("id");
            const item = (allBookings || []).find(b => String(b.id) === String(id));
            if (!item) {
                Swal.fire("Error", "Booking not found.", "error");
                return;
            }
            let peso = n => (n ?? 0).toLocaleString('en-PH', {
                style: 'currency',
                currency: 'PHP'
            });
            $("#view_booking_num").text(item.booking_num ?? '-');
            $("#view_machinery").text((item.machinery_name ?? '-') + ' ' + (item.model ?? ''));
            $("#view_machinery_type").text(item.machinery_type ?? '-');
            $("#view_beneficiary").text(item.beneficiary_name ?? '-');
            $("#view_branch").text(item.branch_name ?? '-');
            $("#view_total_days").text(item.total_days ?? '-');
            $("#view_unit_price").text(peso(item.unit_price));
            $("#view_total_cost").text(peso(item.total_cost));
            $("#view_status").html(statusBadge(item.status));
            $("#view_rated").text(String(item.rated) === '1' ? 'Yes' : 'No');
            $("#view_created_at").text(item.created_at ?? '-');
            $("#viewModal").modal("show");
        });

        /* ================= VIEW REVIEWS ================= */
        $(document).on("click", ".reviewsBtn", function() {
            const machineryId = $(this).data('machinery-id');
            const name = $(this).data('name') || 'this machinery';
            $('#reviewsSub').text(' · ' + name);
            $('#reviewsBody').html('<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin me-1"></i> Loading reviews...</div>');
            $('#reviewsModal').modal('show');

            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MACHINERY_REVIEWS",
                    machinery_id: machineryId
                }),
                success: function(res) {
                    if (res.code != 0) {
                        $('#reviewsBody').html('<div class="text-center text-muted py-4">' + (res.message || 'Failed to load reviews.') + '</div>');
                        return;
                    }
                    const list = res.data || [];
                    if (!list.length) {
                        $('#reviewsBody').html('<div class="text-center text-muted py-4"><i class="fa fa-star fa-2x d-block mb-2"></i>No reviews yet for this machinery.</div>');
                        return;
                    }
                    let html = '';
                    list.forEach(function(r) {
                        html += `
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">${escapeHtml(r.beneficiary_name || 'Anonymous')}</span>
                                    <span class="text-muted fs-12">${r.created_at ?? '-'}</span>
                                </div>
                                <div class="text-warning mb-2">${starsHtml(parseFloat(r.rating || 0))}</div>
                                ${r.comment ? `<p class="mb-0">${escapeHtml(r.comment)}</p>` : '<p class="text-muted mb-0">No comment.</p>'}
                            </div>`;
                    });
                    $('#reviewsBody').html(html);
                },
                error: function() {
                    $('#reviewsBody').html('<div class="text-center text-muted py-4">Failed to load reviews.</div>');
                }
            });
        });
    });
</script>

<?= endSection() ?>
