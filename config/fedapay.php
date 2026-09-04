<?php

define(
    'FEDAPAY_SECRET_KEY',
    'sk_sandbox_0OYGJ2dm_Bo0NDxreq4-lHVh'
);

define(
    'FEDAPAY_API_URL',
    'https://sandbox-api.fedapay.com'
);

define(
    'FEDAPAY_CALLBACK_URL',
    'https://femiempire.free.nf/pages/callback.php'
);


/**
 * Créer une transaction FedaPay
 */
function creerTransactionFedaPay(
    float $montant,
    string $description,
    string $reference,
    int $utilisateurId
): array {

    $payload = [
        'amount' => (int) $montant,

        'currency' => [
            'iso' => 'XOF'
        ],

        'description' => $description,

        'callback_url' => FEDAPAY_CALLBACK_URL
    ];

    $ch = curl_init(
        FEDAPAY_API_URL . '/v1/transactions'
    );

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE
        ),

        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . FEDAPAY_SECRET_KEY,
            'Content-Type: application/json',
            'Accept: application/json'
        ],

        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);

    $curlError = curl_error($ch);

    $httpCode = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    if ($curlError) {
        throw new Exception(
            'Erreur CURL FedaPay : ' . $curlError
        );
    }

    $result = json_decode(
        $response,
        true
    );

    if ($httpCode < 200 || $httpCode >= 300) {

        throw new Exception(
            'FedaPay HTTP ' .
            $httpCode .
            ' : ' .
            $response
        );
    }

    if (!is_array($result)) {

        throw new Exception(
            'Réponse FedaPay invalide : ' .
            $response
        );
    }

    if (isset($result['v1/transaction'])) {
        return $result['v1/transaction'];
    }

    return $result;
}


/**
 * Récupérer une transaction FedaPay
 */

function fedapayGetTransaction(int $transactionId): ?array
{
    $url = FEDAPAY_API_URL . '/v1/transactions/' . $transactionId;

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . FEDAPAY_SECRET_KEY,
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);

    $curlError = curl_error($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($curlError) {
        throw new Exception(
            'Erreur CURL FedaPay : ' . $curlError
        );
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        throw new Exception(
            'FedaPay HTTP ' .
            $httpCode .
            ' : ' .
            $response
        );
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        throw new Exception(
            'Réponse FedaPay invalide : ' .
            $response
        );
    }

    // FedaPay retourne généralement la transaction
    // sous la clé v1/transaction
    return $data['v1/transaction']
        ?? $data['transaction']
        ?? $data;
}