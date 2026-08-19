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

/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
| 1 = Active
| 2 = Closed
| 3 = Paused
|--------------------------------------------------------------------------
*/

function statusLabel($status)
{
    if ($status == 1) return "Active";
    if ($status == 2) return "Closed";
    if ($status == 3) return "Paused";
    return "Unknown";
}

if ($trans == "ADD_PROGRAM") {

    $name             = $data['name'] ?? '';
    $agency_id        = $data['agency_id'] ?? '';
    $total_budget     = $data['total_budget'] ?? 0;
    $remaining_budget = $data['remaining_budget'] ?? 0;
    $start_date       = $data['start_date'] ?? '';
    $end_date         = $data['end_date'] ?? '';
    $asset_type       = $data['asset_type'] ?? 0;
    if ($data['product_id'] === null) {
        $product_id = "NULL";
    } else {
        $product_id = (int)$data['product_id'];
    }

    // validate agency
    $checkAgency = mysqli_query($conn, "SELECT id FROM agency WHERE id='$agency_id'");
    if (mysqli_num_rows($checkAgency) == 0) {
        echo json_encode([
            "code" => 1,
            "message" => "Invalid agency_id",
            "data" => null
        ]);
        exit;
    }

    // 🔥 AUTO-GENERATE PROGRAM CODE
    $datePart = date("Ymd");

    $countQuery = mysqli_query($conn, "
        SELECT COUNT(*) as total 
        FROM program 
        WHERE DATE(created_at) = CURDATE()
    ");

    $row = mysqli_fetch_assoc($countQuery);
    $next = $row['total'] + 1;

    $code = "PRG-" . $datePart . "-" . str_pad($next, 4, "0", STR_PAD_LEFT);

    // insert
    $insert = mysqli_query($conn, "
        INSERT INTO program 
        (code, name, agency_id, product_id, total_budget, remaining_budget, start_date, end_date, asset_type, status, created_at, updated_at)
        VALUES 
        ('$code','$name','$agency_id',$product_id,'$total_budget','$total_budget','$start_date','$end_date','$asset_type', 1,NOW(),NOW())
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
        "message" => "Program added",
        "data" => ["id" => mysqli_insert_id($conn), "code" => $code]
    ]);
    exit;
} else if ($trans == "EDIT_PROGRAM") {

    $id               = $data['id'] ?? '';
    $code             = $data['code'] ?? '';
    $name             = $data['name'] ?? '';
    $agency_id        = $data['agency_id'] ?? '';
    $total_budget     = $data['total_budget'] ?? 0;
    $remaining_budget = $data['remaining_budget'] ?? null;
    $start_date       = $data['start_date'] ?? '';
    $end_date         = $data['end_date'] ?? '';
    $status           = $data['status'] ?? 1;
    $asset_type       = $data['asset_type'] ?? 0;
    $product_id       = !empty($data['product_id']) ? $data['product_id'] : null;
    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    // Preserve the current remaining budget when not explicitly provided.
    if ($remaining_budget === null) {
        $currentRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT remaining_budget FROM program WHERE id='$id' LIMIT 1"));
        $remaining_budget = $currentRow['remaining_budget'] ?? 0;
    }

    $agency_id_sql  = $agency_id !== '' ? "'$agency_id'" : "NULL";
    $product_id_sql = $product_id ? $product_id : "NULL";
    $start_sql      = $start_date !== '' ? "'$start_date'" : "NULL";
    $end_sql        = $end_date !== '' ? "'$end_date'" : "NULL";

    $update = mysqli_query($conn, "
        UPDATE program SET 
        code='$code',
        name='$name',
        agency_id=$agency_id_sql,
        product_id=$product_id_sql,
        total_budget='$total_budget',
        remaining_budget='$remaining_budget',
        start_date=$start_sql,
        end_date=$end_sql,
        asset_type='$asset_type',
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

    echo json_encode([
        "code" => 0,
        "message" => "Program updated",
        "data" => null
    ]);
    exit;
} else if ($trans == "UPDATE_STATUS_PROGRAM") {

    $id     = $data['id'] ?? '';
    $status = $data['status'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $update = mysqli_query($conn, "
        UPDATE program SET 
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

    echo json_encode([
        "code" => 0,
        "message" => "Status updated",
        "data" => null
    ]);
    exit;
} else if ($trans == "DELETE_PROGRAM") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $delete = mysqli_query($conn, "DELETE FROM program WHERE id='$id'");

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
        "message" => "Program deleted",
        "data" => null
    ]);
    exit;
} else if ($trans == "LIST_PROGRAM") {

    $query = "
        SELECT 
            p.*,
            a.name AS agency_name,
            pr.name AS product_name
        FROM program p
        LEFT JOIN agency a ON a.id = p.agency_id
        LEFT JOIN product pr ON pr.id = p.product_id
        ORDER BY p.id DESC
    ";

    $result = mysqli_query($conn, $query);

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $row['status_text'] = statusLabel($row['status']);
        $data[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $data
    ]);
    exit;
}

/* =====================================================
   INVALID
===================================================== */ else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
    exit;
}
