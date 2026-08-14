<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/connect.php';

$data  = $_POST ? $_POST : json_decode(file_get_contents("php://input"), true);
$trans = $data['trans'] ?? '';

if ($trans == "ADD_TRAINING") {

    $reference_no = "TRN-" . time();

    // ================= TEXT FIELDS =================
    $accreditation_no   = mysqli_real_escape_string($conn, $_POST['accreditation_no'] ?? '');
    $accreditation_date = $_POST['accreditation_date'] ?? null;

    $title        = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
    $summary      = mysqli_real_escape_string($conn, $_POST['summary'] ?? '');
    $objectives   = mysqli_real_escape_string($conn, $_POST['objectives'] ?? '');

    $start_at     = $_POST['start_at'] ?? null;
    $end_at       = $_POST['end_at'] ?? null;

    $program_type = $_POST['program_type'] ?? '';

    $street       = mysqli_real_escape_string($conn, $_POST['street'] ?? '');
    $barangay_id  = $_POST['barangay_id'] ?? null;
    $city_id      = $_POST['city_id'] ?? null;
    $province_id  = $_POST['province_id'] ?? null;
    $region_id    = $_POST['region_id'] ?? null;

    // ================= VALIDATION =================
    if (!$title || !$accreditation_no) {
        echo json_encode([
            "code" => 1,
            "message" => "Accreditation Number and Title are required"
        ]);
        return;
    }

    // ================= PROMOTIONAL IMAGE UPLOAD =================
    $image_name = null;

    if (isset($_FILES['promotional_image']) && $_FILES['promotional_image']['error'] == 0) {

        $uploadDir = __DIR__ . "/../assets/images/training/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['promotional_image']['name'], PATHINFO_EXTENSION));

        $image_name = "TRN_IMG_" . time() . "." . $ext;

        move_uploaded_file(
            $_FILES['promotional_image']['tmp_name'],
            $uploadDir . $image_name
        );
    }

    // ================= DISCUSSION FILE UPLOAD =================
    $discussion_file = null;

    if (isset($_FILES['discussion_file']) && $_FILES['discussion_file']['error'] == 0) {

        $uploadDir = __DIR__ . "/../assets/files/training/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['discussion_file']['name'], PATHINFO_EXTENSION));

        $discussion_file = "TRN_DOC_" . time() . "." . $ext;

        move_uploaded_file(
            $_FILES['discussion_file']['tmp_name'],
            $uploadDir . $discussion_file
        );
    }

    // ================= INSERT =================
    $sql = "INSERT INTO training (
                reference_no,
                accreditation_no,
                accreditation_date,
                title,
                summary,
                objectives,
                promotional_image,
                discussion,
                start_at,
                end_at,
                street,
                barangay_id,
                city_id,
                province_id,
                region_id,
                program_type,
                approval_status,
                created_at
            ) VALUES (
                '$reference_no',
                '$accreditation_no',
                " . ($accreditation_date ? "'$accreditation_date'" : "NULL") . ",
                '$title',
                '$summary',
                '$objectives',
                '$image_name',
                '$discussion_file',
                " . ($start_at ? "'$start_at'" : "NULL") . ",
                " . ($end_at ? "'$end_at'" : "NULL") . ",
                '$street',
                " . ($barangay_id ? "'$barangay_id'" : "NULL") . ",
                " . ($city_id ? "'$city_id'" : "NULL") . ",
                " . ($province_id ? "'$province_id'" : "NULL") . ",
                " . ($region_id ? "'$region_id'" : "NULL") . ",
                '$program_type',
                0,
                NOW()
            )";

    if (mysqli_query($conn, $sql)) {

        echo json_encode([
            "code" => 0,
            "message" => "Training added successfully",
            "reference_no" => $reference_no,
            "id" => mysqli_insert_id($conn)
        ]);
    } else {

        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn)
        ]);
    }

    return;
} else if ($trans == "ADD_ADMISSION") {

    $training_id    = $data['training_id'] ?? '';
    $beneficiary_id = $data['beneficiary_id'] ?? '';
    $doc_num        = mysqli_real_escape_string($conn, $data['doc_num'] ?? '');

    if (!$training_id || !$beneficiary_id) {
        echo json_encode([
            "code" => 1,
            "message" => "Missing required fields"
        ]);
        return;
    }

    // Reject enrollment if the training is closed
    $trainingRow = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT status FROM training WHERE id = '$training_id' LIMIT 1
    "));

    if ($trainingRow && (int)$trainingRow['status'] == 2) {
        echo json_encode([
            "code" => 1,
            "message" => "Cannot add student - this training is already closed"
        ]);
        return;
    }

    // Check if beneficiary is already admitted to this training
    $check = mysqli_query($conn, "
        SELECT id
        FROM training_admission
        WHERE training_id = '$training_id'
          AND beneficiary_id = '$beneficiary_id'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        echo json_encode([
            "code" => 1,
            "message" => "Beneficiary already exists in this training"
        ]);
        return;
    }

    $sql = "INSERT INTO training_admission
        (training_id, beneficiary_id, doc_num, registration_at, status, created_at)
        VALUES
        ('$training_id', '$beneficiary_id', '$doc_num', NOW(), 0, NOW())";

    if (mysqli_query($conn, $sql)) {

        echo json_encode([
            "code" => 0,
            "message" => "Admission added successfully"
        ]);
    } else {

        echo json_encode([
            "code" => 1,
            "message" => mysqli_error($conn)
        ]);
    }

    return;
} elseif ($trans == "LIST_TRAINING") {

    $sql = "SELECT t.*,
                (SELECT COUNT(*) FROM training_program tp WHERE tp.training_id = t.id) AS program_count,
                (SELECT COUNT(*) FROM training_admission ta WHERE ta.training_id = t.id) AS enrolled_count
            FROM training t
            ORDER BY t.id DESC";
    $query = mysqli_query($conn, $sql);

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "data" => $data
    ]);

    return;
} else if ($trans == "LIST_ADMISSION") {

    $sql = "SELECT 
                ta.*,
                t.title,
                t.reference_no,
                CONCAT(b.fname, ' ', b.mname, ' ', b.lname) AS beneficiary_name
            FROM training_admission ta
            LEFT JOIN training t ON t.id = ta.training_id
            LEFT JOIN beneficiary b ON b.id = ta.beneficiary_id
            ORDER BY ta.id DESC";

    $query = mysqli_query($conn, $sql);

    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "data" => $data
    ]);

    return;
} else if ($trans == "ADD_PROGRAM") {

    $training_id = $data['training_id'] ?? '';
    $topic       = mysqli_real_escape_string($conn, $data['topic'] ?? '');
    $speaker     = mysqli_real_escape_string($conn, $data['speaker'] ?? '');
    $time_start  = $data['time_start'] ?? null;
    $time_end    = $data['time_end'] ?? null;

    if (!$training_id || !$topic) {
        echo json_encode(["code" => 1, "message" => "Training and Topic are required"]);
        return;
    }

    if (($time_start && !$time_end) || (!$time_start && $time_end)) {
        echo json_encode(["code" => 1, "message" => "Both Time Start and Time End are required together"]);
        return;
    }

    // ================= OVERLAP VALIDATION =================
    if ($time_start && $time_end) {
        $overlap = mysqli_query($conn, "
            SELECT id FROM training_program
            WHERE training_id = '$training_id'
              AND time_start IS NOT NULL
              AND time_end IS NOT NULL
              AND time_start < '$time_end'
              AND time_end > '$time_start'
            LIMIT 1
        ");

        if (mysqli_num_rows($overlap) > 0) {
            echo json_encode(["code" => 1, "message" => "Program time overlaps with an existing program"]);
            return;
        }
    }

    $sql = "INSERT INTO training_program
                (training_id, topic, speaker, time_start, time_end, status, created_at, updated_at)
            VALUES
                ('$training_id', '$topic', '$speaker',
                 " . ($time_start ? "'$time_start'" : "NULL") . ",
                 " . ($time_end   ? "'$time_end'"   : "NULL") . ",
                 0, NOW(), NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["code" => 0, "message" => "Program added successfully", "id" => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn)]);
    }

    return;

} else if ($trans == "EDIT_PROGRAM") {

    $id          = $data['id'] ?? 0;
    $training_id = $data['training_id'] ?? '';
    $topic       = mysqli_real_escape_string($conn, $data['topic'] ?? '');
    $speaker     = mysqli_real_escape_string($conn, $data['speaker'] ?? '');
    $time_start  = $data['time_start'] ?? null;
    $time_end    = $data['time_end'] ?? null;

    if (!$id || !$training_id || !$topic) {
        echo json_encode(["code" => 1, "message" => "Program ID, Training and Topic are required"]);
        return;
    }

    if (($time_start && !$time_end) || (!$time_start && $time_end)) {
        echo json_encode(["code" => 1, "message" => "Both Time Start and Time End are required together"]);
        return;
    }

    // ================= OVERLAP VALIDATION (exclude self) =================
    if ($time_start && $time_end) {
        $overlap = mysqli_query($conn, "
            SELECT id FROM training_program
            WHERE training_id = '$training_id'
              AND id != '$id'
              AND time_start IS NOT NULL
              AND time_end IS NOT NULL
              AND time_start < '$time_end'
              AND time_end > '$time_start'
            LIMIT 1
        ");

        if (mysqli_num_rows($overlap) > 0) {
            echo json_encode(["code" => 1, "message" => "Program time overlaps with an existing program"]);
            return;
        }
    }

    $sql = "UPDATE training_program SET
                topic       = '$topic',
                speaker     = '$speaker',
                time_start  = " . ($time_start ? "'$time_start'" : "NULL") . ",
                time_end    = " . ($time_end   ? "'$time_end'"   : "NULL") . ",
                updated_at  = NOW()
            WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["code" => 0, "message" => "Program updated successfully"]);
    } else {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn)]);
    }

    return;

} else if ($trans == "DELETE_PROGRAM") {

    $id = $data['id'] ?? 0;

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        return;
    }

    $sql = "DELETE FROM training_program WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["code" => 0, "message" => "Program deleted successfully"]);
    } else {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn)]);
    }

    return;

} else if ($trans == "GET_TRAINING") {

    $id = $data['id'] ?? 0;
    $sql = "SELECT t.*,
                   r.name AS region_name,
                   p.name AS province_name,
                   m.name AS city_name,
                   b.name AS barangay_name
            FROM training t
            LEFT JOIN region r ON r.code COLLATE utf8mb4_general_ci = t.region_id
            LEFT JOIN province p ON p.code COLLATE utf8mb4_general_ci = t.province_id
            LEFT JOIN municipality m ON m.code COLLATE utf8mb4_general_ci = t.city_id
            LEFT JOIN barangay b ON b.code COLLATE utf8mb4_general_ci = t.barangay_id
            WHERE t.id = '$id' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    if ($row) {
        echo json_encode(["code" => 0, "data" => $row]);
    } else {
        echo json_encode(["code" => 1, "message" => "Training not found"]);
    }

    return;

} else if ($trans == "APPROVE_TRAINING") {

    $id     = $data['id'] ?? 0;
    $reason = mysqli_real_escape_string($conn, $data['reason'] ?? '');

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        return;
    }

    $approved_by = $_SESSION['users_id'] ?? 0;

    mysqli_query($conn, "UPDATE training SET approval_status = 1, updated_at = NOW() WHERE id = '$id'");

    mysqli_query($conn, "INSERT INTO training_approval (training_id, action, reason, approved_by, created_at)
                         VALUES ('$id', 'approved', '$reason', '$approved_by', NOW())");

    echo json_encode(["code" => 0, "message" => "Training approved"]);
    return;

} else if ($trans == "REJECT_TRAINING") {

    $id     = $data['id'] ?? 0;
    $reason = mysqli_real_escape_string($conn, $data['reason'] ?? '');

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        return;
    }

    $approved_by = $_SESSION['users_id'] ?? 0;

    mysqli_query($conn, "UPDATE training SET approval_status = 2, updated_at = NOW() WHERE id = '$id'");

    mysqli_query($conn, "INSERT INTO training_approval (training_id, action, reason, approved_by, created_at)
                         VALUES ('$id', 'rejected', '$reason', '$approved_by', NOW())");

    echo json_encode(["code" => 0, "message" => "Training rejected"]);
    return;

} else if ($trans == "START_TRAINING") {

    $id = $data['id'] ?? 0;

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        return;
    }

    mysqli_query($conn, "UPDATE training SET status = 1, updated_at = NOW() WHERE id = '$id'");

    echo json_encode(["code" => 0, "message" => "Training started"]);
    return;

} else if ($trans == "CLOSE_TRAINING") {

    $id = $data['id'] ?? 0;

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        return;
    }

    mysqli_query($conn, "UPDATE training SET status = 2, updated_at = NOW() WHERE id = '$id'");

    echo json_encode(["code" => 0, "message" => "Training closed"]);
    return;

} else if ($trans == "LIST_APPROVAL_LOG") {

    $sql = "SELECT ta.*, t.title AS training_title, CONCAT(e.fname, ' ', e.lname) AS approved_by_name
            FROM training_approval ta
            LEFT JOIN training t ON t.id = ta.training_id
            LEFT JOIN users u ON u.id = ta.approved_by
            LEFT JOIN employee e ON e.users_id = u.id
            ORDER BY ta.created_at DESC";

    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode(["code" => 0, "data" => $list]);
    return;

} else if ($trans == "EDIT_TRAINING") {

    $id = $data['id'] ?? 0;

    $accreditation_no   = mysqli_real_escape_string($conn, $data['accreditation_no'] ?? '');
    $accreditation_date = $data['accreditation_date'] ?? null;
    $title              = mysqli_real_escape_string($conn, $data['title'] ?? '');
    $summary            = mysqli_real_escape_string($conn, $data['summary'] ?? '');
    $objectives         = mysqli_real_escape_string($conn, $data['objectives'] ?? '');
    $start_at           = $data['start_at'] ?? null;
    $end_at             = $data['end_at'] ?? null;
    $program_type       = $data['program_type'] ?? '';
    $street             = mysqli_real_escape_string($conn, $data['street'] ?? '');
    $barangay_id        = $data['barangay_id'] ?? null;
    $city_id            = $data['city_id'] ?? null;
    $province_id        = $data['province_id'] ?? null;
    $region_id          = $data['region_id'] ?? null;

    if (!$title) {
        echo json_encode(["code" => 1, "message" => "Title is required"]);
        return;
    }

    // ================= PROMOTIONAL IMAGE UPLOAD =================
    $image_name = null;

    if (isset($_FILES['promotional_image']) && $_FILES['promotional_image']['error'] == 0) {

        $uploadDir = __DIR__ . "/../assets/images/training/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['promotional_image']['name'], PATHINFO_EXTENSION));

        $image_name = "TRN_IMG_" . time() . "." . $ext;

        move_uploaded_file(
            $_FILES['promotional_image']['tmp_name'],
            $uploadDir . $image_name
        );
    }

    // ================= DISCUSSION FILE UPLOAD =================
    $discussion_file = null;

    if (isset($_FILES['discussion_file']) && $_FILES['discussion_file']['error'] == 0) {

        $uploadDir = __DIR__ . "/../assets/files/training/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['discussion_file']['name'], PATHINFO_EXTENSION));

        $discussion_file = "TRN_DOC_" . time() . "." . $ext;

        move_uploaded_file(
            $_FILES['discussion_file']['tmp_name'],
            $uploadDir . $discussion_file
        );
    }

    $set_fields = "
        accreditation_no   = '$accreditation_no',
        accreditation_date = " . ($accreditation_date ? "'$accreditation_date'" : "NULL") . ",
        title              = '$title',
        summary            = '$summary',
        objectives         = '$objectives',
        start_at           = " . ($start_at ? "'$start_at'" : "NULL") . ",
        end_at             = " . ($end_at ? "'$end_at'" : "NULL") . ",
        program_type       = '$program_type',
        street             = '$street',
        barangay_id        = " . ($barangay_id ? "'$barangay_id'" : "NULL") . ",
        city_id            = " . ($city_id ? "'$city_id'" : "NULL") . ",
        province_id        = " . ($province_id ? "'$province_id'" : "NULL") . ",
        region_id          = " . ($region_id ? "'$region_id'" : "NULL") . "";

    if ($image_name) {
        $set_fields .= ", promotional_image = '$image_name'";
    }

    if ($discussion_file) {
        $set_fields .= ", discussion = '$discussion_file'";
    }

    $sql = "UPDATE training SET $set_fields, updated_at = NOW() WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["code" => 0, "message" => "Training updated successfully"]);
    } else {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn)]);
    }

    return;

} else if ($trans == "DELETE_TRAINING") {

    $id = $data['id'] ?? 0;

    $sql = "DELETE FROM training WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["code" => 0, "message" => "Training deleted successfully"]);
    } else {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn)]);
    }

    return;

} else if ($trans == "GET_ADMISSION") {

    $id = $data['id'] ?? 0;

    $sql = "SELECT 
                ta.*,
                t.title,
                t.reference_no,
                CONCAT(b.fname, ' ', b.mname, ' ', b.lname) AS beneficiary_name
            FROM training_admission ta
            LEFT JOIN training t ON t.id = ta.training_id
            LEFT JOIN beneficiary b ON b.id = ta.beneficiary_id
            WHERE ta.id = '$id' LIMIT 1";

    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    if ($row) {
        echo json_encode(["code" => 0, "data" => $row]);
    } else {
        echo json_encode(["code" => 1, "message" => "Admission not found"]);
    }

    return;

} else if ($trans == "UPDATE_ADMISSION_STATUS") {

    $id     = $data['id'] ?? 0;
    $status = $data['status'] ?? 0;

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "ID is required"]);
        return;
    }

    $attendanceCol = ($status == 1) ? ", attendance_at = NOW()" : "";
    $sql = "UPDATE training_admission SET status = '$status', updated_at = NOW(){$attendanceCol} WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["code" => 0, "message" => "Status updated successfully"]);
    } else {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn)]);
    }

    return;

} else if ($trans == "LIST_TRAINING_ADMISSIONS") {

    $sql = "SELECT t.*,
                CASE
                    WHEN t.program_type = 0 THEN 'Other'
                    WHEN t.program_type = 1 THEN 'Onsite Training'
                    WHEN t.program_type = 2 THEN 'Virtual Learning'
                    ELSE 'Other'
                END AS program_type_label,
                (SELECT COUNT(*) FROM training_admission ta WHERE ta.training_id = t.id) AS admission_count,
                (SELECT COUNT(*) FROM training_admission ta WHERE ta.training_id = t.id AND ta.status = 0) AS pending_count,
                (SELECT COUNT(*) FROM training_admission ta WHERE ta.training_id = t.id AND ta.status = 1) AS present_count,
                (SELECT COUNT(*) FROM training_admission ta WHERE ta.training_id = t.id AND ta.status = 2) AS absent_count
            FROM training t
            WHERE t.approval_status = 1 AND t.status IN (1, 2)
            ORDER BY t.start_at DESC";

    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode(["code" => 0, "data" => $list]);
    return;

} else if ($trans == "GET_TRAINING_ADMISSIONS") {

    $training_id = $data['training_id'] ?? 0;

    $sql = "SELECT
                ta.*,
                CONCAT(b.fname, ' ', b.mname, ' ', b.lname) AS beneficiary_name,
                b.doc_num AS beneficiary_doc_num
            FROM training_admission ta
            LEFT JOIN beneficiary b ON b.id = ta.beneficiary_id
            WHERE ta.training_id = '$training_id'
            ORDER BY ta.registration_at DESC";

    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode(["code" => 0, "data" => $list]);
    return;

} else if ($trans == "ADD_ADMISSIONS") {

    $training_id    = $data['training_id'] ?? '';
    $beneficiary_ids = $data['beneficiary_ids'] ?? [];

    if (!$training_id || empty($beneficiary_ids)) {
        echo json_encode(["code" => 1, "message" => "Missing required fields"]);
        return;
    }

    // Reject enrollment if the training is closed
    $trainingRow = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT status FROM training WHERE id = '$training_id' LIMIT 1
    "));

    if ($trainingRow && (int)$trainingRow['status'] == 2) {
        echo json_encode(["code" => 1, "message" => "Cannot add student - this training is already closed"]);
        return;
    }

    $added = 0;
    $skipped = 0;

    foreach ($beneficiary_ids as $bid) {
        $bid = (int)$bid;

        $check = mysqli_query($conn, "
            SELECT id FROM training_admission
            WHERE training_id = '$training_id' AND beneficiary_id = '$bid'
            LIMIT 1
        ");

        if (mysqli_num_rows($check) > 0) {
            $skipped++;
            continue;
        }

        $docRow = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT doc_num FROM beneficiary WHERE id = '$bid' LIMIT 1
        "));
        $doc_num = $docRow['doc_num'] ?? null;

        $sql = "INSERT INTO training_admission
                    (training_id, beneficiary_id, doc_num, registration_at, status, created_at)
                VALUES
                    ('$training_id', '$bid', '$doc_num', NOW(), 0, NOW())";

        if (mysqli_query($conn, $sql)) {
            $added++;
        }
    }

    $msg = "$added beneficiary(ies) enrolled successfully";
    if ($skipped > 0) {
        $msg .= ". $skipped already enrolled (skipped)";
    }

    echo json_encode(["code" => 0, "message" => $msg]);
    return;

} else if ($trans == "LIST_AVAILABLE_TRAINING") {

    $beneficiary_id = $data['beneficiary_id'] ?? 0;

    $sql = "SELECT t.*,
                CASE
                    WHEN t.program_type = 0 THEN 'Other'
                    WHEN t.program_type = 1 THEN 'Onsite Training'
                    WHEN t.program_type = 2 THEN 'Virtual Learning'
                    ELSE 'Other'
                END AS program_type_label,
                (SELECT COUNT(*) FROM training_program tp WHERE tp.training_id = t.id) AS program_count,
                (SELECT COUNT(*) FROM training_admission ta WHERE ta.training_id = t.id AND ta.status IN (0,1)) AS enrolled_count,
                CASE
                    WHEN ta2.id IS NOT NULL THEN 1
                    ELSE 0
                END AS is_enrolled
            FROM training t
            LEFT JOIN training_admission ta2 ON ta2.training_id = t.id AND ta2.beneficiary_id = '$beneficiary_id'
            WHERE t.status != 2 AND t.approval_status = 1
            ORDER BY t.start_at DESC";

    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $list
    ]);
    return;

} else if ($trans == "ENROLL_TRAINING") {

    $training_id    = $data['training_id'] ?? '';
    $beneficiary_id = $data['beneficiary_id'] ?? '';

    if (!$training_id || !$beneficiary_id) {
        echo json_encode(["code" => 1, "message" => "Missing required fields"]);
        return;
    }

    $check = mysqli_query($conn, "
        SELECT id FROM training_admission
        WHERE training_id = '$training_id' AND beneficiary_id = '$beneficiary_id'
        LIMIT 1
    ");

    if (mysqli_num_rows($check) > 0) {
        echo json_encode(["code" => 1, "message" => "You are already enrolled in this training"]);
        return;
    }

    $docRow = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT doc_num FROM beneficiary WHERE id = '$beneficiary_id' LIMIT 1
    "));
    $doc_num = $docRow['doc_num'] ?? null;

    $sql = "INSERT INTO training_admission
                (training_id, beneficiary_id, doc_num, registration_at, status, created_at)
            VALUES
                ('$training_id', '$beneficiary_id', " . ($doc_num ? "'$doc_num'" : "NULL") . ", NOW(), 0, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["code" => 0, "message" => "Enrolled successfully"]);
    } else {
        echo json_encode(["code" => 1, "message" => mysqli_error($conn)]);
    }

    return;

} else if ($trans == "GET_TRAINING_PROGRAMS") {

    $training_id = $data['training_id'] ?? 0;

    $sql = "SELECT * FROM training_program WHERE training_id = '$training_id' ORDER BY time_start ASC";
    $res = mysqli_query($conn, $sql);
    $list = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $list[] = $row;
    }

    echo json_encode(["code" => 0, "data" => $list]);
    return;
}

echo json_encode([
    "code" => 1,
    "message" => "Invalid transaction"
]);
