<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data  = json_decode(file_get_contents("php://input"), true);
$trans = $_GET['trans'] ?? ($data['trans'] ?? '');

if (!$conn) {
    echo json_encode(["code" => 1, "message" => "Database connection failed", "data" => null]);
    exit;
}

if ($trans !== "GET_REPORT") {
    echo json_encode(["code" => 1, "message" => "Invalid transaction", "data" => null]);
    exit;
}

$report      = $data['report'] ?? '';
$facility_id = (int)($data['facility_id'] ?? 0);
$product_id  = (int)($data['product_id'] ?? 0);
$from        = trim($data['from'] ?? '');
$to          = trim($data['to'] ?? '');

// Optional facility scoping for facility-level users.
$fac_where = $facility_id ? " AND pi.facility_id = '$facility_id'" : "";

// Date window helper for ledger/movement reports (based on log created_at).
function dateWindow($conn, $from, $to) {
    $w = "";
    if ($from !== '') {
        $from = mysqli_real_escape_string($conn, $from);
        $w .= " AND DATE(pil.created_at) >= '$from'";
    }
    if ($to !== '') {
        $to = mysqli_real_escape_string($conn, $to);
        $w .= " AND DATE(pil.created_at) <= '$to'";
    }
    return $w;
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

$res = null;

/* ────────────────────────────────────────────────────────────────
   1. PRODUCT SUMMARY
──────────────────────────────────────────────────────────────── */
if ($report === "product_summary") {

    $sql = "
        SELECT
            p.id, p.sku, p.name, p.unit, p.status,
            pc.name AS category_name,
            (SELECT name FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image,
            (SELECT COUNT(DISTINCT pi.facility_id) FROM product_inventory pi WHERE pi.product_id = p.id AND pi.status = 1 $fac_where) AS facility_count,
            (SELECT COALESCE(SUM(pi.current_stock), 0) FROM product_inventory pi WHERE pi.product_id = p.id AND pi.status = 1 $fac_where) AS total_stock,
            (SELECT COALESCE(SUM(pi.reserved_stock), 0) FROM product_inventory pi WHERE pi.product_id = p.id AND pi.status = 1 $fac_where) AS reserved_stock
        FROM product p
        LEFT JOIN product_category pc ON pc.id = p.category_id
        WHERE p.status = 1
        ORDER BY p.name ASC
    ";
    $res = mysqli_query($conn, $sql);

} /* ──────────────────────────────────────────────────────────────
     2. INVENTORY SUMMARY
──────────────────────────────────────────────────────────────── */
elseif ($report === "inventory_summary") {

    $row = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT
            (SELECT COUNT(*) FROM product WHERE status = 1) AS total_products,
            (SELECT COUNT(*) FROM facility WHERE status = 1) AS total_facilities,
            (SELECT COUNT(*) FROM product_inventory WHERE status = 1 $fac_where) AS total_batches,
            (SELECT COALESCE(SUM(current_stock),0) FROM product_inventory WHERE status = 1 $fac_where) AS current_stock,
            (SELECT COALESCE(SUM(reserved_stock),0) FROM product_inventory WHERE status = 1 $fac_where) AS reserved_stock,
            (SELECT COALESCE(SUM(current_stock - reserved_stock),0) FROM product_inventory WHERE status = 1 $fac_where) AS available_stock,
            (SELECT COALESCE(SUM(current_stock * cost_price),0) FROM product_inventory WHERE status = 1 $fac_where) AS inventory_value,
            (SELECT COUNT(*) FROM product_inventory WHERE status = 1 AND current_stock > 0 AND current_stock <= reorder_level $fac_where) AS low_stock,
            (SELECT COUNT(*) FROM product_inventory WHERE status = 1 AND expiry_date IS NOT NULL AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY) $fac_where) AS expiring
    "));

    echo json_encode(["code" => 0, "message" => "Success", "data" => $row ?: []]);
    exit;

} /* ──────────────────────────────────────────────────────────────
     3. INVENTORY BY FACILITY
──────────────────────────────────────────────────────────────── */
elseif ($report === "inventory_by_facility") {

    $res = mysqli_query($conn, "
        SELECT
            f.id, f.name AS facility_name,
            COUNT(DISTINCT pi.product_id) AS product_count,
            COUNT(pi.id) AS batch_count,
            COALESCE(SUM(pi.current_stock),0) AS current_stock,
            COALESCE(SUM(pi.reserved_stock),0) AS reserved_stock,
            COALESCE(SUM(pi.current_stock - pi.reserved_stock),0) AS available_stock,
            COALESCE(SUM(pi.current_stock * pi.cost_price),0) AS inventory_value
        FROM facility f
        LEFT JOIN product_inventory pi ON pi.facility_id = f.id AND pi.status = 1
        WHERE f.status = 1
        " . ($facility_id ? "AND f.id = '$facility_id'" : "") . "
        GROUP BY f.id
        HAVING COUNT(pi.id) > 0
        ORDER BY f.name ASC
    ");

} /* ──────────────────────────────────────────────────────────────
     4. INVENTORY BY PRODUCT
──────────────────────────────────────────────────────────────── */
elseif ($report === "inventory_by_product") {

    $where = "WHERE pi.status = 1 $fac_where";
    if ($product_id) {
        $where .= " AND pi.product_id = '$product_id'";
    }

    $res = mysqli_query($conn, "
        SELECT
            p.id AS product_id, p.name AS product_name, p.sku, p.unit,
            pc.name AS category_name,
            COUNT(DISTINCT pi.facility_id) AS facility_count,
            COUNT(pi.id) AS batch_count,
            COALESCE(SUM(pi.current_stock),0) AS current_stock,
            COALESCE(SUM(pi.reserved_stock),0) AS reserved_stock,
            COALESCE(SUM(pi.current_stock - pi.reserved_stock),0) AS available_stock
        FROM product_inventory pi
        LEFT JOIN product p ON p.id = pi.product_id
        LEFT JOIN product_category pc ON pc.id = p.category_id
        $where
        GROUP BY pi.product_id, p.name, p.sku, p.unit, pc.name
        ORDER BY p.name ASC
    ");

} /* ──────────────────────────────────────────────────────────────
     5. BATCH INVENTORY
──────────────────────────────────────────────────────────────── */
elseif ($report === "batch_inventory") {

    $where = "WHERE pi.status = 1 $fac_where";
    if ($product_id) {
        $where .= " AND pi.product_id = '$product_id'";
    }

    $res = mysqli_query($conn, "
        SELECT
            pi.id, pi.facility_id, pi.product_id, pi.batch_number,
            pi.received_stock, pi.current_stock, pi.reserved_stock,
            (pi.current_stock - pi.reserved_stock) AS available_stock,
            pi.reorder_level, pi.cost_price, pi.expiry_date, pi.storage_location, pi.created_at,
            p.name AS product_name, p.sku, p.unit,
            f.name AS facility_name
        FROM product_inventory pi
        LEFT JOIN product p ON p.id = pi.product_id
        LEFT JOIN facility f ON f.id = pi.facility_id
        $where
        ORDER BY f.name ASC, p.name ASC, pi.expiry_date ASC
    ");

} /* ──────────────────────────────────────────────────────────────
     6. PRICE COMPARISON
──────────────────────────────────────────────────────────────── */
elseif ($report === "price_comparison") {

    $where = "WHERE 1=1";
    if ($facility_id) {
        $where .= " AND pfp.facility_id = '$facility_id'";
    }
    if ($product_id) {
        $where .= " AND pfp.product_id = '$product_id'";
    }

    $res = mysqli_query($conn, "
        SELECT
            pfp.id, pfp.product_id, pfp.facility_id,
            pfp.selling_price, pfp.minimum_price, pfp.maximum_price, pfp.updated_at,
            p.name AS product_name, p.sku,
            f.name AS facility_name
        FROM product_facility_price pfp
        LEFT JOIN product p ON p.id = pfp.product_id
        LEFT JOIN facility f ON f.id = pfp.facility_id
        $where
        ORDER BY p.name ASC, f.name ASC
    ");

} /* ──────────────────────────────────────────────────────────────
     7. PRICE HISTORY
──────────────────────────────────────────────────────────────── */
elseif ($report === "price_history") {

    $where = "WHERE 1=1";
    if ($facility_id) {
        $where .= " AND pph.facility_id = '$facility_id'";
    }
    if ($product_id) {
        $where .= " AND pph.product_id = '$product_id'";
    }

    $res = mysqli_query($conn, "
        SELECT
            pph.id, pph.product_id, pph.facility_id, pph.price_type,
            pph.old_price, pph.new_price, pph.remarks, pph.effective_date, pph.created_at,
            p.name AS product_name, p.sku,
            f.name AS facility_name,
            CONCAT(COALESCE(e.fname, ''), ' ', COALESCE(e.lname, '')) AS created_by
        FROM product_price_history pph
        LEFT JOIN product p ON p.id = pph.product_id
        LEFT JOIN facility f ON f.id = pph.facility_id
        LEFT JOIN employee e ON e.users_id = pph.created_by
        $where
        ORDER BY pph.id DESC
        LIMIT 500
    ");

} /* ──────────────────────────────────────────────────────────────
     8. INVENTORY LEDGER
──────────────────────────────────────────────────────────────── */
elseif ($report === "inventory_ledger") {

    $dw = dateWindow($conn, $from, $to);
    $where = "WHERE 1=1 $fac_where $dw";
    if ($product_id) {
        $where .= " AND pi.product_id = '$product_id'";
    }

    $res = mysqli_query($conn, "
        SELECT
            pil.id, pil.inventory_id, pil.action_type, pil.quantity_changed,
            pil.new_balance, pil.reference_id, pil.remarks, pil.created_at,
            pi.product_id, pi.facility_id,
            p.name AS product_name, p.sku,
            f.name AS facility_name
        FROM product_inventory_logs pil
        LEFT JOIN product_inventory pi ON pi.id = pil.inventory_id
        LEFT JOIN product p ON p.id = pi.product_id
        LEFT JOIN facility f ON f.id = pi.facility_id
        $where
        ORDER BY pil.id DESC
        LIMIT 1000
    ");

} /* ──────────────────────────────────────────────────────────────
     9. INVENTORY MOVEMENT
──────────────────────────────────────────────────────────────── */
elseif ($report === "inventory_movement") {

    // Same dataset as the ledger, but grouped/labelled for movement view.
    $dw = dateWindow($conn, $from, $to);
    $where = "WHERE 1=1 $fac_where $dw";
    if ($product_id) {
        $where .= " AND pi.product_id = '$product_id'";
    }

    $res = mysqli_query($conn, "
        SELECT
            pil.id, pil.action_type, pil.quantity_changed, pil.remarks, pil.created_at,
            pi.product_id, pi.facility_id,
            p.name AS product_name, p.sku,
            f.name AS facility_name
        FROM product_inventory_logs pil
        LEFT JOIN product_inventory pi ON pi.id = pil.inventory_id
        LEFT JOIN product p ON p.id = pi.product_id
        LEFT JOIN facility f ON f.id = pi.facility_id
        $where
        ORDER BY pil.id DESC
        LIMIT 1000
    ");

} /* ──────────────────────────────────────────────────────────────
     10. LOW STOCK
──────────────────────────────────────────────────────────────── */
elseif ($report === "low_stock") {

    $res = mysqli_query($conn, "
        SELECT
            pi.id, pi.product_id, pi.facility_id, pi.current_stock, pi.reserved_stock,
            (pi.current_stock - pi.reserved_stock) AS available_stock,
            pi.reorder_level, pi.batch_number, pi.expiry_date,
            p.name AS product_name, p.sku, p.unit,
            f.name AS facility_name
        FROM product_inventory pi
        LEFT JOIN product p ON p.id = pi.product_id
        LEFT JOIN facility f ON f.id = pi.facility_id
        WHERE pi.status = 1
        AND pi.current_stock > 0
        AND pi.current_stock <= pi.reorder_level
        $fac_where
        ORDER BY (pi.current_stock - pi.reorder_level) ASC
    ");

} /* ──────────────────────────────────────────────────────────────
     11. EXPIRING PRODUCTS
──────────────────────────────────────────────────────────────── */
elseif ($report === "expiring_products") {

    $res = mysqli_query($conn, "
        SELECT
            pi.id, pi.product_id, pi.facility_id, pi.current_stock, pi.reserved_stock,
            pi.batch_number, pi.expiry_date, pi.cost_price,
            DATEDIFF(pi.expiry_date, CURDATE()) AS days_remaining,
            p.name AS product_name, p.sku, p.unit,
            f.name AS facility_name
        FROM product_inventory pi
        LEFT JOIN product p ON p.id = pi.product_id
        LEFT JOIN facility f ON f.id = pi.facility_id
        WHERE pi.status = 1
        AND pi.expiry_date IS NOT NULL
        AND pi.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
        $fac_where
        ORDER BY pi.expiry_date ASC
    ");

} else {
    echo json_encode(["code" => 1, "message" => "Unknown report", "data" => []]);
    exit;
}

if (!$res) {
    echo json_encode(["code" => 1, "message" => mysqli_error($conn), "data" => []]);
    exit;
}

$list = [];
while ($row = mysqli_fetch_assoc($res)) {
    if ($report === "inventory_ledger" || $report === "inventory_movement") {
        $at = (int)$row['action_type'];
        $row['action_label'] = $action_labels[$at] ?? 'Unknown';
    }
    $list[] = $row;
}

echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);