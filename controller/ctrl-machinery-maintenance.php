<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data  = json_decode(file_get_contents("php://input"), true);
$trans = $_GET['trans'] ?? ($data['trans'] ?? '');

if (!$conn) {
    echo json_encode([
        "code" => 0,
        "message" => "Database connection failed",
        "data" => null
    ]);
    exit;
}

if ($trans == "ADD_MAINTENANCE") {

    $machinery_id       = $data['machinery_id'] ?? '';
    $emp_id             = $data['emp_id'] ?? null;
    $facility_id        = $data['facility_id'] ?? '';
    $type               = $data['type'] ?? '';
    $priority           = $data['priority'] ?? '';
    $start_date         = $data['start_date'] ?? null;
    $end_date           = $data['end_date'] ?? null;
    $labor_cost         = $data['labor_cost'] ?? 0;
    $parts_cost         = $data['parts_cost'] ?? 0;
    $odometer_reading   = $data['odometer_reading'] ?? 0;
    $description        = $data['description'] ?? '';

    // $total_cost = $labor_cost + $parts_cost;

    $status = 1; // Scheduled

    if (empty($machinery_id) || empty($facility_id) || empty($type) || empty($priority)) {
        echo json_encode([
            "code" => 0,
            "message" => "Missing required fields",
            "data" => null
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO machinery_maintenance
        (
            machinery_id,
            emp_id,
            facility_id,
            type,
            priority,
            start_date,
            end_date,
            labor_cost,
            parts_cost,
            odometer_reading,
            description,
            status,
            created_at
        )
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())
    ");

    $stmt->bind_param(
        "iissssssdssi",
        $machinery_id,
        $emp_id,
        $facility_id,
        $type,
        $priority,
        $start_date,
        $end_date,
        $labor_cost,
        $parts_cost,
        $odometer_reading,
        $description,
        $status
    );

    if ($stmt->execute()) {

        // 🔥 SET MACHINERY = UNDER MAINTENANCE
        $conn->query("
            UPDATE machinery 
            SET status = 5 
            WHERE id = '$machinery_id'
        ");

        echo json_encode([
            "code" => 1,
            "message" => "Maintenance added and machinery set to Under Maintenance",
            "data" => ["id" => $stmt->insert_id]
        ]);
    } else {
        echo json_encode([
            "code" => 0,
            "message" => "Failed to add maintenance",
            "data" => $stmt->error
        ]);
    }

    exit;
} else if ($trans == "UPDATE_MAINTENANCE_STATUS") {

    $id = $data['id'] ?? '';
    $status = $data['status'] ?? '';

    if (empty($id) || empty($status)) {
        echo json_encode([
            "code" => 0,
            "message" => "Missing parameters",
            "data" => null
        ]);
        exit;
    }

    // update maintenance status
    $stmt = $conn->prepare("
        UPDATE machinery_maintenance
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();

    // if completed → set machinery available
    if ($status == 3) {

        $res = $conn->query("
            SELECT machinery_id 
            FROM machinery_maintenance 
            WHERE id = '$id'
        ");

        $row = $res->fetch_assoc();
        $machinery_id = $row['machinery_id'] ?? null;

        if ($machinery_id) {
            $conn->query("
                UPDATE machinery 
                SET status = 1 
                WHERE id = '$machinery_id'
            ");
        }
    }

    echo json_encode([
        "code" => 1,
        "message" => "Maintenance status updated",
        "data" => null
    ]);

    exit;
} else if ($trans == "LIST_MAINTENANCE") {

    $facility_id   = $data['facility_id'] ?? '';
    $machinery_id  = $data['machinery_id'] ?? '';

    $where = "WHERE 1=1";

    if (!empty($facility_id)) {
        $facility_id = (int)$facility_id;
        $where .= " AND mm.facility_id = '$facility_id'";
    }

    if (!empty($machinery_id)) {
        $machinery_id = (int)$machinery_id;
        $where .= " AND mm.machinery_id = '$machinery_id'";
    }

    $sql = "
        SELECT 
            mm.*,
            m.name AS machinery_name,
            f.name AS facility_name,
            (SELECT name FROM machinery_images WHERE machinery_id = m.id AND is_primary = 1 LIMIT 1) AS image
        FROM machinery_maintenance mm
        LEFT JOIN machinery m ON m.id = mm.machinery_id
        LEFT JOIN facility f ON f.id = mm.facility_id
        $where
        ORDER BY mm.created_at DESC
    ";

    $result = $conn->query($sql);

    $data = [];

    $today = date('Y-m-d');

    while ($row = $result->fetch_assoc()) {

        /* =========================
           LABEL MAPPING
        ========================== */
        $type_map = [
            "1" => "Preventive",
            "2" => "Corrective",
            "3" => "Corrective",
            "4" => "Emergency",
            "5" => "Inspection"
        ];

        $priority_map = [
            "1" => "Low",
            "2" => "Medium",
            "3" => "High",
            "4" => "Critical"
        ];

        $status_map = [
            "1" => "Scheduled",
            "2" => "In Progress",
            "3" => "Completed",
            "4" => "Awaiting Parts"
        ];

        $row['type_label']     = $type_map[$row['type']] ?? $row['type'];
        $row['priority_label'] = $priority_map[$row['priority']] ?? $row['priority'];

        /* =========================
           AUTO STATUS LOGIC
        ========================== */

        if ($row['status'] == 3) {
            $row['status_label'] = "Completed";
        } else if (
            !empty($row['start_date']) &&
            !empty($row['end_date']) &&
            $row['start_date'] <= $today &&
            $row['end_date'] >= $today
        ) {
            $row['status_label'] = "In Progress";
        } else {
            $row['status_label'] = "Scheduled";
        }

        /* =========================
           UI STATUS FLAG
        ========================== */
        $row['ui_status'] =
            ($row['status'] == 3) ? 3 : (($row['start_date'] <= $today && $row['end_date'] >= $today) ? 2 : 1);

        $data[] = $row;
    }

    echo json_encode([
        "code" => 1,
        "message" => "Success",
        "data" => $data
    ]);

    exit;
} echo json_encode([
    "code" => 0,
    "message" => "Invalid transaction",
    "data" => null
]);
