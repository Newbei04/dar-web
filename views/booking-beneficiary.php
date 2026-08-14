<!-- CSS -->
<?= startSection('css') ?>
<style>
    .booking-step {
        display: flex;
        flex: 1;
        flex-direction: column;
        align-items: center;
        position: relative;
        text-align: center;
    }

    .booking-step:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 16px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
    }

    .booking-step.done:not(:last-child)::after {
        background: #0f766e;
    }

    .booking-step.done .step-dot {
        background: #0f766e;
        color: #fff;
        border-color: #0f766e;
    }

    .booking-step.current .step-dot {
        background: #fff;
        color: #0f766e;
        border-color: #0f766e;
        box-shadow: 0 0 0 4px rgba(15, 118, 110, .15);
    }

    .booking-step.current:not(:last-child)::after {
        background: #e9ecef;
    }

    .booking-step .step-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #e9ecef;
        background: #f8f9fa;
        color: #adb5bd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        position: relative;
        z-index: 1;
    }

    .booking-step .step-label {
        margin-top: 6px;
        font-size: 12px;
        color: #adb5bd;
    }

    .booking-step.done .step-label,
    .booking-step.current .step-label {
        color: #212529;
        font-weight: 600;
    }

    .booking-step.failed .step-dot {
        background: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }

    .booking-step.failed .step-label {
        color: #dc3545;
        font-weight: 600;
    }

    .booking-avatar {
        width: 76px;
        min-width: 76px;
        height: 76px;
        object-fit: cover;
        border-radius: 50%;
        background: #f1f5f9;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, .06);
    }

    .booking-tabs .nav-link {
        padding: .5rem 0;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6c757d;
        white-space: nowrap;
    }

    .booking-tabs .nav-link:hover {
        color: var(--primary);
    }

    .booking-tabs .tab-count {
        background: rgba(0, 0, 0, .08);
        border-radius: 10px;
        font-size: 11px;
        padding: 0 7px;
        line-height: 18px;
        min-width: 20px;
        text-align: center;
    }

    .booking-tabs .nav-link.active .tab-count {
        background: var(--primary);
        color: #fff;
    }

    .booking-card {
        transition: box-shadow .15s ease;
    }

    .booking-card:hover {
        box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .08);
    }

    .booking-card .icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .booking-card .icon-box i {
        line-height: 1;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Booking</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">My Bookings</a></li>
        </ol>
    </div>

    <!-- Toolbar: filter tabs + search -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex flex-wrap align-items-center py-3">
                    <h4 class="card-title mb-0 me-auto">My Bookings</h4>
                    <div class="position-relative">
                        <i class="fa fa-magnifying-glass position-absolute text-muted"
                            style="top:50%; left:12px; transform:translateY(-50%); font-size:12px;"></i>
                        <input type="search" id="bookingSearch" class="form-control form-control-sm ps-5"
                            style="width:240px;" placeholder="Search booking no. or machinery...">
                    </div>
                </div>
                <div class="card-body pt-0">
                    <ul class="nav nav-underline nav-underline-primary booking-tabs" id="bookingTabs" style="flex-wrap:wrap;">
                        <li class="nav-item">
                            <a class="nav-link active" data-status="all" href="javascript:void(0)">Overview <span class="tab-count" data-count="all">0</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="0" href="javascript:void(0)">Pending <span class="tab-count" data-count="0">0</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="1" href="javascript:void(0)">Approved <span class="tab-count" data-count="1">0</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="2" href="javascript:void(0)">Checked Out <span class="tab-count" data-count="2">0</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="3" href="javascript:void(0)">Completed <span class="tab-count" data-count="3">0</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-status="4" href="javascript:void(0)">Declined <span class="tab-count" data-count="4">0</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking list -->
    <div class="row">
        <div class="col-12">
            <div id="bookingList"></div>
        </div>
    </div>
</div>

<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        const baseURL = '<?= $baseURL ?>';

        const fmtDate = (val) => {
            if (!val) return '-';
            const d = new Date(val.replace(' ', 'T'));
            if (isNaN(d.getTime())) return val;
            return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        };

        const money = (val) => "₱ " + parseFloat(val || 0).toFixed(2);

        function statusBadge(status) {
            switch (String(status)) {
                case '0':
                    return `<span class="badge light badge-warning">Pending</span>`;
                case '1':
                    return `<span class="badge light badge-info">Approved</span>`;
                case '2':
                    return `<span class="badge light badge-primary">Checked Out</span>`;
                case '3':
                    return `<span class="badge light badge-success">Returned</span>`;
                case '4':
                    return `<span class="badge light badge-danger">Declined</span>`;
                default:
                    return `<span class="badge light badge-secondary">Unknown</span>`;
            }
        }

        function statusTimeline(status) {
            const steps = ['Request', 'Approved', 'Checkout', 'Returned'];
            if (String(status) === '4') {
                return `
                    <div class="booking-step failed" style="flex:0 0 auto">
                        <div class="step-dot"><i class="fa fa-xmark"></i></div>
                        <div class="step-label">Declined</div>
                    </div>`;
            }
            let active = parseInt(status, 10) || 0;
            return steps.map((label, i) => {
                let cls = 'booking-step';
                if (i < active) cls += ' done';
                else if (i === active) cls += ' current';
                let icon = i < active ? '<i class="fa fa-check"></i>' : (i + 1);
                return `<div class="${cls}">
                            <div class="step-dot">${icon}</div>
                            <div class="step-label">${label}</div>
                        </div>`;
            }).join('');
        }

        function bookingCard(item) {
            const image = item.image
                ? baseURL + "assets/images/machinery/" + item.image
                : baseURL + "assets/images/machinery/default.png";

            const declinedBlock = item.status == 4
                ? `
                <div class="alert alert-danger light alert-dismissible fade show mb-0 py-2">
                    <i class="fa fa-circle-exclamation me-2"></i>
                    <strong>Reason:</strong> ${item.declined_remarks || 'No remarks provided.'}
                </div>`
                : '';

            return `
                <div class="card booking-card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-start">
                            <img src="${image}" class="avatar booking-avatar me-3" alt="Machinery">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fs-12 text-muted">${item.booking_num ?? '-'}</div>
                                        <h4 class="mb-1">${item.machinery_name ?? '-'}</h4>
                                        <div class="fs-13 text-muted">
                                            ${item.machinery_type ?? ''}${item.model ? ' &middot; ' + item.model : ''}
                                        </div>
                                        <div class="fs-13 text-muted">
                                            <i class="fa fa-location-dot me-1"></i>${item.branch_name ?? '-'}
                                        </div>
                                    </div>
                                    <div class="text-end">${statusBadge(item.status)}</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bgl-primary rounded me-3">
                                        <i class="fa fa-calendar-day fs-24 text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fs-12 text-muted">Start Date</div>
                                        <div class="fw-bold">${fmtDate(item.start_at)}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bgl-info rounded me-3">
                                        <i class="fa fa-calendar-check fs-24 text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fs-12 text-muted">End Date</div>
                                        <div class="fw-bold">${fmtDate(item.end_at)}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bgl-warning rounded me-3">
                                        <i class="fa fa-clock fs-24 text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fs-12 text-muted">Total Days</div>
                                        <div class="fw-bold">${(item.total_days || 0)} day(s)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bgl-success rounded me-3">
                                        <i class="fa fa-money-check-dollar fs-24 text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fs-12 text-muted">Total Cost</div>
                                        <div class="fw-bold">${money(item.total_cost)}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        <div class="fs-13 fw-bold text-muted mb-3">Booking Status</div>
                        <div class="d-flex mb-3">${statusTimeline(item.status)}</div>
                        ${declinedBlock}
                    </div>
                </div>`;
        }

        let allBookings = [];
        let currentStatus = 'all';

        function updateTabCounts(data) {
            const countFor = (s) => data.filter(b => String(b.status) === String(s)).length;
            $('.tab-count[data-count="all"]').text(data.length);
            $('.tab-count[data-count="0"]').text(countFor(0));
            $('.tab-count[data-count="1"]').text(countFor(1));
            $('.tab-count[data-count="2"]').text(countFor(2));
            $('.tab-count[data-count="3"]').text(countFor(3));
            $('.tab-count[data-count="4"]').text(countFor(4));
        }

        function renderBookings() {
            const term = ($('#bookingSearch').val() || '').toLowerCase().trim();
            const list = allBookings.filter(function(b) {
                const inStatus = currentStatus === 'all' || String(b.status) === String(currentStatus);
                if (!inStatus) return false;
                if (!term) return true;
                const haystack = [b.booking_num, b.machinery_name, b.machinery_type, b.branch_name]
                    .filter(Boolean).join(' ').toLowerCase();
                return haystack.includes(term);
            });

            $('#bookingList').html('');

            if (!list.length) {
                const hasFilters = term || currentStatus !== 'all';
                $('#bookingList').html(`
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-calendar-xmark fa-3x text-muted mb-3"></i>
                            <h5 class="mb-1">${allBookings.length ? 'No matching bookings' : 'No bookings yet'}</h5>
                            <p class="text-muted mb-0">${allBookings.length
                                ? 'Try a different filter or search term.'
                                : 'Once you book a machinery, your bookings will appear here.'}</p>
                            ${!allBookings.length ? `
                                <a href="${baseURL}booking-browse" class="btn btn-primary btn-sm mt-3">
                                    <i class="fas fa-calendar-check me-1"></i> Book a Machine
                                </a>` : ''}
                        </div>
                    </div>`);
                return;
            }

            list.forEach(item => {
                $('#bookingList').append(bookingCard(item));
            });
        }

        $(document).on('click', '#bookingTabs .nav-link', function() {
            currentStatus = $(this).data('status');
            $('#bookingTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            renderBookings();
        });

        $('#bookingSearch').on('input', renderBookings);

        function loadData() {
            showLoader();

            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_BENEFICIARY_BOOKING"
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire("Error", res.message || "Failed to load bookings.", "error");
                        return;
                    }
                    allBookings = res.data || [];
                    updateTabCounts(allBookings);
                    renderBookings();
                },
                error: function(xhr) {
                    closeLoader();
                    console.error("LIST_BENEFICIARY_BOOKING failed:", xhr.responseText);
                    Swal.fire("Error", "Failed to load bookings.", "error");
                }
            });
        }

        loadData();
    });
</script>

<?= endSection() ?>
