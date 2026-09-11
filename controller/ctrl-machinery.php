<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';
include_once __DIR__ . '/../model/gbl.php';

$data = json_decode(file_get_contents("php://input"), true);
$trans = $_GET['trans'] ?? ($data['trans'] ?? '');


if ($trans == "LIST_MACHINERY_TYPE") {

    $id = $data['id'] ?? '';

    $page  = max(1, (int)($data['page'] ?? 1));
    $limit = max(1, (int)($data['limit'] ?? 10));
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";

    if (!empty($id)) {
        $where .= " AND id='$id'";
    }

    $totalQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM machinery_type $where");
    $total = mysqli_fetch_assoc($totalQ)['total'] ?? 0;

    $res = mysqli_query($conn, "
        SELECT id, name, details, created_at, updated_at
        FROM machinery_type
        $where
        ORDER BY id DESC
        LIMIT $offset, $limit
    ");


    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "page" => $page,
            "limit" => $limit,
            "total" => (int)$total,
            "result" => !empty($id) ? ($list[0] ?? null) : $list
        ]
    ]);

    exit;
} else if ($trans == "LIST_MACHINERY") {

    $employee_id = mysqli_real_escape_string($conn, $data['employee_id'] ?? '');
    $type_id     = mysqli_real_escape_string($conn, $data['type_id'] ?? '');

    $where = "WHERE 1=1";

    if (!empty($employee_id)) {
        $where .= " AND m.branch_id = (
        SELECT branch_id
        FROM employee
        WHERE users_id = '$employee_id'
        LIMIT 1
    )";
    }

    if (!empty($type_id)) {
        $where .= " AND m.type_id = '$type_id'";
    }

    $res = mysqli_query($conn, "
        SELECT 
            m.id,
            m.type_id,
            b.id AS branch_id,
            b.name AS branch_name,
            m.name,
            m.description,
            m.daily_rate,
            m.model,
            m.status,
            m.created_at,
            m.updated_at,
            mt.name AS machinery_type,
            (SELECT name FROM machinery_images WHERE machinery_id = m.id AND is_primary = 1 LIMIT 1) AS image
        FROM machinery m
        LEFT JOIN branch b 
            ON b.id = m.branch_id
        LEFT JOIN machinery_type mt 
            ON mt.id = m.type_id
        $where
        ORDER BY m.id DESC
    ");

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);

    exit;
} else if ($trans == "ADD_MACHINERY") {

    $branch_id = $data['branch_id'] ?? '';
    $type_id     = $data['type_id']     ?? '';
    $name        = $data['name']        ?? '';
    $model       = $data['model']       ?? '';
    $daily_rate  = $data['daily_rate']  ?? '';
    $description = $data['description'] ?? '';
    $status      = $data['status']      ?? 1;

    if (!in_array((int)$status, [0, 1], true)) {
        echo json_encode([
            "code"    => 1,
            "message" => "Status must be 0 or 1",
            "data"    => null
        ]);
        exit;
    }

    if (!$branch_id || !$type_id || !$name) {
        echo json_encode([
            "code"    => 1,
            "message" => "branch_id, type_id, name are required",
            "data"    => null
        ]);
        exit;
    }

    $sql = "INSERT INTO machinery (
                branch_id, type_id, name, model, description, daily_rate, status, created_at
            ) VALUES (
                '$branch_id', '$type_id', '$name', '$model', '$description', '$daily_rate', '$status', NOW()
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
        $photo_name = GblFn::processUpload(
            $imageData,
            'machinery',
            'mac',
            $last_id
        );

        if ($photo_name !== '') {
            mysqli_query($conn, "
                INSERT INTO machinery_images (machinery_id, name, is_primary, status, created_at)
                VALUES ('$last_id', '$photo_name', '$isPrimary', 1, NOW())
            ");
            $isPrimary = 0;
        }
    }

    echo json_encode([
        "code"    => 0,
        "message" => "Machinery created successfully",
        "last_id" => $last_id
    ]);
    exit;
} else if ($trans == "UPDATE_MACHINERY") {
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

    if (isset($data['branch_id']))  $fields[] = "branch_id='{$data['branch_id']}'";
    if (isset($data['type_id']))      $fields[] = "type_id='{$data['type_id']}'";
    if (isset($data['name']))         $fields[] = "name='{$data['name']}'";
    if (isset($data['model']))        $fields[] = "model='{$data['model']}'";
    if (isset($data['description']))  $fields[] = "description='{$data['description']}'";
    if (isset($data['daily_rate']))   $fields[] = "daily_rate='{$data['daily_rate']}'";
    if (isset($data['status'])) {
        if (!in_array((int)$data['status'], [0, 1], true)) {
            echo json_encode([
                "code" => 1,
                "message" => "Status must be 0 or 1",
                "data" => null
            ]);
            exit;
        }
        $fields[] = "status='{$data['status']}'";
    }

    $fields[] = "updated_at = NOW()";

    if (!mysqli_query(
        $conn,
        "UPDATE machinery SET " . implode(",", $fields) . " WHERE id='$id'"
    )) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    // ── REPLACE EXISTING IMAGES ─────────────────────────
    $images = $data['images'] ?? [];

    if (!empty($images)) {

        // Get old images
        $oldImages = mysqli_query($conn, "
        SELECT name
        FROM machinery_images
        WHERE machinery_id = '$id'
    ");

        while ($row = mysqli_fetch_assoc($oldImages)) {

            $filePath = "../assets/images/machinery/" . $row['name'];

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete old image records
        mysqli_query($conn, "
        DELETE FROM machinery_images
        WHERE machinery_id = '$id'
    ");

        // Insert new images
        $isPrimary = 1;

        foreach ($images as $imageData) {

            $photo_name = GblFn::processUpload(
                $imageData,
                'machinery',
                'mac',
                $id
            );

            if ($photo_name !== '') {

                mysqli_query($conn, "
                INSERT INTO machinery_images (machinery_id, name, is_primary, status, created_at
                ) VALUES (
                 '$id','$photo_name','$isPrimary',1,NOW()
                )
            ");

                $isPrimary = 0;
            }
        }
    }

    echo json_encode([
        "code" => 0,
        "message" => "Updated successfully",
        "data" => null
    ]);
    exit;
} else if ($trans == "GET_MACHINERY_IMAGES") {

    $id = $data['id'] ?? '';

    $res = mysqli_query($conn, "
        SELECT id,name,is_primary
        FROM machinery_images
        WHERE machinery_id='$id'
        ORDER BY is_primary DESC,id ASC
    ");

    $list = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "data" => $list
    ]);
    exit;
} else if ($trans == "DELETE_MACHINERY_IMAGE") {

    $id   = $data['id'] ?? '';
    $name = $data['name'] ?? '';

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "Image ID is required"]);
        exit;
    }

    // Delete file from disk
    $filePath = "../assets/images/machinery/" . $name;
    if ($name && file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete record
    mysqli_query($conn, "DELETE FROM machinery_images WHERE id='$id'");

    echo json_encode(["code" => 0, "message" => "Image deleted"]);
    exit;
} else if ($trans == "DELETE_MACHINERY") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "Machinery ID is required",
            "data" => null
        ]);
        exit;
    }

    // Prevent deletion while there are active bookings (status 0,1,2)
    $active = mysqli_query($conn, "
        SELECT id FROM booking
        WHERE machinery_id = '$id' AND status IN ('0','1','2')
        LIMIT 1
    ");

    if ($active && mysqli_num_rows($active) > 0) {
        echo json_encode([
            "code" => 1,
            "message" => "Cannot delete machinery with active bookings",
            "data" => null
        ]);
        exit;
    }

    // Remove images from disk
    $imgRes = mysqli_query($conn, "SELECT name FROM machinery_images WHERE machinery_id = '$id'");
    while ($row = mysqli_fetch_assoc($imgRes)) {
        $filePath = "../assets/images/machinery/" . $row['name'];
        if ($row['name'] && file_exists($filePath)) {
            unlink($filePath);
        }
    }

    mysqli_query($conn, "DELETE FROM machinery_images WHERE machinery_id = '$id'");
    mysqli_query($conn, "DELETE FROM machinery_maintenance WHERE machinery_id = '$id'");

    $del = mysqli_query($conn, "DELETE FROM machinery WHERE id = '$id'");

    if ($del) {
        echo json_encode([
            "code" => 0,
            "message" => "Machinery deleted successfully",
            "data" => null
        ]);
    } else {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
    }
    exit;
} else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
    exit;
}
