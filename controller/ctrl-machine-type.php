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

if ($trans == "ADD_MACHINERY_TYPE") {

    $name = $data['name'] ?? '';
    $details = $data['details'] ?? '';

    if (!$name) {
        echo json_encode([
            "code" => 1,
            "message" => "name is required",
            "data" => null
        ]);
        exit;
    }

    $insert = mysqli_query($conn, "
        INSERT INTO machinery_type (name, details, created_at)
        VALUES ('$name', '$details', NOW())
    ");

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
        "message" => "Machinery type added",
        "data" => [
            "id" => mysqli_insert_id($conn)
        ]
    ]);

    exit;
} else if ($trans == "LIST_MACHINERY_TYPE") {

    $id = $data['id'] ?? '';

    $page  = max(1, (int)($data['page'] ?? 1));
    $limit = max(1, (int)($data['limit'] ?? 10));
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";

    if (!empty($id)) {
        $where .= " AND id='$id'";
    }

    $totalQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM machinery_type $where");
    $total = mysqli_fetch_assoc($totalQ)['total'] ?? 0;

    $res = mysqli_query($conn, "
        SELECT id, name, details, created_at, updated_at
        FROM machinery_type
        $where
        ORDER BY id DESC
        LIMIT $offset, $limit
    ");

    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "page" => $page,
            "limit" => $limit,
            "total" => (int)$total,
            "result" => !empty($id) ? ($list[0] ?? null) : $list
        ]
    ]);

    exit;
} else if ($trans == "EDIT_MACHINERY_TYPE") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "id is required",
            "data" => null
        ]);
        exit;
    }

    $name = $data['name'] ?? null;
    $details = $data['details'] ?? null;

    $fields = [];

    if ($name !== null) {
        $fields[] = "name='$name'";
    }

    if ($details !== null) {
        $fields[] = "details='$details'";
    }

    if (empty($fields)) {
        echo json_encode([
            "code" => 1,
            "message" => "No fields to update",
            "data" => null
        ]);
        exit;
    }

    $fields[] = "updated_at=NOW()";

    $update = mysqli_query($conn, "
        UPDATE machinery_type
        SET " . implode(",", $fields) . "
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

    echo json_encode([
        "code" => 0,
        "message" => "Machinery type updated",
        "data" => null
    ]);

    exit;
} else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
    exit;
}
