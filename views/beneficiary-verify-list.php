<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Beneficiary</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">For Verification</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Beneficiary List for Verification</h4>

                    <a href="<?= $baseURL ?>beneficiary-add" class="btn btn-primary">
                        <i class="fa fa-user-plus mr-1"></i> New Beneficiary
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblBeneficiary" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="8%">Photo</th>
                                <th width="15%">Username</th>
                                <th>Name</th>
                                <th width="12%">Status</th>
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
<?= endSection() ?>


<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        var tblData = $('#tblBeneficiary').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        loadBeneficiary();

        function loadBeneficiary() {
            showLoader();
            tblData.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BENEFICIARY",
                    status: "UNVERIFIED"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load beneficiaries.", "error");
                        return;
                    }
                    var data = res.data || [];
                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(user, i) {

                        var status = (user.status == 0) ? `<span class="badge light badge-warning">${user.status_label}</span>` :
                            (user.status == 1) ? `<span class="badge light badge-success">${user.status_label}</span>` :
                            (user.status == 2) ? `<span class="badge light badge-secondary">${user.status_label}</span>` :
                            (user.status == 3) ? `<span class="badge light badge-dark">${user.status_label}</span>` :
                            '<span class="badge light badge-info">Unknown</span>';

                        var profilePhoto = user.profile ?
                            `<img src="<?= $baseURL ?>assets/images/profile/${user.profile}" alt="Profile" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" onerror="this.outerHTML=beneficiaryThumbFallback();">` :
                            beneficiaryThumbFallback();

                        var actions =
                            `<a href="<?= $baseURL ?>beneficiary-verify?id=${user.users_id}" class="btn btn-primary btn-sm mr-1" data-toggle="tooltip" title="Verify Details">
                                <i class="fas fa-user-check mr-1"></i> Verify
                            </a>`;

                        var fullname =
                            `<span class="fas fa-user mr-1 text-muted"></span> ` +
                            ((user.fname || "") + " " + (user.mname || "") + " " + (user.lname || "")).trim() +
                            `<br>`;

                        var email = `<span class="fas fa-envelope mr-1 text-muted"></span> <small>` + (user.email || "-") + `</small><br>`;
                        var mobile = `<span class="fas fa-phone mr-1 text-muted"></span> <small>` + (user.mobile || "-") + `</small><br>`;
                        var address = `<span class="fas fa-map-marker-alt mr-1 text-muted"></span> <small>` + (user.address || "-") + `</small><br>`;
                        var role_title = `<span class="fas fa-shield-alt mr-1 text-muted"></span> <small>` + (user.role_title || "-") + `</small>`;

                        tblData.row.add([
                            i + 1,
                            profilePhoto,
                            user.username || "-",
                            fullname + email + mobile + address,
                            status,
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },

                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_BENEFICIARY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load beneficiaries.", "error");
                }
            });
        }

        function beneficiaryThumbFallback() {
            return '<div style="width: 50px; height: 50px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center;"><i class="fas fa-user text-muted"></i></div>';
        }
        window.beneficiaryThumbFallback = beneficiaryThumbFallback;

    });
</script>
<?= endSection() ?>
