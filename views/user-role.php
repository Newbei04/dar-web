<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
    .dd {
        max-width: 100%;
    }

    .dd-item {
        position: relative;
    }

    /* Toggle buttons */
    .dd-item > button {
        position: absolute;
        left: 12px;
        top: 32px;
        transform: translateY(-50%);
        width: 22px;
        height: 22px;
        margin: 0;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: #1EAAE7;
        color: #fff;
        font-size: 11px;
        line-height: 22px;
        z-index: 3;
        text-indent: 0;
        overflow: visible;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(30, 170, 231, 0.35);
        transition: background .15s ease;
    }

    .dd-item > button:hover {
        background: #1296cc;
    }

    .dd-item > button:before {
        content: '' !important;
    }

    .dd-item > button.dd-expand {
        display: none;
    }

    .dd-collapsed > button.dd-collapse,
    .dd-collapsed .dd-list {
        display: none;
    }

    .dd-collapsed > button.dd-expand {
        display: flex;
    }

    /* Handle / row */
    .dd-handle {
        display: flex;
        align-items: center;
        min-height: 52px;
        margin: 6px 0;
        padding: 8px 14px 8px 44px;
        border: 1px solid #e6e9f2;
        border-radius: 10px;
        background: #fff;
        color: #212529;
        font-weight: 500;
        cursor: default;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        transition: border-color .15s ease, box-shadow .15s ease;
        box-sizing: border-box;
    }

    .dd-handle:hover {
        border-color: #1EAAE7;
        box-shadow: 0 6px 16px rgba(30, 170, 231, 0.12);
    }

    .dd-handle .module-icon {
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(30, 170, 231, 0.12);
        color: #1EAAE7;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 15px;
    }

    .dd-handle .module-text {
        display: flex;
        flex-direction: column;
        line-height: 1.3;
        min-width: 0;
    }

    .dd-handle .module-text strong {
        font-size: 14px;
        font-weight: 600;
        color: #172b4d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dd-handle .module-text small {
        color: #8a8f99;
        font-size: 12px;
    }

    .dd-handle .module-check {
        margin-left: auto;
        flex: 0 0 18px;
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #1EAAE7;
    }

    /* Children indentation + connector */
    .dd-list .dd-list {
        position: relative;
        padding-left: 44px;
    }

    .dd-list .dd-list::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: rgba(30, 170, 231, 0.16);
        border-radius: 2px;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Settings</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">User Roles</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Roles</h4>

                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                        <button type="button" class="btn btn-primary" id="btnAddRole">
                            <i class="fa fa-plus mr-1"></i> Add Role
                        </button>
                    <?php } ?>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table id="tblRole" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="15%">Role</th>
                                <th>Description</th>
                                <th width="10%">Modules</th>
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
</div>

