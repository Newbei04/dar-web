<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Datatable</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Users List</h4>

                    <a href="<?= $basePath ?>/add-user" class="btn btn-primary">
                        <i class="fe fe-plus mr-1"></i> New User
                    </a>
                </div>

                <div class="card-body">
                    <table id="tblUsers" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="8%">Photo</th>
                                <th width="20%">Username</th>
                                <th>User Information</th>
                                <th width="10%">Status</th>
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

<!-- ================= VIEW USER MODAL ================= -->
<div class="modal fade" id="viewUserModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="35%" class="text-muted">Username</th>
                        <td id="view_username">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Name</th>
                        <td id="view_fullname">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email</th>
                        <td id="view_email">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Mobile</th>
                        <td id="view_mobile">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Address</th>
                        <td id="view_address">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Role</th>
                        <td id="view_role">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td id="view_status">-</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
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
                    <h5 class="modal-title" id="changePasswordModalLabel">Update Password</h5>
                    <small class="text-muted" id="passwordUserLabel"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
            </div>
            <div class="modal-body">
                <input type="hidden" id="password_user_id">

                <div class="form-group">
                    <label>New Password</label>
                    <div class="position-relative">
                        <input type="password" class="form-control" id="new_password" placeholder="Enter new password">
                        <span class="show-pass eye togglePassword" data-target="#new_password" aria-label="Show password">
                            <i class="fa fa-eye-slash"></i>
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <small class="form-text text-muted">Use at least 6 characters.</small>
                </div>

                <div class="form-group mb-0">
                    <label>Confirm Password</label>
                    <div class="position-relative">
                        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm new password">
                        <span class="show-pass eye togglePassword" data-target="#confirm_password" aria-label="Show password">
                            <i class="fa fa-eye-slash"></i>
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSavePassword">
                    <i class="fe fe-save mr-1"></i> Update Password
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

        var tblData = $('#tblUsers').DataTable();

        loadUsers();

        function loadUsers() {
            showLoader();
            tblData.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-users.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_USER"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load users.", "error");
                        return;
                    }
                    var data = res.data || [];
                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(user, i) {

                        var profile = user.profile || {};

                        var fullname = [profile.fname, profile.mname, profile.lname]
                            .filter(Boolean)
                            .join(' ') || "-";

                        var status = user.status == 0 ? '<span class="badge badge-warning btn">Pending</span>' :
                            user.status == 1 ? '<span class="badge light badge-success">Active</span>' : '<span class="badge light badge-danger">Inactive</span>';

                        var profilePhoto = profile.profile ?
                            `<img src="<?= $baseURL ?>/assets/images/profile/${profile.profile}" alt="Profile" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">` :
                            `<div style="width: 50px; height: 50px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center;"><i class="fas fa-user text-muted"></i></div>`;

                        var actions = `
                        <button class="btn btn-info mr-2 viewBtn" data-id="${user.id}" data-username="${user.username || '-'}" data-fullname="${fullname.replace(/<[^>]*>/g, '').trim()}" data-email="${profile.email || '-'}" data-mobile="${profile.mobile || '-'}" data-address="${profile.address || '-'}" data-role="${user.role || '-'}" data-status="${user.status}" data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-primary shadow editBtn" data-id="${user.id}" data-toggle="tooltip" title="Update Details">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                        <button class="btn btn-warning mr-2 passBtn" data-id="${user.id}" data-username="${user.username || '-'}" data-toggle="tooltip" title="Change Password">
                            <i class="fas fa-key"></i>
                        </button>
                        `;
                        var fullname =
                            `<span class="fas fa-user"></span> ` +
                            ((profile.fname || "") + " " + (profile.mname || "") + " " + (profile.lname || "")).trim() +
                            `<br>`;
                        var email =
                            `<span class="fas fa-mail"></span> <small>` + (profile.email || "-") + `</small><br>`;
                        var mobile =
                            `<span class="fas fa-phone"></span> <small>` + (profile.mobile || "-") + `</small><br>`;
                        var address =
                            `<span class="fas fa-map-pin"></span> <small>` + (profile.address || "-") + `</small><br>`;
                        var role = `<span class="fas fa-shield"></span> <small>` + (user.role || "-") + `</small>`;
                        tblData.row.add([
                            i + 1,
                            profilePhoto,
                            user.username || "-",
                            fullname + email + mobile + address + role,
                            status,
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },

                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_USER failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load users.", "error");
                }
            });
        }

        $(document).on('click', '.viewBtn', function() {
            let statusText = $(this).data('status') == 0 ? 'Pending' : $(this).data('status') == 1 ? 'Active' : 'Inactive';
            let statusBadge = $(this).data('status') == 0 ? 'badge-warning' : $(this).data('status') == 1 ? 'badge-success' : 'badge-danger';
            $('#view_username').text($(this).data('username') || '-');
            $('#view_fullname').text($(this).data('fullname') || '-');
            $('#view_email').text($(this).data('email') || '-');
            $('#view_mobile').text($(this).data('mobile') || '-');
            $('#view_address').text($(this).data('address') || '-');
            $('#view_role').text($(this).data('role') || '-');
            $('#view_status').html(`<span class="badge ${statusBadge}">${statusText}</span>`);
            $('#viewUserModal').modal('show');
        });

        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');
            window.location.href = "<?= $basePath ?>/edit-user?id=" + id;
        });

        $(document).on('click', '.passBtn', function() {
            let id = $(this).data('id');
            let username = $(this).data('username');

            $("#password_user_id").val(id);
            $("#passwordUserLabel").text(username ? `User: ${username}` : '');
            $("#new_password").val('');
            $("#confirm_password").val('');
            $(".togglePassword")
                .attr("aria-label", "Show password")
                .removeClass("active");
            $("#new_password, #confirm_password").attr("type", "password");
            $("#changePasswordModal").modal("show");
        });

        $(document).on('click', '.togglePassword', function() {
            let target = $(this).data('target');
            let input = $(target);
            let isPassword = input.attr("type") === "password";

            input.attr("type", isPassword ? "text" : "password");
            $(this).toggleClass("active", !isPassword);
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
                    url: "<?= $baseURL ?>/controller/ctrl-auth.php",
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
                            .html(`<i class="fe fe-save mr-1"></i> Update Password`);

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
                            .html(`<i class="fe fe-save mr-1"></i> Update Password`);

                        console.error("CHANGE_PASSWORD failed:", xhr.responseText);
                        Swal.fire("Error", "Failed to update password.", "error");
                    }
                });
            });
        });

        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: "Delete User?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete"
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: "<?= $baseURL ?>/controller/ctrl-users.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "DELETE_USER",
                        id: id
                    }),
                    success: function(res) {
                        if (res.code == 0) {
                            Swal.fire("Deleted", res.message, "success");
                            loadUsers();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>