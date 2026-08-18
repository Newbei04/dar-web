<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

include_once __DIR__ . '/../config/dbcon.php';
include_once __DIR__ . '/../model/gbl.php';

GblFn::errorReporting();

$data = json_decode(file_get_contents("php://input"), true);
$data = GblFn::sanitizeData($data);
$trans = $data['trans'] ?? '';

if (!$_SESSION["IS_LOGIN"]) {
    echo json_encode(["code" => 1, "message" => "Unauthorized", "data" => null]);
    exit;
}

/*
|--------------------------------------------------------------------------
| LIST_LOGS
|--------------------------------------------------------------------------
| Aggregates every audit/trail table in the system into one normalized feed:
|   module     - Booking / Inventory / Pricing / Wallet / Review / Store
|   action     - short label describing the event
|   details    - human readable detail line
|   user_name  - who performed it (employee/buyer display name)
|   created_at - event timestamp
|
| Aggregation is done in PHP (per-table queries merged in memory) to avoid
| MySQL "Illegal mix of collations" errors on UNION across differently
| collated tables.
|--------------------------------------------------------------------------
*/
if ($trans == "LIST_LOGS") {

    $module = $data['module'] ?? '';

    $db = DBCon::getConnection();

    try {
        $logs = [];

        // users_id -> display name (employee name, else username)
        $nameMap = [];
        $stmt = $db->query("
            SELECT u.id AS users_id,
                   COALESCE(NULLIF(CONCAT(e.fname, ' ', e.lname), ' '), u.username) AS name
            FROM users u
            LEFT JOIN employee e ON e.users_id = u.id
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $nameMap[$r['users_id']] = $r['name'];
        }
        $userName = function ($usersId) use ($nameMap) {
            return isset($nameMap[$usersId]) && $nameMap[$usersId] !== '' ? $nameMap[$usersId] : '-';
        };
        $benName = function ($fname, $lname) {
            $n = trim(($fname ?? '') . ' ' . ($lname ?? ''));
            return $n !== '' ? $n : '-';
        };

        // ============ BOOKING LOGS ============
        $stmt = $db->query("
            SELECT b.booking_num,
                   bl.booked_by, bl.created_at,
                   bl.approved_at, bl.approved_staff_id,
                   bl.checkout_at, bl.checkout_staff_id,
                   bl.returned_at, bl.returned_staff_id,
                   bl.declined_at, bl.declined_staff_id, bl.declined_remarks
            FROM booking_logs bl
            LEFT JOIN booking b ON b.id = bl.booking_id
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $ref = 'Booking #' . ($r['booking_num'] ?? $r['booking_num'] ?? '-');
            if (!empty($r['created_at'])) {
                $logs[] = ['module' => 'Booking', 'action' => 'Booking Created', 'details' => $ref, 'user_name' => $userName($r['booked_by']), 'created_at' => $r['created_at']];
            }
            if (!empty($r['approved_at'])) {
                $logs[] = ['module' => 'Booking', 'action' => 'Booking Approved', 'details' => $ref, 'user_name' => $userName($r['approved_staff_id']), 'created_at' => $r['approved_at']];
            }
            if (!empty($r['checkout_at'])) {
                $logs[] = ['module' => 'Booking', 'action' => 'Booking Checked Out', 'details' => $ref, 'user_name' => $userName($r['checkout_staff_id']), 'created_at' => $r['checkout_at']];
            }
            if (!empty($r['returned_at'])) {
                $logs[] = ['module' => 'Booking', 'action' => 'Booking Returned', 'details' => $ref, 'user_name' => $userName($r['returned_staff_id']), 'created_at' => $r['returned_at']];
            }
            if (!empty($r['declined_at'])) {
                $remarks = !empty($r['declined_remarks']) ? ' - ' . $r['declined_remarks'] : '';
                $logs[] = ['module' => 'Booking', 'action' => 'Booking Declined', 'details' => $ref . $remarks, 'user_name' => $userName($r['declined_staff_id']), 'created_at' => $r['declined_at']];
            }
        }

        // ============ INVENTORY LOGS ============
        $actionTypes = [1 => 'Restock', 2 => 'Sale', 3 => 'Return', 4 => 'Adjustment', 5 => 'Expired', 6 => 'Damaged'];
        $stmt = $db->query("
            SELECT pil.action_type, pil.quantity_changed, pil.new_balance, pil.users_id,
                   pil.remarks, pil.created_at,
                   p.name AS product_name, inv.batch_number
            FROM product_inventory_logs pil
            LEFT JOIN product_inventory inv ON inv.id = pil.inventory_id
            LEFT JOIN product p ON p.id = inv.product_id
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $action = $actionTypes[$r['action_type']] ?? 'Movement';
            $product = ($r['product_name'] ?? 'Product') . (($r['batch_number'] ?? '') !== '' ? ' (' . $r['batch_number'] . ')' : '');
            $remarks = !empty($r['remarks']) ? ' - ' . $r['remarks'] : '';
            $logs[] = [
                'module' => 'Inventory',
                'action' => $action,
                'details' => $product . ' ' . $r['quantity_changed'] . ' units -> balance ' . $r['new_balance'] . $remarks,
                'user_name' => $userName($r['users_id']),
                'created_at' => $r['created_at']
            ];
        }

        // ============ PRICE HISTORY ============
        $stmt = $db->query("
            SELECT pph.price_type, pph.old_price, pph.new_price, pph.created_by, pph.created_at,
                   p.name AS product_name, f.name AS facility_name
            FROM product_price_history pph
            LEFT JOIN product p ON p.id = pph.product_id
            LEFT JOIN facility f ON f.id = pph.facility_id
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $where = ($r['facility_name'] ?? '') !== '' ? ' @ ' . $r['facility_name'] : '';
            $logs[] = [
                'module' => 'Pricing',
                'action' => 'Price changed (' . ($r['price_type'] ?? 'SELLING') . ')',
                'details' => ($r['product_name'] ?? 'Product') . ': ' . ($r['old_price'] ?? '-') . ' -> ' . ($r['new_price'] ?? '-') . $where,
                'user_name' => $userName($r['created_by']),
                'created_at' => $r['created_at']
            ];
        }

        // ============ WALLET LOGS ============
        $walletActions = [0 => 'Wallet Created', 1 => 'Debit', 2 => 'Credit', 3 => 'Frozen'];
        $stmt = $db->query("
            SELECT action, amount, balance_before, balance_after, created_at
            FROM wallet_logs
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $logs[] = [
                'module' => 'Wallet',
                'action' => $walletActions[$r['action']] ?? 'Wallet Update',
                'details' => 'Amount: ' . $r['amount'] . ' (' . $r['balance_before'] . ' -> ' . $r['balance_after'] . ')',
                'user_name' => '-',
                'created_at' => $r['created_at']
            ];
        }

        // ============ MACHINERY REVIEWS ============
        $stmt = $db->query("
            SELECT mr.rating, mr.comment, mr.created_at, m.name AS machinery_name, ben.fname, ben.lname
            FROM machinery_reviews mr
            LEFT JOIN machinery m ON m.id = mr.machinery_id
            LEFT JOIN beneficiary ben ON ben.id = mr.beneficiary_id
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $comment = !empty($r['comment']) ? ' - ' . $r['comment'] : '';
            $logs[] = [
                'module' => 'Review',
                'action' => 'Rated ' . $r['rating'] . '/5',
                'details' => 'Machinery: ' . ($r['machinery_name'] ?? '-') . $comment,
                'user_name' => $benName($r['fname'], $r['lname']),
                'created_at' => $r['created_at']
            ];
        }

        // ============ SERVICE REVIEWS ============
        $stmt = $db->query("
            SELECT sr.rating, sr.comments, sr.created_at, ben.fname, ben.lname
            FROM service_reviews sr
            LEFT JOIN booking b ON b.id = sr.booking_id
            LEFT JOIN beneficiary ben ON ben.id = b.beneficiary_id
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $logs[] = [
                'module' => 'Review',
                'action' => 'Service rated ' . $r['rating'] . '/5',
                'details' => !empty($r['comments']) ? $r['comments'] : 'No comment',
                'user_name' => $benName($r['fname'], $r['lname']),
                'created_at' => $r['created_at']
            ];
        }

        // ============ STORE TRANSACTIONS ============
        $stmt = $db->query("
            SELECT st.reference_no, st.net_amount, COALESCE(st.transaction_dt, st.created_at) AS created_at,
                   ben.fname, ben.lname
            FROM store_transactions st
            LEFT JOIN beneficiary ben ON ben.id = st.beneficiary_id
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $logs[] = [
                'module' => 'Store',
                'action' => 'Checkout',
                'details' => 'Ref ' . ($r['reference_no'] ?? '-') . ' - Net ' . $r['net_amount'],
                'user_name' => $benName($r['fname'], $r['lname']),
                'created_at' => $r['created_at']
            ];
        }

        // Filter by module (if requested)
        if ($module) {
            $logs = array_values(array_filter($logs, function ($l) use ($module) {
                return $l['module'] === $module;
            }));
        }

        // Sort newest first
        usort($logs, function ($a, $b) {
            return strcmp((string)$b['created_at'], (string)$a['created_at']);
        });

        echo json_encode(["code" => 0, "message" => "Success", "data" => $logs]);
    } catch (Exception $e) {
        error_log("LIST_LOGS error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;
}
