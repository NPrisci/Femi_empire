<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Get POST data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// TODO: Validate credentials against your database
// This is just an example - implement your actual authentication logic
if ($email === 'test@example.com' && $password === 'password') {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_nom'] = 'Doe';
    $_SESSION['user_prenom'] = 'John';
    $_SESSION['user_role'] = 'user';
    
    echo json_encode(['success' => true, 'message' => 'Connexion réussie']);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
}
?>