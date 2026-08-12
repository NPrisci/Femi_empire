<?php
// pages/admin/includes/header.php
require_once __DIR__ . '/functions.php';
requireAdmin();

$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
function uploadFile($file, $uploadDir, $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    // Vérifier les erreurs
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erreur lors de l\'upload du fichier.'];
    }
    
    // Vérifier la taille (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Le fichier est trop volumineux (max 5MB).'];
    }
    
    // Vérifier l'extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Extension non autorisée. Types acceptés : ' . implode(', ', $allowedExtensions)];
    }
    
    // Créer le dossier si nécessaire
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Générer un nom unique
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadDir . '/' . $filename;
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Erreur lors du déplacement du fichier.'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration FemiEmpire</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-icon">F</span>
                    <span class="logo-text">Femi<span>Empire</span></span>
                </div>
                <span class="logo-badge">Admin</span>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span>Tableau de bord</span>
                </a>
                <a href="formations.php" class="<?= $current_page == 'formations.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📚</span>
                    <span>Formations</span>
                </a>
                <a href="programmes.php" class="<?= $current_page == 'programmes.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📝</span>
                    <span>Programmes</span>
                </a>
                <a href="inscriptions.php" class="<?= $current_page == 'inscriptions.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📋</span>
                    <span>Inscriptions</span>
                </a>
                <a href="utilisateurs.php" class="<?= $current_page == 'utilisateurs.php' ? 'active' : '' ?>">
                    <span class="nav-icon">👤</span>
                    <span>Utilisateurs</span>
                </a>
                <a href="paiements.php" class="<?= $current_page == 'paiements.php' ? 'active' : '' ?>">
                    <span class="nav-icon">💰</span>
                    <span>Paiements</span>
                </a>
                <a href="realisations.php" class="<?= $current_page == 'realisations.php' ? 'active' : '' ?>">
                    <span class="nav-icon">🖼️</span>
                    <span>Réalisations</span>
                </a>
                <a href="supports.php" class="<?= $current_page == 'supports.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📎</span>
                    <span>Supports</span>
                </a>
                <a href="exercices.php" class="<?= $current_page == 'exercices.php' ? 'active' : '' ?>">
                    <span class="nav-icon">✏️</span>
                    <span>Exercices</span>
                </a>
                <a href="parametres.php" class="<?= $current_page == 'parametres.php' ? 'active' : '' ?>">
                    <span class="nav-icon">⚙️</span>
                    <span>Paramètres</span>
                </a>
                <a href="logout.php" class="logout-link">
                    <span class="nav-icon">🚪</span>
                    <span>Déconnexion</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">☰</button>
                    <h1 class="page-title">
                        <?php
                        $titles = [
                            'index.php' => 'Tableau de bord',
                            'formations.php' => 'Gestion des formations',
                            'programmes.php' => 'Gestion des programmes',
                            'inscriptions.php' => 'Gestion des inscriptions',
                            'utilisateurs.php' => 'Gestion des utilisateurs',
                            'paiements.php' => 'Gestion des paiements',
                            'realisations.php' => 'Gestion des réalisations',
                            'supports.php' => 'Gestion des supports',
                            'exercices.php' => 'Gestion des exercices',
                            'parametres.php' => 'Paramètres du site'
                        ];
                        echo $titles[$current_page] ?? 'Administration';
                        ?>
                    </h1>
                </div>
                <div class="topbar-right">
                    <span class="admin-user">
                        <span class="user-avatar"><?= strtoupper(substr($_SESSION['user_prenom'], 0, 1) . substr($_SESSION['user_nom'], 0, 1)) ?></span>
                        <span class="user-name"><?= htmlspecialchars($user_name) ?></span>
                    </span>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if ($flash = getFlash()): ?>
            <div class="flash-message flash-<?= $flash['type'] ?>">
                <?= htmlspecialchars($flash['message']) ?>
                <button class="flash-close">&times;</button>
            </div>
            <?php endif; ?>