<!-- ================= EDIT ROLE ACCESS MODAL ================= -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Edit Role Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Role Title <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select id="edit_status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" id="edit_description" class="form-control">
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="mb-0"><strong>Module Access</strong></label>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnCheckAll">
                            <i class="fa fa-check-square"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnUncheckAll">
                            <i class="fa fa-square"></i> Unselect All
                        </button>
                    </div>
                </div>

                <div class="border rounded p-3" id="moduleAccessList" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-muted text-center mb-0">Loading modules...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveRole">
                    <i class="fa fa-save mr-1"></i> Save Changes
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

        var tblData = $('#tblRole').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });
        let modules = [];

        loadData();

        function loadData() {
            showLoader();
            tblData.clear().draw();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-role.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_USER_ROLES"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load roles.", "error");
                        return;
                    }
                    var data = res.data || [];
                    if (data.length === 0) {
                        tblData.draw(false);
                        return;
                    }
                    data.forEach(function(row, i) {
                        var statusBadge = row.status == 1 ?
                            '<span class="badge light badge-success">Active</span>' :
                            '<span class="badge light badge-danger">Inactive</span>';

                        var actions = `
                        <button class="btn btn-sm btn-primary shadow editBtn" data-id="${row.id}" data-toggle="tooltip" title="Edit Access">
                            <i class="fas fa-pencil-alt"></i> Edit Access
                        </button>
                        `;

                        tblData.row.add([
                            i + 1,
                            row.title,
                            row.description,
                            row.access_count,
                            statusBadge,
                            actions
                        ]);
                    });

                    tblData.draw(false);
                },

                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_USER_ROLES failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load roles.", "error");
                }
            });
        }

        function iconClass(icon) {
            icon = (icon || '').trim();
            if (icon.indexOf('fa ') === 0 || icon.indexOf('fas ') === 0 || icon.indexOf('far ') === 0) {
                return icon;
            }
            var name = icon.replace(/^fe\s+fe-/, '').replace(/^fe-/, '');
            var map = {
                award: 'award',
                'book-open': 'book-open',
                box: 'box',
                briefcase: 'briefcase',
                calendar: 'calendar',
                camera: 'camera',
                check: 'check',
                'check-circle': 'check-circle',
                edit: 'edit',
                eye: 'eye',
                'file-text': 'file-alt',
                grid: 'th',
                home: 'home',
                list: 'list',
                map: 'map',
                'map-pin': 'map-marker-alt',
                menu: 'bars',
                package: 'box-open',
                plus: 'plus',
                receipt: 'receipt',
                settings: 'cogs',
                share: 'share-alt',
                shield: 'shield-alt',
                'shopping-cart': 'shopping-cart',
                tag: 'tag',
                tool: 'wrench',
                truck: 'truck',
                user: 'user',
                'user-plus': 'user-plus',
                users: 'users',
                'x-circle': 'times-circle'
            };
            return 'fas fa-' + (map[name] || 'folder-open');
        }

        function renderNode(m, allowedSet) {
            let children = modules.filter(x => x.parent_id == m.id);
            let checked = allowedSet.has(m.id) ? 'checked' : '';
            let html = '<li class="dd-item" data-id="' + m.id + '">';

            if (children.length) {
                html += '<button class="dd-collapse" data-action="collapse" type="button" title="Collapse"><i class="fas fa-chevron-down"></i></button>';
                html += '<button class="dd-expand" data-action="expand" type="button" title="Expand"><i class="fas fa-chevron-right"></i></button>';
            }

            html += '<div class="dd-handle">';
            html += '<span class="module-icon"><i class="' + iconClass(m.icon) + '"></i></span>';
            html += '<span class="module-text"><strong>' + m.title + '</strong><small>' + (m.page || '-') + '</small></span>';
            html += '<input type="checkbox" class="module-check" value="' + m.id + '" ' + checked + '>';
            html += '</div>';

            if (children.length) {
                html += '<ol class="dd-list">';
                children.forEach(function(c) {
                    html += renderNode(c, allowedSet);
                });
                html += '</ol>';
            }

            html += '</li>';
            return html;
        }

        function renderModules(allowedSet) {
            let roots = modules.filter(m => m.parent_id == 0);

            if (roots.length === 0) {
                $("#moduleAccessList").html('<p class="text-muted text-center mb-0">No modules available.</p>');
                return;
            }

            let html = '<div class="dd"><ol class="dd-list">';
            roots.forEach(function(m) {
                html += renderNode(m, allowedSet);
            });
            html += '</ol></div>';

            $("#moduleAccessList").html(html);
        }

        $("#moduleAccessList").on("click", ".dd-item > button[data-action]", function(e) {
            e.preventDefault();
            let $item = $(this).closest(".dd-item");
            if ($(this).data("action") === "collapse") {
                $item.addClass("dd-collapsed");
            } else {
                $item.removeClass("dd-collapsed");
            }
        });

        $(document).on("click", ".editBtn", function() {
            let id = $(this).data("id");
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-role.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_ROLE_DETAIL",
                    id: id
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        let d = res.data;
                        let role = d.role;
                        modules = d.modules;

                        $("#edit_id").val(role.id);
                        $("#edit_title").val(role.title);
                        $("#edit_description").val(role.description || '');
                        $("#edit_status").val(role.status);

                        let allowedSet = new Set(
                            (role.access || '').split(',').map(Number).filter(Boolean)
                        );
                        renderModules(allowedSet);

                        $("#roleModalTitle").text("Edit Role Access");
                        $("#btnSaveRole").html('<i class="fa fa-save mr-1"></i> Save Changes');

                        $("#roleModal").modal("show");
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("GET_ROLE_DETAIL failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load role details.", "error");
                }
            });
        });

        $("#btnAddRole").on("click", function() {
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-role.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MODULES"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        modules = res.data || [];

                        $("#edit_id").val("");
                        $("#edit_title").val("");
                        $("#edit_description").val("");
                        $("#edit_status").val(1);

                        renderModules(new Set());

                        $("#roleModalTitle").text("Add Role");
                        $("#btnSaveRole").html('<i class="fa fa-plus mr-1"></i> Add Role');

                        $("#roleModal").modal("show");
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_MODULES failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load modules.", "error");
                }
            });
        });

        $("#btnCheckAll").on("click", function() {
            $(".module-check").prop("checked", true);
        });

        $("#btnUncheckAll").on("click", function() {
            $(".module-check").prop("checked", false);
        });

        $("#btnSaveRole").on("click", function() {
            let id = $("#edit_id").val();
            let title = $("#edit_title").val().trim();
            let description = $("#edit_description").val().trim();
            let status = $("#edit_status").val();

            if (!title) {
                Swal.fire("Required", "Role title is required.", "warning");
                return;
            }

            let access = [];
            $(".module-check:checked").each(function() {
                access.push($(this).val());
            });

            Swal.fire({
                title: $("#edit_id").val() ? "Save Role Access?" : "Add Role?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: $("#edit_id").val() ? "Save" : "Add"
            }).then((result) => {
                if (!result.isConfirmed) return;

                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-role.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: $("#edit_id").val() ? "UPDATE_ROLE_ACCESS" : "ADD_ROLE",
                        id: id,
                        title: title,
                        description: description,
                        status: status,
                        access: access
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Success", res.message, "success");
                            $("#roleModal").modal("hide");
                            loadData();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function(xhr) {
                        closeLoader();
                        console.error("UPDATE_ROLE_ACCESS failed:", xhr.responseText);
                        Swal.fire("Error", "Failed to save role access.", "error");
                    }
                });
            });
        });

    });
</script>
<?= endSection() ?>
