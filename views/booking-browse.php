<!-- CSS -->
<?= startSection('css') ?>
<style>
    /* ── Machine cards ── */
    .machine-card {
        transition: transform .15s ease, box-shadow .15s ease;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .machine-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .12);
    }

    .machine-card .machine-thumb {
        height: 160px;
        width: 100%;
        object-fit: cover;
        border-radius: 0.75rem 0.75rem 0 0;
        background: #f1f5f9;
        display: block;
    }

    .machine-card .machine-thumb-fallback {
        height: 160px;
        width: 100%;
        border-radius: 0.75rem 0.75rem 0 0;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
    }

    /* ── Gallery ── */
    .gal-thumb {
        width: 70px;
        height: 55px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
        border: 2px solid transparent;
        background: #f1f5f9;
    }

    .gal-thumb.active {
        border-color: #0f766e;
    }

    /* ── Calendar inside modal ── */
    .cal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .cal-header button {
        background: none;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        width: 28px;
        height: 28px;
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
        color: #495057;
    }

    .cal-header button:hover {
        background: #f8f9fa;
    }

    .cal-title {
        font-size: 13px;
        font-weight: 600;
        color: #212529;
    }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        font-size: 11px;
    }

    .cal-dow {
        text-align: center;
        font-weight: 600;
        color: #6c757d;
        padding: 3px 0;
    }

    .cal-day {
        text-align: center;
        padding: 4px 2px;
        border-radius: 4px;
        cursor: default;
        color: #212529;
    }

    .cal-day.past {
        color: #ced4da;
    }

    .cal-day.avail {
        background: #bbf7d0;
        color: #14532d;
        cursor: pointer;
    }

    .cal-day.avail:hover {
        background: #4ade80;
    }

    .cal-day.booked-approved {
        background: #93c5fd;
        color: #1e3a5f;
    }

    .cal-day.booked-pending {
        background: #fde047;
        color: #713f12;
    }

    .cal-day.booked-declined {
        background: #fca5a5;
        color: #7f1d1d;
    }

    .cal-day.sel-start,
    .cal-day.sel-end {
        background: #0f766e !important;
        color: #fff !important;
        font-weight: 600;
    }

    .cal-day.sel-range {
        background: #99f6e4;
        color: #134e4a;
    }

    .cal-day.conflict {
        background: #fbcfe8;
        color: #831843;
    }

    .cal-day.today-dot {
        outline: 2px solid #0f766e;
        outline-offset: -2px;
        border-radius: 4px;
    }

    .cal-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
        font-size: 11px;
        color: #6c757d;
    }

    .cal-legend span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .cal-legend i {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    #calConflictMsg {
        font-size: 12px;
        color: #dc3545;
        min-height: 16px;
        margin-top: 4px;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Booking</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Book a Machine</a></li>
        </ol>
    </div>

    <!-- Machine grid -->
    <div class="row" id="machineGrid">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center">
                    <h4 class="card-title me-auto mb-2 mb-md-0">Available Machinery</h4>
                    <div class="d-flex flex-wrap">
                        <input type="search" id="machineSearch" class="form-control form-control-sm me-2 mb-1"
                            style="width:200px;" placeholder="Search machinery...">
                        <select id="machineType" class="form-control form-control-sm single-select me-2 mb-1" style="width:auto;">
                            <option value="">All Types</option>
                        </select>
                        <select id="machineSort" class="form-control form-control-sm single-select mb-1" style="width:auto;">
                            <option value="rating">Top Rated</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="name">Name (A-Z)</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="machineCards"></div>
                    <div id="machineEmpty" class="text-center py-5 d-none">
                        <i class="fa fa-tractor fa-3x text-muted mb-3"></i>
                        <h5 class="mb-1" id="machineEmptyTitle">No available machinery</h5>
                        <p class="text-muted mb-0" id="machineEmptyText">Machines will appear here once they are available.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= BOOK DETAIL MODAL ================= -->
<div class="modal fade" id="bookDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="bookModalTitle">Book a Machine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="machinery_id">
                <input type="hidden" id="branch_id">
                <input type="hidden" id="daily_rate">
                <input type="hidden" id="booked_by" value="<?= $_SESSION['users_id'] ?? '' ?>">

                <?php if (!(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3)) { ?>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Select Beneficiary</label>
                        <select class="form-control single-select" id="beneficiary_id">
                            <option value="">Select Beneficiary</option>
                        </select>
                    </div>
                <?php } ?>

                <div class="row g-3">

                    <!-- Gallery -->
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <img id="galMain" class="w-100 rounded d-none" style="height:300px; object-fit:cover; background:#f1f5f9;" alt="Machinery">
                            <div id="galMainFallback" class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="height:300px;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        </div>
                        <div id="galThumbs" class="d-flex flex-wrap mt-2"></div>
                    </div>

                    <!-- Machine info + booking -->
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-1" id="bookMachineName">-</h4>
                                <div class="text-muted" id="bookMeta">-</div>
                            </div>
                            <span id="bookStatusBadge"></span>
                        </div>

                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Daily Rate</small>
                                    <span class="fs-20 fw-bold" id="bookRate">-</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Rating</small>
                                    <span id="bookRating" class="fs-20 fw-bold text-warning">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3" id="bookDesc"></div>

                        <hr>

                        <label class="d-block mb-1" style="font-size:13px; font-weight:600;">
                            Availability Calendar
                            <small class="text-muted font-weight-normal ml-1">Click start then end date</small>
                        </label>
                        <div class="border rounded p-2">
                            <div class="cal-header">
                                <button id="calPrev" type="button">&#8249;</button>
                                <span class="cal-title" id="calTitle"></span>
                                <button id="calNext" type="button">&#8250;</button>
                            </div>
                            <div class="cal-grid" id="calGrid"></div>
                            <div class="cal-legend">
                                <span><i style="background:#bbf7d0;border:1px solid #4ade80"></i> Available</span>
                                <span><i style="background:#93c5fd"></i> Approved</span>
                                <span><i style="background:#fde047"></i> Pending</span>
                                <span><i style="background:#fca5a5"></i> Declined</span>
                                <span><i style="background:#2dd4bf"></i> Your range</span>
                                <span><i style="background:#c084fc"></i> Conflict</span>
                            </div>
                            <div id="calConflictMsg"></div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date">
                            </div>
                        </div>

                        <div class="p-3 rounded bg-light">
                            <div class="d-flex justify-content-between">
                                <span>Total Days</span>
                                <span id="sumDays">0 day(s)</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold mt-1">
                                <span>Total Cost</span>
                                <span id="sumCost">₱ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="confirmBookBtn">
                    <i class="fas fa-check mr-1"></i> Confirm Booking
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
        const IMG_BASE = baseURL + 'assets/images/machinery/';

        if ($('#beneficiary_id').length) {
            loadBeneficiaries();
        }

        /* ── Calendar state ── */
        const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        const DAYS_DOW = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

        let calYear, calMonth;
        let calSelStart = null,
            calSelEnd = null;
        let calBookings = [];

        const fmtDate = d => {
            const x = new Date(d);
            const y = x.getFullYear();
            const m = String(x.getMonth() + 1).padStart(2, '0');
            const day = String(x.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        };
        const addDays = (d, n) => {
            const x = new Date(d);
            x.setDate(x.getDate() + n);
            return x;
        };
        const today = fmtDate(new Date());

        function rangesOverlap(s1, e1, s2, e2) {
            return s1 <= e2 && e1 >= s2;
        }

        function buildBookedMap() {
            const map = {};
            calBookings.forEach(b => {
                let cur = new Date(b.from_date);
                const end = new Date(b.end_date);
                while (cur <= end) {
                    map[fmtDate(cur)] = b.status;
                    cur = addDays(cur, 1);
                }
            });
            return map;
        }

        function renderCalendar() {
            const bmap = buildBookedMap();
            $('#calTitle').text(MONTHS[calMonth] + ' ' + calYear);

            const conflictDates = new Set();
            if (calSelStart && calSelEnd) {
                calBookings.filter(b => b.status !== 'declined')
                    .forEach(b => {
                        if (rangesOverlap(calSelStart, calSelEnd, b.from_date, b.end_date)) {
                            let c = new Date(b.from_date),
                                e = new Date(b.end_date);
                            while (c <= e) {
                                conflictDates.add(fmtDate(c));
                                c = addDays(c, 1);
                            }
                        }
                    });
            }

            let html = DAYS_DOW.map(d => `<div class="cal-dow">${d}</div>`).join('');

            const firstDay = new Date(calYear, calMonth, 1).getDay();
            const lastDate = new Date(calYear, calMonth + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) html += `<div class="cal-day"></div>`;

            for (let d = 1; d <= lastDate; d++) {
                const ds = fmtDate(new Date(calYear, calMonth, d));
                const isPast = ds <= today;
                const booked = bmap[ds];
                let cls = 'cal-day';

                if (booked === 'approved') {
                    cls += ' booked-approved';
                } else if (booked === 'pending') {
                    cls += ' booked-pending';
                } else if (booked === 'declined') {
                    cls += ' booked-declined';
                } else if (isPast) {
                    cls += ' past';
                } else if (calSelStart && calSelEnd && conflictDates.has(ds)) {
                    cls += ' conflict';
                } else if (calSelStart && calSelEnd && ds > calSelStart && ds < calSelEnd) {
                    cls += ' sel-range';
                } else {
                    cls += ' avail';
                }

                if (ds === calSelStart) cls += ' sel-start';
                if (ds === calSelEnd) cls += ' sel-end';
                if (ds === today) cls += ' today-dot';

                const clickable = !isPast ? `data-date="${ds}"` : '';
                html += `<div class="${cls}" ${clickable}>${d}</div>`;
            }

            $('#calGrid').html(html);

            if (calSelStart && calSelEnd && conflictDates.size > 0) {
                $('#calConflictMsg').text('Selected range conflicts with existing bookings.');
            } else {
                $('#calConflictMsg').text('');
            }
            updateCostSummary();
        }

        function updateCostSummary() {
            const rate = parseFloat($('#daily_rate').val() || 0);
            let days = 0;
            if (calSelStart && calSelEnd) {
                const diff = (new Date(calSelEnd) - new Date(calSelStart)) / (1000 * 60 * 60 * 24);
                days = Math.max(1, Math.round(diff) + 1);
            }
            $('#sumDays').text(days + ' day(s)');
            $('#sumCost').text('₱ ' + (rate * days).toFixed(2));
        }

        $(document).on('click', '#calGrid .avail', function() {
            const ds = $(this).data('date');
            if (!ds) return;

            if (!calSelStart || (calSelStart && calSelEnd)) {
                calSelStart = ds;
                calSelEnd = null;
            } else {
                if (ds < calSelStart) {
                    calSelEnd = calSelStart;
                    calSelStart = ds;
                } else if (ds === calSelStart) {
                    calSelStart = null;
                    calSelEnd = null;
                } else calSelEnd = ds;
            }

            $('#start_date').val(calSelStart || '');
            $('#end_date').val(calSelEnd || '');
            renderCalendar();
        });

        $('#start_date').on('change', function() {
            calSelStart = $(this).val() || null;
            if (!calSelStart) {
                calSelEnd = null;
                $('#end_date').val('');
            }
            renderCalendar();
        });
        $('#end_date').on('change', function() {
            calSelEnd = $(this).val() || null;
            renderCalendar();
        });

        $('#calPrev').click(function() {
            calMonth--;
            if (calMonth < 0) {
                calMonth = 11;
                calYear--;
            }
            renderCalendar();
        });
        $('#calNext').click(function() {
            calMonth++;
            if (calMonth > 11) {
                calMonth = 0;
                calYear++;
            }
            renderCalendar();
        });

        /* ── Machine cards ── */
        function machineFallback() {
            return '<div class="machine-thumb-fallback"><i class="fas fa-image fa-3x text-muted"></i></div>';
        }
        window.machineFallback = machineFallback;

        let allMachines = [];

        function machineCardHtml(item) {
            const image = item.image
                ? IMG_BASE + item.image
                : '';
            const stars = parseFloat(item.ratings || 0);
            return `
                <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
                    <div class="card machine-card h-100">
                        ${image
                            ? `<img src="${image}" class="machine-thumb" alt="${item.name ?? ''}" onerror="this.outerHTML=machineFallback();">`
                            : machineFallback()}
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="mb-1">${item.name ?? '-'}</h5>
                            </div>
                            <div class="text-muted" style="font-size:13px;">
                                ${item.machinery_type ?? ''}${item.model ? ' · ' + item.model : ''}
                            </div>
                            <div class="text-muted" style="font-size:13px;">
                                <i class="fa fa-location-dot me-1"></i>${item.facility_name ?? '-'}
                            </div>
                            <div class="d-flex justify-content-between align-items-end mt-3">
                                <div>
                                    <small class="text-muted d-block">Daily Rate</small>
                                    <span class="fw-bold">₱ ${parseFloat(item.daily_rate || 0).toFixed(2)}</span>
                                </div>
                                <div class="text-warning">
                                    <i class="fa fa-star me-1"></i>${stars ? stars.toFixed(1) : '-'}
                                </div>
                            </div>
                            <button class="btn btn-primary btn-block mt-3 viewBookBtn"
                                data-id="${item.id}"
                                data-branch="${item.branch_id}">
                                <i class="fas fa-calendar-check mr-1"></i> View & Book
                            </button>
                        </div>
                    </div>
                </div>`;
        }

        function renderMachines() {
            const term = ($('#machineSearch').val() || '').toLowerCase().trim();
            const type = $('#machineType').val() || '';
            const sort = $('#machineSort').val() || 'rating';

            let list = allMachines.filter(function(item) {
                const haystack = [item.name, item.machinery_type, item.model, item.facility_name]
                    .filter(Boolean).join(' ').toLowerCase();
                return (!term || haystack.includes(term)) && (!type || item.machinery_type === type);
            });

            switch (sort) {
                case 'price-asc':
                    list.sort((a, b) => parseFloat(a.daily_rate || 0) - parseFloat(b.daily_rate || 0));
                    break;
                case 'price-desc':
                    list.sort((a, b) => parseFloat(b.daily_rate || 0) - parseFloat(a.daily_rate || 0));
                    break;
                case 'name':
                    list.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
                    break;
                default:
                    list.sort((a, b) => parseFloat(b.ratings || 0) - parseFloat(a.ratings || 0));
            }

            $('#machineCards').html(list.map(machineCardHtml).join(''));

            if (!list.length) {
                const hasFilters = !!term || !!type;
                $('#machineEmptyTitle').text(hasFilters ? 'No matches found' : 'No available machinery');
                $('#machineEmptyText').text(hasFilters
                    ? 'Try adjusting your search or filter.'
                    : 'Machines will appear here once they are available.');
                $('#machineEmpty').removeClass('d-none');
            } else {
                $('#machineEmpty').addClass('d-none');
            }
        }

        function populateTypeFilter(list) {
            const types = [...new Set(list.map(i => i.machinery_type).filter(Boolean))].sort();
            const $sel = $('#machineType');
            $sel.html('<option value="">All Types</option>');
            types.forEach(t => {
                $sel.append(`<option value="${t.replace(/"/g, '&quot;')}">${t}</option>`);
            });
        }

        $('#machineSearch').on('input', renderMachines);
        $('#machineType').on('change', renderMachines);
        $('#machineSort').on('change', renderMachines);

        function loadMachines() {
            showLoader();
            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    trans: 'LIST_AVAILABLE_BOOKINGS'
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code != 0) {
                        Swal.fire('Error', res.message || 'Failed to load machinery.', 'error');
                        return;
                    }
                    allMachines = res.data || [];
                    populateTypeFilter(allMachines);
                    renderMachines();
                },
                error: function(xhr) {
                    closeLoader();
                    console.error('LIST_AVAILABLE_BOOKINGS failed:', xhr.responseText);
                    Swal.fire('Error', 'Failed to load machinery.', 'error');
                }
            });
        }

        /* ── Gallery ── */
        function renderGallery(images) {
            $('#galThumbs').html('');
            const list = (images || []).slice().sort((a, b) => (b.is_primary || 0) - (a.is_primary || 0));

            if (!list.length) {
                $('#galMain').addClass('d-none');
                $('#galMainFallback').removeClass('d-none');
                return;
            }

            const setMain = (name) => {
                const src = IMG_BASE + name;
                $('#galMain').attr('src', src);
                $('#galMain').removeClass('d-none');
                $('#galMainFallback').addClass('d-none');
                $('#galThumbs .gal-thumb').removeClass('active');
                $(`#galThumb_${name}`).addClass('active');
            };

            list.forEach(function(img) {
                const name = img.name;
                $('#galThumbs').append(`
                    <img src="${IMG_BASE}${name}" class="gal-thumb me-2 mb-2"
                         id="galThumb_${name}"
                         title="${name}"
                         onclick="setGalleryMain('${name.replace(/'/g, "\\'")}')"
                         onerror="this.outerHTML=''">`);
            });

            window.setGalleryMain = setMain;
            setMain(list[0].name);
        }

        /* ── Open modal ── */
        $(document).on('click', '.viewBookBtn', function() {
            const macId = $(this).data('id');
            const branchId = $(this).data('branch');

            $('#machinery_id').val(macId);
            $('#branch_id').val(branchId);

            const now = new Date();
            calYear = now.getFullYear();
            calMonth = now.getMonth();
            calSelStart = null;
            calSelEnd = null;
            $('#start_date').val('');
            $('#end_date').val('');
            $('#calConflictMsg').text('');
            $('#daily_rate').val(0);
            updateCostSummary();

            loadMachineDetail(macId);
            loadBookedDates(macId);

            $('#bookDetailModal').modal('show');
        });

        /* ── Machine detail + gallery ── */
        function loadMachineDetail(macId) {
            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    trans: 'GET_MACHINERY_IMAGES',
                    id: macId
                }),
                success: function(res) {
                    renderGallery(res.code == 0 ? (res.data || []) : []);
                },
                error: function() {
                    renderGallery([]);
                }
            });

            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    trans: 'LIST_AVAILABLE_BOOKINGS'
                }),
                success: function(res) {
                    if (res.code != 0) return;
                    const item = (res.data || []).find(m => String(m.id) === String(macId));
                    if (!item) return;
                    $('#bookModalTitle').text(item.name ?? 'Book a Machine');
                    $('#bookMachineName').text(item.name ?? '-');
                    $('#bookMeta').html(`${item.machinery_type ?? ''}${item.model ? ' · ' + item.model : ''}<br>
                        <i class="fa fa-location-dot me-1"></i>${item.facility_name ?? '-'}`);
                    $('#bookRate').text('₱ ' + parseFloat(item.daily_rate || 0).toFixed(2));
                    $('#bookRating').html(`<i class="fa fa-star me-1"></i>${parseFloat(item.ratings || 0).toFixed(1)}`);
                    $('#bookDesc').html(item.description ? `<p class="text-muted mb-0">${item.description}</p>` : '');
                    $('#bookStatusBadge').html('<span class="badge light badge-success">Available</span>');
                    $('#daily_rate').val(item.daily_rate || 0);
                    updateCostSummary();
                }
            });
        }

        /* ── Booked dates ── */
        function loadBookedDates(machineryId) {
            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    trans: 'LIST_BOOKED_DATES',
                    machinery_id: machineryId
                }),
                success: function(res) {
                    calBookings = (res.code == 0) ? (res.data || []) : [];
                    renderCalendar();
                },
                error: function() {
                    calBookings = [];
                    renderCalendar();
                }
            });
        }

        /* ── Confirm booking ── */
        $('#confirmBookBtn').click(function() {
            const machineryId = $('#machinery_id').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!startDate || !endDate) {
                Swal.fire('Validation', 'Please select a start and end date.', 'warning');
                return;
            }
            if ($('#beneficiary_id').length && !$('#beneficiary_id').val()) {
                Swal.fire('Validation', 'Please select a beneficiary.', 'warning');
                return;
            }

            const diffTime = Math.abs(new Date(endDate) - new Date(startDate));
            const totalDays = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1);
            const unitPrice = parseFloat($('#daily_rate').val() || 0);
            const totalCost = unitPrice * totalDays;

            showLoader('Submitting booking...');
            $.ajax({
                url: baseURL + 'controller/ctrl-booking.php',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    trans: 'ADD_BOOKING',
                    booked_by: $('#booked_by').val(),
                    beneficiary_id: $('#beneficiary_id').length ? $('#beneficiary_id').val() : '',
                    branch_id: $('#branch_id').val(),
                    machinery_id: machineryId,
                    start_date: startDate,
                    end_date: endDate,
                    total_days: totalDays,
                    unit_price: unitPrice,
                    total_cost: totalCost
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire('Success', res.message, 'success');
                        $('#bookDetailModal').modal('hide');
                        if ($('#beneficiary_id').length) $('#beneficiary_id').val('');
                        loadMachines();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    closeLoader();
                    console.error('ADD_BOOKING failed:', xhr.responseText);
                    Swal.fire('Error', 'Failed to submit booking.', 'error');
                }
            });
        });

        /* ── Beneficiaries (staff only) ── */
        function loadBeneficiaries() {
            $.ajax({
                url: baseURL + 'controller/ctrl-beneficiary.php',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    trans: 'LIST_BENEFICIARY'
                }),
                success: function(res) {
                    $('#beneficiary_id').html('<option value="">Select Beneficiary</option>');
                    if (res.code == 0) {
                        (res.data || []).forEach(function(item) {
                            $('#beneficiary_id').append(`
                                <option value="${item.id}">
                                    ${item.fname ?? ''} ${item.mname ?? ''} ${item.lname ?? ''}
                                </option>`);
                        });
                    }
                }
            });
        }

        loadMachines();
    });
</script>

<?= endSection() ?>
