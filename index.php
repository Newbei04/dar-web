<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Include routes and functions 
require 'sections.php';

// Load env file
$ENV = parse_ini_file(__DIR__ . '/.env');

$baseURL = base_url() . "/";
$basePath = base_url() . "/index.php";


$defaultProvince = "";
$defaultCity = "";

if (!isset($_SESSION["IS_LOGIN"])) {
	$_SESSION["IS_LOGIN"] = false;
}

// Load the user's module access + menu tree into the session once per login.
// The sidebar (views/navbar_1.php) renders menu items from this session data.
if ($_SESSION["IS_LOGIN"] && empty($_SESSION["user_modules"])) {
	require_once __DIR__ . '/controller/connect.php';

	$roleId = (int)($_SESSION["role_id"] ?? 0);
	$accessMenu = [];

	if ($roleId > 0) {
		$roleRes = mysqli_query($conn, "SELECT access FROM users_role WHERE id='$roleId' LIMIT 1");
		$roleRow = $roleRes ? mysqli_fetch_assoc($roleRes) : null;
		if ($roleRow && !empty($roleRow['access'])) {
			$accessMenu = array_filter(array_map('intval', explode(',', $roleRow['access'])));
			sort($accessMenu);
		}
	}

	$modules = [];
	$modRes = mysqli_query($conn, "
		SELECT id, parent_id, title, icon, page, filename, sort_order, is_menu
		FROM user_modules
		WHERE status = 1
		ORDER BY parent_id ASC, sort_order ASC, title ASC
	");
	if ($modRes) {
		while ($m = mysqli_fetch_assoc($modRes)) {
			$m['id']        = (int)$m['id'];
			$m['parent_id'] = (int)$m['parent_id'];
			$m['sort_order'] = (int)$m['sort_order'];
			$m['is_menu']   = (int)$m['is_menu'];
			$modules[] = $m;
		}
	}

	$_SESSION["role_access"]  = $accessMenu;
	$_SESSION["user_modules"] = $modules;
	$_SESSION["name"] = $_SESSION["name"] ?? ($_SESSION["username"] ?? 'User');
}


// Split the URI into segments
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));
$indexPosition = array_search('index.php', $segments);
$route = isset($segments[$indexPosition + 1]) ? $segments[$indexPosition + 1] : '';

// Normalize /index.php/<route> document URLs so relative asset paths resolve correctly
if (strpos($uri, '/index.php/') !== false) {
	header("Location: " . str_replace('/index.php/', '/', $uri));
	exit;
}

// $has_patient = isset($_SESSION["PATIENT_INFO"]) ? 1:0;
if ($route === 'logout') {
	$_SESSION = [];
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	}
	session_destroy();
	header("Location: " . $baseURL . "login");
	exit;
}

$publicRoutes = ['', 'login', 'page-login', 'page-register', 'page-forgot-password', 'page-lock-screen', 'page-error-400', 'page-error-403', 'page-error-404', 'page-error-500', 'page-error-503'];

if (!$_SESSION["IS_LOGIN"] && !in_array($route, $publicRoutes, true) && strpos($uri, '.') === false) {
	header("Location: " . $baseURL . "login");
	exit;
}

$is404 = false;

