<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$trans = $data['trans'] ?? '';

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

if ($trans == "LIST_INVENTORY") {

    $employee_id = $data['employee_id'] ?? null;

    $where = "WHERE 1=1";

    if (!empty($employee_id)) {
        $employee_id = (int)$employee_id;

        $where .= " AND f.id = (
            SELECT e.facility_id 
            FROM employee e 
            WHERE e.users_id = '$employee_id' 
            LIMIT 1
        )";
    }

    $sql = "
        SELECT 
            pi.id,
            pi.facility_id,
            pi.product_id,
            pi.current_stock,
            pi.reserved_stock,
            (pi.current_stock - pi.reserved_stock) AS available_stock,
            pi.cost_price,
            pi.selling_price,
            pi.batch_number,
            pi.expiry_date,
            pi.storage_location,
            pi.status,
            pi.created_at,
            pi.updated_at,

            f.name AS facility_name,
            f.branch_id AS cooperative_id,

            p.name AS product_name,
            p.sku,
            p.unit,
            (SELECT pi2.name FROM product_images pi2 
            WHERE pi2.product_id = p.id AND pi2.is_primary = 1 
            LIMIT 1) AS primary_image,
            pc.name AS category_name

        FROM product_inventory pi

        LEFT JOIN facility f 
            ON f.id = pi.facility_id

        LEFT JOIN product p 
            ON p.id = pi.product_id

        LEFT JOIN product_category pc 
            ON pc.id = p.category_id

        $where

        ORDER BY pi.id DESC
    ";

    $res = mysqli_query($conn, $sql);

    if (!$res) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => []
        ]);
        exit;
    }

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {

        $list[] = [
            "id" => (int)$row["id"],
            "facility_id" => (int)$row["facility_id"],
            "facility_name" => $row["facility_name"],
            "cooperative_id" => (int)$row["cooperative_id"],
            "product_id" => (int)$row["product_id"],
            "product_name" => $row["product_name"],
            "sku" => $row["sku"],
            "category_name" => $row["category_name"],
            "primary_image" => $row["primary_image"],
            "current_stock" => (float)$row["current_stock"],
            "reserved_stock" => (float)$row["reserved_stock"],
            "available_stock" => (float)$row["available_stock"],

            "cost_price" => (float)$row["cost_price"],
            "selling_price" => (float)$row["selling_price"],

            "batch_number" => $row["batch_number"],
            "expiry_date" => $row["expiry_date"],
            "storage_location" => $row["storage_location"],

            "status" => (int)$row["status"],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);

    exit;
} else if ($trans == "EDIT_STATUS") {

    $id     = $data['id'] ?? '';
    $status = $data['status'] ?? '';

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID required", "data" => null]);
        exit;
    }

    if ($status === '' || !in_array($status, ['0', '1'])) {
        echo json_encode(["code" => 1, "message" => "Invalid status", "data" => null]);
        exit;
    }

    $update = mysqli_query($conn, "
        UPDATE product_inventory SET
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

} else if ($trans == "ADD_INVENTORY" || $trans == "RECEIVE_INVENTORY") {

    $facility_id      = (int)($data['facility_id'] ?? 0);
    $product_id       = (int)($data['product_id'] ?? 0);
    $current_stock    = (float)($data['current_stock'] ?? 0);
    $reserved_stock   = (float)($data['reserved_stock'] ?? 0);
    $cost_price       = (float)($data['cost_price'] ?? 0);
    $selling_price    = (float)($data['selling_price'] ?? 0);
    $batch_number     = mysqli_real_escape_string($conn, trim($data['batch_number'] ?? ''));
    $reorder_level    = (int)($data['reorder_level'] ?? 0);
    $expiry_date      = trim($data['expiry_date'] ?? '');
    $expiry_date      = $expiry_date !== '' ? $expiry_date : null;
    $storage_location = trim($data['storage_location'] ?? '');
    $storage_location = $storage_location !== '' ? mysqli_real_escape_string($conn, $storage_location) : null;
    $status           = (int)($data['status'] ?? 1);
    $users_id         = (int)($data['users_id'] ?? 0);

    if (!$facility_id || !$product_id) {
        echo json_encode([
            "code" => 1,
            "message" => "Facility and Product are required",
            "data" => []
        ]);

        exit;
    }
    if ($reserved_stock > $current_stock) {
        echo json_encode([
            "code" => 1,
            "message" => "Reserved stock cannot exceed current stock",
            "data" => []
        ]);
        exit;
    }
    /*
    |--------------------------------------------------------------------------
    | SAME BATCH RESTOCK vs NEW BATCH
    |--------------------------------------------------------------------------
    | Same facility + product + batch number + price + expiry  => restock
    | the existing row (UPDATE current_stock + log).
    | Anything different                                        => new batch row.
    */
    $expiry_match = $expiry_date ? "AND expiry_date = '$expiry_date'" : "AND expiry_date IS NULL";

    $check = mysqli_query($conn, "
        SELECT id, current_stock
        FROM product_inventory
        WHERE facility_id = '$facility_id'
        AND product_id = '$product_id'
        AND batch_number = '$batch_number'
        AND cost_price = '$cost_price'
        AND selling_price = '$selling_price'
        $expiry_match
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {

        $existing    = mysqli_fetch_assoc($check);
        $existing_id = (int)$existing['id'];
        $new_balance = (float)$existing['current_stock'] + $current_stock;

        $update = mysqli_query($conn, "
            UPDATE product_inventory
            SET current_stock = '$new_balance',
                updated_at = NOW()
            WHERE id = '$existing_id'
        ");

        if (!$update) {
            echo json_encode([
                "code" => 1,
                "message" => mysqli_error($conn),
                "data" => []
            ]);
            exit;
        }

        mysqli_query($conn, "
            INSERT INTO product_inventory_logs
            (
                inventory_id,
                action_type,
                quantity_changed,
                new_balance,
                reference_id,
                users_id,
                remarks,
                created_at
            )
            VALUES
            (
                '$existing_id',
                '1',
                '$current_stock',
                '$new_balance',
                NULL,
                '$users_id',
                'Restocked into existing batch',
                NOW()
            )
        ");

        echo json_encode([
            "code" => 0,
            "message" => "Inventory restocked into existing batch",
            "data" => [
                "inventory_id" => $existing_id,
                "restocked" => true
            ]
        ]);

        exit;
    }

    $insert = mysqli_query($conn, "
        INSERT INTO product_inventory
        (
            facility_id,
            product_id,
            received_stock,
            current_stock,
            reserved_stock,
            reorder_level,
            cost_price,
            selling_price,
            batch_number,
            expiry_date,
            storage_location,
            status,
            created_at
        )
        VALUES
        (
            '$facility_id',
            '$product_id',
            '$current_stock',
            '$current_stock',
            '$reserved_stock',
            '$reorder_level',
            '$cost_price',
            '$selling_price',
            '$batch_number',
            " . ($expiry_date ? "'$expiry_date'" : "NULL") . ",
            " . ($storage_location ? "'$storage_location'" : "NULL") . ",
            '$status',
            NOW()
        )
    ");

    if (!$insert) {

        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => []
        ]);

        exit;
    }

    $inventory_id = mysqli_insert_id($conn);
    mysqli_query($conn, "
        INSERT INTO product_inventory_logs
        (
            inventory_id,
            action_type,
            quantity_changed,
            new_balance,
            reference_id,
            users_id,
            remarks,
            created_at
        )
        VALUES
        (
            '$inventory_id',
            '1',
            '$current_stock',
            '$current_stock',
            NULL,
            '$users_id',
            'Initial inventory stock',
            NOW()
        )
    ");

    echo json_encode([
        "code" => 0,
        "message" => "Inventory added successfully",
        "data" => [
            "inventory_id" => $inventory_id
        ]
    ]);

    exit;
} else if ($trans == "GET_PRODUCT_IMAGES") {

    $product_id = (int)($data['product_id'] ?? 0);

    if (!$product_id) {
        echo json_encode(["code" => 1, "message" => "product_id required", "data" => []]);
        exit;
    }

    $sql = "
        SELECT id, product_id, name, is_primary, status
        FROM product_images
        WHERE product_id = '$product_id'
        AND status = 1
        ORDER BY is_primary DESC, id ASC
    ";

    $res = mysqli_query($conn, $sql);

    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = [
            "id"         => (int)$row["id"],
            "product_id" => (int)$row["product_id"],
            "name"       => $row["name"],
            "is_primary" => (int)$row["is_primary"],
            "status"     => (int)$row["status"],
        ];
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    exit;

} else if ($trans == "UPDATE_INVENTORY") {

    $id = (int)($data['id'] ?? 0);

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID required", "data" => null]);
        exit;
    }

    $fetch = mysqli_query($conn, "
        SELECT * FROM product_inventory WHERE id = '$id' LIMIT 1
    ");

    if (!$fetch || mysqli_num_rows($fetch) == 0) {
        echo json_encode(["code" => 1, "message" => "Inventory not found", "data" => null]);
        exit;
    }

    $old = mysqli_fetch_assoc($fetch);

    $facility_id      = (int)($data['facility_id'] ?? $old['facility_id']);
    $product_id       = (int)($data['product_id'] ?? $old['product_id']);
    $current_stock    = (float)($data['current_stock'] ?? $old['current_stock']);
    $reserved_stock   = (float)($data['reserved_stock'] ?? $old['reserved_stock']);
    $cost_price       = (float)($data['cost_price'] ?? $old['cost_price']);
    $selling_price    = (float)($data['selling_price'] ?? $old['selling_price']);
    $batch_number     = isset($data['batch_number']) ? mysqli_real_escape_string($conn, trim($data['batch_number'])) : ($old['batch_number'] ?? null);
    $expiry_date      = isset($data['expiry_date']) ? trim($data['expiry_date']) : ($old['expiry_date'] ?? null);
    $expiry_date      = $expiry_date !== '' && $expiry_date !== null ? $expiry_date : null;
    $storage_location = isset($data['storage_location']) ? trim($data['storage_location']) : ($old['storage_location'] ?? null);
    $storage_location = $storage_location !== '' && $storage_location !== null ? mysqli_real_escape_string($conn, $storage_location) : null;
    $status           = (int)($data['status'] ?? $old['status']);
    $users_id         = (int)($data['users_id'] ?? 0);

    if (!$facility_id || !$product_id) {
        echo json_encode([
            "code" => 1,
            "message" => "Facility and Product are required",
            "data" => []
        ]);
        exit;
    }

    if ($reserved_stock > $current_stock) {
        echo json_encode([
            "code" => 1,
            "message" => "Reserved stock cannot exceed current stock",
            "data" => []
        ]);
        exit;
    }

    $old_current  = (float)$old['current_stock'];
    $old_reserved = (float)$old['reserved_stock'];
    $old_cost     = (float)$old['cost_price'];
    $old_selling  = (float)$old['selling_price'];

    /*
    |--------------------------------------------------------------------------
    | STOCK MOVEMENT AUDIT TRAIL
    |--------------------------------------------------------------------------
    */
    $stock_delta = $current_stock - $old_current;

    if ($stock_delta > 0) {
        mysqli_query($conn, "
            INSERT INTO product_inventory_logs
            (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
            VALUES
            ('$id', '1', '$stock_delta', '$current_stock', NULL, '$users_id', 'Restocked via inventory update', NOW())
        ");
    } else if ($stock_delta < 0) {
        mysqli_query($conn, "
            INSERT INTO product_inventory_logs
            (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
            VALUES
            ('$id', '4', '$stock_delta', '$current_stock', NULL, '$users_id', 'Stock adjusted via inventory update', NOW())
        ");
    }

    if ($reserved_stock != $old_reserved) {
        mysqli_query($conn, "
            INSERT INTO product_inventory_logs
            (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
            VALUES
            ('$id', '4', '0', '$current_stock', NULL, '$users_id', 'Reserved stock changed from $old_reserved to $reserved_stock', NOW())
        ");
    }

    /*
    |--------------------------------------------------------------------------
    | PRICE CHANGE HISTORY
    |--------------------------------------------------------------------------
    | No separate price-history table, so the audit trail lives in
    | product_inventory_logs (action_type 7 = Price Change).
    */
    $price_changes = [];

    if ($selling_price != $old_selling) {
        $price_changes[] = "Selling price changed from $old_selling to $selling_price";
    }

    if ($cost_price != $old_cost) {
        $price_changes[] = "Cost price changed from $old_cost to $cost_price";
    }

    if (!empty($price_changes)) {
        mysqli_query($conn, "
            INSERT INTO product_inventory_logs
            (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
            VALUES
            ('$id', '7', '0', '$current_stock', NULL, '$users_id', '" . mysqli_real_escape_string($conn, implode("; ", $price_changes)) . "', NOW())
        ");
    }

    /*
    |--------------------------------------------------------------------------
    | APPLY UPDATE
    |--------------------------------------------------------------------------
    */
    $expiry_sql  = $expiry_date ? "'$expiry_date'" : "NULL";
    $storage_sql = $storage_location ? "'$storage_location'" : "NULL";

    $update = mysqli_query($conn, "
        UPDATE product_inventory SET
            facility_id      = '$facility_id',
            product_id       = '$product_id',
            current_stock    = '$current_stock',
            reserved_stock   = '$reserved_stock',
            cost_price       = '$cost_price',
            selling_price    = '$selling_price',
            batch_number     = '$batch_number',
            expiry_date      = $expiry_sql,
            storage_location = $storage_sql,
            status           = '$status',
            updated_at       = NOW()
        WHERE id = '$id'
    ");

    if (!$update) {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Inventory updated successfully",
        "data" => ["id" => $id]
    ]);
    exit;

} else if ($trans == "GET_INVENTORY_LOGS") {

    $inventory_id = (int)($data['inventory_id'] ?? 0);
    $product_id   = (int)($data['product_id'] ?? 0);
    $facility_id  = (int)($data['facility_id'] ?? 0);
    $action_type  = (int)($data['transaction_type'] ?? 0);
    $limit        = (int)($data['limit'] ?? 200);

    $where = "WHERE 1=1";

    if ($inventory_id) {
        $where .= " AND pil.inventory_id = '$inventory_id'";
    }

    if ($product_id) {
        $where .= " AND pi.product_id = '$product_id'";
    }

    if ($facility_id) {
        $where .= " AND pi.facility_id = '$facility_id'";
    }

    if ($action_type) {
        $where .= " AND pil.action_type = '$action_type'";
    }

    $sql = "
        SELECT
            pil.id,
            pil.inventory_id,
            pil.action_type,
            pil.quantity_changed,
            pil.new_balance,
            pil.reference_id,
            pil.users_id,
            pil.remarks,
            pil.created_at,
            pi.product_id,
            pi.facility_id,
            p.name AS product_name,
            p.sku,
            f.name AS facility_name,
            u.username AS created_by_username,
            CONCAT(COALESCE(e.fname, ''), ' ', COALESCE(e.lname, '')) AS created_by_name
        FROM product_inventory_logs pil
        LEFT JOIN product_inventory pi ON pi.id = pil.inventory_id
        LEFT JOIN product p ON p.id = pi.product_id
        LEFT JOIN facility f ON f.id = pi.facility_id
        LEFT JOIN users u ON u.id = pil.users_id
        LEFT JOIN employee e ON e.users_id = pil.users_id
        $where
        ORDER BY pil.id DESC
        LIMIT $limit
    ";

    $res = mysqli_query($conn, $sql);

    if (!$res) {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => []]);
        exit;
    }

    $action_labels = [
        1 => "Receive",
        2 => "Sale",
        3 => "Return",
        4 => "Adjustment",
        5 => "Expired",
        6 => "Damaged",
        7 => "Price Change",
        8 => "Reserve",
        9 => "Release",
        10 => "Transfer"
    ];

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {

        $action_type = (int)$row['action_type'];

        $list[] = [
            "id" => (int)$row['id'],
            "inventory_id" => (int)$row['inventory_id'],
            "product_id" => (int)$row['product_id'],
            "product_name" => $row['product_name'],
            "sku" => $row['sku'],
            "facility_id" => (int)$row['facility_id'],
            "facility_name" => $row['facility_name'],
            "action_type" => $action_type,
            "action_label" => $action_labels[$action_type] ?? 'Unknown',
            "quantity_changed" => (float)$row['quantity_changed'],
            "new_balance" => (float)$row['new_balance'],
            "reference_id" => $row['reference_id'],
            "users_id" => (int)$row['users_id'],
            "created_by" => $row['created_by_name'] ?: ($row['created_by_username'] ?? ''),
            "remarks" => $row['remarks'],
            "created_at" => $row['created_at']
        ];
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    exit;

} else if ($trans == "GET_INVENTORY_DASHBOARD") {

    $total_products = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) AS total FROM product WHERE status = 1
    "))['total'] ?? 0;

    $total_facilities = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) AS total FROM facility WHERE status = 1
    "))['total'] ?? 0;

    $total_inventory = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COALESCE(SUM(current_stock), 0) AS total
        FROM product_inventory WHERE status = 1
    "))['total'] ?? 0;

    $low_stock = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM product_inventory
        WHERE status = 1 AND current_stock > 0 AND current_stock <= reorder_level
    "))['total'] ?? 0;

    $expiring = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM product_inventory
        WHERE status = 1
        AND expiry_date IS NOT NULL
        AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
    "))['total'] ?? 0;

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "total_products" => (int)$total_products,
            "total_facilities" => (int)$total_facilities,
            "total_inventory" => (int)$total_inventory,
            "low_stock" => (int)$low_stock,
            "expiring_inventory" => (int)$expiring
        ]
    ]);
    exit;

} else if ($trans == "RESERVE_INVENTORY") {

    $inventory_id = (int)($data['inventory_id'] ?? 0);
    $qty          = (float)($data['quantity'] ?? 0);
    $users_id     = (int)($data['users_id'] ?? 0);
    $remarks      = trim($data['remarks'] ?? '');
    $remarks      = $remarks !== '' ? mysqli_real_escape_string($conn, $remarks) : 'Stock reserved';

    if (!$inventory_id || $qty <= 0) {
        echo json_encode(["code" => 1, "message" => "Inventory and quantity are required", "data" => null]);
        exit;
    }

    $fetch = mysqli_query($conn, "
        SELECT * FROM product_inventory WHERE id = '$inventory_id' AND status = 1 LIMIT 1
    ");

    if (!$fetch || mysqli_num_rows($fetch) == 0) {
        echo json_encode(["code" => 1, "message" => "Inventory batch not found", "data" => null]);
        exit;
    }

    $inv       = mysqli_fetch_assoc($fetch);
    $current   = (float)$inv['current_stock'];
    $reserved  = (float)$inv['reserved_stock'];
    $available = $current - $reserved;

    if ($qty > $available) {
        echo json_encode([
            "code" => 1,
            "message" => "Insufficient available stock (available: $available)",
            "data" => null
        ]);
        exit;
    }

    $new_reserved = $reserved + $qty;

    mysqli_begin_transaction($conn);

    $update = mysqli_query($conn, "
        UPDATE product_inventory
        SET reserved_stock = '$new_reserved', updated_at = NOW()
        WHERE id = '$inventory_id'
    ");

    if (!$update) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    $log = mysqli_query($conn, "
        INSERT INTO product_inventory_logs
        (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
        VALUES
        ('$inventory_id', '8', '$qty', '$current', NULL, '$users_id', '$remarks', NOW())
    ");

    if (!$log) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    mysqli_commit($conn);

    echo json_encode([
        "code" => 0,
        "message" => "Stock reserved successfully",
        "data" => ["reserved_stock" => $new_reserved]
    ]);
    exit;

} else if ($trans == "RELEASE_RESERVATION") {

    $inventory_id = (int)($data['inventory_id'] ?? 0);
    $qty          = (float)($data['quantity'] ?? 0);
    $users_id     = (int)($data['users_id'] ?? 0);
    $remarks      = trim($data['remarks'] ?? '');
    $remarks      = $remarks !== '' ? mysqli_real_escape_string($conn, $remarks) : 'Reservation released';

    if (!$inventory_id || $qty <= 0) {
        echo json_encode(["code" => 1, "message" => "Inventory and quantity are required", "data" => null]);
        exit;
    }

    $fetch = mysqli_query($conn, "
        SELECT * FROM product_inventory WHERE id = '$inventory_id' AND status = 1 LIMIT 1
    ");

    if (!$fetch || mysqli_num_rows($fetch) == 0) {
        echo json_encode(["code" => 1, "message" => "Inventory batch not found", "data" => null]);
        exit;
    }

    $inv       = mysqli_fetch_assoc($fetch);
    $current   = (float)$inv['current_stock'];
    $reserved  = (float)$inv['reserved_stock'];

    if ($qty > $reserved) {
        echo json_encode([
            "code" => 1,
            "message" => "Cannot release more than the reserved quantity (reserved: $reserved)",
            "data" => null
        ]);
        exit;
    }

    $new_reserved = $reserved - $qty;

    mysqli_begin_transaction($conn);

    $update = mysqli_query($conn, "
        UPDATE product_inventory
        SET reserved_stock = '$new_reserved', updated_at = NOW()
        WHERE id = '$inventory_id'
    ");

    if (!$update) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    $log = mysqli_query($conn, "
        INSERT INTO product_inventory_logs
        (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
        VALUES
        ('$inventory_id', '9', '$qty', '$current', NULL, '$users_id', '$remarks', NOW())
    ");

    if (!$log) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    mysqli_commit($conn);

    echo json_encode([
        "code" => 0,
        "message" => "Reservation released successfully",
        "data" => ["reserved_stock" => $new_reserved]
    ]);
    exit;

} else if ($trans == "ADJUST_INVENTORY") {

    $inventory_id = (int)($data['inventory_id'] ?? 0);
    $new_stock    = (float)($data['new_stock'] ?? 0);
    $users_id     = (int)($data['users_id'] ?? 0);
    $remarks      = trim($data['remarks'] ?? '');
    $remarks      = $remarks !== '' ? mysqli_real_escape_string($conn, $remarks) : 'Manual stock adjustment';

    if (!$inventory_id || $new_stock < 0) {
        echo json_encode(["code" => 1, "message" => "Inventory and new stock are required", "data" => null]);
        exit;
    }

    $fetch = mysqli_query($conn, "
        SELECT * FROM product_inventory WHERE id = '$inventory_id' AND status = 1 LIMIT 1
    ");

    if (!$fetch || mysqli_num_rows($fetch) == 0) {
        echo json_encode(["code" => 1, "message" => "Inventory batch not found", "data" => null]);
        exit;
    }

    $inv          = mysqli_fetch_assoc($fetch);
    $old_current  = (float)$inv['current_stock'];
    $reserved     = (float)$inv['reserved_stock'];

    if ($new_stock < $reserved) {
        echo json_encode([
            "code" => 1,
            "message" => "Current stock cannot be less than reserved stock ($reserved)",
            "data" => null
        ]);
        exit;
    }

    $delta = $new_stock - $old_current;
    $adj_remarks = mysqli_real_escape_string($conn, "Adjusted from $old_current to $new_stock. " . $remarks);

    mysqli_begin_transaction($conn);

    $update = mysqli_query($conn, "
        UPDATE product_inventory
        SET current_stock = '$new_stock', updated_at = NOW()
        WHERE id = '$inventory_id'
    ");

    if (!$update) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    $log = mysqli_query($conn, "
        INSERT INTO product_inventory_logs
        (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
        VALUES
        ('$inventory_id', '4', '$delta', '$new_stock', NULL, '$users_id', '$adj_remarks', NOW())
    ");

    if (!$log) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    mysqli_commit($conn);

    echo json_encode([
        "code" => 0,
        "message" => "Inventory adjusted successfully",
        "data" => ["current_stock" => $new_stock]
    ]);
    exit;

} else if ($trans == "TRANSFER_INVENTORY") {

    $source_id              = (int)($data['source_inventory_id'] ?? 0);
    $destination_facility   = (int)($data['destination_facility_id'] ?? 0);
    $qty                    = (float)($data['quantity'] ?? 0);
    $users_id               = (int)($data['users_id'] ?? 0);
    $remarks                = trim($data['remarks'] ?? '');
    $remarks                = $remarks !== '' ? mysqli_real_escape_string($conn, $remarks) : 'Stock transfer';

    if (!$source_id || !$destination_facility || $qty <= 0) {
        echo json_encode(["code" => 1, "message" => "Source, destination and quantity are required", "data" => null]);
        exit;
    }

    $fetch = mysqli_query($conn, "
        SELECT * FROM product_inventory WHERE id = '$source_id' AND status = 1 LIMIT 1
    ");

    if (!$fetch || mysqli_num_rows($fetch) == 0) {
        echo json_encode(["code" => 1, "message" => "Source inventory batch not found", "data" => null]);
        exit;
    }

    $inv          = mysqli_fetch_assoc($fetch);
    $current      = (float)$inv['current_stock'];
    $reserved     = (float)$inv['reserved_stock'];
    $available    = $current - $reserved;

    if ((int)$inv['facility_id'] === $destination_facility) {
        echo json_encode(["code" => 1, "message" => "Source and destination facilities must be different", "data" => null]);
        exit;
    }

    if ($qty > $available) {
        echo json_encode([
            "code" => 1,
            "message" => "Insufficient available stock (available: $available)",
            "data" => null
        ]);
        exit;
    }

    $reference_id = time();

    mysqli_begin_transaction($conn);

    // ── 1. Deduct from source batch ──
    $new_source_stock = $current - $qty;

    $update_source = mysqli_query($conn, "
        UPDATE product_inventory
        SET current_stock = '$new_source_stock', updated_at = NOW()
        WHERE id = '$source_id'
    ");

    if (!$update_source) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    $out_remarks = mysqli_real_escape_string($conn, "TRANSFER OUT to facility $destination_facility. " . $remarks);

    $log_out = mysqli_query($conn, "
        INSERT INTO product_inventory_logs
        (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
        VALUES
        ('$source_id', '10', '-$qty', '$new_source_stock', '$reference_id', '$users_id', '$out_remarks', NOW())
    ");

    if (!$log_out) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    // ── 2. Create destination batch ──
    $dest_batch     = mysqli_real_escape_string($conn, ($inv['batch_number'] ?? '') . "-T" . date("ymdHis"));
    $dest_expiry    = $inv['expiry_date'] ? "'{$inv['expiry_date']}'" : "NULL";
    $dest_storage   = !empty($inv['storage_location']) ? "'{$inv['storage_location']}'" : "NULL";
    $dest_cost      = (float)$inv['cost_price'];
    $dest_selling   = (float)$inv['selling_price'];
    $dest_reorder   = (int)$inv['reorder_level'];

    $insert_dest = mysqli_query($conn, "
        INSERT INTO product_inventory
        (
            facility_id, product_id, received_stock, current_stock, reserved_stock,
            reorder_level, cost_price, selling_price, batch_number, expiry_date,
            storage_location, status, created_at
        )
        VALUES
        (
            '$destination_facility', '{$inv['product_id']}', '$qty', '$qty', '0',
            '$dest_reorder', '$dest_cost', '$dest_selling', '$dest_batch', $dest_expiry,
            $dest_storage, '1', NOW()
        )
    ");

    if (!$insert_dest) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    $dest_id = mysqli_insert_id($conn);

    $in_remarks = mysqli_real_escape_string($conn, "TRANSFER IN from source batch $source_id. " . $remarks);

    $log_in = mysqli_query($conn, "
        INSERT INTO product_inventory_logs
        (inventory_id, action_type, quantity_changed, new_balance, reference_id, users_id, remarks, created_at)
        VALUES
        ('$dest_id', '10', '$qty', '$qty', '$reference_id', '$users_id', '$in_remarks', NOW())
    ");

    if (!$log_in) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    mysqli_commit($conn);

    echo json_encode([
        "code" => 0,
        "message" => "Inventory transferred successfully",
        "data" => [
            "source_inventory_id" => $source_id,
            "destination_inventory_id" => $dest_id,
            "reference_no" => $reference_id
        ]
    ]);
    exit;

} echo json_encode([
    "code" => 1,
    "message" => "Invalid transaction",
    "data" => []
]);
