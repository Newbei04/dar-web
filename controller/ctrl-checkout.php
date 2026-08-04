<?php
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include __DIR__ . '/connect.php';

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

/*
|--------------------------------------------------------------------------
| PLACE CHECKOUT
|--------------------------------------------------------------------------
*/
if ($trans == "PLACE_CHECKOUT") {

    $beneficiary_id = $data['beneficiary_id'] ?? '';
    $cooperative_id = $data['cooperative_id'] ?? '';
    // $users_id       = $data['users_id'] ?? '';
    $users_id = $data['users_id'] ?? $data['beneficiary_id'] ?? '';
    $items          = $data['items'] ?? [];

    if (!$beneficiary_id || !$cooperative_id || !$users_id) {

        echo json_encode([
            "code" => 1,
            "message" => "Missing beneficiary, cooperative or user",
            "data" => null
        ]);

        exit;
    }

    if (!is_array($items) || empty($items)) {

        echo json_encode([
            "code" => 1,
            "message" => "Cart items required",
            "data" => null
        ]);

        exit;
    }

    // $reference_id = "CHK-" . date("YmdHis");
    // reference_id is an INT column — use a Unix timestamp (fits INT range)
    $reference_id = time();

    $success = true;
    $error_message = "";

    $checkout_items = [];
    $grand_total = 0;

    mysqli_begin_transaction($conn);

    foreach ($items as $item) {

        $inventory_id = $item['inventory_id'] ?? '';
        $product_id   = $item['product_id'] ?? '';
        $qty          = (float)($item['qty'] ?? 0);

        if ($qty <= 0) {

            $success = false;
            $error_message = "Invalid item";
            break;
        }

        /*
        |--------------------------------------------------------------------------
        | FIFO ALLOCATION (First Expiry, First Out)
        |--------------------------------------------------------------------------
        | When no specific batch (inventory_id) is given, deduct across the
        | batches of the same product, oldest expiry first, until the qty is met.
        */
        if (!$inventory_id) {

            if (!$product_id) {

                $success = false;
                $error_message = "Invalid item (no inventory or product)";
                break;
            }

            $batches_query = mysqli_query($conn, "
                SELECT
                    pi.*,
                    p.name as product_name,
                    p.unit,
                    p.sku,
                    pc.name as category_name
                FROM product_inventory pi
                LEFT JOIN product p
                    ON p.id = pi.product_id
                LEFT JOIN product_category pc
                    ON pc.id = p.category_id
                WHERE pi.product_id = '$product_id'
                AND pi.status = 1
                AND pi.current_stock > pi.reserved_stock
                ORDER BY
                    CASE WHEN pi.expiry_date IS NULL THEN 1 ELSE 0 END ASC,
                    pi.expiry_date ASC,
                    pi.id ASC
            ");

            if (!$batches_query) {

                $success = false;
                $error_message = mysqli_error($conn);
                break;
            }

            if (mysqli_num_rows($batches_query) == 0) {

                $success = false;
                $error_message = "No stock available";
                break;
            }

            $remaining = $qty;

            while ($remaining > 0) {

                $batch = mysqli_fetch_assoc($batches_query);

                if (!$batch) {
                    break;
                }

                $batch_avail =
                    (float)$batch['current_stock'] -
                    (float)$batch['reserved_stock'];

                if ($batch_avail <= 0) {
                    continue;
                }

                $take         = min($remaining, $batch_avail);
                $batch_id     = (int)$batch['id'];
                $new_balance  = (float)$batch['current_stock'] - $take;

                $update_batch = mysqli_query($conn, "
                    UPDATE product_inventory
                    SET current_stock = '$new_balance',
                        updated_at = NOW()
                    WHERE id = '$batch_id'
                ");

                if (!$update_batch) {

                    $success = false;
                    $error_message = mysqli_error($conn);
                    break 2;
                }

                $remarks = mysqli_real_escape_string(
                    $conn,
                    "Checkout transaction (FIFO batch " . ($batch['batch_number'] ?? '') . ")"
                );

                $insert_batch_log = mysqli_query($conn, "
                    INSERT INTO product_inventory_logs (
                        inventory_id,
                        action_type,
                        quantity_changed,
                        new_balance,
                        reference_id,
                        users_id,
                        remarks,
                        created_at
                    )
                    VALUES (
                        '$batch_id',
                        '2',
                        '$take',
                        '$new_balance',
                        '$reference_id',
                        '$users_id',
                        '$remarks',
                        NOW()
                    )
                ");

                if (!$insert_batch_log) {

                    $success = false;
                    $error_message = mysqli_error($conn);
                    break 2;
                }

                $batch_price = (float)$batch['selling_price'];
                $subtotal    = $batch_price * $take;

                $grand_total += $subtotal;

                $checkout_items[] = [

                    "inventory_id" => $batch_id,
                    "product_id" => (int)$batch['product_id'],

                    "sku" => $batch['sku'],
                    "product_name" => $batch['product_name'],
                    "category_name" => $batch['category_name'],
                    "unit" => $batch['unit'],

                    "qty" => $take,
                    "selling_price" => $batch_price,
                    "subtotal" => $subtotal,

                    "remaining_stock" => $new_balance
                ];

                $remaining -= $take;
            }

            if (!$success) {
                break;
            }

            if ($remaining > 0) {

                $success = false;
                $error_message = "Insufficient stock";
                break;
            }

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | GET INVENTORY
        |--------------------------------------------------------------------------
        */
        $inventory_query = mysqli_query($conn, "
            SELECT 
                pi.*,
                p.name as product_name,
                p.unit,
                p.sku,
                pc.name as category_name
            FROM product_inventory pi
            LEFT JOIN product p 
                ON p.id = pi.product_id
            LEFT JOIN product_category pc 
                ON pc.id = p.category_id
            WHERE pi.id = '$inventory_id'
            LIMIT 1
        ");

        if (!$inventory_query || mysqli_num_rows($inventory_query) == 0) {

            $success = false;
            $error_message = "Inventory not found";
            break;
        }

        $inventory = mysqli_fetch_assoc($inventory_query);

        $available_stock =
            (float)$inventory['current_stock'] -
            (float)$inventory['reserved_stock'];

        /*
        |--------------------------------------------------------------------------
        | CHECK STOCK
        |--------------------------------------------------------------------------
        */
        if ($available_stock < $qty) {

            $success = false;

            $error_message =
                "Insufficient stock for " .
                $inventory['product_name'];

            break;
        }

        $selling_price = (float)$inventory['selling_price'];
        $subtotal      = $selling_price * $qty;
        $new_balance   = (float)$inventory['current_stock'] - $qty;

        $grand_total += $subtotal;

        /*
        |--------------------------------------------------------------------------
        | UPDATE INVENTORY
        |--------------------------------------------------------------------------
        */
        $update_inventory = mysqli_query($conn, "
            UPDATE product_inventory
            SET 
                current_stock = '$new_balance',
                updated_at = NOW()
            WHERE id = '$inventory_id'
        ");

        if (!$update_inventory) {

            $success = false;
            $error_message = mysqli_error($conn);
            break;
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT INVENTORY LOG
        |--------------------------------------------------------------------------
        */
        $remarks = mysqli_real_escape_string(
            $conn,
            "Checkout transaction"
        );

        $insert_log = mysqli_query($conn, "
            INSERT INTO product_inventory_logs (
                inventory_id,
                action_type,
                quantity_changed,
                new_balance,
                reference_id,
                users_id,
                remarks,
                created_at
            )
            VALUES (
                '$inventory_id',
                '2',
                '$qty',
                '$new_balance',
                '$reference_id',
                '$users_id',
                '$remarks',
                NOW()
            )
        ");

        if (!$insert_log) {

            $success = false;
            $error_message = mysqli_error($conn);
            break;
        }

        /*
        |--------------------------------------------------------------------------
        | STORE RESPONSE ITEMS
        |--------------------------------------------------------------------------
        */
        $checkout_items[] = [

            "inventory_id" => (int)$inventory['id'],
            "product_id" => (int)$inventory['product_id'],

            "sku" => $inventory['sku'],
            "product_name" => $inventory['product_name'],
            "category_name" => $inventory['category_name'],
            "unit" => $inventory['unit'],

            "qty" => $qty,
            "selling_price" => $selling_price,
            "subtotal" => $subtotal,

            "remaining_stock" => $new_balance
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FAILED
    |--------------------------------------------------------------------------
    */
    if (!$success) {

        mysqli_rollback($conn);

        echo json_encode([
            "code" => 1,
            "message" => $error_message,
            "data" => null
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | GET BENEFICIARY
    |--------------------------------------------------------------------------
    */
    $beneficiary_query = mysqli_query($conn, "
        SELECT 
            b.*,
            u.username
        FROM beneficiary b
        LEFT JOIN users u 
            ON u.id = b.users_id
        WHERE b.users_id = '$beneficiary_id'
        LIMIT 1
    ");

    $beneficiary = mysqli_fetch_assoc($beneficiary_query);

    /*
    |--------------------------------------------------------------------------
    | GET COOPERATIVE
    |--------------------------------------------------------------------------
    */
    $cooperative_query = mysqli_query($conn, "
        SELECT 
            e.*,
            f.name as facility_name
        FROM employee e
        LEFT JOIN facility f 
            ON f.id = e.facility_id
        WHERE e.users_id = '$cooperative_id'
        LIMIT 1
    ");

    $cooperative = mysqli_fetch_assoc($cooperative_query);

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */
    mysqli_commit($conn);

    echo json_encode([
        "code" => 0,
        "message" => "Checkout successful",

        "data" => [

            "reference_id" => $reference_id,

            "beneficiary" => [

                "id" => $beneficiary['id'] ?? null,

                "name" =>
                trim(
                    ($beneficiary['fname'] ?? '') . ' ' .
                        ($beneficiary['lname'] ?? '')
                ),

                "mobile" => $beneficiary['mobile'] ?? '',
                "address" => $beneficiary['address'] ?? ''
            ],

            "cooperative" => [

                "id" => $cooperative['id'] ?? null,

                "name" =>
                trim(
                    ($cooperative['fname'] ?? '') . ' ' .
                        ($cooperative['lname'] ?? '')
                ),

                "facility_name" =>
                $cooperative['facility_name'] ?? ''
            ],

            "summary" => [

                "total_items" => count($checkout_items),
                "grand_total" => $grand_total
            ],

            "items" => $checkout_items
        ]
    ]);
}

/*
|--------------------------------------------------------------------------
| GET CHECKOUT LOGS
|--------------------------------------------------------------------------
*/ else if ($trans == "GET_CHECKOUT_LOGS") {

    $reference_id = $data['reference_id'] ?? '';

    $query = "
        SELECT 
            pil.*,
            pi.product_id,
            p.name as product_name,
            p.sku,
            p.unit,
            pc.name as category_name
        FROM product_inventory_logs pil
        LEFT JOIN product_inventory pi 
            ON pi.id = pil.inventory_id
        LEFT JOIN product p 
            ON p.id = pi.product_id
        LEFT JOIN product_category pc
            ON pc.id = p.category_id
        WHERE pil.action_type = '2'
    ";

    if ($reference_id != '') {

        $query .= "
            AND pil.reference_id = '$reference_id'
        ";
    }

    $query .= "
        ORDER BY pil.id DESC
    ";

    $res = mysqli_query($conn, $query);

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {

        $list[] = [

            "id" => (int)$row['id'],
            "inventory_id" => (int)$row['inventory_id'],
            "product_id" => (int)$row['product_id'],

            "sku" => $row['sku'],
            "product_name" => $row['product_name'],
            "category_name" => $row['category_name'],
            "unit" => $row['unit'],

            "quantity_changed" =>
            (float)$row['quantity_changed'],

            "new_balance" =>
            (float)$row['new_balance'],

            "reference_id" => $row['reference_id'],
            "users_id" => (int)$row['users_id'],

            "remarks" => $row['remarks'],
            "created_at" => $row['created_at']
        ];
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
}

/*
|--------------------------------------------------------------------------
| INVALID TRANSACTION
|--------------------------------------------------------------------------
*/ else {

    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
}
