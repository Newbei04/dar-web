<!-- CSS -->
<?= startSection('css') ?>
<link href="<?= $baseURL ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.css" rel="stylesheet">
<style>
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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Available Machinery</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Available Bookings</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblData" class="display responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Image</th>
                                    <th>Machinery</th>
                                    <th>Type</th>
                                    <th>Facility</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
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

<!-- ================= BOOK MODAL ================= -->
<div class="modal fade" id="bookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Book Machinery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="machinery_id">
                <input type="hidden" id="branch_id">
                <input type="hidden" id="booked_by" value="<?= $_SESSION['users_id'] ?? '' ?>">

                <!-- Machinery image preview -->
                <div class="text-center mb-3">
                    <img id="bookMachineryImage" src="" alt="Machinery Preview"
                         class="w-100 bg-light"
                         style="max-height:220px; object-fit:cover; border-radius:8px; display:none;">
                    <div id="bookMachineryImageFallback" class="bg-light d-flex flex-column align-items-center justify-content-center text-muted"
                         style="height:220px; border-radius:8px;">
                        <i class="fas fa-image fa-3x mb-2"></i>
                        <small>Machinery preview</small>
                    </div>
                </div>

                <div class="row">

                    <?php if (!(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3)) { ?>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Select Beneficiary</label>
                            <select class="form-control single-select" id="beneficiary_id">
                                <option value="">Select Beneficiary</option>
                            </select>
                        </div>
                    <?php } ?>

                    <!-- Calendar availability -->
                    <div class="col-md-12 mb-3">
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
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date">
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
<script src="<?= $baseURL ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseURL ?>assets/vendor/datatables/responsive/responsive.js"></script>
<script src="<?= $baseURL ?>assets/js/plugins-init/datatables.init.js"></script>

<script>
    $(document).ready(function() {

        if ("<?= $_SESSION["IS_LOGIN"] ?>" != "1") {
            window.location.href = '<?= $baseURL ?>login';
        }

        const baseURL = '<?= $baseURL ?>';

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
        let calBookings = []; // fetched per machinery

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

            // conflict message
            if (calSelStart && calSelEnd && conflictDates.size > 0) {
                $('#calConflictMsg').text('Selected range conflicts with existing bookings.');
            } else {
                $('#calConflictMsg').text('');
            }
        }

        // Day click — select start then end
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

        // Manual date input → sync calendar
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

        // Month nav
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

        /* ── Fetch booked dates for a machinery ── */
        function loadBookedDates(machineryId) {
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-booking.php',
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

        /* ── LOAD DATA ── */
        function loadData() {
            showLoader();
            tbl.clear().draw();
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-booking.php',
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
                    (res.data || []).forEach(function(item, i) {

                        function machineThumbFallback() {
                            return '<div style="width:50px;height:50px;border-radius:10%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted"></i></div>';
                        }
                        window.machineThumbFallback = machineThumbFallback;

                        let machineryImage = item.image ?
                            `<img src="<?= $baseURL ?>assets/images/machinery/${item.image}" alt="Machine" style="width:50px;height:50px;border-radius:10%;object-fit:cover;" onerror="this.outerHTML=machineThumbFallback();">` :
                            machineThumbFallback();

                        let statusText = item.status == 1 ?
                            `<span class="badge light badge-success">Available</span>` :
                            item.status == 0 ?
                            `<span class="badge light badge-warning">Under Maintenance</span>` :
                            `<span class="badge light badge-secondary">Unavailable</span>`;
                        tbl.row.add([
                            i + 1,
                            machineryImage,
                            item.name ?? '-',
                            item.machinery_type ?? '-',
                            item.facility_name ?? '-',
                            item.model ?? '-',
                            statusText,
                            item.created_at ?? '-',
                            `<button class="btn btn-primary btn-sm bookBtn"
                                data-id="${item.id}"
                                data-branch="${item.branch_id}"
                                data-image="${baseURL}assets/images/machinery/${item.image || ''}">
                                <i class="fas fa-calendar-check mr-1"></i> Book
                            </button>`
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    closeLoader();
                    console.error('LIST_AVAILABLE_BOOKINGS failed:', xhr.responseText);
                    Swal.fire('Error', 'Failed to load machinery.', 'error');
                }
            });
        }

        /* ── LOAD BENEFICIARIES ── */
        function loadBeneficiaries() {
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-beneficiary.php',
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
                        </option>
                    `);
                        });
                    }
                    $('#beneficiary_id').select2('destroy').select2({ dropdownParent: $('#bookModal') });
                }
            });
        }

        /* ── MACHINERY IMAGE PREVIEW ── */
        function setBookMachineryImage(src) {
            const $img = $('#bookMachineryImage');
            const $fb = $('#bookMachineryImageFallback');
            if (!src) {
                $img.hide();
                $fb.show();
                return;
            }
            $img.off('error load').on('error', function() {
                $(this).hide();
                $fb.show();
            }).on('load', function() {
                $(this).show();
                $fb.hide();
            }).attr('src', src);
        }

        /* ── OPEN MODAL ── */
        $(document).on('click', '.bookBtn', function() {
            const macId = $(this).data('id');
            const branchId = $(this).data('branch');
            const imageSrc = $(this).data('image') || '';
            $('#machinery_id').val(macId);
            $('#branch_id').val(branchId);
            setBookMachineryImage(imageSrc);

            const now = new Date();
            calYear = now.getFullYear();
            calMonth = now.getMonth();
            calSelStart = null;
            calSelEnd = null;
            $('#start_date').val('');
            $('#end_date').val('');
            $('#calConflictMsg').text('');

            loadBookedDates(macId);

            $('#bookModal').modal('show');
        });

        /* ── CONFIRM BOOKING ── */
        $('#confirmBookBtn').click(function() {
            const machineryId = $('#machinery_id').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!$('#start_date').val() || !$('#end_date').val()) {
                Swal.fire('Validation', 'Please select a start and end date.', 'warning');
                return;
            }

            if ($('#beneficiary_id').length && !$('#beneficiary_id').val()) {
                Swal.fire('Validation', 'Please select a beneficiary.', 'warning');
                return;
            }

            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const totalDays = diffDays > 0 ? diffDays : 1;

            showLoader('Submitting booking...');
            $.ajax({
                url: '<?= $baseURL ?>controller/ctrl-booking.php',
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
                    total_days: totalDays
                }),
                success: function(res) {
                    closeLoader();
                    if (res.code == 0) {
                        Swal.fire('Success', res.message, 'success');
                        $('#bookModal').modal('hide');
                        if ($('#beneficiary_id').length) $('#beneficiary_id').val('');
                        $('#start_date').val('');
                        $('#end_date').val('');
                        loadData();
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

    });
</script>

<?= endSection() ?>
