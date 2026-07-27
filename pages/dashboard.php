<?php
// dashboard.php - Tableau de bord

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

try {
    $pdo = getDB();
    
    // Récupérer les infos de l'utilisateur
    $stmt = $pdo->prepare('
        SELECT id, prenom, nom, email, telephone, role, created_at
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

    // Récupérer les formations auxquelles l'utilisateur est inscrit
    $stmt = $pdo->prepare('
        SELECT f.*, c.date_creation, c.progression, c.modules_done, c.status as commande_status
        FROM commandes c
        INNER JOIN formations f ON f.id = c.formation_id
        WHERE c.utilisateur_id = ? AND c.status = "payee"
        ORDER BY c.date_creation DESC
    ');
    $stmt->execute([$userId]);
    $mesFormations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les formations recommandées (non inscrites)
    $idsInscrits = array_column($mesFormations, 'id');
    if (empty($idsInscrits)) {
        $stmt = $pdo->query('SELECT * FROM formations WHERE statut = "active" ORDER BY RAND() LIMIT 4');
        $formationsRecommandees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $placeholders = str_repeat('?,', count($idsInscrits) - 1) . '?';
        $stmt = $pdo->prepare("
            SELECT * FROM formations 
            WHERE statut = 'active' AND id NOT IN ($placeholders)
            ORDER BY RAND() LIMIT 4
        ");
        $stmt->execute($idsInscrits);
        $formationsRecommandees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Statistiques
    $totalFormations = count($mesFormations);
    
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(montant), 0) FROM commandes WHERE utilisateur_id = ? AND status = 'payee'");
    $stmt->execute([$userId]);
    $totalDepenses = $stmt->fetchColumn();
    
    $nbTerminees = 0;
    foreach ($mesFormations as $f) {
        if ((int)($f['progression'] ?? 0) >= 100) {
            $nbTerminees++;
        }
    }

} catch (PDOException $e) {
    error_log("ERREUR CONNEXION: " . $e->getMessage());
    die('Erreur connexion : ' . $e->getMessage());
}

function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function initiales(string $prenom, string $nom): string {
    return strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEMI Fairy Finger — Tableau de bord</title>
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
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .dashboard-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 600;
        }

        .dashboard-header h1 em {
            color: var(--rose);
            font-style: italic;
        }

        .dashboard-header p {
            color: var(--muted);
            font-size: .95rem;
        }

        /* ===== WELCOME BANNER ===== */
        .welcome-banner {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--rose), var(--rose-dark));
            border-radius: 20px;
            padding: 32px;
            color: white;
            margin-bottom: 8px;
        }

        .welcome-banner::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -70px;
            top: -80px;
            border-radius: 50%;
            border: 35px solid rgba(255, 255, 255, 0.10);
        }

        .welcome-banner::before {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            right: 120px;
            bottom: -100px;
            border-radius: 50%;
            border: 25px solid rgba(255, 255, 255, 0.08);
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-banner h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .welcome-banner p {
            margin: 0;
            opacity: 0.9;
            font-size: 1rem;
        }

        .welcome-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 10px 24px;
            background: white;
            color: var(--rose-dark);
            border-radius: 10px;
            text-decoration: none;
            font-size: .9rem;
            font-weight: 600;
            transition: .25s ease;
        }

        .welcome-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 8px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border-radius: 12px;
            background: var(--blush);
            color: var(--rose);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stat-info small {
            display: block;
            color: var(--muted);
            font-size: .75rem;
            margin-bottom: 2px;
        }

        .stat-value {
            color: var(--charcoal);
            font-size: 1.4rem;
            font-weight: 700;
        }

        .stat-value small {
            font-size: .7rem;
            font-weight: 400;
            color: var(--muted);
        }

        /* ===== SECTION ===== */
        .section-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .section-title em {
            color: var(--rose);
            font-style: italic;
        }

        .section-link {
            color: var(--rose);
            text-decoration: none;
            font-size: .85rem;
            font-weight: 500;
            transition: color .2s;
        }

        .section-link:hover {
            color: var(--rose-dark);
        }

        .section-body {
            padding: 20px 24px;
        }

        /* ===== COURSE CARDS ===== */
        .course-card {
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            transition: all .2s;
        }

        .course-card:hover {
            border-color: var(--rose);
        }

        .course-card:last-child {
            margin-bottom: 0;
        }

        .course-main {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .course-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--blush);
            color: var(--rose);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .course-info {
            flex: 1;
        }

        .course-title {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .course-date {
            font-size: .8rem;
            color: var(--muted);
        }

        .course-badge {
            font-size: .7rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            background: var(--green-light);
            color: var(--green);
        }

        .course-progress {
            margin-top: 8px;
        }

        .course-progress .progress-bar {
            width: 100%;
            height: 4px;
            background: var(--border);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 4px;
        }

        .course-progress .progress-fill {
            height: 100%;
            border-radius: 10px;
            background: var(--rose);
            transition: width .6s ease;
        }

        .course-progress .progress-fill.completed {
            background: var(--green);
        }

        .course-progress .progress-text {
            font-size: .75rem;
            color: var(--muted);
        }

        /* ===== RECOMMENDATIONS ===== */
        .recommendations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }

        .recommendation-card {
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            transition: all .2s;
        }

        .recommendation-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
            border-color: var(--rose);
        }

        .recommendation-card .rec-icon {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .recommendation-card h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .recommendation-card .rec-category {
            font-size: .7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .recommendation-card .rec-price {
            font-weight: 700;
            color: var(--rose-dark);
            margin-top: 8px;
        }

        .btn-inscrire {
            display: inline-block;
            background: var(--rose);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: .8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            margin-top: 8px;
            width: 100%;
            text-align: center;
        }

        .btn-inscrire:hover {
            background: var(--rose-dark);
            transform: translateY(-1px);
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 12px;
            display: block;
        }

        .btn-primary {
            display: inline-block;
            background: var(--rose);
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all .2s;
            margin-top: 12px;
        }

        .btn-primary:hover {
            background: var(--rose-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(201, 135, 122, .35);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

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

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .welcome-banner {
                padding: 24px;
            }

            .welcome-banner h2 {
                font-size: 1.4rem;
            }

            .recommendations-grid {
                grid-template-columns: 1fr;
            }

            .course-main {
                flex-wrap: wrap;
            }
        }
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
                        <span class="stat-val"><?= $totalFormations ?></span>
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
                <a href="?page=dashboard" class="side-nav-item active">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-20v8h8V1h-8z" />
                    </svg>
                    Tableau de bord
                </a>
                <a href="?page=mesformations" class="side-nav-item">
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

            <!-- HEADER -->
            <div class="dashboard-header">
                <div>
                    <h1>Bonjour <em><?= h($user['prenom']) ?></em> 👋</h1>
                    <p>Continuez votre apprentissage et développez vos compétences.</p>
                </div>
            </div>

            <!-- WELCOME BANNER -->
            <div class="welcome-banner">
                <div class="welcome-content">
                    <h2>Prêt à continuer votre parcours ?</h2>
                    <p class="text-white">Découvrez vos formations, suivez votre progression et développez de nouvelles compétences.</p>
                    <a href="?page=mesformations" class="welcome-btn">
                        📚 Voir mes formations
                    </a>
                </div>
            </div>

            <!-- STATISTIQUES -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-info">
                        <small>Formations suivies</small>
                        <div class="stat-value"><?= $totalFormations ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <small>Formations terminées</small>
                        <div class="stat-value"><?= $nbTerminees ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-info">
                        <small>Progression moyenne</small>
                        <div class="stat-value">
                            <?php
                            $avgProgress = $totalFormations > 0 
                                ? round(array_sum(array_column($mesFormations, 'progression')) / $totalFormations) 
                                : 0;
                            echo $avgProgress . '%';
                            ?>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <small>Total investi</small>
                        <div class="stat-value"><?= number_format($totalDepenses, 0, ',', ' ') ?> <small>FCFA</small></div>
                    </div>
                </div>
            </div>

            <!-- MES FORMATIONS -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">Mes <em>formations</em></div>
                    <a href="?page=mesformations" class="section-link">Voir toutes →</a>
                </div>
                <div class="section-body">
                    <?php if (empty($mesFormations)): ?>
                        <div class="empty-state">
                            <span class="empty-icon">📚</span>
                            <p>Aucune formation pour le moment.</p>
                            <a href="?page=formations" class="btn-primary">Découvrir les formations</a>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($mesFormations, 0, 3) as $f): 
                            $progression = (int)($f['progression'] ?? 0);
                            $estTerminee = $progression >= 100;
                        ?>
                            <div class="course-card">
                                <div class="course-main">
                                    <div class="course-icon">💅</div>
                                    <div class="course-info">
                                        <div class="course-title"><?= h($f['titre']) ?></div>
                                        <div class="course-date">📅 Inscrit le <?= date('d/m/Y', strtotime($f['date_creation'])) ?></div>
                                    </div>
                                    <span class="course-badge"><?= $estTerminee ? '✅ Terminée' : 'En cours' ?></span>
                                </div>
                                <div class="course-progress">
                                    <div class="progress-text">Progression : <?= $progression ?>%</div>
                                    <div class="progress-bar">
                                        <div class="progress-fill <?= $estTerminee ? 'completed' : '' ?>" style="width: <?= $progression ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($mesFormations) > 3): ?>
                            <div style="text-align:center; margin-top:12px;">
                                <a href="?page=mesformations" class="section-link">Voir les <?= count($mesFormations) - 3 ?> autres formations →</a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FORMATIONS RECOMMANDÉES -->
            <?php if (!empty($formationsRecommandees)): ?>
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-title">Recommandé pour <em>vous</em></div>
                        <a href="?page=formation" class="section-link">Voir le catalogue →</a>
                    </div>
                    <div class="section-body">
                        <div class="recommendations-grid">
                            <?php foreach ($formationsRecommandees as $f): ?>
                                <div class="recommendation-card">
                                    <div class="rec-icon">🎯</div>
                                    <h4><?= h($f['titre']) ?></h4>
                                    <div class="rec-category"><?= h(ucfirst($f['categorie'] ?? 'Général')) ?></div>
                                    <div class="rec-price"><?= number_format($f['prix'] ?? 0, 0, ',', ' ') ?> FCFA</div>
                                    <a href="?page=formation&id=<?= $f['id'] ?>" class="btn-inscrire">S'inscrire</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <script>
        console.log('=== 🐛 DASHBOARD ===');
        console.log('👤 Utilisateur:', <?= json_encode($user) ?>);
        console.log('📚 Mes formations:', <?= json_encode($mesFormations) ?>);
        console.log('🎯 Recommandations:', <?= json_encode($formationsRecommandees) ?>);
        console.log('=== FIN DEBUG ===');
    </script>

</body>
</html>