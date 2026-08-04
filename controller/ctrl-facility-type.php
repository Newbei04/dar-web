<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
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

if ($trans === 'LIST_FACILITY_TYPE') {

    $id = $data['id'] ?? '';
    $page  = max(1, (int)($data['page'] ?? 1));
    $limit = max(1, (int)($data['limit'] ?? 10));
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";

    if (!empty($id)) {
        $where .= " AND id='$id'";
    }

    $totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM facility_type $where");
    $total = mysqli_fetch_assoc($totalResult)['total'] ?? 0;

    $result = mysqli_query(
        $conn,
        "SELECT id, name, details, created_at, updated_at
         FROM facility_type
         $where
         ORDER BY id DESC
         LIMIT $offset, $limit"
    );

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "page" => $page,
            "limit" => $limit,
            "total" => (int)$total,
            "result" => !empty($id) ? ($rows[0] ?? null) : $rows
        ]
    ]);
    exit;
} else if ($trans === 'ADD_FACILITY_TYPE') {

    $name = $data['name'] ?? '';
    $details = $data['details'] ?? '';

    if (empty($name)) {
        echo json_encode([
            "code" => 1,
            "message" => "Name is required",
            "data" => null
        ]);
        exit;
    }

    $check = mysqli_query($conn, "SELECT id FROM facility_type WHERE name='$name'");

    if (mysqli_num_rows($check) > 0) {
        echo json_encode([
            "code" => 1,
            "message" => "Facility type already exists",
            "data" => null
        ]);
        exit;
    }

    $insert = mysqli_query(
        $conn,
        "INSERT INTO facility_type(name, details, created_at)
         VALUES('$name', '$details', NOW())"
    );

    if (!$insert) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Added successfully",
        "data" => ["id" => mysqli_insert_id($conn)]
    ]);
    exit;
} else if ($trans === 'UPDATE_FACILITY_TYPE') {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $fields = [];

    if (isset($data['name'])) {
        $fields[] = "name='{$data['name']}'";
    }

    if (isset($data['details'])) {
        $fields[] = "details='{$data['details']}'";
    }

    $fields[] = "updated_at=NOW()";

    $update = mysqli_query(
        $conn,
        "UPDATE facility_type SET " . implode(",", $fields) . " WHERE id='$id'"
    );

    if (!$update) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Updated successfully",
        "data" => null
    ]);
    exit;
} else if ($trans === 'DELETE_FACILITY_TYPE') {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $delete = mysqli_query(
        $conn,
        "DELETE FROM facility_type WHERE id='$id'"
    );

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
        "message" => "Deleted successfully",
        "data" => null
    ]);
    exit;
}
echo json_encode([
    "code" => 1,
    "message" => "Unknown transaction",
    "data" => null
]);
