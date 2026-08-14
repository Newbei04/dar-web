<!-- Start the content for this page -->
<?= startSection('navbar') ?>
<style>
    .nav-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
    }

    .nav-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nav-avatar .nav-avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6b7aff 0%, #7c4dff 100%);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
    }

    .nav-menu-empty {
        padding: 18px 16px;
        font-size: 13px;
        color: #adb5bd;
        text-align: center;
    }
</style>
<!--**********************************
            Nav header start
        ***********************************-->
<div class="nav-header">
    <a href="<?= $baseURL ?>dashboard" class="brand-logo">
        <img class="logo-abbr" src="<?= $baseURL ?>assets/images/dar.png" alt="">
        <img class="logo-compact" src="<?= $baseURL ?>assets/images/logo-text.png" alt="">
        <img class="brand-title" src="<?= $baseURL ?>assets/images/logo-text.png" alt="">
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>
<!--**********************************
            Nav header end
        ***********************************-->
<!--**********************************
            Chat box start
        ***********************************-->
<div class="chatbox">
    <div class="chatbox-close"></div>
    <div class="custom-tab-1">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#notes">Notes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#alerts">Alerts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#chat">Chat</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active show" id="chat" role="tabpanel">
                <div class="card mb-sm-3 mb-md-0 contacts_card dz-chat-user-box">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><i class="fa fa-bars fs-5 text-dark"></i></a>
                        <div>
                            <h6 class="mb-1">Chat List</h6>
                            <p class="mb-0">Show All</p>
                        </div>
                        <a href="javascript:void(0)"><i class="fa fa-plus fs-5 text-dark"></i></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body">
                        <ul class="contacts">
                            <li class="name-first-letter">A</li>
                            <li class="active dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="<?= $baseURL ?>assets/images/avatar/1.jpg" class="rounded-circle user_img" alt="">
                                        <span class="online_icon"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Archie Parker</span>
                                        <p>Kalid is online</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="<?= $baseURL ?>assets/images/avatar/2.jpg" class="rounded-circle user_img" alt="">
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Alfie Mason</span>
                                        <p>Taherah left 7 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="name-first-letter">B</li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="<?= $baseURL ?>assets/images/avatar/5.jpg" class="rounded-circle user_img" alt="">
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Bashid Samim</span>
                                        <p>Rashid left 50 mins ago</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="alerts" role="tabpanel">
                <div class="card mb-sm-3 mb-md-0 contacts_card">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><i class="fa fa-search fs-5 text-dark"></i></a>
                        <div>
                            <h6 class="mb-1">Notications</h6>
                            <p class="mb-0">Show All</p>
                        </div>
                        <a href="javascript:void(0)"><i class="fa fa-ellipsis-v fs-5 text-dark"></i></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body1">
                        <ul class="contacts">
                            <li class="name-first-letter">SEVER STATUS</li>
                            <li class="active">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont primary">KK</div>
                                    <div class="user_info">
                                        <span>David Nester Birthday</span>
                                        <p class="text-primary">Today</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont success">RU</div>
                                    <div class="user_info">
                                        <span>Perfection Simplified</span>
                                        <p>Jame Smith commented on your status</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="notes">
                <div class="card mb-sm-3 mb-md-0 note_card">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><i class="fa fa-bars fs-5 text-dark"></i></a>
                        <div>
                            <h6 class="mb-1">Notes</h6>
                            <p class="mb-0">Add New Nots</p>
                        </div>
                        <a href="javascript:void(0)"><i class="fa fa-plus fs-5 text-dark"></i></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body2">
                        <ul class="contacts">
                            <li class="active">
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>New order placed..</span>
                                        <p>10 Aug 2024</p>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs sharp"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>Youtube, a video-sharing website..</span>
                                        <p>10 Aug 2024</p>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs sharp"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--**********************************
            Chat box End
        ***********************************-->
<!--**********************************
            Header start
        ***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        <div class="input-group search-area d-lg-inline-flex d-none">
                            <div class="input-group-append">
                                <button class="input-group-text search_icon search_icon"><i class="flaticon-381-search-2"></i></button>
                            </div>
                            <input type="text" class="form-control" placeholder="Search here...">
                        </div>
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link bell dz-theme-mode" href="javascript:void(0);">
                            <i id="icon-light" class="fas fa-sun"></i>
                            <i id="icon-dark" class="fas fa-moon"></i>
                        </a>
                    </li>
                    <?php
                    $navProfile = $_SESSION['profile'] ?? [];
                    $navFname = $navProfile['fname'] ?? '';
                    $navLname = $navProfile['lname'] ?? '';
                    $navFullName = trim($navFname . ' ' . $navLname);
                    if (!$navFullName) {
                        $navFullName = $_SESSION['name'] ?? ($_SESSION['username'] ?? 'User');
                    }
                    $navRoleId = (int)($_SESSION['role_id'] ?? 0);
                    $navRoleMap = [1 => 'Administrator', 2 => 'Employee / Cooperative', 3 => 'Beneficiary (ARB)'];
                    $navRole = $navRoleMap[$navRoleId] ?? 'User';
                    $navPhoto = $navProfile['profile'] ?? '';
                    $navInitials = strtoupper(substr($navFname, 0, 1) . substr($navLname, 0, 1));
                    if ($navInitials === '') {
                        $navInitials = strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1));
                    }
                    ?>
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <div class="header-info">
                                <span class="text-black">Hello, <strong><?= htmlspecialchars($navFullName) ?></strong></span>
                                <p class="fs-12 mb-0"><?= htmlspecialchars($navRole) ?></p>
                            </div>
                            <?php if ($navPhoto): ?>
                                <span class="nav-avatar">
                                    <img src="<?= $baseURL ?>assets/images/profile/<?= htmlspecialchars($navPhoto) ?>" alt="">
                                </span>
                            <?php else: ?>
                                <span class="nav-avatar">
                                    <span class="nav-avatar-fallback"><?= htmlspecialchars($navInitials) ?></span>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="<?= $baseURL ?>profile" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ms-2">Profile </span>
                            </a>
                            <a href="<?= $baseURL ?>email-inbox" class="dropdown-item ai-icon">
                                <svg id="icon-inbox" xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span class="ms-2">Inbox </span>
                            </a>
                            <a href="<?= $baseURL ?>logout" class="dropdown-item ai-icon" id="btnLogout">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ms-2">Logout </span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
            Header end
        ***********************************-->
