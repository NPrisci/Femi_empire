<?php

// mesformations.php - Page des formations de l'utilisateur

require_once __DIR__ . '/../config/database.php';

// Démarrer la session si elle n'est pas active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$success = '';
$error = '';

try {
    $pdo = getDB();
    
    // Récupérer les infos de l'utilisateur
    $stmt = $pdo->prepare('
        SELECT id, prenom, nom, email, role, created_at
        FROM utilisateurs
        WHERE id = ?
    ');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: ?page=login');
        exit;
    }

    // ================================================
    // RÉCUPÉRER TOUTES LES FORMATIONS DE L'UTILISATEUR
    // ================================================
    
    // 1. Formations payées (avec progression)
    $stmt = $pdo->prepare('
        SELECT 
            f.id,
            f.titre,
            f.description,
            f.categorie,
            f.prix,
            f.duree,
            f.statut,
            c.id as commande_id,
            c.montant,
            c.status as commande_status,
            c.progression,
            c.modules_done,
            c.reference as commande_reference,
            c.date_creation as date_commande,
            c.date_obtention,
            "payee" as type_acces
        FROM commandes c
        INNER JOIN formations f ON f.id = c.formation_id
        WHERE c.utilisateur_id = ? 
        AND c.status = "payee"
        ORDER BY c.date_creation DESC
    ');
    $stmt->execute([$userId]);
    $formationsPayees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Formations en attente de paiement
    $stmt = $pdo->prepare('
        SELECT 
            f.id,
            f.titre,
            f.description,
            f.categorie,
            f.prix,
            f.duree,
            f.statut,
            c.id as commande_id,
            c.montant,
            c.status as commande_status,
            c.progression,
            c.modules_done,
            c.reference as commande_reference,
            c.date_creation as date_commande,
            c.date_obtention,
            "en_attente" as type_acces
        FROM commandes c
        INNER JOIN formations f ON f.id = c.formation_id
        WHERE c.utilisateur_id = ? 
        AND c.status = "en_attente"
        ORDER BY c.date_creation DESC
    ');
    $stmt->execute([$userId]);
    $formationsEnAttente = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Formations annulées
    $stmt = $pdo->prepare('
        SELECT 
            f.id,
            f.titre,
            f.description,
            f.categorie,
            f.prix,
            f.duree,
            f.statut,
            c.id as commande_id,
            c.montant,
            c.status as commande_status,
            c.progression,
            c.modules_done,
            c.reference as commande_reference,
            c.date_creation as date_commande,
            c.date_obtention,
            "annulee" as type_acces
        FROM commandes c
        INNER JOIN formations f ON f.id = c.formation_id
        WHERE c.utilisateur_id = ? 
        AND c.status = "annulee"
        ORDER BY c.date_creation DESC
    ');
    $stmt->execute([$userId]);
    $formationsAnnulees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fusionner toutes les formations
    $formations = array_merge($formationsPayees, $formationsEnAttente, $formationsAnnulees);
    
    // Statistiques
    $nbFormations = count($formationsPayees);
    $nbEnAttente = count($formationsEnAttente);
    $nbAnnulees = count($formationsAnnulees);
    $progressTotal = $nbFormations > 0
        ? (int) round(array_sum(array_column($formationsPayees, 'progression')) / $nbFormations)
        : 0;
    
    // Nombre de formations terminées
    $nbTerminees = 0;
    foreach ($formationsPayees as $f) {
        if ((int)$f['progression'] >= 100) {
            $nbTerminees++;
        }
    }

} catch (PDOException $e) {
    error_log("ERREUR CONNEXION: " . $e->getMessage());
    die('Erreur connexion : ' . $e->getMessage());
}

// Fonctions utilitaires
function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function initiales(string $prenom, string $nom): string {
    return strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}

function formatDateFr(string $date): string {
    if (empty($date)) return '—';
    $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
             'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $ts = strtotime($date);
    return (int)date('j', $ts) . ' ' . $mois[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

function getCategoryIcon(string $categorie): string {
    $icons = [
        'onglerie' => '💅',
        'business' => '💼',
        'design' => '🎨',
        'marketing' => '📈',
        'beaute' => '✨',
        'bien-etre' => '🧘',
        'default' => '📚'
    ];
    return $icons[strtolower($categorie)] ?? $icons['default'];
}

function getCategoryColor(string $categorie): string {
    $colors = [
        'onglerie' => '#fde8e5',
        'business' => '#e8d5f5',
        'design' => '#d5e8f5',
        'marketing' => '#fef0dc',
        'beaute' => '#fce8f0',
        'bien-etre' => '#d5f5e8',
        'default' => '#f0eded'
    ];
    return $colors[strtolower($categorie)] ?? $colors['default'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEMI Fairy Finger — Mes Formations</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* ===== STYLES GLOBAUX ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
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
            --shadow: 0 8px 32px rgba(0,0,0,0.12);
            --green: #1a7a45;
            --green-light: #d4f5e4;
            --orange: #a05c10;
            --orange-light: #fef0dc;
            --red: #a8200d;
            --red-light: #fde8e5;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(250, 247, 244, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 48px;
            height: 64px;
        }

        .navbar .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: .04em;
        }

        .navbar .logo span {
            color: var(--rose);
            font-style: italic;
            font-weight: 300;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .navbar-link {
            font-size: .82rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            letter-spacing: .06em;
            text-transform: uppercase;
            transition: color .2s;
        }

        .navbar-link:hover {
            color: var(--rose);
        }

        .navbar-link.active {
            color: var(--rose);
            font-weight: 600;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rose), var(--gold));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            color: white;
            font-weight: 600;
            border: 2px solid var(--border);
            cursor: pointer;
        }

        /* ===== LAYOUT ===== */
        .page-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 32px 80px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 32px;
            align-items: start;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: sticky;
            top: 80px;
        }

        .profile-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: linear-gradient(135deg, var(--blush), #f5e8e0);
        }

        .avatar-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 16px;
            margin-top: 20px;
            z-index: 1;
        }

        .avatar-main {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rose), var(--gold));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            color: white;
            font-weight: 600;
            border: 4px solid var(--white);
            box-shadow: 0 4px 20px rgba(201, 135, 122, .3);
        }

        .profile-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .profile-role {
            font-size: .78rem;
            color: var(--rose);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 16px;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 24px;
            padding: 16px 0;
            border-top: 1px solid var(--border);
        }

        .stat {
            text-align: center;
        }

        .stat-val {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 600;
            display: block;
        }

        .stat-lbl {
            font-size: .72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .side-nav {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .side-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            font-size: .85rem;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            border-left: 3px solid transparent;
        }

        .side-nav-item:not(:last-child) {
            border-bottom: 1px solid var(--border);
        }

        .side-nav-item:hover {
            background: var(--cream);
            color: var(--charcoal);
        }

        .side-nav-item.active {
            background: #fef4f2;
            color: var(--rose);
            border-left-color: var(--rose);
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            opacity: .6;
            flex-shrink: 0;
        }

        .side-nav-item.active .nav-icon {
            opacity: 1;
        }

        .logout-btn {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 13px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .83rem;
            font-weight: 500;
            color: #c0392b;
            cursor: pointer;
            transition: all .2s;
            width: 100%;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #fff5f5;
            border-color: #f5c6c0;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* ===== HEADER ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            font-weight: 600;
        }

        .page-title em {
            color: var(--rose);
            font-style: italic;
        }

        .header-stats {
            display: flex;
            gap: 24px;
            background: var(--white);
            padding: 12px 24px;
            border-radius: 16px;
            border: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;
            display: block;
            color: var(--rose);
        }

        .stat-label {
            font-size: .72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .stat-divider {
            width: 1px;
            background: var(--border);
        }

        /* ===== FILTRES ===== */
        .filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 20px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: .78rem;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
        }

        .filter-btn:hover {
            border-color: var(--rose);
            color: var(--rose);
        }

        .filter-btn.active {
            background: var(--rose);
            color: white;
            border-color: var(--rose);
        }

        /* ===== GRILLE DES FORMATIONS ===== */
        .formations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .formation-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            transition: all .3s ease;
            position: relative;
        }

        .formation-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .formation-card .card-header {
            padding: 20px 24px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }

        .formation-card .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .formation-card .card-info {
            flex: 1;
            margin-left: 14px;
        }

        .formation-card .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .formation-card .card-category {
            font-size: .72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .formation-card .card-body {
            padding: 16px 24px 20px;
        }

        .formation-card .card-description {
            font-size: .85rem;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .formation-card .card-meta {
            display: flex;
            gap: 16px;
            font-size: .78rem;
            color: var(--muted);
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .formation-card .card-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .formation-card .progress-section {
            margin-top: 12px;
        }

        .formation-card .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: .72rem;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .formation-card .progress-bar {
            width: 100%;
            height: 6px;
            background: var(--cream);
            border-radius: 10px;
            overflow: hidden;
        }

        .formation-card .progress-bar .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width .6s ease;
        }

        .formation-card .card-footer {
            padding: 12px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--cream);
        }

        .formation-card .card-footer .status-badge {
            font-size: .68rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .status-badge.payee {
            background: var(--green-light);
            color: var(--green);
        }

        .status-badge.en_attente {
            background: var(--orange-light);
            color: var(--orange);
        }

        .status-badge.annulee {
            background: var(--red-light);
            color: var(--red);
        }

        .status-badge.termine {
            background: var(--green-light);
            color: var(--green);
        }

        .btn-continuer {
            background: var(--rose);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: .78rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-continuer:hover {
            background: var(--rose-dark);
            transform: translateY(-1px);
        }

        .btn-continuer:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-continuer.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--muted);
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 16px;
            display: block;
        }

        .btn-primary {
            display: inline-block;
            background: var(--rose);
            color: white;
            padding: 12px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all .2s;
            margin-top: 16px;
        }

        .btn-primary:hover {
            background: var(--rose-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(201, 135, 122, .35);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .page-wrap {
                grid-template-columns: 1fr;
                padding: 24px 16px 60px;
            }

            .sidebar {
                position: static;
            }

            .navbar {
                padding: 0 20px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-stats {
                width: 100%;
                justify-content: space-around;
            }

            .formations-grid {
                grid-template-columns: 1fr;
            }

            .formation-card .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .formation-card .card-info {
                margin-left: 0;
                margin-top: 12px;
            }

            .formation-card .card-footer {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .formation-card .card-footer .status-badge {
                text-align: center;
            }
        }

        /* ===== ANIMATION ===== */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .formation-card {
            animation: fadeUp .5s ease both;
        }

        .formation-card:nth-child(2) { animation-delay: .05s; }
        .formation-card:nth-child(3) { animation-delay: .1s; }
        .formation-card:nth-child(4) { animation-delay: .15s; }
        .formation-card:nth-child(5) { animation-delay: .2s; }
        .formation-card:nth-child(6) { animation-delay: .25s; }
    </style>
</head>
<body>
    <!-- ===== PAGE ===== -->
    <div class="page-wrap">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <div class="profile-card">
                <div class="avatar-wrap">
                    <div class="avatar-main"><?= h(initiales($user['prenom'], $user['nom'])) ?></div>
                </div>
                <div class="profile-name"><?= h($user['prenom'] . ' ' . $user['nom']) ?></div>
                <div class="profile-role">
                    <?= $user['role'] === 'admin' ? '⚙ Administrateur' : '✧ Membre' ?>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-val"><?= $nbFormations ?></span>
                        <span class="stat-lbl">Formations</span>
                    </div>
                    <div class="stat">
                        <span class="stat-val"><?= $nbTerminees ?></span>
                        <span class="stat-lbl">Terminées</span>
                    </div>
                    <div class="stat">
                        <span class="stat-val"><?= date('Y', strtotime($user['created_at'])) ?></span>
                        <span class="stat-lbl">Depuis</span>
                    </div>
                </div>
            </div>

            <nav class="side-nav">
                <a href="?page=dashboard" class="side-nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-20v8h8V1h-8z" />
                    </svg>
                    Tableau de bord
                </a>
                <a href="?page=mesformations" class="side-nav-item active">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                    </svg>
                    Mes formations
                </a>
                <a href="?page=certificats" class="side-nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z" />
                    </svg>
                    Certificats
                </a>
                <a href="?page=profile" class="side-nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                    </svg>
                    Mon profil
                </a>
            </nav>

            <a href="?action=logout" class="logout-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#c0392b">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                </svg>
                Se déconnecter
            </a>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="main-content">

        <?php if (isset($_GET['paiement'])): ?>

    <?php if ($_GET['paiement'] === 'success'): ?>

        <div class="alert alert-success">
            ✅ Paiement effectué avec succès.
            Votre formation est maintenant disponible.
        </div>

    <?php elseif ($_GET['paiement'] === 'cancelled'): ?>

        <div class="alert alert-warning">
            ⚠️ Le paiement a été annulé.
        </div>

    <?php elseif ($_GET['paiement'] === 'pending'): ?>

        <div class="alert alert-info">
            ⏳ Le paiement est encore en attente de confirmation.
        </div>

    <?php elseif ($_GET['paiement'] === 'error'): ?>

        <div class="alert alert-danger">
            ❌ Impossible de confirmer le paiement.
        </div>

    <?php endif; ?>

<?php endif; ?>
            <!-- HEADER -->
            <div class="page-header">
                <h1 class="page-title">Mes <em>formations</em></h1>
                <div class="header-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?= $nbFormations ?></span>
                        <span class="stat-label">En cours / Terminées</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $nbEnAttente ?></span>
                        <span class="stat-label">En attente</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $progressTotal ?>%</span>
                        <span class="stat-label">Progression moyenne</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $nbTerminees ?></span>
                        <span class="stat-label">✅ Terminées</span>
                    </div>
                </div>
            </div>

            <!-- FILTRES -->
            <div class="filters">
                <button class="filter-btn active" data-filter="all">Toutes</button>
                <button class="filter-btn" data-filter="payee">En cours</button>
                <button class="filter-btn" data-filter="termine">Terminées</button>
                <button class="filter-btn" data-filter="en_attente">En attente</button>
                <button class="filter-btn" data-filter="annulee">Annulées</button>
            </div>

            <!-- ===== GRILLE DES FORMATIONS ===== -->
            <?php if (empty($formations)): ?>
                <div class="empty-state">
                    <span class="empty-icon">📚</span>
                    <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; margin-bottom:8px;">Aucune formation</h2>
                    <p>Vous n'êtes inscrit à aucune formation pour le moment.</p>
                    <a href="?page=formations" class="btn-primary">Découvrir nos formations</a>
                </div>
            <?php else: ?>
                <div class="formations-grid">
                    <?php foreach ($formations as $f): 
                        $progression = (int)($f['progression'] ?? 0);
                        $estTerminee = $progression >= 100;
                        $statusClasse = $f['commande_status'] ?? 'payee';
                        $statusLabel = $statusClasse === 'payee' ? ($estTerminee ? '✅ Terminée' : 'En cours') : ucfirst(str_replace('_', ' ', $statusClasse));
                        $btnText = $estTerminee ? 'Voir le certificat' : 'Continuer';
                        $btnLink = $estTerminee ? '?page=certificats' : '?page=formation&id=' . $f['id'];
                        $btnDisabled = ($statusClasse !== 'payee' && !$estTerminee);
                    ?>
                        <div class="formation-card" data-status="<?= $statusClasse ?>">
                            <div class="card-header">
                                <div style="display:flex; align-items:center; flex:1;">
                                    <div class="card-icon" style="background: <?= getCategoryColor($f['categorie'] ?? 'default') ?>;">
                                        <?= getCategoryIcon($f['categorie'] ?? 'default') ?>
                                    </div>
                                    <div class="card-info">
                                        <h3 class="card-title"><?= h($f['titre']) ?></h3>
                                        <div class="card-category"><?= h(ucfirst($f['categorie'] ?? 'Général')) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-description"><?= h($f['description'] ?? 'Formation complète en onglerie professionnelle') ?></p>
                                <div class="card-meta">
                                    <span>⏱ <?= $f['duree'] ?? 0 ?> heures</span>
                                    <span>📅 <?= formatDateFr($f['date_obtention']) ?></span>
                                    <span>💰 <?= number_format($f['prix'] ?? 0, 2, ',', ' ') ?> FCFA</span>
                                </div>
                                <div class="progress-section">
                                    <div class="progress-header">
                                        <span>Progression</span>
                                        <span><?= $progression ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $progression ?>%; background: <?= $estTerminee ? 'var(--green)' : 'var(--rose)' ?>;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <span class="status-badge <?= $statusClasse ?> <?= $estTerminee ? 'termine' : '' ?>">
                                    <?= $statusLabel ?>
                                </span>
                                <a href="<?= $btnLink ?>" class="btn-continuer <?= $btnDisabled ? 'disabled' : '' ?>" <?= $btnDisabled ? 'disabled' : '' ?>>
                                    <?= $btnText ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- ===== SCRIPT ===== -->
    <script>
        // ===== FILTRES =====
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const cards = document.querySelectorAll('.formation-card');
                
                cards.forEach(card => {
                    const status = card.dataset.status;
                    if (filter === 'all') {
                        card.style.display = '';
                    } else if (filter === 'termine') {
                        const progressText = card.querySelector('.progress-header span:last-child');
                        const progress = progressText ? parseInt(progressText.textContent) : 0;
                        card.style.display = (status === 'payee' && progress >= 100) ? '' : 'none';
                    } else {
                        card.style.display = status === filter ? '' : 'none';
                    }
                });
            });
        });

        console.log('=== 🐛 MES FORMATIONS ===');
        console.log('👤 Utilisateur:', <?= json_encode($user) ?>);
        console.log('📚 Formations:', <?= json_encode($formations) ?>);
        console.log('📊 Statistiques:', {
            total: <?= json_encode($nbFormations) ?>,
            enAttente: <?= json_encode($nbEnAttente) ?>,
            annulees: <?= json_encode($nbAnnulees) ?>,
            terminees: <?= json_encode($nbTerminees) ?>,
            progressionMoyenne: <?= json_encode($progressTotal) ?>
        });
        console.log('=== FIN DEBUG ===');
    </script>

</body>
</html>