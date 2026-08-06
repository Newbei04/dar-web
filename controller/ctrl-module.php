<?php

session_start();
date_default_timezone_set("Asia/Manila");

include __DIR__ . '/connect.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$trans = $data['trans'] ?? '';

/*
|--------------------------------------------------------------------------
| ADD MODULE
|--------------------------------------------------------------------------
*/
if ($trans == "ADD_MODULE") {

    $parent_id  = $data['parent_id'] ?? 0;
    $title      = mysqli_real_escape_string($conn, trim($data['title']));
    $icon       = mysqli_real_escape_string($conn, trim($data['icon']));
    $page       = mysqli_real_escape_string($conn, trim($data['page']));
    $filename   = mysqli_real_escape_string($conn, trim($data['filename']));
    $sort_order = $data['sort_order'] ?? 0;
    $is_menu    = $data['is_menu'] ?? 1;

    if ($title == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Module title is required."
        ]);
        exit;
    }

    $check = mysqli_query($conn, "
        SELECT id
        FROM user_modules
        WHERE
            title='$title'
            AND parent_id='$parent_id'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {

        echo json_encode([
            "code" => 1,
            "message" => "Module already exists."
        ]);
        exit;
    }

    mysqli_query($conn, "
        INSERT INTO user_modules
        (
            parent_id,
            title,
            icon,
            page,
            filename,
            sort_order,
            is_menu,
            status,
            created_at,
            updated_at
        )
        VALUES
        (
            '$parent_id',
            '$title',
            '$icon',
            '$page',
            '$filename',
            '$sort_order',
            '$is_menu',
            '1',
            NOW(),
            NOW()
        )
    ");

    echo json_encode([
        "code" => 0,
        "message" => "Module added successfully."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| LIST MODULE
|--------------------------------------------------------------------------
*/ else if ($trans == "LIST_MODULE") {

    $query = mysqli_query($conn, "
        SELECT
            m.*,
            IFNULL(p.title,'-') AS parent_name
        FROM user_modules m
        LEFT JOIN user_modules p
            ON p.id = m.parent_id
        WHERE m.status = 1
        ORDER BY
            m.parent_id ASC,
            m.sort_order ASC,
            m.title ASC
    ");

    $list = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $row['menu'] = $row['is_menu'] == 1 ? "Yes" : "No";

        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| GET MODULE
|--------------------------------------------------------------------------
*/ else if ($trans == "GET_MODULE") {

    $id = $data['id'] ?? '';

    if ($id == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Module ID is required."
        ]);

        exit;
    }

    $query = mysqli_query($conn, "
        SELECT
            m.*,
            IFNULL(p.title,'-') AS parent_name
        FROM user_modules m
        LEFT JOIN user_modules p
            ON p.id = m.parent_id
        WHERE
            m.id='$id'
        LIMIT 1
    ");

    if (mysqli_num_rows($query) == 0) {

        echo json_encode([
            "code" => 1,
            "message" => "Module not found."
        ]);

        exit;
    }

    $row = mysqli_fetch_assoc($query);

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $row
    ]);

    exit;
}
/*
|--------------------------------------------------------------------------
| UPDATE MODULE
|--------------------------------------------------------------------------
*/ else if ($trans == "UPDATE_MODULE") {

    $id         = $data['id'] ?? '';
    $parent_id  = $data['parent_id'] ?? 0;
    $title      = mysqli_real_escape_string($conn, trim($data['title']));
    $icon       = mysqli_real_escape_string($conn, trim($data['icon']));
    $page       = mysqli_real_escape_string($conn, trim($data['page']));
    $filename   = mysqli_real_escape_string($conn, trim($data['filename']));
    $sort_order = $data['sort_order'] ?? 0;
    $is_menu    = $data['is_menu'] ?? 1;
    $status     = $data['status'] ?? 1;

    if ($id == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Module ID is required."
        ]);

        exit;
    }

    if ($title == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Module title is required."
        ]);

        exit;
    }

    $check = mysqli_query($conn, "
        SELECT id
        FROM user_modules
        WHERE
            title='$title'
            AND parent_id='$parent_id'
            AND id<>'$id'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {

        echo json_encode([
            "code" => 1,
            "message" => "Module already exists."
        ]);

        exit;
    }

    mysqli_query($conn, "
        UPDATE user_modules
        SET

            parent_id='$parent_id',
            title='$title',
            icon='$icon',
            page='$page',
            filename='$filename',
            sort_order='$sort_order',
            is_menu='$is_menu',
            status='$status',
            updated_at=NOW()

        WHERE id='$id'
    ");

    echo json_encode([
        "code" => 0,
        "message" => "Module updated successfully."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| REORDER MODULE
|--------------------------------------------------------------------------
*/ else if ($trans == "REORDER_MODULE") {

    $order = $data['order'] ?? [];

    if (!is_array($order) || count($order) == 0) {

        echo json_encode([
            "code" => 1,
            "message" => "Module order is required."
        ]);

        exit;
    }

    $errors = 0;
    $sortCounters = [];

    foreach ($order as $item) {

        if (is_array($item)) {
            $id        = (int) ($item['id'] ?? 0);
            $parent_id = (int) ($item['parent_id'] ?? 0);
        } else {
            $id        = (int) $item;
            $parent_id = 0;
        }

        if ($id == 0 || $id == $parent_id) {
            continue;
        }

        if (!isset($sortCounters[$parent_id])) {
            $sortCounters[$parent_id] = 0;
        }

        $sortCounters[$parent_id]++;

        $sort_order = $sortCounters[$parent_id];

        $result = mysqli_query($conn, "
            UPDATE user_modules
            SET
                parent_id='$parent_id',
                sort_order='$sort_order',
                updated_at=NOW()
            WHERE id='$id'
        ");

        if (!$result) {
            $errors++;
        }
    }

    if ($errors > 0) {

        echo json_encode([
            "code" => 1,
            "message" => "Some modules failed to reorder."
        ]);

        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Module order updated successfully."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| DELETE MODULE
|--------------------------------------------------------------------------
*/ else if ($trans == "DELETE_MODULE") {

    $id = $data['id'] ?? '';

    if ($id == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Module ID is required."
        ]);

        exit;
    }

    mysqli_query($conn, "
        UPDATE user_modules
        SET
            status='0',
            updated_at=NOW()
        WHERE id='$id'
    ");

    echo json_encode([
        "code" => 0,
        "message" => "Module deleted successfully."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| INVALID TRANSACTION
|--------------------------------------------------------------------------
*/ else {

    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction."
    ]);

    exit;
}