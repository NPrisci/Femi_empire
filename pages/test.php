<?php

$secretKey = 'sk_sandbox_0OYGJ2dm_Bo0NDxreq4-lHVh';

$url = 'https://sandbox-api.fedapay.com/v1/transactions';

$data = [
    'description' => 'Test simple FedaPay',
    'amount' => 100,
    'currency' => [
        'iso' => 'XOF'
    ],
    'callback_url' => 'http://localhost:8000/pages/test.php',
    'customer' => [
        'firstname' => 'Test',
        'lastname' => 'Client',
        'email' => 'test@example.com'
    ]
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,

    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ],

    CURLOPT_POSTFIELDS => json_encode($data),

    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

echo '<pre>';

echo "HTTP CODE : " . $httpCode . PHP_EOL;
echo "CURL ERROR : " . $curlError . PHP_EOL;
echo "RESPONSE BRUTE :" . PHP_EOL;
echo $response . PHP_EOL;

echo '</pre>';