switch ($route) {
	case '':
	case '/':
	case 'login':
		if ($_SESSION["IS_LOGIN"]) {
			header("Location: " . $baseURL . "home");
			exit;
		}
		require_once 'views/page-login.php';
		break;
	// ==================== Dashboard ====================
	case 'home':
	case 'dashboard':
		require_once 'views/home.php';
		break;
	// ==================== User ====================
	case 'user-list':
		require_once 'views/users-list.php';
		break;
	case 'add-user':
		require_once 'views/users-add.php';
		break;
	case 'edit-user':
		require_once 'views/users-edit.php';
		break;
	case 'view-user':
		require_once 'views/users-view.php';
		break;
	case 'profile':
		require_once 'views/profile.php';
		break;
	// ==================== Module ====================
	case 'module-list':
		require_once 'views/module-list.php';
		break;
	// ==================== User Roles ====================
	case 'user-roles':
		require_once 'views/user-role.php';
		break;
	// ==================== Machine ====================
	case 'machine-list':
		require_once 'views/machine-list.php';
		break;
	case 'machine-types':
		require_once 'views/machine-types.php';
		break;
	case 'machine-maintenance':
		require_once 'views/machine-maintenance.php';
		break;
	// ==================== Branch ====================
	case 'branch-list':
		require_once 'views/branch-list.php';
		break;
	// ==================== Agency ====================
	case 'agency':
		require_once 'views/agency.php';
		break;
	// ==================== Beneficiary ====================
	case 'beneficiary-list':
		require_once 'views/beneficiary-list.php';
		break;
	case 'beneficiary-verify-list':
		require_once 'views/beneficiary-verify-list.php';
		break;
	case 'beneficiary-verify':
		require_once 'views/beneficiary-verify.php';
		break;
	case 'beneficiary-view':
		require_once 'views/beneficiary-view.php';
		break;
	case 'beneficiary-add':
		require_once 'views/beneficiary-add.php';
		break;
	case 'beneficiary-edit':
		require_once 'views/beneficiary-edit.php';
		break;
	// ==================== Facility ====================
	case 'facility-type':
		require_once 'views/facility-type.php';
		break;
	case 'list-facility':
		require_once 'views/facility-list.php';
		break;
	case 'add-facility':
		require_once 'views/facility-add.php';
		break;
	case 'edit-facility':
		require_once 'views/facility-edit.php';
		break;
	// ==================== Booking ====================
	case 'booking-browse':
		require_once 'views/booking-browse.php';
		break;
	case 'booking-beneficiary':
		require_once 'views/booking-beneficiary.php';
		break;
	case 'booking-available':
		require_once 'views/booking-available.php';
		break;
	case 'booking-training':
		require_once 'views/booking-training.php';
		break;
	case 'booking-approval':
		require_once 'views/booking-approval.php';
		break;
	case 'booking-completed':
		require_once 'views/booking-completed.php';
		break;
	case 'booking-declined':
		require_once 'views/booking-declined.php';
		break;
	case 'booking-list':
		require_once 'views/booking-list.php';
		break;
	// ==================== Program ====================
	case 'list-programs':
		require_once 'views/program-list.php';
		break;
	case 'add-program':
		require_once 'views/program-add.php';
		break;
	case 'list-allocations':
		require_once 'views/program-allocation.php';
		break;
	case 'add-allocation':
		require_once 'views/program-allocation-add.php';
		break;
	case 'edit-allocation':
		require_once 'views/program-allocation-edit.php';
		break;
	case 'list-program-beneficiary':
		require_once 'views/program-beneficiary.php';
		break;
	// ==================== Training ====================
	case 'list-training':
		require_once 'views/training-list.php';
		break;
	case 'add-training':
		require_once 'views/training-add.php';
		break;
	case 'training-admission':
		require_once 'views/training-admission.php';
		break;
	case 'training-available':
		require_once 'views/training-available.php';
		break;
	// ==================== Land Monitoring ====================
	case 'list-land-monitoring':
		require_once 'views/land-monitoring-logs.php';
		break;
	case 'list-land-parcel':
		require_once 'views/land-monitoring-parcel.php';
		break;
	case 'add-land-parcel':
		require_once 'views/land-monitoring-parcel-add.php';
		break;
	case 'edit-land-parcel':
		require_once 'views/land-monitoring-parcel-edit.php';
		break;
	case 'list-land-records':
		require_once 'views/land-monitoring-record.php';
		break;
	case 'add-land-records':
		require_once 'views/land-monitoring-record-add.php';
		break;
	case 'edit-land-records':
		require_once 'views/land-monitoring-record-edit.php';
		break;
	// ==================== Product ====================
	case 'list-products':
		require_once 'views/product-list.php';
		break;
	case 'add-product':
		require_once 'views/product-add.php';
		break;
	case 'edit-product':
		require_once 'views/product-edit.php';
		break;
	case 'product-details':
		require_once 'views/product-details.php';
		break;
	case 'product-category':
		require_once 'views/product-category.php';
		break;
	case 'product-available':
		require_once 'views/product-available.php';
		break;
	case 'list-inventory':
		require_once 'views/product-inventory.php';
		break;
	case 'add-inventory':
		require_once 'views/product-inventory-add.php';
		break;
	case 'stock-movements':
		require_once 'views/product-stock-movements.php';
		break;
	case 'low-stock':
		require_once 'views/product-low-stock.php';
		break;
	case 'simulation':
		require_once 'views/product-simulation.php';
		break;
	case 'facility-prices':
		require_once 'views/facility-prices.php';
		break;
	case 'price-history':
		require_once 'views/price-history.php';
		break;
	// ==================== Logs ====================
	case 'logs':
		require_once 'views/logs.php';
		break;
	// ==================== Wallet ====================
	case 'wallet-list':
		require_once 'views/wallet-list.php';
		break;
	// ==================== Pages (Standalone) ====================
	case 'page-login':
		require_once 'views/page-login.php';
		break;
	case 'page-register':
		require_once 'views/page-register.php';
		break;
	case 'page-forgot-password':
		require_once 'views/page-forgot-password.php';
		break;
	case 'page-lock-screen':
		require_once 'views/page-lock-screen.php';
		break;
	case 'page-error-400':
		require_once 'views/page-error-400.php';
		break;
	case 'page-error-403':
		require_once 'views/page-error-403.php';
		break;
	case 'page-error-404':
		require_once 'views/page-error-404.php';
		break;
	case 'page-error-500':
		require_once 'views/page-error-500.php';
		break;
	case 'page-error-503':
		require_once 'views/page-error-503.php';
		break;
	case 'empty-page':
		require_once 'views/empty-page.php';
		break;

	default:
		http_response_code(404);
		$is404 = true;
		require_once 'views/page-error-404.php';
}

if (!$is404 && !in_array($route, $publicRoutes, true)) {
	require 'views/layout.php';
}
