<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

include_once __DIR__ . '/../config/dbcon.php';
include_once __DIR__ . '/../model/gbl.php';
include_once __DIR__ . '/../model/booking.php';

GblFn::errorReporting();

$data = json_decode(file_get_contents("php://input"), true);
$data = GblFn::sanitizeData($data);
$trans = $data['trans'] ?? '';

if (!$_SESSION["IS_LOGIN"]) {
    echo json_encode(["code" => 1, "message" => "Unauthorized", "data" => null]);
    exit;
}

/*
|--------------------------------------------------------------------------
| BOOKING STATUS
|--------------------------------------------------------------------------
| 0 = Pending
| 1 = Approved
| 2 = Checkout
| 3 = Returned
| 4 = Declined
|--------------------------------------------------------------------------
|
| MACHINERY STATUS
|--------------------------------------------------------------------------
| 0 = Under Maintenance
| 1 = Available
|--------------------------------------------------------------------------
| NOTE: Booking state is NOT stored in machinery.status.
| A machine's availability for booking is derived from active
| bookings (status IN 0,1,2) via NOT EXISTS subqueries.
|--------------------------------------------------------------------------
*/

if ($trans == "LIST_AVAILABLE_BOOKINGS") {

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT
                    m.*, mt.name AS machinery_type, b.name AS facility_name,
                    COALESCE((SELECT AVG(mr.rating) FROM machinery_reviews mr WHERE mr.machinery_id = m.id AND mr.status = 1), 0) AS ratings,
                    CASE
                        WHEN m.status = 0 THEN 'Maintenance'
                        WHEN m.status = 1 THEN 'Available'
                        ELSE 'Unknown'
                    END AS status_label,
                    (SELECT name FROM machinery_images WHERE machinery_id = m.id AND is_primary = 1 LIMIT 1) AS image
                FROM machinery m
                LEFT JOIN machinery_type mt ON mt.id = m.type_id
                LEFT JOIN branch b ON b.id = m.branch_id
                WHERE m.status IN ('0','1')
                  AND NOT EXISTS (
                      SELECT 1 FROM booking bb
                      WHERE bb.machinery_id = m.id
                        AND bb.status IN ('0','1','2')
                  )
                ORDER BY ratings DESC";

        $stmt = $db->query($sql);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch machinery error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "LIST_AVAILABLE_MACHINERY") {

    $branch_id = $data['branch_id'] ?? '';
    $role_id = $_SESSION['role_id'] ?? '';

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT
                    m.*, mt.name AS machinery_type, b.name AS branch,
                    COALESCE((SELECT AVG(mr.rating) FROM machinery_reviews mr WHERE mr.machinery_id = m.id AND mr.status = 1), 0) AS ratings,
                    CASE
                        WHEN m.status = 0 THEN 'Maintenance'
                        WHEN m.status = 1 THEN 'Available'
                        ELSE 'Unknown'
                    END AS status_label,
                    (SELECT name FROM machinery_images WHERE machinery_id = m.id AND is_primary = 1 LIMIT 1) AS image
                FROM machinery m
                LEFT JOIN machinery_type mt ON mt.id = m.type_id
                LEFT JOIN branch b ON b.id = m.branch_id
                WHERE m.status IN ('0','1')
                  AND NOT EXISTS (
                      SELECT 1 FROM booking bb
                      WHERE bb.machinery_id = m.id
                        AND bb.status IN ('0','1','2')
                  )";

        $params = [];
        if ($role_id != 1 && $branch_id) {
            $sql .= " AND m.branch_id = ?";
            $params[] = $branch_id;
        }
        $sql .= " ORDER BY ratings DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include_once __DIR__ . '/../config/dbconn.php';
        $baseUrl = $ENV['APP_URL'] ?? '';

        $list = [];
        foreach ($rows as $row) {
            $imageName = $row['image'] ?? 'default.png';
            $row['image'] = $baseUrl . "/assets/images/machinery/" . $imageName;
            $list[] = $row;
        }

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch machinery error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "ADD_BOOKING") {

    $beneficiary_id = $data['beneficiary_id'] ?? '';
    $isBeneficiaryRole = (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3);
    $machinery_id   = $data['machinery_id'] ?? '';
    $branch_id      = $data['branch_id'] ?? ($_SESSION["profile"]['branch_id'] ?? '');
    $start_date     = $data['start_date'] ?? '';
    $end_date       = $data['end_date'] ?? '';
    $booked_by      = $data['booked_by'] ?? ($_SESSION['users_id'] ?? '');
    $total_days     = $data['total_days'] ?? 0;
    $unit_price     = $data['unit_price'] ?? 0;
    $total_cost     = $data['total_cost'] ?? 0;

    if (!$machinery_id || !$start_date || !$end_date) {
        echo json_encode(["code" => 1, "message" => "Required fields are missing", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        if ($isBeneficiaryRole) {
            $sessionUserId = $_SESSION['user_id'] ?? ($_SESSION['users_id'] ?? 0);
            $beneficiary_id = '';
            if ($sessionUserId) {
                $stmt = $db->prepare("SELECT id FROM beneficiary WHERE users_id = ? LIMIT 1");
                $stmt->execute([$sessionUserId]);
                $beneficiary_id = (int)$stmt->fetchColumn();
            }
        }
        if (!$beneficiary_id) {
            echo json_encode(["code" => 1, "message" => "Required fields are missing", "data" => null]);
            exit;
        }

        $db->beginTransaction();

        $bookingSchedSql = "SELECT 1
                            FROM booking_schedules bs
                            INNER JOIN booking b ON bs.booking_id = b.id
                            WHERE b.machinery_id = ?
                              AND b.status IN (0, 1, 2)
                              AND bs.status != 2
                              AND bs.start_at < ?
                              AND bs.end_at > ?";

        $stmt = $db->prepare($bookingSchedSql);
        $stmt->execute([$machinery_id, $end_date, $start_date]);

        if ((bool) $stmt->fetchColumn()) {
            $db->rollBack();
            echo json_encode(["code" => 1, "message" => "Machinery not available at the selected date range", "data" => null]);
            exit;
        }

        $bookingSql = "INSERT INTO booking (
                            beneficiary_id, branch_id, machinery_id,
                            total_days, unit_price, total_cost,
                            status, created_at
                        )
                        VALUES (?, ?, ?, ?, ?, ?, '0', NOW())";

        $bookingStmt = $db->prepare($bookingSql);
        $bookingStmt->execute([
            $beneficiary_id, $branch_id, $machinery_id,
            $total_days, $unit_price, $total_cost
        ]);
        $insertedId = $db->lastInsertId();

        $bookingNumber = Booking::generateBookingNumber($insertedId);
        $updateNumStmt = $db->prepare("UPDATE booking SET booking_num = ? WHERE id = ?");
        $updateNumStmt->execute([$bookingNumber, $insertedId]);

        $logStmt = $db->prepare("INSERT INTO booking_logs (booking_id, booked_by, created_at) VALUES (?, ?, NOW())");
        $logStmt->execute([$insertedId, $booked_by]);

        $schedStmt = $db->prepare("INSERT INTO booking_schedules (booking_id, start_at, end_at, status, created_at) VALUES (?, ?, ?, 0, NOW())");
        $schedStmt->execute([$insertedId, $start_date, $end_date]);

        $db->commit();

        echo json_encode(["code" => 0, "message" => "Booking submitted", "data" => ["booking_id" => $insertedId]]);
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log("Booking error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred during booking processing."]);
    }
    exit;

} else if ($trans == "LIST_BOOKING_APPROVAL") {

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT b.*, m.name AS machinery_name, m.model,
                       CONCAT(ben.fname, ' ', ben.lname) AS beneficiary_name
                FROM booking b
                LEFT JOIN machinery m ON m.id = b.machinery_id
                LEFT JOIN beneficiary ben ON ben.id = b.beneficiary_id
                WHERE b.status = 0
                ORDER BY b.id DESC";

        $stmt = $db->query($sql);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch approval error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "GET_BOOKING_APPROVAL_DETAIL") {

    $booking_id = $data['booking_id'] ?? '';

    if (!$booking_id) {
        echo json_encode(["code" => 1, "message" => "Booking ID is required", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT
                    b.*,
                    m.name AS machinery_name,
                    m.model AS machinery_model,
                    m.description AS machinery_description,
                    mt.name AS machinery_type,
                    br.name AS branch_name,
                    CONCAT(ben.fname, ' ', ben.lname) AS beneficiary_name,
                    ben.mobile AS beneficiary_mobile,
                    ben.email AS beneficiary_email,
                    ben.address AS beneficiary_address,
                    bs.start_at,
                    bs.end_at
                FROM booking b
                LEFT JOIN machinery m ON m.id = b.machinery_id
                LEFT JOIN machinery_type mt ON mt.id = m.type_id
                LEFT JOIN branch br ON br.id = b.branch_id
                LEFT JOIN beneficiary ben ON ben.id = b.beneficiary_id
                LEFT JOIN booking_schedules bs ON bs.booking_id = b.id
                WHERE b.id = ?
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([$booking_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode(["code" => 1, "message" => "Booking not found", "data" => null]);
            exit;
        }

        echo json_encode(["code" => 0, "message" => "Success", "data" => $row]);
    } catch (Exception $e) {
        error_log("Fetch approval detail error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "APPROVE_BOOKING") {

    $booking_id = $data['booking_id'] ?? '';
    $users_id   = $data['users_id'] ?? ($_SESSION['users_id'] ?? '');

    if (!$booking_id) {
        echo json_encode(["code" => 1, "message" => "Booking ID is required", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $db->beginTransaction();

        $checkSql = "SELECT b.status, b.machinery_id FROM booking b WHERE b.id = ? AND b.status = '0' LIMIT 1 FOR UPDATE";
        $stmt = $db->prepare($checkSql);
        $stmt->execute([$booking_id]);
        $bookingRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bookingRow) {
            $updateBooking = $db->prepare("UPDATE booking SET status = '1' WHERE id = ?");
            $updateBooking->execute([$booking_id]);

            $updateLog = $db->prepare("UPDATE booking_logs SET approved_at = NOW(), approved_staff_id = ? WHERE booking_id = ?");
            $updateLog->execute([$users_id, $booking_id]);

            $db->commit();
            echo json_encode(["code" => 0, "message" => "Booking has been approved", "data" => null]);
        } else {
            $db->rollBack();
            echo json_encode(["code" => 1, "message" => "Booking not found or already processed", "data" => null]);
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log("Approve error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "DECLINE_BOOKING") {

    $booking_id = $data['booking_id'] ?? '';
    $users_id   = $data['users_id'] ?? ($_SESSION['users_id'] ?? '');
    $remarks    = $data['remarks'] ?? '';

    if (!$booking_id) {
        echo json_encode(["code" => 1, "message" => "Booking ID is required", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $db->beginTransaction();

        $checkSql = "SELECT b.status, b.machinery_id FROM booking b WHERE b.id = ? AND b.status = '0' LIMIT 1 FOR UPDATE";
        $stmt = $db->prepare($checkSql);
        $stmt->execute([$booking_id]);
        $bookingRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bookingRow) {
            $updateBooking = $db->prepare("UPDATE booking SET status = '4' WHERE id = ?");
            $updateBooking->execute([$booking_id]);

            $updateLog = $db->prepare("UPDATE booking_logs SET declined_at = NOW(), declined_staff_id = ?, declined_remarks = ? WHERE booking_id = ?");
            $updateLog->execute([$users_id, $remarks, $booking_id]);

            $db->commit();
            echo json_encode(["code" => 0, "message" => "Booking has been declined", "data" => null]);
        } else {
            $db->rollBack();
            echo json_encode(["code" => 1, "message" => "Booking not found or already processed", "data" => null]);
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log("Decline error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "CHECKOUT_BOOKING") {

    $booking_id = $data['booking_id'] ?? '';
    $users_id   = $data['users_id'] ?? ($_SESSION['users_id'] ?? '');

    if (!$booking_id) {
        echo json_encode(["code" => 1, "message" => "Booking ID is required", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $db->beginTransaction();

        $checkSql = "SELECT b.status, b.machinery_id FROM booking b WHERE b.id = ? AND b.status = '1' LIMIT 1 FOR UPDATE";
        $stmt = $db->prepare($checkSql);
        $stmt->execute([$booking_id]);
        $bookingRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bookingRow) {
            $updateBooking = $db->prepare("UPDATE booking SET status = '2' WHERE id = ?");
            $updateBooking->execute([$booking_id]);

            $updateLog = $db->prepare("UPDATE booking_logs SET checkout_at = NOW(), checkout_staff_id = ? WHERE booking_id = ?");
            $updateLog->execute([$users_id, $booking_id]);

            $db->commit();
            echo json_encode(["code" => 0, "message" => "Booking has been checked out", "data" => null]);
        } else {
            $db->rollBack();
            echo json_encode(["code" => 1, "message" => "Booking not found or not approved", "data" => null]);
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log("Checkout error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "LIST_RETURN_BOOKING") {

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT b.*, m.name AS machinery_name, m.model,
                       CONCAT(ben.fname, ' ', ben.lname) AS beneficiary_name,
                       bs.start_at, bs.end_at
                FROM booking b
                LEFT JOIN machinery m ON m.id = b.machinery_id
                LEFT JOIN beneficiary ben ON ben.id = b.beneficiary_id
                LEFT JOIN booking_schedules bs ON bs.booking_id = b.id
                WHERE b.status IN ('1','2','3')
                ORDER BY b.id DESC";

        $stmt = $db->query($sql);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch return error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "RETURN_BOOKING") {

    $booking_id = $data['booking_id'] ?? '';
    $users_id   = $data['users_id'] ?? ($_SESSION['users_id'] ?? '');

    if (!$booking_id) {
        echo json_encode(["code" => 1, "message" => "Booking ID is required", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $db->beginTransaction();

        $checkSql = "SELECT b.status, b.machinery_id FROM booking b WHERE b.id = ? AND b.status = '2' LIMIT 1 FOR UPDATE";
        $stmt = $db->prepare($checkSql);
        $stmt->execute([$booking_id]);
        $bookingRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bookingRow) {
            $updateBooking = $db->prepare("UPDATE booking SET status = '3' WHERE id = ?");
            $updateBooking->execute([$booking_id]);

            $updateLog = $db->prepare("UPDATE booking_logs SET returned_at = NOW(), returned_staff_id = ? WHERE booking_id = ?");
            $updateLog->execute([$users_id, $booking_id]);

            $db->commit();
            echo json_encode(["code" => 0, "message" => "Booking has been returned", "data" => null]);
        } else {
            $db->rollBack();
            echo json_encode(["code" => 1, "message" => "Booking not found or not checked out", "data" => null]);
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log("Return error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "LIST_BOOKING_DECLINED") {

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT
                    b.*, m.name AS machinery_name, m.model,
                    CONCAT(ben.fname, ' ', ben.lname) AS beneficiary_name,
                    br.name AS branch_name
                FROM booking b
                LEFT JOIN machinery m ON m.id = b.machinery_id
                LEFT JOIN beneficiary ben ON ben.id = b.beneficiary_id
                LEFT JOIN branch br ON br.id = b.branch_id
                WHERE b.status = '4'
                ORDER BY b.id DESC";

        $stmt = $db->query($sql);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch declined error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "LIST_BENEFICIARY_BOOKING") {

    $db = DBCon::getConnection();

    try {
        // Resolve the beneficiary from the logged-in session user account,
        // so each user only ever sees their own bookings.
        $sessionUserId = $_SESSION['user_id'] ?? ($_SESSION['users_id'] ?? 0);
        $beneficiary_id = '';
        if ($sessionUserId) {
            $stmt = $db->prepare("SELECT id FROM beneficiary WHERE users_id = ? LIMIT 1");
            $stmt->execute([$sessionUserId]);
            $beneficiary_id = (int)$stmt->fetchColumn();
        }

        if (!$beneficiary_id) {
            echo json_encode(["code" => 0, "message" => "Success", "data" => []]);
            exit;
        }

        $sql = "SELECT
                    b.*, m.name AS machinery_name, m.model,
                    mt.name AS machinery_type,
                    br.name AS branch_name,
                    bs.start_at, bs.end_at,
                    bl.declined_remarks,
                    (SELECT name FROM machinery_images WHERE machinery_id = m.id AND is_primary = 1 LIMIT 1) AS image,
                    CASE
                        WHEN b.status = 0 THEN 'Pending'
                        WHEN b.status = 1 THEN 'Approved'
                        WHEN b.status = 2 THEN 'Checkout'
                        WHEN b.status = 3 THEN 'Returned'
                        WHEN b.status = 4 THEN 'Declined'
                        ELSE 'Unknown'
                    END AS status_label
                FROM booking b
                LEFT JOIN machinery m ON m.id = b.machinery_id
                LEFT JOIN machinery_type mt ON mt.id = m.type_id
                LEFT JOIN branch br ON br.id = b.branch_id
                LEFT JOIN booking_schedules bs ON bs.booking_id = b.id
                LEFT JOIN booking_logs bl ON bl.booking_id = b.id
                WHERE b.beneficiary_id = ?
                ORDER BY b.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$beneficiary_id]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch beneficiary bookings error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "LIST_BOOKING") {

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT
                    b.*, m.name AS machinery_name, m.model,
                    mt.name AS machinery_type,
                    br.name AS branch_name,
                    CONCAT(ben.fname, ' ', ben.lname) AS beneficiary_name,
                    CASE
                        WHEN b.status = 0 THEN 'Pending'
                        WHEN b.status = 1 THEN 'Approved'
                        WHEN b.status = 2 THEN 'Checkout'
                        WHEN b.status = 3 THEN 'Returned'
                        WHEN b.status = 4 THEN 'Declined'
                        ELSE 'Unknown'
                    END AS status_label
                FROM booking b
                LEFT JOIN machinery m ON m.id = b.machinery_id
                LEFT JOIN machinery_type mt ON mt.id = m.type_id
                LEFT JOIN beneficiary ben ON ben.id = b.beneficiary_id
                LEFT JOIN branch br ON br.id = b.branch_id
                ORDER BY b.id DESC";

        $stmt = $db->query($sql);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch all bookings error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "GET_MACHINERY_IMAGES") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode(["code" => 1, "message" => "Machinery ID is required", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $stmt = $db->prepare("SELECT id, name, is_primary FROM machinery_images WHERE machinery_id = ? ORDER BY is_primary DESC, id ASC");
        $stmt->execute([$id]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch images error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "LIST_BOOKED_DATES") {

    $machinery_id = $data['machinery_id'] ?? '';

    if (!$machinery_id) {
        echo json_encode(["code" => 1, "message" => "Machinery ID is required", "data" => []]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $sql = "                SELECT bs.start_at AS from_date, bs.end_at AS end_date,
                       CASE
                           WHEN b.status = 0 THEN 'pending'
                           WHEN b.status = 1 THEN 'approved'
                           WHEN b.status = 2 THEN 'approved'
                           WHEN b.status = 4 THEN 'declined'
                           ELSE 'pending'
                       END AS status
                FROM booking_schedules bs
                INNER JOIN booking b ON bs.booking_id = b.id
                WHERE b.machinery_id = ?
                  AND b.status IN (0, 1, 2)
                  AND bs.status != 2";

        $stmt = $db->prepare($sql);
        $stmt->execute([$machinery_id]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (Exception $e) {
        error_log("Fetch booked dates error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred.", "data" => []]);
    }
    exit;

} else if ($trans == "LIST_MACHINERY_REVIEWS") {

    $machinery_id = (int)($data['machinery_id'] ?? 0);

    if (!$machinery_id) {
        echo json_encode(["code" => 1, "message" => "Machinery ID is required", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT
                    mr.id, mr.rating, mr.comment, mr.created_at,
                    CONCAT(ben.fname, ' ', ben.lname) AS beneficiary_name
                FROM machinery_reviews mr
                LEFT JOIN beneficiary ben ON ben.id = mr.beneficiary_id
                WHERE mr.machinery_id = ?
                  AND mr.status = 1
                ORDER BY mr.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$machinery_id]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $average = 0;
        $count = count($list);
        if ($count) {
            $average = round(array_sum(array_map(function ($r) {
                return (float)$r['rating'];
            }, $list)) / $count, 1);
        }

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list, "average" => $average, "count" => $count]);
    } catch (Exception $e) {
        error_log("Fetch machinery reviews error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else if ($trans == "SUBMIT_MACHINERY_RATING") {

    $beneficiary_id = $_SESSION["profile"]['id'] ?? '';
    $booking_id = (int)($data['booking_id'] ?? 0);
    $machinery_id = (int)($data['machinery_id'] ?? 0);
    $rating = (int)($data['rating'] ?? 0);
    $comment = trim((string)($data['comment'] ?? ''));

    if (!$beneficiary_id || !$booking_id || !$machinery_id) {
        echo json_encode(["code" => 1, "message" => "Missing required fields", "data" => null]);
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        echo json_encode(["code" => 1, "message" => "Rating must be between 1 and 5.", "data" => null]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $stmt = $db->prepare("SELECT id, rated FROM booking WHERE id = ? AND beneficiary_id = ? AND status = 3");
        $stmt->execute([$booking_id, $beneficiary_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo json_encode(["code" => 1, "message" => "Booking not found or not yet completed.", "data" => null]);
            exit;
        }

        if ((int)$booking['rated'] === 1) {
            echo json_encode(["code" => 1, "message" => "You have already rated this booking.", "data" => null]);
            exit;
        }

        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO machinery_reviews (machinery_id, beneficiary_id, rating, comment, status, created_at, updated_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())");
        $stmt->execute([$machinery_id, $beneficiary_id, $rating, $comment]);

        $stmt = $db->prepare("UPDATE booking SET rated = 1 WHERE id = ?");
        $stmt->execute([$booking_id]);

        $stmt = $db->prepare("SELECT AVG(rating) AS avg_rating FROM machinery_reviews WHERE machinery_id = ? AND status = 1");
        $stmt->execute([$machinery_id]);
        $avg = $stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];

        $stmt = $db->prepare("UPDATE machinery SET ratings = ? WHERE id = ?");
        $stmt->execute([$avg ? round((float)$avg, 1) : 0, $machinery_id]);

        $db->commit();

        echo json_encode(["code" => 0, "message" => "Thank you! Your rating has been submitted.", "data" => null]);
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Submit machinery rating error: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "An error occurred."]);
    }
    exit;

} else {
    echo json_encode(["code" => 1, "message" => "Invalid transaction", "data" => null]);
    exit;
}
