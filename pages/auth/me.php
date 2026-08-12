<?php
// auth/me.php
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$user = getCurrentUser();

if ($user) {
    $prenom = $user['prenom'] ?? explode(' ', $user['name'] ?? 'Utilisateur')[0];
    $nom    = $user['nom']    ?? (explode(' ', $user['name'] ?? '')[1] ?? '');
    $email  = $user['email']  ?? '';

    echo json_encode([
        'loggedIn' => true,
        'prenom'   => $prenom,
        'nom'      => $nom,
        'name'     => trim($prenom . ' ' . $nom),
        'email'    => $email,
        'initials' => strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1)),
        'role'     => $user['role'] ?? 'client',
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
