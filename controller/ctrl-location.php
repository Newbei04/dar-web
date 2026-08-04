<?php
header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$input = json_decode(file_get_contents('php://input'), true);
$trans = $input['trans'] ?? '';

if (!$conn) {
    echo json_encode(["code" => 1, "message" => "Database connection failed"]);
    exit;
}

if ($trans == "region") {

    $res = mysqli_query($conn, "SELECT code,name FROM region ORDER BY name ASC");
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $data]);
    exit;

} else if ($trans == "province") {

    $code = mysqli_real_escape_string($conn, $input['code'] ?? '');

    $res = mysqli_query($conn, "
        SELECT code,name 
        FROM province 
        WHERE region_code='$code'
        ORDER BY name ASC
    ");

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $data]);
    exit;

} else if ($trans == "city") {

    $code = mysqli_real_escape_string($conn, $input['code'] ?? '');

    $res = mysqli_query($conn, "
        SELECT code,name,province_code,region_code
        FROM municipality
        WHERE province_code='$code'
        ORDER BY name ASC
    ");

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $data]);
    exit;

} else if ($trans == "ncr_city") {

    $NCR_CODE = "130000000";

    $res = mysqli_query($conn, "
        SELECT code,name 
        FROM municipality
        WHERE region_code='$NCR_CODE'
        ORDER BY name ASC
    ");

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $data]);
    exit;

} else if ($trans == "district") {

    $res = mysqli_query($conn, "SELECT code,name FROM district ORDER BY name ASC");

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $data]);
    exit;

} else if ($trans == "barangay") {

    $code = mysqli_real_escape_string($conn, $input['code'] ?? '');

    $res = mysqli_query($conn, "
        SELECT code,name 
        FROM barangay
        WHERE municipality_code='$code' OR city_code='$code'
        ORDER BY name ASC
    ");

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $data]);
    exit;

} else if ($trans == "barangay_district") {

    $code = mysqli_real_escape_string($conn, $input['code'] ?? '');

    $res = mysqli_query($conn, "
        SELECT code,name 
        FROM barangay
        WHERE sub_municipality_code='$code'
        ORDER BY name ASC
    ");

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    echo json_encode(["code" => 0, "message" => "Success", "data" => $data]);
    exit;
}

echo json_encode(["code" => 1, "message" => "Invalid transaction"]);
