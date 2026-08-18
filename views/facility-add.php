<?= startSection('css') ?>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-facility">Facility</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Facility</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Add Facility</h4>
                    <a href="<?= $baseURL ?>list-facility" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="submitRequest">
                        <!-- FACILITY INFO -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Facility Info</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                                    <select id="branch_id" class="form-control single-select" required>
                                        <option value="" disabled selected>Select Branch</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_ids" class="form-label">Facility Type <span class="text-danger">*</span></label>
                                    <select id="type_ids" class="form-control single-select" multiple required>
                                        <option value="" disabled>Select Facility Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Assigned Employee</label>
                                    <select id="employee_id" class="form-control single-select">
                                        <option value="" disabled selected>Select Employee</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Facility Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" placeholder="Enter facility name" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" placeholder="Enter phone number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="email@example.com">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- ADDRESS -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Address Details</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="region" class="form-label">Region <span class="text-danger">*</span></label>
                                    <select class="form-control single-select" id="region" required>
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

                        <!-- STATUS -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Status</small></h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control single-select" id="status" required>
                                        <option value="0">Pending</option>
                                        <option value="1" selected>Active</option>
                                        <option value="2">Not Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= $baseURL ?>list-facility" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fa fa-save me-1"></i> Save Facility
                            </button>
                        </div>
                    </form>
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
    const API_URL = "<?= $baseURL ?>controller/ctrl-location.php";

    $(document).ready(function() {

        loadBranches();
        loadFacilityTypes();
        loadEmployees();
        getRegionList();

        // ================= CASCADE EVENTS =================

        $("#region").on("change", function() {
            let val = $(this).val();

            $("#province").prop("disabled", true).html('<option value="">Select Province</option>');
            reinitSelect2("#province");
            $("#city").prop("disabled", true).html('<option value="">Select City</option>');
            reinitSelect2("#city");
            $("#barangay").prop("disabled", true).html('<option value="">Select Barangay</option>');
            reinitSelect2("#barangay");
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
            let val = $(this).val();
            $("#city").prop("disabled", true).html('<option value="">Select City</option>');
            reinitSelect2("#city");
            $("#barangay").prop("disabled", true).html('<option value="">Select Barangay</option>');
            reinitSelect2("#barangay");
            if (!val) return;
            getCityList(val);
        });

        $("#city").on("change", function() {
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
            let val = $(this).val();
            if (!val) return;
            getBarangayByDistrict(val);
        });

        // ================= SUBMIT =================
        $("#submitRequest").submit(function(e) {
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

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-facility.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "ADD_FACILITY",
                    branch_id: $("#branch_id").val(),
                    type_ids: $("#type_ids").val(),
                    employee_id: $("#employee_id").val() || null,
                    name: $("#name").val(),
                    phone: $("#phone").val(),
                    email: $("#email").val(),

                    address: address,

                    region_id: $("#region").val(),
                    street: $("#street").val(),
                    province_id: $("#province").val(),
                    city_id: $("#city").val(),
                    district_id: $("#submuni").val() || null,
                    barangay_id: $("#barangay").val(),

                    status: $("#status").val()
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success").then(() => {
                            window.location.href = "<?= $baseURL ?>list-facility";
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

    });

    // ================= LOADERS =================

    function loadBranches() {
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-branch.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                trans: "LIST_BRANCH"
            }),
            success: function(res) {
                let dropdown = $("#branch_id");
                dropdown.html('<option value="" disabled selected>Select Branch</option>');
                if (res.code == 0 && res.data) {
                    res.data.forEach(function(b) {
                        dropdown.append(`<option value="${b.id}">${b.code} — ${b.name}</option>`);
                    });
                }
                reinitSelect2(dropdown);
            },
            error: function() {
                $("#branch_id").html('<option value="" disabled selected>Select Branch</option>');
                reinitSelect2("#branch_id");
            }
        });
    }

    function loadFacilityTypes() {
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-facility-type.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                trans: "LIST_FACILITY_TYPE",
            }),
            success: function(res) {
                let dropdown = $("#type_ids");
                dropdown.html(`<option value="" disabled>Select Facility Type</option>`);
                (res.data?.result || []).forEach(item => {
                    dropdown.append(`<option value="${item.id}">${item.name}</option>`);
                });
                reinitSelect2(dropdown);
            },
            error: function() {
                $("#type_ids").html('<option value="" disabled>Select Facility Type</option>');
                reinitSelect2("#type_ids");
            }
        });
    }

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
                let dropdown = $("#employee_id");
                dropdown.html('<option value="" disabled selected>Select Employee</option>');
                (res.data || []).forEach(item => {
                    let profile = item.profile || {};
                    let fullname = [
                        profile.fname,
                        profile.mname,
                        profile.lname
                    ].filter(Boolean).join(' ');
                    dropdown.append(`<option value="${item.id}">${fullname || item.username}</option>`);
                });
                reinitSelect2(dropdown);
            },
            error: function() {
                $("#employee_id").html('<option value="" disabled selected>Select Employee</option>');
                reinitSelect2("#employee_id");
            }
        });
    }

    // ================= LOCATION FUNCTIONS =================

    function getRegionList() {
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
                reinitSelect2(d);
            },
            error: function() {
                console.error("Failed to load regions");
            }
        });
    }

    function getProvinceList(code) {
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
                reinitSelect2(d);
            },
            error: function() {
                console.error("Failed to load provinces");
            }
        });
    }

    function getCityList(code) {
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
                reinitSelect2(d);
            },
            error: function() {
                console.error("Failed to load cities");
            }
        });
    }

    function getNCRCityList() {
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
                reinitSelect2(d);
            },
            error: function() {
                console.error("Failed to load NCR cities");
            }
        });
    }

    function getDistrictList() {
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
                reinitSelect2(d);
            },
            error: function() {
                console.error("Failed to load districts");
            }
        });
    }

    function getBarangayList(code) {
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
                reinitSelect2(d);
            },
            error: function() {
                console.error("Failed to load barangays");
            }
        });
    }

    function getBarangayByDistrict(code) {
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
                reinitSelect2(d);
            },
            error: function() {
                console.error("Failed to load barangays by district");
            }
        });
    }
</script>

<?= endSection() ?>
