<?php

// ================================================
// pages/callback.php
// Callback FedaPay
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fedapay.php';

$pdo = getDB();

try {

    // ------------------------------------------------
    // 1. Récupération des paramètres FedaPay
    // ------------------------------------------------

    $transactionId = $_GET['id'] ?? null;
    $status        = $_GET['status'] ?? null;

    if (!$transactionId) {
        throw new Exception('Identifiant de transaction manquant.');
    }

    $transactionId = (int) $transactionId;


    // ------------------------------------------------
    // 2. Vérification de la transaction auprès de FedaPay
    // ------------------------------------------------

    // $transaction = fedapayGetTransaction($transactionId);

    // if (!$transaction) {
    //     throw new Exception(
    //         'Impossible de récupérer la transaction FedaPay.'
    //     );
    // }


    // ------------------------------------------------
    // 3. Récupération du statut réel
    // ------------------------------------------------

    // $fedapayStatus = $transaction['status'] ?? null;

    $transaction = fedapayGetTransaction($transactionId);
    error_log(
        '[FedaPay CALLBACK] Transaction récupérée : ' .
            print_r($transaction, true)
    );
    $fedapayStatus = $transaction['status'] ?? null;

    error_log(
        '[FedaPay CALLBACK] ID=' .
            $transactionId .
            ' STATUS=' .
            var_export($fedapayStatus, true)
    );

    if (!$transaction) {
        throw new Exception(
            'Impossible de récupérer la transaction FedaPay.'
        );
    }

    error_log(
        '[FedaPay CALLBACK] TYPE=' . gettype($transaction) .
            ' DATA=' . print_r($transaction, true)
    );

    if (is_array($transaction)) {
        $fedapayStatus = $transaction['status'] ?? null;
    } elseif (is_object($transaction)) {
        $fedapayStatus = $transaction->status ?? null;
    } else {
        throw new Exception(
            'Réponse FedaPay inattendue : ' . gettype($transaction)
        );
    }

    // ------------------------------------------------
    // 4. Recherche de la commande
    // ------------------------------------------------

    $stmt = $pdo->prepare("
        SELECT *
        FROM commandes
        WHERE transaction_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        (string) $transactionId
    ]);

    $commande = $stmt->fetch();


    if (!$commande) {
        throw new Exception(
            'Commande introuvable pour cette transaction.'
        );
    }


    // ------------------------------------------------
    // 5. Paiement confirmé
    // ------------------------------------------------

    if ($fedapayStatus === 'approved') {

        $stmt = $pdo->prepare("
            UPDATE commandes
            SET
                status = 'payee',
                date_obtention = NOW(),
                logs = ?
            WHERE id = ?
        ");

        $log = json_encode([
            'event' => 'payment_approved',
            'transaction_id' => $transactionId,
            'status' => $fedapayStatus,
            'date' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);

        $stmt->execute([
            $log,
            $commande['id']
        ]);

        // Redirection vers les formations de l'utilisateur
        header('Location: ../?page=mesformations&paiement=success');
        exit;
    }


    // ------------------------------------------------
    // 6. Paiement annulé / refusé
    // ------------------------------------------------

    if (
        $fedapayStatus === 'canceled' ||
        $fedapayStatus === 'declined'
    ) {

        $stmt = $pdo->prepare("
            UPDATE commandes
            SET
                status = 'annulee',
                logs = ?
            WHERE id = ?
        ");

        $log = json_encode([
            'event' => 'payment_failed',
            'transaction_id' => $transactionId,
            'status' => $fedapayStatus,
            'date' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);

        $stmt->execute([
            $log,
            $commande['id']
        ]);

        header('Location: ../?page=formation&paiement=failed');
        exit;
    }


    // ------------------------------------------------
    // 7. Autre statut
    // ------------------------------------------------

    header('Location: ../?page=formation&paiement=pending');
    exit;
} catch (Throwable $e) {

    // ------------------------------------------------
    // LOG serveur
    // ------------------------------------------------

    error_log(
        '[FedaPay CALLBACK] ' .
            $e->getMessage() .
            ' | fichier=' .
            $e->getFile() .
            ' | ligne=' .
            $e->getLine()
    );

    // Ne jamais afficher l'erreur PHP à l'utilisateur
    http_response_code(500);

    echo '
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Erreur de paiement</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f8f9fa;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
            }

            .box {
                background: white;
                padding: 40px;
                border-radius: 15px;
                max-width: 500px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0,0,0,.08);
            }

            h1 {
                color: #dc3545;
            }

            a {
                display: inline-block;
                margin-top: 20px;
                padding: 12px 20px;
                background: #8B5CF6;
                color: white;
                text-decoration: none;
                border-radius: 8px;
            }
        </style>
    </head>

    <body>

        <div class="box">

            <h1>Erreur de paiement</h1>

            <p>
                Une erreur est survenue lors de la confirmation
                de votre paiement.
            </p>

            <a href="../?page=formation">
                Retour aux formations
            </a>

        </div>

    </body>
    </html>
    ';

    exit;
}
