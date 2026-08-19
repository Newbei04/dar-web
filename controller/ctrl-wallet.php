<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Manila');

include_once __DIR__ . '/../config/dbcon.php';
include_once __DIR__ . '/../model/gbl.php';
include_once __DIR__ . '/../model/wallet.php';

GblFn::errorReporting();

session_start();

$data = json_decode(file_get_contents("php://input"), true);
$data = GblFn::sanitizeData($data);

$trans = $data['trans'] ?? '';

if ($trans == "LIST_WALLET") {

    $db = DBCon::getConnection();
    $roleId = (int)($_SESSION['role_id'] ?? 0);
    $params = [];

    $sql = "SELECT
                w.id,
                w.account_num,
                w.total_balance,
                w.credit_limit,
                w.is_frozen,
                w.created_at,
                b.id AS beneficiary_id,
                b.fname,
                b.mname,
                b.lname,
                b.branch_id,
                b.status AS beneficiary_status,
                COALESCE(br.name, '') AS branch_name,
                u.username,
                u.id AS users_id
            FROM wallet w
            INNER JOIN beneficiary b ON b.id = w.beneficiary_id
            LEFT JOIN branch br ON br.id = b.branch_id
            LEFT JOIN users u ON u.id = b.users_id
            WHERE 1=1";

    if ($roleId === 3) {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $sql .= " AND b.users_id = ?";
        $params[] = $userId;
    } else if ($roleId === 2) {
        $branchId = $_SESSION['profile']['branch_id'] ?? '';
        if ($branchId !== '') {
            $sql .= " AND b.branch_id = ?";
            $params[] = $branchId;
        }
    }

    $sql .= " ORDER BY w.created_at DESC";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $list = $stmt->fetchAll();

        foreach ($list as &$row) {
            $row['full_name'] = trim($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']);
            $row['is_frozen_label'] = $row['is_frozen'] ? 'Frozen' : 'Active';
        }
        unset($row);

        echo json_encode([
            "code"    => 0,
            "message" => "Success",
            "count"   => count($list),
            "data"    => $list
        ]);

    } catch (PDOException $e) {
        error_log("LIST_WALLET failed: " . $e->getMessage());
        echo json_encode([
            "code"    => 1,
            "message" => "Failed to load wallets."
        ]);
    }

    exit;

} else if ($trans == "GET_WALLET") {

    $walletId = $data['id'] ?? $_REQUEST['id'] ?? '';
    $beneficiaryId = $data['beneficiary_id'] ?? $_REQUEST['beneficiary_id'] ?? '';
    $usersId = $data['users_id'] ?? $_REQUEST['users_id'] ?? '';

    if (!$walletId && !$beneficiaryId && !$usersId) {
        echo json_encode(["code" => 1, "message" => "Wallet ID, beneficiary_id, or users_id required"]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $sql = "SELECT
                    w.*,
                    b.fname, b.mname, b.lname, b.email, b.mobile,
                    b.doc_num, b.branch_id, b.address,
                    COALESCE(br.name, '') AS branch_name,
                    u.username
                FROM wallet w
                INNER JOIN beneficiary b ON b.id = w.beneficiary_id
                LEFT JOIN branch br ON br.id = b.branch_id
                LEFT JOIN users u ON u.id = b.users_id
                WHERE 1=1";
        $params = [];

        if ($walletId) {
            $sql .= " AND w.id = ?";
            $params[] = $walletId;
        } else if ($beneficiaryId) {
            $sql .= " AND w.beneficiary_id = ?";
            $params[] = $beneficiaryId;
        } else if ($usersId) {
            $sql .= " AND b.users_id = ?";
            $params[] = $usersId;
        }

        $sql .= " LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $wallet = $stmt->fetch();

        if (!$wallet) {
            echo json_encode(["code" => 1, "message" => "Wallet not found"]);
            exit;
        }

        $wallet['full_name'] = trim($wallet['fname'] . ' ' . $wallet['mname'] . ' ' . $wallet['lname']);

        $balStmt = $db->prepare("SELECT wb.*, p.name AS program_name,
            CASE wb.balance_type
                WHEN 1 THEN 'Personal Savings'
                WHEN 2 THEN 'Fuel Subsidy'
                WHEN 3 THEN 'Planting Loan'
                WHEN 4 THEN 'RFFA Assistance'
                ELSE 'Other'
            END AS balance_type_label
        FROM wallet_balances wb
        LEFT JOIN program p ON p.id = wb.program_id
        WHERE wb.wallet_id = ?
        ORDER BY wb.id ASC");
        $balStmt->execute([$walletId]);
        $wallet['balances'] = $balStmt->fetchAll();

        echo json_encode([
            "code"    => 0,
            "message" => "Success",
            "data"    => $wallet
        ]);

    } catch (PDOException $e) {
        error_log("GET_WALLET failed: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "Failed to load wallet details."]);
    }

    exit;

} else if ($trans == "GET_WALLET_LOGS") {

    $walletId = $data['id'] ?? $_REQUEST['id'] ?? '';
    if (!$walletId) {
        echo json_encode(["code" => 1, "message" => "Wallet ID required"]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $stmt = $db->prepare("SELECT
            wl.*,
            CASE wl.action
                WHEN 0 THEN 'Created'
                WHEN 1 THEN 'Debit'
                WHEN 2 THEN 'Credit'
                WHEN 3 THEN 'Frozen'
                ELSE 'Unknown'
            END AS action_label
        FROM wallet_logs wl
        WHERE wl.wallet_id = ?
        ORDER BY wl.created_at DESC");
        $stmt->execute([$walletId]);
        $logs = $stmt->fetchAll();

        echo json_encode([
            "code"    => 0,
            "message" => "Success",
            "data"    => $logs
        ]);

    } catch (PDOException $e) {
        error_log("GET_WALLET_LOGS failed: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "Failed to load wallet logs."]);
    }

    exit;

} else if ($trans == "LIST_PROGRAMS") {

    $db = DBCon::getConnection();

    try {
        $stmt = $db->prepare("SELECT id, code, name FROM program WHERE status = 1 ORDER BY name ASC");
        $stmt->execute();
        $list = $stmt->fetchAll();

        echo json_encode(["code" => 0, "message" => "Success", "data" => $list]);
    } catch (PDOException $e) {
        error_log("LIST_PROGRAMS failed: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "Failed to load programs."]);
    }

    exit;

} else if ($trans == "CASH_IN") {

    $walletId    = (int)($data['wallet_id'] ?? 0);
    $amount      = (float)($data['amount'] ?? 0);
    $programId   = (int)($data['program_id'] ?? 0);
    $balanceType = (int)($data['balance_type'] ?? 1);

    if (!$walletId || $amount <= 0) {
        echo json_encode(["code" => 1, "message" => "Please enter a valid amount."]);
        exit;
    }

    if (!$programId) {
        echo json_encode(["code" => 1, "message" => "Please select a program."]);
        exit;
    }

    $db = DBCon::getConnection();

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("SELECT total_balance, is_frozen FROM wallet WHERE id = ? FOR UPDATE");
        $stmt->execute([$walletId]);
        $row = $stmt->fetch();

        if (!$row) {
            $db->rollBack();
            echo json_encode(["code" => 1, "message" => "Wallet not found."]);
            exit;
        }

        if ($row['is_frozen'] == 1) {
            $db->rollBack();
            echo json_encode(["code" => 1, "message" => "This wallet is frozen. Cannot process cash in."]);
            exit;
        }

        $oldBalance = (float)$row['total_balance'];
        $newBalance = $oldBalance + $amount;

        $db->prepare("UPDATE wallet SET total_balance = ? WHERE id = ?")->execute([$newBalance, $walletId]);

        $balStmt = $db->prepare("SELECT id, amount FROM wallet_balances WHERE wallet_id = ? AND program_id = ? AND balance_type = ? LIMIT 1");
        $balStmt->execute([$walletId, $programId, $balanceType]);
        $balRow = $balStmt->fetch();

        if ($balRow) {
            $updatedAmount = (float)$balRow['amount'] + $amount;
            $db->prepare("UPDATE wallet_balances SET amount = ? WHERE id = ?")->execute([$updatedAmount, $balRow['id']]);
        } else {
            $db->prepare("INSERT INTO wallet_balances (wallet_id, program_id, balance_type, amount, created_at) VALUES (?, ?, ?, ?, NOW())")
                ->execute([$walletId, $programId, $balanceType, $amount]);
        }

        $metadata = Wallet::getRequestMetadata();
        $db->prepare("INSERT INTO wallet_logs (wallet_id, action, amount, balance_before, balance_after, metadata, created_at, updated_at) VALUES (?, 2, ?, ?, ?, ?, NOW(), NOW())")
            ->execute([$walletId, $amount, $oldBalance, $newBalance, $metadata]);

        $db->commit();

        echo json_encode([
            "code"    => 0,
            "message" => "Cash in successful!",
            "new_balance" => $newBalance
        ]);

    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log("CASH_IN failed: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "Cash in failed. Please try again."]);
    }

    exit;

} else if ($trans == "GET_WALLET_SUMMARY") {

    $db = DBCon::getConnection();
    $roleId = (int)($_SESSION['role_id'] ?? 0);
    $params = [];

    $where = "WHERE 1=1";

    if ($roleId === 3) {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $where .= " AND b.users_id = ?";
        $params[] = $userId;
    } else if ($roleId === 2) {
        $branchId = $_SESSION['profile']['branch_id'] ?? '';
        if ($branchId !== '') {
            $where .= " AND b.branch_id = ?";
            $params[] = $branchId;
        }
    }

    try {
        $stmt = $db->prepare("SELECT
            COUNT(*) AS total_wallets,
            COALESCE(SUM(w.total_balance), 0) AS total_balance,
            COALESCE(SUM(w.credit_limit), 0) AS total_credit_limit,
            SUM(CASE WHEN w.is_frozen = 1 THEN 1 ELSE 0 END) AS frozen_count,
            SUM(CASE WHEN w.total_balance > 0 THEN 1 ELSE 0 END) AS active_balance_count
        FROM wallet w
        INNER JOIN beneficiary b ON b.id = w.beneficiary_id
        $where");
        $stmt->execute($params);
        $summary = $stmt->fetch();

        echo json_encode([
            "code"    => 0,
            "message" => "Success",
            "data"    => $summary
        ]);

    } catch (PDOException $e) {
        error_log("GET_WALLET_SUMMARY failed: " . $e->getMessage());
        echo json_encode(["code" => 1, "message" => "Failed to load wallet summary."]);
    }

    exit;

} else {
    echo json_encode(["code" => 1, "message" => "Invalid transaction"]);
}
