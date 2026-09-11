<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Land</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Land Monitoring Logs</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Land Monitoring Logs</h4>
                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                        <button type="button" class="btn btn-primary btn-sm" id="btnAddMonitoring">
                            <i class="fas fa-plus me-1"></i> Add Monitoring Log
                        </button>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblData" class="display responsive nowrap w-100">
                            <thead class="fw-bold">
                                <tr>
                                    <th>No.</th>
                                    <th>Title Number</th>
                                    <th>Employee</th>
                                    <th>Log Type</th>
                                    <th>Title</th>
                                    <th>Notes</th>
                                    <th>Created at</th>
                                    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                                        <th>Action</th>
                                    <?php } ?>
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
<?= endSection() ?>

<!-- ADD MONITORING MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Monitoring Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMonitoringForm">
                    <h6 class="text-uppercase text-muted mb-3 fw-bold"><small>Log Details</small></h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_land_parcels_id" class="form-label">Land Parcel <span class="text-danger">*</span></label>
                                <select id="add_land_parcels_id" class="form-control default-select">
                                    <option value="">Select land parcel</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                <select id="add_employee_id" class="form-control default-select">
                                    <option value="">Select employee</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add_log_type" class="form-label">Log Type <span class="text-danger">*</span></label>
                                <select id="add_log_type" class="form-control default-select">
                                    <option value="">Select type</option>
                                    <option value="0">Site Visit</option>
                                    <option value="1">Photo Report</option>
                                    <option value="2">Soil Test</option>
                                    <option value="3">Pest Control</option>
                                    <option value="4">Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="add_title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="add_title" placeholder="Enter title">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="add_notes" rows="4" placeholder="Enter notes..."></textarea>
                    </div>

                    <hr>

                    <h6 class="text-uppercase text-muted mb-3 fw-bold"><small>Images</small></h6>

                    <div class="mb-3">
                        <label for="add_images" class="form-label">Attach Images</label>
                        <input type="file" class="form-control" id="add_images" name="images[]" accept="image/*" multiple>
                        <small class="form-text text-muted">You can select multiple images. JPG, PNG · up to 10 files.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addMonitoringForm" class="btn btn-primary px-4" id="addSaveBtn">
                    <i class="fe fe-save mr-1"></i> Save Log
                </button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MONITORING MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Monitoring Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMonitoringForm">
                    <input type="hidden" id="edit_id">

                    <h6 class="text-uppercase text-muted mb-3 fw-bold"><small>Log Details</small></h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_land_parcels_id" class="form-label">Land Parcel <span class="text-danger">*</span></label>
                                <select id="edit_land_parcels_id" class="form-control default-select">
                                    <option value="">Select land parcel</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                <select id="edit_employee_id" class="form-control default-select">
                                    <option value="">Select employee</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_log_type" class="form-label">Log Type <span class="text-danger">*</span></label>
                                <select id="edit_log_type" class="form-control default-select">
                                    <option value="">Select type</option>
                                    <option value="0">Site Visit</option>
                                    <option value="1">Photo Report</option>
                                    <option value="2">Soil Test</option>
                                    <option value="3">Pest Control</option>
                                    <option value="4">Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="edit_title" placeholder="Enter title">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" rows="4" placeholder="Enter notes..."></textarea>
                    </div>

                    <hr>

                    <h6 class="text-uppercase text-muted mb-3 fw-bold"><small>Images</small></h6>

                    <div class="mb-3">
                        <label class="form-label">Current Images</label>
                        <div id="edit_images_preview" class="d-flex flex-wrap"></div>
                        <input type="hidden" id="edit_deleted_images" value="">
                    </div>

                    <div class="mb-3">
                        <label for="edit_new_images" class="form-label">Add New Images</label>
                        <input type="file" class="form-control" id="edit_new_images" name="new_images[]" accept="image/*" multiple>
                        <small class="form-text text-muted">You can select multiple images. JPG, PNG · up to 10 files.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editMonitoringForm" class="btn btn-primary px-4" id="editSaveBtn">
                    <i class="fe fe-save mr-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<?= startSection('scripts') ?>
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>
<script>
    $(document).ready(function() {

        const API = '<?= $baseURL ?>controller/ctrl-land-monitoring.php';
        const isAdmin = <?= isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1 ? 'true' : 'false' ?>;
        let tbl = $('#tblData').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            }
        });

        loadEmployees();
        loadLandParcels();
        loadData();

        function loadEmployees() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-users.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_USER_ROLE",
                    role_id: 2
                }),
                success: function(res) {
                    let html = '<option value="">Select Employee</option>';
                    if (res.code == 0) {
                        res.data.forEach(p => {
                            html += `<option value="${p.id}">${p.username}</option>`;
                        });
                    }
                    $("#edit_employee_id").html(html);
                    $("#add_employee_id").html(html);
                    $('#edit_employee_id, #add_employee_id').selectpicker('refresh');
                }
            });
        }

        function loadLandParcels() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-land-parcel.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_LAND_PARCEL"
                }),
                success: function(res) {
                    let html = '<option value="">Select Land Parcel</option>';
                    if (res.code == 0) {
                        res.data.forEach(p => {
                            html += `<option value="${p.id}">${p.title_number}</option>`;
                        });
                    }
                    $("#edit_land_parcels_id").html(html);
                    $("#add_land_parcels_id").html(html);
                }
            });
        }

        // ================= LOAD LIST =================
        function loadData() {
            tbl.clear().draw();

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MONITORING"
                }),
                success: function(res) {
                    if (res.code == 0) {
                        let data = res.data || [];
                        tbl.clear();

                        data.forEach(function(item, i) {
                            let row = [
                                i + 1,
                                item.title_number ?? '-',
                                item.employee_name ?? '-',
                                item.log_type == 0 ? 'Site Visit' :
                                item.log_type == 1 ? 'Photo Report' :
                                item.log_type == 2 ? 'Soil Test' :
                                item.log_type == 3 ? 'Pest Control' :
                                item.log_type == 4 ? 'Others' : 'Unknown',
                                item.title ?? '-',
                                item.notes ?? '-',
                                item.created_at ?? '-'
                            ];

                            if (isAdmin) {
                                row.push(`
                                    <button class="btn btn-info mr-2 viewBtn" data-id="${item.id}" data-toggle="tooltip" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-primary shadow editBtn" data-id="${item.id}" data-toggle="tooltip" title="Update Details">
                                        <span class="fas fa-pencil-alt"></span>
                                    </button>
                                    <button class="btn btn-danger shadow deleteBtn" data-id="${item.id}" data-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                `);
                            }

                            tbl.row.add(row);
                        });
                        tbl.draw();
                    }
                }
            });
        }

        // ================= VIEW =================
        $(document).on('click', '.viewBtn', function() {
            let id = $(this).data('id');
            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_MONITORING",
                    id: id
                }),
                success: function(res) {
                    if (res.code == 0 && res.data) {
                        let d = res.data;
                        let logType = d.log_type == 0 ? 'Site Visit' : d.log_type == 1 ? 'Photo Report' : d.log_type == 2 ? 'Soil Test' : d.log_type == 3 ? 'Pest Control' : d.log_type == 4 ? 'Others' : 'Unknown';
                        let imagesHtml = '';
                        if (d.images && d.images.length > 0) {
                            d.images.forEach(img => {
                                imagesHtml += `<img src="<?= $baseURL ?>assets/images/monitoring/${img.name}" class="img-thumbnail m-1" style="max-height:100px;">`;
                            });
                        } else {
                            imagesHtml = '<span class="text-muted">No images</span>';
                        }
                        Swal.fire({
                            title: d.title || 'Monitoring Log',
                            html: `
                                <table class="table table-bordered text-left">
                                    <tr><th>Title Number</th><td>${d.title_number || '-'}</td></tr>
                                    <tr><th>Employee</th><td>${d.employee_name || '-'}</td></tr>
                                    <tr><th>Log Type</th><td>${logType}</td></tr>
                                    <tr><th>Notes</th><td>${d.notes || '-'}</td></tr>
                                    <tr><th>Images</th><td>${imagesHtml}</td></tr>
                                    <tr><th>Created</th><td>${d.created_at || '-'}</td></tr>
                                </table>
                            `,
                            width: 600
                        });
                    }
                }
            });
        });

        // ================= EDIT =================
        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');
            $('#edit_deleted_images').val('');
            $('#edit_new_images').val('');

            $.ajax({
                url: API,
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_MONITORING",
                    id: id
                }),
                success: function(res) {
                    if (res.code == 0 && res.data) {
                        let d = res.data;
                        $('#edit_id').val(d.id);
                        $('#edit_land_parcels_id').val(d.land_parcels_id).selectpicker('refresh');
                        $('#edit_employee_id').val(d.employee_id).selectpicker('refresh');
                        $('#edit_log_type').val(d.log_type).selectpicker('refresh');
                        $('#edit_title').val(d.title);
                        $('#edit_notes').val(d.notes);

                        let imgPreview = '';
                        if (d.images && d.images.length > 0) {
                            d.images.forEach(img => {
                                imgPreview += `
                                    <div class="position-relative d-inline-block m-1" style="display:inline-block;">
                                        <img src="<?= $baseURL ?>assets/images/monitoring/${img.name}" class="img-thumbnail" style="max-height:80px;">
                                        <button type="button" class="btn btn-sm btn-danger removeImgBtn position-absolute" 
                                            style="top:-6px;right:-6px;border-radius:50%;padding:2px 6px;font-size:10px;" 
                                            data-name="${img.name}" title="Remove image">&times;</button>
                                    </div>`;
                            });
                        } else {
                            imgPreview = '<span class="text-muted">No images</span>';
                        }
                        $('#edit_images_preview').html(imgPreview);

                        $('#editModal').modal('show');
                    }
                }
            });
        });

        // Remove existing image标记删除
        $(document).on('click', '.removeImgBtn', function() {
            let name = $(this).data('name');
            let deleted = $('#edit_deleted_images').val();
            let arr = deleted ? deleted.split(',') : [];
            arr.push(name);
            $('#edit_deleted_images').val(arr.join(','));
            $(this).closest('.position-relative').remove();

            if ($('#edit_images_preview').children().length === 0) {
                $('#edit_images_preview').html('<span class="text-muted">No images</span>');
            }
        });

        $('#editMonitoringForm').on('submit', function(e) {
            e.preventDefault();
            $("#editSaveBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            let formData = new FormData();
            formData.append('trans', 'EDIT_MONITORING');
            formData.append('id', $('#edit_id').val());
            formData.append('land_parcels_id', $('#edit_land_parcels_id').val());
            formData.append('employee_id', $('#edit_employee_id').val());
            formData.append('log_type', $('#edit_log_type').val());
            formData.append('title', $('#edit_title').val());
            formData.append('notes', $('#edit_notes').val());
            formData.append('delete_images', $('#edit_deleted_images').val());

            const newImages = $('#edit_new_images')[0].files;
            for (const file of newImages) {
                formData.append('new_images[]', file);
            }

            showLoader();
            $.ajax({
                url: API,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $('#editModal').modal('hide');
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    closeLoader();
                },
                complete: function() {
                    $("#editSaveBtn").prop("disabled", false).html('<i class="fe fe-save mr-1"></i> Save Changes');
                }
            });
        });

        // ================= ADD =================
        $(document).on('click', '#btnAddMonitoring', function() {
            $('#addMonitoringForm')[0].reset();
            $('#add_land_parcels_id').val('').selectpicker('refresh');
            $('#add_employee_id').val('').selectpicker('refresh');
            $('#add_log_type').val('').selectpicker('refresh');
            $('#add_images_preview').empty();
            $('#addModal').modal('show');
        });

        // Preview selected images in the add modal
        $('#add_images').on('change', function() {
            const files = this.files;
            $('#add_images_preview').empty();
            for (const file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#add_images_preview').append(
                        `<img src="${e.target.result}" class="img-thumbnail m-1" style="max-height:80px;">`
                    );
                };
                reader.readAsDataURL(file);
            }
        });

        $('#addMonitoringForm').on('submit', function(e) {
            e.preventDefault();
            $("#addSaveBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            let formData = new FormData();
            formData.append('trans', 'ADD_MONITORING');
            formData.append('land_parcels_id', $('#add_land_parcels_id').val());
            formData.append('employee_id', $('#add_employee_id').val());
            formData.append('log_type', $('#add_log_type').val());
            formData.append('title', $('#add_title').val());
            formData.append('notes', $('#add_notes').val());

            const images = $('#add_images')[0].files;
            for (const file of images) {
                formData.append('images[]', file);
            }

            showLoader();
            $.ajax({
                url: API,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success");
                        $('#addModal').modal('hide');
                        $('#addMonitoringForm')[0].reset();
                        $('#add_land_parcels_id').val('').selectpicker('refresh');
                        $('#add_employee_id').val('').selectpicker('refresh');
                        $('#add_log_type').val('').selectpicker('refresh');
                        $('#add_images_preview').empty();
                        loadData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function() {
                    closeLoader();
                },
                complete: function() {
                    $("#addSaveBtn").prop("disabled", false).html('<i class="fe fe-save mr-1"></i> Save Log');
                }
            });
        });

        // ================= DELETE =================
        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This monitoring log will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.ajax({
                        url: API,
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_MONITORING",
                            id: id
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                Swal.fire("Deleted!", res.message, "success");
                                loadData();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function() {
                            closeLoader();
                        }
                    });
                }
            });
        });

    });
</script>
<?= endSection() ?>