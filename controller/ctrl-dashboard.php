<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';
error_reporting(0);

$data = json_decode(file_get_contents("php://input"), true);
$trans = $data['trans'] ?? '';

if ($trans == "GET_DASHBOARD_COUNTS") {

    $response = [];

    if ($data['role_id'] == 1 || $data['role_id'] == 2 || $data['role_id'] == 3) {

        $machines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM machinery"));
        $bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM machinery WHERE status = 2"));
        $facility = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM facility"));
        $programs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM program"));
        $users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));
        $trainings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM training"));
        $beneficiaries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE type = 1"));
        $inventory = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT SUM(current_stock) AS total 
            FROM product_inventory
        "));
        $reserved = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT SUM(reserved_stock) AS total 
            FROM product_inventory
        "));

        $response = [
            "machines" => $machines['total'],
            "bookings" => $bookings['total'],
            "facility" => $facility['total'],
            "programs" => $programs['total'],
            "trainings" => $trainings['total'],
            "total_inventory" => $inventory['total'] ?? 0,
            "reserved" => $reserved['total'] ?? 0,
            "users_count" => $users['total'],
            "beneficiaries_count" => $beneficiaries['total']
        ];

        // Add branch and facility details for role_id 2 (cooperative) and 3 (beneficiary)
        $roleId = $data['role_id'];
        $usersId = $data['users_id'] ?? 0;
        if (($roleId == 2 || $roleId == 3) && $usersId) {
            $branchData = null;
            $facilityData = null;

            // Get branch_id from profile
            $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT branch_id, facility_id FROM users u LEFT JOIN employee e ON e.users_id = u.id WHERE u.id = '$usersId' LIMIT 1"));
            if (!$profile || !$profile['branch_id']) {
                $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT branch_id, facility_id FROM users u LEFT JOIN beneficiary b ON b.users_id = u.id WHERE u.id = '$usersId' LIMIT 1"));
            }

            if ($profile) {
                $branchId = $profile['branch_id'] ?? null;
                $facilityId = $profile['facility_id'] ?? null;

                if ($branchId) {
                    $branchData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM branch WHERE id = '$branchId' LIMIT 1"));
                }
                if ($facilityId) {
                    $facilityData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM facility WHERE id = '$facilityId' LIMIT 1"));
                } else if ($branchId) {
                    // Fallback: get facility by branch_id
                    $facilityData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM facility WHERE branch_id = '$branchId' LIMIT 1"));
                }
            }

            $response['branch'] = $branchData;
            $response['facility'] = $facilityData;
        }
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $response
    ]);
    exit;
}

echo json_encode([
    "code" => 1,
    "message" => "Invalid transaction",
    "data" => []
]);
