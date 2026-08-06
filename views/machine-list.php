<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/magnific-popup/magnific-popup.css" rel="stylesheet">
<?= endSection() ?>


<?= startSection('content') ?>

<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Machine</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">List Machine</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Machine List</h4>

                    <button type="button" class="btn btn-primary" id="btnAddMachine">
                        <i class="fa fa-plus mr-1"></i> Add Machine
                    </button>
                </div>

                <div class="card-body">
                    <table id="tblData" class="display responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="2%">#</th>
                                <th width="8%">Image</th>
                                <th width="12%">Branch</th>
                                <th width="12%">Type</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th width="10%">Model</th>
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

<!-- ================= ADD MACHINE MODAL ================= -->
<div class="modal fade" id="addMachineModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="mb-0 font-weight-bold">Add Machine</h5>
                    <small class="text-muted">Create new machine information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <h6 class="text-muted text-uppercase mb-3">Assignment</h6>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Branch <span class="text-danger">*</span></label>
                    <select class="form-control" id="add_branch_id"></select>
                </div>

                <hr>

                <h6 class="text-muted text-uppercase mb-3">Machine Information</h6>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Machine Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="add_type_id"></select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Machine Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Model</label>
                            <input type="text" class="form-control" id="add_model">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Description</label>
                    <textarea class="form-control" id="add_description" rows="3"></textarea>
                </div>

                <hr>

                <h6 class="text-muted text-uppercase mb-3">Pricing & Availability</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Daily Rate</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="add_daily_rate" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Status</label>
                            <select class="form-control" id="add_status">
                                <option value="1">Available</option>
                                <option value="0">Under Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="text-muted text-uppercase mb-3">Machine Images</h6>

                <div class="form-group mb-0">
                    <label class="font-weight-semibold">Images</label>
                    <input type="file" class="form-control" id="add_image_input" accept="image/*" multiple>
                    <small class="form-text text-muted">You can select multiple images. The first one will be set as primary.</small>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveAdd">
                    <i class="fa fa-plus mr-1"></i> Add Machine
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div class="modal fade" id="editMachineModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="mb-0 font-weight-bold">Edit Machine</h5>
                    <small class="text-muted">Update machine information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit_id">

                <h6 class="text-muted text-uppercase mb-3">Assignment</h6>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Branch</label>
                    <select class="form-control" id="edit_branch_id"></select>
                </div>

                <hr>

                <h6 class="text-muted text-uppercase mb-3">Machine Information</h6>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Machine Type</label>
                    <select class="form-control" id="edit_type_id"></select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Machine Name</label>
                            <input type="text" class="form-control" id="edit_name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Model</label>
                            <input type="text" class="form-control" id="edit_model">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Description</label>
                    <textarea class="form-control" id="edit_description" rows="3"></textarea>
                </div>

                <hr>

                <h6 class="text-muted text-uppercase mb-3">Pricing & Availability</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Daily Rate</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="edit_daily_rate" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Status</label>
                            <select class="form-control" id="edit_status">
                                <option value="1">Available</option>
                                <option value="0">Under Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="text-muted text-uppercase mb-3">Machine Images</h6>

                <label class="font-weight-semibold">Current Images <span id="imageCount" class="badge badge-secondary ml-1" style="font-size:11px;">0</span></label>
                <div id="currentImages" class="row mb-2 mt-1">
                    <div class="col-12 border rounded-3 bg-light text-center text-muted py-4">
                        <i class="fas fa-image" style="font-size:1.5rem;"></i>
                        <p class="mb-0 mt-1 small">No images uploaded</p>
                    </div>
                </div>

                <hr class="my-3">

                <div class="form-group mb-0">
                    <label class="font-weight-semibold">Add / Replace Images</label>
                    <input type="file" class="form-control" id="edit_image_input" accept="image/*" multiple>
                    <small class="form-text text-muted">Selecting new images will replace all current images.</small>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateMachineBtn">
                    <i class="fa fa-save mr-1"></i> Update Machine
                </button>
            </div>

        </div>
    </div>
</div>


