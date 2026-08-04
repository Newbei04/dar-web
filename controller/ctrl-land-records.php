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

# =========================
# ADD RECORD
# =========================
if ($trans == "ADD_RECORD") {

    $land_parcel_id       = $data['land_parcel_id'] ?? '';
    $beneficiary_id       = $data['beneficiary_id'] ?? '';
    $credit_limit         = $data['credit_limit'] ?? '';
    $certificate_status   = $data['certificate_status'] ?? '';
    $issued_date          = $data['issued_date'] ?? '';
    $expiry_date          = $data['expiry_date'] ?? '';
    $encrypted_signature  = $data['encrypted_signature'] ?? '';

    if (empty($land_parcel_id)) {
        echo json_encode([
            "code" => 1,
            "message" => "Land Parcel ID is required",
            "data" => null
        ]);
        exit;
    }

    $query = "
        INSERT INTO cocrom_records
        (land_parcel_id, beneficiary_id, credit_limit, certificate_status, issued_date, expiry_date, encrypted_signature)
        VALUES
        ('$land_parcel_id', '$beneficiary_id', '$credit_limit', '$certificate_status', '$issued_date', '$expiry_date', '$encrypted_signature')
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
        "message" => "Record added",
        "data" => [
            "id" => mysqli_insert_id($conn)
        ]
    ]);
    exit;
}

# =========================
# EDIT RECORD
# =========================
else if ($trans == "EDIT_RECORD") {

    $id                  = $data['id'] ?? '';
    $land_parcel_id      = $data['land_parcel_id'] ?? '';
    $credit_limit        = $data['credit_limit'] ?? '';
    $certificate_status  = $data['certificate_status'] ?? '';
    $issued_date         = $data['issued_date'] ?? '';
    $expiry_date         = $data['expiry_date'] ?? '';
    $encrypted_signature = $data['encrypted_signature'] ?? '';

    if (empty($id)) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required",
            "data" => null
        ]);
        exit;
    }

    $query = "
        UPDATE cocrom_records
        SET 
            land_parcel_id='$land_parcel_id',
            credit_limit='$credit_limit',
            certificate_status='$certificate_status',
            issued_date='$issued_date',
            expiry_date='$expiry_date',
            encrypted_signature='$encrypted_signature'
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
        "message" => "Record updated",
        "data" => null
    ]);
    exit;
} else if ($trans == "LIST_RECORDS") {

    $query = "
        SELECT 
            r.id,
            r.land_parcel_id,
            lp.title_number,
            r.credit_limit,
            r.certificate_status,
            r.issued_date,
            r.expiry_date,
            r.encrypted_signature
        FROM cocrom_records r
        LEFT JOIN cocrom_land_parcels lp ON lp.id = r.land_parcel_id
        ORDER BY r.id DESC
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
# =========================
# GET RECORD DETAIL
# =========================
} else if ($trans == "GET_RECORD_DETAIL") {

    $id = $data['id'] ?? 0;

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        exit;
    }

    $query = "
        SELECT 
            r.*,
            lp.title_number,
            CONCAT(b.fname, ' ', b.mname, ' ', b.lname) AS beneficiary_name
        FROM cocrom_records r
        LEFT JOIN cocrom_land_parcels lp ON lp.id = r.land_parcel_id
        LEFT JOIN beneficiary b ON b.id = r.beneficiary_id
        WHERE r.id = '$id'
    ";

    $result = mysqli_query($conn, $query);
    $record = mysqli_fetch_assoc($result);

    if (!$record) {
        echo json_encode(["code" => 1, "message" => "Record not found"]);
        exit;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $record
    ]);
    exit;
} else if ($trans == "DELETE_RECORD") {

    $id = $data['id'] ?? 0;

    if (empty($id)) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        exit;
    }

    mysqli_query($conn, "DELETE FROM cocrom_records WHERE id = '$id'");

    echo json_encode(["code" => 0, "message" => "Record deleted"]);
    exit;
} else if ($trans == "GET_RECORDS_BY_PARCEL") {

    $land_parcel_id = $data['land_parcel_id'] ?? 0;

    if (empty($land_parcel_id)) {
        echo json_encode(["code" => 1, "message" => "Land Parcel ID is required"]);
        exit;
    }

    $query = "
        SELECT 
            r.*,
            lp.title_number,
            CONCAT(b.fname, ' ', b.mname, ' ', b.lname) AS beneficiary_name
        FROM cocrom_records r
        LEFT JOIN cocrom_land_parcels lp ON lp.id = r.land_parcel_id
        LEFT JOIN beneficiary b ON b.id = r.beneficiary_id
        WHERE r.land_parcel_id = '$land_parcel_id'
        ORDER BY r.id DESC
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
} else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
    exit;
}
