<?php
// /config/fedapay.php - Configuration FedaPay

require_once __DIR__ . '/../vendor/autoload.php';

use FedaPay\FedaPay;

// Charger les variables d'environnement
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Configuration FedaPay
$api_key = getenv('FEDAPAY_API_KEY');
$mode = getenv('FEDAPAY_MODE') ?: 'test';

if ($mode === 'test') {
    $api_key = getenv('FEDAPAY_API_KEY_TEST') ?: $api_key;
}

FedaPay::setApiKey($api_key);
FedaPay::setEnvironment($mode === 'live' ? 'production' : 'sandbox');

// Fonctions FedaPay
function createFedaPayTransaction($amount, $customer, $description, $reference, $callback_url) {
    try {
        $transaction = \FedaPay\Transaction::create([
            'amount' => $amount,
            'currency' => 'XOF',
            'description' => $description,
            'reference' => $reference,
            'customer' => $customer,
            'callback_url' => $callback_url,
            'cancel_url' => $callback_url,
            'mode' => 'redirect'
        ]);
        
        return [
            'success' => true,
            'transaction' => $transaction,
            'payment_url' => $transaction->payment_url
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function verifyFedaPayTransaction($transaction_id) {
    try {
        $transaction = \FedaPay\Transaction::retrieve($transaction_id);
        return [
            'success' => true,
            'status' => $transaction->status,
            'transaction' => $transaction
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}