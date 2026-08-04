<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Include routes and functions 
require 'sections.php';

// Load env file
$ENV = parse_ini_file(__DIR__ . '/.env');

$baseURL = $ENV['APP_URL'] . "/";
$basePath = $ENV['APP_URL'] . "/index.php";


$defaultProvince = "";
$defaultCity = "";

// For Debugging
$_SESSION["IS_LOGIN"] = true; // Set to true to bypass login for testing
$_SESSION["type"] = 1; // 1 - Admin, 2 - Cooperative, 3 - Farmer
$_SESSION["user_id"] = 1;
$_SESSION["name"] = "Juan Dela Cruz";

if (!isset($_SESSION["IS_LOGIN"])) {
	$_SESSION["IS_LOGIN"] = false;
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
