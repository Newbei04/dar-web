<?= startSection('css') ?>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>list-facility">Facility</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Facility</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Facility</h4>
                    <a href="<?= $baseURL ?>list-facility" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="submitRequest">
                        <input type="hidden" id="id">
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
                                        <option value="1">Active</option>
                                        <option value="2">Not Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= $baseURL ?>list-facility" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fa fa-save me-1"></i> Update Facility
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

    let facilityData = null;
    let cascading = false;

    $(document).ready(function() {

        $("#id").val(getUrlParameter('id'));

        loadBranches(() => {
            loadFacilityTypes(() => {
                getRegionList(() => {
                    loadEmployees(() => {
                        loadFacility();
                    });
                });
            });
        });

        /* ================= LOAD FACILITY ================= */
        function loadFacility() {

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-facility.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_FACILITY",
                    id: $("#id").val()
                }),
                success: function(res) {

                    if (res.code == 0) {

                        let p = res.data;
                        if (Array.isArray(p)) p = p[0];

                        facilityData = p;

                        /* BASIC */
                        $("#branch_id").val(p.branch_id || "").trigger('change');
                        $("#employee_id").val(p.employee_id || "").trigger('change');
                        $("#name").val(p.name || "");
                        $("#phone").val(p.phone || "");
                        $("#email").val(p.email || "");
                        $("#street").val(p.street || "");
                        $("#status").val(p.status || "").trigger('change');

                        let typeIds = p.type_ids ? String(p.type_ids).split(',').filter(Boolean) : [];
                        $("#type_ids").val(typeIds).trigger('change');

                        /* REGION FLOW */
                        cascading = true;
                        setRegion(p.region_id, function() {
                            setProvince(p.province_id, function() {
                                setCity(p.city_id, function() {
                                    setSubmuni(p.district_id, function() {
                                        $("#barangay").val(p.barangay_id).trigger('change');
                                        cascading = false;
                                    });
                                });
                            });
                        });
                    }
                }
            });
        }

        /* ================= SUBMIT ================= */
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
                    trans: "UPDATE_FACILITY",
                    id: $("#id").val(),

                    branch_id: $("#branch_id").val(),
                    type_ids: $("#type_ids").val(),
                    employee_id: $("#employee_id").val() || null,
                    name: $("#name").val(),
                    phone: $("#phone").val(),
                    email: $("#email").val(),

                    street: $("#street").val(),

                    region_id: $("#region").val(),
                    province_id: $("#province").val(),
                    city_id: $("#city").val(),
                    district_id: $("#submuni").val() || null,
                    barangay_id: $("#barangay").val(),

                    address: address,
                    status: $("#status").val()
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success")
                            .then(() => {
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

    /* ================= LOADERS ================= */

    function loadBranches(cb) {
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-branch.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                trans: "LIST_BRANCH"
            }),
            success: function(res) {
                let d = $("#branch_id");
                d.html('<option value="" disabled selected>Select Branch</option>');
                if (res.code == 0 && res.data) {
                    res.data.forEach(function(b) {
                        d.append(`<option value="${b.id}">${b.code} — ${b.name}</option>`);
                    });
                }
                reinitSelect2(d);
                cb && cb();
            }
        });
    }

    function loadFacilityTypes(cb) {
        $.ajax({
            url: "<?= $baseURL ?>controller/ctrl-facility-type.php",
            type: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                trans: "LIST_FACILITY_TYPE"
            }),
            success: function(res) {
                let d = $("#type_ids");
                d.html(`<option value="" disabled>Select Facility Type</option>`);
                (res.data?.result || []).forEach(i => {
                    d.append(`<option value="${i.id}">${i.name}</option>`);
                });
                reinitSelect2(d);
                cb && cb();
            }
        });
    }

    function loadEmployees(cb) {
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
                let d = $("#employee_id");
                d.html('<option value="" disabled selected>Select Employee</option>');
                (res.data || []).forEach(function(item) {
                    let profile = item.profile || {};
                    let fullname = [
                        profile.fname,
                        profile.mname,
                        profile.lname
                    ].filter(Boolean).join(' ');
                    d.append(`<option value="${item.id}">${fullname || item.username}</option>`);
                });
                reinitSelect2(d);
                cb && cb();
            }
        });
    }

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
                reinitSelect2(d);
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
                reinitSelect2(d);
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
                reinitSelect2(d);
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
                reinitSelect2(d);
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
                reinitSelect2(d);
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
                reinitSelect2(d);
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
                reinitSelect2(d);
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
        $("#region").val(val).trigger('change');
        $("#submuniField").hide();
        if (val == NCR_CODE) {
            $("#provinceField").hide();
            $("#province").val("").trigger('change');
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
        $("#province").val(val).trigger('change');
        getCityList(val, callback);
    }

    function setCity(val, callback) {
        if (!val) {
            if (callback) callback();
            return;
        }
        $("#city").val(val).trigger('change');
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
        $("#submuni").val(val).trigger('change');
        getBarangayByDistrict(val, callback);
    }

    function getUrlParameter(name) {
        return new URL(window.location.href).searchParams.get(name);
    }
</script>

<?= endSection() ?>
