<?= startSection('css') ?>
<style>
    .uv-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 14px rgba(16, 24, 40, 0.14);
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .uv-avatar-icon {
        font-size: 42px;
        color: #adb5bd;
    }

    .uv-detail-table {
        margin-bottom: 0;
    }

    .uv-detail-table th {
        width: 32%;
        font-weight: 500;
        color: #6b7280;
        padding-left: 0;
        vertical-align: top;
    }

    .uv-detail-table td {
        color: #172b4d;
    }

    .uv-section-title {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>user-list">User</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">View User</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="uvTitle">User Details</h4>
                    <div>
                        <a href="<?= $baseURL ?>edit-user?id=" class="btn btn-primary btn-sm me-1" id="uvEditBtn" style="display:none;">
                            <i class="fas fa-pencil-alt me-1"></i> Edit
                        </a>
                        <a href="<?= $baseURL ?>user-list" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- PROFILE HEADER -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="uv-avatar me-3" id="uvPhotoWrap">
                            <i class="fas fa-user uv-avatar-icon" id="uvPhotoIcon"></i>
                            <img id="uvPhoto" src="" alt="Profile Photo" class="uv-avatar d-none">
                        </div>
                        <div>
                            <h4 class="mb-1" id="uvFullname">-</h4>
                            <p class="text-muted mb-1"><i class="fas fa-user-circle me-1"></i><span id="uvUsername">-</span></p>
                            <div>
                                <span class="badge light badge-info" id="uvRole">-</span>
                                <span class="badge light" id="uvStatus">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- ACCOUNT -->
                        <div class="col-lg-6">
                            <h6 class="text-muted mb-3 uv-section-title"><i class="fas fa-lock me-1"></i> Account</h6>
                            <table class="table table-borderless uv-detail-table">
                                <tbody>
                                    <tr>
                                        <th>Username</th>
                                        <td id="uv_username">-</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td id="uv_role_text">-</td>
                                    </tr>
                                    <tr>
                                        <th>Reference No.</th>
                                        <td id="uv_reference_no">-</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td id="uv_status_text">-</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td id="uv_created_at">-</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td id="uv_updated_at">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- PERSONAL INFO -->
                        <div class="col-lg-6">
                            <h6 class="text-muted mb-3 uv-section-title"><i class="fas fa-user me-1"></i> Personal Info</h6>
                            <table class="table table-borderless uv-detail-table">
                                <tbody>
                                    <tr>
                                        <th>First Name</th>
                                        <td id="uv_fname">-</td>
                                    </tr>
                                    <tr>
                                        <th>Middle Name</th>
                                        <td id="uv_mname">-</td>
                                    </tr>
                                    <tr>
                                        <th>Last Name</th>
                                        <td id="uv_lname">-</td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td id="uv_gender">-</td>
                                    </tr>
                                    <tr>
                                        <th>Marital Status</th>
                                        <td id="uv_marital">-</td>
                                    </tr>
                                    <tr>
                                        <th>Birthday</th>
                                        <td id="uv_birthday">-</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td id="uv_email">-</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td id="uv_mobile">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- ADDRESS -->
                        <div class="col-12">
                            <h6 class="text-muted mb-3 uv-section-title"><i class="fas fa-map-marker-alt me-1"></i> Address</h6>
                            <table class="table table-borderless uv-detail-table">
                                <tbody>
                                    <tr>
                                        <th>Street / Landmark</th>
                                        <td id="uv_street">-</td>
                                    </tr>
                                    <tr>
                                        <th>Full Address</th>
                                        <td id="uv_address">-</td>
                                    </tr>
                                    <tr>
                                        <th>Zip Code</th>
                                        <td id="uv_zip_code">-</td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td id="uv_country">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {

        var userId = new URLSearchParams(window.location.search).get('id');

        if (!userId) {
            Swal.fire("Error", "No user ID provided.", "error").then(function() {
                window.location.href = "<?= $baseURL ?>user-list";
            });
            return;
        }

        $("#uvEditBtn").attr("href", "<?= $baseURL ?>edit-user?id=" + userId);

        function genderLabel(value) {
            value = (value || '').trim().toUpperCase();
            if (value === 'M') return 'Male';
            if (value === 'F') return 'Female';
            return value || '-';
        }

        function maritalLabel(value) {
            var map = { 'S': 'Single', 'M': 'Married', 'D': 'Divorced', 'W': 'Widowed' };
            value = (value || '').trim().toUpperCase();
            return map[value] || value || '-';
        }

        function ageFromBirthday(birthday) {
            if (!birthday) return '';
            var b = new Date(birthday);
            if (isNaN(b.getTime())) return '';
            var now = new Date();
            var age = now.getFullYear() - b.getFullYear();
            var m = now.getMonth() - b.getMonth();
            if (m < 0 || (m === 0 && now.getDate() < b.getDate())) age--;
            return ' (' + age + ' yrs old)';
        }

        function statusBadge(status) {
            if (status == 0) return '<span class="badge light badge-warning">Pending</span>';
            if (status == 1) return '<span class="badge light badge-success">Active</span>';
            return '<span class="badge light badge-danger">Inactive</span>';
        }

        showLoader();
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-users.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                trans: "GET_USER",
                id: userId
            }),
            success: function(res) {
                closeLoader();
                if (res.code != 0 || !res.data) {
                    Swal.fire("Error", res.message || "Failed to load user.", "error");
                    return;
                }

                var d = res.data;
                var p = d.profile || {};

                var fullname = [p.fname, p.mname, p.lname].filter(Boolean).join(' ').trim() || '-';
                var role = d.role || '-';

                $("#uvTitle").text(fullname + " - User Details");
                $("#uvFullname").text(fullname);
                $("#uvUsername").text(d.username || '-');
                $("#uvRole").text(role);

                var statusHtml = statusBadge(d.status);
                $("#uvStatus").html(statusHtml);
                $("#uv_status_text").html(statusHtml);
                $("#uv_role_text").text(role);

                if (p.profile) {
                    $("#uvPhoto").attr("src", "<?= $baseURL ?>assets/images/profile/" + p.profile).removeClass("d-none");
                    $("#uvPhotoIcon").addClass("d-none");
                }

                $("#uv_reference_no").text(p.reference_no || '-');
                $("#uv_created_at").text(d.created_at || '-');
                $("#uv_updated_at").text(d.updated_at || '-');

                $("#uv_fname").text(p.fname || '-');
                $("#uv_mname").text(p.mname || '-');
                $("#uv_lname").text(p.lname || '-');
                $("#uv_gender").text(genderLabel(p.gender));
                $("#uv_marital").text(maritalLabel(p.marital));
                $("#uv_birthday").text((p.birthday || '-') + (p.birthday ? ageFromBirthday(p.birthday) : ''));
                $("#uv_email").text(p.email || '-');
                $("#uv_mobile").text(p.mobile || '-');

                $("#uv_street").text(p.street || '-');
                $("#uv_address").text(p.address || '-');
                $("#uv_zip_code").text(p.zip_code || '-');
                $("#uv_country").text(p.country || '-');

                $("#uvEditBtn").show();
            },
            error: function(xhr) {
                closeLoader();
                console.error("GET_USER failed:", xhr.responseText);
                Swal.fire("Error", "Failed to load user.", "error");
            }
        });

    });
</script>
<?= endSection() ?>
