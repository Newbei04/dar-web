<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';
include_once __DIR__ . '/../model/inventory.php';

$data  = json_decode(file_get_contents("php://input"), true);
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

/* ============================================================================
   SET / UPDATE FACILITY PRICE
   Upserts product_facility_price and ALWAYS inserts a product_price_history row.
   Never overwrites historical prices.
============================================================================ */
if ($trans === "SET_FACILITY_PRICE") {

    $product_id     = (int)($data['product_id'] ?? 0);
    $facility_id    = (int)($data['facility_id'] ?? 0);
    $selling_price  = (float)($data['selling_price'] ?? 0);
    $minimum_price  = isset($data['minimum_price']) && $data['minimum_price'] !== '' ? (float)$data['minimum_price'] : null;
    $maximum_price  = isset($data['maximum_price']) && $data['maximum_price'] !== '' ? (float)$data['maximum_price'] : null;
    $created_by     = (int)($data['created_by'] ?? 0);
    $remarks        = trim($data['remarks'] ?? '');
    $remarks        = $remarks !== '' ? mysqli_real_escape_string($conn, $remarks) : null;
    $effective_date = trim($data['effective_date'] ?? '');
    $effective_date = $effective_date !== '' ? $effective_date : date('Y-m-d');

    if (!$product_id || !$facility_id) {
        echo json_encode(["code" => 1, "message" => "Product and Facility are required", "data" => null]);
        exit;
    }

    if ($selling_price < 0) {
        echo json_encode(["code" => 1, "message" => "Selling price is invalid", "data" => null]);
        exit;
    }

    // Optional price-band validation
    if ($minimum_price !== null && $selling_price < $minimum_price) {
        echo json_encode(["code" => 1, "message" => "Selling price cannot be below the minimum price", "data" => null]);
        exit;
    }
    if ($maximum_price !== null && $selling_price > $maximum_price) {
        echo json_encode(["code" => 1, "message" => "Selling price cannot exceed the maximum price", "data" => null]);
        exit;
    }

    mysqli_begin_transaction($conn);

    // Fetch current price row (if any)
    $check = mysqli_query($conn, "
        SELECT id, selling_price
        FROM product_facility_price
        WHERE product_id = '$product_id' AND facility_id = '$facility_id'
        LIMIT 1
    ");

    $existing = mysqli_fetch_assoc($check);
    $old_price = $existing ? (float)$existing['selling_price'] : 0;

    if ($existing) {
        $update = mysqli_query($conn, "
            UPDATE product_facility_price
            SET selling_price = '$selling_price',
                minimum_price = " . ($minimum_price !== null ? "'$minimum_price'" : "NULL") . ",
                maximum_price = " . ($maximum_price !== null ? "'$maximum_price'" : "NULL") . ",
                status = '1',
                updated_at = NOW()
            WHERE id = '{$existing['id']}'
        ");

        if (!$update) {
            mysqli_rollback($conn);
            echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
            exit;
        }
    } else {
        $insert = mysqli_query($conn, "
            INSERT INTO product_facility_price
            (product_id, facility_id, selling_price, minimum_price, maximum_price, status, created_at, updated_at)
            VALUES
            ('$product_id', '$facility_id', '$selling_price',
             " . ($minimum_price !== null ? "'$minimum_price'" : "NULL") . ",
             " . ($maximum_price !== null ? "'$maximum_price'" : "NULL") . ",
             '1', NOW(), NOW())
        ");

        if (!$insert) {
            mysqli_rollback($conn);
            echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
            exit;
        }
    }

    // ── Append-only price history ──
    $history = mysqli_query($conn, "
        INSERT INTO product_price_history
        (product_id, facility_id, price_type, old_price, new_price, remarks, created_by, effective_date, created_at)
        VALUES
        ('$product_id', '$facility_id', 'SELLING', '$old_price', '$selling_price',
         " . ($remarks ? "'$remarks'" : "NULL") . ",
         " . ($created_by ? "'$created_by'" : "NULL") . ",
         '$effective_date', NOW())
    ");

    if (!$history) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    // ── Sync inventory display cache so batch rows show the same price ──
    $sync = mysqli_query($conn, "
        UPDATE product_inventory
        SET selling_price = '$selling_price', updated_at = NOW()
        WHERE product_id = '$product_id' AND facility_id = '$facility_id'
    ");

    if (!$sync) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => null]);
        exit;
    }

    mysqli_commit($conn);

    echo json_encode([
        "code" => 0,
        "message" => $existing ? "Selling price updated" : "Selling price set",
        "data" => [
            "old_price" => $old_price,
            "new_price" => $selling_price
        ]
    ]);
    exit;
}

/* ============================================================================
   LIST FACILITY PRICES
   Optionally filter by product_id and/or facility_id.
   Returns one row per (product, facility) with current price band.
============================================================================ */
if ($trans === "LIST_FACILITY_PRICES") {

    $product_id  = (int)($data['product_id'] ?? 0);
    $facility_id = (int)($data['facility_id'] ?? 0);

    $where = "WHERE 1=1";

    if ($product_id) {
        $where .= " AND pfp.product_id = '$product_id'";
    }

    if ($facility_id) {
        $where .= " AND pfp.facility_id = '$facility_id'";
    }

    $sql = "
        SELECT
            pfp.id,
            pfp.product_id,
            pfp.facility_id,
            pfp.selling_price,
            pfp.minimum_price,
            pfp.maximum_price,
            pfp.status,
            pfp.updated_at,
            p.name AS product_name,
            p.sku,
            f.name AS facility_name
        FROM product_facility_price pfp
        LEFT JOIN product p ON p.id = pfp.product_id
        LEFT JOIN facility f ON f.id = pfp.facility_id
        $where
        ORDER BY p.name ASC, f.name ASC
    ";

    $res = mysqli_query($conn, $sql);

    if (!$res) {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => []]);
        exit;
    }

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = [
            "id"             => (int)$row['id'],
            "product_id"     => (int)$row['product_id'],
            "product_name"   => $row['product_name'],
            "sku"            => $row['sku'],
            "facility_id"    => (int)$row['facility_id'],
            "facility_name"  => $row['facility_name'],
            "selling_price"  => (float)$row['selling_price'],
            "minimum_price"  => $row['minimum_price'] !== null ? (float)$row['minimum_price'] : null,
            "maximum_price"  => $row['maximum_price'] !== null ? (float)$row['maximum_price'] : null,
            "status"         => (int)$row['status'],
            "updated_at"     => $row['updated_at']
        ];
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
    exit;
}

