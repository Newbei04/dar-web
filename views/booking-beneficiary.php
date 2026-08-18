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

    .booking-toggle-icon {
        transition: transform .2s ease;
    }

    [aria-expanded="true"] .booking-toggle-icon {
        transform: rotate(180deg);
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

<!-- View Reviews Modal -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-star text-warning me-1"></i>
                    Reviews <small class="text-muted fs-13" id="reviewsSub"></small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviewsBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Rate Booking Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-star text-warning me-1"></i> Rate Your Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" id="ratingMachineName"></p>
                <div class="text-center mb-3">
                    <div id="rateStars" class="fs-2">
                        <i class="fa fa-star text-muted" data-val="1"></i>
                        <i class="fa fa-star text-muted" data-val="2"></i>
                        <i class="fa fa-star text-muted" data-val="3"></i>
                        <i class="fa fa-star text-muted" data-val="4"></i>
                        <i class="fa fa-star text-muted" data-val="5"></i>
                    </div>
                    <div class="text-muted fs-13" id="ratingLabel">Select a rating</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Comment (optional)</label>
                    <textarea class="form-control" id="ratingComment" rows="3" placeholder="Share your experience..."></textarea>
                </div>
                <input type="hidden" id="ratingBookingId" value="">
                <input type="hidden" id="ratingMachineryId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSubmitRating">
                    <i class="fa fa-star me-1"></i> Submit Rating
                </button>
            </div>
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
            return d.toLocaleDateString('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric'
            });
        };

        const money = (val) => "₱ " + parseFloat(val || 0).toFixed(2);

        const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (m) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[m]);

        const starsHtml = (val) => {
            const filled = Math.round(parseFloat(val || 0));
            let html = '';
            for (let i = 1; i <= 5; i++) {
                html += `<i class="fa fa-star ${i <= filled ? 'text-warning' : 'text-muted'}"></i>`;
            }
            return html;
        };

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

        function schedRows(item) {
            const days = parseInt(item.total_days || 0, 10);
            const rate = parseFloat(item.unit_price || 0);
            if (!item.start_at || days < 1) {
                return '<tr><td colspan="3" class="text-center text-muted">No schedule available</td></tr>';
            }
            const start = new Date(String(item.start_at).replace(' ', 'T'));
            let rows = '';
            for (let d = 0; d < days; d++) {
                const dt = new Date(start);
                dt.setDate(start.getDate() + d);
                rows += `
                    <tr>
                        <td class="fw-bold">Day ${d + 1}</td>
                        <td>${fmtDate(dt.toISOString().slice(0, 10))}</td>
                        <td class="text-end">${money(rate)}</td>
                    </tr>`;
            }
            return rows;
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
            const image = item.image ?
                baseURL + "assets/images/machinery/" + item.image :
                baseURL + "assets/images/machinery/default.png";

            const declinedBlock = item.status == 4 ?
                `
            <div class="alert alert-danger light alert-dismissible fade show mb-0 py-2">
                <i class="fa fa-circle-exclamation me-2"></i>
                <strong>Reason:</strong> ${item.declined_remarks || 'No remarks provided.'}
            </div>` :
                    '';

                const collapseId = `bookingDetail_${item.id}`;

                return `
            <div class="card booking-card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-start collapsed" role="button"
                        data-bs-toggle="collapse" data-bs-target="#${collapseId}"
                        aria-expanded="false" aria-controls="${collapseId}"
                        style="cursor:pointer;">
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
                                <div class="text-end d-flex align-items-center gap-2">
                                    ${statusBadge(item.status)}
                                    <i class="fa fa-chevron-down booking-toggle-icon text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="collapse" id="${collapseId}">
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

                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-sm btn-outline-primary view-reviews-btn"
                                data-machinery-id="${item.machinery_id}"
                                data-name="${escapeHtml(item.machinery_name || '')}">
                                <i class="fa fa-star me-1"></i>View Reviews
                            </button>
                            ${String(item.status) === '3' && String(item.rated) !== '1' ? `
                            <button class="btn btn-sm btn-primary rate-btn"
                                data-booking-id="${item.id}"
                                data-machinery-id="${item.machinery_id}"
                                data-name="${escapeHtml(item.machinery_name || '')}">
                                <i class="fa fa-star me-1"></i>Rate This Booking
                            </button>` : ''}
                        </div>

                                            <hr class="my-3">
                        <div class="fs-13 fw-bold text-muted mb-3">Booking Schedule</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Day</th>
                                        <th>Date</th>
                                        <th class="text-end">Daily Rate</th>
                                    </tr>
                                </thead>
                                <tbody>${schedRows(item)}</tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th colspan="2" class="text-end">Total</th>
                                        <th class="text-end">${money(item.total_cost)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
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

        /* ================= VIEW REVIEWS ================= */
        $(document).on('click', '.view-reviews-btn', function() {
            const machineryId = $(this).data('machinery-id');
            const name = $(this).data('name') || 'this machinery';
            $('#reviewsSub').text(' · ' + name);
            $('#reviewsBody').html('<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin me-1"></i> Loading reviews...</div>');
            $('#reviewsModal').modal('show');

            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "LIST_MACHINERY_REVIEWS",
                    machinery_id: machineryId
                }),
                success: function(res) {
                    if (res.code != 0) {
                        $('#reviewsBody').html('<div class="text-center text-muted py-4">' + (res.message || 'Failed to load reviews.') + '</div>');
                        return;
                    }
                    const list = res.data || [];
                    if (!list.length) {
                        $('#reviewsBody').html('<div class="text-center text-muted py-4"><i class="fa fa-star fa-2x d-block mb-2"></i>No reviews yet for this machinery.</div>');
                        return;
                    }
                    let html = '';
                    list.forEach(function(r) {
                        html += `
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">${escapeHtml(r.beneficiary_name || 'Anonymous')}</span>
                                    <span class="text-muted fs-12">${fmtDate(r.created_at)}</span>
                                </div>
                                <div class="text-warning mb-2">${starsHtml(parseFloat(r.rating || 0))}</div>
                                ${r.comment ? `<p class="mb-0">${escapeHtml(r.comment)}</p>` : '<p class="text-muted mb-0">No comment.</p>'}
                            </div>`;
                    });
                    $('#reviewsBody').html(html);
                },
                error: function() {
                    $('#reviewsBody').html('<div class="text-center text-muted py-4">Failed to load reviews.</div>');
                }
            });
        });

        /* ================= RATE BOOKING ================= */
        let selectedRating = 0;

        function setRating(val) {
            selectedRating = val;
            $('#rateStars i').each(function() {
                const v = parseInt($(this).data('val'), 10);
                $(this).toggleClass('text-warning', v <= val).toggleClass('text-muted', v > val);
            });
            const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            $('#ratingLabel').text(val ? labels[val] : 'Select a rating');
        }

        $('#rateStars i').on('mouseenter', function() {
            const v = parseInt($(this).data('val'), 10);
            $('#rateStars i').each(function() {
                const sv = parseInt($(this).data('val'), 10);
                $(this).toggleClass('text-warning', sv <= v).toggleClass('text-muted', sv > v);
            });
        });

        $('#rateStars').on('mouseleave', function() {
            setRating(selectedRating);
        });

        $('#rateStars i').on('click', function() {
            setRating(parseInt($(this).data('val'), 10));
        });

        $(document).on('click', '.rate-btn', function() {
            $('#ratingBookingId').val($(this).data('booking-id'));
            $('#ratingMachineryId').val($(this).data('machinery-id'));
            $('#ratingMachineName').text($(this).data('name') || '');
            $('#ratingComment').val('');
            setRating(0);
            $('#ratingModal').modal('show');
        });

        $('#btnSubmitRating').on('click', function() {
            if (!selectedRating) {
                Swal.fire('Rating Required', 'Please select a rating from 1 to 5.', 'warning');
                return;
            }
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Submitting...');
            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "SUBMIT_MACHINERY_RATING",
                    booking_id: $('#ratingBookingId').val(),
                    machinery_id: $('#ratingMachineryId').val(),
                    rating: selectedRating,
                    comment: $('#ratingComment').val()
                }),
                success: function(res) {
                    $('#btnSubmitRating').prop('disabled', false).html('<i class="fa fa-star me-1"></i> Submit Rating');
                    if (res.code == 0) {
                        $('#ratingModal').modal('hide');
                        Swal.fire('Thank You', res.message, 'success').then(function() {
                            loadData();
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to submit rating.', 'error');
                    }
                },
                error: function() {
                    $('#btnSubmitRating').prop('disabled', false).html('<i class="fa fa-star me-1"></i> Submit Rating');
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                }
            });
        });

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