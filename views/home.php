<?= startSection('css') ?>
<style>
    .dash-hero {
        border-radius: 18px;
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 45%, #14b8a6 100%);
        position: relative;
        overflow: hidden;
    }
    .bg-teal { color: #11998e; }
    .bg-transparent { background: transparent; }
    .dash-hero::before {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        top: -90px;
        right: -40px;
    }
    .dash-hero::after {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        bottom: -60px;
        right: 160px;
    }
    .stat-card {
        border: 1px solid #ebedf2;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        transition: transform .2s ease, box-shadow .2s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(2, 6, 23, 0.10) !important;
    }
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .tile-teal { background: rgba(17, 153, 142, 0.12); color: #11998e; }
    .tile-purple { background: rgba(102, 126, 234, 0.12); color: #667eea; }
    .tile-green { background: rgba(22, 163, 74, 0.12); color: #16a34a; }
    .tile-orange { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .tile-blue { background: rgba(77, 121, 246, 0.12); color: #4d79f6; }
    .tile-slate { background: rgba(100, 116, 139, 0.15); color: #64748b; }
    .tile-red { background: rgba(245, 87, 108, 0.12); color: #f5576c; }
    .tile-pink { background: rgba(236, 72, 153, 0.12); color: #ec4899; }
    .stat-card .stat-label {
        font-size: 12px;
        letter-spacing: .4px;
        text-transform: uppercase;
        font-weight: 600;
        color: #6c757d;
    }
    .stat-card .stat-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.15;
        color: #2d3436;
    }
    .dash-card {
        border: 1px solid #ebedf2;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    }
    .dash-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eee;
        padding: 1.25rem 1.5rem;
    }
    .booking-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .recent-booking {
        border-left: 3px solid transparent;
        padding: 12px 14px;
        border-radius: 10px;
        transition: background .15s ease;
    }
    .recent-booking:hover {
        background: rgba(2, 6, 23, .035);
    }

    [data-theme-version="dark"] .text-black {
        color: var(--text-dark, #fff) !important;
    }

    [data-theme-version="dark"] .stat-card,
    [data-theme-version="dark"] .dash-card {
        background: #1f2533;
        border-color: rgba(255, 255, 255, 0.06);
        box-shadow: none;
    }

    [data-theme-version="dark"] .stat-card .stat-value {
        color: #fff;
    }

    [data-theme-version="dark"] .dash-card .card-header {
        border-bottom-color: rgba(255, 255, 255, 0.06);
    }

    [data-theme-version="dark"] .recent-booking:hover {
        background: rgba(255, 255, 255, 0.045);
    }

    [data-theme-version="dark"] .stat-card:hover {
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45) !important;
    }
</style>
<?= endSection() ?>

<?= startSection('content') ?>
<div class="container-fluid">
    <!-- PAGE TITLES -->
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
        </ol>
    </div>

    <!-- HERO -->
    <div class="dash-hero p-4 mb-4 text-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between position-relative" style="z-index:1;">
            <div>
                <h3 class="text-white mb-1">Welcome back, <?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['username'] ?? 'User') ?> </h3>
                <p class="text-white-50 mb-0" id="todayDate"></p>
                <p class="text-white mb-0 fs-20 fw-bold" id="clockTime"></p>
            </div>            <div class="text-end" id="branchBox" style="display:none;">
                <small class="text-white-50 d-block">Assigned Branch</small>
                <strong id="branchName" class="text-white fs-18">—</strong>
            </div>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-4 mb-4" id="statsRow">
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Machines</p>
                        <h3 class="stat-value mb-0" id="statMachines">0</h3>
                    </div>
                    <div class="stat-icon tile-teal"><i class="fa-solid fa-tractor"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Bookings</p>
                        <h3 class="stat-value mb-0" id="statBookings">0</h3>
                    </div>
                    <div class="stat-icon tile-purple"><i class="fa-solid fa-calendar-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Facilities</p>
                        <h3 class="stat-value mb-0" id="statFacility">0</h3>
                    </div>
                    <div class="stat-icon tile-green"><i class="fa-solid fa-building-columns"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Programs</p>
                        <h3 class="stat-value mb-0" id="statPrograms">0</h3>
                    </div>
                    <div class="stat-icon tile-orange"><i class="fa-solid fa-list-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Trainings</p>
                        <h3 class="stat-value mb-0" id="statTrainings">0</h3>
                    </div>
                    <div class="stat-icon tile-blue"><i class="fa-solid fa-chalkboard-user"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Inventory</p>
                        <h3 class="stat-value mb-0" id="statInventory">0</h3>
                    </div>
                    <div class="stat-icon tile-slate"><i class="fa-solid fa-boxes-stacked"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Reserved Stock</p>
                        <h3 class="stat-value mb-0" id="statReserved">0</h3>
                    </div>
                    <div class="stat-icon tile-red"><i class="fa-solid fa-lock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Users</p>
                        <h3 class="stat-value mb-0" id="statUsers">0</h3>
                    </div>
                    <div class="stat-icon tile-purple"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <p class="stat-label mb-1">Beneficiaries</p>
                        <h3 class="stat-value mb-0" id="statBeneficiaries">0</h3>
                    </div>
                    <div class="stat-icon tile-pink"><i class="fa-solid fa-hand-holding-heart"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS + RECENT ACTIVITY -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card dash-card">
                <div class="card-header d-flex flex-wrap align-items-center py-3">
                    <div class="me-auto">
                        <h4 class="card-title mb-0">Booking Trend</h4>
                        <small class="text-muted">Last 6 months</small>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartTrend"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card dash-card h-100">
                <div class="card-header py-3">
                    <h4 class="card-title mb-0">Booking Status</h4>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="chartPie"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card dash-card">
                <div class="card-header d-flex flex-wrap align-items-center py-3">
                    <div class="me-auto">
                        <h4 class="card-title mb-0">Inventory Overview</h4>
                        <small class="text-muted">Top 5 products by available stock</small>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartInventory"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card dash-card h-100">
                <div class="card-header py-3">
                    <h4 class="card-title mb-0">Machine Availability</h4>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="chartMachine"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card dash-card h-100">
                <div class="card-header d-flex flex-wrap align-items-center py-3">
                    <div class="me-auto">
                        <h4 class="card-title mb-0">Recent Activity</h4>
                        <small class="text-muted">Latest transactions</small>
                    </div>
                    <ul class="nav nav-pills ms-auto" id="activityTabs">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#actBookings">Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#actInventory">Inventory</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="actBookings">
                            <div id="recentBookings">
                                <div class="text-center text-muted py-4">Loading...</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="actInventory">
                            <div id="recentLogs">
                                <div class="text-center text-muted py-4">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card dash-card h-100">
                <div class="card-header py-3">
                    <h4 class="card-title mb-0">Needs Attention</h4>
                </div>
                <div class="card-body" id="alertsBox">
                    <div class="text-center text-muted py-4">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= endSection() ?>

<?= startSection('scripts') ?>
<script>
    $(document).ready(function() {
        const baseURL = "<?= $baseURL ?>";
        const roleId = "<?= $_SESSION['role_id'] ?? 0 ?>";
        const usersId = "<?= $_SESSION['users_id'] ?? 0 ?>";

        const isDark = document.body.getAttribute('data-theme-version') === 'dark';
        const CHART_TEXT = isDark ? '#b3b3b3' : '#737B8B';
        const CHART_GRID = isDark ? 'rgba(255,255,255,.08)' : 'rgba(2,6,23,.08)';

        function chartTheme(baseOptions) {
            return Object.assign({}, baseOptions, {
                theme: { mode: isDark ? 'dark' : 'light' },
                chart: Object.assign({ foreColor: CHART_TEXT }, baseOptions.chart),
                grid: Object.assign({ borderColor: CHART_GRID }, baseOptions.grid)
            });
        }

        $('#todayDate').text(new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }));

        function updateClock() {
            $('#clockTime').text(new Date().toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }));
        }
        updateClock();
        setInterval(updateClock, 1000);

        const STATUS_COLORS = {
            Pending: '#ffb547',
            Approved: '#0d9488',
            Checkout: '#4d79f6',
            Returned: '#2bc155',
            Declined: '#f6498b'
        };

        function statusBadge(status) {
            let cls = 'secondary';
            if (status === 'Pending') cls = 'warning';
            else if (status === 'Approved') cls = 'success';
            else if (status === 'Checkout') cls = 'info';
            else if (status === 'Returned') cls = 'success';
            else if (status === 'Declined') cls = 'danger';
            return `<span class="badge light badge-${cls}">${status}</span>`;
        }

        function fmtDate(d) {
            if (!d) return '—';
            return new Date(d).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function renderTrendChart(labels, totals) {
            if (!labels || !labels.length) {
                $('#chartTrend').html('<div class="text-center text-muted py-5">No booking data available</div>');
                return;
            }
            let options = {
                series: [{
                    name: 'Bookings',
                    data: totals
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    animations: { enabled: true }
                },
                colors: ['#0d9488'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                xaxis: {
                    categories: labels,
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: { formatter: v => Math.round(v) }
                },
                grid: {
                    borderColor: 'rgba(2,6,23,.08)',
                    strokeDashArray: 4
                },
                tooltip: { y: { formatter: v => v + ' bookings' } }
            };
            new ApexCharts(document.querySelector('#chartTrend'), chartTheme(options)).render();
        }

        function renderPieChart(breakdown) {
            let series = [], labels = [];
            (breakdown || {}).forEach(function(b) {
                series.push(b.value);
                labels.push(b.label);
            });
            let total = series.reduce((a, b) => a + b, 0);
            if (total === 0) {
                $('#chartPie').html('<div class="text-center text-muted py-5">No booking data available</div>');
                return;
            }
            let options = {
                series: series,
                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'inherit'
                },
                labels: labels,
                colors: ['#ffb547', '#0d9488', '#4d79f6', '#2bc155', '#f6498b'],
                legend: { position: 'bottom' },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '14px',
                                    formatter: () => total
                                }
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { height: 280 },
                        legend: { position: 'bottom' }
                    }
                }]
            };
            new ApexCharts(document.querySelector('#chartPie'), chartTheme(options)).render();
        }

        function renderRecent(bookings) {
            if (!bookings || !bookings.length) {
                $('#recentBookings').html('<div class="text-center text-muted py-4">No bookings yet</div>');
                return;
            }
            let html = bookings.map(function(b) {
                let color = STATUS_COLORS[b.status_label] || '#6c757d';
                let ref = b.id ? 'Booking #' + b.id : '';
                let machine = b.machinery_name || '—';
                if (b.machinery_model) machine += ` <small class="text-muted">(${b.machinery_model})</small>`;
                return `
                    <div class="recent-booking d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center me-3">
                            <span class="booking-status-dot" style="background:${color};"></span>
                            <div>
                                <div class="fw-bold text-black">${machine}</div>
                                <small class="text-muted">${ref}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <small class="text-muted d-none d-sm-inline">${fmtDate(b.created_at)}</small>
                            ${statusBadge(b.status_label)}
                        </div>
                    </div>`;
            }).join('<hr class="my-1">');
            $('#recentBookings').html(html);
        }

        const LOG_ACTIONS = { 1: 'Receive', 2: 'Sale', 3: 'Return', 4: 'Adjustment', 5: 'Expired', 6: 'Damaged', 7: 'Price Change', 8: 'Reserve', 9: 'Release', 10: 'Transfer' };
        const LOG_COLORS = { 1: 'success', 2: 'info', 3: 'warning', 4: 'secondary', 5: 'danger', 6: 'danger', 7: 'primary', 8: 'warning', 9: 'success', 10: 'info' };

        function renderRecentLogs(logs) {
            if (!logs || !logs.length) {
                $('#recentLogs').html('<div class="text-center text-muted py-4">No inventory activity yet</div>');
                return;
            }
            let html = logs.map(function(l) {
                let act = LOG_ACTIONS[l.action_type] || 'Log';
                let who = l.created_by_name ? ' · ' + l.created_by_name : '';
                return `
                    <div class="recent-booking d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center me-3">
                            <span class="badge light badge-${LOG_COLORS[l.action_type] || 'secondary'} me-2">${act}</span>
                            <div>
                                <div class="fw-bold text-black">${l.product_name || '—'}</div>
                                <small class="text-muted">${l.facility_name || ''}${who}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <small class="text-muted d-none d-sm-inline">${fmtDate(l.created_at)}</small>
                            <span class="badge light badge-dark">${l.quantity_changed}</span>
                        </div>
                    </div>`;
            }).join('<hr class="my-1">');
            $('#recentLogs').html(html);
        }

        function renderAlerts(pending, lowStock) {
            pending = pending || [];
            lowStock = lowStock || [];
            let html = '';
            if (pending.length) {
                html += '<div class="d-block text-uppercase text-muted fw-bold mb-2" style="font-size:11px;letter-spacing:.4px;">Pending Approvals</div>';
                html += pending.map(function(p) {
                    return `
                        <div class="recent-booking d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center me-2">
                                <span class="booking-status-dot" style="background:#ffb547;"></span>
                                <div>
                                    <div class="fw-bold text-black">${p.machinery_name || 'Booking #' + p.id}</div>
                                    <small class="text-muted">${p.booked_by_name || ''}</small>
                                </div>
                            </div>
                            <small class="text-muted">${fmtDate(p.created_at)}</small>
                        </div>`;
                }).join('<hr class="my-1">');
                html += '<hr class="my-3">';
            }
            if (lowStock.length) {
                html += '<div class="d-block text-uppercase text-muted fw-bold mb-2" style="font-size:11px;letter-spacing:.4px;">Low Stock</div>';
                html += lowStock.map(function(s) {
                    return `
                        <div class="recent-booking d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center me-2">
                                <span class="booking-status-dot" style="background:#f6498b;"></span>
                                <div>
                                    <div class="fw-bold text-black">${s.product_name || '—'}</div>
                                    <small class="text-muted">${s.facility_name || ''}</small>
                                </div>
                            </div>
                            <span class="badge light badge-danger">${s.current_stock} left</span>
                        </div>`;
                }).join('<hr class="my-1">');
            }
            if (!html) html = '<div class="text-center text-muted py-4">All good — nothing needs attention</div>';
            $('#alertsBox').html(html);
        }

        function renderInventoryChart(data) {
            if (!data || !data.length) {
                $('#chartInventory').html('<div class="text-center text-muted py-5">No inventory data available</div>');
                return;
            }
            let labels = data.map(function(d) {
                let n = d.product_name || '';
                return n.length > 14 ? n.slice(0, 13) + '…' : n;
            });
            let options = {
                series: [
                    { name: 'Available', data: data.map(function(d) { return (+d.current_stock || 0) - (+d.reserved_stock || 0); }) },
                    { name: 'Reserved', data: data.map(function(d) { return +d.reserved_stock || 0; }) }
                ],
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'inherit' },
                colors: ['#0d9488', '#ffb547'],
                plotOptions: { bar: { horizontal: false, columnWidth: '45%', borderRadius: 4 } },
                dataLabels: { enabled: false },
                xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '11px' } } },
                yaxis: { min: 0 },
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: 'rgba(2,6,23,.08)', strokeDashArray: 4 },
                tooltip: { y: { formatter: v => v + ' units' } }
            };
            new ApexCharts(document.querySelector('#chartInventory'), chartTheme(options)).render();
        }

        function renderMachineDonut(data) {
            data = data || {};
            let series = [(+data.available || 0), (+data.maintenance || 0), (+data.checked_out || 0)];
            let labels = ['Available', 'Maintenance', 'Checked Out'];
            let total = series.reduce((a, b) => a + b, 0);
            if (total === 0) {
                $('#chartMachine').html('<div class="text-center text-muted py-5">No machines recorded</div>');
                return;
            }
            let options = {
                series: series,
                chart: { type: 'donut', height: 320, fontFamily: 'inherit' },
                labels: labels,
                colors: ['#16a34a', '#f59e0b', '#4d79f6'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '14px',
                                    formatter: () => total
                                }
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: { chart: { height: 280 }, legend: { position: 'bottom' } }
                }]
            };
            new ApexCharts(document.querySelector('#chartMachine'), chartTheme(options)).render();
        }

        function loadCounts() {
            $.ajax({
                url: baseURL + "controller/ctrl-dashboard.php",
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                data: JSON.stringify({
                    trans: "GET_DASHBOARD_COUNTS",
                    role_id: roleId,
                    users_id: usersId
                }),
                success: function(res) {
                    if (res.code == 0) {
                        const d = res.data || {};
                        $('#statMachines').text(d.machines ?? 0);
                        $('#statBookings').text(d.bookings ?? 0);
                        $('#statFacility').text(d.facility ?? 0);
                        $('#statPrograms').text(d.programs ?? 0);
                        $('#statTrainings').text(d.trainings ?? 0);
                        $('#statInventory').text(d.total_inventory ?? 0);
                        $('#statReserved').text(d.reserved ?? 0);
                        $('#statUsers').text(d.users_count ?? 0);
                        $('#statBeneficiaries').text(d.beneficiaries_count ?? 0);

                        if (d.branch && d.branch.name) {
                            $('#branchBox').show();
                            $('#branchName').text(d.branch.name);
                        }

                        const breakdown = d.booking_breakdown || {};
                        renderPieChart([
                            { label: 'Pending', value: +breakdown.pending || 0 },
                            { label: 'Approved', value: +breakdown.approved || 0 },
                            { label: 'Checkout', value: +breakdown.checkout || 0 },
                            { label: 'Returned', value: +breakdown.returned || 0 },
                            { label: 'Declined', value: +breakdown.declined || 0 }
                        ]);

                        renderTrendChart(
                            (d.booking_trend || {}).labels || [],
                            (d.booking_trend || {}).totals || []
                        );

                        renderRecent(d.recent_bookings || []);
                        renderInventoryChart(d.top_products || []);
                        renderMachineDonut(d.machine_breakdown || {});
                        renderRecentLogs(d.recent_logs || []);
                        renderAlerts(d.pending_bookings || [], d.low_stock || []);
                    }
                }
            });
        }

        loadCounts();
    });
</script>
<?= endSection() ?>
