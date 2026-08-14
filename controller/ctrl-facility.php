<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data  = json_decode(file_get_contents("php://input"), true);
$trans = $_GET['trans'] ?? ($data['trans'] ?? '');

if (!$conn) {
    echo json_encode([
        "code" => 1,
        "message" => "Database connection failed",
        "data" => null
    ]);
    exit;
}

if (empty($trans)) {
    echo json_encode([
        "code" => 1,
        "message" => "Transaction is required",
        "data" => null
    ]);
    exit;
}

if ($trans === 'LIST_FACILITY') {

    $role_id    = (int)($_SESSION['role_id'] ?? 0);
    $employee_id = (int)($_SESSION['employee_id'] ?? $_SESSION['user_id'] ?? 0);

    $page   = max(1, (int)($data['page'] ?? 1));
    $limit  = max(1, (int)($data['limit'] ?? 10));
    $offset = ($page - 1) * $limit;

    $where = " WHERE 1=1 ";

    /**
     * role_id = 1 -> ADMIN
     * role_id = 2 -> EMPLOYEE
     */
    if ($role_id == 2) {
        $where .= " AND f.id = (SELECT facility_id FROM employee WHERE users_id = $employee_id LIMIT 1) ";
    }

    $totalQuery = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM facility f
        $where
    ");

    if (!$totalQuery) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn)
        ]);
        exit;
    }

    $total = mysqli_fetch_assoc($totalQuery)['total'] ?? 0;

    $result = mysqli_query($conn, "
        SELECT 
            f.*,
            GROUP_CONCAT(ft.name ORDER BY ft.id SEPARATOR ', ') AS facility_types
        FROM facility f
        LEFT JOIN facility_type ft 
            ON FIND_IN_SET(ft.id, f.type_ids)
        $where
        GROUP BY f.id
        ORDER BY f.id DESC
        LIMIT $offset, $limit
    ");

    if (!$result) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn)
        ]);
        exit;
    }

    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "items" => $rows,
            "pagination" => [
                "page"  => $page,
                "limit" => $limit,
                "total" => (int)$total
            ]
        ]
    ]);

    exit;
} else if ($trans === 'GET_FACILITY') {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $sql = mysqli_query($conn, "
        SELECT 
            f.*,
            GROUP_CONCAT(ft.name ORDER BY ft.id SEPARATOR ', ') AS facility_types,
            b.name AS branch_name,
            (SELECT users_id FROM employee WHERE facility_id = f.id LIMIT 1) AS employee_id
        FROM facility f
        LEFT JOIN facility_type ft ON FIND_IN_SET(ft.id, f.type_ids)
        LEFT JOIN branch b ON b.id = f.branch_id
        WHERE f.id='$id'
        GROUP BY f.id
        LIMIT 1
    ");

    $row = mysqli_fetch_assoc($sql);

    echo json_encode([
        "code" => $row ? 0 : 1,
        "message" => $row ? "Success" : "Facility not found",
        "data" => $row
    ]);

    exit;
} else if ($trans === 'ADD_FACILITY') {

    $branch_id  = $data['branch_id'] ?? '';
    $type_ids = $data['type_ids'] ?? '';
    $employee_id = $data['employee_id'] ?? '';

    if (is_array($type_ids)) {
        $type_ids = implode(',', $type_ids);
    }

    $name           = $data['name'] ?? '';
    $phone          = $data['phone'] ?? '';
    $email          = $data['email'] ?? '';
    $street         = $data['street'] ?? '';
    $barangay_id    = $data['barangay_id'] ?? '';
    $district_id    = $data['district_id'] ?? '';
    $city_id        = $data['city_id'] ?? '';
    $province_id    = $data['province_id'] ?? '';
    $region_id      = $data['region_id'] ?? '';
    $postal_code    = $data['postal_code'] ?? '';
    $address        = $data['address'] ?? '';
    $location_lat   = $data['location_lat'] ?? '';
    $location_lng   = $data['location_lng'] ?? '';
    $operating_hours = $data['operating_hours'] ?? '';
    $status         = $data['status'] ?? 1;

    if (empty($type_ids) || empty($name)) {
        echo json_encode([
            "code" => 1,
            "message" => "type_id and name are required",
            "data" => null
        ]);
        exit;
    }

    $insert = mysqli_query($conn, "
        INSERT INTO facility (
            branch_id,
            type_ids,
            name,
            phone,
            email,
            street,
            barangay_id,
            district_id,
            city_id,
            province_id,
            region_id,
            postal_code,
            address,
            operating_hours,
            status,
            created_at
        ) VALUES (
            " . ($branch_id ? "'$branch_id'" : "NULL") . ",
            '$type_ids',
            '$name',
            '$phone',
            '$email',
            '$street',
            " . ($barangay_id ? "'$barangay_id'" : "NULL") . ",
            " . ($district_id ? "'$district_id'" : "NULL") . ",
            " . ($city_id ? "'$city_id'" : "NULL") . ",
            " . ($province_id ? "'$province_id'" : "NULL") . ",
            " . ($region_id ? "'$region_id'" : "NULL") . ",
            '$postal_code',
            '$address',
            '$operating_hours',
            '$status',
            NOW()
        )
    ");

    if (!$insert) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    $facility_id = mysqli_insert_id($conn);

    if (!empty($employee_id)) {
        mysqli_query($conn, "UPDATE employee SET facility_id='$facility_id' WHERE users_id='$employee_id'");
    }

    echo json_encode([
        "code" => 0,
        "message" => "Facility added successfully",
        "data" => [
            "id" => $facility_id
        ]
    ]);

    exit;
} else if ($trans === 'UPDATE_FACILITY') {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $branch_id  = $data['branch_id'] ?? '';
    $type_ids = $data['type_ids'] ?? '';
    $employee_id = $data['employee_id'] ?? '';

    if (is_array($type_ids)) {
        $type_ids = implode(',', $type_ids);
    }
    
    $name           = $data['name'] ?? '';
    $phone          = $data['phone'] ?? '';
    $email          = $data['email'] ?? '';
    $street         = $data['street'] ?? '';
    $barangay_id    = $data['barangay_id'] ?? '';
    $district_id    = $data['district_id'] ?? '';
    $city_id        = $data['city_id'] ?? '';
    $province_id    = $data['province_id'] ?? '';
    $region_id      = $data['region_id'] ?? '';
    $postal_code    = $data['postal_code'] ?? '';
    $address        = $data['address'] ?? '';
    $location_lat   = $data['location_lat'] ?? '';
    $location_lng   = $data['location_lng'] ?? '';
    $operating_hours = $data['operating_hours'] ?? '';
    $status         = $data['status'] ?? 1;

    $update = mysqli_query($conn, "
        UPDATE facility SET
            branch_id=" . ($branch_id ? "'$branch_id'" : "NULL") . ",
            type_ids='$type_ids',
            name='$name',
            phone='$phone',
            email='$email',
            street='$street',
            barangay_id=" . ($barangay_id ? "'$barangay_id'" : "NULL") . ",
            district_id=" . ($district_id ? "'$district_id'" : "NULL") . ",
            city_id=" . ($city_id ? "'$city_id'" : "NULL") . ",
            province_id=" . ($province_id ? "'$province_id'" : "NULL") . ",
            region_id=" . ($region_id ? "'$region_id'" : "NULL") . ",
            postal_code='$postal_code',
            address='$address',
            operating_hours='$operating_hours',
            status='$status',
            updated_at=NOW()
        WHERE id='$id'
    ");

    if (!$update) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    if (!empty($employee_id)) {
        mysqli_query($conn, "UPDATE employee SET facility_id=NULL WHERE facility_id='$id' AND users_id != '$employee_id'");
        mysqli_query($conn, "UPDATE employee SET facility_id='$id' WHERE users_id='$employee_id'");
    }

    echo json_encode([
        "code" => 0,
        "message" => "Facility updated successfully",
        "data" => null
    ]);

    exit;
} else if ($trans === 'DELETE_FACILITY') {

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

// Get facility by employee ID
} else if ($trans === 'GET_FACILITY_BY_EMPLOYEE') {

    $employee_id = $data['employee_id'] ?? '';

    if (empty($employee_id)) {
        echo json_encode([
            "code" => 1,
            "message" => "Employee ID is required",
            "data" => null
        ]);
        exit;
    }

    $sql = mysqli_query($conn, "
        SELECT 
            f.*,
            GROUP_CONCAT(ft.name ORDER BY ft.id SEPARATOR ', ') AS facility_types
        FROM facility f
        LEFT JOIN facility_type ft ON FIND_IN_SET(ft.id, f.type_ids)
        WHERE f.id = (SELECT facility_id FROM employee WHERE users_id='$employee_id' LIMIT 1)
        GROUP BY f.id
        LIMIT 1
    ");

    $row = mysqli_fetch_assoc($sql);

    echo json_encode([
        "code" => $row ? 0 : 1,
        "message" => $row ? "Success" : "Facility not found",
        "data" => $row
    ]); 

    exit;
} else if ($trans === 'LIST_FACILITY_BY_TYPE') { 
    $facility_type = $data['facility_type'] ?? '';

    $where = " WHERE f.status = 1 ";

    if (!empty($facility_type)) {
        $type_ids = array_values(array_filter(array_map('intval', explode(',', (string)$facility_type))));
        if ($type_ids) {
            $parts = [];
            foreach ($type_ids as $tid) {
                $parts[] = "FIND_IN_SET('$tid', f.type_ids)";
            }
            $where .= " AND (" . implode(" OR ", $parts) . ") ";
        }
    }

    $result = mysqli_query($conn, "
        SELECT
            f.*,
            GROUP_CONCAT(ft.name ORDER BY ft.id SEPARATOR ', ') AS facility_types
        FROM facility f
        LEFT JOIN facility_type ft ON FIND_IN_SET(ft.id, f.type_ids)
        $where
        GROUP BY f.id
        ORDER BY f.name ASC
    ");

    if (!$result) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn)
        ]);
        exit;
    }

    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $rows
    ]);

    exit;
}

echo json_encode([
    "code" => 1,
    "message" => "Unknown transaction",
    "data" => null
]);
