<?= startSection('css') ?>
<style>
    :root {
        --gold: #f4c430;
        --gold-dark: #d4a017;
        --gold-dim: rgba(244, 196, 48, 0.15);
        --green-deep: #0a1f0e;
        --green-mid: #0d3d1f;
        --green-light: #1a7a3e;
        --text-light: rgba(212, 237, 218, 0.5);
        --text-bright: #e8f5e9;
    }

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

    .bv-idcard-section {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        border: 1px dashed #d1d9e0;
        border-radius: 16px;
        padding: 18px 18px 14px;
    }

    .dar2-card-container { width: 100%; max-width: 520px; margin: 0 auto; }

    .dar2-card {
        width: 100%;
        aspect-ratio: 1.586;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: linear-gradient(165deg, var(--green-deep) 0%, var(--green-mid) 25%, #145a2e 50%, var(--green-light) 75%, var(--green-mid) 100%);
        box-shadow: 0 25px 50px -12px rgba(0, 60, 20, 0.5), 0 0 0 1px rgba(212, 175, 55, 0.15) inset, 0 0 60px rgba(20, 90, 46, 0.3) inset;
        transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        transform-style: preserve-3d;
    }

    .dar2-card:hover { transform: rotateY(-5deg) rotateX(5deg) scale(1.02); }

    .dar2-top-bar {
        display: flex;
        align-items: center;
        padding: 16px 22px 0;
        position: relative;
        z-index: 3;
    }

    .dar2-logo {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.3));
    }
    .dar2-logo img { width: 100%; height: 100%; object-fit: contain; }

    .dar2-title-group { flex: 1; padding-left: 10px; }

    .dar2-title {
        color: var(--gold);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        line-height: 1.2;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
    }

    .dar2-subtitle {
        color: rgba(212, 237, 218, 0.7);
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 1px;
        margin-top: 1px;
    }

    .dar2-network-logo {
        display: flex;
        align-items: center;
        gap: 0;
        flex-shrink: 0;
        position: relative;
        margin-left: auto;
    }

    .dar2-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }

    .dar2-circle-left {
        background: linear-gradient(135deg, var(--gold), #e8b830);
        position: relative;
        z-index: 2;
    }

    .dar2-circle-right {
        background: linear-gradient(135deg, #145a2e, var(--green-light));
        margin-left: -14px;
        position: relative;
        z-index: 1;
    }

    .dar2-network-text {
        font-size: 11px;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: 1px;
        margin-left: 6px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    }

    .dar2-content {
        display: flex;
        flex: 1;
        align-items: center;
        gap: 16px;
        padding: 10px 22px 4px;
        position: relative;
        z-index: 3;
    }

    .dar2-chip {
        width: 42px;
        height: 32px;
        background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 30%, var(--gold-dark) 60%, #b8860b 100%);
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.4);
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 1px;
        padding: 3px;
        overflow: hidden;
        position: relative;
    }

    .dar2-chip::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent 40%, rgba(255, 255, 255, 0.4) 50%, transparent 60%);
        pointer-events: none;
    }

    .dar2-chip-cell { background: rgba(0, 0, 0, 0.08); border-radius: 2px; }

    .dar2-card-number {
        padding: 0 22px 6px;
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .dar2-card-number-label {
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .dar2-id-number {
        color: #ffffff;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 5px;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }

    .dar2-photo-frame {
        width: 90px;
        height: 110px;
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border-radius: 10px;
        padding: 3px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(244, 196, 48, 0.2);
        flex-shrink: 0;
        overflow: hidden;
    }

    .dar2-photo {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #1a4a2a 0%, var(--green-mid) 100%);
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-light);
        font-size: 8px;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .dar2-photo::before {
        content: '';
        position: absolute;
        top: 14%;
        left: 50%;
        transform: translateX(-50%);
        width: 32px;
        height: 32px;
        background: rgba(212, 237, 218, 0.15);
        border-radius: 50%;
    }

    .dar2-photo::after {
        content: '';
        position: absolute;
        bottom: 8%;
        left: 50%;
        transform: translateX(-50%);
        width: 48px;
        height: 28px;
        background: rgba(212, 237, 218, 0.15);
        border-radius: 28px 28px 0 0;
    }

    .dar2-details {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 4px;
        flex: 1;
        min-width: 0;
    }

    .dar2-detail-row {
        display: flex;
        flex-direction: column;
        text-align: right;
        gap: 0;
    }

    .dar2-detail-label {
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .dar2-detail-value {
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dar2-bottom-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 22px 14px;
        position: relative;
        z-index: 3;
    }

    .dar2-valid {
        display: flex;
        flex-direction: column;
    }

    .dar2-valid-label {
        color: var(--text-light);
        font-size: 7px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .dar2-valid-date {
        color: var(--gold);
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 1px;
    }

    .dar2-type {
        color: rgba(255, 255, 255, 0.15);
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .dar2-shine {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent 30%, rgba(244, 196, 48, 0.04) 40%, rgba(244, 196, 48, 0.1) 50%, rgba(244, 196, 48, 0.04) 60%, transparent 70%);
        transform: rotate(25deg);
        pointer-events: none;
        z-index: 10;
        animation: shine 6s ease-in-out infinite;
    }

    @keyframes shine {
        0%, 100% { transform: translateX(-100%) rotate(25deg); }
        50% { transform: translateX(100%) rotate(25deg); }
    }

    .bv-wallet-card { border-left: 4px solid #198754; }
    .bv-wallet-card .wallet-stat { text-align: center; padding: 8px 0; }
    .bv-wallet-card .wallet-stat .stat-value { font-size: 1.2rem; font-weight: 700; }
    .bv-wallet-card .wallet-stat .stat-label { font-size: 0.7rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-frozen { background: #dc3545; color: #fff; }
    .badge-active { background: #198754; color: #fff; }
    .balance-type-badge { font-size: 0.75rem; padding: 4px 8px; }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>beneficiary-list">Beneficiary</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">View Beneficiary</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="bvTitle">Beneficiary Details</h4>
                    <div>
                        <a href="<?= $baseURL ?>beneficiary-edit?id=" class="btn btn-primary btn-sm me-1" id="bvEditBtn" style="display:none;">
                            <i class="fas fa-pencil-alt me-1"></i> Edit
                        </a>
                        <a href="<?= $baseURL ?>beneficiary-list" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- PROFILE HEADER -->
                    <div class="d-flex align-items-center mb-4 flex-wrap">
                        <div class="uv-avatar me-3" id="bvPhotoWrap">
                            <i class="fas fa-user uv-avatar-icon" id="bvPhotoIcon"></i>
                            <img id="bvPhoto" src="" alt="Profile Photo" class="uv-avatar d-none">
                        </div>
                        <div>
                            <h4 class="mb-1" id="bvFullname">-</h4>
                            <p class="text-muted mb-1"><i class="fas fa-user-circle me-1"></i><span id="bvUsername">-</span></p>
                            <div>
                                <span class="badge light badge-info" id="bvRole">-</span>
                                <span class="badge light" id="bvStatus">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- ID CARD -->
                        <div class="col-lg-5" id="bvCardCol">
                            <h6 class="text-muted mb-3 uv-section-title"><i class="fas fa-id-card me-1"></i> DAR ID Card</h6>
                            <div class="bv-idcard-section">
                                <div class="dar2-card-container">
                                    <div class="dar2-card">
                                        <div class="dar2-shine"></div>
                                        <div class="dar2-top-bar">
                                            <div class="dar2-logo">
                                                <img src="<?= $baseURL ?>assets/images/dar.png" alt="DAR">
                                            </div>
                                            <div class="dar2-title-group">
                                                <div class="dar2-title">Department of Agrarian Reform</div>
                                                <div class="dar2-subtitle">DAR ID Card</div>
                                            </div>
                                            <div class="dar2-network-logo">
                                                <div class="dar2-circle dar2-circle-left"></div>
                                                <div class="dar2-circle dar2-circle-right"></div>
                                                <span class="dar2-network-text">DAR</span>
                                            </div>
                                        </div>

                                        <div class="dar2-content">
                                            <div class="dar2-chip">
                                                <div class="dar2-chip-cell"></div>
                                                <div class="dar2-chip-cell"></div>
                                                <div class="dar2-chip-cell"></div>
                                                <div class="dar2-chip-cell"></div>
                                            </div>
                                            <div class="dar2-details">
                                                <div class="dar2-detail-row">
                                                    <span class="dar2-detail-label">ID No.</span>
                                                    <span class="dar2-detail-value" id="cardID">---</span>
                                                </div>
                                                <div class="dar2-detail-row">
                                                    <span class="dar2-detail-label">Given Name</span>
                                                    <span class="dar2-detail-value" id="cardGivenName">---</span>
                                                </div>
                                                <div class="dar2-detail-row">
                                                    <span class="dar2-detail-label">Middle Name</span>
                                                    <span class="dar2-detail-value" id="cardMiddleName">---</span>
                                                </div>
                                                <div class="dar2-detail-row">
                                                    <span class="dar2-detail-label">Surname</span>
                                                    <span class="dar2-detail-value" id="cardSurname">---</span>
                                                </div>
                                            </div>
                                            <div class="dar2-photo-frame">
                                                <div class="dar2-photo" id="cardPhotoPreview">
                                                    <span style="z-index:1;font-size:8px;margin-top:52px;color:rgba(212,237,218,0.4);">PHOTO</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dar2-card-number">
                                            <div class="dar2-card-number-label">Card Number</div>
                                            <span class="dar2-id-number" id="cardNumber">---- ---- ---- ----</span>
                                        </div>

                                        <div class="dar2-bottom-row">
                                            <div class="dar2-valid">
                                                <span class="dar2-valid-label">Valid Thru</span>
                                                <span class="dar2-valid-date" id="cardValidThru">--/--</span>
                                            </div>
                                            <span class="dar2-type">ID CARD</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DETAILS -->
                        <div class="col-lg-7" id="bvDetailsCol">
                            <div class="row g-4">
                                <!-- ACCOUNT -->
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3 uv-section-title"><i class="fas fa-lock me-1"></i> Account</h6>
                                    <table class="table table-borderless uv-detail-table">
                                        <tbody>
                                            <tr>
                                                <th>Username</th>
                                                <td id="bv_username">-</td>
                                            </tr>
                                            <tr>
                                                <th>Branch</th>
                                                <td id="bv_branch">-</td>
                                            </tr>
                                            <tr>
                                                <th>Facility</th>
                                                <td id="bv_facility">-</td>
                                            </tr>
                                            <tr>
                                                <th>Doc / ID No.</th>
                                                <td id="bv_doc_num">-</td>
                                            </tr>
                                            <tr>
                                                <th>Card Number</th>
                                                <td id="bv_card_num">-</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td id="bv_status_text">-</td>
                                            </tr>
                                            <tr>
                                                <th>Verified Date</th>
                                                <td id="bv_verified_dt">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- PERSONAL INFO -->
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3 uv-section-title"><i class="fas fa-user me-1"></i> Personal Info</h6>
                                    <table class="table table-borderless uv-detail-table">
                                        <tbody>
                                            <tr>
                                                <th>First Name</th>
                                                <td id="bv_fname">-</td>
                                            </tr>
                                            <tr>
                                                <th>Middle Name</th>
                                                <td id="bv_mname">-</td>
                                            </tr>
                                            <tr>
                                                <th>Last Name</th>
                                                <td id="bv_lname">-</td>
                                            </tr>
                                            <tr>
                                                <th>Gender</th>
                                                <td id="bv_gender">-</td>
                                            </tr>
                                            <tr>
                                                <th>Marital Status</th>
                                                <td id="bv_marital">-</td>
                                            </tr>
                                            <tr>
                                                <th>Birthday</th>
                                                <td id="bv_birthday">-</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td id="bv_email">-</td>
                                            </tr>
                                            <tr>
                                                <th>Mobile</th>
                                                <td id="bv_mobile">-</td>
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
                                                <td id="bv_street">-</td>
                                            </tr>
                                            <tr>
                                                <th>Full Address</th>
                                                <td id="bv_address">-</td>
                                            </tr>
                                            <tr>
                                                <th>Zip Code</th>
                                                <td id="bv_zip_code">-</td>
                                            </tr>
                                            <tr>
                                                <th>Country</th>
                                                <td id="bv_country">-</td>
                                            </tr>
                                            <tr>
                                                <th>Land Tenure</th>
                                                <td id="bv_land_tenure">-</td>
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
    </div>

    <!-- WALLET SUMMARY -->
    <div class="row" id="bvWalletSection" style="display:none;">
        <div class="col-xl-12">
            <div class="card bv-wallet-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-wallet me-1"></i> Wallet Summary</h5>
                    <span class="badge" id="bvWalletStatus">-</span>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Account Number</small>
                            <code class="fs-6" id="bvWalletAccount">-</code>
                        </div>
                        <div class="col-md-2 wallet-stat">
                            <div class="stat-value text-success" id="bvWalletBalance">₱0.00</div>
                            <div class="stat-label">Balance</div>
                        </div>
                        <div class="col-md-2 wallet-stat">
                            <div class="stat-value text-primary" id="bvWalletCredit">₱0.00</div>
                            <div class="stat-label">Credit Limit</div>
                        </div>
                        <div class="col-md-2 wallet-stat">
                            <div class="stat-value text-warning" id="bvWalletBalCount">0</div>
                            <div class="stat-label">Balance Types</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Date Created</small>
                            <span id="bvWalletCreated">-</span>
                        </div>
                    </div>

                    <div id="bvWalletBalances" class="mt-3" style="display:none;">
                        <h6 class="text-muted mb-2"><i class="fas fa-layer-group me-1"></i> Balance Breakdown</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr><th>Program</th><th>Type</th><th class="text-end">Amount</th></tr>
                                </thead>
                                <tbody id="bvWalletBalTbody"></tbody>
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

        var benId = new URLSearchParams(window.location.search).get('id');

        if (!benId) {
            Swal.fire("Error", "No beneficiary ID provided.", "error").then(function() {
                window.location.href = "<?= $baseURL ?>beneficiary-list";
            });
            return;
        }

        $("#bvEditBtn").attr("href", "<?= $baseURL ?>beneficiary-edit?id=" + benId);

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
            if (status == 0) return '<span class="badge light badge-warning">For Verification</span>';
            if (status == 1) return '<span class="badge light badge-success">Verified</span>';
            if (status == 2) return '<span class="badge light badge-secondary">Inactive</span>';
            if (status == 3) return '<span class="badge light badge-dark">Deactivated</span>';
            return '<span class="badge light badge-info">Unknown</span>';
        }

        function formatDocNum(d) {
            if (!d || d.length !== 9) return d || '-';
            return d.substring(0, 2) + '-' + d.substring(2, 8) + '-' + d.substring(8, 9);
        }

        function validThru(dt) {
            if (!dt) return '--/--';
            var d = new Date(dt.replace(' ', 'T'));
            if (isNaN(d)) return '--/--';
            var y = d.getFullYear() + 5;
            var m = ('0' + (d.getMonth() + 1)).slice(-2);
            return m + '/' + String(y).slice(-2);
        }

        function cardPhotoFallback() {
            return '<span style="z-index:1;font-size:8px;margin-top:52px;color:rgba(212,237,218,0.4);">PHOTO</span>';
        }

        function renderCard(p) {
            $('#cardID').text(formatDocNum(p.doc_num));
            $('#cardGivenName').text((p.fname || '---').toUpperCase());
            $('#cardMiddleName').text((p.mname || '---').toUpperCase());
            $('#cardSurname').text((p.lname || '---').toUpperCase());
            $('#cardNumber').text(p.card_num || '---- ---- ---- ----');
            $('#cardValidThru').text(validThru(p.verified_dt || p.created_at));

            if (p.profile) {
                $('#cardPhotoPreview').html('<img src="<?= $baseURL ?>assets/images/profile/' + p.profile + '" style="width:100%;height:100%;object-fit:cover;border-radius:7px;" onerror="this.style.display=\'none\'; this.parentElement.innerHTML=cardPhotoFallback();">');
            } else {
                $('#cardPhotoPreview').html(cardPhotoFallback());
            }
        }

        function formatCurrency(val) {
            return '₱' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function loadWallet(usersId) {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-wallet.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({ trans: "GET_WALLET", users_id: usersId }),
                success: function(res) {
                    if (res.code !== 0 || !res.data) return;
                    var w = res.data;

                    $("#bvWalletSection").show();
                    $("#bvWalletAccount").text(w.account_num || '-');
                    $("#bvWalletCreated").text(w.created_at || '-');
                    $("#bvWalletBalance").text(formatCurrency(w.total_balance));
                    $("#bvWalletCredit").text(formatCurrency(w.credit_limit));
                    $("#bvWalletBalCount").text(w.balances ? w.balances.length : 0);

                    if (w.is_frozen == 1) {
                        $("#bvWalletStatus").addClass("badge-frozen").text("Frozen");
                    } else {
                        $("#bvWalletStatus").addClass("badge-active").text("Active");
                    }

                    if (w.balances && w.balances.length > 0) {
                        $("#bvWalletBalances").show();
                        var tbody = $("#bvWalletBalTbody");
                        tbody.empty();
                        w.balances.forEach(function(b) {
                            tbody.append(
                                '<tr>' +
                                '<td>' + (b.program_name || '-') + '</td>' +
                                '<td><span class="badge balance-type-badge bg-light-primary text-dark">' + (b.balance_type_label || '-') + '</span></td>' +
                                '<td class="text-end fw-bold">' + formatCurrency(b.amount) + '</td>' +
                                '</tr>'
                            );
                        });
                    }
                }
            });
        }

        showLoader();
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                trans: "GET_BENEFICIARY",
                id: benId
            }),
            success: function(res) {
                closeLoader();
                if (res.code != 0 || !res.data) {
                    Swal.fire("Error", res.message || "Failed to load beneficiary.", "error");
                    return;
                }

                var d = res.data;

                var fullname = [d.fname, d.mname, d.lname].filter(Boolean).join(' ').trim() || '-';

                $("#bvTitle").text(fullname + " - Beneficiary Details");
                $("#bvFullname").text(fullname);
                $("#bvUsername").text(d.username || '-');
                $("#bvRole").text(d.branch || 'Beneficiary');

                var statusHtml = statusBadge(d.status);
                $("#bvStatus").html(statusHtml);
                $("#bv_status_text").html(statusHtml);

                if (d.status == 0) {
                    $("#bvCardCol").addClass("d-none");
                    $("#bvDetailsCol").removeClass("col-lg-7").addClass("col-lg-12");
                } else {
                    $("#bvCardCol").removeClass("d-none");
                    $("#bvDetailsCol").removeClass("col-lg-12").addClass("col-lg-7");
                }

                if (d.profile) {
                    $("#bvPhoto").attr("src", "<?= $baseURL ?>assets/images/profile/" + d.profile).removeClass("d-none");
                    $("#bvPhotoIcon").addClass("d-none");
                }

                $("#bv_username").text(d.username || '-');
                $("#bv_branch").text(d.branch || '-');
                $("#bv_facility").text(d.facility || '-');
                $("#bv_doc_num").text(formatDocNum(d.doc_num));
                $("#bv_card_num").text(d.card_num || '-');
                $("#bv_verified_dt").text(d.verified_dt || '-');

                $("#bv_fname").text(d.fname || '-');
                $("#bv_mname").text(d.mname || '-');
                $("#bv_lname").text(d.lname || '-');
                $("#bv_gender").text(genderLabel(d.gender));
                $("#bv_marital").text(maritalLabel(d.marital));
                $("#bv_birthday").text((d.birthday || '-') + (d.birthday ? ageFromBirthday(d.birthday) : ''));
                $("#bv_email").text(d.email || '-');
                $("#bv_mobile").text(d.mobile || '-');

                $("#bv_street").text(d.street || '-');
                $("#bv_address").text(d.address || '-');
                $("#bv_zip_code").text(d.zip_code || '-');
                $("#bv_country").text(d.country || '-');
                $("#bv_land_tenure").text(d.land_tenure_status || '-');

                renderCard(d);

                loadWallet(d.users_id);

                $("#bvEditBtn").show();
            },
            error: function(xhr) {
                closeLoader();
                console.error("GET_BENEFICIARY failed:", xhr.responseText);
                Swal.fire("Error", "Failed to load beneficiary.", "error");
            }
        });

    });
</script>
<?= endSection() ?>
