<?php

session_start();
header('Content-Type: application/json');

include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$trans = $data['trans'] ?? '';

if ($trans == "LOGIN") {

    $username = mysqli_real_escape_string($conn, $data['username'] ?? '');
    $password = $data['password'] ?? '';

    if (!$username || !$password) {
        echo json_encode([
            "code" => 1,
            "message" => "Username and password are required"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        SELECT 
            u.id as users_id,
            u.username,
            u.password,
            u.role_id,
            u.type,
            u.status,
            (SELECT t.title FROM users_role t WHERE t.id = u.role_id LIMIT 1) AS role
        FROM users u
        WHERE u.username = '$username'
        LIMIT 1
    ");

    if (mysqli_num_rows($query) == 0) { 
        echo json_encode([
            "code" => 1,
            "message" => "Invalid username or password"
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($query);

    // VERIFY HASHED PASSWORD, WITH ONE-TIME UPGRADE FOR LEGACY PLAINTEXT ROWS
    $passwordMatches = password_verify($password, $row['password']);

    // This means the password is not hashed yet, but matches the plaintext password
    // We will hash the password and update the database for better security
    // This is for temporary support of legacy plaintext passwords and should be removed after all passwords are upgraded
    if (!$passwordMatches && hash_equals((string) $row['password'], $password)) {
        $passwordMatches = true;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed_password', updated_at=NOW() WHERE id='{$row['users_id']}'");
    }
    
    if (!$passwordMatches) { 
        echo json_encode([
            "code" => 1,
            "message" => "Invalid username or password"
        ]);
        exit;
    }

    // CHECK STATUS
    if ($row['status'] != 1) { 
        echo json_encode([
            "code" => 1,
            "message" => "Account is inactive"
        ]);
        exit;
    }

    $_SESSION['users_id'] = $row['users_id'];
    $_SESSION['user_id'] = $row['users_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['role_id'] = $row['role_id'];
    $_SESSION['type'] = $row['type'];
    
    $profile = null;
    if($row['type'] == 0) {
        $sql = mysqli_query($conn, "SELECT * FROM employee WHERE users_id='{$row['users_id']}'");
        $profile = mysqli_fetch_assoc($sql);
    } else {
        $sql = mysqli_query($conn, "SELECT * FROM beneficiary WHERE users_id='{$row['users_id']}'");
        $profile = mysqli_fetch_assoc($sql);
    }
    $_SESSION["profile"] = $profile;
    $_SESSION["IS_LOGIN"] = true;

    $fullName = trim(($profile['fname'] ?? '') . ' ' . ($profile['lname'] ?? ''));
    $_SESSION['name'] = $fullName !== '' ? $fullName : $row['username'];

    // ── Load role access (access_menu) + module tree into session ──
    $accessMenu = [];
    $roleRes = mysqli_query($conn, "SELECT access FROM users_role WHERE id='{$row['role_id']}' LIMIT 1");
    $roleRow = $roleRes ? mysqli_fetch_assoc($roleRes) : null;
    if ($roleRow && !empty($roleRow['access'])) {
        $accessMenu = array_filter(array_map('intval', explode(',', $roleRow['access'])));
        sort($accessMenu);
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
            $m['id']         = (int)$m['id'];
            $m['parent_id']  = (int)$m['parent_id'];
            $m['sort_order'] = (int)$m['sort_order'];
            $m['is_menu']    = (int)$m['is_menu'];
            $modules[] = $m;
        }
    }

    $_SESSION['role_access']  = $accessMenu;
    $_SESSION['user_modules'] = $modules;

    echo json_encode([
        "code" => 0,
        "message" => "Login successful",
        "users_id" => $row['users_id'],
        "role_id" => $row['role_id']
    ]);
} else if ($trans == "LOGOUT") {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    session_destroy();

    echo json_encode([
        "code" => 0,
        "message" => "Logout successful"
    ]);
    exit;
} else if ($trans == "CHANGE_PASSWORD") {
    if (empty($_SESSION['IS_LOGIN'])) {
        echo json_encode([
            "code" => 1,
            "message" => "Unauthorized request"
        ]);
        exit;
    }

    $id = (int)($data['id'] ?? 0);
    $password = $data['password'] ?? '';

    if (!$id || !$password) {
        echo json_encode([
            "code" => 1,
            "message" => "User and password are required"
        ]);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode([
            "code" => 1,
            "message" => "Password must be at least 6 characters"
        ]);
        exit;
    }

    $sessionUserId = (int)($_SESSION['user_id'] ?? 0);
    $sessionType = (int)($_SESSION['type'] ?? 0);

    if ($sessionType !== 1 && $sessionUserId !== $id) {
        echo json_encode([
            "code" => 1,
            "message" => "You are not allowed to update this password"
        ]);
        exit;
    }

    $userQ = mysqli_query($conn, "SELECT id FROM users WHERE id='$id' LIMIT 1");

    if (!$userQ || mysqli_num_rows($userQ) == 0) {
        echo json_encode([
            "code" => 1,
            "message" => "User not found"
        ]);
        exit;
    }

    $hashed_password = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));
    $update = mysqli_query($conn, "UPDATE users SET password='$hashed_password', updated_at=NOW() WHERE id='$id'");

    if (!$update) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn)
        ]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Password updated successfully"
    ]);
    exit;
} else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction type"
    ]);
}

