<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/nestable2/css/jquery.nestable.min.css" rel="stylesheet">
<style>
    .dd {
        max-width: 100%;
    }

    .dd-item {
        position: relative;
    }

    /* Toggle buttons (injected by Nestable) */
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

    .dd-collapsed > button.dd-collapse {
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
        padding: 8px 12px 8px 44px;
        border: 1px solid #e6e9f2;
        border-radius: 10px;
        background: #fff;
        color: #212529;
        font-weight: 500;
        cursor: all-scroll;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        transition: border-color .15s ease, box-shadow .15s ease;
        box-sizing: border-box;
    }

    .dd-handle:hover {
        border-color: #1EAAE7;
        box-shadow: 0 6px 16px rgba(30, 170, 231, 0.12);
    }

    .dd-handle .drag-grip {
        color: #c4c9d4;
        font-size: 15px;
        margin-right: 12px;
        flex: 0 0 auto;
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

    .dd-handle .module-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: 14px;
        flex: 0 0 auto;
    }

    .dd-handle .module-actions {
        margin-left: auto;
        display: flex;
        align-items: center;
        cursor: default;
    }

    .dd-handle .module-actions .btn {
        width: 30px;
        height: 30px;
        margin-left: 6px;
        padding: 0;
        border: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: background .15s ease, color .15s ease, transform .15s ease;
    }

    .dd-handle .module-actions .btn:hover {
        transform: translateY(-1px);
    }

    .dd-handle .module-actions .viewBtn {
        background: rgba(30, 170, 231, 0.12);
        color: #1EAAE7;
    }

    .dd-handle .module-actions .viewBtn:hover {
        background: #1EAAE7;
        color: #fff;
    }

    .dd-handle .module-actions .editBtn {
        background: rgba(243, 156, 18, 0.14);
        color: #f39c12;
    }

    .dd-handle .module-actions .editBtn:hover {
        background: #f39c12;
        color: #fff;
    }

    .dd-handle .module-actions .deleteBtn {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
    }

    .dd-handle .module-actions .deleteBtn:hover {
        background: #dc3545;
        color: #fff;
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

    /* Placeholder / drag */
    .dd-placeholder {
        min-height: 48px;
        border: 2px dashed #1EAAE7;
        border-radius: 10px;
        background: rgba(30, 170, 231, 0.06);
    }

    .dd-dragel {
        z-index: 9999;
    }

    .dd-dragel .dd-item > button {
        display: none;
    }

    .dd-dragel .dd-handle {
        border-color: #1EAAE7;
        box-shadow: 0 12px 28px rgba(16, 24, 40, 0.16);
        opacity: .96;
    }
</style>
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
                    <h4 class="card-title">List Modules</h4>

                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                        <button type="button" class="btn btn-primary" id="btnAddModule">
                            <i class="fa fa-plus mr-1"></i> Add Module
                        </button>
                    <?php } ?>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <small class="text-muted mb-1">
                            <i class="fas fa-arrows-alt mr-1 text-primary"></i> Drag a module by its row to reorder — drop it onto another module to nest it as a child.
                        </small>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnToggleAll">
                            <i class="fas fa-compress-alt mr-1"></i> Collapse All
                        </button>
                    </div>

                    <div class="dd" id="moduleTree">
                        <ol class="dd-list">
                            <!-- Loaded via AJAX -->
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= ADD MODULE MODAL ================= -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Parent Module</label>
                        <select id="add_parent_id" class="form-control select2">
                            <option value="0">-- No Parent (Top Level) --</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" id="add_title" class="form-control" placeholder="e.g. Users List">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Icon</label>
                        <input type="text" id="add_icon" class="form-control" placeholder="e.g. fe fe-users">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Page</label>
                        <input type="text" id="add_page" class="form-control" placeholder="e.g. list-users">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Filename</label>
                        <input type="text" id="add_filename" class="form-control" placeholder="e.g. list_users.php">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Sort Order</label>
                        <input type="number" id="add_sort_order" class="form-control" value="1" min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Show in Menu?</label>
                        <select id="add_is_menu" class="form-control single-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select id="add_status" class="form-control single-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveAdd">
                    <i class="fa fa-plus mr-1"></i> Add Module
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= VIEW MODULE MODAL ================= -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Module Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="30%" class="text-muted">Parent</th>
                        <td id="view_parent">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Title</th>
                        <td id="view_title">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Icon</th>
                        <td id="view_icon">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Page</th>
                        <td id="view_page">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Filename</th>
                        <td id="view_filename">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Sort Order</th>
                        <td id="view_sort_order">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Show in Menu</th>
                        <td id="view_is_menu">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td id="view_status">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Created At</th>
                        <td id="view_created_at">-</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Updated At</th>
                        <td id="view_updated_at">-</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= EDIT MODULE MODAL ================= -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Parent Module</label>
                        <select id="edit_parent_id" class="form-control select2">
                            <option value="0">-- No Parent (Top Level) --</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" class="form-control" placeholder="e.g. Users List">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Icon</label>
                        <input type="text" id="edit_icon" class="form-control" placeholder="e.g. fe fe-users">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Page</label>
                        <input type="text" id="edit_page" class="form-control" placeholder="e.g. list-users">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Filename</label>
                        <input type="text" id="edit_filename" class="form-control" placeholder="e.g. list_users.php">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Sort Order</label>
                        <input type="number" id="edit_sort_order" class="form-control" min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Show in Menu?</label>
                        <select id="edit_is_menu" class="form-control single-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Status</label>
                        <select id="edit_status" class="form-control single-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveEdit">
                    <i class="fa fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>


