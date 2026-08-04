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

if ($trans == "ADD_AGENCY") {

    $code    = $data['code'] ?? '';
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

    if (empty($code)) {
        $words = preg_split('/\s+/', trim($name));
        $code = '';
        foreach ($words as $w) {
            if ($w !== '') {
                $code .= strtoupper(substr($w, 0, 1));
            }
        }
        if ($code === '') {
            $code = 'AGY';
        }
    }

    // ensure uniqueness
    $base   = $code;
    $suffix = 2;
    $exists = mysqli_query($conn, "SELECT id FROM agency WHERE code='$code'");
    while ($exists && mysqli_num_rows($exists) > 0) {
        $code   = $base . $suffix;
        $suffix++;
        $exists = mysqli_query($conn, "SELECT id FROM agency WHERE code='$code'");
    }

    $query = "
        INSERT INTO agency (code, name, details, created_at, updated_at)
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
        "message" => "Agency added",
        "data" => ["id" => mysqli_insert_id($conn)]
    ]);
    exit;
} else if ($trans == "EDIT_AGENCY") {

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
        UPDATE agency 
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
        "message" => "Agency updated",
        "data" => null
    ]);
    exit;
} else if ($trans == "DELETE_AGENCY") {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $query = "DELETE FROM agency WHERE id='$id'";
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
        "message" => "Agency deleted",
        "data" => null
    ]);
    exit;
} else if ($trans == "LIST_AGENCY") {

    $query = "SELECT id, code, name, details, created_at, updated_at FROM agency ORDER BY id DESC";

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
