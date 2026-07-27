<?php
// ================================================
//  config/session.php
//  Gestion des sessions utilisateur
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,       // 24h
        'path'     => '/',
        'secure'   => false,       // ← mettre true en HTTPS (production)
        'httponly' => true,        // protection XSS
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ── Vérifier si l'utilisateur est connecté ──
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// ── Récupérer l'utilisateur connecté ──
function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'nom'     => $_SESSION['user_nom'],
        'prenom'  => $_SESSION['user_prenom'],
        'email'    => $_SESSION['user_email'],
        'formation'    => $_SESSION['user_formation'],
        'initials' => $_SESSION['user_initials'],
        'role'     => $_SESSION['user_role'] ?? 'client',
    ];
}

// ── Créer la session après connexion ──
function createSession(array $user): void {
    session_regenerate_id(true); // sécurité anti-fixation
    $_SESSION['user_id']        = $user['id'];
    $_SESSION['user_nom']       = $user['nom']       ?? '';
    $_SESSION['user_prenom']    = $user['prenom']    ?? '';
    $_SESSION['user_email']     = $user['email']     ?? '';
    $_SESSION['user_formation'] = $user['formation'] ?? '';
    $_SESSION['user_initials']  = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
    $_SESSION['user_role']      = $user['role']      ?? 'client';
}

// ── Détruire la session (logout) ──
function destroySession(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

// ── Rediriger si non connecté ──
function requireLogin(string $redirect = '?page=login'): void {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

// ── Rediriger si déjà connecté ──
function redirectIfLoggedIn(string $redirect = '?page=dashboard'): void {
    if (isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}
