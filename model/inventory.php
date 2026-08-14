<?php

/**
 * Shared inventory / FEFO / pricing engine.
 *
 * Single source of truth used by ctrl-inventory.php, ctrl-checkout.php,
 * ctrl-product-price.php and ctrl-simulation.php so that stock validation,
 * FEFO ordering and price resolution behave identically everywhere.
 */

header("Content-Type: application/json");
include_once __DIR__ . '/gbl.php';

class InventoryEngine {

    /**
     * Available stock = current - reserved.
     */
    public static function availableStock($current_stock, $reserved_stock) {
        return (float)$current_stock - (float)$reserved_stock;
    }

    /**
     * Authoritative selling price: product_facility_price (active row).
     * Returns null when no active facility price exists.
     */
    public static function getFacilitySellingPrice($conn, $product_id, $facility_id) {
        $res = mysqli_query($conn, "
            SELECT selling_price
            FROM product_facility_price
            WHERE product_id = '" . (int)$product_id . "'
            AND facility_id = '" . (int)$facility_id . "'
            AND status = 1
            LIMIT 1
        ");
        if (!$res) return null;
        $row = mysqli_fetch_assoc($res);
        return $row ? (float)$row['selling_price'] : null;
    }

    /**
     * Batches available for sale at a facility:
     * - active (status = 1)
     * - not expired (expiry is NULL OR expiry_date >= today)
     * - has available stock (current - reserved > 0)
     * Ordered FEFO: no-expiry last, then earliest expiry first, then id.
     */
    public static function getFEFOBatches($conn, $facility_id, $product_id) {
        $sql = "
            SELECT *
            FROM product_inventory
            WHERE facility_id = '" . (int)$facility_id . "'
            AND product_id = '" . (int)$product_id . "'
            AND status = 1
            AND (expiry_date IS NULL OR expiry_date >= CURDATE())
            AND (current_stock - reserved_stock) > 0
            ORDER BY
                (expiry_date IS NULL) ASC,
                expiry_date ASC,
                id ASC
        ";
        $res = mysqli_query($conn, $sql);
        $batches = [];
        if (!$res) return $batches;
        while ($row = mysqli_fetch_assoc($res)) {
            $batches[] = $row;
        }
        return $batches;
    }

    /**
     * Allocate a quantity across batches following FEFO order.
     *
     * @return array<int,array{inventory_id:int,batch_number:string,product_id:int,facility_id:int,quantity:float}>
     * @throws \RuntimeException when available stock is insufficient
     */
    public static function allocateFEFO($batches, $qty) {
        $remaining = (float)$qty;
        $allocation = [];
        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $avail = self::availableStock($batch['current_stock'], $batch['reserved_stock']);
            if ($avail <= 0) continue;
            $take = min($remaining, $avail);
            $allocation[] = [
                'inventory_id' => (int)$batch['id'],
                'batch_number' => isset($batch['batch_number']) ? (string)$batch['batch_number'] : '',
                'product_id'   => (int)$batch['product_id'],
                'facility_id'  => (int)$batch['facility_id'],
                'quantity'     => $take
            ];
            $remaining -= $take;
        }
        if ($remaining > 0) {
            throw new \RuntimeException(
                'Insufficient available stock (short by ' . rtrim(rtrim(number_format($remaining, 2, '.', ''), '0'), '.') . ')'
            );
        }
        return $allocation;
    }

    /**
     * Aggregate available stock (current - reserved) across active, non-expired
     * batches of a product at a facility.
     */
    public static function getAvailableByProduct($conn, $facility_id, $product_id) {
        $res = mysqli_query($conn, "
            SELECT COALESCE(SUM(current_stock - reserved_stock), 0) AS available
            FROM product_inventory
            WHERE facility_id = '" . (int)$facility_id . "'
            AND product_id = '" . (int)$product_id . "'
            AND status = 1
            AND (expiry_date IS NULL OR expiry_date >= CURDATE())
        ");
        if (!$res) return 0;
        $row = mysqli_fetch_assoc($res);
        return $row ? (float)$row['available'] : 0;
    }

    /**
     * Aggregate total current stock of a product at a facility (all batches).
     */
    public static function getCurrentByProduct($conn, $facility_id, $product_id) {
        $res = mysqli_query($conn, "
            SELECT COALESCE(SUM(current_stock), 0) AS total
            FROM product_inventory
            WHERE facility_id = '" . (int)$facility_id . "'
            AND product_id = '" . (int)$product_id . "'
        ");
        if (!$res) return 0;
        $row = mysqli_fetch_assoc($res);
        return $row ? (float)$row['total'] : 0;
    }

    /**
     * Minimum quantity that must be deducted (sale/damage/expired) so that
     * reserved stock is never consumed.
     */
    public static function assertReservedSafe($current_stock, $reserved_stock, $qty) {
        $after = (float)$current_stock - (float)$qty;
        if ($after < (float)$reserved_stock) {
            throw new \RuntimeException(
                'Deduction would consume reserved stock (reserved ' . (float)$reserved_stock . ', available ' . self::availableStock($current_stock, $reserved_stock) . ')'
            );
        }
    }
}
