<?php
// /webhook/fedapay.php - Webhook pour FedaPay

error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fedapay.php';

// Créer le dossier logs
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

file_put_contents(__DIR__ . '/../logs/webhook.log', date('Y-m-d H:i:s') . " - Webhook reçu\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../logs/webhook.log', $input . "\n\n", FILE_APPEND);

if (!isset($data['type']) || !isset($data['data'])) {
    http_response_code(400);
    echo 'Données invalides';
    exit;
}

$event_type = $data['type'];
$event_data = $data['data'];

try {
    $pdo = getDB();
    
    if ($event_type === 'transaction.approved') {
        $transaction_id = $event_data['id'] ?? '';
        if ($transaction_id) {
            updateFedaPayTransaction($transaction_id, 'approved', $event_data);
            http_response_code(200);
            echo 'OK';
            exit;
        }
    } elseif ($event_type === 'transaction.canceled') {
        $transaction_id = $event_data['id'] ?? '';
        if ($transaction_id) {
            updateFedaPayTransaction($transaction_id, 'canceled', $event_data);
        }
        http_response_code(200);
        echo 'OK';
        exit;
    } elseif ($event_type === 'transaction.failed') {
        $transaction_id = $event_data['id'] ?? '';
        if ($transaction_id) {
            updateFedaPayTransaction($transaction_id, 'failed', $event_data);
        }
        http_response_code(200);
        echo 'OK';
        exit;
    }
    
    http_response_code(200);
    echo 'OK';
    
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/../logs/webhook.log', "Erreur: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo 'Erreur';
}