/* ============================================================================
   GET PRICE HISTORY
   Append-only audit of selling price changes.
============================================================================ */
if ($trans === "GET_PRICE_HISTORY") {

    $product_id  = (int)($data['product_id'] ?? 0);
    $facility_id = (int)($data['facility_id'] ?? 0);
    $limit       = (int)($data['limit'] ?? 200);

    $where = "WHERE 1=1";

    if ($product_id) {
        $where .= " AND pph.product_id = '$product_id'";
    }

    if ($facility_id) {
        $where .= " AND pph.facility_id = '$facility_id'";
    }

    $sql = "
        SELECT
            pph.id,
            pph.product_id,
            pph.facility_id,
            pph.price_type,
            pph.old_price,
            pph.new_price,
            pph.remarks,
            pph.created_by,
            pph.effective_date,
            pph.created_at,
            p.name AS product_name,
            p.sku,
            f.name AS facility_name,
            u.username AS created_by_username,
            CONCAT(COALESCE(e.fname, ''), ' ', COALESCE(e.lname, '')) AS created_by_name
        FROM product_price_history pph
        LEFT JOIN product p ON p.id = pph.product_id
        LEFT JOIN facility f ON f.id = pph.facility_id
        LEFT JOIN users u ON u.id = pph.created_by
        LEFT JOIN employee e ON e.users_id = pph.created_by
        $where
        ORDER BY pph.id DESC
        LIMIT $limit
    ";

    $res = mysqli_query($conn, $sql);

    if (!$res) {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => []]);
        exit;
    }

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = [
            "id"            => (int)$row['id'],
            "product_id"    => (int)$row['product_id'],
            "product_name"  => $row['product_name'],
            "sku"           => $row['sku'],
            "facility_id"   => (int)$row['facility_id'],
            "facility_name" => $row['facility_name'],
            "price_type"    => $row['price_type'],
            "old_price"     => $row['old_price'] !== null ? (float)$row['old_price'] : null,
            "new_price"     => $row['new_price'] !== null ? (float)$row['new_price'] : null,
            "remarks"       => $row['remarks'],
            "created_by"    => $row['created_by_name'] ?: ($row['created_by_username'] ?? ''),
            "effective_date" => $row['effective_date'],
            "created_at"    => $row['created_at']
        ];
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
    exit;
}

/* ============================================================================
   GET FACILITY PRICE
   Returns the active selling price for a (product, facility) pair.
   Used by the Receive Stock and Sell dialogs.
============================================================================ */
if ($trans === "GET_FACILITY_PRICE") {

    $product_id  = (int)($data['product_id'] ?? 0);
    $facility_id = (int)($data['facility_id'] ?? 0);

    if (!$product_id || !$facility_id) {
        echo json_encode(["code" => 1, "message" => "Product and Facility are required", "data" => null]);
        exit;
    }

    $price = InventoryEngine::getFacilitySellingPrice($conn, $product_id, $facility_id);

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => ["selling_price" => $price]
    ]);
    exit;
}

echo json_encode([
    "code" => 1,
    "message" => "Invalid transaction",
    "data" => null
]);
