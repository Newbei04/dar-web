<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$trans = $_GET['trans'] ?? ($data['trans'] ?? '');

if (!$conn) {
    echo json_encode(["code" => 1, "message" => "DB failed", "data" => null]);
    exit;
}

if (empty($trans)) {
    echo json_encode(["code" => 1, "message" => "Transaction required", "data" => null]);
    exit;
}

if ($trans == "ADD_ALLOCATION") {

    $subsidy_type        = $data['subsidy_type'] ?? '';
    $program_id  = $data['program_id'] ?? '';
    $branch_id = $data['branch_id'] ?? null;

    $product_id  = !empty($data['product_id']) ? $data['product_id'] : null;

    $allocated_budget = $subsidy_type == "0" ? $data['allocation_budget'] ?? 0 : $data['allocated_quantity'] ?? 0;
    $unit_subsidy_value    = $data['unit_subsidy_value'] ?? 0;
    $max_per_beneficiary   = $data['max_per_beneficiary'] ?? 0;

    $checkProgram = mysqli_query($conn, "SELECT id FROM program WHERE id='$program_id'");
    if (mysqli_num_rows($checkProgram) == 0) {
        echo json_encode(["code" => 1, "message" => "Invalid program", "data" => null]);
        exit;
    }

    if (!empty($branch_id)) {
        $checkBranch = mysqli_query($conn, "SELECT id FROM branch WHERE id='$branch_id'");
        if (mysqli_num_rows($checkBranch) == 0) {
            echo json_encode(["code" => 1, "message" => "Invalid branch", "data" => null]);
            exit;
        }
    }

    if ($subsidy_type == 0) {

        if ($allocated_budget <= 0) {
            echo json_encode(["code" => 1, "message" => "Invalid budget", "data" => null]);
            exit;
        }

        $insert = mysqli_query($conn, "
            INSERT INTO program_allocation
            (program_id, branch_id, allocated_budget, reserved_budget, distributed_budget, max_per_beneficiary, status, created_at, updated_at)
            VALUES
            ('$program_id','$branch_id','$allocated_budget','$allocated_budget',0,'$max_per_beneficiary','1',NOW(),NOW())
        ");

        if (!$insert) {
            echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
            exit;
        }

        echo json_encode([
            "code" => 0,
            "message" => "Money allocated to wallet",
            "data" => ["id" => mysqli_insert_id($conn)]
        ]);
        exit;
    }

    if ($subsidy_type == 1) {

        if (empty($product_id)) {
            echo json_encode(["code" => 1, "message" => "Product required", "data" => null]);
            exit;
        }

        $checkProduct = mysqli_query($conn, "SELECT id FROM product WHERE id='$product_id'");
        if (mysqli_num_rows($checkProduct) == 0) {
            echo json_encode(["code" => 1, "message" => "Invalid product", "data" => null]);
            exit;
        }

        $insert = mysqli_query($conn, "
            INSERT INTO program_allocation
            (program_id, branch_id, allocated_budget, reserved_budget, distributed_budget, unit_subsidy_value, max_per_beneficiary, status, created_at, updated_at)
            VALUES
            ('$program_id','$branch_id','$allocated_budget', '$allocated_budget', 0,'$unit_subsidy_value','$max_per_beneficiary','1',NOW(),NOW())
        ");

        if (!$insert) {
            echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
            exit;
        }

        echo json_encode([
            "code" => 0,
            "message" => "Product allocated",
            "data" => ["id" => mysqli_insert_id($conn)]
        ]);
        exit;
    }

    echo json_encode(["code" => 1, "message" => "Invalid allocation type", "data" => null]);
    exit;
} else if ($trans == "LIST_ALLOCATION") {

    $status = $data['status'] ?? '';

    $where = "";

    if ($status !== '' && strtolower($status) !== 'all') {
        $where = "WHERE pa.status='$status'";
    }

    $query = "
        SELECT 
            pa.*,
            p.name AS program_name,
            pr.name AS product_name,
            b.name AS branch_name
        FROM program_allocation pa
        LEFT JOIN program p ON p.id = pa.program_id
        LEFT JOIN product pr ON pr.id = p.product_id
        LEFT JOIN branch b ON b.id = pa.branch_id
        $where
        ORDER BY pa.id DESC;
    ";

    $result = mysqli_query($conn, $query);

    $list = [];

    while ($row = mysqli_fetch_assoc($result)) {

        if (!empty($row['product_id'])) {
            $row['allocation_type'] = "PRODUCT";
        } else {
            $row['allocation_type'] = "MONEY";
        }

        if ($row['status'] == 0) {
            $row['status_text'] = "Pending";
        } else if ($row['status'] == 1) {
            $row['status_text'] = "In-progress";
        } else if ($row['status'] == 2) {
            $row['status_text'] = "Processed";
        } else {
            $row['status_text'] = "Unknown";
        }

        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
    exit;
} else if ($trans == "GET_ALLOCATION") {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID required", "data" => null]);
        exit;
    }

    $query = "
        SELECT 
            pa.*,
            p.name AS program_name,
            p.product_id AS product_id,
            COALESCE(pr.name, 'Cash') AS product_name,
            b.name AS branch_name
        FROM program_allocation pa
        LEFT JOIN program p ON p.id = pa.program_id
        LEFT JOIN product pr ON pr.id = p.product_id
        LEFT JOIN branch b ON b.id = pa.branch_id
        WHERE pa.id = '$id';
    ";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo json_encode(["code" => 1, "message" => "Not found", "data" => null]);
        exit;
    }

    if (!empty($row['product_id'])) {
        $row['allocation_type'] = "PRODUCT";
    } else {
        $row['allocation_type'] = "MONEY";
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $row]);
    exit;
} else if ($trans == "EDIT_ALLOCATION") {

    $id = $data['id'] ?? '';

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID required", "data" => null]);
        exit;
    }

    $allocated_budget    = $data['allocated_budget'] ?? 0;
    $unit_subsidy_value  = $data['unit_subsidy_value'] ?? 0;
    $max_per_beneficiary = $data['max_per_beneficiary'] ?? 0;

    $check = mysqli_query($conn, "SELECT distributed_budget FROM program_allocation WHERE id='$id'");
    $existing = mysqli_fetch_assoc($check);
    $distributed = $existing['distributed_budget'] ?? 0;
    $reserved_budget = $allocated_budget - $distributed;

    $update = mysqli_query($conn, "
        UPDATE program_allocation SET
        allocated_budget='$allocated_budget',
        reserved_budget='$reserved_budget',
        unit_subsidy_value='$unit_subsidy_value',
        max_per_beneficiary='$max_per_beneficiary',
        updated_at=NOW()
        WHERE id='$id'
    ");

    if (!$update) {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    echo json_encode(["code" => 0, "message" => "Updated", "data" => null]);
    exit;
} else if ($trans == "EDIT_STATUS") {

    $id     = $data['id'] ?? '';
    $status = $data['status'] ?? '';

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID required", "data" => null]);
        exit;
    }

    if ($status === '' || !in_array($status, ['0', '1', '2'])) {
        echo json_encode(["code" => 1, "message" => "Invalid status", "data" => null]);
        exit;
    }

    $update = mysqli_query($conn, "
        UPDATE program_allocation SET
        status='$status',
        updated_at=NOW()
        WHERE id='$id'
    ");

    if (!$update) {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    echo json_encode(["code" => 0, "message" => "Status updated", "data" => null]);
    exit;
} else {
    echo json_encode(["code" => 1, "message" => "Invalid transaction", "data" => null]);
    exit;
}
