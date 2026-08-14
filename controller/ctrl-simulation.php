<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';
include_once __DIR__ . '/../model/inventory.php';

$data  = json_decode(file_get_contents("php://input"), true);
$trans = $_GET['trans'] ?? ($data['trans'] ?? '');

if (!$conn) {
    echo json_encode(["code" => 1, "message" => "Database connection failed", "data" => null]);
    exit;
}

if (empty($trans)) {
    echo json_encode(["code" => 1, "message" => "Transaction is required", "data" => null]);
    exit;
}

/* ---------------------------------------------------------------------------
   SESSION-ONLY SIMULATION
   Preview / execute never touch product_inventory or product_inventory_logs.
   The simulation history lives in $_SESSION['inv_sim'] and is explicitly
   temporary — cleared when the browser session ends or via SIM_CLEAR.
--------------------------------------------------------------------------- */

function simInit() {
    if (!isset($_SESSION['inv_sim']) || !is_array($_SESSION['inv_sim'])) {
        $_SESSION['inv_sim'] = ['history' => [], 'seq' => 0];
    }
}

function simStats($history) {
    $total = 0;
    $buy = 0;
    $sell = 0;
    $units_bought = 0;
    $units_sold = 0;
    $sales_value = 0;

    foreach ($history as $h) {
        if (($h['status'] ?? '') !== 'SUCCESS') continue;
        $total++;
        $qty = (float)$h['quantity'];
        if ($h['type'] === 'BUY') {
            $buy++;
            $units_bought += $qty;
        } else {
            $sell++;
            $units_sold += $qty;
            $sales_value += (float)$h['total'];
        }
    }

    return [
        "total"        => (int)$total,
        "buy"          => (int)$buy,
        "sell"         => (int)$sell,
        "units_bought" => $units_bought,
        "units_sold"   => $units_sold,
        "sales_value"  => $sales_value
    ];
}

function simNextId() {
    simInit();
    $_SESSION['inv_sim']['seq'] = (int)$_SESSION['inv_sim']['seq'] + 1;
    $seq = $_SESSION['inv_sim']['seq'];
    return 'SIM-' . date('Ymd') . '-' . str_pad($seq, 6, '0', STR_PAD_LEFT);
}

function simProductAndFacility($conn, $facility_id, $product_id) {
    $product_name = '';
    $facility_name = '';

    $pq = mysqli_query($conn, "SELECT name FROM product WHERE id = '" . (int)$product_id . "' LIMIT 1");
    $pr = mysqli_fetch_assoc($pq);
    if ($pr) $product_name = $pr['name'];

    $fq = mysqli_query($conn, "SELECT name FROM facility WHERE id = '" . (int)$facility_id . "' LIMIT 1");
    $fr = mysqli_fetch_assoc($fq);
    if ($fr) $facility_name = $fr['name'];

    return [$product_name, $facility_name];
}

function simValidateCommon($data) {
    $type       = $data['type'] ?? '';
    $facility_id = (int)($data['facility_id'] ?? 0);
    $product_id = (int)($data['product_id'] ?? 0);
    $quantity   = (float)($data['quantity'] ?? 0);

    if (!in_array($type, ['BUY', 'SELL'], true)) {
        return ['error' => 'Invalid type. Use BUY or SELL.'];
    }
    if (!$facility_id || !$product_id) {
        return ['error' => 'Facility and Product are required'];
    }
    if ($quantity <= 0) {
        return ['error' => 'Quantity must be greater than zero'];
    }

    return ['type' => $type, 'facility_id' => $facility_id, 'product_id' => $product_id, 'quantity' => $quantity];
}

