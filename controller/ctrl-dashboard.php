<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';
error_reporting(0);

$data = json_decode(file_get_contents("php://input"), true);
$trans = $data['trans'] ?? '';

if ($trans == "GET_DASHBOARD_COUNTS") {

    $response = [];

    if ($data['role_id'] == 1 || $data['role_id'] == 2 || $data['role_id'] == 3) {

        $machines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM machinery"));
        $bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status IN (0, 1, 2)"));
        $facility = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM facility"));
        $programs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM program"));
        $users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));
        $trainings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM training"));
        $beneficiaries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE type = 1"));
        $inventory = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT SUM(current_stock) AS total 
            FROM product_inventory
        "));
        $reserved = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT SUM(reserved_stock) AS total 
            FROM product_inventory
        "));

        // Booking status breakdown
        $bookingStatus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status = 0"));
        $bookingApproved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status = 1"));
        $bookingCheckout = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status = 2"));
        $bookingReturned = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status = 3"));
        $bookingDeclined = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status = 4"));

        // Monthly booking trend (last 6 months)
        $trendRes = mysqli_query($conn, "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total
            FROM booking
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");
        $trendLabels = [];
        $trendTotals = [];
        while ($row = mysqli_fetch_assoc($trendRes)) {
            $trendLabels[] = date('M Y', strtotime($row['month'] . '-01'));
            $trendTotals[] = (int)$row['total'];
        }

        // Recent bookings (latest 5)
        $recentRes = mysqli_query($conn, "
            SELECT b.*, m.name AS machinery_name, m.model AS machinery_model
            FROM booking b
            LEFT JOIN machinery m ON m.id = b.machinery_id
            ORDER BY b.created_at DESC
            LIMIT 5
        ");
        $recentBookings = [];
        while ($row = mysqli_fetch_assoc($recentRes)) {
            $row['status_label'] = match ((int)$row['status']) {
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Checkout',
                3 => 'Returned',
                4 => 'Declined',
                default => 'Unknown',
            };
            $recentBookings[] = $row;
        }

        // Machine availability breakdown
        $machAvailable = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM machinery WHERE status = 1"));
        $machMaintenance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM machinery WHERE status = 0"));
        $machCheckedOut = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT machinery_id) AS total FROM booking WHERE status IN (1, 2)"));

        // Top products by available stock
        $topProdRes = mysqli_query($conn, "
            SELECT p.name AS product_name, p.sku,
                   SUM(pi.current_stock) AS current_stock,
                   SUM(pi.reserved_stock) AS reserved_stock
            FROM product_inventory pi
            LEFT JOIN product p ON p.id = pi.product_id
            WHERE pi.status = 1
            GROUP BY p.id, p.name, p.sku
            ORDER BY (SUM(pi.current_stock) - SUM(pi.reserved_stock)) DESC
            LIMIT 5
        ");
        $topProducts = [];
        while ($row = mysqli_fetch_assoc($topProdRes)) {
            $row['current_stock'] = (int)$row['current_stock'];
            $row['reserved_stock'] = (int)$row['reserved_stock'];
            $topProducts[] = $row;
        }

        // Low stock alerts
        $lowStockRes = mysqli_query($conn, "
            SELECT pi.product_id, p.name AS product_name, p.unit,
                   pi.current_stock, pi.reorder_level, f.name AS facility_name
            FROM product_inventory pi
            LEFT JOIN product p ON p.id = pi.product_id
            LEFT JOIN facility f ON f.id = pi.facility_id
            WHERE pi.status = 1 AND pi.current_stock > 0 AND pi.current_stock <= pi.reorder_level
            ORDER BY pi.current_stock ASC
            LIMIT 8
        ");
        $lowStock = [];
        while ($row = mysqli_fetch_assoc($lowStockRes)) {
            $lowStock[] = $row;
        }

        // Pending approvals (bookings awaiting decision)
        $pendingRes = mysqli_query($conn, "
            SELECT b.id, b.created_at, m.name AS machinery_name,
                   CONCAT(COALESCE(be.fname, ''), ' ', COALESCE(be.lname, '')) AS booked_by_name
            FROM booking b
            LEFT JOIN machinery m ON m.id = b.machinery_id
            LEFT JOIN beneficiary be ON be.id = b.beneficiary_id
            WHERE b.status = 0
            ORDER BY b.created_at DESC
            LIMIT 6
        ");
        $pendingBookings = [];
        while ($row = mysqli_fetch_assoc($pendingRes)) {
            $pendingBookings[] = $row;
        }

        // Recent inventory logs (latest 7)
        $logRes = mysqli_query($conn, "
            SELECT pil.id, pil.action_type, pil.quantity_changed, pil.created_at,
                   p.name AS product_name, p.unit,
                   f.name AS facility_name,
                   CONCAT(COALESCE(e.fname, ''), ' ', COALESCE(e.lname, '')) AS created_by_name
            FROM product_inventory_logs pil
            LEFT JOIN product_inventory pi ON pi.id = pil.inventory_id
            LEFT JOIN product p ON p.id = pi.product_id
            LEFT JOIN facility f ON f.id = pi.facility_id
            LEFT JOIN users u ON u.id = pil.users_id
            LEFT JOIN employee e ON e.users_id = pil.users_id
            ORDER BY pil.id DESC
            LIMIT 7
        ");
        $recentLogs = [];
        while ($row = mysqli_fetch_assoc($logRes)) {
            $recentLogs[] = $row;
        }

        $response = [
            "machines" => $machines['total'],
            "bookings" => $bookings['total'],
            "facility" => $facility['total'],
            "programs" => $programs['total'],
            "trainings" => $trainings['total'],
            "total_inventory" => $inventory['total'] ?? 0,
            "reserved" => $reserved['total'] ?? 0,
            "users_count" => $users['total'],
            "beneficiaries_count" => $beneficiaries['total'],
            "booking_breakdown" => [
                "pending" => $bookingStatus['total'],
                "approved" => $bookingApproved['total'],
                "checkout" => $bookingCheckout['total'],
                "returned" => $bookingReturned['total'],
                "declined" => $bookingDeclined['total']
            ],
            "booking_trend" => [
                "labels" => $trendLabels,
                "totals" => $trendTotals
            ],
            "recent_bookings" => $recentBookings,
            "machine_breakdown" => [
                "available" => $machAvailable['total'],
                "maintenance" => $machMaintenance['total'],
                "checked_out" => $machCheckedOut['total']
            ],
            "top_products" => $topProducts,
            "low_stock" => $lowStock,
            "pending_bookings" => $pendingBookings,
            "recent_logs" => $recentLogs
        ];

        // Add branch and facility details for role_id 2 (cooperative) and 3 (beneficiary)
        $roleId = $data['role_id'];
        $usersId = $data['users_id'] ?? 0;
        if (($roleId == 2 || $roleId == 3) && $usersId) {
            $branchData = null;
            $facilityData = null;

            // Get branch_id from profile
            $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT branch_id, facility_id FROM users u LEFT JOIN employee e ON e.users_id = u.id WHERE u.id = '$usersId' LIMIT 1"));
            if (!$profile || !$profile['branch_id']) {
                $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT branch_id, facility_id FROM users u LEFT JOIN beneficiary b ON b.users_id = u.id WHERE u.id = '$usersId' LIMIT 1"));
            }

            if ($profile) {
                $branchId = $profile['branch_id'] ?? null;
                $facilityId = $profile['facility_id'] ?? null;

                if ($branchId) {
                    $branchData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM branch WHERE id = '$branchId' LIMIT 1"));
                }
                if ($facilityId) {
                    $facilityData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM facility WHERE id = '$facilityId' LIMIT 1"));
                } else if ($branchId) {
                    // Fallback: get facility by branch_id
                    $facilityData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM facility WHERE branch_id = '$branchId' LIMIT 1"));
                }
            }

            $response['branch'] = $branchData;
            $response['facility'] = $facilityData;
        }
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $response
    ]);
    exit;
}

echo json_encode([
    "code" => 1,
    "message" => "Invalid transaction",
    "data" => []
]);
