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

if ($trans == "ADD_BRANCH") {

    $name    = $data['name'] ?? '';
    $details = $data['details'] ?? '';

    if (empty($name)) {
        echo json_encode([
            "code" => 1,
            "message" => "Name is required",
            "data" => null
        ]);
        exit;
    }

    $result = mysqli_query($conn, "SELECT code FROM branch ORDER BY id DESC LIMIT 1");
    $last = mysqli_fetch_assoc($result);
    if ($last && preg_match('/^B(\d+)$/', $last['code'], $m)) {
        $next = (int)$m[1] + 1;
    } else {
        $next = 1;
    }
    $code = 'B' . str_pad($next, 3, '0', STR_PAD_LEFT);

    $query = "
        INSERT INTO branch (code, name, details, created_at, updated_at)
        VALUES ('$code', '$name', '$details', NOW(), NOW())
    ";

    $insert = mysqli_query($conn, $query);

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
        "message" => "Branch added (Code: $code)",
        "data" => ["id" => mysqli_insert_id($conn), "code" => $code]
    ]);
    exit;
} else if ($trans == "EDIT_BRANCH") {

    $id      = $data['id'] ?? '';
    $code    = $data['code'] ?? '';
    $name    = $data['name'] ?? '';
    $details = $data['details'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $query = "
        UPDATE branch 
        SET 
            code='$code',
            name='$name',
            details='$details',
            updated_at=NOW()
        WHERE id='$id'
    ";

    $update = mysqli_query($conn, $query);

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
        "message" => "Branch updated",
        "data" => null
    ]);
    exit;
} else if ($trans == "DELETE_BRANCH") {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $query = "DELETE FROM branch WHERE id='$id'";
    $delete = mysqli_query($conn, $query);

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
        "message" => "Branch deleted",
        "data" => null
    ]);
    exit;
} else if ($trans == "LIST_BRANCH") {

    $query = "SELECT id, code, name, details, created_at, updated_at FROM branch ORDER BY id DESC";

    $result = mysqli_query($conn, $query);

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $data
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
