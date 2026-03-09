<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (!$userMessage) {
    echo json_encode(['success'=>false, 'response'=>'No message sent.']);
    exit;
}

// Hugging Face token
$apiKey = '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';

$data = ["inputs" => $userMessage];

$ch = curl_init('https://api-inference.huggingface.co/models/distilgpt2');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$resData = json_decode($response, true);
$aiMessage = "No response";

if($resData && isset($resData[0]['generated_text'])){
    $aiMessage = $resData[0]['generated_text'];
}

echo json_encode(['success'=>true,'response'=>$aiMessage]);