if ($trans === "SIM_GET_STATE") {

    simInit();

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "history" => $_SESSION['inv_sim']['history'],
            "stats"   => simStats($_SESSION['inv_sim']['history'])
        ]
    ]);
    exit;

} else if ($trans === "SIM_CLEAR") {

    simInit();
    $_SESSION['inv_sim']['history'] = [];
    $_SESSION['inv_sim']['seq'] = 0;

    echo json_encode([
        "code" => 0,
        "message" => "Simulation history cleared",
        "data" => [
            "history" => [],
            "stats"   => simStats([])
        ]
    ]);
    exit;

} else if ($trans === "SIM_PREVIEW") {

    $v = simValidateCommon($data);
    if (isset($v['error'])) {
        echo json_encode(["code" => 1, "message" => $v['error'], "data" => null]);
        exit;
    }

    list($product_name, $facility_name) = simProductAndFacility($conn, $v['facility_id'], $v['product_id']);

    $current_stock = InventoryEngine::getCurrentByProduct($conn, $v['facility_id'], $v['product_id']);
    $available     = InventoryEngine::getAvailableByProduct($conn, $v['facility_id'], $v['product_id']);

    $projected_current   = 0;
    $projected_available = 0;
    $unit_price          = null;
    $total               = 0;
    $allocation          = [];
    $failure             = null;
    $low_stock_after     = false;

    if ($v['type'] === 'BUY') {
        $unit_price          = (float)($data['unit_price'] ?? 0);
        $total               = $unit_price * $v['quantity'];
        $projected_current   = $current_stock + $v['quantity'];
        $projected_available = $available + $v['quantity'];
    } else {

        $selling_price = InventoryEngine::getFacilitySellingPrice($conn, $v['product_id'], $v['facility_id']);

        if ($selling_price === null) {
            $failure = 'No facility price set for this product at ' . ($facility_name ?: 'the selected facility');
        }

        $batches = InventoryEngine::getFEFOBatches($conn, $v['facility_id'], $v['product_id']);

        if ($failure === null) {
            try {
                $allocation = InventoryEngine::allocateFEFO($batches, $v['quantity']);
                $unit_price = $selling_price;
                $total      = $selling_price * $v['quantity'];
            } catch (\RuntimeException $e) {
                $failure = $e->getMessage();
            }
        }

        $projected_current   = $current_stock - $v['quantity'];
        $projected_available = $available - $v['quantity'];
    }

    if ($failure !== null) {
        $projected_current   = $current_stock;
        $projected_available = $available;
    }

    // Low-stock projection: reorder check against smallest reorder level at the facility
    $rq = mysqli_query($conn, "
        SELECT COALESCE(MIN(reorder_level), 0) AS rl
        FROM product_inventory
        WHERE facility_id = '" . (int)$v['facility_id'] . "'
        AND product_id = '" . (int)$v['product_id'] . "'
        AND status = 1
    ");
    $rl = (float)(mysqli_fetch_assoc($rq)['rl'] ?? 0);
    $low_stock_after = $rl > 0 && $projected_available <= $rl;

    echo json_encode([
        "code" => 0,
        "message" => "Preview ready",
        "data" => [
            "type"               => $v['type'],
            "facility_id"        => $v['facility_id'],
            "facility_name"      => $facility_name,
            "product_id"         => $v['product_id'],
            "product_name"       => $product_name,
            "quantity"           => $v['quantity'],
            "unit_price"         => $unit_price,
            "total"              => $total,
            "current_stock"      => $current_stock,
            "available_stock"    => $available,
            "projected_current"  => $projected_current,
            "projected_available"=> $projected_available,
            "reorder_level"      => $rl,
            "low_stock_after"    => $low_stock_after,
            "allocation"         => $allocation,
            "failure"            => $failure
        ]
    ]);
    exit;

} else if ($trans === "SIM_EXECUTE") {

    $v = simValidateCommon($data);
    if (isset($v['error'])) {
        echo json_encode(["code" => 1, "message" => $v['error'], "data" => null]);
        exit;
    }

    list($product_name, $facility_name) = simProductAndFacility($conn, $v['facility_id'], $v['product_id']);

    $current_stock = InventoryEngine::getCurrentByProduct($conn, $v['facility_id'], $v['product_id']);
    $available     = InventoryEngine::getAvailableByProduct($conn, $v['facility_id'], $v['product_id']);

    $unit_price  = null;
    $total       = 0;
    $allocation  = [];
    $failure     = null;
    $projected_current   = 0;
    $projected_available = 0;

    if ($v['type'] === 'BUY') {
        $unit_price          = (float)($data['unit_price'] ?? 0);
        $total               = $unit_price * $v['quantity'];
        $projected_current   = $current_stock + $v['quantity'];
        $projected_available = $available + $v['quantity'];
    } else {
        $selling_price = InventoryEngine::getFacilitySellingPrice($conn, $v['product_id'], $v['facility_id']);

        if ($selling_price === null) {
            $failure = 'No facility price set for this product at ' . ($facility_name ?: 'the selected facility');
        }

        $batches = InventoryEngine::getFEFOBatches($conn, $v['facility_id'], $v['product_id']);

        if ($failure === null) {
            try {
                $allocation = InventoryEngine::allocateFEFO($batches, $v['quantity']);
                $unit_price = $selling_price;
                $total      = $selling_price * $v['quantity'];
            } catch (\RuntimeException $e) {
                $failure = $e->getMessage();
            }
        }

        $projected_current   = $current_stock - $v['quantity'];
        $projected_available = $available - $v['quantity'];
    }

    if ($failure !== null) {
        $projected_current   = $current_stock;
        $projected_available = $available;
    }

    simInit();

    $record = [
        "id"          => simNextId(),
        "type"        => $v['type'],
        "facility_id" => $v['facility_id'],
        "facility_name" => $facility_name,
        "product_id"  => $v['product_id'],
        "product_name" => $product_name,
        "quantity"    => $v['quantity'],
        "unit_price"  => $unit_price,
        "total"       => $total,
        "status"      => $failure === null ? 'SUCCESS' : 'FAILED',
        "reason"      => $failure,
        "allocation"  => $allocation,
        "created_at"  => date('Y-m-d H:i:s')
    ];

    $_SESSION['inv_sim']['history'][] = $record;

    echo json_encode([
        "code" => $failure === null ? 0 : 1,
        "message" => $failure === null ? "Simulated " . strtolower($v['type']) . " recorded" : $failure,
        "data" => [
            "record" => $record,
            "history" => $_SESSION['inv_sim']['history'],
            "stats"   => simStats($_SESSION['inv_sim']['history'])
        ]
    ]);
    exit;
}

echo json_encode([
    "code" => 1,
    "message" => "Invalid transaction",
    "data" => null
]);
