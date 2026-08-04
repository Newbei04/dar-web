<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';
include_once __DIR__ . '/../model/gbl.php';

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

if ($trans == "ADD_PRODUCT") {

    $category_id  = $data['category_id'] ?? null;
    $name         = trim($data['name'] ?? '');
    $details      = $data['details'] ?? '';
    $unit         = $data['unit'] ?? '';
    $is_hazardous = $data['is_hazardous'] ?? 0;
    $status       = $data['status'] ?? 1;
    $barcode      = mysqli_real_escape_string($conn, trim($data['barcode'] ?? ''));

    if (!$category_id || $name == '') {
        echo json_encode([
            "code"    => 1,
            "message" => "Missing required fields",
            "data"    => null
        ]);
        exit;
    }

    $dupQ = mysqli_query($conn, "
        SELECT id 
        FROM product 
        WHERE name = '$name' 
        LIMIT 1
    ");

    if (mysqli_num_rows($dupQ) > 0) {
        echo json_encode([
            "code"    => 1,
            "message" => "Product already exists in the catalog",
            "data"    => null
        ]);
        exit;
    }

    $catQ = mysqli_query($conn, "
        SELECT name 
        FROM product_category 
        WHERE id = '$category_id' 
        LIMIT 1
    ");
    $cat = mysqli_fetch_assoc($catQ);

    // ── SKU generation ────────────────────────────────────────────
    $isUnique = false;
    $sku      = '';

    do {
        $sku  = GblFn::generateSKU($name, $cat['name']);
        $stmt = $conn->prepare("SELECT sku FROM product WHERE sku = ?");
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $isUnique = true;
        }
        $stmt->close();
    } while (!$isUnique);

    // ── Insert product ────────────────────────────────────────────
    $sql = "INSERT INTO product (
                category_id, sku, barcode, name, details,
                unit, is_hazardous, status, created_at
            ) VALUES (
                '$category_id', '$sku', " . ($barcode !== '' ? "'$barcode'" : "NULL") . ", '$name', '$details',
                '$unit', '$is_hazardous', '$status', NOW()
            )";

    if (!mysqli_query($conn, $sql)) {
        echo json_encode([
            "code"    => 1,
            "message" => "We encountered an error. Please try again!"
        ]);
        exit;
    }

    $last_id   = mysqli_insert_id($conn);
    $images    = $data['images'] ?? [];
    $isPrimary = 1;

    foreach ($images as $imageData) {
        $photo_name = GblFn::processUpload($imageData, 'product', 'prod', $last_id);
        if ($photo_name !== '') {
            mysqli_query($conn, "
                INSERT INTO product_images (product_id, name, is_primary, status, created_at)
                VALUES ('$last_id', '$photo_name', '$isPrimary', 1, NOW())
            ");
            $isPrimary = 0;
        }
    }

    echo json_encode([
        "code"    => 0,
        "message" => "Product created successfully",
        "last_id" => $last_id
    ]);
    exit;
} else if ($trans == "UPDATE_PRODUCT") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "id is required",
            "data" => null
        ]);
        exit;
    }

    $fields = [];

    // if (isset($data['facility_id']))  $fields[] = "facility_id='{$data['facility_id']}'";
    if (isset($data['category_id']))  $fields[] = "category_id='{$data['category_id']}'";
    if (isset($data['name']))         $fields[] = "name='{$data['name']}'";
    if (isset($data['sku']))          $fields[] = "sku='{$data['sku']}'";
    if (isset($data['barcode']))      $fields[] = "barcode='" . mysqli_real_escape_string($conn, trim($data['barcode'])) . "'";
    if (isset($data['unit']))         $fields[] = "unit='{$data['unit']}'";
    if (isset($data['details']))      $fields[] = "details='{$data['details']}'";
    if (isset($data['is_hazardous'])) $fields[] = "is_hazardous='{$data['is_hazardous']}'";
    if (isset($data['status']))       $fields[] = "status='{$data['status']}'";

    $fields[] = "updated_at = NOW()";

    if (!mysqli_query($conn, "UPDATE product SET " . implode(",", $fields) . " WHERE id='$id'")) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    // ── APPEND NEW PRODUCT IMAGES (keep existing ones) ──────
    $images = $data['images'] ?? [];

    if (!empty($images)) {

        // Check if the product already has an active primary image
        $hasPrimary = false;
        $existingImgs = mysqli_query($conn, "
            SELECT id, is_primary
            FROM product_images
            WHERE product_id = '$id'
            AND status = 1
            LIMIT 1
        ");

        while ($row = mysqli_fetch_assoc($existingImgs)) {
            if ((int)$row['is_primary'] === 1) {
                $hasPrimary = true;
                break;
            }
        }

        // Only add new images — never delete the old ones
        foreach ($images as $imageData) {

            $photo_name = GblFn::processUpload(
                $imageData,
                'product',
                'prod',
                $id
            );

            if ($photo_name !== '') {

                $isPrimary = $hasPrimary ? 0 : 1;

                mysqli_query($conn, "
                    INSERT INTO product_images (product_id, name, is_primary, status, created_at
                    ) VALUES (
                     '$id','$photo_name','$isPrimary',1,NOW()
                    )
                ");

                $hasPrimary = true;
            }
        }
    }

    echo json_encode([
        "code" => 0,
        "message" => "Product updated successfully",
        "data" => null
    ]);
    exit;
} else if ($trans == "LIST_PRODUCT") {

    $where = "WHERE 1=1";

    $search = trim($data['search'] ?? '');
    if ($search !== '') {
        $search = mysqli_real_escape_string($conn, $search);
        $where .= " AND (p.name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.barcode LIKE '%$search%')";
    }

    $sql = mysqli_query($conn, "
        SELECT p.id, p.category_id, c.name AS category_name, p.sku, p.barcode, p.name, p.details, p.unit, p.is_hazardous, p.status, p.created_at,
        (SELECT name FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image,
        (SELECT COUNT(DISTINCT pi.facility_id) FROM product_inventory pi WHERE pi.product_id = p.id AND pi.status = 1) AS facility_count,
        (SELECT COALESCE(SUM(pi.current_stock), 0) FROM product_inventory pi WHERE pi.product_id = p.id AND pi.status = 1) AS total_stock
        FROM product p
        LEFT JOIN product_category c ON c.id = p.category_id
        $where
        ORDER BY p.id DESC
    ");

    $items = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $items[] = [
            "id"             => (int)$row['id'],
            "category_id"    => (int)$row['category_id'],
            "category_name"  => $row['category_name'],
            "sku"            => $row['sku'],
            "barcode"        => $row['barcode'],
            "name"           => $row['name'],
            "details"        => $row['details'],
            "unit"           => $row['unit'],
            "is_hazardous"   => (int)$row['is_hazardous'],
            "status"         => (int)$row['status'],
            "created_at"     => $row['created_at'],
            "image"          => $row['image'],
            "facility_count" => (int)$row['facility_count'],
            "total_stock"    => (float)$row['total_stock']
        ];
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $items
    ]);

    exit; 
} else if ($trans == "SET_PRIMARY_IMAGE") {

    $image_id   = (int)($data['image_id'] ?? 0);
    $product_id = (int)($data['product_id'] ?? 0);

    if (!$image_id || !$product_id) {
        echo json_encode(["code" => 1, "message" => "image_id and product_id are required", "data" => null]);
        exit;
    }

    mysqli_begin_transaction($conn);

    $clear = mysqli_query($conn, "
        UPDATE product_images SET is_primary = 0 WHERE product_id = '$product_id'
    ");

    $set = mysqli_query($conn, "
        UPDATE product_images SET is_primary = 1 WHERE id = '$image_id' AND product_id = '$product_id'
    ");

    if (!$clear || !$set || mysqli_affected_rows($conn) < 1) {
        mysqli_rollback($conn);
        echo json_encode(["code" => 1, "message" => "Could not set primary image", "data" => null]);
        exit;
    }

    mysqli_commit($conn);

    echo json_encode(["code" => 0, "message" => "Primary image updated", "data" => null]);
    exit;

} else if ($trans == "GET_PRODUCT_DETAILS") {

    $product_id = (int)($data['product_id'] ?? 0);

    if (!$product_id) {
        echo json_encode(["code" => 1, "message" => "product_id is required", "data" => null]);
        exit;
    }

    $pq = mysqli_query($conn, "
        SELECT p.*, c.name AS category_name
        FROM product p
        LEFT JOIN product_category c ON c.id = p.category_id
        WHERE p.id = '$product_id'
        LIMIT 1
    ");

    $product = mysqli_fetch_assoc($pq);

    if (!$product) {
        echo json_encode(["code" => 1, "message" => "Product not found", "data" => null]);
        exit;
    }

    $images = [];
    $ir = mysqli_query($conn, "
        SELECT id, product_id, name, is_primary, status
        FROM product_images
        WHERE product_id = '$product_id' AND status = 1
        ORDER BY is_primary DESC, id ASC
    ");
    while ($r = mysqli_fetch_assoc($ir)) {
        $images[] = [
            "id"         => (int)$r['id'],
            "name"       => $r['name'],
            "is_primary" => (int)$r['is_primary'],
            "status"     => (int)$r['status']
        ];
    }

    $inventory = [];
    $fi = mysqli_query($conn, "
        SELECT pi.*, f.name AS facility_name,
               (pi.current_stock - pi.reserved_stock) AS available_stock
        FROM product_inventory pi
        LEFT JOIN facility f ON f.id = pi.facility_id
        WHERE pi.product_id = '$product_id'
        ORDER BY pi.facility_id ASC, pi.id ASC
    ");
    while ($r = mysqli_fetch_assoc($fi)) {
        $inventory[] = [
            "id"               => (int)$r['id'],
            "facility_id"      => (int)$r['facility_id'],
            "facility_name"    => $r['facility_name'],
            "batch_number"     => $r['batch_number'],
            "current_stock"    => (float)$r['current_stock'],
            "reserved_stock"   => (float)$r['reserved_stock'],
            "available_stock"  => (float)$r['available_stock'],
            "reorder_level"    => (int)$r['reorder_level'],
            "cost_price"       => (float)$r['cost_price'],
            "expiry_date"      => $r['expiry_date'],
            "storage_location" => $r['storage_location'],
            "status"           => (int)$r['status'],
            "created_at"       => $r['created_at']
        ];
    }

    $prices = [];
    $pr = mysqli_query($conn, "
        SELECT pfp.id, pfp.product_id, pfp.facility_id, pfp.selling_price,
               pfp.minimum_price, pfp.maximum_price, pfp.status, pfp.updated_at,
               f.name AS facility_name
        FROM product_facility_price pfp
        LEFT JOIN facility f ON f.id = pfp.facility_id
        WHERE pfp.product_id = '$product_id'
        ORDER BY f.id ASC
    ");
    while ($r = mysqli_fetch_assoc($pr)) {
        $prices[] = [
            "id"             => (int)$r['id'],
            "facility_id"    => (int)$r['facility_id'],
            "facility_name"  => $r['facility_name'],
            "selling_price"  => (float)$r['selling_price'],
            "minimum_price"  => $r['minimum_price'] !== null ? (float)$r['minimum_price'] : null,
            "maximum_price"  => $r['maximum_price'] !== null ? (float)$r['maximum_price'] : null,
            "status"         => (int)$r['status'],
            "updated_at"     => $r['updated_at']
        ];
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "product" => [
                "id"            => (int)$product['id'],
                "category_id"   => (int)$product['category_id'],
                "category_name" => $product['category_name'],
                "sku"           => $product['sku'],
                "barcode"       => $product['barcode'],
                "name"          => $product['name'],
                "details"       => $product['details'],
                "unit"          => $product['unit'],
                "is_hazardous"  => (int)$product['is_hazardous'],
                "status"        => (int)$product['status'],
                "created_at"    => $product['created_at']
            ],
            "images"    => $images,
            "inventory" => $inventory,
            "prices"    => $prices
        ]
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
