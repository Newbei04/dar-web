<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$trans = $_POST['trans'] ?? $_GET['trans'] ?? ($data['trans'] ?? '');

if (!$conn) {
    echo json_encode([
        "code" => 1,
        "message" => "Database connection failed"
    ]);
    exit;
}

if ($trans == "ADD_USER") {

    $username = mysqli_real_escape_string($conn, $data['username'] ?? '');
    $password = $data['password'] ?? '123456';
    $role_id  = $data['role_id'] ?? '';
    $type     = 0;

    if (!$username || !$role_id) {
        echo json_encode([
            "code" => 1,
            "message"       => "Missing required fields"
        ]);
        exit;
    }

    // ── Check username if already exists ──────────────────────────────────────────────
    $sql = "SELECT * 
            FROM users 
            WHERE username='$username' ";

    // Execute the query
    $runQuery = mysqli_query($conn, $sql);
    $count_rows = mysqli_num_rows($runQuery);

    // If rows are found, return them as JSON
    if ($count_rows > 0) {
        echo json_encode([
            "code" => 1,
            "message"       => "User already exists"
        ]);
        exit;
    }

    // ── Check email if already exists ──────────────────────────────────────────────
    $email  = mysqli_real_escape_string($conn, $data['email']  ?? '');
    $sql = "SELECT * 
            FROM employee 
            WHERE email='$email' ";

    // Execute the query
    $runQuery = mysqli_query($conn, $sql);
    $count_rows = mysqli_num_rows($runQuery);

    // If rows are found, return them as JSON
    if ($count_rows > 0) {
        echo json_encode([
            "code" => 1,
            "message"       => "Email already exists"
        ]);
        exit;
    }

    // ── Check mobile if already exists ──────────────────────────────────────────────
    $mobile = mysqli_real_escape_string($conn, $data['mobile'] ?? '');
    $sql = "SELECT * 
            FROM employee 
            WHERE mobile='$mobile' ";

    // Execute the query
    $runQuery = mysqli_query($conn, $sql);
    $count_rows = mysqli_num_rows($runQuery);

    // If rows are found, return them as JSON
    if ($count_rows > 0) {
        echo json_encode([
            "code" => 1,
            "message"       => "Mobile number already exists"
        ]);
        exit;
    }

    // ── HASH PASSWORD ──────────────────────────────────────────────
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (
                username, password, role_id, type, status, created_at
            ) VALUES (
                '$username', '$hashed_password', '$role_id', '$type', 1, NOW()
            )";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        // Get the last inserted ID
        $users_id = mysqli_insert_id($conn);
        
        // ── PROFILE PHOTO ──────────────────────────────────────────────
        $upload_dir = dirname(__DIR__) . '/assets/images/profile/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $profile_photo = null;

        // Handle base64 image from JSON
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
                        $filename = 'user_' . $users_id . '_' . time() . '.' . $ext;

                        if (file_put_contents($upload_dir . $filename, $image_data) !== false) {
                            $profile_photo = $filename;
                        }
                    }
                }
            }
        // Fallback for file upload via $_FILES
        } else if (!empty($_FILES['profile']['name'])) {
            $ext = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));

            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $allowed)) {
                $filename = 'user_' . $users_id . '_' . time() . '.' . $ext;

                if (move_uploaded_file(
                    $_FILES['profile']['tmp_name'],
                    $upload_dir . $filename
                )) {
                    $profile_photo = $filename;
                }
            }
        }

        $photo_escaped = $profile_photo ? mysqli_real_escape_string($conn, $profile_photo) : null;
        $photo_sql     = $photo_escaped ? "'$photo_escaped'" : "NULL";

        // ── PERSONAL INFO ──────────────────────────────────────────────
        $fname  = mysqli_real_escape_string($conn, $data['fname']  ?? '');
        $mname  = mysqli_real_escape_string($conn, $data['mname']  ?? '');
        $lname  = mysqli_real_escape_string($conn, $data['lname']  ?? '');
        $gender = mysqli_real_escape_string($conn, $data['gender'] ?? '');
        $marital = mysqli_real_escape_string($conn, $data['marital'] ?? '');
        $birthday = mysqli_real_escape_string($conn, $data['birthday'] ?? '');
        $email  = mysqli_real_escape_string($conn, $data['email']  ?? '');
        $mobile = mysqli_real_escape_string($conn, $data['mobile'] ?? '');
        // ── ADDRESS ────────────────────────────────────────────────────
        $street      = mysqli_real_escape_string($conn, $data['street']  ?? '');
        $address     = mysqli_real_escape_string($conn, $data['address'] ?? '');
        $barangay_id = $data['barangay_id'] ?? '';
        $district_id = $data['district_id'] ?? null;
        $city_id     = $data['city_id']     ?? '';
        $province_id = $data['province_id'] ?? '';
        $region_id   = $data['region_id']   ?? '';
        $zip_code    = $data['zip_code']    ?? '';
        $country     = $data['country']     ?? 'PH';
        $facility_id = $data['facility_id'] ?? null;
        
        $sql = "INSERT INTO employee (
                    users_id, facility_id, reference_no, 
                    fname, mname, lname, 
                    gender, marital, birthday, email, mobile, 
                    street, address, barangay_id, 
                    district_id, city_id, province_id, region_id, zip_code, 
                    country, 
                    profile, 
                    status, created_at
                ) VALUES (
                    '$users_id', " . ($facility_id ? "'$facility_id'" : "NULL") . ", CONCAT('EMP-', '$users_id'),
                    '$fname', '$mname', '$lname', 
                    '$gender', '$marital', " . ($birthday ? "'$birthday'" : "NULL") . ", '$email', '$mobile',
                    '$street', '$address',
                    " . ($barangay_id ? "'$barangay_id'" : "NULL") . ",
                    " . ($district_id ? "'$district_id'" : "NULL") . ",
                    " . ($city_id     ? "'$city_id'"     : "NULL") . ",
                    " . ($province_id ? "'$province_id'" : "NULL") . ",
                    " . ($region_id   ? "'$region_id'"   : "NULL") . ",
                    " . ($zip_code    ? "'$zip_code'"    : "NULL") . ",
                    '$country',
                    $photo_sql,
                    1, NOW()
                )";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "code" => 0,
                "message"       => "User created successfully",
                "users_id"      => $users_id,
                "profile_photo" => $profile_photo
            ]);
        } else {
            echo json_encode([
                "code" => 1,
                "message"       => "We encountered an error in creating this user. Please try again!"
            ]);
        }
    } else {
        echo json_encode([
            "code" => 1,
            "message"       => "We encountered an error in creating this user. Please try again!"
        ]);
    }
    exit;
} else if ($trans == "LIST_USER") {
    $query = "  SELECT 
					u.id, u.username, u.type, u.status,
					(SELECT t.title FROM users_role t WHERE t.id = u.role_id LIMIT 1) AS role
				FROM users u
				WHERE u.type = 0 
                ORDER BY u.id ASC
			";
    $res = mysqli_query($conn, $query);

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {

        $id = $row['id'];
        $profile = null;

        $q = mysqli_query($conn, "SELECT * FROM employee WHERE users_id='$id'");
        $profile = mysqli_fetch_assoc($q);

        $row['profile'] = $profile;
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
} else if ($trans == "LIST_USER_ROLE") {
    $role_id = $data['role_id'] ?? null;
    $query = "SELECT * FROM users WHERE 1";

    // FILTER BY ROLE
    if (!is_null($role_id) && $role_id !== '') {
        $query .= " AND role_id = '$role_id'";
    }
    $query .= " ORDER BY id DESC";
    $res = mysqli_query($conn, $query);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $user_id = $row['id'];
        $profile = null;
        // EMPLOYEE
        if ($row['role_id'] == 1 || $row['role_id'] == 2) {
            $q = mysqli_query($conn, "
                SELECT * 
                FROM employee
                WHERE users_id = '$user_id'
            ");
            $profile = mysqli_fetch_assoc($q);
        }
        // BENEFICIARY
        else if ($row['role_id'] == 3) {
            $q = mysqli_query($conn, "
                SELECT * 
                FROM beneficiary
                WHERE users_id = '$user_id'
            ");
            $profile = mysqli_fetch_assoc($q);
        }
        $row['profile'] = $profile;
        $list[] = $row;
    }
    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
} else if ($trans == "GET_USER") {

    $id = $_GET['id'] ?? $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID required"
        ]);
        exit;
    }

    $res = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
    $row = mysqli_fetch_assoc($res);

    if (!$row) {
        echo json_encode([
            "code" => 1,
            "message" => "User not found"
        ]);
        exit;
    }

    unset($row['password']);

    $roleRes = mysqli_query($conn, "SELECT title FROM users_role WHERE id='" . $row['role_id'] . "' LIMIT 1");
    $roleRow = mysqli_fetch_assoc($roleRes);
    $row['role'] = $roleRow['title'] ?? '';

    $profile = null;

    if ($row['type'] == 0) {
        $q = mysqli_query($conn, "SELECT * FROM employee WHERE users_id='$id'");
        $profile = mysqli_fetch_assoc($q);
    }



    if ($row['type'] == 1) {
        $q = mysqli_query($conn, "SELECT * FROM beneficiary WHERE users_id='$id'");
        $profile = mysqli_fetch_assoc($q);
    }

    $row['profile'] = $profile;

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $row
    ]);
} else if ($trans == "UPDATE_USER") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID required"
        ]);
        exit;
    }

    $username = mysqli_real_escape_string($conn, $data['username'] ?? '');
    $password = $data['password'] ?? '';
    $status = mysqli_real_escape_string($conn, $data['status'] ?? 1);

    $passwordSql = '';
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = mysqli_real_escape_string($conn, $hashed_password);
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
                        $profile_photo_escaped = mysqli_real_escape_string($conn, $profile_photo);
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
} else if ($trans == "DELETE_USER") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID required"
        ]);
        exit;
    }

    $userRes = mysqli_query($conn, "SELECT type FROM users WHERE id='$id'");
    $userRow = mysqli_fetch_assoc($userRes);

    if (!$userRow) {
        echo json_encode([
            "code" => 1,
            "message" => "User not found"
        ]);
        exit;
    }

    if ($userRow['type'] == 0) {
        mysqli_query($conn, "DELETE FROM employee WHERE users_id='$id'");
    } else if ($userRow['type'] == 1) {
        mysqli_query($conn, "DELETE FROM beneficiary WHERE users_id='$id'");
    }

    if (mysqli_query($conn, "DELETE FROM users WHERE id='$id'")) {
        echo json_encode([
            "code" => 0,
            "message" => "User deleted successfully"
        ]);
    } else {
        echo json_encode([
            "code" => 1,
            "message" => "Failed to delete user"
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
            LEFT JOIN facility f ON f.id = e.facility_id AND f.status = 1
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
} else if ($trans === 'GET_ALL_FACILITY') {
    $query = "  SELECT 
                    f.id, 
                    f.name,
                    GROUP_CONCAT(ft.name) AS facility_types
                FROM facility f
                LEFT JOIN facility_type ft ON FIND_IN_SET(ft.id, f.type_ids)
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
} else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction"
    ]);
}