<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/nestable2/js/jquery.nestable.min.js"></script>

<script>
    $(document).ready(function() {

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        var modules = [];
        var allCollapsed = false;

        loadData();

        $('#moduleTree').on('change', function() {
            var nested = $('#moduleTree').nestable('serialize');
            saveOrder(flattenTree(nested, 0));
        });

        $("#btnToggleAll").on("click", function() {
            if (allCollapsed) {
                $('#moduleTree').nestable('expandAll');
                $(this).html('<i class="fas fa-compress-alt mr-1"></i> Collapse All');
            } else {
                $('#moduleTree').nestable('collapseAll');
                $(this).html('<i class="fas fa-expand-alt mr-1"></i> Expand All');
            }
            allCollapsed = !allCollapsed;
        });

        function loadData() {
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-module.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MODULE"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load modules.", "error");
                        return;
                    }
                    modules = res.data || [];
                    renderTree();
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_MODULE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load modules.", "error");
                }
            });
        }

        function buildTree(data) {
            var map = {};
            data.forEach(function(m) {
                m.children = [];
                map[m.id] = m;
            });
            var roots = [];
            data.forEach(function(m) {
                var pid = parseInt(m.parent_id, 10) || 0;
                if (pid && map[pid]) {
                    map[pid].children.push(m);
                } else {
                    roots.push(m);
                }
            });
            return roots;
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

        function renderModule(m) {
            var menuBadge = m.is_menu == 1 ?
                '<span class="badge light badge-info">Menu</span>' :
                '<span class="badge light badge-secondary">Hidden</span>';

            var statusBadge = m.status == 1 ?
                '<span class="badge light badge-success">Active</span>' :
                '<span class="badge light badge-danger">Inactive</span>';

            var html = '';
            html += '<li class="dd-item" data-id="' + m.id + '">';
            html += '<div class="dd-handle">';
            html += '<i class="fas fa-grip-vertical drag-grip"></i>';
            html += '<span class="module-icon"><i class="' + iconClass(m.icon) + '"></i></span>';
            html += '<span class="module-text"><strong>' + m.title + '</strong><small>' + (m.page || '-') + '</small></span>';
            html += '<span class="module-meta">' + menuBadge + statusBadge + '</span>';
            html += '<span class="dd-nodrag module-actions">';
            html += '<button class="btn viewBtn" data-id="' + m.id + '" data-toggle="tooltip" title="View Details"><i class="fas fa-eye"></i></button>';
            html += '<button class="btn editBtn" data-id="' + m.id + '" data-toggle="tooltip" title="Update Details"><span class="fas fa-pencil-alt"></span></button>';
            html += '<button class="btn deleteBtn" data-id="' + m.id + '" data-toggle="tooltip" title="Delete Module"><i class="fas fa-trash"></i></button>';
            html += '</span>';
            html += '</div>';
            if (m.children && m.children.length) {
                html += '<ol class="dd-list">';
                m.children.forEach(function(c) {
                    html += renderModule(c);
                });
                html += '</ol>';
            }
            html += '</li>';
            return html;
        }

        function renderTree() {
            var roots = buildTree(modules);
            var $root = $('#moduleTree > .dd-list');

            $root.html('');

            if (roots.length === 0) {
                $root.html('<li><div class="text-center py-5"><i class="fas fa-layer-group fa-3x text-muted mb-3 d-block"></i><p class="mb-0 text-muted">No modules found. Click "Add Module" to create one.</p></div></li>');
            } else {
                roots.forEach(function(m) {
                    $root.append(renderModule(m));
                });
            }

            allCollapsed = false;
            $("#btnToggleAll")
                .html('<i class="fas fa-compress-alt mr-1"></i> Collapse All');

            initNestable();
        }

        function initNestable() {
            if ($('#moduleTree').data('nestable')) {
                $('#moduleTree').nestable('destroy');
            }
            $('#moduleTree').nestable({
                group: 1,
                maxDepth: 10,
                expandBtnHTML: '<button class="dd-expand" data-action="expand" type="button" title="Expand"><i class="fas fa-chevron-right"></i></button>',
                collapseBtnHTML: '<button class="dd-collapse" data-action="collapse" type="button" title="Collapse"><i class="fas fa-chevron-down"></i></button>'
            });
        }

        function flattenTree(nodes, parentId) {
            var items = [];
            nodes.forEach(function(n) {
                items.push({
                    id: n.id,
                    parent_id: parentId
                });
                if (n.children && n.children.length) {
                    items = items.concat(flattenTree(n.children, n.id));
                }
            });
            return items;
        }

        function saveOrder(order) {
            if (order.length === 0) return;

            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-module.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "REORDER_MODULE",
                    order: order
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        loadData();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire("Error", res.message, "error");
                        loadData();
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("REORDER_MODULE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to save module order.", "error");
                    loadData();
                }
            });
        }

        function isDescendant(id, ancestorId) {
            var parentOf = {};
            modules.forEach(function(m) {
                parentOf[m.id] = m.parent_id;
            });
            var cur = parentOf[id];
            while (cur && cur != 0) {
                if (cur == ancestorId) return true;
                cur = parentOf[cur];
            }
            return false;
        }

        function loadParentOptions(selectedId) {
            var opts = '<option value="0">-- No Parent (Top Level) --</option>';

            modules.forEach(function(m) {
                if (selectedId && m.id == selectedId) return;
                if (selectedId && isDescendant(m.id, selectedId)) return;
                opts += '<option value="' + m.id + '">' + m.title + '</option>';
            });

            return opts;
        }

        /* ---------- ADD ---------- */
        $("#btnAddModule").on("click", function() {
            $("#add_title").val('');
            $("#add_icon").val('');
            $("#add_page").val('');
            $("#add_filename").val('');
            $("#add_sort_order").val('1');
            $("#add_is_menu").val('1').trigger('change');
            $("#add_status").val('1').trigger('change');
            $("#add_parent_id").html(loadParentOptions(null));
            reinitSelect2("#add_parent_id");
            $("#addModal").modal("show");
        });

        $("#btnSaveAdd").on("click", function() {
            let data = {
                trans: "ADD_MODULE",
                parent_id: $("#add_parent_id").val(),
                title: $("#add_title").val().trim(),
                icon: $("#add_icon").val().trim(),
                page: $("#add_page").val().trim(),
                filename: $("#add_filename").val().trim(),
                sort_order: $("#add_sort_order").val(),
                is_menu: $("#add_is_menu").val()
            };

            if (!data.title) {
                Swal.fire("Required", "Module title is required.", "warning");
                return;
            }

            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-module.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(data),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $("#addModal").modal("hide");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("ADD_MODULE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to add module.", "error");
                }
            });
        });

        /* ---------- VIEW ---------- */
        $(document).on("click", ".viewBtn", function() {
            let id = $(this).data("id");
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-module.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_MODULE",
                    id: id
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        let d = res.data;
                        $("#view_parent").text(d.parent_name ?? '-');
                        $("#view_title").text(d.title ?? '-');
                        $("#view_icon").text(d.icon ?? '-');
                        $("#view_page").text(d.page ?? '-');
                        $("#view_filename").text(d.filename ?? '-');
                        $("#view_sort_order").text(d.sort_order ?? '-');
                        $("#view_is_menu").text(d.is_menu == 1 ? 'Yes' : 'No');
                        $("#view_status").text(d.status == 1 ? 'Active' : 'Inactive');
                        $("#view_created_at").text(d.created_at ?? '-');
                        $("#view_updated_at").text(d.updated_at ?? '-');
                        $("#viewModal").modal("show");
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("GET_MODULE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load module details.", "error");
                }
            });
        });

        /* ---------- EDIT ---------- */
        $(document).on("click", ".editBtn", function() {
            let id = $(this).data("id");
            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-module.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_MODULE",
                    id: id
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        let d = res.data;
                        $("#edit_id").val(d.id);
                        $("#edit_parent_id").html(loadParentOptions(d.id));
                        reinitSelect2("#edit_parent_id");
                        $("#edit_parent_id").val(d.parent_id).trigger('change');
                        $("#edit_title").val(d.title);
                        $("#edit_icon").val(d.icon);
                        $("#edit_page").val(d.page);
                        $("#edit_filename").val(d.filename);
                        $("#edit_sort_order").val(d.sort_order);
                        $("#edit_is_menu").val(d.is_menu).trigger('change');
                        $("#edit_status").val(d.status).trigger('change');
                        $("#editModal").modal("show");
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("GET_MODULE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load module details.", "error");
                }
            });
        });

        $("#btnSaveEdit").on("click", function() {
            let data = {
                trans: "UPDATE_MODULE",
                id: $("#edit_id").val(),
                parent_id: $("#edit_parent_id").val(),
                title: $("#edit_title").val().trim(),
                icon: $("#edit_icon").val().trim(),
                page: $("#edit_page").val().trim(),
                filename: $("#edit_filename").val().trim(),
                sort_order: $("#edit_sort_order").val(),
                is_menu: $("#edit_is_menu").val(),
                status: $("#edit_status").val()
            };

            if (!data.title) {
                Swal.fire("Required", "Module title is required.", "warning");
                return;
            }

            showLoader();
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-module.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(data),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $("#editModal").modal("hide");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("UPDATE_MODULE failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to update module.", "error");
                }
            });
        });

        /* ---------- DELETE ---------- */
        $(document).on("click", ".deleteBtn", function() {
            let id = $(this).data("id");
            Swal.fire({
                title: "Delete Module?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete"
            }).then((result) => {
                if (!result.isConfirmed) return;
                showLoader();
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-module.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "DELETE_MODULE",
                        id: id
                    }),
                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {
                            Swal.fire("Deleted", res.message, "success");
                            loadData();
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

    });
</script>
<?= endSection() ?>
