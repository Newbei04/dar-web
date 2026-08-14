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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Declined Bookings</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Declined Bookings</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblDeclined" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Beneficiary</th>
                                    <th>Branch</th>
                                    <th>Machine</th>
                                    <th>Unit Price</th>
                                    <th>Total Days</th>
                                    <th>Cost</th>
                                    <th>Status</th>
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

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = "<?= $baseURL ?>login";
        }

        let tbl = $("#tblDeclined").DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        loadDeclined();

        function loadDeclined() {
            showLoader();
            tbl.clear().draw();

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-booking.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BOOKING_DECLINED"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load bookings.", "error");
                        return;
                    }
                    (res.data || []).forEach((item, i) => {
                        tbl.row.add([
                            i + 1,
                            item.beneficiary_name ?? '-',
                            item.branch_name ?? '-',
                            item.machinery_name ?? '-',
                            "₱ " + parseFloat(item.unit_price || 0).toFixed(2),
                            (item.total_days || 0) + " day(s)",
                            "₱ " + parseFloat(item.total_cost || 0).toFixed(2),
                            `<span class="badge light badge-danger">Declined</span>`
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_BOOKING_DECLINED failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load bookings.", "error");
                }
            });
        }

    });
</script>

<?= endSection() ?>
