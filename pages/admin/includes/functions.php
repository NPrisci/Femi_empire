<?php
// pages/admin/includes/functions.php
// Fonctions spécifiques à l'administration

// ===== FONCTIONS POUR LES FICHIERS =====

if (!function_exists('uploadFile')) {
    /**
     * Upload d'un fichier avec validation
     * 
     * @param array $file Le fichier $_FILES['nom']
     * @param string $upload_dir Le dossier de destination
     * @param array $allowed_extensions Les extensions autorisées
     * @param int $max_size Taille max en octets (défaut: 10Mo)
     * @return array ['success' => bool, 'filename' => string|null, 'message' => string]
     */
    function uploadFile($file, $upload_dir, $allowed_extensions = [], $max_size = 10485760) {
        // Vérifier s'il y a une erreur
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $messages = [
                UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse la taille maximale autorisée par le serveur.',
                UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée par le formulaire.',
                UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
                UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
                UPLOAD_ERR_NO_TMP_DIR => 'Le dossier temporaire est manquant.',
                UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le disque.',
                UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement.'
            ];
            return [
                'success' => false,
                'filename' => null,
                'message' => $messages[$file['error']] ?? 'Erreur inconnue lors du téléchargement.'
            ];
        }
        
        // Vérifier la taille
        if ($file['size'] > $max_size) {
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Le fichier est trop volumineux. Taille maximale : ' . ($max_size / 1024 / 1024) . ' Mo.'
            ];
        }
        
        // Récupérer l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Vérifier l'extension
        if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Extension non autorisée. Extensions autorisées : ' . implode(', ', $allowed_extensions) . '.'
            ];
        }
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                return [
                    'success' => false,
                    'filename' => null,
                    'message' => 'Impossible de créer le dossier de téléchargement.'
                ];
            }
        }
        
        // Vérifier que le dossier est accessible en écriture
        if (!is_writable($upload_dir)) {
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Le dossier de téléchargement n\'est pas accessible en écriture.'
            ];
        }
        
        // Générer un nom de fichier unique
        $basename = pathinfo($file['name'], PATHINFO_FILENAME);
        $basename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $basename);
        $basename = substr($basename, 0, 50);
        $filename = $basename . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
        
        // Chemin complet
        $filepath = $upload_dir . '/' . $filename;
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => false,
                'filename' => null,
                'message' => 'Erreur lors du déplacement du fichier.'
            ];
        }
        
        return [
            'success' => true,
            'filename' => $filename,
            'message' => 'Fichier téléchargé avec succès.'
        ];
    }
}

// Les autres fonctions...
if (!function_exists('deleteFile')) {
    function deleteFile($filepath) {
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}

if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('getMimeType')) {
    function getMimeType($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime_types = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
        ];
        return $mime_types[$extension] ?? 'application/octet-stream';
    }
}

if (!function_exists('sanitizeFilename')) {
    function sanitizeFilename($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        return substr($filename, 0, 255);
    }
}

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