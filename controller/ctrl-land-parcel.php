<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

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

if ($trans == "ADD_LAND_PARCEL") {

    $beneficiary_id      = $data['beneficiary_id'] ?? '';
    $title_number        = $data['title_number'] ?? '';
    $total_area_hectares = $data['total_area_hectares'] ?? '';
    $latitude            = $data['latitude'] ?? '';
    $longitude           = $data['longitude'] ?? '';
    $land_use_type       = $data['land_use_type'] ?? '';
    $productivity_score  = $data['productivity_score'] ?? '';
    $last_survey_date    = $data['last_survey_date'] ?? '';

    if (empty($beneficiary_id) || empty($title_number)) {
        echo json_encode([
            "code" => 1,
            "message" => "Beneficiary and Title Number are required",
            "data" => null
        ]);
        exit;
    }

    $query = "
        INSERT INTO cocrom_land_parcels
        (beneficiary_id, title_number, total_area_hectares, latitude, longitude, land_use_type, productivity_score, last_survey_date, created_at, updated_at)
        VALUES
        ('$beneficiary_id', '$title_number', '$total_area_hectares', '$latitude', '$longitude', '$land_use_type', '$productivity_score', '$last_survey_date', NOW(), NOW())
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

    echo json_encode([
        "code" => 0,
        "message" => "Land parcel added",
        "data" => [
            "id" => mysqli_insert_id($conn)
        ]
    ]);
    exit;
} else if ($trans == "EDIT_LAND_PARCEL") {

    $id                  = $data['id'] ?? '';
    $beneficiary_id      = $data['beneficiary_id'] ?? '';
    $title_number        = $data['title_number'] ?? '';
    $total_area_hectares = $data['total_area_hectares'] ?? '';
    $latitude            = $data['latitude'] ?? '';
    $longitude           = $data['longitude'] ?? '';
    $land_use_type       = $data['land_use_type'] ?? '';
    $productivity_score  = $data['productivity_score'] ?? '';
    $last_survey_date    = $data['last_survey_date'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $query = "
        UPDATE cocrom_land_parcels
        SET 
            beneficiary_id='$beneficiary_id',
            title_number='$title_number',
            total_area_hectares='$total_area_hectares',
            latitude='$latitude',
            longitude='$longitude',
            land_use_type='$land_use_type',
            productivity_score='$productivity_score',
            last_survey_date='$last_survey_date',
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

    echo json_encode([
        "code" => 0,
        "message" => "Land parcel updated",
        "data" => null
    ]);
    exit;
} else if ($trans == "LIST_LAND_PARCEL") {

    $query = "
        SELECT 
            lp.id,
            lp.beneficiary_id,
            CONCAT(b.fname, ' ', b.mname, ' ', b.lname) AS beneficiary_name,
            lp.title_number,
            lp.total_area_hectares,
            lp.latitude,
            lp.longitude,
            lp.land_use_type,
            lp.productivity_score,
            lp.last_survey_date,
            lp.created_at,
            lp.updated_at
        FROM cocrom_land_parcels lp
        LEFT JOIN beneficiary b ON b.id = lp.beneficiary_id
        ORDER BY lp.id DESC
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
} else if ($trans == "GET_PARCEL_DETAIL") {

    $id = $data['id'] ?? 0;

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        exit;
    }

    // Get parcel info with beneficiary
    $parcelQuery = "
        SELECT 
            lp.*,
            CONCAT(b.fname, ' ', b.mname, ' ', b.lname) AS beneficiary_name,
            b.fname AS b_fname,
            b.mname AS b_mname,
            b.lname AS b_lname
        FROM cocrom_land_parcels lp
        LEFT JOIN beneficiary b ON b.id = lp.beneficiary_id
        WHERE lp.id = '$id'
    ";
    $parcelResult = mysqli_query($conn, $parcelQuery);
    $parcel = mysqli_fetch_assoc($parcelResult);

    if (!$parcel) {
        echo json_encode(["code" => 1, "message" => "Parcel not found"]);
        exit;
    }

    // Get certificates/records for this parcel
    $certs = [];
    $certResult = mysqli_query($conn, "
        SELECT * FROM cocrom_records 
        WHERE land_parcel_id = '$id' 
        ORDER BY id DESC
    ");
    while ($row = mysqli_fetch_assoc($certResult)) {
        $certs[] = $row;
    }

    // Get monitoring logs for this parcel
    $monitors = [];
    $monResult = mysqli_query($conn, "
        SELECT 
            m.*,
            CONCAT(e.fname, ' ', e.lname) AS employee_name
        FROM cocrom_land_monitoring m
        LEFT JOIN users u ON u.id = m.employee_id
        LEFT JOIN employee e ON e.users_id = u.id
        WHERE m.land_parcels_id = '$id'
        ORDER BY m.id DESC
    ");
    while ($row = mysqli_fetch_assoc($monResult)) {
        // Get images for each monitoring log
        $imgs = [];
        $imgResult = mysqli_query($conn, "
            SELECT * FROM cocrom_land_images 
            WHERE land_monitoring_id = '{$row['id']}' 
            ORDER BY is_primary DESC
        ");
        while ($img = mysqli_fetch_assoc($imgResult)) {
            $imgs[] = $img;
        }
        $row['images'] = $imgs;
        $monitors[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => [
            "parcel" => $parcel,
            "certificates" => $certs,
            "monitoring_logs" => $monitors
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