<!-- ================= VIEW MACHINE MODAL ================= -->
<div class="modal fade" id="viewMachineModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="viewModalTitle">Machine Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-5 mb-3 mb-md-0">

                        <!--
                            FIX: previously the placeholder used style="display:none;" and the
                            image used class="d-block" (Bootstrap's !important utility). jQuery's
                            .hide()/.show() only ever toggled the *inline* style, which can never
                            beat a class carrying !important. Result: both the broken image icon
                            and the placeholder could end up visible at once ("Machine Image" +
                            "No images"). Fix: control visibility purely with Bootstrap d-* classes
                            on both elements (toggled in JS below), no inline display styles.
                        -->
                        <div class="position-relative rounded-3 overflow-hidden bg-light" id="viewGalleryMain">
                            <div id="viewGalleryPlaceholder" class="d-flex align-items-center justify-content-center text-muted" style="height:260px;">
                                <i class="fas fa-image me-2"></i> No images
                            </div>
                            <img id="viewGalleryMainImg" src="" alt="Machine Image" class="d-none w-100 object-fit-cover" style="height:260px;cursor:zoom-in;position:relative;z-index:1;">
                            <button type="button" class="btn btn-light position-absolute top-50 start-0 translate-middle-y shadow-sm ms-2" id="viewGalleryPrev" style="z-index:5;">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y shadow-sm me-2" id="viewGalleryNext" style="z-index:5;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 end-0 m-2" id="viewGalleryCounter">0 / 0</span>
                        </div>

                        <div class="d-flex gap-2 mt-2 overflow-auto pb-1" id="viewGalleryThumbs"></div>

                    </div>

                    <div class="col-md-7">
                        <h5 class="fw-semibold mb-1" id="viewDetailName">—</h5>
                        <small class="text-muted d-block" id="viewDetailModel" style="font-family:monospace;"></small>

                        <h4 class="fw-bold text-dark my-2" id="viewDetailPrice">—</h4>

                        <div id="viewDetailRows" class="mt-3"></div>
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
<script src="<?= $baseURL ?>assets/vendor/magnific-popup/magnific-popup.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }


        // ── DataTable ────────────────────────────────────────────────
        let tbl = $('#tblData').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        loadData();

        // Fallback thumbnail when an image file is missing
        function machineThumbFallback() {
            return '<div style="width:50px;height:50px;border-radius:10%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted"></i></div>';
        }

        // ================= LOAD LIST =================
        function loadData() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-machinery.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MACHINERY",
                    employee_id: "<?= (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) ? $_SESSION['user_id'] : '' ?>"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load machines.", "error");
                        return;
                    }
                    let data = res.data || [];
                    if (data.length === 0) {
                        tbl.draw(false);
                        return;
                    }
                    data.forEach(function(item, i) {
                        var imagePrimary = item.image ?
                            `<img src="<?= $baseURL ?>assets/images/machinery/${item.image}" alt="Machine" style="width:50px;height:50px;border-radius:10%;object-fit:cover;" onerror="this.outerHTML=machineThumbFallback();">` :
                            machineThumbFallback();

                        var statusBadge =
                            item.status == 0 ? '<span class="badge light badge-warning">Under Maintenance</span>' :
                            item.status == 1 ? '<span class="badge light badge-success">Available</span>' :
                            '<span class="badge light badge-secondary">Unknown</span>';

                        var actions = `
                        <button class="btn btn-info mr-2 viewBtn"
                            data-id="${item.id}"
                            data-name="${item.name ?? ''}"
                            data-model="${item.model ?? ''}"
                            data-description="${item.description ?? ''}"
                            data-dailyrate="${item.daily_rate ?? ''}"
                            data-status="${item.status ?? 1}"
                            data-branch="${item.branch_name ?? ''}"
                            data-type="${item.machinery_type ?? ''}"
                            data-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        
                        <button class="btn btn-primary shadow editBtn"
                            data-id="${item.id}"
                            data-branchid="${item.branch_id ?? ''}"
                            data-type="${item.type_id ?? ''}"
                            data-name="${item.name ?? ''}"
                            data-model="${item.model ?? ''}"
                            data-description="${item.description ?? ''}"
                            data-dailyrate="${item.daily_rate ?? ''}"
                            data-status="${item.status ?? 1}"
                            data-toggle="tooltip" title="Update Details">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        `;

                        tbl.row.add([
                            i + 1,
                            imagePrimary,
                            item.branch_name ?? '-',
                            item.machinery_type ?? '-',
                            item.name ?? '-',
                            item.description ?? '-',
                            item.model ?? '-',
                            statusBadge,
                            actions
                        ]);
                    });
                    tbl.draw(false);
                },
                error: function(xhr, status, error) {
                    closeLoader();
                    console.error("LIST_MACHINERY request failed:", status, error, xhr.responseText);
                    Swal.fire("Error", "Failed to load machines.", "error");
                }
            });
        }

        // ================= LOAD DROPDOWNS =================
        function loadDropdowns(callback) {
            $.when(
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-branch.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "LIST_BRANCH"
                    })
                }),
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "LIST_MACHINERY_TYPE"
                    })
                })
            ).done(function(branchRes, typeRes) {

                let branches = branchRes[0]?.data || [];
                let branchHtml = `<option value="">Select Branch</option>`;
                branches.forEach(b => {
                    branchHtml += `<option value="${b.id}">${b.name}</option>`;
                });
                $("#add_branch_id").html(branchHtml);
                $("#edit_branch_id").html(branchHtml);

                let types = typeRes[0]?.data?.result || [];
                let typeHtml = `<option value="">Select Type</option>`;
                types.forEach(t => {
                    typeHtml += `<option value="${t.id}">${t.name}</option>`;
                });
                $("#add_type_id").html(typeHtml);
                $("#edit_type_id").html(typeHtml);

                if (callback) callback();

            }).fail(function() {
                Swal.fire("Error", "Failed to load dropdown data.", "error");
            });
        }

        // ================= ADD =================
        $("#btnAddMachine").on("click", function() {
            $("#add_branch_id").val('').trigger('change');
            $("#add_type_id").val('').trigger('change');
            $("#add_name").val('');
            $("#add_model").val('');
            $("#add_description").val('');
            $("#add_daily_rate").val('');
            $("#add_status").val('1');
            $("#add_image_input").val('');

            loadDropdowns(function() {
                $("#addMachineModal").modal("show");
            });
        });

        $("#btnSaveAdd").on("click", async function() {
            let data = {
                trans: "ADD_MACHINERY",
                branch_id: $("#add_branch_id").val(),
                type_id: $("#add_type_id").val(),
                name: $("#add_name").val().trim(),
                model: $("#add_model").val().trim(),
                description: $("#add_description").val().trim(),
                daily_rate: $("#add_daily_rate").val(),
                status: $("#add_status").val()
            };

            if (!data.branch_id || !data.type_id || !data.name) {
                Swal.fire("Required", "Branch, Machine Type and Name are required.", "warning");
                return;
            }

            const files = Array.from($("#add_image_input")[0].files || []);
            data.images = await filesToBase64(files);

            showLoader("Saving...");
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(data),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $("#addMachineModal").modal("hide");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("ADD_MACHINERY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to add machine.", "error");
                }
            });
        });

        // ================= EDIT =================
        $(document).on("click", ".editBtn", function() {

            let id = $(this).data("id");
            let branch = $(this).data("branchid");
            let type = $(this).data("type");
            let name = $(this).data("name");
            let model = $(this).data("model");
            let description = $(this).data("description");
            let dailyrate = $(this).data("dailyrate");
            let status = $(this).data("status");

            $("#edit_image_input").val('');
            loadEditImages(id);
            loadDropdowns(function() {

                $("#edit_id").val(id);
                $("#edit_branch_id").val(branch).trigger('change');
                $("#edit_type_id").val(type).trigger('change');
                $("#edit_name").val(name);
                $("#edit_model").val(model);
                $("#edit_description").val(description);
                $("#edit_daily_rate").val(dailyrate);
                $("#edit_status").val(status);

                $("#editMachineModal").modal("show");
            });
        });

        // ================= LOAD EDIT IMAGES =================
        function loadEditImages(machineryId) {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_MACHINERY_IMAGES",
                    id: machineryId
                }),
                success: function(res) {
                    renderEditImages(res.data || [], machineryId);
                }
            });
        }

        function renderEditImages(images, machineryId) {
            const $container = $("#currentImages").empty();
            $("#imageCount").text(images.length);

            if (!images.length) {
                $container.html(`
                    <div class="col-12 border rounded-3 bg-light text-center text-muted py-4">
                        <i class="fas fa-image" style="font-size:1.5rem;"></i>
                        <p class="mb-0 mt-1 small">No images uploaded</p>
                    </div>
                `);
                return;
            }

            images.forEach(function(img) {
                const primaryBadge = img.is_primary == 1 ?
                    '<span class="badge badge-success position-absolute top-0 start-0 m-1" style="font-size:10px;">Primary</span>' :
                    '';
                $container.append(`
                    <div class="col-md-3 col-6">
                        <div class="position-relative rounded-3 overflow-hidden border" data-img-id="${img.id}" data-img-name="${img.name}">
                            <img src="<?= $baseURL ?>assets/images/machinery/${img.name}" alt="${img.name}" class="w-100 object-fit-cover d-block" style="height:120px;cursor:pointer;">
                            ${primaryBadge}
                            <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle d-flex align-items-center justify-content-center edit-img-delete" style="width:24px;height:24px;padding:0;" title="Remove image">
                                <i class="fas fa-times" style="font-size:10px;"></i>
                            </button>
                        </div>
                    </div>
                `);
            });
        }

        // ================= DELETE SINGLE IMAGE =================
        $(document).on("click", ".edit-img-delete", function(e) {
            e.stopPropagation();
            const $wrap = $(this).closest(".position-relative");
            const imgId = $wrap.data("img-id");
            const imgName = $wrap.data("img-name");
            const machineryId = $("#edit_id").val();

            Swal.fire({
                title: "Remove image?",
                text: "This image will be permanently deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Yes, remove it"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_MACHINERY_IMAGE",
                            id: imgId,
                            name: imgName
                        }),
                        success: function(res) {
                            if (res.code == 0) {
                                loadEditImages(machineryId);
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function() {
                            Swal.fire("Error", "Failed to delete image.", "error");
                        }
                    });
                }
            });
        });

        // ================= FILE -> BASE64 =================
        function filesToBase64(files) {
            const promises = files.map(function(file) {
                return new Promise(function(resolve, reject) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        resolve(reader.result);
                    };
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
            });
            return Promise.all(promises);
        }

        // ================= UPDATE =================
        $("#updateMachineBtn").on("click", async function() {

            const files = Array.from($("#edit_image_input")[0].files || []);
            const base64Array = await filesToBase64(files);

            showLoader("Updating...");
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "UPDATE_MACHINERY",
                    id: $("#edit_id").val(),
                    branch_id: $("#edit_branch_id").val(),
                    type_id: $("#edit_type_id").val(),
                    name: $("#edit_name").val(),
                    model: $("#edit_model").val(),
                    description: $("#edit_description").val(),
                    daily_rate: $("#edit_daily_rate").val(),
                    status: $("#edit_status").val(),
                    images: base64Array
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $("#editMachineModal").modal("hide");
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("UPDATE_MACHINERY failed:", xhr.responseText);
                    Swal.fire("Error", "Something went wrong. Please try again.", "error");
                }
            });
        });


        /* ============================================================
           VIEW MACHINE GALLERY STATE
           ============================================================ */
        const ViewGallery = {
            images: [],
            current: 0,
            _loadToken: 0,

            init(urls, startIndex) {
                this.images = urls || [];
                this.current = startIndex || 0;
                this._renderMain();
                this._renderThumbs();
            },

            go(index) {
                if (index < 0 || index >= this.images.length) return;
                this.current = index;
                this._renderMain();
                this._syncThumbs();
            },

            prev() {
                this.go(this.current - 1);
            },
            next() {
                this.go(this.current + 1);
            },

            /*
             * FIX (v1): use Bootstrap's own d-* utility classes to toggle visibility
             * instead of jQuery .hide()/.show(), which only touch inline styles
             * and can never override a class that ships with !important (like
             * d-block).
             *
             * FIX (v2): don't point the *visible* <img> at a URL until it's
             * confirmed to load. Swapping src directly means the browser can
             * briefly render the native "broken image" icon + alt text inside
             * the 260px box before the error handler has a chance to hide it -
             * that's the "image blocking the modal" flash. Instead we preload
             * with a throwaway Image() object off-DOM, and only touch the real
             * <img> once we know the result (success -> show it, failure ->
             * never let it become visible at all).
             *
             * We also stamp a request token so that if the user pages through
             * images quickly, a slow-loading older request can't clobber a
             * newer one when it finally resolves.
             */
            _renderMain() {
                const url = this.images[this.current] || '';
                const total = this.images.length || 0;
                const $img = $('#viewGalleryMainImg');
                const $ph = $('#viewGalleryPlaceholder');
                const token = ++this._loadToken;

                // Always hide the real <img> immediately; nothing gets shown
                // until we know it's actually loadable.
                $img.removeClass('d-block').addClass('d-none').removeAttr('src');

                $('#viewGalleryCounter').text(total ? `${this.current + 1} / ${total}` : '0 / 0');
                $('#viewGalleryPrev, #viewGalleryNext, #viewGalleryCounter').toggle(total > 0);
                $('#viewGalleryPrev').prop('disabled', this.current === 0);
                $('#viewGalleryNext').prop('disabled', this.current === total - 1);

                if (!url) {
                    $ph.removeClass('d-none').addClass('d-flex');
                    return;
                }

                // Show placeholder while we verify the image loads; swap only on success.
                $ph.removeClass('d-none').addClass('d-flex');

                const preload = new Image();
                preload.onload = () => {
                    if (token !== this._loadToken) return; // stale response, user moved on
                    $ph.removeClass('d-flex').addClass('d-none');
                    $img.attr('src', url).removeClass('d-none').addClass('d-block');
                };
                preload.onerror = () => {
                    if (token !== this._loadToken) return;
                    // stays on placeholder; real <img> never becomes visible
                };
                preload.src = url;
            },

            _renderThumbs() {
                const $strip = $('#viewGalleryThumbs').empty();
                if (this.images.length < 2) {
                    $strip.hide();
                    return;
                }
                $strip.show();
                this.images.forEach((url, i) => {
                    $(`<button type="button" class="btn p-0 border rounded-3 overflow-hidden flex-shrink-0 d-block" style="width:60px;height:60px;">
                        <img src="${url}" alt="thumb ${i+1}" class="w-100 h-100 object-fit-cover d-block" onerror="this.parentElement.style.display='none';">
                    </button>`)
                        .on('click', () => this.go(i))
                        .appendTo($strip);
                });
                this._syncThumbs();
            },

            _syncThumbs() {
                $('#viewGalleryThumbs button')
                    .removeClass('border-2 border-primary')
                    .eq(this.current).addClass('border-2 border-primary');
            }
        };

        // Open fullscreen viewer (Magnific Popup)
        $('#viewGalleryMainImg').on('click', function() {
            const urls = ViewGallery.images;
            if (!urls.length) return;
            $.magnificPopup.open({
                items: urls.map(url => ({
                    src: url
                })),
                type: 'image',
                gallery: {
                    enabled: true,
                    navigateByImgClick: true
                },
                image: {
                    titleSrc: function(item) {
                        return ViewGallery.images.length ? ('Image ' + (item.index + 1) + ' / ' + ViewGallery.images.length) : '';
                    }
                }
            }, ViewGallery.current);
        });

        // Gallery nav binds
        $('#viewGalleryPrev').on('click', () => ViewGallery.prev());
        $('#viewGalleryNext').on('click', () => ViewGallery.next());

        // ================= VIEW =================
        $(document).on("click", ".viewBtn", function() {
            const id = $(this).data("id");
            const name = $(this).data("name") || '—';
            const model = $(this).data("model") || '—';
            const description = $(this).data("description") || '—';
            const dailyrate = parseFloat($(this).data("dailyrate")) || 0;
            const status = parseInt($(this).data("status"));
            const branchName = $(this).data("branch") || '—';
            const type = $(this).data("type") || '—';

            const statusMap = {
                0: '<span class="badge light badge-warning">Under Maintenance</span>',
                1: '<span class="badge light badge-success">Available</span>',
                2: '<span class="badge light badge-info">For Approval</span>',
                3: '<span class="badge light badge-primary">Approved</span>',
                4: '<span class="badge light badge-dark">Borrowed</span>'
            };

            $('#viewModalTitle').text(name);
            $('#viewDetailName').text(name);
            $('#viewDetailModel').text(model ? 'Model: ' + model : '');
            $('#viewDetailPrice').text(dailyrate ? '₱' + dailyrate.toFixed(2) + ' / day' : '—');

            $('#viewDetailRows').html(`
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Branch</span>
                    <span class="fw-semibold text-end">${branchName}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Type</span>
                    <span class="fw-semibold text-end">${type}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Model</span>
                    <span class="fw-semibold text-end">${model}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Daily Rate</span>
                    <span class="fw-semibold text-end">₱${dailyrate.toFixed(2)}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                    <span class="text-muted">Status</span>
                    <span class="fw-semibold text-end">${statusMap[status] || '<span class="badge light badge-secondary">Unknown</span>'}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 small">
                    <span class="text-muted">Description</span>
                    <span class="fw-semibold text-end">${description}</span>
                </div>
            `);

            // Reset gallery
            ViewGallery.init([], 0);

            $('#viewMachineModal').modal('show');

            // Fetch images
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-machinery.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_MACHINERY_IMAGES",
                    id: id
                }),
                success: function(res) {
                    const base = "<?= $baseURL ?>assets/images/machinery/";
                    let urls = [];
                    if (res.code === 0 && Array.isArray(res.data) && res.data.length) {
                        urls = res.data.map(img => base + img.name);
                    }
                    ViewGallery.init(urls, 0);
                }
            });
        });

    });
</script>
<?= endSection() ?>