<?php
// pages/admin/includes/functions.php
// Fonctions spécifiques à l'administration

require_once __DIR__ . '/../../../config/database.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté et est admin
 */
function isAdmin() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'admin';
}

/**
 * Redirige vers la page de connexion si non admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ?page=login');
        exit;
    }
}

/**
 * Nettoie une chaîne pour éviter XSS
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Formatte un prix
 */
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' FCFA';
}

/**
 * Récupère les statistiques du tableau de bord
 */
function getDashboardStats($pdo) {
    $stats = [];

    // Total utilisateurs (hors admin)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateurs WHERE role != 'admin'");
    $stats['utilisateurs'] = $stmt->fetch()['total'] ?? 0;

    // Total formations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM formations");
    $stats['formations'] = $stmt->fetch()['total'] ?? 0;

    // Total commandes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes");
    $stats['commandes'] = $stmt->fetch()['total'] ?? 0;

    // Chiffre d'affaires total
    $stmt = $pdo->query("SELECT SUM(montant) as total FROM commandes WHERE status = 'payee'");
    $stats['ca_total'] = $stmt->fetch()['total'] ?? 0;

    // Commandes en attente
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes WHERE status = 'en_attente'");
    $stats['commandes_attente'] = $stmt->fetch()['total'] ?? 0;

    // Inscriptions actives
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM commandes WHERE status = 'payee'");
    $stats['inscriptions_actives'] = $stmt->fetch()['total'] ?? 0;

    // Commandes récentes (5 dernières)
    $stmt = $pdo->query("
        SELECT c.*, u.prenom, u.nom, f.titre as formation_titre 
        FROM commandes c
        JOIN utilisateurs u ON c.utilisateur_id = u.id
        JOIN formations f ON c.formation_id = f.id
        ORDER BY c.date_creation DESC
        LIMIT 5
    ");
    $stats['commandes_recentes'] = $stmt->fetchAll();

    // Inscriptions récentes
    $stmt = $pdo->query("
        SELECT i.*, u.prenom, u.nom, f.titre as formation_titre 
        FROM commandes i
        JOIN utilisateurs u ON i.utilisateur_id = u.id
        JOIN formations f ON i.formation_id = f.id
        ORDER BY i.date_creation DESC
        LIMIT 5
    ");
    $stats['inscriptions_recentes'] = $stmt->fetchAll();

    // Répartition des formations par catégorie
    $stmt = $pdo->query("
        SELECT categorie, COUNT(*) as total 
        FROM formations 
        GROUP BY categorie
    ");
    $stats['formations_categories'] = $stmt->fetchAll();

    return $stats;
}

/**
 * Génère un message flash
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Génère un slug à partir d'une chaîne
 */
function createSlug($string) {
    $string = preg_replace('/[^a-zA-Z0-9\s-]/', '', $string);
    $string = strtolower(trim($string));
    $string = preg_replace('/[\s-]+/', '-', $string);
    return $string;
}

// ===== FONCTIONS POUR LE PAIEMENT =====

// Générer une référence unique
function generateReference($prefix = 'CMD') {
    return $prefix . '-' . date('Ymd') . '-' . strtoupper(uniqid());
}

// Vérifier si l'utilisateur est déjà inscrit à une formation
function isUserEnrolled($user_id, $formation_id) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE utilisateur_id = ? AND formation_id = ? AND status != 'annulee'");
        $stmt->execute([$user_id, $formation_id]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Enregistrer une inscription
function enrollUser($user_id, $formation_id, $montant, $reference) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, formation_id, montant, status, reference, date_creation) VALUES (?, ?, ?, 'en_attente', ?, NOW())");
        $stmt->execute([$user_id, $formation_id, $montant, $reference]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        throw new Exception('Erreur lors de l\'inscription : ' . $e->getMessage());
    }
}

// Enregistrer une transaction FedaPay
function saveFedaPayTransaction($commande_id, $transaction_id, $reference, $amount, $customer_name, $customer_email, $customer_phone) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO transactions_fedapay (commande_id, transaction_id, reference, amount, customer_name, customer_email, customer_phone, status, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$commande_id, $transaction_id, $reference, $amount, $customer_name, $customer_email, $customer_phone]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        throw new Exception('Erreur lors de l\'enregistrement de la transaction : ' . $e->getMessage());
    }
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}