<!--**********************************
            Sidebar start
        ***********************************-->
<?php
$userModules = $_SESSION['user_modules'] ?? [];
$roleAccess  = $_SESSION['role_access'] ?? [];
$isSuperAdmin = !is_array($roleAccess) || count($roleAccess) === 0;

$canAccess = function ($id) use ($roleAccess, $isSuperAdmin) {
    return $isSuperAdmin || in_array((int) $id, $roleAccess, true);
};

$children = [];
foreach ($userModules as $m) {
    if ((int) $m['is_menu'] !== 1) continue;
    $pid = (int) $m['parent_id'];
    if ($pid > 0) $children[$pid][] = $m;
}

$topLevel = [];
foreach ($userModules as $m) {
    if ((int) $m['is_menu'] === 1 && (int) $m['parent_id'] === 0) $topLevel[] = $m;
}
usort($topLevel, function ($a, $b) {
    return (int) $a['sort_order'] <=> (int) $b['sort_order'];
});

$menuHtml = '';
foreach ($topLevel as $m) {
    $hasPage = !empty($m['page']);
    $allowedChildren = [];
    if (isset($children[$m['id']])) {
        foreach ($children[$m['id']] as $c) {
            if ($canAccess($c['id'])) $allowedChildren[] = $c;
        }
    }

    if (!$hasPage && count($allowedChildren) === 0) continue;
    if ($hasPage && !$canAccess($m['id'])) continue;

    $icon = !empty($m['icon']) ? $m['icon'] : 'fas fa-circle';
    $title = htmlspecialchars($m['title'] ?? '');
    $href = $baseURL . htmlspecialchars($m['page'] ?? '');

    if ($hasPage && count($allowedChildren) === 0) {
        $menuHtml .= '<li><a href="' . $href . '" aria-expanded="false">' . "\n";
        $menuHtml .= '    <i class="' . $icon . '"></i>' . "\n";
        $menuHtml .= '    <span class="nav-text">' . $title . '</span>' . "\n";
        $menuHtml .= '</a></li>' . "\n";
    } else {
        $menuHtml .= '<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">' . "\n";
        $menuHtml .= '    <i class="' . $icon . '"></i>' . "\n";
        $menuHtml .= '    <span class="nav-text">' . $title . '</span>' . "\n";
        $menuHtml .= '</a>' . "\n";
        $menuHtml .= '<ul aria-expanded="false">' . "\n";
        foreach ($allowedChildren as $c) {
            $menuHtml .= '<li><a href="' . $baseURL . htmlspecialchars($c['page']) . '">' . htmlspecialchars($c['title']) . '</a></li>' . "\n";
        }
        $menuHtml .= '</ul>' . "\n";
        $menuHtml .= '</li>' . "\n";
    }
}
?>
<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <?php if ($menuHtml === ''): ?>
                <li class="nav-menu-empty">No modules assigned to your account. Please contact your administrator.</li>
            <?php else: ?>
                <?= $menuHtml ?>
            <?php endif; ?>
        </ul>

        <div class="copyright">
            <p><strong>DAR Web</strong> © <?= date('Y') ?> All Rights Reserved</p>
        </div>
    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->

<?= endSection() ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.getElementById('btnLogout');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Logout?',
                    text: 'You will be returned to the login page.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, logout',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= $baseURL ?>controller/ctrl-auth.php",
                            type: "POST",
                            contentType: "application/json",
                            dataType: "json",
                            data: JSON.stringify({
                                trans: "LOGOUT"
                            }),
                            success: function(res) {
                                if (res.code == 0) {
                                    window.location.href = "<?= $baseURL ?>login";
                                } else {
                                    Swal.fire("Error", res.message || "Logout failed", "error");
                                }
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire("Error", "Logout failed. Please try again.", "error");
                            }
                        });
                    }
                });
            });
        }

        const currentPath = window.location.pathname;
        const basePath = '<?= $baseURL ?>';

        const navLinks = document.querySelectorAll('.deznav .metismenu a');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href !== '#') {
                let cleanHref = href.replace(basePath, '');
                let cleanCurrentPath = currentPath.replace(basePath, '');

                if (cleanCurrentPath === cleanHref ||
                    cleanCurrentPath.endsWith(cleanHref)) {

                    link.classList.add('active');
                }
            }
        });
    });
</script>
