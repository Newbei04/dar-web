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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Booking Approval</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Booking Approval</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblApproval" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Image</th>
                                    <th>Machinery</th>
                                    <th>Beneficiary</th>
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
                <!-- Machinery image -->
                <div class="text-center mb-3">
                    <img id="viewMachineryImage" src="" alt="Machinery Preview"
                         class="w-100 bg-light"
                         style="max-height:220px; object-fit:cover; border-radius:8px; display:none;">
                    <div id="viewMachineryImageFallback" class="bg-light d-flex flex-column align-items-center justify-content-center text-muted"
                         style="height:180px; border-radius:8px;">
                        <i class="fas fa-image fa-3x mb-2"></i>
                        <small>No image available</small>
                    </div>
                </div>
                <table class="table table-bordered mb-0">
                    <tr>
                        <th style="width:30%">Booking No</th>
                        <td id="view_booking_num"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="view_status"></td>
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
                        <th>Description</th>
                        <td id="view_machinery_description"></td>
                    </tr>
                    <tr>
                        <th>Beneficiary</th>
                        <td id="view_beneficiary"></td>
                    </tr>
                    <tr>
                        <th>Mobile</th>
                        <td id="view_beneficiary_mobile"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="view_beneficiary_email"></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td id="view_beneficiary_address"></td>
                    </tr>
                    <tr>
                        <th>Branch</th>
                        <td id="view_branch"></td>
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td id="view_start_at"></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td id="view_end_at"></td>
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
                        <th>Requested At</th>
                        <td id="view_created_at"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="modalDeclineBtn">
                    <i class="fas fa-times mr-1"></i> Decline
                </button>
                <button class="btn btn-success" id="modalApproveBtn">
                    <i class="fas fa-check mr-1"></i> Approve
                </button>
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
        let tbl = $('#tblApproval').DataTable({
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
            tbl.clear().draw();
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BOOKING_APPROVAL"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load bookings.", "error");
                        return;
                    }
                    (res.data || []).forEach(function(item, i) {

                        function machineThumbFallback() {
                            return '<div style="width:50px;height:50px;border-radius:10%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted"></i></div>';
                        }
                        window.machineThumbFallback = machineThumbFallback;

                        let machineryImage = item.machinery_image ?
                            `<img src="<?= $baseURL ?>assets/images/machinery/${item.machinery_image}" alt="Machine" style="width:50px;height:50px;border-radius:10%;object-fit:cover;" onerror="this.outerHTML=machineThumbFallback();">` :
                            machineThumbFallback();

                        tbl.row.add([
                            i + 1,
                            machineryImage,
                            item.machinery_name ?? '-',
                            item.beneficiary_name ?? '-',
                            `<span class="badge light badge-warning">For Approval</span>`,
                            item.created_at ?? '-',
                            `
                            <button class="btn btn-info btn-sm viewBtn"
                                data-id="${item.id}" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-success btn-sm approveBtn"
                                data-id="${item.id}" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm declineBtn"
                                data-id="${item.id}" title="Decline">
                                <i class="fas fa-times"></i>
                            </button>
                            `
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_BOOKING_APPROVAL failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load bookings.", "error");
                }
            });
        }

        $(document).on("click", ".viewBtn", function() {
            let id = $(this).data("id");
            showLoader('Loading details...');
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_BOOKING_APPROVAL_DETAIL",
                    booking_id: id
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        let d = res.data;
                        let peso = n => (n ?? 0).toLocaleString('en-PH', {
                            style: 'currency',
                            currency: 'PHP'
                        });
                        $("#view_booking_num").text(d.booking_num ?? '-');
                        const STATUS = {
                            0: { label: 'For Approval', cls: 'badge-warning' },
                            1: { label: 'Approved', cls: 'badge-success' },
                            2: { label: 'Completed', cls: 'badge-info' },
                            3: { label: 'Released', cls: 'badge-primary' },
                            4: { label: 'Declined', cls: 'badge-danger' }
                        };
                        let st = STATUS[d.status] || { label: 'Unknown', cls: 'badge-secondary' };
                        $("#view_status").html(`<span class="badge light ${st.cls}">${st.label}</span>`);
                        $("#view_machinery").text(
                            (d.machinery_name ?? '-') + ' ' + (d.machinery_model ?? '')
                        );
                        $("#view_machinery_type").text(d.machinery_type ?? '-');
                        $("#view_machinery_description").text(d.machinery_description ?? '-');
                        $("#view_beneficiary").text(d.beneficiary_name ?? '-');
                        $("#view_beneficiary_mobile").text(d.beneficiary_mobile ?? '-');
                        $("#view_beneficiary_email").text(d.beneficiary_email ?? '-');
                        $("#view_beneficiary_address").text(d.beneficiary_address ?? '-');
                        $("#view_branch").text(d.branch_name ?? '-');
                        $("#view_start_at").text(d.start_at ?? '-');
                        $("#view_end_at").text(d.end_at ?? '-');
                        $("#view_total_days").text(d.total_days ?? '-');
                        $("#view_unit_price").text(peso(d.unit_price));
                        $("#view_total_cost").text(peso(d.total_cost));
                        $("#view_created_at").text(d.created_at ?? '-');

                        const $img = $('#viewMachineryImage');
                        const $ph = $('#viewMachineryImageFallback');
                        $img.removeClass('d-block').addClass('d-none').removeAttr('src');
                        if (d.machinery_image) {
                            const base = "<?= $baseURL ?>assets/images/machinery/";
                            const preload = new Image();
                            preload.onload = function() {
                                $ph.removeClass('d-flex').addClass('d-none');
                                $img.attr('src', base + d.machinery_image).removeClass('d-none').addClass('d-block');
                            };
                            preload.onerror = function() {
                                $ph.removeClass('d-none').addClass('d-flex');
                            };
                            preload.src = base + d.machinery_image;
                        } else {
                            $ph.removeClass('d-none').addClass('d-flex');
                        }

                        $("#modalApproveBtn").data("id", d.id);
                        $("#modalDeclineBtn").data("id", d.id);
                        $("#viewModal").modal("show");
                    } else {
                        Swal.fire("Error", res.message || "Booking not found.", "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("GET_BOOKING_APPROVAL_DETAIL failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load booking details.", "error");
                }
            });
        });

        $(document).on("click", "#modalApproveBtn", function() {
            let id = $(this).data("id");
            $("#viewModal").modal("hide");
            confirmApprove(id);
        });

        $(document).on("click", "#modalDeclineBtn", function() {
            let id = $(this).data("id");
            $("#viewModal").modal("hide");
            confirmDecline(id);
        });

        $(document).on("click", ".approveBtn", function() {
            confirmApprove($(this).data("id"));
        });

        $(document).on("click", ".declineBtn", function() {
            confirmDecline($(this).data("id"));
        });

        function confirmApprove(id) {
            Swal.fire({
                title: "Approve Booking?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Approve"
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader('Approving...');
                    $.ajax({
                        url: "<?= $baseURL ?>controller/ctrl-booking.php",
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "APPROVE_BOOKING",
                            booking_id: id
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                Swal.fire("Success", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            closeLoader();
                            console.error("APPROVE_BOOKING failed:", xhr.responseText);
                            Swal.fire("Error", "Failed to approve booking.", "error");
                        }
                    });
                }
            });
        }

        function confirmDecline(id) {
            Swal.fire({
                title: "Decline Booking?",
                icon: "warning",
                input: "textarea",
                inputLabel: "Reason for declining",
                inputPlaceholder: "Enter remarks...",
                inputAttributes: {
                    required: true
                },
                showCancelButton: true,
                confirmButtonText: "Decline",
                preConfirm: (remarks) => {
                    if (!remarks) {
                        Swal.showValidationMessage("Remarks are required");
                    }
                    return remarks;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader('Declining...');
                    $.ajax({
                        url: "<?= $baseURL ?>controller/ctrl-booking.php",
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DECLINE_BOOKING",
                            booking_id: id,
                            remarks: result.value
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                Swal.fire("Success", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            closeLoader();
                            console.error("DECLINE_BOOKING failed:", xhr.responseText);
                            Swal.fire("Error", "Failed to decline booking.", "error");
                        }
                    });
                }
            });
        }
    });
</script>

<?= endSection() ?>
