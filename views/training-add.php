<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Training</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Training List</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Training</a></li>
        </ol>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">

            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Add Training</h4>
                        <small class="text-muted">Create a new training for beneficiaries</small>
                    </div>
                    <a href="<?= $basePath ?>/list-training" class="btn btn-sm btn-outline-secondary ms-auto">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="trainingForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Accreditation Number</label>
                                <input type="text" id="accreditation_no" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Accreditation Date</label>
                                <input type="date" id="accreditation_date" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Summary</label>
                            <textarea id="summary" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Objectives</label>
                            <textarea id="objectives" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Promotional Image</label>
                            <input type="file" id="promotional_image" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label>Discussion File</label>
                            <input type="file"
                                id="discussion_file"
                                name="discussion_file"
                                class="form-control"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Start Date</label>
                                <input type="date" id="start_at" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>End Date</label>
                                <input type="date" id="end_at" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Program Type</label>
                            <select id="program_type" class="form-control">
                                <option value="">Select Program Type</option>
                                <option value="0">OTHER</option>
                                <option value="1">ONSITE TRAINING</option>
                                <option value="2">VIRTUAL LEARNING</option>
                            </select>
                        </div>

                        <hr>

                        <h6 class="text-uppercase text-muted mb-3"><small>Address</small></h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Region</label>
                                <select class="form-control select2" id="region">
                                    <option value="">Select Region</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="provinceField">
                                <label>Province</label>
                                <select class="form-control select2" id="province" disabled>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>City / Municipality</label>
                                <select class="form-control select2" id="city" disabled>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="submuniField" style="display:none;">
                                <label>District</label>
                                <select class="form-control select2" id="submuni" disabled>
                                    <option value="">Select District</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Barangay</label>
                                <select class="form-control select2" id="barangay" disabled>
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Street / Landmark</label>
                            <textarea class="form-control" id="street" rows="2" placeholder="House no., street, landmark..."></textarea>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= $basePath ?>/list-training" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Training</button>
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
    const API = "<?= $baseURL ?>controller/ctrl-training.php";
    const API_URL = "<?= $baseURL ?>controller/ctrl-location.php";

    $(document).ready(function() {

        $('.select2').select2();

        $("#region").change(function() {

            let val = $(this).val();

            $("#province, #city, #barangay").val('').prop("disabled", true);

            if (!val) return;

            if (val === NCR_CODE) {
                $("#provinceField").hide();
                getNCRCityList();
            } else {
                $("#provinceField").show();
                getProvinceList(val);
            }
        });

        $("#province").change(function() {
            let val = $(this).val();

            $("#city, #barangay").val('').prop("disabled", true);

            if (!val) return;

            getCityList(val);
        });

        $("#city").change(function() {

            let val = $(this).val();

            $("#barangay").val('').prop("disabled", true);

            if (!val) return;

            if (val === MANILA_CODE) {
                $("#submuniField").show();
                getDistrictList();
            } else {
                $("#submuniField").hide();
                getBarangayList(val);
            }
        });

        $("#submuni").change(function() {
            let val = $(this).val();
            if (!val) return;
            getBarangayByDistrict(val);
        });

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
                },
                error: function() {
                    console.error("Failed to load barangays by district");
                }
            });
        }

        $("#trainingForm").submit(function(e) {
            e.preventDefault();

            let formData = new FormData();

            formData.append("trans", "ADD_TRAINING");

            // Accreditation
            formData.append("accreditation_no", $("#accreditation_no").val());
            formData.append("accreditation_date", $("#accreditation_date").val());

            // Training Information
            formData.append("title", $("#title").val());
            formData.append("summary", $("#summary").val());
            formData.append("objectives", $("#objectives").val());

            formData.append("start_at", $("#start_at").val());
            formData.append("end_at", $("#end_at").val());

            formData.append("program_type", $("#program_type").val());

            // Address
            formData.append("street", $("#street").val());
            formData.append("barangay_id", $("#barangay").val());
            formData.append("city_id", $("#city").val());
            formData.append("province_id", $("#province").val());
            formData.append("region_id", $("#region").val());

            // Promotional Image
            let promotionalImage = $("#promotional_image")[0].files[0];
            if (promotionalImage) {
                formData.append("promotional_image", promotionalImage);
            }

            // Discussion File
            let discussionFile = $("#discussion_file")[0].files[0];
            if (discussionFile) {
                formData.append("discussion_file", discussionFile);
            }

            $.ajax({
                url: API,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    $("button[type='submit']")
                        .prop("disabled", true)
                        .text("Saving...");
                },
                success: function(res) {

                    if (res.code == 0) {

                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: res.message
                        }).then(() => {
                            window.location.href = "<?= $basePath ?>/list-training";
                        });

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: res.message
                        });

                    }
                },
                error: function(xhr) {

                    Swal.fire({
                        icon: "error",
                        title: "Request Failed",
                        text: xhr.responseText
                    });

                },
                complete: function() {
                    $("button[type='submit']")
                        .prop("disabled", false)
                        .text("Save Training");
                }
            });
        });

        getRegionList();

    });
</script>
<?= endSection() ?>
