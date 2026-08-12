<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fedapay.php';


try {

    /*
    |--------------------------------------------------------------------------
    | Vérifier que FedaPay nous a envoyé une transaction
    |--------------------------------------------------------------------------
    */

    $transactionId = $_GET['id']
        ?? $_GET['transaction_id']
        ?? null;


    if (!$transactionId) {

        throw new Exception(
            'Identifiant de transaction absent.'
        );
    }


    $transactionId = (int) $transactionId;


    if ($transactionId <= 0) {

        throw new Exception(
            'Identifiant de transaction invalide.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Récupérer la transaction directement chez FedaPay
    |--------------------------------------------------------------------------
    */

    $transaction = getTransactionFedaPay(
        $transactionId
    );


    /*
    |--------------------------------------------------------------------------
    | Vérifier le statut FedaPay
    |--------------------------------------------------------------------------
    */

    $status = strtolower(
        $transaction['status'] ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | Connexion BDD
    |--------------------------------------------------------------------------
    */

    $pdo = getDB();


    /*
    |--------------------------------------------------------------------------
    | Rechercher la commande
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT *
        FROM commandes
        WHERE transaction_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $transactionId
    ]);

    $commande = $stmt->fetch();


    if (!$commande) {

        throw new Exception(
            'Commande associée à cette transaction introuvable.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Enregistrer la réponse FedaPay dans les logs
    |--------------------------------------------------------------------------
    */

    $logs = json_encode(
        $transaction,
        JSON_UNESCAPED_UNICODE |
        JSON_PRETTY_PRINT
    );


    /*
    |--------------------------------------------------------------------------
    | Paiement confirmé
    |--------------------------------------------------------------------------
    */

    if ($status === 'approved') {

        $stmt = $pdo->prepare("
            UPDATE commandes
            SET
                status = 'payee',
                date_obtention = NOW(),
                logs = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $logs,
            $commande['id']
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirection vers les formations
        |--------------------------------------------------------------------------
        */

        header(
            'Location: ../?page=mesformations&paiement=success'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Paiement annulé
    |--------------------------------------------------------------------------
    */

    if (
        $status === 'canceled' ||
        $status === 'cancelled'
    ) {

        $stmt = $pdo->prepare("
            UPDATE commandes
            SET
                status = 'annulee',
                logs = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $logs,
            $commande['id']
        ]);


        header(
            'Location: ../?page=mesformations&paiement=cancelled'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Paiement non confirmé
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE commandes
        SET logs = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $logs,
        $commande['id']
    ]);


    header(
        'Location: ../?page=mesformations&paiement=pending'
    );

    exit;


} catch (Throwable $e) {

    error_log(
        'CALLBACK FEDAPAY : ' .
        $e->getMessage()
    );


    /*
    |--------------------------------------------------------------------------
    | En cas d'erreur
    |--------------------------------------------------------------------------
    */

    header(
        'Location: ../?page=mesformations&paiement=error'
    );

    exit;
}