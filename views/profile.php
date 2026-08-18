<?= startSection('css') ?>
<style>
    .profile-cover {
        height: 150px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6b7aff 0%, #7c4dff 60%, #a855f7 100%);
        position: relative;
        overflow: hidden;
    }

    .profile-cover::after {
        content: "";
        position: absolute;
        top: -60px;
        right: -40px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .12);
    }

    .profile-cover::before {
        content: "";
        position: absolute;
        bottom: -80px;
        left: 10%;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
    }

    .profile-head {
        margin-top: -56px;
        padding: 0 8px;
        position: relative;
    }

    .profile-avatar-wrap {
        width: 112px;
        height: 112px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .18);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .profile-avatar-wrap .active-dot {
        font-size: 15px;
        margin: 3px;
        z-index: 2;
    }

    .profile-avatar-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .profile-avatar-wrap .profile-avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6b7aff 0%, #7c4dff 100%);
        color: #fff;
        font-size: 38px;
        font-weight: 700;
        border-radius: 50%;
    }

    .profile-stat {
        text-align: center;
        padding: 16px 8px;
        border-radius: 12px;
        background: var(--bs-tertiary-bg);
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .profile-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
    }

    .profile-stat .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--bs-primary-bg-subtle);
        color: var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 16px;
    }

    .profile-stat .value {
        font-size: 17px;
        font-weight: 700;
        color: var(--bs-body-color);
        line-height: 1.3;
        word-break: break-word;
    }

    .profile-stat .label {
        font-size: 11px;
        color: var(--bs-secondary-color);
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-top: 2px;
    }

    .detail-card .card-body {
        padding-top: 0;
        padding-bottom: 0;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px dashed var(--bs-border-color);
        font-size: 14px;
    }

    .detail-row:last-child {
        border-bottom: 0;
    }

    .detail-row span {
        color: var(--bs-secondary-color);
        flex-shrink: 0;
    }

    .detail-row strong {
        color: var(--bs-body-color);
        font-weight: 600;
        text-align: right;
        word-break: break-word;
        min-width: 0;
    }

    #avatarPreview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--bs-secondary-bg);
        border: 2px dashed var(--bs-border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        cursor: pointer;
        overflow: hidden;
    }

    #avatarImg {
        display: none;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .pf-wallet-card { border-left: 4px solid #198754; }
    .pf-wallet-card .wallet-stat { text-align: center; padding: 8px 0; }
    .pf-wallet-card .wallet-stat .stat-value { font-size: 1.2rem; font-weight: 700; }
    .pf-wallet-card .wallet-stat .stat-label { font-size: 0.7rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-frozen { background: #dc3545; color: #fff; }
    .badge-active { background: #198754; color: #fff; }
    .balance-type-badge { font-size: 0.75rem; padding: 4px 8px; }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>home">Home</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">My Profile</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">My Profile</h4>
                    <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="cancelEditBtn">
                        <i class="fas fa-arrow-left me-1"></i> Back to View
                    </button>
                </div>
                <div class="card-body">

                    <!-- ==================== VIEW MODE ==================== -->
                    <div id="viewMode">
                        <!-- COVER BANNER -->
                        <div class="profile-cover mb-4"></div>

                        <!-- PROFILE HEAD -->
                        <div class="profile-head d-flex flex-column flex-md-row align-items-center gap-3 pb-4 mb-3">
                            <div id="profileAvatarWrap" class="profile-avatar-wrap"></div>
                            <div class="text-center text-md-start me-auto">
                                <h4 class="mb-1" id="profileFullname">—</h4>
                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 align-items-center">
                                    <span class="badge light badge-primary" id="profileRole">—</span>
                                    <span id="profileStatus"></span>
                                </div>
                                <div class="text-muted mt-1" id="profileReference">—</div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="editProfileBtn">
                                <i class="fas fa-edit me-1"></i> Edit Profile
                            </button>
                        </div>

                        <!-- PROFILE TABS -->
                        <ul class="nav nav-underline gap-3 nav-scroll nav-scroll-auto-xl mb-4 border-bottom" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link py-3 px-1 border-3 active" id="tabOverview">Overview</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="javascript:void(0);" class="nav-link py-3 px-1 border-3" id="tabSettings">Settings</a>
                            </li>
                        </ul>

                        <!-- QUICK STATS -->
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-lg-3">
                                <div class="profile-stat">
                                    <div class="stat-icon"><i class="fas fa-user"></i></div>
                                    <div class="value" id="statUsername">—</div>
                                    <div class="label">Username</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="profile-stat">
                                    <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                                    <div class="value" id="statEmail">—</div>
                                    <div class="label">Email</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="profile-stat">
                                    <div class="stat-icon"><i class="fas fa-phone-alt"></i></div>
                                    <div class="value" id="statMobile">—</div>
                                    <div class="label">Mobile</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="profile-stat">
                                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                                    <div class="value" id="statMemberSince">—</div>
                                    <div class="label">Member Since</div>
                                </div>
                            </div>
                        </div>

                        <!-- DETAIL CARDS -->
                        <div class="row g-3">
                            <!-- PERSONAL INFORMATION -->
                            <div class="col-lg-6 col-xl-4">
                                <div class="card detail-card h-100 mb-0">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Personal Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="detail-row"><span>First Name</span><strong id="pfFname">—</strong></div>
                                        <div class="detail-row"><span>Middle Name</span><strong id="pfMname">—</strong></div>
                                        <div class="detail-row"><span>Last Name</span><strong id="pfLname">—</strong></div>
                                        <div class="detail-row"><span>Gender</span><strong id="pfGender">—</strong></div>
                                        <div class="detail-row"><span>Marital Status</span><strong id="pfMarital">—</strong></div>
                                        <div class="detail-row"><span>Birthday</span><strong id="pfBirthday">—</strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTACT -->
                            <div class="col-lg-6 col-xl-4">
                                <div class="card detail-card h-100 mb-0">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="fas fa-address-book me-2 text-success"></i>Contact</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="detail-row"><span>Email Address</span><strong id="pfEmail">—</strong></div>
                                        <div class="detail-row"><span>Mobile Number</span><strong id="pfMobile">—</strong></div>
                                        <div class="detail-row"><span>Facility</span><strong id="pfFacility">—</strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- ADDRESS -->
                            <div class="col-lg-12 col-xl-4">
                                <div class="card detail-card h-100 mb-0">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2 text-warning"></i>Address</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="detail-row"><span>Region</span><strong id="pfRegion">—</strong></div>
                                        <div class="detail-row"><span>Province</span><strong id="pfProvince">—</strong></div>
                                        <div class="detail-row"><span>City / Municipality</span><strong id="pfCity">—</strong></div>
                                        <div class="detail-row"><span>Barangay</span><strong id="pfBarangay">—</strong></div>
                                        <div class="detail-row"><span>Street / Landmark</span><strong id="pfStreet">—</strong></div>
                                        <div class="detail-row"><span>Zip Code</span><strong id="pfZip">—</strong></div>
                                        <div class="detail-row"><span>Country</span><strong id="pfCountry">—</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- WALLET SUMMARY -->
                        <div id="walletSection" class="mt-4" style="display:none;">
                            <div class="card pf-wallet-card">
                                <div class="card-header d-flex align-items-center">
                                    <h6 class="card-title mb-0"><i class="fas fa-wallet me-2 text-success"></i>Wallet Summary</h6>
                                    <span class="badge bg-success ms-auto" id="walletCountBadge">0</span>
                                </div>
                                <div class="card-body" id="walletBody">
                                    <!-- single wallet: beneficiary view -->
                                    <div id="walletSingleView" style="display:none;">
                                        <div class="row g-3 text-center mb-3">
                                            <div class="col-6 col-md-3">
                                                <div class="wallet-stat">
                                                    <div class="stat-value text-success" id="pfWBalance">₱0.00</div>
                                                    <div class="stat-label">Total Balance</div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="wallet-stat">
                                                    <div class="stat-value" id="pfWCreditLimit">₱0.00</div>
                                                    <div class="stat-label">Credit Limit</div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="wallet-stat">
                                                    <div class="stat-value" id="pfWAccountNum">—</div>
                                                    <div class="stat-label">Account No.</div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="wallet-stat">
                                                    <div id="pfWStatus">—</div>
                                                    <div class="stat-label">Status</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr><th>Program</th><th>Balance Type</th><th class="text-end">Amount</th></tr>
                                                </thead>
                                                <tbody id="pfWBalances"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- multiple wallets: admin view -->
                                    <div id="walletMultiView" style="display:none;">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover align-middle mb-0" id="pfWTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Account No.</th>
                                                        <th>Beneficiary</th>
                                                        <th>Branch</th>
                                                        <th class="text-end">Balance</th>
                                                        <th class="text-end">Credit Limit</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="pfWTableBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="walletEmpty" class="text-center text-muted py-4" style="display:none;">
                                        <i class="fas fa-wallet fa-2x mb-2 d-block"></i>
                                        No wallet found.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== EDIT MODE ==================== -->
                    <div id="editMode" style="display:none;">
                        <form id="profileForm">
                            <!-- ACCOUNT -->
                            <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Account</small></h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="username" placeholder="Enter username" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" placeholder="Leave blank to keep current password">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- PERSONAL INFO -->
                            <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Personal Info</small></h6>

                            <!-- PROFILE PHOTO -->
                            <div class="text-center mb-4">
                                <div id="avatarPreview">
                                    <i class="fas fa-user fa-2x text-muted" id="avatarIcon"></i>
                                    <img id="avatarImg" src="" alt="Profile Photo">
                                </div>
                                <input type="file" id="profile_photo" accept="image/*" style="display:none;">
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="$('#profile_photo').click()">
                                        <i class="fas fa-camera me-1"></i> Upload Photo
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                                        id="removePhoto" style="display:none;"
                                        onclick="removeProfilePhoto()">
                                        <i class="fas fa-times me-1"></i> Remove
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">JPG, PNG or GIF · Max 2MB</small>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fname" placeholder="First name" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="mname" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="mname" placeholder="Middle name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="lname" placeholder="Last name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="birthday" class="form-label">Birthday <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="birthday" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select id="gender" class="form-control single-select" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="M">Male</option>
                                            <option value="F">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="marital" class="form-label">Marital Status <span class="text-danger">*</span></label>
                                        <select id="marital" class="form-control single-select" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="S">Single</option>
                                            <option value="M">Married</option>
                                            <option value="D">Divorced</option>
                                            <option value="W">Widowed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" placeholder="email@example.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="mobile" class="form-label">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mobile" placeholder="09XXXXXXXXX" required>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- ADDRESS -->
                            <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Address</small></h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="region" class="form-label">Region <span class="text-danger">*</span></label>
                                        <select class="form-control single-select" id="region">
                                            <option value="">Select Region</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="provinceField">
                                    <div class="mb-3">
                                        <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                                        <select class="form-control single-select" id="province" disabled>
                                            <option value="">Select Province</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City / Municipality <span class="text-danger">*</span></label>
                                        <select class="form-control single-select" id="city" disabled>
                                            <option value="">Select City</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="submuniField" style="display:none;">
                                    <div class="mb-3">
                                        <label for="submuni" class="form-label">District</label>
                                        <select class="form-control single-select" id="submuni" disabled>
                                            <option value="">Select District</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                                        <select class="form-control single-select" id="barangay" disabled>
                                            <option value="">Select Barangay</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="street" class="form-label">Street / Landmark</label>
                                        <textarea class="form-control" id="street" rows="2" placeholder="House no., street, landmark..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary me-2" id="cancelEditFormBtn">Cancel</button>
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="fa fa-save me-1"></i> Save Profile
                                </button>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <!-- ================= DANGER ZONE ================= -->
                                    <div class="card border-danger mt-4">
                                        <div class="card-header bg-danger-light border-danger d-flex justify-content-between align-items-center">
                                            <h4 class="card-title text-danger mb-0">
                                                <i class="fas fa-exclamation-triangle me-2"></i> Danger Zone
                                            </h4>
                                        </div>
                                        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                            <div>
                                                <h6 class="mb-1 fw-semibold text-danger">Delete this user</h6>
                                                <p class="text-muted mb-0" style="font-size: 14px;">
                                                    This action is permanent and cannot be undone. Once deleted,
                                                    the user account and its profile will be removed from the system.
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-danger px-4" id="btnDeleteUser">
                                                    <i class="fas fa-trash me-1"></i> Delete User
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    const MANILA_CODE = "133900000";
    const NCR_CODE = "130000000";
    const API = "<?= $baseURL ?>controller/ctrl-users.php";
    const API_URL = "<?= $baseURL ?>controller/ctrl-location.php";
    const BASE = "<?= $baseURL ?>";
    const SESSION_USER_ID = "<?= $_SESSION['users_id'] ?? '' ?>";
    const SESSION_ROLE_ID = <?= (int)($_SESSION['role_id'] ?? 0) ?>;
    const WALLET_API = "<?= $baseURL ?>controller/ctrl-wallet.php";

    const ROLE_MAP = {
        1: 'Administrator',
        2: 'Employee / Cooperative',
        3: 'Beneficiary (ARB)'
    };

    const GENDER_MAP = {
        'Male': 'M',
        'Female': 'F'
    };
    const MARITAL_MAP = {
        'Single': 'S',
        'Married': 'M',
        'Divorced': 'D',
        'Widowed': 'W'
    };

    let profile = null;
    let cascading = false;

    $(document).ready(function() {

        loadProfile();

        // ── Photo upload (edit mode) ─────────────────────────────
        $("#avatarPreview").on("click", function() {
            $("#profile_photo").click();
        });

        $("#profile_photo").on("change", function() {
            let file = this.files[0];
            if (!file) return;

            if (!file.type.startsWith("image/")) {
                Swal.fire("Invalid File", "Please select a valid image file.", "warning");
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire("File Too Large", "Maximum allowed size is 2MB.", "warning");
                return;
            }

            let reader = new FileReader();
            reader.onload = function(e) {
                profile = e.target.result;
                $("#avatarImg").attr("src", e.target.result).show();
                $("#avatarIcon").hide();
                $("#removePhoto").show();
                $("#avatarPreview").css("border", "2px solid #ced4da");
            };
            reader.readAsDataURL(file);
        });

        window.removeProfilePhoto = function() {
            profile = null;
            $("#profile_photo").val("");
            $("#avatarImg").attr("src", "").hide();
            $("#avatarIcon").show();
            $("#removePhoto").hide();
            $("#avatarPreview").css("border", "2px dashed #ced4da");
        };

        // ── Mode toggles ─────────────────────────────────────────
        function setActiveTab(tab) {
            $('#tabOverview').toggleClass('active', tab === 'overview');
            $('#tabSettings').toggleClass('active', tab === 'settings');
        }

        $("#editProfileBtn").on("click", function() {
            enterEditMode();
        });

        $("#tabOverview").on("click", function() {
            leaveEditMode();
        });

        $("#tabSettings").on("click", function() {
            enterEditMode();
        });

        $("#cancelEditBtn").on("click", function() {
            leaveEditMode();
        });

        $("#cancelEditFormBtn").on("click", function() {
            leaveEditMode();
        });

        /* ================= VIEW MODE ================= */

        function setText(id, val) {
            $('#' + id).text((val === null || val === undefined || val === '') ? '-' : val);
        }

        function initials(fname, lname) {
            return ((fname || '').charAt(0) || '') + ((lname || '').charAt(0) || '') || '?';
        }

        function fmtDate(d) {
            if (!d) return '-';
            const x = new Date(d);
            if (isNaN(x.getTime())) return d;
            return x.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function findName(list, code) {
            if (!list) return null;
            for (let i = 0; i < list.length; i++) {
                if (list[i].code == code) return list[i].name;
            }
            return null;
        }

        function locAjax(payload, cb) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(payload),
                success: function(res) {
                    cb(res && res.code == 0 ? res.data : null);
                },
                error: function() {
                    cb(null);
                }
            });
        }

        function resolveAddress(p) {
            locAjax({
                trans: "region"
            }, function(regions) {
                let regionName = p.region_id ? findName(regions, p.region_id) : null;
                setText('pfRegion', regionName);
                if (!regionName) return;

                if (p.region_id == NCR_CODE) {
                    locAjax({
                        trans: "ncr_city"
                    }, function(cities) {
                        let cityName = p.city_id ? findName(cities, p.city_id) : null;
                        setText('pfCity', cityName);
                        if (cityName) {
                            locAjax({
                                trans: "barangay",
                                code: p.city_id
                            }, function(bgys) {
                                setText('pfBarangay', p.barangay_id ? findName(bgys, p.barangay_id) : null);
                            });
                        }
                    });
                    return;
                }

                locAjax({
                    trans: "province",
                    code: p.region_id
                }, function(provinces) {
                    let provinceName = p.province_id ? findName(provinces, p.province_id) : null;
                    setText('pfProvince', provinceName);
                    if (!provinceName) return;

                    locAjax({
                        trans: "city",
                        code: p.province_id
                    }, function(cities) {
                        let cityName = p.city_id ? findName(cities, p.city_id) : null;
                        setText('pfCity', cityName);
                        if (cityName) {
                            locAjax({
                                trans: "barangay",
                                code: p.city_id
                            }, function(bgys) {
                                setText('pfBarangay', p.barangay_id ? findName(bgys, p.barangay_id) : null);
                            });
                        }
                    });
                });
            });
        }

        function loadProfile() {
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_USER",
                    id: SESSION_USER_ID
                }),
                success: function(res) {
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Unable to load profile", "error");
                        return;
                    }
                    renderView(res.data);
                },
                error: function() {
                    Swal.fire("Error", "Unable to load profile", "error");
                }
            });
        }

        function renderView(u) {
            let p = u.profile || {};

            let fullname = [p.fname, p.mname, p.lname].filter(Boolean).join(' ') || u.username || '-';
            $('#profileFullname').text(fullname);

            let activeDot = '<span class="fa fa-circle border border-3 border-white text-success position-absolute bottom-0 end-0 rounded-circle active-dot"></span>';
            if (p.profile) {
                $('#profileAvatarWrap').html(
                    `<img src="${BASE}assets/images/profile/${p.profile}" class="profile-avatar" alt="Profile Photo">${activeDot}`
                );
            } else {
                $('#profileAvatarWrap').html(
                    `<div class="profile-avatar-fallback">${initials(p.fname, p.lname)}</div>${activeDot}`
                );
            }

            $('#profileRole').text(ROLE_MAP[u.role_id] || 'User');
            let active = parseInt(u.status) == 1;
            $('#profileStatus').html(active ?
                '<span class="badge light badge-success">Active</span>' :
                '<span class="badge light badge-danger">Inactive</span>');
            setText('profileReference', 'Reference No: ' + (p.reference_no || '-'));

            setText('statUsername', u.username);
            setText('statEmail', p.email);
            setText('statMobile', p.mobile);
            setText('statMemberSince', fmtDate(p.created_at));

            setText('pfFname', p.fname);
            setText('pfMname', p.mname);
            setText('pfLname', p.lname);
            setText('pfGender', p.gender);
            setText('pfMarital', p.marital);
            setText('pfBirthday', fmtDate(p.birthday));

            setText('pfEmail', p.email);
            setText('pfMobile', p.mobile);

            setText('pfStreet', p.street);
            setText('pfZip', p.zip_code);
            setText('pfCountry', p.country);
            resolveAddress(p);

            if (p.facility_id) {
                $.ajax({
                    url: API,
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "GET_ALL_FACILITY"
                    }),
                    success: function(fres) {
                        if (fres.code == 0) {
                            let fac = (fres.data || []).find(f => f.id == p.facility_id);
                            setText('pfFacility', fac ? fac.name : null);
                        }
                    }
                });
            }
        }

        /* ================= EDIT MODE ================= */

        function enterEditMode() {
            // Re-fetch fresh data so the form reflects the latest saved profile
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_USER",
                    id: SESSION_USER_ID
                }),
                success: function(res) {
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Unable to load profile", "error");
                        return;
                    }
                    fillEditForm(res.data);
                    getRegionList(function() {
                        let u = res.data;
                        let p = u.profile || {};
                        cascading = true;
                        setRegion(p.region_id, function() {
                            setProvince(p.province_id, function() {
                                setCity(p.city_id, function() {
                                    setSubmuni(p.district_id, function() {
                                        $("#barangay").val(p.barangay_id);
                                        $("#region, #province, #city, #submuni, #barangay").trigger('change.select2');
                                        cascading = false;
                                    });
                                });
                            });
                        });
                    });
                },
                error: function() {
                    Swal.fire("Error", "Unable to load profile", "error");
                }
            });
        }

        function fillEditForm(u) {
            let p = u.profile || {};

            $("#username").val(u.username || '');
            $("#password").val('');
            $("#fname").val(p.fname || '');
            $("#mname").val(p.mname || '');
            $("#lname").val(p.lname || '');

            $("#gender").val(GENDER_MAP[p.gender] || p.gender || '').trigger('change');
            $("#marital").val(MARITAL_MAP[p.marital] || p.marital || '').trigger('change');
            $("#birthday").val(p.birthday ? p.birthday.slice(0, 10) : '');

            $("#email").val(p.email || '');
            $("#mobile").val(p.mobile || '');
            $("#street").val(p.street || '');

            // Existing photo
            profile = null;
            $("#profile_photo").val("");
            if (p.profile) {
                $("#avatarImg").attr("src", BASE + "assets/images/profile/" + p.profile).show();
                $("#avatarIcon").hide();
                $("#removePhoto").show();
                $("#avatarPreview").css("border", "2px solid #ced4da");
            } else {
                $("#avatarImg").attr("src", "").hide();
                $("#avatarIcon").show();
                $("#removePhoto").hide();
                $("#avatarPreview").css("border", "2px dashed #ced4da");
            }

            $("#viewMode").hide();
            $("#editMode").show();
            $("#editProfileBtn").addClass('d-none');
            $("#cancelEditBtn").removeClass('d-none');
            setActiveTab('settings');
        }

        function leaveEditMode() {
            $("#editMode").hide();
            $("#viewMode").show();
            $("#cancelEditBtn").addClass('d-none');
            $("#editProfileBtn").removeClass('d-none');
            setActiveTab('overview');
        }

        /* ================= SAVE PROFILE ================= */

        $("#profileForm").submit(function(e) {
            e.preventDefault();
            showLoader();

            let address = [
                    $("#street").val(),
                    $("#barangay option:selected").text(),
                    $("#submuni option:selected").text(),
                    $("#city option:selected").text(),
                    $("#province option:selected").text(),
                    $("#region option:selected").text()
                ]
                .filter(v => v && v.trim() !== '' && !v.startsWith('Select'))
                .join(', ');

            let postData = {
                trans: "UPDATE_USER",
                id: SESSION_USER_ID,
                username: $("#username").val(),
                fname: $("#fname").val(),
                mname: $("#mname").val(),
                lname: $("#lname").val(),
                gender: $("#gender").val(),
                marital: $("#marital").val(),
                birthday: $("#birthday").val(),
                email: $("#email").val(),
                mobile: $("#mobile").val(),
                profile: profile,
                street: $("#street").val(),
                barangay_id: $("#barangay").val(),
                city_id: $("#city").val(),
                province_id: $("#province").val(),
                region_id: $("#region").val(),
                district_id: $("#submuni").val() || null,
                country: "PH",
                address: address
            };

            if ($("#password").val()) {
                postData.password = $("#password").val();
            }

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(postData),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success").then(() => {
                            leaveEditMode();
                            loadProfile();
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    closeLoader();
                    Swal.fire("Error", "Something went wrong. Please try again.", "error");
                }
            });
        });

        // ================= LOCATION FUNCTIONS =================

        function getRegionList(callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "region"
                }),
                success: function(res) {
                    let d = $("#region");
                    d.html('<option value="">Select Region</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    console.error("Failed to load regions");
                    if (callback) callback();
                }
            });
        }

        function getProvinceList(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "province",
                    code: code
                }),
                success: function(res) {
                    let d = $("#province");
                    d.prop("disabled", false).html('<option value="">Select Province</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    console.error("Failed to load provinces");
                    if (callback) callback();
                }
            });
        }

        function getCityList(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "city",
                    code: code
                }),
                success: function(res) {
                    let d = $("#city");
                    d.prop("disabled", false).html('<option value="">Select City</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    console.error("Failed to load cities");
                    if (callback) callback();
                }
            });
        }

        function getNCRCityList(callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ncr_city"
                }),
                success: function(res) {
                    let d = $("#city");
                    d.prop("disabled", false).html('<option value="">Select City</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    console.error("Failed to load NCR cities");
                    if (callback) callback();
                }
            });
        }

        function getDistrictList(callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "district"
                }),
                success: function(res) {
                    let d = $("#submuni");
                    d.prop("disabled", false).html('<option value="">Select District</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    console.error("Failed to load districts");
                    if (callback) callback();
                }
            });
        }

        function getBarangayList(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "barangay",
                    code: code
                }),
                success: function(res) {
                    let d = $("#barangay");
                    d.prop("disabled", false).html('<option value="">Select Barangay</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    console.error("Failed to load barangays");
                    if (callback) callback();
                }
            });
        }

        function getBarangayByDistrict(code, callback) {
            $.ajax({
                url: API_URL,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "barangay_district",
                    code: code
                }),
                success: function(res) {
                    let d = $("#barangay");
                    d.prop("disabled", false).html('<option value="">Select Barangay</option>');
                    if (res.code == 0 && res.data) {
                        res.data.forEach(i => d.append(`<option value="${i.code}">${i.name}</option>`));
                    }
                    if (callback) callback();
                },
                error: function() {
                    console.error("Failed to load barangays by district");
                    if (callback) callback();
                }
            });
        }

        // ================= CASCADE EVENTS (user-driven only) =================

        $("#region").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            $("#submuniField").hide();
            if (!val) return;
            if (val === NCR_CODE) {
                $("#provinceField").hide();
                getNCRCityList();
            } else {
                $("#provinceField").show();
                getProvinceList(val);
            }
        });

        $("#province").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            if (!val) return;
            getCityList(val);
        });

        $("#city").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            if (!val) return;
            if (val === MANILA_CODE) {
                $("#submuniField").show();
                getDistrictList();
            } else {
                $("#submuniField").hide();
                getBarangayList(val);
            }
        });

        $("#submuni").on("change", function() {
            if (cascading) return;
            let val = $(this).val();
            if (!val) return;
            getBarangayByDistrict(val);
        });

        // ================= PROGRAMMATIC CASCADE SETTERS =================

        function setRegion(val, callback) {
            if (!val) {
                if (callback) callback();
                return;
            }
            $("#region").val(val);
            $("#submuniField").hide();
            if (val == NCR_CODE) {
                $("#provinceField").hide();
                $("#province").val("");
                getNCRCityList(callback);
            } else {
                $("#provinceField").show();
                getProvinceList(val, callback);
            }
        }

        function setProvince(val, callback) {
            if ($("#region").val() == NCR_CODE || !val) {
                if (callback) callback();
                return;
            }
            $("#province").val(val);
            getCityList(val, callback);
        }

        function setCity(val, callback) {
            if (!val) {
                if (callback) callback();
                return;
            }
            $("#city").val(val);
            if (val == MANILA_CODE) {
                $("#submuniField").show();
                getDistrictList(callback);
            } else {
                $("#submuniField").hide();
                getBarangayList(val, callback);
            }
        }

        function setSubmuni(val, callback) {
            if (!val) {
                if (callback) callback();
                return;
            }
            $("#submuni").val(val);
            getBarangayByDistrict(val, callback);
        }

    });
</script>
<?= endSection() ?>