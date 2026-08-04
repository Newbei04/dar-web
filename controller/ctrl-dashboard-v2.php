<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);
$trans = $data['trans'] ?? '';

if ($trans == "GET_DASHBOARD_V2_COUNTS") {

    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM beneficiary"));
    $verified = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM beneficiary WHERE status = 1"));
    $pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM beneficiary WHERE status = 0"));
    $inactive = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM beneficiary WHERE status = 2"));
    $branches = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM branch"));
    $programs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM program"));

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "total_beneficiaries" => $total['total'],
            "verified" => $verified['total'],
            "pending" => $pending['total'],
            "inactive" => $inactive['total'],
            "branches" => $branches['total'],
            "programs" => $programs['total']
        ]
    ]);
    exit;

} else if ($trans == "LIST_BENEFICIARY_V2") {

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
                -- COALESCE(fa.name, '') AS facility,
                u.username AS username,
                u.type AS user_type
            FROM beneficiary b
            LEFT JOIN branch br ON br.id = b.branch_id
            -- LEFT JOIN facility fa ON fa.id = b.facility_id
            LEFT JOIN users u ON u.id = b.users_id
            WHERE b.status = 1
            ORDER BY b.created_at DESC";

    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "count" => count($list),
        "data" => $list
    ]);
    exit;

} else if ($trans == "GET_BENEFICIARY_DETAILS") {

    $id = (int)($data['id'] ?? 0);
    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID required"]);
        exit;
    }

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
                u.username,
                u.status AS user_status
            FROM beneficiary b
            LEFT JOIN branch br ON br.id = b.branch_id
            LEFT JOIN facility fa ON fa.id = b.facility_id
            LEFT JOIN users u ON u.id = b.users_id
            WHERE b.id = '$id'
            LIMIT 1";

    $profile = mysqli_fetch_assoc(mysqli_query($conn, $sql));

    if (!$profile) {
        echo json_encode(["code" => 1, "message" => "Beneficiary not found"]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $profile
    ]);
    exit;

} else if ($trans == "GET_BENEFICIARY_BOOKINGS") {

    $id = (int)($data['id'] ?? 0);
    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID required"]);
        exit;
    }

    $sql = "SELECT
                b.*,
                m.name AS machinery_name,
                m.model AS machinery_model,
                mt.name AS machinery_type,
                -- f.name AS facility_name,
                CASE
                    WHEN b.status = 0 THEN 'Pending'
                    WHEN b.status = 1 THEN 'Borrowed'
                    WHEN b.status = 2 THEN 'Returned'
                    WHEN b.status = 3 THEN 'Declined'
                    ELSE 'Unknown'
                END AS status_label
            FROM booking b
            LEFT JOIN machinery m ON m.id = b.machinery_id
            LEFT JOIN machinery_type mt ON mt.id = m.type_id
            -- LEFT JOIN facility f ON f.id = b.facility_id
            WHERE b.beneficiary_id = '$id'
            ORDER BY b.created_at DESC";

    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
    exit;

} else if ($trans == "GET_BENEFICIARY_TRAININGS") {

    $id = (int)($data['id'] ?? 0);
    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID required"]);
        exit;
    }

    $sql = "SELECT
                ta.*,
                t.title AS training_title,
                t.reference_no,
                t.start_at,
                t.end_at,
                CASE
                    WHEN ta.status = 0 THEN 'Pending'
                    WHEN ta.status = 1 THEN 'Present'
                    WHEN ta.status = 2 THEN 'Absent'
                    ELSE 'Unknown'
                END AS status_label
            FROM training_admission ta
            LEFT JOIN training t ON t.id = ta.training_id
            WHERE ta.beneficiary_id = '$id'
            ORDER BY ta.created_at DESC";

    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
    exit;
}

echo json_encode([
    "code" => 1,
    "message" => "Invalid transaction"
]);
