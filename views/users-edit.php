<?= startSection('css') ?>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>user-list">User</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit User</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit User</h4>
                    <a href="<?= $baseURL ?>user-list" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="submitUser">
                        <input type="hidden" id="id">
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
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select id="role_id" class="form-control default-select" required>
                                        <option value="" disabled selected>Select Role</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="facilityField" style="display:none;">
                                <div class="mb-3">
                                    <label for="facility_id" class="form-label fw-bold">Facility <span class="text-danger">*</span></label>
                                    <select id="facility_id" class="form-control default-select">
                                        <option value="" disabled selected>Select Facility</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- PERSONAL INFO -->
                        <h6 class="text-uppercase text-muted mb-4 fw-bold"><small>Personal Info</small></h6>

                        <!-- PROFILE PHOTO -->
                        <div class="text-center mb-4">
                            <div id="avatarPreview" style="
                                width: 100px; height: 100px; border-radius: 50%;
                                background: #e9ecef; border: 2px dashed #ced4da;
                                display: flex; align-items: center; justify-content: center;
                                margin: 0 auto 10px; cursor: pointer; overflow: hidden;">
                                <i class="fas fa-user fa-2x text-muted" id="avatarIcon"></i>
                                <img id="avatarImg" src="" alt="Profile Photo"
                                    style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;">
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
                                    <select id="gender" class="form-control" required>
                                        <option value="" disabled selected>Select</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="marital" class="form-label">Marital Status <span class="text-danger">*</span></label>
                                    <select id="marital" class="form-control" required>
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
                                    <select class="form-control default-select" id="region">
                                        <option value="">Select Region</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="provinceField">
                                <div class="mb-3">
                                    <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                                    <select class="form-control default-select" id="province" disabled>
                                        <option value="">Select Province</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City / Municipality <span class="text-danger">*</span></label>
                                    <select class="form-control default-select" id="city" disabled>
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="submuniField" style="display:none;">
                                <div class="mb-3">
                                    <label for="submuni" class="form-label">District</label>
                                    <select class="form-control default-select" id="submuni" disabled>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                                    <select class="form-control default-select" id="barangay" disabled>
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
                            <a href="<?= $baseURL ?>user-list" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fa fa-save me-1"></i> Save User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
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
    <?= endSection() ?>


    <?= startSection('scripts') ?>
    <script>
        const CONFIG = "<?= $baseURL ?>config/";
        const MANILA_CODE = "133900000";
        const NCR_CODE = "130000000";
        const API_URL = "<?= $baseURL ?>controller/ctrl-location.php";

        let userData = null;
        let profile = null;
        let cascading = false;

        $(document).ready(function() {

            // ── Profile photo ──────────────────────────────────────────
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

            loadGetAllUsersRole();
            loadBranch();

            // Hide facility field if role is admin (admin role_id = 1)
            $("#role_id").on("change", function() {
                let roleId = $(this).val();
                if (roleId == 1) {
                    $("#facilityField").hide();
                    $("#branch_id").attr("required", false).val("").trigger('change');
                } else {
                    $("#facilityField").show();
                    $("#branch_id").attr("required", true);
                }
            });

            function loadGetAllUsersRole() {
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-users.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "GET_ALL_USER_ROLE"
                    }),

                    success: function(res) {
                        $('#role_id')
                            .empty()
                            .append(`<option value="" disabled selected>Select Role</option>`);
                        let items = res.data || [];
                        items.forEach(function(item) {
                            $('#role_id').append(`
                        <option value="${item.id}">
                            ${item.title} (${item.description})
                        </option>
                    `);
                        });
                        $('#role_id').trigger('change');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }

                });

            }

            function loadBranch() {
                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-branch.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "LIST_BRANCH",
                    }),

                    success: function(res) {
                        console.log(res);
                        $('#branch_id')
                            .empty()
                            .append(`<option value="" disabled selected>Select Branch</option>`);
                        let items = res.data || [];
                        items.forEach(function(item) {

                            $('#branch_id').append(`
                        <option value="${item.id}">
                            ${item.name}
                        </option>
                    `);
                        });
                        $('#branch_id').trigger('change');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }

                });

            }

            $("#id").val(getUrlParameter('id'));

            // Load regions first, then load user data
            getRegionList(() => {
                loadUser();
            });

            /* ================= LOAD USER ================= */
            function loadUser() {

                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-users.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify({
                        trans: "GET_USER",
                        id: $("#id").val()
                    }),

                    success: function(res) {

                        if (res.code == 0) {

                            userData = res.data;

                            let p = userData.profile || {};

                            $("#username").val(userData.username);
                            $("#role_id").val(userData.role_id).trigger('change');

                            $("#fname").val(p.fname);
                            $("#mname").val(p.mname);
                            $("#lname").val(p.lname);

                            const genderMap = {
                                'Male': 'M',
                                'Female': 'F'
                            };
                            const maritalMap = {
                                'Single': 'S',
                                'Married': 'M',
                                'Divorced': 'D',
                                'Widowed': 'W'
                            };
                            $("#gender").val(genderMap[p.gender] || p.gender || '').trigger('change');
                            $("#marital").val(maritalMap[p.marital] || p.marital || '').trigger('change');
                            $("#birthday").val(p.birthday || '');

                            $("#email").val(p.email);
                            $("#mobile").val(p.mobile);

                            $("#street").val(p.street);
                            $("#status").val(userData.status).trigger('change');

                            // Load existing profile photo
                            if (p.profile) {
                                $("#avatarImg").attr("src", "<?= $baseURL ?>assets/images/profile/" + p.profile).show();
                                $("#avatarIcon").hide();
                                $("#removePhoto").show();
                                $("#avatarPreview").css("border", "2px solid #ced4da");
                            }

                            // Load existing facility for cooperative
                            if (userData.role_id == 2 && p.id) {
                                $.ajax({
                                    url: "<?= $baseURL ?>controller/ctrl-branch.php",
                                    type: "POST",
                                    contentType: "application/json",
                                    dataType: "json",
                                    data: JSON.stringify({
                                        trans: "GET_BRANCH_BY_EMPLOYEE",
                                        employee_id: p.id
                                    }),
                                    success: function(res) {
                                        if (res.code == 0 && res.data) {
                                            $("#branch_id").val(res.data.id).trigger('change');
                                        }
                                    }
                                });
                            }

                            // Set location cascade
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
                        }
                    }
                });
            }

            /* ================= UPDATE USER ================= */
            $("#submitUser").submit(function(e) {

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
                    id: $("#id").val(),
                    username: $("#username").val(),
                    role_id: $("#role_id").val(),

                    fname: $("#fname").val(),
                    mname: $("#mname").val(),
                    lname: $("#lname").val(),
                    gender: $("#gender").val(),
                    marital: $("#marital").val(),
                    birthday: $("#birthday").val(),
                    email: $("#email").val(),
                    mobile: $("#mobile").val(),

                    profile: profile,
                    branch_id: $("#branch_id").val() || null,

                    street: $("#street").val(),
                    barangay_id: $("#barangay").val(),
                    city_id: $("#city").val(),
                    province_id: $("#province").val(),
                    region_id: $("#region").val(),
                    district_id: $("#submuni").val() || null,
                    country: "PH",
                    address: address,
                    status: $("#status").val()
                };

                if ($("#password").val()) {
                    postData.password = $("#password").val();
                }

                $.ajax({
                    url: "<?= $baseURL ?>controller/ctrl-users.php",
                    type: "POST",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify(postData),

                    success: function(res) {
                        closeLoader();
                        if (res.code == 0) {

                            Swal.fire("Success", res.message, "success").then(() => {

                                window.location.href = "<?= $baseURL ?>user-list";
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

            function getUrlParameter(name) {
                return new URL(window.location.href).searchParams.get(name);
            }

            /* ================= DELETE USER ================= */
            var currentUserId = "<?= $_SESSION['users_id'] ?? '' ?>";
            var deleteUserId = $("#id").val() || getUrlParameter('id');

            $("#btnDeleteUser").on("click", function() {

                if (deleteUserId && currentUserId && String(deleteUserId) === String(currentUserId)) {
                    Swal.fire("Not Allowed", "You cannot delete your own account.", "warning");
                    return;
                }

                Swal.fire({
                    title: "Delete this user?",
                    text: "This action is permanent and cannot be undone.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, delete',
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    showLoader();
                    $.ajax({
                        url: "<?= $baseURL ?>controller/ctrl-users.php",
                        type: "POST",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            trans: "DELETE_USER",
                            id: deleteUserId
                        }),
                        success: function(res) {
                            closeLoader();
                            if (res.code == 0) {
                                Swal.fire("Deleted", res.message, "success").then(function() {
                                    window.location.href = "<?= $baseURL ?>user-list";
                                });
                            } else {
                                Swal.fire("Error", res.message || "Failed to delete user.", "error");
                            }
                        },
                        error: function(xhr) {
                            closeLoader();
                            console.error("DELETE_USER failed:", xhr.responseText);
                            Swal.fire("Error", "Something went wrong.", "error");
                        }
                    });
                });
            });

        });
    </script>

    <?= endSection() ?>