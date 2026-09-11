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
     * Handles file uploads. Supports single files, multiple files,
     * base64 data URIs, and native $_FILES uploads.
     *
     * @param mixed $fileData
     *   - string: A single base64 data URI (e.g. "data:image/png;base64,...")
     *   - array of strings: Multiple base64 data URIs (a sequential list)
     *   - array: A single native $_FILES entry, e.g. $_FILES['profile']
     *   - array: A multi-upload $_FILES entry, e.g. $_FILES['images']
     *            where 'name'/'tmp_name'/'error' are arrays
     * @param string $uploadFolder The folder to save to
     * @param string $prefix Prefix for the filename (e.g., 'mac')
     * @param int|string $id The ID associated with the record
     * @return string|array Single filename string for a single file,
     *                      or an array of filenames for multiple files.
     */
    public static function processUpload($fileData, $uploadFolder, $prefix, $id) {
        $uploadDir = dirname(__DIR__) . '/assets/images/'.$uploadFolder.'/';
        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // 1. Multiple native files: $_FILES['x']['name'] is an array
        if (is_array($fileData) && isset($fileData['name']) && is_array($fileData['name'])) {
            $filenames = [];
            foreach ($fileData['name'] as $key => $name) {
                $single = [
                    'name'     => $fileData['name'][$key],
                    'tmp_name' => $fileData['tmp_name'][$key] ?? '',
                    'error'    => $fileData['error'][$key] ?? UPLOAD_ERR_NO_FILE,
                    'size'     => $fileData['size'][$key] ?? 0,
                ];
                if (!empty($single['tmp_name']) && (int)$single['error'] === UPLOAD_ERR_OK) {
                    $saved = self::saveFromFiles($single, $uploadDir, $prefix, $id);
                    if ($saved !== '') {
                        $filenames[] = $saved;
                    }
                }
            }
            return $filenames;
        }

        // 2. Single native file: $_FILES['profile']
        if (is_array($fileData) && isset($fileData['name']) && is_string($fileData['name'])) {
            if (!empty($fileData['tmp_name']) && (int)($fileData['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                return self::saveFromFiles($fileData, $uploadDir, $prefix, $id);
            }
            return '';
        }

        // 3. Multiple base64 strings: a sequential list of strings
        if (is_array($fileData)) {
            $filenames = [];
            foreach ($fileData as $item) {
                if (is_string($item)) {
                    $saved = self::saveFromBase64($item, $uploadDir, $prefix, $id);
                    if ($saved !== '') {
                        $filenames[] = $saved;
                    }
                }
            }
            return $filenames;
        }

        // 4. Single base64 string
        if (is_string($fileData)) {
            return self::saveFromBase64($fileData, $uploadDir, $prefix, $id);
        }

        return '';
    }

    /**
     * Saves a single base64 data URI and returns the generated filename.
     */
    private static function saveFromBase64($fileData, $uploadDir, $prefix, $id) {
        if (empty($fileData) || !preg_match('/^data:image\/(\w+);base64,/', $fileData, $matches)) {
            return '';
        }
        $ext = ($matches[1] === 'jpeg') ? 'jpg' : strtolower($matches[1]);
        $image_data = base64_decode(substr($fileData, strpos($fileData, ',') + 1));
        $filename = $prefix . '_' . $id . '_' . time() . '_' . uniqid() . '.' . $ext;

        if (file_put_contents($uploadDir . $filename, $image_data) !== false) {
            return $filename;
        }
        return '';
    }

    /**
     * Saves a single native $_FILES entry and returns the generated filename.
     */
    private static function saveFromFiles($fileData, $uploadDir, $prefix, $id) {
        if (empty($fileData['name']) || empty($fileData['tmp_name'])) {
            return '';
        }
        $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        $filename = $prefix . '_' . $id . '_' . time() . '_' . uniqid() . '.' . $ext;

        if (move_uploaded_file($fileData['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }
        return '';
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

