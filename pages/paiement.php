<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fedapay.php';

try {

    /*
    |--------------------------------------------------------------------------
    | Vérifier la connexion
    |--------------------------------------------------------------------------
    */

    if (empty($_SESSION['user_id'])) {

        echo json_encode([
            'success' => false,
            'message' => 'Vous devez être connecté pour vous inscrire.'
        ]);

        exit;
    }

    $utilisateurId = (int) $_SESSION['user_id'];

    /*
    |--------------------------------------------------------------------------
    | Vérifier la formation
    |--------------------------------------------------------------------------
    */

    $formationId = filter_input(
        INPUT_POST,
        'formation_id',
        FILTER_VALIDATE_INT
    );

    if (!$formationId) {

        echo json_encode([
            'success' => false,
            'message' => 'Formation invalide.'
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Connexion BDD
    |--------------------------------------------------------------------------
    */

    $pdo = getDB();

    /*
    |--------------------------------------------------------------------------
    | Récupérer la formation
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id, titre, prix
        FROM formations
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$formationId]);

    $formation = $stmt->fetch();

    if (!$formation) {

        echo json_encode([
            'success' => false,
            'message' => 'Formation introuvable.'
        ]);

        exit;
    }

    $montant = (float) $formation['prix'];

    /*
    |--------------------------------------------------------------------------
    | Vérifier si l'utilisateur possède déjà une commande
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT *
        FROM commandes
        WHERE utilisateur_id = ?
          AND formation_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $utilisateurId,
        $formationId
    ]);

    $commandeExistante = $stmt->fetch();

    /*
    |--------------------------------------------------------------------------
    | Si déjà payée
    |--------------------------------------------------------------------------
    */

    if (
        $commandeExistante &&
        $commandeExistante['status'] === 'payee'
    ) {

        echo json_encode([
            'success' => false,
            'message' => 'Vous êtes déjà inscrit à cette formation.'
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Créer la référence
    |--------------------------------------------------------------------------
    */

    $reference = 'CMD-' .
        date('YmdHis') .
        '-' .
        strtoupper(bin2hex(random_bytes(3)));

    /*
    |--------------------------------------------------------------------------
    | Créer / mettre à jour la commande
    |--------------------------------------------------------------------------
    */

    if ($commandeExistante) {

        $commandeId = $commandeExistante['id'];

        $stmt = $pdo->prepare("
            UPDATE commandes
            SET montant = ?,
                status = 'en_attente',
                reference = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $montant,
            $reference,
            $commandeId
        ]);

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO commandes
            (
                utilisateur_id,
                formation_id,
                montant,
                status,
                reference
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'en_attente',
                ?
            )
        ");

        $stmt->execute([
            $utilisateurId,
            $formationId,
            $montant,
            $reference
        ]);

        $commandeId = $pdo->lastInsertId();
    }

    /*
    |--------------------------------------------------------------------------
    | Création transaction FedaPay
    |--------------------------------------------------------------------------
    */

    $transaction = creerTransactionFedaPay(
        $montant,
        $formation['titre'],
        $reference,
        $utilisateurId
    );

    /*
    |--------------------------------------------------------------------------
    | Enregistrer la transaction FedaPay
    |--------------------------------------------------------------------------
    */

    $transactionId = $transaction['id'] ?? null;

    $paymentUrl = $transaction['payment_url'] ?? null;

    if (!$transactionId || !$paymentUrl) {

        throw new Exception(
            'FedaPay n\'a pas retourné les informations de paiement.'
        );
    }

    $stmt = $pdo->prepare("
        UPDATE commandes
        SET transaction_id = ?,
            logs = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $transactionId,
        json_encode($transaction, JSON_UNESCAPED_UNICODE),
        $commandeId
    ]);

    /*
    |--------------------------------------------------------------------------
    | Réponse JSON
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        'success' => true,
        'message' => 'Transaction créée avec succès.',
        'payment_url' => $paymentUrl,
        'transaction_id' => $transactionId,
        'reference' => $reference
    ], JSON_UNESCAPED_UNICODE);

    exit;

} catch (Throwable $e) {

    error_log(
        'Erreur paiement : ' . $e->getMessage()
    );

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);

    exit;
}