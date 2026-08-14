<?php

header("Content-Type: application/json");
include_once __DIR__ . '/../config/dbcon.php';

class Wallet {
    /**
    * This are JSON object that captures the "Who, Where, and How" of the request.
    */
    public static function getRequestMetadata(): string {
        $metadata = [
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_id' => bin2hex(random_bytes(8)), // Unique ID for tracing
            'timestamp'  => date('Y-m-d H:i:s'),
            'path'       => $_SERVER['REQUEST_URI'] ?? 'cli'
        ];
        
        return json_encode($metadata);
    }

    /**
    * Generates a 16-digit number where the last digit is a Luhn check digit.
    */
    public static function generateAccountNumber() {
        $number = '';
        // Generate 15 random digits
        for ($i = 0; $i < 15; $i++) {
            $number .= random_int(0, 9);
        }
        
        // Calculate the check digit
        $sum = 0;
        $shouldDouble = true;
        
        // Loop through the 15 digits in reverse
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int)$number[$i];
            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) $digit -= 9;
            }
            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }
        
        $checkDigit = (10 - ($sum % 10)) % 10;
        return $number . $checkDigit;
    }

    /**
    * Create wallet account number
    */
    public static function createWallet($beneficiary_id) {
        $db = DBCon::getConnection();

        // Check if an outer transaction is already active
        $isNested = $db->inTransaction();

        $attempts = 0;
        $maxAttempts = 10;

        while ($attempts < $maxAttempts) {
            $accNumber = self::generateAccountNumber(); 

            try {
                // 1. Only start a transaction if one isn't already active
                if (!$isNested) {
                    $db->beginTransaction();
                }

                // 2. Insert the new wallet
                $stmt = $db->prepare("INSERT INTO wallet (beneficiary_id, account_num, credit_limit, is_frozen) VALUES (?, ?, 0, 0)");
                $stmt->execute([$beneficiary_id, $accNumber]);
                
                // Get the ID of the wallet we just created so we can log it
                $walletId = $db->lastInsertId();

                // 3. Insert audit log
                $metadata = self::getRequestMetadata(); 
                
                $logStmt = $db->prepare("INSERT INTO wallet_logs (wallet_id, action, amount, balance_before, balance_after, metadata, created_at) VALUES (?, 0, 0, 0, 0, ?, NOW())");
                $logStmt->execute([$walletId, $metadata]);

                // 4. Only commit if this function originally started the transaction
                if (!$isNested) {
                    $db->commit();
                }
                
                // 5. Exit the loop and return the new account number
                return $accNumber;

            } catch (PDOException $e) {
                // Only rollback if this function started the transaction
                if (!$isNested && $db->inTransaction()) {
                    $db->rollBack();
                }

                // Check specifically for MySQL error code 1062 (Duplicate entry)
                if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                    $attempts++;
                    continue; // Loop restarts, generating a new account number
                }

                // If it's a different database error, rethrow it
                throw new Exception("Database error: " . $e->getMessage());
            }
        }
        
        throw new Exception("System busy. Could not generate a unique account number. Please try again.");
    }
    
    /**
    * Update wallets table simultaneously
    */
    public function updateBalance(int $walletId, float $amount, string $action) {
        $this->db->beginTransaction();
        
        try {
            // 1. Get current balance and lock the row for update
            $stmt = $this->db->prepare("SELECT balance FROM wallets WHERE id = ? FOR UPDATE");
            $stmt->execute([$walletId]);
            $oldBalance = $stmt->fetchColumn();
            
            $newBalance = $oldBalance + $amount;
            
            // 2. Update wallet
            $stmt = $this->db->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
            $stmt->execute([$newBalance, $walletId]);
            
            // 3. Insert audit log
            // Inside your WalletRepository (updateBalance method)
            $metadata = getRequestMetadata();
            $stmt = $this->db->prepare("INSERT INTO wallet_logs (wallet_id, action, amount, balance_before, balance_after, metadata) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$walletId, $action, $amount, $oldBalance, $newBalance, $metadata]);
            
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
    