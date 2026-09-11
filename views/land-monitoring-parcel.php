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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Land Parcels</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Land Parcels List</h4>
                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                        <a href="<?= $baseURL ?>add-land-parcel" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Land Parcel
                        </a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblData" class="display responsive nowrap w-100">
                            <thead class="fw-bold">
                                <tr>
                                    <th>No.</th>
                                    <th>Beneficiary</th>
                                    <th>Title Number</th>
                                    <th>Total Area (Ha)</th>
                                    <th>Land Use Type</th>
                                    <th>Last Survey Date</th>
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

        // Initialize DataTable once
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
                url: '<?= $baseURL ?>controller/ctrl-land-parcel.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_LAND_PARCEL",
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let data = res.data || [];
                        tbl.clear();

                        data.forEach(function(item, i) {

                            let actions = `<button class="btn btn-info mr-2 viewDetailBtn" data-id="${item.id}" data-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>`;

                            if ("<?= $_SESSION['role_id'] ?? '' ?>" == 1) {
                                actions += ` <button class="btn btn-primary shadow editParcelBtn" data-id="${item.id}" data-toggle="tooltip" title="Update Details">
                                    <span class="fas fa-pencil-alt"></span>
                                </button>`;
                            }

                            let row = [
                                i + 1,
                                item.beneficiary_name ?? '-',
                                item.title_number ?? '-',
                                item.total_area_hectares ?? '-',
                                item.land_use_type ?? '-',
                                item.last_survey_date ?? '-',
                                actions
                            ];

                            tbl.row.add(row);
                        });
                        tbl.draw();

                    } else {
                        console.error("LIST_LAND_PARCEL error:", res.message);
                    }

                },
                error: function(xhr, status, error) {
                    console.error("LIST_LAND_PARCEL request failed:", status, error);
                }
            });
        }

        // ================= VIEW DETAIL =================
        $(document).on('click', '.viewDetailBtn', function() {
            let id = $(this).data('id');
            window.location.href = '<?= $baseURL ?>parcel-detail?id=' + id;
        });

        // ================= EDIT PARCEL =================
        $(document).on('click', '.editParcelBtn', function() {
            let id = $(this).data('id');
            window.location.href = '<?= $baseURL ?>edit-land-parcel?id=' + id;
        });

    });
</script>
<?= endSection() ?>