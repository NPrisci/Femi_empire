<?php
// /pages/client/confirmation.php - Confirmation de paiement

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/fedapay.php';

// Démarrer la session si elle n'est pas active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: /index.php?page=login');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$pdo = getDB();

$transaction_id = isset($_GET['transaction_id']) ? trim($_GET['transaction_id']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$reference = isset($_GET['reference']) ? trim($_GET['reference']) : '';

$success = false;
$message = '';
$commande = null;

// Si un transaction_id est présent, vérifier le statut
if ($transaction_id) {
    // Vérifier la transaction dans notre base
    $stmt = $pdo->prepare("SELECT * FROM transactions_fedapay WHERE transaction_id = ?");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch();
    
    if ($transaction) {
        // Vérifier le statut auprès de FedaPay
        $result = verifyFedaPayTransaction($transaction_id);
        
        if ($result['success']) {
            $fedapay_status = $result['status'];
            
            // Mettre à jour notre base
            if ($fedapay_status === 'approved') {
                updateFedaPayTransaction($transaction_id, 'approved');
                $success = true;
                $message = 'Votre paiement a été confirmé avec succès ! Vous êtes maintenant inscrit à la formation.';
                
                // Récupérer les infos de la commande
                $stmt = $pdo->prepare("SELECT c.*, f.titre as formation_titre FROM commandes c JOIN formations f ON c.formation_id = f.id WHERE c.id = ?");
                $stmt->execute([$transaction['commande_id']]);
                $commande = $stmt->fetch();
            } elseif ($fedapay_status === 'canceled') {
                $message = 'Le paiement a été annulé.';
            } elseif ($fedapay_status === 'failed') {
                $message = 'Le paiement a échoué. Veuillez réessayer.';
            } else {
                $message = 'Le paiement est en cours de traitement.';
            }
        } else {
            $message = 'Erreur lors de la vérification du paiement : ' . $result['error'];
        }
    } else {
        $message = 'Transaction non trouvée.';
    }
} elseif ($status === 'canceled') {
    $message = 'Vous avez annulé le paiement.';
} elseif ($reference) {
    // Vérifier par référence
    $stmt = $pdo->prepare("SELECT c.*, f.titre as formation_titre FROM commandes c JOIN formations f ON c.formation_id = f.id WHERE c.reference = ? AND c.utilisateur_id = ?");
    $stmt->execute([$reference, $user_id]);
    $commande = $stmt->fetch();
    
    if ($commande && $commande['status'] === 'payee') {
        $success = true;
        $message = 'Votre paiement a été confirmé avec succès ! Vous êtes maintenant inscrit à la formation.';
    } else {
        $message = 'Paiement non confirmé. Veuillez contacter le support.';
    }
} else {
    $message = 'Aucune information de paiement trouvée.';
}

// Rediriger avec le message si pas de transaction_id
if (!$transaction_id && !$reference && !$status) {
    header('Location: /index.php?page=formations');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Femi Fairy Finger</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --cream: #faf7f4;
            --blush: #f0e6df;
            --rose: #c9877a;
            --rose-dark: #a8655a;
            --charcoal: #2c2420;
            --muted: #9a8a85;
            --white: #ffffff;
            --border: #e8ddd8;
            --gold: #c9a96e;
            --gold-light: #e8d5a3;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .container {
            max-width: 560px;
            width: 100%;
            margin: 0 auto;
        }
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .card.success { border-color: #22c55e; border-width: 2px; }
        .card.error { border-color: #ef4444; border-width: 2px; }
        .icon { font-size: 64px; margin-bottom: 16px; display: block; }
        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .success h1 { color: #22c55e; }
        .error h1 { color: #ef4444; }
        .message {
            color: var(--muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .details {
            background: var(--cream);
            padding: 16px;
            border-radius: 12px;
            text-align: left;
            margin-bottom: 24px;
        }
        .details p {
            padding: 4px 0;
            font-size: 0.9rem;
        }
        .details strong {
            color: var(--charcoal);
        }
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .btn-primary {
            background: var(--rose);
            color: white;
        }
        .btn-primary:hover {
            background: var(--rose-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(201, 135, 122, 0.35);
        }
        .btn-success {
            background: #22c55e;
            color: white;
        }
        .btn-success:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: var(--cream);
            color: var(--charcoal);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background: var(--border);
        }
        @media (max-width: 480px) {
            .card { padding: 24px 16px; }
            .btn { width: 100%; text-align: center; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
        <div class="card success">
            <span class="icon">🎉</span>
            <h1>Paiement réussi !</h1>
            <p class="message"><?= htmlspecialchars($message) ?></p>
            
            <?php if ($commande): ?>
            <div class="details">
                <p><strong>📖 Formation :</strong> <?= htmlspecialchars($commande['formation_titre'] ?? '') ?></p>
                <p><strong>💰 Montant :</strong> <?= number_format($commande['montant'] ?? 0, 0, ',', ' ') ?> F</p>
                <p><strong>📅 Date :</strong> <?= date('d/m/Y H:i', strtotime($commande['date_creation'] ?? 'now')) ?></p>
                <p><strong>🔑 Référence :</strong> <?= htmlspecialchars($commande['reference'] ?? '') ?></p>
            </div>
            <?php endif; ?>
            
            <div class="btn-group">
                <a href="/index.php?page=mesformations" class="btn btn-success">📚 Mes formations</a>
                <a href="/index.php?page=dashboard" class="btn btn-primary">🏠 Dashboard</a>
            </div>
        </div>
        
        <?php else: ?>
        <div class="card error">
            <span class="icon">😕</span>
            <h1>Paiement non confirmé</h1>
            <p class="message"><?= htmlspecialchars($message) ?></p>
            
            <div class="btn-group">
                <a href="/index.php?page=formations" class="btn btn-primary">🔄 Réessayer</a>
                <a href="/index.php?page=dashboard" class="btn btn-secondary">🏠 Accueil</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>