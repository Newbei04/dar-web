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
            <li class="breadcrumb-item"><a href="javascript:void(0)">Beneficiary</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">For Verification</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card user-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-check me-2 text-primary"></i>Beneficiary List for Verification
                    </h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge-status badge-pending-status" id="beneficiaryCount">0 pending</span>
                        <a href="<?= $baseURL ?>beneficiary-add" class="btn btn-primary">
                            <i class="fa fa-user-plus me-1"></i> New Beneficiary
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblBeneficiary" class="display responsive nowrap w-100 user-table">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th width="6%">Photo</th>
                                <th width="15%">Username</th>
                                <th>Name</th>
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
            var $tips = $('#tblBeneficiary tbody [data-bs-toggle="tooltip"]');
            $tips.each(function() {
                var inst = bootstrap.Tooltip.getInstance(this);
                if (inst) inst.dispose();
            });
            $tips.each(function() {
                new bootstrap.Tooltip(this);
            });
        }

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
                        $('#beneficiaryCount').text('0 pending');
                        tblData.draw(false);
                        refreshTooltips();
                        return;
                    }
                    data.forEach(function(user, i) {

                        var status = (user.status == 0) ? `<span class="badge-status badge-pending-status"><i class="fas fa-clock me-1"></i>${escapeHtml(user.status_label)}</span>` :
                            (user.status == 1) ? `<span class="badge-status badge-active"><i class="fas fa-check-circle me-1"></i>${escapeHtml(user.status_label)}</span>` :
                            (user.status == 2) ? `<span class="badge-status badge-inactive"><i class="fas fa-ban me-1"></i>${escapeHtml(user.status_label)}</span>` :
                            (user.status == 3) ? `<span class="badge-status badge-deactivated"><i class="fas fa-user-slash me-1"></i>${escapeHtml(user.status_label)}</span>` :
                            '<span class="badge-status badge-neutral">Unknown</span>';

                        var initials = (((user.fname || '').charAt(0) || '') + ((user.lname || '').charAt(0) || '')) || '?';

                        var profilePhoto = `<span class="user-avatar">` + (user.profile ?
                            `<img src="<?= $baseURL ?>assets/images/profile/${encodeURIComponent(user.profile)}" alt="Profile" data-init="${escapeAttr(initials)}" onerror="this.outerHTML=beneficiaryThumbFallback(this.dataset.init);">` :
                            escapeHtml(initials)) + `</span>`;

                        var actions = `
                        <a href="<?= $baseURL ?>beneficiary-verify?id=${user.users_id}" class="btn-action btn-verify verifyBtn" data-bs-toggle="tooltip" title="Verify Beneficiary">
                            <i class="fas fa-user-check"></i>
                        </a>
                        `;

                        var displayName = [user.fname, user.mname, user.lname].filter(Boolean).join(' ').trim() || '-';
                        var username = user.username || '';

                        var nameCell = `
                        <div class="user-name-cell">${escapeHtml(displayName)}</div>
                        <div class="user-contact-cell"><i class="fas fa-envelope"></i><span title="${escapeAttr(user.email || '')}">${escapeHtml(user.email || "-")}</span></div>
                        <div class="user-contact-cell"><i class="fas fa-phone"></i><span title="${escapeAttr(user.mobile || '')}">${escapeHtml(user.mobile || "-")}</span></div>
                        <div class="user-contact-cell"><i class="fas fa-map-marker-alt"></i><span title="${escapeAttr(user.address || '')}">${escapeHtml(user.address || "-")}</span></div>
                        `;

                        tblData.row.add([
                            i + 1,
                            profilePhoto,
                            username ? `<span class="user-username-cell" title="${escapeAttr(username)}">${escapeHtml(username)}</span>` : '<span class="text-muted">—</span>',
                            nameCell,
                            status,
                            actions
                        ]);
                    });

                    $('#beneficiaryCount').text(data.length + ' pending');
                    tblData.draw(false);
                    refreshTooltips();
                },

                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_BENEFICIARY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load beneficiaries.", "error");
                }
            });
        }

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : value).html();
        }

        function escapeAttr(value) {
            return $('<div>').text(value == null ? '' : value).html().replace(/"/g, '&quot;');
        }

        function beneficiaryThumbFallback(init) {
            return escapeHtml(init || '?');
        }
        window.beneficiaryThumbFallback = beneficiaryThumbFallback;

    });
</script>
<?= endSection() ?>
