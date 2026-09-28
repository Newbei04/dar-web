<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/css/user-table.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">User</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List User</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card user-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users-cog me-2 text-primary"></i>Users List
                    </h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge-status badge-neutral" id="userCount">0 total</span>
                        <a href="<?= $baseURL ?>add-user" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> New User
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblUsers" class="display responsive nowrap w-100 user-table">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th width="6%">Photo</th>
                                <th width="15%">Username</th>
                                <th>User Information</th>
                                <th width="12%">Status</th>
                                <th width="14%">Actions</th>
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
            <div class="modal-header modal-header-muted">
                <div>
                    <h5 class="modal-title" id="changePasswordModalLabel">Update Password</h5>
                    <small class="text-muted" id="passwordUserLabel"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
            </div>
            <div class="modal-body modal-body-sub">
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
                    <i class="fa fa-save me-1"></i> Update Password
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

        var tblData = $('#tblUsers').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            bFilter: false,
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6">>rtip',
            columnDefs: [
                { orderable: false, targets: [5] }
            ],
            order: [[2, 'asc']]
        });

        function refreshTooltips() {
            if (!window.bootstrap || !bootstrap.Tooltip) return;
            var $tips = $('#tblUsers tbody [data-bs-toggle="tooltip"]');
            $tips.each(function() {
                var inst = bootstrap.Tooltip.getInstance(this);
                if (inst) inst.dispose();
            });
            $tips.each(function() {
                new bootstrap.Tooltip(this);
            });
        }

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
                        $('#userCount').text('0 total');
                        tblData.draw(false);
                        refreshTooltips();
                        return;
                    }
                    data.forEach(function(user, i) {

                        var profile = user.profile || {};

                        var status = user.status == 0
                                ? '<span class="badge-status badge-pending-status"><i class="fas fa-clock me-1"></i>Pending</span>'
                                : user.status == 1
                                ? '<span class="badge-status badge-active"><i class="fas fa-check-circle me-1"></i>Active</span>'
                                : '<span class="badge-status badge-inactive"><i class="fas fa-ban me-1"></i>Inactive</span>';

                        var displayName = [profile.fname, profile.mname, profile.lname]
                            .filter(Boolean).join(' ').trim() || '-';

                        var initials = (((profile.fname || '').charAt(0) || '') + ((profile.lname || '').charAt(0) || '')) || '?';

                        var profilePhoto = `<span class="user-avatar">` + (profile.profile ?
                            `<img src="<?= $baseURL ?>assets/images/profile/${encodeURIComponent(profile.profile)}" alt="Profile" data-init="${escapeAttr(initials)}" onerror="this.outerHTML=userAvatarFallback(this.dataset.init);">` :
                            escapeHtml(initials)) + `</span>`;

                        var actions = `
                        <button class="btn-action btn-view viewBtn" data-id="${user.id}" data-bs-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit editBtn" data-id="${user.id}" data-bs-toggle="tooltip" title="Update Details">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-password passBtn" data-id="${user.id}" data-username="${escapeAttr(user.username || '-')}" data-bs-toggle="tooltip" title="Change Password">
                            <i class="fas fa-key"></i>
                        </button>
                        `;

                        var username = user.username || '';
                        var info = `
                        <div class="user-name-cell">${escapeHtml(displayName)}</div>
                        <div class="user-contact-cell"><i class="fas fa-envelope"></i><span title="${escapeAttr(profile.email || '')}">${escapeHtml(profile.email || "-")}</span></div>
                        <div class="user-contact-cell"><i class="fas fa-phone"></i><span title="${escapeAttr(profile.mobile || '')}">${escapeHtml(profile.mobile || "-")}</span></div>
                        <div class="user-contact-cell"><i class="fas fa-map-marker-alt"></i><span title="${escapeAttr(profile.address || '')}">${escapeHtml(profile.address || "-")}</span></div>
                        <div class="user-contact-cell"><i class="fas fa-shield-alt"></i><span title="${escapeAttr(user.role || '')}">${escapeHtml(user.role || "-")}</span></div>
                        `;

                        tblData.row.add([
                            i + 1,
                            profilePhoto,
                            username ? `<span class="user-username-cell" title="${escapeAttr(username)}">${escapeHtml(username)}</span>` : '<span class="text-muted">—</span>',
                            info,
                            status,
                            actions
                        ]);
                    });

                    $('#userCount').text(data.length + ' total');
                    tblData.draw(false);
                    refreshTooltips();
                },

                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_USER failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load users.", "error");
                }
            });
        }

        $(document).on('click', '.viewBtn', function() {
            let id = $(this).data('id');
            window.location.href = "<?= $baseURL ?>view-user?id=" + id;
        });

        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');
            window.location.href = "<?= $baseURL ?>edit-user?id=" + id;
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
                    .html(`<span class="spinner-border spinner-border-sm me-1"></span> Updating...`);

                showLoader();
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
                        closeLoader();
                        $("#btnSavePassword")
                            .prop("disabled", false)
                            .html(`<i class="fa fa-save me-1"></i> Update Password`);

                        if (res.code == 0) {
                            $("#changePasswordModal").modal("hide");
                            Swal.fire("Success", res.message, "success");
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        closeLoader();
                        $("#btnSavePassword")
                            .prop("disabled", false)
                            .html(`<i class="fa fa-save me-1"></i> Update Password`);

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
                showLoader();
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
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Deleted", res.message, "success");
                            loadUsers();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        closeLoader();
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            });
        });

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : value).html();
        }

        function escapeAttr(value) {
            return $('<div>').text(value == null ? '' : value).html().replace(/"/g, '&quot;');
        }

        function userAvatarFallback(init) {
            return escapeHtml(init || '?');
        }
        window.userAvatarFallback = userAvatarFallback;

    });
</script>
<?= endSection() ?>