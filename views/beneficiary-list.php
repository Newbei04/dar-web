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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List Beneficiary</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Beneficiary List</h4>

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

<!-- ================= CHANGE PASSWORD MODAL ================= -->
<div class="modal fade" id="changePasswordModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Update Password</h5>
                    <small class="text-muted" id="passwordUserLabel"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="password_user_id">

                <div class="form-group">
                    <label>New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" placeholder="Enter new password">
                        <button class="btn btn-outline-secondary togglePassword" type="button" data-target="#new_password" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">Use at least 6 characters.</small>
                </div>

                <div class="form-group mb-0">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm new password">
                        <button class="btn btn-outline-secondary togglePassword" type="button" data-target="#confirm_password" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSavePassword">
                    <i class="fas fa-save mr-1"></i> Update Password
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
                    trans: "LIST_BENEFICIARY"
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
                            `<a href="<?= $baseURL ?>beneficiary-view?id=${user.users_id}" class="btn btn-dark mr-1" data-toggle="tooltip" title="View ID Card & Details">
                                <i class="fas fa-id-card"></i>
                            </a>
                            <button class="btn btn-warning mr-1 passBtn" data-id="${user.users_id}" data-username="${user.username || '-'}" data-toggle="tooltip" title="Change Password">
                                <i class="fas fa-key"></i>
                            </button>
                            <a href="<?= $baseURL ?>beneficiary-edit?id=${user.users_id}" class="btn btn-secondary mr-1" data-toggle="tooltip" title="Edit Beneficiary">
                                <i class="fas fa-edit"></i>
                            </a>
                            ${user.status == 0 ? `<a href="<?= $baseURL ?>beneficiary-verify?id=${user.users_id}" class="btn btn-success mr-1" data-toggle="tooltip" title="Verify Beneficiary"><i class="fas fa-check-circle"></i></a>` : ''}
                            `;

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

        /* ---------- CHANGE PASSWORD ---------- */
        $(document).on('click', '.passBtn', function() {
            let id = $(this).data('id');
            let username = $(this).data('username');

            $("#password_user_id").val(id);
            $("#passwordUserLabel").text(username ? `User: ${username}` : '');
            $("#new_password").val('');
            $("#confirm_password").val('');
            $(".togglePassword")
                .attr("aria-label", "Show password")
                .find("i")
                .removeClass("fa-eye-slash")
                .addClass("fa-eye");
            $("#new_password, #confirm_password").attr("type", "password");
            $("#changePasswordModal").modal("show");
        });

        $(document).on('click', '.togglePassword', function() {
            let target = $(this).data('target');
            let input = $(target);
            let icon = $(this).find("i");
            let isPassword = input.attr("type") === "password";

            input.attr("type", isPassword ? "text" : "password");
            icon.toggleClass("fa-eye", !isPassword);
            icon.toggleClass("fa-eye-slash", isPassword);
            $(this).attr("aria-label", isPassword ? "Hide password" : "Show password");
        });

        $("#btnSavePassword").click(function() {
            let id = $("#password_user_id").val();
            let password = $("#new_password").val();
            let confirmPassword = $("#confirm_password").val();

            if (!password || !confirmPassword) {
                Swal.fire("Validation", "Please enter and confirm the new password.", "warning");
                return;
            }

            if (password.length < 6) {
                Swal.fire("Validation", "Password must be at least 6 characters.", "warning");
                return;
            }

            if (password !== confirmPassword) {
                Swal.fire("Validation", "Passwords do not match.", "warning");
                return;
            }

            Swal.fire({
                title: "Update password?",
                text: "The user will need to use the new password on their next login.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, update"
            }).then((result) => {
                if (!result.isConfirmed) return;

                $("#btnSavePassword")
                    .prop("disabled", true)
                    .html(`<span class="spinner-border spinner-border-sm mr-1"></span> Updating...`);

                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-auth.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "CHANGE_PASSWORD",
                        id: id,
                        password: password
                    }),
                    success: function(res) {
                        $("#btnSavePassword")
                            .prop("disabled", false)
                            .html(`<i class="fas fa-save mr-1"></i> Update Password`);

                        if (res.code == 0) {
                            $("#changePasswordModal").modal("hide");
                            Swal.fire("Success", res.message, "success");
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        $("#btnSavePassword")
                            .prop("disabled", false)
                            .html(`<i class="fas fa-save mr-1"></i> Update Password`);

                        console.error("CHANGE_PASSWORD failed:", xhr.responseText);
                        Swal.fire("Error", "Failed to update password.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>
