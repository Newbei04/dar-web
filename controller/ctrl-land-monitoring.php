<?php

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$trans = $_POST['trans'] ?? $_GET['trans'] ?? ($data['trans'] ?? '');

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

# =========================
# ADD MONITORING
# =========================
if ($trans == "ADD_MONITORING") {
    
    $land_parcels_id = $_POST['land_parcels_id'] ?? '';
    $employee_id     = $_POST['employee_id'] ?? '';
    $log_type        = $_POST['log_type'] ?? '';
    $title           = $_POST['title'] ?? '';
    $notes           = $_POST['notes'] ?? '';

    if ($land_parcels_id === '' || $employee_id === '' || $log_type === '') {
        echo json_encode([
            "code" => 1,
            "message" => "Land Parcel, Employee, and Log Type are required",
            "data" => null
        ]);
        exit;
    }

    $uploadedImages = [];

    // ==========================
    // UPLOAD MULTIPLE IMAGES
    // ==========================
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {

        $uploadDir = __DIR__ . '/../assets/images/monitoring/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {

            if ($_FILES['images']['error'][$key] == 0) {

                $originalName = $_FILES['images']['name'][$key];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                $fileName = uniqid('MON_') . '.' . $extension;

                if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                    $uploadedImages[] = $fileName;
                }
            }
        }
    }

    $query = "
        INSERT INTO cocrom_land_monitoring
        (
            employee_id,
            land_parcels_id,
            log_type,
            title,
            notes,
            created_at,
            updated_at
        )
        VALUES
        (
            '$employee_id',
            '$land_parcels_id',
            '$log_type',
            '$title',
            '$notes',
            NOW(),
            NOW()
        )
    ";

    $insert = mysqli_query($conn, $query);

    if (!$insert) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    $monitoringId = mysqli_insert_id($conn);

    // ==========================
    // INSERT INTO cocrom_land_images
    // ==========================
    foreach ($uploadedImages as $idx => $imgName) {
        $isPrimary = ($idx === 0) ? 1 : 0;
        mysqli_query($conn, "
            INSERT INTO cocrom_land_images
                (land_monitoring_id, name, is_primary, status, created_at, updated_at)
            VALUES
                ('$monitoringId', '$imgName', $isPrimary, 1, NOW(), NOW())
        ");
    }

    echo json_encode([
        "code" => 0,
        "message" => "Monitoring log added successfully",
        "data" => [
            "id" => $monitoringId,
            "images" => $uploadedImages
        ]
    ]);
    exit;
} else if ($trans == "EDIT_MONITORING") {

    $id              = $_POST['id'] ?? $data['id'] ?? '';
    $land_parcels_id = $_POST['land_parcels_id'] ?? $data['land_parcels_id'] ?? '';
    $employee_id     = $_POST['employee_id'] ?? $data['employee_id'] ?? '';
    $log_type        = $_POST['log_type'] ?? $data['log_type'] ?? '';
    $title           = $_POST['title'] ?? $data['title'] ?? '';
    $notes           = $_POST['notes'] ?? $data['notes'] ?? '';
    $deleteImages    = $_POST['delete_images'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $query = "
        UPDATE cocrom_land_monitoring
        SET 
            land_parcels_id='$land_parcels_id',
            employee_id='$employee_id',
            log_type='$log_type',
            title='$title',
            notes='$notes',
            updated_at=NOW()
        WHERE id='$id'
    ";

    $update = mysqli_query($conn, $query);

    if (!$update) {
        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn),
            "data" => null
        ]);
        exit;
    }

    // ==========================
    // DELETE REMOVED IMAGES
    // ==========================
    if (!empty($deleteImages)) {
        $delArr = array_filter(explode(',', $deleteImages));
        $uploadDir = __DIR__ . '/../assets/images/monitoring/';
        foreach ($delArr as $delName) {
            $delName = trim($delName);
            if (!empty($delName)) {
                // Delete file from disk
                $filePath = $uploadDir . $delName;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                // Delete from DB
                mysqli_query($conn, "DELETE FROM cocrom_land_images WHERE land_monitoring_id = '$id' AND name = '$delName'");
            }
        }
    }

    // ==========================
    // UPLOAD NEW IMAGES
    // ==========================
    if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
        $uploadDir = __DIR__ . '/../assets/images/monitoring/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['new_images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['new_images']['error'][$key] == 0) {
                $originalName = $_FILES['new_images']['name'][$key];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $fileName = uniqid('MON_') . '.' . $extension;

                if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                    // Check if this is the first image (primary)
                    $countResult = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM cocrom_land_images WHERE land_monitoring_id = '$id'");
                    $countRow = mysqli_fetch_assoc($countResult);
                    $isPrimary = ($countRow['cnt'] == 0) ? 1 : 0;

                    mysqli_query($conn, "
                        INSERT INTO cocrom_land_images
                            (land_monitoring_id, name, is_primary, status, created_at, updated_at)
                        VALUES
                            ('$id', '$fileName', $isPrimary, 1, NOW(), NOW())
                    ");
                }
            }
        }
    }

    echo json_encode([
        "code" => 0,
        "message" => "Monitoring updated",
        "data" => null
    ]);
    exit;
} else if ($trans == "LIST_MONITORING") {

    $query = "
        SELECT 
            m.id,
            m.employee_id,
            CONCAT(e.fname, ' ', e.lname) AS employee_name,
            m.land_parcels_id,
            lp.title_number,
            m.log_type,
            m.title,
            m.notes,
            m.created_at,
            m.updated_at
        FROM cocrom_land_monitoring m
        LEFT JOIN users u ON u.id = m.employee_id
        LEFT JOIN employee e ON e.users_id = u.id
        LEFT JOIN cocrom_land_parcels lp ON lp.id = m.land_parcels_id
        ORDER BY m.id DESC
    ";

    $result = mysqli_query($conn, $query);

    $list = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
    exit;
}

# =========================
# GET MONITORING
# =========================
else if ($trans == "GET_MONITORING") {

    $id = $data['id'] ?? 0;

    $query = "
        SELECT 
            m.id,
            m.employee_id,
            CONCAT(e.fname, ' ', e.lname) AS employee_name,
            m.land_parcels_id,
            lp.title_number,
            m.log_type,
            m.title,
            m.notes,
            m.created_at,
            m.updated_at
        FROM cocrom_land_monitoring m
        LEFT JOIN users u ON u.id = m.employee_id
        LEFT JOIN employee e ON e.users_id = u.id
        LEFT JOIN cocrom_land_parcels lp ON lp.id = m.land_parcels_id
        WHERE m.id = '$id'
    ";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Get images from cocrom_land_images
    $images = [];
    $imgResult = mysqli_query($conn, "SELECT * FROM cocrom_land_images WHERE land_monitoring_id = '$id' ORDER BY is_primary DESC");
    while ($imgRow = mysqli_fetch_assoc($imgResult)) {
        $images[] = $imgRow;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $row ? array_merge($row, ['images' => $images]) : null
    ]);
    exit;
}

# =========================
# DELETE MONITORING
# =========================
else if ($trans == "DELETE_MONITORING") {

    $id = $data['id'] ?? 0;

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        exit;
    }

    // Delete images first
    mysqli_query($conn, "DELETE FROM cocrom_land_images WHERE land_monitoring_id = '$id'");

    // Delete monitoring record
    mysqli_query($conn, "DELETE FROM cocrom_land_monitoring WHERE id = '$id'");

    echo json_encode(["code" => 0, "message" => "Monitoring log deleted"]);
    exit;
}

# =========================
# INVALID TRANS
# =========================
else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
    exit;
}
