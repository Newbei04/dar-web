<?php

header("Content-Type: application/json");
include_once __DIR__ . '/../config/dbcon.php';

class GblFn {

    /**
    * Error reporting
    */
    public static function errorReporting($display_errors = 1, $log_errors = 1) {
        // Safe settings for a live website
        error_reporting(E_ALL);
        // 0-No, 1-Yes Show errors from public view
        ini_set('display_errors', $display_errors); 
        // 0-No, 1-Yes Keeps saving errors to a error log file
        ini_set('log_errors', $log_errors);     
    }

    /**
    * Recursively trims all string values within an array.
    * @param array $data The raw input array (e.g., decoded JSON)
    * @param bool $removeEmptyElements Optional: Set to true if you want to filter out empty array elements
    * @return array The cleaned and trimmed array
    */
    public static function sanitizeData(array $data): array {
        array_walk_recursive($data, function(&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });
        return $data;
    }

    /**
     * @param array $fileData 
     * @param string $uploadFolder The folder to save to
     * @param string $prefix Prefix for the filename (e.g., 'mac')
     * @param int|string $id The ID associated with the record
     */
    public static function processUpload($fileData, $uploadFolder, $prefix, $id) {
        $filename = '';

        $uploadDir = dirname(__DIR__) . '/assets/images/'.$uploadFolder.'/';
        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $unique = uniqid();

        // 1. Handle Base64
        if (!empty($fileData) && preg_match('/^data:image\/(\w+);base64,/', $fileData, $matches)) {
            $ext = ($matches[1] === 'jpeg') ? 'jpg' : strtolower($matches[1]);
            $image_data = base64_decode(substr($fileData, strpos($fileData, ',') + 1));
            $filename = $prefix . '_' . $id . '_' . time() . '_' . $unique . '.' . $ext;
            
            if (file_put_contents($uploadDir . $filename, $image_data) !== false) {
                return $filename;
            }
        } 
        // 2. Handle $_FILES
        else if (!empty($fileData['name'])) {
            $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
            $filename = $prefix . '_' . $id . '_' . time() . '_' . $unique . '.' . $ext;
            
            if (move_uploaded_file($fileData['tmp_name'], $uploadDir . $filename)) {
                return $filename;
            }
        }
        return $filename; 
    }

    /**
     * Generates an SKU: BRAND-CAT-YYMMDD-RANDOM
     * Example: NIKE-SHOE-260610-A1B2
     */
    public static function generateSKU($brand, $category, $length = 4) {
        // Sanitize and format inputs
        $brandCode = strtoupper(substr(preg_replace('/[^A-Z]/', '', $brand), 0, 4));
        $catCode   = strtoupper(substr(preg_replace('/[^A-Z]/', '', $category), 0, 4));
        $dateCode  = date('ymd');
        
        // Generate a random suffix for uniqueness
        $randomSuffix = strtoupper(substr(md5(uniqid()), 0, $length));

        // Assemble the SKU
        return "{$brandCode}-{$catCode}-{$dateCode}-{$randomSuffix}";
    }

    /*
     * If a record exists, it returns '1' (which casts to boolean true).
     * If no record exists, it returns false.
    */
    public static function recordExists($tableName, $fieldName, $fieldValue) {
        $db = DBCon::getConnection(); 
        $sql = "SELECT 1 FROM `$tableName` WHERE `$fieldName` = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fieldValue]); 
        return (bool) $stmt->fetchColumn();
    }
}

