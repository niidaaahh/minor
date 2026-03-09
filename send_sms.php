<?php
// elderly-care-backend/send_sms.php

define('TWILIO_SID',   'AC7ff3daf2c321eb57e8aa27fd5f1aeb6f'); // ← your Account SID
define('TWILIO_TOKEN', '9976c382e5bf5b5536daf29a6f82b36c');               // ← your Auth Token
define('TWILIO_FROM',  '+19403945982');                       // ← your Twilio number

function sendSMS(string $phone, string $message): bool {
    // Ensure number is in E.164 format e.g. +919876543210
    $phone = trim($phone);
    if (!str_starts_with($phone, '+')) {
        $phone = '+91' . $phone; // assume India if no country code
    }

    $sid   = TWILIO_SID;
    $token = TWILIO_TOKEN;
    $url   = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

    $data = [
        'From' => TWILIO_FROM,
        'To'   => $phone,
        'Body' => $message,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD,        "{$sid}:{$token}");
    curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // for XAMPP localhost

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log('Twilio cURL error: ' . $error);
        return false;
    }

    return $httpCode === 201;
}
?>