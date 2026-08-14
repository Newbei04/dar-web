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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Checkout & Return</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Checkout & Return</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblReturn" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Booking No.</th>
                                    <th>Machinery</th>
                                    <th>Beneficiary</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
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

<?= endSection() ?>

<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        let tbl = $('#tblReturn').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        function showToast(title, icon) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon || 'success',
                title: title,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        }

        loadData();

        function loadData(silent) {
            if (!silent) showLoader();
            tbl.clear().draw();
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_RETURN_BOOKING"
                }),
                success: function(res) {
                    if (!silent) closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load bookings.", "error");
                        return;
                    }
                    (res.data || []).forEach(function(item, i) {

                        let statusText = '';
                        let actionBtn = '';

                        if (item.status == 1) {
                            statusText = `<span class="badge light badge-info">Approved</span>`;
                            actionBtn = `
                                <button class="btn btn-warning btn-sm checkoutBtn"
                                    data-id="${item.id}">
                                    <i class="fas fa-truck mr-1"></i> Checkout
                                </button>
                            `;
                        } else if (item.status == 2) {
                            statusText = `<span class="badge light badge-danger">Checked Out</span>`;
                            actionBtn = `
                                <button class="btn btn-success btn-sm returnBtn"
                                    data-id="${item.id}">
                                    <i class="fas fa-undo mr-1"></i> Return
                                </button>
                            `;
                        } else if (item.status == 3) {
                            statusText = `<span class="badge light badge-success">Returned</span>`;
                            actionBtn = `<span class="badge light badge-success">Done</span>`;
                        } else {
                            statusText = `<span class="badge light badge-secondary">${item.status_label ?? 'Unknown'}</span>`;
                        }

                        tbl.row.add([
                            i + 1,
                            item.booking_num ?? '-',
                            item.machinery_name ?? '-',
                            item.beneficiary_name ?? '-',
                            item.start_at ?? '-',
                            item.end_at ?? '-',
                            statusText,
                            actionBtn
                        ]).draw(false);

                    });
                },
                error: function(xhr) {
                    if (!silent) closeLoader();
                    console.error("LIST_RETURN_BOOKING failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load bookings.", "error");
                }
            });
        }

        /* =========================================================
           RETURN
        ========================================================= */
        $(document).on("click", ".returnBtn", function() {

            let id = $(this).data("id");

            Swal.fire({
                title: "Return Machinery?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Return"
            }).then((result) => {

                if (result.isConfirmed) {

                    showLoader('Returning machinery...');
                    $.ajax({
                        url: "<?= $baseURL ?>controller/ctrl-booking.php",
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "RETURN_BOOKING",
                            booking_id: id
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                loadData(true);
                                showToast(res.message || "Booking returned.", "success");
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            closeLoader();
                            console.error("RETURN_BOOKING failed:", xhr.responseText);
                            Swal.fire("Error", "Failed to return machinery.", "error");
                        }
                    });
                }

            });

        });

        /* =========================================================
           CHECKOUT
        ========================================================= */
        $(document).on("click", ".checkoutBtn", function() {

            let id = $(this).data("id");

            Swal.fire({
                title: "Checkout Machinery?",
                text: "Confirm that the machinery has been picked up.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Checkout"
            }).then((result) => {

                if (result.isConfirmed) {

                    showLoader('Checking out...');
                    $.ajax({
                        url: "<?= $baseURL ?>controller/ctrl-booking.php",
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "CHECKOUT_BOOKING",
                            booking_id: id
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                loadData(true);
                                showToast(res.message || "Booking checked out.", "success");
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            closeLoader();
                            console.error("CHECKOUT_BOOKING failed:", xhr.responseText);
                            Swal.fire("Error", "Failed to checkout machinery.", "error");
                        }
                    });
                }

            });

        });

    });
</script>

<?= endSection() ?>
