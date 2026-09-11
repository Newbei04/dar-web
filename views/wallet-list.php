<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
    .wallet-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .wallet-card:hover { transform: translateY(-2px); }
    .wallet-card.active { border-left-color: #0d6efd; }
    .wallet-card.frozen { border-left-color: #dc3545; }
    .summary-card .summary-icon {
        width: 60px; height: 60px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
    }
    .badge-frozen { background: #dc3545; color: #fff; }
    .badge-active { background: #198754; color: #fff; }
    #walletDetailModal .detail-label { font-size: 0.8rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    #walletDetailModal .detail-value { font-size: 1rem; font-weight: 600; }
    .balance-type-badge { font-size: 0.75rem; padding: 4px 8px; }
    .log-action-0 { color: #0d6efd; }
    .log-action-1 { color: #dc3545; }
    .log-action-2 { color: #198754; }
    .log-action-3 { color: #6c757d; }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Wallet</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Wallet Management</a></li>
        </ol>
    </div>

    <!-- Summary Cards -->
    <div class="row" id="summaryRow">
        <div class="col-xl-3 col-sm-6">
            <div class="card wallet-card active">
                <div class="card-body d-flex align-items-center">
                    <div class="summary-icon bg-light-primary me-3">
                        <i class="fas fa-wallet text-primary"></i>
                    </div>
                    <div>
                        <span class="detail-label">Total Wallets</span>
                        <h3 class="mb-0" id="sumTotalWallets">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card wallet-card active">
                <div class="card-body d-flex align-items-center">
                    <div class="summary-icon bg-light-success me-3">
                        <i class="fas fa-peso-sign text-success"></i>
                    </div>
                    <div>
                        <span class="detail-label">Total Balance</span>
                        <h3 class="mb-0" id="sumTotalBalance">₱0.00</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card wallet-card active">
                <div class="card-body d-flex align-items-center">
                    <div class="summary-icon bg-light-warning me-3">
                        <i class="fas fa-coins text-warning"></i>
                    </div>
                    <div>
                        <span class="detail-label">Total Credit Limit</span>
                        <h3 class="mb-0" id="sumCreditLimit">₱0.00</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card wallet-card frozen">
                <div class="card-body d-flex align-items-center">
                    <div class="summary-icon bg-light-danger me-3">
                        <i class="fas fa-snowflake text-danger"></i>
                    </div>
                    <div>
                        <span class="detail-label">Frozen Wallets</span>
                        <h3 class="mb-0" id="sumFrozenCount">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Wallet List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblWallet" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th width="12%">Account No.</th>
                                    <th width="20%">Beneficiary</th>
                                    <th width="12%">Branch</th>
                                    <th width="10%">Balance</th>
                                    <th width="10%">Credit Limit</th>
                                    <th width="8%">Status</th>
                                    <th width="10%">Created</th>
                                    <th width="12%">Actions</th>
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

<!-- ================= WALLET DETAIL MODAL ================= -->
<div class="modal fade" id="walletDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="fas fa-wallet mr-1"></i> Wallet Details</h5>
                    <small class="text-muted" id="walletDetailAccount"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Beneficiary Info -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="detail-label">Beneficiary Name</div>
                        <div class="detail-value" id="wdName">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Username</div>
                        <div class="detail-value" id="wdUsername">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Branch</div>
                        <div class="detail-value" id="wdBranch">-</div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="detail-label">Email</div>
                        <div class="detail-value" id="wdEmail">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Mobile</div>
                        <div class="detail-value" id="wdMobile">-</div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Doc Number</div>
                        <div class="detail-value" id="wdDocNum">-</div>
                    </div>
                </div>

                <hr>

                <!-- Wallet Info -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="detail-label">Total Balance</div>
                        <div class="detail-value text-success" id="wdBalance">₱0.00</div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-label">Credit Limit</div>
                        <div class="detail-value text-primary" id="wdCreditLimit">₱0.00</div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-label">Status</div>
                        <div class="detail-value" id="wdStatus">-</div>
                    </div>
                    <div class="col-md-3">
                        <div class="detail-label">Date Created</div>
                        <div class="detail-value" id="wdCreated">-</div>
                    </div>
                </div>

                <!-- Balance Breakdown -->
                <h6 class="mb-3"><i class="fas fa-layer-group mr-1"></i> Balance Breakdown</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered" id="wdBalanceTable">
                        <thead class="table-light">
                            <tr>
                                <th>Program</th>
                                <th>Balance Type</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Transaction Logs -->
                <h6 class="mb-3"><i class="fas fa-history mr-1"></i> Transaction History</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="wdLogTable">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Before</th>
                                <th class="text-end">After</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= CASH IN MODAL ================= -->
<div class="modal fade" id="cashInModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-1"></i> Cash In</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ciWalletId">
                <div class="text-center mb-3">
                    <small class="text-muted">Cash in to</small>
                    <h5 class="mb-0" id="ciBeneficiaryName">-</h5>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Amount</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control form-control-lg text-end" id="ciAmount" min="0.01" step="0.01" placeholder="0.00" style="font-size:1.5rem;font-weight:700;">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6" id="ciProgramWrap">
                        <label class="form-label fw-bold">Program</label>
                        <select class="single-select" id="ciProgram">
                            <option value="0">Cash In</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="ciBalanceTypeWrap">
                        <label class="form-label fw-bold">Balance Type</label>
                        <select class="single-select" id="ciBalanceType">
                            <option value="1">Personal Savings</option>
                            <option value="2">Fuel Subsidy</option>
                            <option value="3">Planting Loan</option>
                            <option value="4">RFFA Assistance</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnConfirmCashIn">
                    <i class="fas fa-check me-1"></i> Confirm Cash In
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

    if ("<?= $_SESSION["IS_LOGIN"] ?>" !== "1") {
        window.location.href = '<?= $baseURL ?>login';
    }

    const roleId = <?= (int)($_SESSION['role_id'] ?? 0) ?>;

    var tblWallet = $('#tblWallet').DataTable({
        responsive: true,
        language: {
            paginate: {
                next: '<i class="fa fa-angle-double-right"></i>',
                previous: '<i class="fa fa-angle-double-left"></i>'
            }
        }
    });

    loadSummary();
    loadWallets();

    function formatCurrency(val) {
        return '₱' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function loadSummary() {
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-wallet.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "GET_WALLET_SUMMARY" }),
            success: function(res) {
                if (res.code === 0 && res.data) {
                    var d = res.data;
                    $('#sumTotalWallets').text(parseInt(d.total_wallets || 0));
                    $('#sumTotalBalance').text(formatCurrency(d.total_balance));
                    $('#sumCreditLimit').text(formatCurrency(d.total_credit_limit));
                    $('#sumFrozenCount').text(parseInt(d.frozen_count || 0));
                }
            }
        });
    }

    function loadWallets() {
        showLoader();
        tblWallet.clear().draw();

        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-wallet.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "LIST_WALLET" }),
            success: function(res) {
                closeLoader();
                if (res.code !== 0) {
                    Swal.fire("Error", res.message || "Failed to load wallets.", "error");
                    return;
                }
                var data = res.data || [];
                if (data.length === 0) {
                    tblWallet.draw(false);
                    return;
                }
                data.forEach(function(w, i) {
                    var statusBadge = w.is_frozen == 1
                        ? '<span class="badge badge-frozen">Frozen</span>'
                        : '<span class="badge badge-active">Active</span>';

                    var actions = `<button class="btn btn-primary btn-sm viewWallet" data-id="${w.id}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${w.is_frozen != 1 ? `<button class="btn btn-success btn-sm cashInBtn" data-id="${w.id}" data-name="${w.full_name || ''}" title="Cash In">
                        <i class="fas fa-plus-circle"></i>
                    </button>` : ''}`;

                    tblWallet.row.add([
                        i + 1,
                        '<code>' + (w.account_num || '-') + '</code>',
                        '<strong>' + (w.full_name || '-') + '</strong><br><small class="text-muted">' + (w.username || '-') + '</small>',
                        w.branch_name || '-',
                        '<strong class="text-success">' + formatCurrency(w.total_balance) + '</strong>',
                        formatCurrency(w.credit_limit),
                        statusBadge,
                        w.created_at || '-',
                        actions
                    ]);
                });
                tblWallet.draw(false);
            },
            error: function(xhr) {
                closeLoader();
                Swal.fire("Error", "Failed to load wallets.", "error");
            }
        });
    }

    /* ── View Wallet Details ── */
    $(document).on('click', '.viewWallet', function() {
        var walletId = $(this).data('id');
        showLoader();

        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-wallet.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "GET_WALLET", id: walletId }),
            success: function(res) {
                closeLoader();
                if (res.code !== 0) {
                    Swal.fire("Error", res.message || "Wallet not found.", "error");
                    return;
                }
                var w = res.data;

                $('#walletDetailAccount').text('Account: ' + (w.account_num || '-'));
                $('#wdName').text(w.full_name || '-');
                $('#wdUsername').text(w.username || '-');
                $('#wdBranch').text(w.branch_name || '-');
                $('#wdEmail').text(w.email || '-');
                $('#wdMobile').text(w.mobile || '-');
                $('#wdDocNum').text(w.doc_num || '-');
                $('#wdBalance').text(formatCurrency(w.total_balance));
                $('#wdCreditLimit').text(formatCurrency(w.credit_limit));
                $('#wdStatus').html(w.is_frozen == 1
                    ? '<span class="badge badge-frozen">Frozen</span>'
                    : '<span class="badge badge-active">Active</span>');
                $('#wdCreated').text(w.created_at || '-');

                // Balance breakdown
                var balTbody = $('#wdBalanceTable tbody');
                balTbody.empty();
                if (w.balances && w.balances.length > 0) {
                    w.balances.forEach(function(b) {
                        balTbody.append(
                            '<tr>' +
                            '<td>' + (b.program_name || '-') + '</td>' +
                            '<td><span class="badge balance-type-badge bg-light-primary text-dark">' + (b.balance_type_label || '-') + '</span></td>' +
                            '<td class="text-end fw-bold">' + formatCurrency(b.amount) + '</td>' +
                            '</tr>'
                        );
                    });
                } else {
                    balTbody.append('<tr><td colspan="3" class="text-center text-muted">No balance records found</td></tr>');
                }

                // Load logs
                loadWalletLogs(walletId);

                $('#walletDetailModal').modal('show');
            },
            error: function() {
                closeLoader();
                Swal.fire("Error", "Failed to load wallet details.", "error");
            }
        });
    });

    function loadWalletLogs(walletId) {
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-wallet.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "GET_WALLET_LOGS", id: walletId }),
            success: function(res) {
                var logTbody = $('#wdLogTable tbody');
                logTbody.empty();
                if (res.code === 0 && res.data && res.data.length > 0) {
                    res.data.forEach(function(l) {
                        var actionClass = 'log-action-' + l.action;
                        logTbody.append(
                            '<tr>' +
                            '<td>' + (l.created_at || '-') + '</td>' +
                            '<td><span class="' + actionClass + ' fw-bold">' + (l.action_label || '-') + '</span></td>' +
                            '<td class="text-end">' + formatCurrency(l.amount) + '</td>' +
                            '<td class="text-end">' + formatCurrency(l.balance_before) + '</td>' +
                            '<td class="text-end">' + formatCurrency(l.balance_after) + '</td>' +
                            '</tr>'
                        );
                    });
                } else {
                    logTbody.append('<tr><td colspan="5" class="text-center text-muted">No transaction history</td></tr>');
                }
            }
        });
    }

    /* ── Cash In ── */
    function loadPrograms() {
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-wallet.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({ trans: "LIST_PROGRAMS" }),
                success: function(res) {
                if (res.code === 0 && res.data) {
                    var sel = $('#ciProgram');
                    sel.find('option:gt(0)').remove();
                    res.data.forEach(function(p) {
                        sel.append('<option value="' + p.id + '">' + (p.name || p.code || 'Program ' + p.id) + '</option>');
                    });
                    reinitSelect2(sel);
                }
            }
        });
    }
    loadPrograms();

    $(document).on('click', '.cashInBtn', function() {
        var walletId = $(this).data('id');
        var name = $(this).data('name');
        $('#ciWalletId').val(walletId);
        $('#ciBeneficiaryName').text(name || 'Beneficiary');
        $('#ciAmount').val('');
        $('#ciProgram').val('');
        $('#ciBalanceType').val('1');
        toggleProgramField();
        $('#cashInModal').modal('show');
        reinitSelect2('#ciProgram');
        reinitSelect2('#ciBalanceType');
    });

    function toggleProgramField() {
        var balanceType = parseInt($('#ciBalanceType').val());
        var isPersonal = balanceType == 1;
        $('#ciProgram').prop('required', !isPersonal);
        $('#ciProgram').prop('disabled', isPersonal);
        if (isPersonal) {
            $('#ciProgram').val('');
        }
        $('#ciProgramWrap').toggleClass('d-none', isPersonal);
        $('#ciBalanceTypeWrap').toggleClass('col-md-12', isPersonal).toggleClass('col-md-6', !isPersonal);
    }

    $(document).on('change', '#ciBalanceType', function() {
        toggleProgramField();
    });

    $(document).on('click', '#btnConfirmCashIn', function() {
        var amount = parseFloat($('#ciAmount').val());
        var programId = parseInt($('#ciProgram').val());
        var balanceType = parseInt($('#ciBalanceType').val());
        var walletId = parseInt($('#ciWalletId').val());

        if (!amount || amount <= 0) {
            Swal.fire("Validation", "Please enter a valid amount.", "warning");
            return;
        }
        if (balanceType != 1 && !programId) {
            Swal.fire("Validation", "Please select a program.", "warning");
            return;
        }

        Swal.fire({
            title: "Cash In?",
            html: "You are about to cash in <strong>" + formatCurrency(amount) + "</strong> to this wallet.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            confirmButtonText: "Yes, cash in"
        }).then((result) => {
            if (!result.isConfirmed) return;

            $('#btnConfirmCashIn')
                .prop("disabled", true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-wallet.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "CASH_IN",
                    wallet_id: walletId,
                    amount: amount,
                    program_id: programId,
                    balance_type: balanceType
                }),
                success: function(res) {
                    closeLoader();
                    $('#btnConfirmCashIn')
                        .prop("disabled", false)
                        .html('<i class="fas fa-check me-1"></i> Confirm Cash In');

                    if (res.code === 0) {
                        $('#cashInModal').modal('hide');
                        Swal.fire({
                            icon: "success",
                            title: "Cash In Successful!",
                            html: "New balance: <strong>" + formatCurrency(res.new_balance) + "</strong>",
                            confirmButtonColor: "#198754"
                        });
                        loadWallets();
                        loadSummary();
                    } else {
                        Swal.fire("Error", res.message || "Cash in failed.", "error");
                    }
                },
                error: function() {
                    closeLoader();
                    $('#btnConfirmCashIn')
                        .prop("disabled", false)
                        .html('<i class="fas fa-check me-1"></i> Confirm Cash In');
                    Swal.fire("Error", "Cash in failed.", "error");
                }
            });
        });
    });

});
</script>
<?= endSection() ?>
