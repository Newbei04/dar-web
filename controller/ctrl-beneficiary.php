<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Manila');

include_once __DIR__ . '/../config/dbcon.php';
include_once __DIR__ . '/../model/gbl.php';
include_once __DIR__ . '/../model/wallet.php';
include_once __DIR__ . '/connect.php';

// Set error logging 
GblFn::errorReporting();

// Read the raw input
$data = json_decode(file_get_contents("php://input"), true);
// Trims all string values within an array
$data = GblFn::sanitizeData($data);

// Transaction code
$trans = $data['trans'] ?? '';

if ($trans == "ADD_BENEFICIARY") {

    $username   = $data['username'] ?? '';
    $password   = $data['password'] ?? '123456';
    $email      = $data['email'] ?? '';
    $mobile     = $data['mobile'] ?? '';
    $doc_num    = $data['doc_num'] ?? '';

    // ── Check username if empty ──────────────────────────────────────────────
    if ($username === '' || $email === '' || $mobile === '' || $doc_num === '') {
        echo json_encode([
            "code" => 1,
            "message" => "Missing required fields"
        ]);
        exit;
    }

    // ── Check username if already exists ──────────────────────────────────────────────
    $is_username = GblFn::recordExists('users', 'username', $username);
    if ($is_username) {
        echo json_encode([
            "code" => 1,
            "message" => "User already exists"
        ]);
        exit;
    }

    // ── Check email if already exists ──────────────────────────────────────────────
    $is_email = GblFn::recordExists('beneficiary', 'email', $email  ?? '');
    if ($is_email) {
        echo json_encode([
            "code" => 1,
            "message" => "Email already exists"
        ]);
        exit;
    }

    // ── Check mobile if already exists ──────────────────────────────────────────────
    $is_mobile = GblFn::recordExists('beneficiary', 'mobile', $mobile  ?? '');
    if ($is_mobile) {
        echo json_encode([
            "code" => 1,
            "message" => "Mobile already exists"
        ]);
        exit;
    }

    // ── Check Document number if already exists ──────────────────────────────────────────────
    $is_mobile = GblFn::recordExists('beneficiary', 'doc_num', $doc_num  ?? '');
    if ($is_mobile) {
        echo json_encode([
            "code" => 1,
            "message" => "Document number already exists"
        ]);
        exit;
    }

    // ── STRT PDO Transaction ──────────────────────────────────────────────
    $db = DBCon::getConnection();

    try {
        $db->beginTransaction();

        // ── CREATE USER ─────────────────────────────────────────────
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $userSql = "INSERT INTO users (username, password, role_id, type, status, created_at) VALUES (?, ?, 2, 1, 1, NOW())";
        $userStmt = $db->prepare($userSql);
        $userStmt->execute([$username, $hashed_password]);

        // Captures the last ID from the 'users' table
        $users_id = $db->lastInsertId(); 

        // ── PHOTO UPLOAD ────────────────────────────────────────────
        $photo_name = GblFn::processUpload($data['profile'] ?? null, 'profile', 'user', $users_id);

        // ── CREATE BENEFICIARY ──────────────────────────────────────
        // Auto-generate unique 9-digit doc_num (003000001 format)
        do {
            $maxDoc = $db->query("SELECT doc_num FROM beneficiary WHERE doc_num REGEXP '^[0-9]+$' ORDER BY doc_num DESC LIMIT 1")->fetchColumn();
            $nextNum = $maxDoc ? (int)$maxDoc + 1 : 3000001;
            $doc_num = str_pad($nextNum, 9, '0', STR_PAD_LEFT);
            $checkDoc = $db->prepare("SELECT id FROM beneficiary WHERE doc_num = ? LIMIT 1");
            $checkDoc->execute([$doc_num]);
        } while ($checkDoc->fetch() !== false);

        $beneficiarySql = "INSERT INTO beneficiary (
                            users_id, branch_id, doc_num, fname, mname, lname, 
                            gender, marital, birthday, email, mobile, 
                            street, address, barangay_id, district_id, 
                            city_id, province_id, region_id, zip_code, 
                            country, profile, status, created_at
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW()
                        )";

        $beneficiaryStmt = $db->prepare($beneficiarySql);
        $beneficiaryStmt->execute([
            $users_id, $data['branch_id'] ?? null,
            $doc_num,
            $data['fname'] ?? '', $data['mname'] ?? '', $data['lname'] ?? '',
            $data['gender'] ?? '', $data['marital'] ?? '', $data['birthday'] ?? '',
            $data['email'] ?? '', $data['mobile'] ?? '', $data['street'] ?? '',
            $data['address'] ?? '', $data['barangay_id'] ?: null, $data['district_id'] ?: null,
            $data['city_id'] ?: null, $data['province_id'] ?: null, $data['region_id'] ?: null,
            ($data['zip_code'] ?? '') ?: null, $data['country'] ?? 'PH', $photo_name
        ]);

        // Captures the last ID from the 'beneficiary' table 
        $beneficiary_id = $db->lastInsertId();

        // ── CALL THE WALLET FUNCTION FROM OTHER CLASS ────────────────
        // We pass $beneficiary_id as the beneficiary identifier
        $accountNumber = Wallet::createWallet($beneficiary_id);

        // If we made it here, everything (User, Beneficiary, and Wallet) succeeded!
        $db->commit();

        echo json_encode([
            "code"           => 0,
            "message"        => "User and Wallet created successfully",
            "users_id"       => $users_id,
            "account_number" => $accountNumber,
            "profile_photo"  => $photo_name
        ]);

    } catch (Exception $e) {
        // If the wallet loop fails or DB crashes, everything above is deleted
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        error_log("Registration failed: " . $e->getMessage());

        echo json_encode([
            "code"    => 1,
            "message" => "We encountered an error processing registration. Please try again!"
        ]);
    }

    exit;

} else if ($trans == "LIST_BENEFICIARY") { 

    $db = DBCon::getConnection();
    $status = $data['status'] ?? '';

    $sql = "SELECT 
                b.*,
                CONCAT(
                    b.fname, ' ', 
                    CASE 
                        WHEN b.mname IS NOT NULL AND b.mname <> '' 
                        THEN CONCAT(LEFT(b.mname, 1), '.') 
                        ELSE '' 
                    END, 
                    ' ', 
                    b.lname
                ) AS name,
                CASE 
                    WHEN b.gender = 'F' THEN 'Female'
                    WHEN b.gender = 'M' THEN 'Male'
                    ELSE 'Unknown' 
                END AS gender_label,
                CASE 
                    WHEN b.marital = 'S' THEN 'Single'
                    WHEN b.marital = 'M' THEN 'Married'
                    WHEN b.marital = 'D' THEN 'Divorced'
                    WHEN b.marital = 'W' THEN 'Widowed'
                    ELSE 'Unknown' 
                END AS marital_label,
                CASE 
                    WHEN b.status = 0 THEN 'For Verification'
                    WHEN b.status = 1 THEN 'Verified'
                    WHEN b.status = 2 THEN 'Inactive'
                    WHEN b.status = 3 THEN 'Deactivated'
                    ELSE 'Unknown' 
                END AS status_label,
                COALESCE(br.name, '') AS branch,
                COALESCE(fa.name, '') AS facility,
                u.username AS username,
                u.type AS user_type,
                ur.title AS role_title,
                ur.description AS role_description
            FROM beneficiary b
            LEFT JOIN branch br ON br.id = b.branch_id
            LEFT JOIN facility fa ON fa.id = b.facility_id
            LEFT JOIN users u ON u.id = b.users_id
            LEFT JOIN users_role ur ON ur.id = u.role_id 
        ";

    if ($status == "UNVERIFIED") {
        $sql .= " WHERE b.status = 0 ";
    } else if ($status == "VERIFIED") {
        $sql .= " WHERE b.status = 1 "; 
    } else {
        $sql .= " WHERE b.status IN (0, 1) ";
    }

    $params = [];
    if (isset($_SESSION['role_id']) && $_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
        $branch_id = $_SESSION["profile"]['branch_id'] ?? '';
        
        $sql .= " AND b.branch_id = ? ";
        $params[] = $branch_id; 
    }

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $list = $stmt->fetchAll();
        $count = count($list); 

        echo json_encode([
            "code"    => 0,
            "message" => "Success beneficiary",
            "count"   => $count,
            "data"    => $list
        ]);

    } catch (PDOException $e) {
        error_log("Failed retrieving beneficiaries: " . $e->getMessage());
        
        echo json_encode([
            "code"    => 1,
            "message" => "An error occurred while compiling data streams."
        ]);
    }

    exit;

} else if ($trans == "GET_BENEFICIARY") {

    $id = $_REQUEST['id'] ?? $data['id'] ?? '';
    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID required"
        ]);
        exit;
    }

    try {
        // 2. Obtain the centralized PDO connection instance
        $db = DBCon::getConnection();

        // Structural query framework with parameter bound placeholder
        $sql = "SELECT
                    b.*,
                    u.username AS username,
                    CONCAT(
                        b.fname, ' ',
                        CASE
                            WHEN b.mname IS NOT NULL AND b.mname <> ''
                            THEN CONCAT(LEFT(b.mname, 1), '.')
                            ELSE ''
                        END,
                        ' ',
                        b.lname
                    ) AS name,
                    CASE
                        WHEN b.gender = 'F' THEN 'Female'
                        WHEN b.gender = 'M' THEN 'Male'
                        ELSE 'Unknown'
                    END AS gender_label,
                    CASE
                        WHEN b.marital = 'S' THEN 'Single'
                        WHEN b.marital = 'M' THEN 'Married'
                        WHEN b.marital = 'D' THEN 'Divorced'
                        WHEN b.marital = 'W' THEN 'Widowed'
                        ELSE 'Unknown'
                    END AS marital_label,
                    (SELECT name FROM facility WHERE id = b.facility_id LIMIT 1) AS facility,
                    (SELECT name FROM branch WHERE id = b.branch_id LIMIT 1) AS branch
                FROM beneficiary b
                LEFT JOIN users u ON u.id = b.users_id
                WHERE u.id = ?
                LIMIT 1;";

        // 3. Prepare and safely execute the statement mapping
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        
        // 4. Fetch the single matching row record
        $profile = $stmt->fetch();

        if (!$profile) {
            echo json_encode([
                "code" => 1,
                "message" => "Beneficiary not found"
            ]);
            exit;
        }

        echo json_encode([
            "code" => 0,
            "message" => "Beneficiary found successfully",
            "data" => $profile
        ]);

    } catch (PDOException $e) {
        // Log internal error trace messages away from client endpoints
        error_log("Failed searching beneficiary ID ({$id}): " . $e->getMessage());
        
        echo json_encode([
            "code" => 1,
            "message" => "An error occurred while compiling beneficiary metrics."
        ]);
    }

    exit;
    
} else if ($trans == "UPDATE_USER") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID required"
        ]);
        exit;
    }

    $username = trim($data['username']) ?? '';
    $password = $data['password'] ?? '';
    $status = trim($data['status'] ?? 1);

    $passwordSql = '';
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = trim($hashed_password);
        $passwordSql = ",password='$hashed_password'";
    }

    mysqli_query($conn, "UPDATE users SET username='$username'$passwordSql,status='$status',updated_at=NOW() WHERE id='$id'");

    $userRes = mysqli_query($conn, "SELECT role_id, type FROM users WHERE id='$id'");
    $userRow = mysqli_fetch_assoc($userRes);

    $role_id = $userRow['role_id'];
    $type = $userRow['type'];

    $fname = $data['fname'] ?? '';
    $mname = $data['mname'] ?? '';
    $lname = $data['lname'] ?? '';

    $gender = $data['gender'] ?? '';
    $marital = $data['marital'] ?? '';
    $birthday = $data['birthday'] ?? '';

    $email = $data['email'] ?? '';
    $mobile = $data['mobile'] ?? '';

    $street = $data['street'] ?? '';
    $address = $data['address'] ?? '';
    $facility_id = $data['facility_id'] ?? null;

    $barangay_id = $data['barangay_id'] ?? '';
    $district_id = $data['district_id'] ?? null;
    $city_id = $data['city_id'] ?? '';
    $province_id = $data['province_id'] ?? '';
    $region_id = $data['region_id'] ?? '';

    // ── PROFILE PHOTO UPDATE ──────────────────────────────────────
    $profile_photo = null;
    $photo_sql = '';

    if (!empty($data['profile'])) {
        $base64_string = $data['profile'];

        if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
            $ext = strtolower($matches[1]);
            $ext = ($ext === 'jpeg') ? 'jpg' : $ext;

            $allowed = ['jpg', 'png', 'gif', 'webp'];

            if (in_array($ext, $allowed)) {
                $image_data = substr($base64_string, strpos($base64_string, ',') + 1);
                $image_data = base64_decode($image_data);

                if ($image_data !== false) {
                    $upload_dir = dirname(__DIR__) . '/assets/images/profile/';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $filename = 'user_' . $id . '_' . time() . '.' . $ext;

                    if (file_put_contents($upload_dir . $filename, $image_data) !== false) {
                        $profile_photo = $filename;
                        $profile_photo_escaped = trim($profile_photo);
                        $photo_sql = ",profile='$profile_photo_escaped'";
                    }
                }
            }
        }
    }

    if ($type == 0) {

        mysqli_query($conn, "UPDATE employee SET fname='$fname',mname='$mname',lname='$lname',gender='$gender',marital='$marital',birthday='$birthday',email='$email',mobile='$mobile',street='$street',address='$address',barangay_id=" . ($barangay_id ? "'$barangay_id'" : "NULL") . ",district_id=" . ($district_id ? "'$district_id'" : "NULL") . ",city_id=" . ($city_id ? "'$city_id'" : "NULL") . ",province_id=" . ($province_id ? "'$province_id'" : "NULL") . ",region_id=" . ($region_id ? "'$region_id'" : "NULL") . ",updated_at=NOW()$photo_sql WHERE users_id='$id'");
        
        // Link facility to cooperative
        if ($role_id == 2 && $facility_id) {
            $empRes = mysqli_query($conn, "SELECT id FROM employee WHERE users_id='$id'");
            $empRow = mysqli_fetch_assoc($empRes);
            $employee_id = $empRow['id'];
            mysqli_query($conn, "UPDATE employee SET facility_id='$facility_id' WHERE id='$employee_id'");
        }
    }

    if ($type == 1) {

        mysqli_query($conn, "UPDATE beneficiary SET fname='$fname',mname='$mname',lname='$lname',gender='$gender',marital='$marital',birthday='$birthday',email='$email',mobile='$mobile',street='$street',address='$address',barangay_id=" . ($barangay_id ? "'$barangay_id'" : "NULL") . ",district_id=" . ($district_id ? "'$district_id'" : "NULL") . ",city_id=" . ($city_id ? "'$city_id'" : "NULL") . ",province_id=" . ($province_id ? "'$province_id'" : "NULL") . ",region_id=" . ($region_id ? "'$region_id'" : "NULL") . ",updated_at=NOW()$photo_sql WHERE users_id='$id'");
    }

    echo json_encode([
        "code" => 0,
        "message" => "User updated successfully",
        "profile_photo" => $profile_photo
    ]);

    exit;

} else if ($trans == "UPDATE_BENEFICIARY") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID required"
        ]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $db->beginTransaction();

        // ── UPDATE USERS TABLE ──────────────────────────────────────
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $status   = $data['status'] ?? 1;

        $userUpdates = ['username = ?', 'status = ?', 'updated_at = NOW()'];
        $userParams  = [$username, $status];

        if ($password !== '') {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $userUpdates[] = 'password = ?';
            $userParams[]  = $hashed_password;
        }

        $userParams[] = $id;
        $userSql = "UPDATE users SET " . implode(', ', $userUpdates) . " WHERE id = ?";
        $db->prepare($userSql)->execute($userParams);

        // ── PROFILE PHOTO UPDATE ──────────────────────────────────────
        $photo_name = null;
        if (!empty($data['profile'])) {
            $photo_name = GblFn::processUpload($data['profile'], 'profile', 'user', $id);
        }

        // ── UPDATE BENEFICIARY TABLE ──────────────────────────────────
        $benUpdates = [
            'fname = ?', 'mname = ?', 'lname = ?',
            'gender = ?', 'marital = ?', 'birthday = ?',
            'email = ?', 'mobile = ?', 'doc_num = ?',
            'street = ?', 'address = ?', 'barangay_id = ?', 'district_id = ?',
            'city_id = ?', 'province_id = ?', 'region_id = ?',
            'country = ?', 'status = ?', 'updated_at = NOW()'
        ];
        $benParams = [
            $data['fname'] ?? '', $data['mname'] ?? '', $data['lname'] ?? '',
            $data['gender'] ?? '', $data['marital'] ?? '', $data['birthday'] ?? '',
            $data['email'] ?? '', $data['mobile'] ?? '', $data['doc_num'] ?? '',
            $data['street'] ?? '', $data['address'] ?? '', $data['barangay_id'] ?: null, $data['district_id'] ?: null,
            $data['city_id'] ?: null, $data['province_id'] ?: null, $data['region_id'] ?: null,
            $data['country'] ?? 'PH', $data['status'] ?? 1
        ];

        if (isset($data['branch_id'])) {
            $benUpdates[] = 'branch_id = ?';
            $benParams[]  = $data['branch_id'] ?: null;
        }

        if ($photo_name) {
            $benUpdates[] = 'profile = ?';
            $benParams[]  = $photo_name;
        }

        $benParams[] = $id;
        $benSql = "UPDATE beneficiary SET " . implode(', ', $benUpdates) . " WHERE users_id = ?";
        $db->prepare($benSql)->execute($benParams);

        $db->commit();

        echo json_encode([
            "code" => 0,
            "message" => "Beneficiary updated successfully",
            "profile_photo" => $photo_name
        ]);

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Update beneficiary failed: " . $e->getMessage());
        echo json_encode([
            "code" => 1,
            "message" => "We encountered an error updating the beneficiary. Please try again!"
        ]);
    }

    exit;

} else if ($trans == "GET_INVOICE_DATA") {

    $beneficiary_id = $data['beneficiary_id'];
    $cooperative_id = $data['cooperative_id'];

    // Validate input
    if (empty($beneficiary_id) || empty($cooperative_id)) {
        echo json_encode([
            "code" => 1,
            "message" => "Missing beneficiary or cooperative",
            "data" => null
        ]);
        exit;
    }

    $coop_query = "SELECT 
                u.id as user_id,
                e.fname, e.mname, e.lname, e.email, e.mobile, e.address,
                f.name as facility_name
            FROM users u
            INNER JOIN employee e ON u.id = e.users_id
            LEFT JOIN facility f ON f.employee_id = e.id AND f.status = 1
            WHERE u.id = $cooperative_id
            LIMIT 1";

    $coop_res = mysqli_query($conn, $coop_query);
    $cooperative = null;

    if ($coop_res && mysqli_num_rows($coop_res) > 0) {
        $cooperative = mysqli_fetch_assoc($coop_res);
    }

    $beneficiary_query = "SELECT 
                u.id,
                u.type,
                CASE 
                    WHEN u.type = 1 THEN 'Admin'
                    WHEN u.type = 2 THEN 'Cooperative'
                    WHEN u.type = 3 THEN 'Beneficiary'
                    ELSE 'User'
                END as type_label,
                COALESCE(e.fname, b.fname) as fname,
                COALESCE(e.mname, b.mname) as mname,
                COALESCE(e.lname, b.lname) as lname,
                COALESCE(e.email, b.email) as email,
                COALESCE(e.mobile, b.mobile) as mobile,
                COALESCE(e.address, b.address) as address
            FROM users u
            LEFT JOIN employee e ON u.id = e.users_id
            LEFT JOIN beneficiary b ON u.id = b.users_id
            WHERE u.id = $beneficiary_id
            LIMIT 1";

    $beneficiary_res = mysqli_query($conn, $beneficiary_query);
    $beneficiary = null;

    if ($beneficiary_res && mysqli_num_rows($beneficiary_res) > 0) {
        $beneficiary = mysqli_fetch_assoc($beneficiary_res);
    }

    // ==============================
    // RESPONSE
    // ==============================
    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "cooperative" => $cooperative,
            "beneficiary" => $beneficiary
        ]
    ]);

    exit;
} else if ($trans == "GET_ALL_USER_ROLE") {
    // This endpoint is used to get all user roles except beneficiary (role_id = 2) for the role dropdown
    $query = "SELECT * FROM users_role WHERE status = 1 AND id <> 2 ORDER BY id ASC";
    $res = mysqli_query($conn, $query);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }
    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $delete = mysqli_query($conn, "
        DELETE FROM facility
        WHERE id='$id'
    ");

    if (!$delete) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Facility deleted successfully",
        "data" => null
    ]);

    exit;
} else if ($trans === 'GET_COOPERATIVE_FACILITY') {
    $query = "  SELECT 
                    f.id, 
                    f.name,
                    GROUP_CONCAT(ft.name) AS facility_types
                FROM facility f
                LEFT JOIN facility_type ft ON FIND_IN_SET(ft.id, f.type_ids)
                WHERE FIND_IN_SET(ft.id, f.type_ids)
                GROUP BY f.id ";
    $sql = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $rows[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $rows
    ]);

    exit;

} else if ($trans === 'LIST_BRANCH') {
    $query = "  SELECT 
                    *
                FROM branch
                -- WHERE status=1
            ";
    $sql = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $rows[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $rows
    ]);

    exit;

} else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction"
    ]);
}
