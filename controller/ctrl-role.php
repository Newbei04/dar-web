<?php

session_start();
date_default_timezone_set("Asia/Manila");

include __DIR__ . '/connect.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$trans = $data['trans'] ?? '';

/*
|--------------------------------------------------------------------------
| LIST USER ROLES
|--------------------------------------------------------------------------
*/
if ($trans == "LIST_USER_ROLES") {

    $query = mysqli_query($conn, "
        SELECT *
        FROM users_role
        ORDER BY id ASC
    ");

    $list = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $access = array_filter(array_map('intval', explode(',', $row['access'] ?? '')));
        $row['access_count'] = count($access);
        $row['status_label'] = $row['status'] == 1 ? "Active" : "Inactive";

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
| GET ROLE DETAIL + MODULES (for access editor)
|--------------------------------------------------------------------------
*/
else if ($trans == "GET_ROLE_DETAIL") {

    $id = $data['id'] ?? '';

    if ($id == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Role ID is required."
        ]);

        exit;
    }

    $query = mysqli_query($conn, "
        SELECT *
        FROM users_role
        WHERE id='$id'
        LIMIT 1
    ");

    if (mysqli_num_rows($query) == 0) {

        echo json_encode([
            "code" => 1,
            "message" => "Role not found."
        ]);

        exit;
    }

    $role = mysqli_fetch_assoc($query);

    $allowed = array_filter(array_map('intval', explode(',', $role['access'] ?? '')));

    $modQuery = mysqli_query($conn, "
        SELECT
            id,
            parent_id,
            title,
            icon,
            page,
            sort_order,
            is_menu
        FROM user_modules
        WHERE status = 1
        ORDER BY parent_id ASC, sort_order ASC, title ASC
    ");

    $modules = [];

    while ($m = mysqli_fetch_assoc($modQuery)) {

        $m['allowed'] = in_array((int) $m['id'], $allowed) ? 1 : 0;

        $modules[] = $m;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "role" => $role,
            "modules" => $modules
        ]
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| LIST MODULES (for the add-role access editor)
|--------------------------------------------------------------------------
*/
else if ($trans == "LIST_MODULES") {

    $modQuery = mysqli_query($conn, "
        SELECT
            id,
            parent_id,
            title,
            icon,
            page,
            sort_order,
            is_menu
        FROM user_modules
        WHERE status = 1
        ORDER BY parent_id ASC, sort_order ASC, title ASC
    ");

    $modules = [];

    while ($m = mysqli_fetch_assoc($modQuery)) {
        $m['allowed'] = 0;
        $modules[] = $m;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $modules
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE ROLE ACCESS
|--------------------------------------------------------------------------
*/
else if ($trans == "UPDATE_ROLE_ACCESS") {

    $id       = $data['id'] ?? '';
    $title    = mysqli_real_escape_string($conn, trim($data['title']));
    $desc     = mysqli_real_escape_string($conn, trim($data['description']));
    $status   = $data['status'] ?? 1;
    $access   = $data['access'] ?? [];

    if ($id == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Role ID is required."
        ]);

        exit;
    }

    if ($title == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Role title is required."
        ]);

        exit;
    }

    if (!is_array($access)) {
        $access = [];
    }

    $access = array_values(array_unique(array_map('intval', $access)));
    sort($access);

    $accessStr = implode(',', $access);

    mysqli_query($conn, "
        UPDATE users_role
        SET
            title='$title',
            description='$desc',
            access='$accessStr',
            status='$status',
            updated_dt=NOW()
        WHERE id='$id'
    ");

    echo json_encode([
        "code" => 0,
        "message" => "Role access updated successfully."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| ADD ROLE
|--------------------------------------------------------------------------
*/
else if ($trans == "ADD_ROLE") {

    $title    = trim($data['title'] ?? '');
    $desc     = trim($data['description'] ?? '');
    $status   = $data['status'] ?? 1;
    $access   = $data['access'] ?? [];

    if ($title == "") {

        echo json_encode([
            "code" => 1,
            "message" => "Role title is required."
        ]);

        exit;
    }

    $titleEsc = mysqli_real_escape_string($conn, $title);
    $descEsc  = mysqli_real_escape_string($conn, $desc);

    $dup = mysqli_query($conn, "
        SELECT id
        FROM users_role
        WHERE title='$titleEsc'
        LIMIT 1
    ");

    if (mysqli_num_rows($dup) > 0) {

        echo json_encode([
            "code" => 1,
            "message" => "Role title already exists."
        ]);

        exit;
    }

    if (!is_array($access)) {
        $access = [];
    }

    $access = array_values(array_unique(array_map('intval', $access)));
    sort($access);

    $accessStr = implode(',', $access);

    mysqli_query($conn, "
        INSERT INTO users_role (title, description, access, status, created_dt, updated_dt)
        VALUES ('$titleEsc', '$descEsc', '$accessStr', '$status', NOW(), NOW())
    ");

    echo json_encode([
        "code" => 0,
        "message" => "Role created successfully."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| INVALID TRANSACTION
|--------------------------------------------------------------------------
*/
else {

    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction."
    ]);

    exit;
}
