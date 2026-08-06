<?php

header("Content-Type: application/json");
include_once __DIR__ . '/../config/dbcon.php';

class Booking {

    /**
    * Generates a 15-digit number where the last digit is a Luhn check digit.
    */
    public static function generateBookingNumber($booking_id) { 
        // Generate 15 digits 
        $baseNumber = time() .str_pad($booking_id, 6, '0', STR_PAD_LEFT); 

        // Calculate the check digit
        $sum = 0;
        $shouldDouble = true;
        
        // Loop through the 15 digits in reverse
        for ($i = strlen($baseNumber) - 1; $i >= 0; $i--) {
            $digit = (int)$baseNumber[$i];
            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) $digit -= 9;
            }
            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }
        
        $checkDigit = (10 - ($sum % 10)) % 10;
        return 'BK-' . $baseNumber . $checkDigit;
    }

}
    