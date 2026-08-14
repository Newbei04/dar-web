<?php

header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$trans = $data['trans'] ?? $_REQUEST['trans'] ?? '';

if (empty($trans)) {
    echo json_encode([
        "code" => 1,
        "message" => "Transaction is required"
    ]);
    exit;
}

// 0 = ENROLLED   (default when added)
// 1 = RELEASED   (approved / ready for claim)
// 2 = RECEIVED   (claimed / completed)
// 3 = CANCELLED  (void / removed from process)

if ($trans == "ADD_PROGRAM_BENEFICIARY") {

    $allocation_id = $data['allocation_id'] ?? '';
    $beneficiary_ids       = $data['beneficiary_ids'] ?? [];

    if (!$allocation_id || empty($beneficiary_ids)) {
        echo json_encode([
            "code" => 1,
            "message" => "Invalid input data"
        ]);
        exit;
    }

    $allocQ = mysqli_query($conn, "
        SELECT *
        FROM program_allocation
        WHERE id = '$allocation_id'
        LIMIT 1
    ");

    $allocation = mysqli_fetch_assoc($allocQ);

    if (!$allocation) {
        echo json_encode([
            "code" => 1,
            "message" => "Program allocation not found"
        ]);
        exit;
    }

    $program_id = $allocation['program_id'];

    // Check max beneficiary limit
    $max_beneficiary = $allocation['reserved_budget'] ?? 0;

    if ($max_beneficiary > 0) {

        $beneficiaryCountQ = mysqli_query($conn, "
            SELECT COUNT(DISTINCT beneficiary_id) AS total
            FROM program_beneficiary
            WHERE allocation_id = '$allocation_id'
        ");

        $beneficiaryCount = mysqli_fetch_assoc($beneficiaryCountQ)['total'];

        if (($beneficiaryCount + count($beneficiary_ids)) > $max_beneficiary) {
            echo json_encode([
                "code" => 1,
                "message" => "Maximum beneficiary limit will be exceeded"
            ]);
            exit;
        }
    }

    $success = 0;
    $skipped = 0;
    $errors  = [];

    // foreach ($beneficiary_ids as $beneficiary_id) {

    //     $beneficiary_id = mysqli_real_escape_string($conn, $beneficiary_id);

    //     if (!$beneficiary_id || $beneficiary_id == 'undefined') {
    //         $skipped++;
    //         continue;
    //     }

    //     // Check if already enrolled in this allocation
    //     $enrolledQ = mysqli_query($conn, "
    //         SELECT id, status
    //         FROM program_beneficiary
    //         WHERE allocation_id = '$allocation_id'
    //         AND beneficiary_id = '$beneficiary_id'
    //         LIMIT 1
    //     ");

    //     if (mysqli_num_rows($enrolledQ) > 0) {

    //         $existing = mysqli_fetch_assoc($enrolledQ);

    //         if ($existing['status'] != 2) {
    //             $skipped++;
    //             continue;
    //         }

    //         // Re-enroll existing record
    //         mysqli_query($conn, "
    //         UPDATE program_beneficiary
    //         SET
    //             status = 0,
    //             date_enrolled = NOW()
    //         WHERE id = '{$existing['id']}'
    //     ");
    //     } else {
    //         mysqli_query($conn, "
    //         INSERT INTO program_beneficiary (
    //             allocation_id,
    //             beneficiary_id,
    //             status,
    //             date_enrolled,
    //             created_at
    //         )
    //         VALUES (
    //             '$allocation_id',
    //             '$beneficiary_id',
    //             0,
    //             NOW(),
    //             NOW()
    //         )
    //     ");
    //     }

    //     // Check duplicate product enrollment
    //     if (!empty($allocation['product_id'])) {

    //         $product_id = $allocation['product_id'];

    //         $existingQ = mysqli_query($conn, "
    //             SELECT pb.id
    //             FROM program_beneficiary pb
    //             INNER JOIN program_allocation pa
    //                 ON pa.id = pb.allocation_id
    //             WHERE pb.beneficiary_id = '$beneficiary_id'
    //             AND pa.product_id = '$product_id'
    //             LIMIT 1
    //         ");

    //         if (mysqli_num_rows($existingQ) > 0) {
    //             $skipped++;
    //             continue;
    //         }
    //     }

    //     // Check max per beneficiary
    //     // Check maximum beneficiaries based on reserved budget
    //     $reserved_budget      = (float)$allocation['reserved_budget'];
    //     $max_per_beneficiary  = (float)$allocation['max_per_beneficiary'];

    //     if ($max_per_beneficiary > 0) {

    //         // Maximum beneficiaries allowed by the reserved budget
    //         $max_beneficiary = floor($reserved_budget / $max_per_beneficiary);

    //         // Count currently active beneficiaries
    //         $beneficiaryCountQ = mysqli_query($conn, "
    //             SELECT COUNT(DISTINCT beneficiary_id) AS total
    //             FROM program_beneficiary
    //             WHERE allocation_id = '$allocation_id'
    //             AND status != 2
    //         ");

    //         $currentBeneficiaries = (int)mysqli_fetch_assoc($beneficiaryCountQ)['total'];

    //         $newBeneficiaries = 0;

    //         foreach ($beneficiary_ids as $beneficiary_id) {

    //             $beneficiary_id = mysqli_real_escape_string($conn, $beneficiary_id);

    //             if (!$beneficiary_id || $beneficiary_id == 'undefined') {
    //                 continue;
    //             }

    //             $existsQ = mysqli_query($conn, "
    //                 SELECT id
    //                 FROM program_beneficiary
    //                 WHERE allocation_id = '$allocation_id'
    //                 AND beneficiary_id = '$beneficiary_id'
    //                 LIMIT 1
    //             ");

    //             if (mysqli_num_rows($existsQ) == 0) {
    //                 $newBeneficiaries++;
    //             }
    //         }

    //         if (($currentBeneficiaries + $newBeneficiaries) > $max_beneficiary) {

    //             $remainingSlots = $max_beneficiary - $currentBeneficiaries;

    //             echo json_encode([
    //                 "code" => 1,
    //                 "message" => "Allocation allows only {$max_beneficiary} beneficiary(ies). Remaining slot(s): {$remainingSlots}."
    //             ]);
    //             exit;
    //         }
    //     }

    //     // Check remaining allocation
    //     $quantity = 1;

    //     $available =
    //         (float)$allocation['allocated_budget']
    //         - (float)$allocation['distributed_budget'];

    //     if ($available < $quantity) {
    //         $errors[] = "Allocation budget exhausted";
    //         continue;
    //     }

    //     // Enroll beneficiary
    //     mysqli_query($conn, "
    //         INSERT INTO program_beneficiary (
    //             allocation_id,
    //             beneficiary_id,
    //             status,
    //             date_enrolled,
    //             created_at
    //         )
    //         VALUES (
    //             '$allocation_id',
    //             '$beneficiary_id',
    //             0,
    //             NOW(),
    //             NOW()
    //         )
    //     ");

    //     // Update allocation
    //     mysqli_query($conn, "
    //         UPDATE program_allocation
    //         SET
    //             distributed_budget = distributed_budget + $quantity,
    //             reserved_budget = allocated_budget - (distributed_budget + $quantity)
    //         WHERE id = '{$allocation['id']}'
    //     ");

    //     $allocation['distributed_budget'] += $quantity;

    //     $success++;
    // }

    foreach ($beneficiary_ids as $beneficiary_id) {

        $beneficiary_id = mysqli_real_escape_string($conn, $beneficiary_id);

            if (!$beneficiary_id || $beneficiary_id == 'undefined') {
                $skipped++;
                continue;
            }

            // Check existing enrollment in this allocation
            $existingQ = mysqli_query($conn, "
                SELECT id, status
                FROM program_beneficiary
                WHERE allocation_id = '$allocation_id'
                AND beneficiary_id = '$beneficiary_id'
                LIMIT 1
            ");

        $existing = null;

        if (mysqli_num_rows($existingQ) > 0) {

            $existing = mysqli_fetch_assoc($existingQ);

            // Already active
            if ($existing['status'] != 2) {
                $skipped++;
                continue;
            }
        }

        // Check duplicate product enrollment
        if (!empty($allocation['product_id'])) {

            $product_id = $allocation['product_id'];

            $duplicateProductQ = mysqli_query($conn, "
            SELECT pb.id
            FROM program_beneficiary pb
            INNER JOIN program_allocation pa
                ON pa.id = pb.allocation_id
            WHERE pb.beneficiary_id = '$beneficiary_id'
            AND pa.product_id = '$product_id'
            AND pb.status != 2
            LIMIT 1
        ");

            if (mysqli_num_rows($duplicateProductQ) > 0) {
                $skipped++;
                continue;
            }
        }

        // Check allocation capacity
        $reserved_budget     = (float)$allocation['reserved_budget'];
        $max_per_beneficiary = (float)$allocation['max_per_beneficiary'];

        if ($max_per_beneficiary > 0) {

            $maxBeneficiaries = floor($reserved_budget / $max_per_beneficiary);

            $countQ = mysqli_query($conn, "
            SELECT COUNT(*) AS total
            FROM program_beneficiary
            WHERE allocation_id = '$allocation_id'
            AND status != 2
        ");

            $currentCount = (int)mysqli_fetch_assoc($countQ)['total'];

            if ($currentCount >= $maxBeneficiaries) {

                echo json_encode([
                    "code" => 1,
                    "message" => "Allocation already reached its maximum of {$maxBeneficiaries} beneficiaries."
                ]);
                exit;
            }
        }

        // Check available budget
        $quantity = 1;

        $available =
            (float)$allocation['allocated_budget']
            - (float)$allocation['distributed_budget'];

        if ($available < $quantity) {
            $errors[] = "Allocation budget exhausted.";
            continue;
        }

        // Re-enroll if cancelled
        if ($existing) {

            mysqli_query($conn, "
            UPDATE program_beneficiary
            SET
                status = 0,
                date_enrolled = NOW()
            WHERE id = '{$existing['id']}'
        ");
        } else {

            mysqli_query($conn, "
            INSERT INTO program_beneficiary (
                allocation_id,
                beneficiary_id,
                status,
                date_enrolled,
                created_at
            )
            VALUES (
                '$allocation_id',
                '$beneficiary_id',
                0,
                NOW(),
                NOW()
            )
        ");
        }

        // Update allocation
        mysqli_query($conn, "
            UPDATE program_allocation
            SET
                distributed_budget = distributed_budget + $quantity,
                reserved_budget = allocated_budget - (distributed_budget + $quantity)
            WHERE id = '$allocation_id'
        ");

        $allocation['distributed_budget'] += $quantity;

        $success++;
    }

    // Recompute program remaining budget
    mysqli_query($conn, "
        UPDATE program p
        SET remaining_budget = (
            SELECT COALESCE(SUM(remaining_budget), 0)
            FROM program_allocation
            WHERE program_id = '$program_id'
        )
        WHERE p.id = '$program_id'
    ");

    $msg = "$success beneficiary(ies) enrolled successfully.";

    if ($skipped > 0) {
        $msg .= " $skipped skipped (already enrolled or limit reached).";
    }

    if (!empty($errors)) {
        $msg .= " " . implode(", ", $errors);
    }

    echo json_encode([
        "code" => 0,
        "message" => $msg,
        "data" => [
            "success" => $success,
            "skipped" => $skipped
        ]
    ]);

    exit;
} elseif ($trans == "LIST_PROGRAM_BENEFICIARY") {

    $allocation_id = $data['allocation_id'] ?? '';

    $query = mysqli_query($conn, "
        SELECT
            pb.*,

            pa.id AS allocation_id,
            pa.subsidy_type,
            pa.max_per_beneficiary,
            pa.unit_subsidy_value,

            p.id AS program_id,
            p.code AS program_code,
            p.name AS program_name,
            p.asset_type,

            b.doc_num,
            b.card_num,
            b.email,
            b.mobile,
            CONCAT(b.fname,' ',b.lname) AS beneficiary_name

        FROM program_beneficiary pb

        LEFT JOIN beneficiary b
            ON b.id = pb.beneficiary_id

        LEFT JOIN program_allocation pa
            ON pa.id = pb.allocation_id

        LEFT JOIN program p
            ON p.id = pa.program_id

        WHERE pb.allocation_id = '$allocation_id'

        ORDER BY pb.id DESC
    ");

    $rows = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = $row;
    }

    echo json_encode([
        "code" => 0,
        "message" => "Success",
        "data" => $rows
    ]);
} elseif ($trans == "LIST_BENEFICIARY") {

    $allocation_id = $data['allocation_id'] ?? '';

    $alloc = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT branch_id FROM program_allocation WHERE id = '$allocation_id' LIMIT 1
    "));

    $branch_id = $alloc['branch_id'] ?? '';

    $query = mysqli_query($conn, "
        SELECT
            b.id,
            b.fname,
            b.lname,
            b.doc_num,
            b.card_num,
            b.mobile
        FROM beneficiary b
        WHERE b.status = 1
        AND b.id NOT IN (
            SELECT beneficiary_id
            FROM program_beneficiary
            WHERE allocation_id = '$allocation_id'
        )
        ORDER BY b.lname ASC;
    ");

    $rows = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $rows[] = [
            "id"   => $row['id'],
            "profile" => [
                "fname"    => $row['fname'],
                "lname"    => $row['lname'],
                // "email"    => $row['email'],
                "mobile"   => $row['mobile'],
                "doc_num"  => $row['doc_num'],
                "card_num" => $row['card_num'],
            ]
        ];
    }

    echo json_encode([
        "code"    => 0,
        "message" => "Success",
        "data"    => $rows
    ]);
} elseif ($trans == "GET_PROGRAM_ALLOCATION") {

    $id = $data['allocation_id'];

    $query = mysqli_query($conn, "
        SELECT
            pa.*,
            p.name AS program_name,
            p.code AS program_code,
            p.total_budget,
            p.remaining_budget,
            p.asset_type,
            br.name AS branch_name,
            pr.name AS product_name

        FROM program_allocation pa

        LEFT JOIN program p
            ON p.id = pa.program_id

        LEFT JOIN branch br
            ON br.id = pa.branch_id

        LEFT JOIN product pr
            ON pr.id = p.product_id

        WHERE pa.id = '$id'
        LIMIT 1
    ");

    $row = mysqli_fetch_assoc($query);

    echo json_encode([
        "code" => 0,
        "data" => $row
    ]);

    exit;
} else if ($trans == "GET_PROGRAM_BENEFICIARY") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required"
        ]);
        exit;
    }

    $query = mysqli_query($conn, "
        SELECT
            pb.*,
            pa.program_id,
            pa.branch_id,
            p.product_id,
            pa.allocated_budget,
            pa.distributed_budget,
            pa.reserved_budget,

            p.name AS program_name,
            p.code AS program_code,
            p.start_date,
            p.end_date,

            b.doc_num,
            b.card_num,
            b.email,
            b.mobile,
            CONCAT(b.fname, ' ', b.lname) AS beneficiary_name

        FROM program_beneficiary pb

        LEFT JOIN program_allocation pa
            ON pa.id = pb.allocation_id

        LEFT JOIN program p
            ON p.id = pa.program_id

        LEFT JOIN beneficiary b
            ON b.id = pb.beneficiary_id

        WHERE pb.id = '$id'

        LIMIT 1
    ");

    $row = mysqli_fetch_assoc($query);

    if ($row) {

        echo json_encode([
            "code" => 0,
            "message" => "Success",
            "data" => $row
        ]);
    } else {

        echo json_encode([
            "code" => 1,
            "message" => "Record not found"
        ]);
    }
} else if ($trans == "UPDATE_PROGRAM_BENEFICIARY_STATUS") {

    $id     = $data['id'] ?? '';
    $action = $data['action'] ?? '';

    if (!$id || !$action) {
        echo json_encode([
            "code" => 1,
            "message" => "Invalid request"
        ]);
        exit;
    }

    // Get current beneficiary record
    $q = mysqli_query($conn, "
        SELECT pb.*, pa.max_per_beneficiary
        FROM program_beneficiary pb
        LEFT JOIN program_allocation pa
            ON pa.id = pb.allocation_id
        WHERE pb.id = '$id'
        LIMIT 1
    ");

    $row = mysqli_fetch_assoc($q);

    if (!$row) {
        echo json_encode([
            "code" => 1,
            "message" => "Record not found"
        ]);
        exit;
    }

    $current_status = (int)$row['status'];

    // THIS is the amount to store when received
    $amount = $row['max_per_beneficiary'] ?? 0;

    $status = $current_status;
    $date_received = "";

    if ($action == "RELEASE") {

        $status = 1;
    } else if ($action == "RECEIVE") {

        $status = 2;
        $date_received = ", date_received = NOW()";

        // Only apply once
        if ($current_status != 2) {

            mysqli_query($conn, "
                UPDATE program_beneficiary
                SET recieved_budget = $amount
                WHERE id = '$id'
            ");
        }
    } else if ($action == "CANCEL") {

        $status = 3;
    } else {

        echo json_encode([
            "code" => 1,
            "message" => "Invalid action"
        ]);
        exit;
    }

    // Update main status
    mysqli_query($conn, "
        UPDATE program_beneficiary
        SET status = '$status' $date_received
        WHERE id = '$id'
    ");

    echo json_encode([
        "code" => 0,
        "message" => "Status updated successfully",
        "data" => [
            "id" => $id,
            "status" => $status
        ]
    ]);

    exit;
} else if ($trans == "DELETE_PROGRAM_BENEFICIARY") {

    $id = $data['id'] ?? '';

    if (!$id) {
        echo json_encode([
            "code" => 1,
            "message" => "ID is required"
        ]);
        exit;
    }

    mysqli_query($conn, "DELETE FROM program_beneficiary WHERE id = '$id'");

    echo json_encode([
        "code" => 0,
        "message" => "Deleted successfully"
    ]);
} else {
    echo json_encode([
        "code" => 1,
        "message" => "Invalid transaction",
        "data" => null
    ]);
    exit;
}
