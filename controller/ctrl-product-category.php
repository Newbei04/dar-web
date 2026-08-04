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

/* =========================
   ADD PRODUCT CATEGORY
========================= */
if ($trans === "ADD_PRODUCT_CATEGORY") {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "code" => 1,
            "message" => "Invalid request method",
            "data" => null
        ]);
        exit;
    }

    $name = trim($data['name'] ?? '');
    $details = trim($data['details'] ?? '');
    $status = $data['status'] ?? 1;

    if ($name === '') {
        echo json_encode([
            "code" => 1,
            "message" => "Name is required",
            "data" => null
        ]);
        exit;
    }

    $sql = mysqli_query(
        $conn,
        "INSERT INTO product_category (name, details, status, created_at)
         VALUES ('$name', '$details', '$status', NOW())"
    );

    if (!$sql) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Category created",
        "data" => ["id" => mysqli_insert_id($conn)]
    ]);

    exit;
}

/* =========================
   UPDATE PRODUCT CATEGORY
========================= */ else if ($trans === "UPDATE_PRODUCT_CATEGORY") {

    $id = $data['id'] ?? '';

    if ($id === '') {
        echo json_encode([
            "code" => 1,
            "message" => "ID required",
            "data" => null
        ]);
        exit;
    }

    $name = trim($data['name'] ?? '');
    $details = trim($data['details'] ?? '');
    $status = $data['status'] ?? 1;

    $sql = mysqli_query(
        $conn,
        "UPDATE product_category 
         SET name='$name',
             details='$details',
             status='$status',
             updated_at=NOW()
         WHERE id='$id'"
    );

    if (!$sql) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Category updated",
        "data" => null
    ]);

    exit;
}

/* =========================
   LIST PRODUCT CATEGORY
========================= */ else if ($trans === "LIST_PRODUCT_CATEGORY") {

    $sql = mysqli_query(
        $conn,
        "SELECT id, name, details, status, created_at, updated_at 
         FROM product_category 
         ORDER BY id DESC"
    );

    $list = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);

    exit;
}

/* =====================================================
   DELETE PRODUCT CATEGORY (soft delete)
==================================================== */ else if ($trans === "DELETE_PRODUCT_CATEGORY") {

    $id = $data['id'] ?? '';

    if ($id === '') {
        echo json_encode([
            "code" => 1,
            "message" => "ID required",
            "data" => null
        ]);
        exit;
    }

    $used = mysqli_query($conn, "
        SELECT COUNT(*) AS total FROM product WHERE category_id = '$id' AND status = 1
    ");
    $usedCount = mysqli_fetch_assoc($used)['total'] ?? 0;

    if ($usedCount > 0) {
        echo json_encode([
            "code" => 1,
            "message" => "Cannot delete. $usedCount active product(s) are using this category.",
            "data" => null
        ]);
        exit;
    }

    $update = mysqli_query($conn, "
        UPDATE product_category
        SET status = '0', updated_at = NOW()
        WHERE id = '$id'
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
        "message" => "Category deleted",
        "data" => null
    ]);

    exit;
}

/* =====================================================
   INVALID TRANSACTION
==================================================== */ else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
    exit;
}
