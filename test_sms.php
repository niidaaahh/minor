<?php
require_once 'send_sms.php';

$result = sendSMS('+919876543210', 'Test SOS alert.'); // ← put real verified number

echo $result ? '✅ SMS sent!' : '❌ Failed';
?>