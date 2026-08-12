<?php
// /pages/paiement_fedapay.php - Traitement du paiement FedaPay

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fedapay.php';
require_once __DIR__ . 'admin/functions.php';

// Démarrer la session si elle n'est pas active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour vous inscrire.'
    ]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$pdo = getDB();

// Récupérer les données POST
$input = json_decode(file_get_contents('php://input'), true);
$formation_id = isset($input['formation_id']) ? (int)$input['formation_id'] : 0;
$formation_titre = isset($input['titre']) ? $input['titre'] : '';
$formation_prix = isset($input['prix']) ? (float)$input['prix'] : 0;

// Vérifier les données
if (!$formation_id || !$formation_titre || $formation_prix <= 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Données de formation invalides.'
    ]);
    exit;
}

try {
    // Vérifier si la formation existe et est active
    $stmt = $pdo->prepare("SELECT * FROM formations WHERE id = ? AND statut = 'active'");
    $stmt->execute([$formation_id]);
    $formation = $stmt->fetch();

    if (!$formation) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Formation non trouvée ou indisponible.'
        ]);
        exit;
    }

    // Vérifier si déjà inscrit
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE utilisateur_id = ? AND formation_id = ? AND status IN ('payee', 'en_attente')");
    $stmt->execute([$user_id, $formation_id]);
    if ($stmt->fetchColumn() > 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Vous êtes déjà inscrit à cette formation.'
        ]);
        exit;
    }

    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Utilisateur non trouvé.'
        ]);
        exit;
    }

    // Générer une référence unique
    $reference = generateReference('CMD');
    
    // Créer la commande en attente
    $commande_id = enrollUser($user_id, $formation_id, $formation_prix, $reference);
    
    // Préparer les données du client pour FedaPay
    $customer = [
        'first_name' => $user['prenom'],
        'last_name' => $user['nom'],
        'email' => $user['email'],
        'phone_number' => $user['telephone'] ?? ''
    ];
    
    $description = 'Inscription à : ' . $formation_titre;
    $amount = (int)($formation_prix * 100); // FedaPay utilise les centimes
    $callback_url = getenv('SITE_URL') . '/pages/client/confirmation.php';
    
    // Créer la transaction FedaPay
    $result = createFedaPayTransaction($amount, $customer, $description, $reference, $callback_url);
    
    if ($result['success']) {
        // Enregistrer la transaction
        saveFedaPayTransaction(
            $commande_id,
            $result['transaction']->id,
            $reference,
            $formation_prix,
            $user['prenom'] . ' ' . $user['nom'],
            $user['email'],
            $user['telephone'] ?? ''
        );
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'url' => $result['payment_url'],
            'message' => 'Redirection vers FedaPay...'
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la création du paiement : ' . $result['error']
        ]);
    }

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur : ' . $e->getMessage()
    ]);
}