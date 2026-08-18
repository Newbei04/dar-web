<?= startSection('css') ?>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseURL ?>beneficiary-list">Beneficiary</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Beneficiary</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Beneficiary</h4>
                    <a href="<?= $baseURL ?>beneficiary-list" class="btn btn-outline-secondary btn-sm">
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
                                    <label for="password" class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
                                    <input type="password" class="form-control" id="password" placeholder="Enter new password">
                                </div>
                            </div>
                        </div>

                        <!-- BRANCH -->
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) { ?>
                            <div class="row" id="branchField">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="branch_id" class="form-label fw-bold">Branch <span class="text-muted">(Optional)</span></label>
                                        <select id="branch_id" class="form-control single-select">
                                            <option value="" disabled selected>Select Branch (Optional)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="birthday" class="form-label">Birthday <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="birthday" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select id="gender" class="form-control single-select" required>
                                        <option value="" disabled selected>Select</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="marital" class="form-label">Marital Status <span class="text-danger">*</span></label>
                                    <select id="marital" class="form-control single-select" required>
                                        <option value="" disabled selected>Select</option>
                                        <option value="S">Single</option>
                                        <option value="M">Married</option>
                                        <option value="D">Divorced</option>
                                        <option value="W">Widowed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="doc_num" class="form-label">Document / ID Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="doc_num" placeholder="e.g. 00-000000-0" maxlength="11" required readonly>
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
                                    <input type="text" class="form-control" id="mobile" placeholder="09XXXXXXXXX" maxlength="11" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" class="form-control single-select" required>
                                        <option value="0">For Verification</option>
                                        <option value="1">Verified</option>
                                        <option value="2">Inactive</option>
                                        <option value="3">Deactivated</option>
                                    </select>
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
                                    <select class="form-control single-select" id="region">
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

                        <div class="d-flex justify-content-end">
                            <a href="<?= $baseURL ?>beneficiary-list" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fa fa-save me-1"></i> Save Beneficiary
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

        loadBranches();

        function loadBranches() {
            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BRANCH",
                }),
                success: function(res) {
                    $('#branch_id')
                        .empty()
                        .append(`<option value="" disabled selected>Select Branch (Optional)</option>`);
                    let items = res.data || [];
                    items.forEach(function(item) {
                        $('#branch_id').append(`
                        <option value="${item.id}">
                            ${item.name}
                        </option>
                    `);
                    });
                    reinitSelect2('#branch_id');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $("#id").val(getUrlParameter('id'));

        // Load regions first, then load beneficiary data
        getRegionList(() => {
            loadBeneficiary();
        });

        /* ================= LOAD BENEFICIARY ================= */
        function loadBeneficiary() {

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_BENEFICIARY",
                    id: $("#id").val()
                }),

                success: function(res) {

                    if (res.code == 0) {

                        let p = res.data;

                        $("#username").val(p.username);
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
                        $("#doc_num").val(p.doc_num || '');

                        $("#email").val(p.email);
                        $("#mobile").val(p.mobile);
                        $("#street").val(p.street);
                        $("#status").val(p.status != null ? p.status : 1).trigger('change');

                        if (p.branch_id) {
                            $("#branch_id").val(p.branch_id).trigger('change');
                        }

                        // Load existing profile photo
                        if (p.profile) {
                            $("#avatarImg").attr("src", "<?= $baseURL ?>assets/images/profile/" + p.profile).show();
                            $("#avatarIcon").hide();
                            $("#removePhoto").show();
                            $("#avatarPreview").css("border", "2px solid #ced4da");
                        }

                        // Set location cascade
                        cascading = true;
                        setRegion(p.region_id, function() {
                            setProvince(p.province_id, function() {
                                setCity(p.city_id, function() {
                                    setSubmuni(p.district_id, function() {
                                        $("#barangay").val(p.barangay_id).trigger('change');
                                        $("#region, #province, #city, #submuni, #barangay").trigger('change');
                                        cascading = false;
                                    });
                                });
                            });
                        });
                    } else {
                        Swal.fire("Error", res.message || "Beneficiary not found.", "error");
                    }
                },
                error: function(xhr) {
                    console.error("GET_BENEFICIARY failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load beneficiary details.", "error");
                }
            });
        }

        /* ================= UPDATE BENEFICIARY ================= */
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
                trans: "UPDATE_BENEFICIARY",
                id: $("#id").val(),
                username: $("#username").val(),
                status: $("#status").val(),

                fname: $("#fname").val(),
                mname: $("#mname").val(),
                lname: $("#lname").val(),
                gender: $("#gender").val(),
                marital: $("#marital").val(),
                birthday: $("#birthday").val(),
                email: $("#email").val(),
                mobile: $("#mobile").val(),
                doc_num: $("#doc_num").val(),

                profile: profile,
                branch_id: $("#branch_id").val() || null,

                street: $("#street").val(),
                barangay_id: $("#barangay").val(),
                city_id: $("#city").val(),
                province_id: $("#province").val(),
                region_id: $("#region").val(),
                district_id: $("#submuni").val() || null,
                country: "PH",
                address: address
            };

            if ($("#password").val()) {
                postData.password = $("#password").val();
            }

            $.ajax({
                url: "<?= $baseURL ?>controller/ctrl-beneficiary.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify(postData),

                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire("Success", res.message, "success").then(() => {
                            window.location.href = "<?= $baseURL ?>beneficiary-list";
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

    });
</script>
<?= endSection() ?>
