<?php
// formation.php - Page dynamique des formations (accessible sans connexion)

require_once __DIR__ . '/../config/database.php';

// Démarrer la session si elle n'est pas active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID utilisateur s'il est connecté
$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
$isLoggedIn = !empty($_SESSION['user_id']);

try {
    $pdo = getDB();
    
    // Récupérer les infos de l'utilisateur s'il est connecté
    $user = null;
    if ($isLoggedIn) {
        $stmt = $pdo->prepare('
            SELECT id, prenom, nom, email, role, created_at
            FROM utilisateurs
            WHERE id = ?
        ');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            session_destroy();
            $isLoggedIn = false;
            $userId = null;
        }
    }

    // Récupérer toutes les formations actives
    $stmt = $pdo->prepare('
        SELECT 
            id,
            titre,
            description,
            categorie,
            prix,
            duree,
            statut,
            created_at
        FROM formations 
        WHERE statut = "active"
        ORDER BY created_at DESC
    ');
    $stmt->execute();
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les formations auxquelles l'utilisateur est déjà inscrit (si connecté)
    $formationsInscrites = [];
    if ($isLoggedIn && $userId) {
        $stmt = $pdo->prepare('
            SELECT formation_id 
            FROM commandes 
            WHERE utilisateur_id = ? 
            AND status IN ("payee", "en_attente")
        ');
        $stmt->execute([$userId]);
        $inscriptions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $formationsInscrites = array_flip($inscriptions);
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

function getCategoryIcon(string $categorie): string {
    $icons = [
        'onglerie' => '💅',
        'onglerie-speciale' => '🌟',
        'pedicure-manucure' => '💆',
        'beaute' => '✨',
        'business' => '💼',
        'design' => '🎨',
        'marketing' => '📈',
        'default' => '📚'
    ];
    return $icons[strtolower($categorie)] ?? $icons['default'];
}

function getCategoryColor(string $categorie): string {
    $colors = [
        'onglerie' => '#fde8e5',
        'onglerie-speciale' => '#fce4ec',
        'pedicure-manucure' => '#e8f5e9',
        'beaute' => '#fce8f0',
        'business' => '#e8d5f5',
        'design' => '#d5e8f5',
        'marketing' => '#fef0dc',
        'default' => '#f0eded'
    ];
    return $colors[strtolower($categorie)] ?? $colors['default'];
}

function getCategoryBadge(string $categorie): string {
    $badges = [
        'onglerie' => 'Onglerie',
        'onglerie-speciale' => 'Spéciale',
        'pedicure-manucure' => 'Pédicure',
        'beaute' => 'Beauté',
        'business' => 'Business',
        'design' => 'Design',
        'marketing' => 'Marketing',
        'default' => 'Général'
    ];
    return $badges[strtolower($categorie)] ?? $badges['default'];
}

function formatPrice(float $price): string {
    return number_format($price, 0, ',', ' ') . ' F';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEMI Fairy Finger — Formations</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ===== RESET BOOTSTRAP CONFLICTS ===== */
        .navbar {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            background: rgba(250, 247, 244, 0.92) !important;
            backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid var(--border) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 48px !important;
            height: 64px !important;
            flex-wrap: nowrap !important;
        }

        .navbar .logo {
            font-family: 'Cormorant Garamond', serif !important;
            font-size: 1.4rem !important;
            font-weight: 600 !important;
            letter-spacing: .04em !important;
            color: var(--charcoal) !important;
            text-decoration: none !important;
        }

        .navbar .logo span {
            color: var(--rose) !important;
            font-style: italic !important;
            font-weight: 300 !important;
        }

        .navbar-right {
            display: flex !important;
            align-items: center !important;
            gap: 24px !important;
            margin: 0 !important;
        }

        .navbar-link {
            font-size: .82rem !important;
            font-weight: 500 !important;
            color: var(--muted) !important;
            text-decoration: none !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            transition: color .2s !important;
            padding: 0 !important;
            background: transparent !important;
        }

        .navbar-link:hover {
            color: var(--rose) !important;
            background: transparent !important;
        }

        .navbar-link.active {
            color: var(--rose) !important;
            font-weight: 600 !important;
        }

        .btn-connexion {
            font-size: .82rem !important;
            font-weight: 500 !important;
            color: var(--rose) !important;
            text-decoration: none !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            transition: color .2s !important;
            padding: 6px 16px !important;
            border: 1px solid var(--rose) !important;
            border-radius: 20px !important;
            background: transparent !important;
        }

        .btn-connexion:hover {
            background: var(--rose) !important;
            color: white !important;
        }

        .avatar-sm {
            width: 36px !important;
            height: 36px !important;
            border-radius: 50% !important;
            background: linear-gradient(135deg, var(--rose), var(--gold)) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-family: 'Cormorant Garamond', serif !important;
            font-size: 1rem !important;
            color: white !important;
            font-weight: 600 !important;
            border: 2px solid var(--border) !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
        }

        /* ===== STYLES GLOBAUX ===== */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

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
            padding-top: 0 !important;
        }

        /* ===== HERO ===== */
        .hero {
            position: relative;
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            background: linear-gradient(135deg, var(--rose-dark), var(--rose));
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%),
                radial-gradient(circle at 80% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 60%);
            z-index: 1;
        }

        .hero-band {
            position: absolute;
            top: -200px;
            right: -200px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            z-index: 1;
        }

        .hero-band::after {
            content: '';
            position: absolute;
            bottom: -250px;
            left: -250px;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.02);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 40px 24px;
        }

        .hero-brand {
            font-size: .8rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            opacity: .7;
            margin-bottom: 16px;
        }

        .hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            font-weight: 600;
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .hero-title span {
            font-style: italic;
            color: var(--gold-light);
        }

        .hero-subtitle {
            font-size: 1.1rem;
            opacity: .85;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            background: white;
            color: var(--rose-dark);
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all .3s;
            border: none;
        }

        .hero-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            color: var(--rose-dark);
            text-decoration: none;
        }

        /* ===== STRIP ===== */
        .strip {
            display: flex;
            justify-content: center;
            gap: 48px;
            padding: 20px 24px;
            background: var(--charcoal);
            color: white;
            flex-wrap: wrap;
        }

        .strip-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .85rem;
            opacity: .8;
        }

        .strip-item svg {
            flex-shrink: 0;
        }

        /* ===== FORMATIONS ===== */
        .formations {
            padding: 80px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .formations-header {
            text-align: center;
            margin-bottom: 56px;
        }

        .section-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: var(--rose);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            font-weight: 600;
        }

        .section-title em {
            color: var(--rose);
            font-style: italic;
        }

        .section-title+p {
            color: var(--muted);
            max-width: 600px;
            margin: 12px auto 0;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 32px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 24px;
            overflow: hidden;
            transition: all .3s;
            opacity: 0;
            transform: translateY(30px);
        }

        .card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow);
            border-color: var(--rose);
        }

        .card-img-wrap {
            position: relative;
            overflow: hidden;
            height: 220px;
            background: var(--blush);
        }

        .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s;
        }

        .card:hover .card-img-wrap img {
            transform: scale(1.03);
        }

        .card-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            background: var(--gold);
            color: white;
        }

        .card-badge.promo {
            background: #c0392b;
        }

        .card-badge.new {
            background: #27ae60;
        }

        .card-body {
            padding: 28px 24px 24px;
        }

        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .card-title em {
            color: var(--rose);
            font-style: italic;
        }

        .card-desc {
            font-size: .88rem;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .checklist {
            list-style: none;
            padding: 0;
            margin: 0 0 16px;
        }

        .checklist li {
            padding: 4px 0;
            font-size: .85rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checklist li::before {
            content: '✓';
            color: var(--rose);
            font-weight: 600;
        }

        .card-meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .meta-pill {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 20px;
            background: var(--cream);
            font-size: .78rem;
            color: var(--muted);
        }

        .pricing {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 16px;
            padding: 12px 16px;
            background: var(--cream);
            border-radius: 12px;
        }

        .price-box {
            text-align: center;
        }

        .price-amount {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--rose-dark);
        }

        .price-label {
            font-size: .7rem;
            color: var(--muted);
        }

        .card-cta {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            background: var(--rose);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all .3s;
            font-size: .95rem;
            cursor: pointer;
        }

        .card-cta:hover {
            background: var(--rose-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(201, 135, 122, .35);
            text-decoration: none;
        }

        .card-cta.inscrit {
            background: var(--muted);
            cursor: default;
        }

        .card-cta.inscrit:hover {
            background: var(--muted);
            transform: none;
            box-shadow: none;
        }

        .card-cta.btn-connexion-required {
            background: var(--gold);
        }

        .card-cta.btn-connexion-required:hover {
            background: var(--gold);
            opacity: 0.8;
        }

        /* ===== WHY US ===== */
        .why {
            background: var(--white);
            padding: 60px 24px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .why-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .why-text {
            opacity: 0;
            transform: translateX(-20px);
            transition: all .6s;
        }

        .why-text.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .why-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 24px;
            margin-top: 20px;
        }

        .why-feature {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .why-feature-icon {
            font-size: 1.2rem;
            color: var(--rose);
            flex-shrink: 0;
            margin-top: 2px;
            width: 28px;
            text-align: center;
        }

        .why-feature-body h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .why-feature-body p {
            font-size: 0.82rem;
            color: var(--muted);
            line-height: 1.5;
            margin: 0;
        }

        .why-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            opacity: 0;
            transform: translateX(20px);
            transition: all .6s .2s;
        }

        .why-stats.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .stat-box {
            background: var(--cream);
            padding: 20px 16px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .stat-number {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 600;
            color: var(--rose-dark);
            display: block;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--muted);
            display: block;
            margin-top: 2px;
        }

        /* ===== CONTACT ===== */
        .contact {
            text-align: center;
            padding: 80px 24px;
            background: linear-gradient(135deg, var(--rose-dark), var(--rose));
            color: white;
        }

        .contact h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.4rem;
            font-weight: 600;
        }

        .contact p {
            opacity: .85;
            margin: 12px auto 32px;
            max-width: 500px;
        }

        .contact-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .contact-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 28px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all .3s;
            backdrop-filter: blur(4px);
        }

        .contact-link:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }

        /* ===== REVEAL ===== */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all .6s;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== MODAL CONNEXION ===== */
        .modal-connexion .modal-content {
            border-radius: 24px !important;
            border: none !important;
            overflow: hidden !important;
        }

        .modal-connexion .modal-header {
            border-bottom: 2px solid var(--border) !important;
            padding: 20px 24px !important;
        }

        .modal-connexion .modal-body {
            padding: 32px 24px !important;
        }

        .modal-connexion .btn-inscription-link {
            color: var(--rose) !important;
            text-decoration: none !important;
            font-weight: 600 !important;
        }

        .modal-connexion .btn-inscription-link:hover {
            text-decoration: underline !important;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .why-inner {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .why-stats {
                order: -1;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
            }

            .cards-grid {
                grid-template-columns: 1fr 1fr;
            }

            .why-features {
                grid-template-columns: 1fr 1fr;
                gap: 12px 20px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px !important;
            }

            .hero-title {
                font-size: 2.8rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .pricing {
                grid-template-columns: 1fr 1fr;
            }

            .why {
                padding: 40px 16px;
            }

            .why-inner {
                gap: 24px;
            }

            .why-stats {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-box {
                padding: 14px 12px;
                border-radius: 12px;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .stat-label {
                font-size: 0.7rem;
            }

            .why-features {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .strip {
                gap: 16px;
                padding: 16px;
            }

            .strip-item {
                font-size: .75rem;
            }

            .contact-links {
                flex-direction: column;
                align-items: center;
            }

            .navbar-right {
                gap: 12px !important;
            }

            .navbar-link {
                font-size: .7rem !important;
            }

            .btn-connexion {
                font-size: .7rem !important;
                padding: 4px 12px !important;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2.2rem;
            }

            .why-stats {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .pricing {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 20px 16px;
            }

            .stat-box {
                padding: 12px 8px;
                border-radius: 10px;
            }

            .stat-number {
                font-size: 1.3rem;
            }

            .stat-label {
                font-size: 0.65rem;
            }

            .why-feature-icon {
                font-size: 1rem;
                width: 22px;
            }

            .navbar {
                padding: 0 12px !important;
            }

            .navbar-link {
                font-size: .6rem !important;
            }

            .btn-connexion {
                font-size: .6rem !important;
                padding: 3px 10px !important;
            }

            .avatar-sm {
                width: 28px !important;
                height: 28px !important;
                font-size: .8rem !important;
            }
        }
    </style>
</head>

<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar">
        <a href="?page=dashboard" class="logo">FEMI <span>Fairy Finger</span></a>
        <div class="navbar-right">
            <?php if ($isLoggedIn && $user): ?>
                <a href="?page=dashboard" class="navbar-link">Dashboard</a>
                <a href="?page=mesformations" class="navbar-link">Mes formations</a>
                <a href="?page=certificats" class="navbar-link">Certificats</a>
                <a href="?page=profile" class="navbar-link">Profil</a>
                <div class="avatar-sm"><?= h(initiales($user['prenom'], $user['nom'])) ?></div>
            <?php else: ?>
                <a href="?page=login" class="btn-connexion">Se connecter</a>
                <a href="?page=register" class="btn-connexion" style="background: var(--rose); color: white;">S'inscrire</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-band"></div>
        <div class="hero-content">
            <div class="hero-brand">FEMI &nbsp;·&nbsp; FAIRY FINGER</div>
            <h1 class="hero-title">
                Nos<br>
                <span>Formations</span>
            </h1>
            <p class="hero-subtitle">
                Devenez une professionnelle de la beauté des ongles.<br>
                Des formations pratiques, certifiées, à votre rythme.
            </p>
            <a href="#formations" class="hero-cta">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M8 3v10M8 13l-4-4M8 13l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Voir les formations
            </a>
        </div>
    </section>

    <!-- ===== INFO STRIP ===== -->
    <div class="strip">
        <div class="strip-item">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z" />
            </svg>
            <?= count($formations) ?> formations disponibles
        </div>
        <div class="strip-item">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            Attestation + Support
        </div>
        <div class="strip-item">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
            </svg>
            Dès maintenant
        </div>
        <div class="strip-item">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
            </svg>
            Djadjo, Abomey-Calavi
        </div>
    </div>

    <!-- ===== FORMATIONS ===== -->
    <section class="formations" id="formations">
        <div class="formations-header reveal">
            <div class="section-label">Ce que nous proposons</div>
            <h2 class="section-title">Choisissez votre <em>formation</em></h2>
            <p>Trois parcours professionnels pour maîtriser l'art de la beauté des ongles, du débutant au niveau avancé.</p>
        </div>

        <div class="cards-grid">
            <?php if (empty($formations)): ?>
                <div class="text-center py-5" style="grid-column: 1 / -1;">
                    <p class="text-muted">Aucune formation disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($formations as $index => $f): 
                    $estInscrit = $isLoggedIn && isset($formationsInscrites[$f['id']]);
                    $categoryIcon = getCategoryIcon($f['categorie']);
                    $categoryColor = getCategoryColor($f['categorie']);
                    $categoryName = getCategoryBadge($f['categorie']);
                    $badgeClass = '';
                    $badgeText = '';
                    
                    // Déterminer le badge
                    if (strpos(strtolower($f['titre']), 'spécial') !== false) {
                        $badgeClass = 'promo';
                        $badgeText = 'Promo';
                    } elseif ($index === 0) {
                        $badgeClass = 'new';
                        $badgeText = 'Nouveau';
                    }
                ?>
                    <div class="card reveal" style="transition-delay: <?= $index * 0.1 ?>s">
                        <div class="card-img-wrap">
                            <?php
                            // Image par défaut selon la catégorie
                            $defaultImage = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"%3E%3Crect width="400" height="300" fill="%23f0e6df"/%3E%3Ctext x="200" y="150" font-family="Arial" font-size="48" text-anchor="middle" fill="%23c9877a"%3E' . urlencode($categoryIcon) . '%3C/text%3E%3C/svg%3E';
                            ?>
                            <img src="<?= $defaultImage ?>" alt="<?= h($f['titre']) ?>">
                            <?php if ($badgeText): ?>
                                <span class="card-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">
                                <?php if (strpos(strtolower($f['categorie']), 'speciale') !== false): ?>
                                    <em>Spéciale</em>
                                <?php endif; ?>
                                <?= h($f['titre']) ?>
                            </h3>
                            <p class="card-desc"><?= h($f['description'] ?? 'Formation complète en onglerie professionnelle') ?></p>
                            <ul class="checklist">
                                <?php
                                // Générer des points de formation basés sur la catégorie
                                $points = [
                                    'onglerie' => ['Manucure & pédicure', 'Onglerie professionnelle', 'Nail art moderne', 'Hygiène & techniques pro'],
                                    'onglerie-speciale' => ['Pose d\'ongles en gel & acrylique', 'Techniques de finition pro', 'Support de cours inclus', 'Suivi personnalisé'],
                                    'pedicure-manucure' => ['Soins des mains & des pieds', 'Esthétique des ongles', 'Techniques professionnelles', 'Hygiène & protocoles'],
                                    'default' => ['Formation professionnelle', 'Techniques modernes', 'Pratique intensive', 'Certification incluse']
                                ];
                                $pointsList = $points[strtolower($f['categorie'])] ?? $points['default'];
                                foreach ($pointsList as $point) {
                                    echo '<li>' . h($point) . '</li>';
                                }
                                ?>
                            </ul>
                            <div class="card-meta">
                                <div class="meta-pill">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 6v6l4 2" />
                                    </svg>
                                    <strong><?= (int)($f['duree'] ?? 3) ?> mois</strong>
                                </div>
                                <div class="meta-pill">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path d="M9 11l3 3L22 4" />
                                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                                    </svg>
                                    <?= h($categoryName) ?>
                                </div>
                                <div class="meta-pill">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <rect x="2" y="7" width="20" height="14" rx="2" />
                                        <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
                                    </svg>
                                    Attestation
                                </div>
                            </div>
                            <div class="pricing">
                                <div class="price-box">
                                    <div class="price-amount"><?= formatPrice($f['prix'] ?? 75000) ?></div>
                                    <div class="price-label">Sans matériel</div>
                                </div>
                                <div class="price-box">
                                    <div class="price-amount"><?= formatPrice(($f['prix'] ?? 75000) * 2) ?></div>
                                    <div class="price-label">Avec matériel débutant</div>
                                </div>
                            </div>
                            <?php if ($estInscrit): ?>
                                <a href="?page=mesformations" class="card-cta inscrit">✅ Déjà inscrit</a>
                            <?php elseif ($isLoggedIn): ?>
                                <button type="button" class="card-cta inscription-btn"
                                        data-formation-id="<?= $f['id'] ?>"
                                        data-formation-titre="<?= h($f['titre']) ?>"
                                        data-formation-prix="<?= $f['prix'] ?? 75000 ?>">
                                    S'inscrire maintenant
                                </button>
                            <?php else: ?>
                                <button type="button" class="card-cta card-cta btn-connexion-required"
                                        onclick="showConnexionModal('<?= h($f['titre']) ?>')">
                                    🔒 Connectez-vous pour vous inscrire
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== WHY US ===== -->
    <section class="why">
        <div class="why-inner">
            <div class="why-text reveal">
                <div class="section-label">Pourquoi nous choisir</div>
                <h2 class="section-title">L'excellence à <em>chaque</em> formation</h2>
                <div class="why-features">
                    <div class="why-feature">
                        <div class="why-feature-icon">✦</div>
                        <div class="why-feature-body">
                            <h4>Formatrices certifiées</h4>
                            <p>Nos formatrices sont des professionnelles expérimentées avec des années de pratique en salon.</p>
                        </div>
                    </div>
                    <div class="why-feature">
                        <div class="why-feature-icon">◈</div>
                        <div class="why-feature-body">
                            <h4>Formation 100% pratique</h4>
                            <p>Vous travaillez sur de vraies clientes dès le premier mois pour acquérir une expérience réelle.</p>
                        </div>
                    </div>
                    <div class="why-feature">
                        <div class="why-feature-icon">⬡</div>
                        <div class="why-feature-body">
                            <h4>Attestation reconnue</h4>
                            <p>Obtenez une attestation de formation et un support de cours pour démarrer votre activité.</p>
                        </div>
                    </div>
                    <div class="why-feature">
                        <div class="why-feature-icon">◎</div>
                        <div class="why-feature-body">
                            <h4>Accompagnement post-formation</h4>
                            <p>Nous vous accompagnons même après la formation pour vous aider à lancer votre carrière.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="why-stats reveal" style="transition-delay:0.2s">
                <div class="stat-box">
                    <span class="stat-number"><?= date('Y') - 2020 + 1 ?>+</span>
                    <span class="stat-label">Années d'expérience</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">200+</span>
                    <span class="stat-label">Apprenantes formées</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?= count($formations) ?></span>
                    <span class="stat-label">Formations disponibles</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Pratique terrain</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CONTACT ===== -->
    <section class="contact">
        <h2>Prête à vous lancer ?</h2>
        <p>Contactez-nous dès aujourd'hui pour réserver votre place. Les inscriptions sont limitées.</p>
        <div class="contact-links">
            <a href="tel:+2290161798094" class="contact-link">
                <svg width="18" height="18" fill="white" viewBox="0 0 24 24">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                </svg>
                (+229) 01 61 79 80 94
            </a>
            <a href="https://wa.me/2290161798094" class="contact-link">
                <svg width="18" height="18" fill="white" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                WhatsApp
            </a>
            <a href="https://instagram.com/femiempire229" class="contact-link">
                <svg width="18" height="18" fill="white" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                </svg>
                @femiempire229
            </a>
        </div>
    </section>

    <!-- ===== MODAL CONNEXION REQUISE ===== -->
    <div class="modal fade modal-connexion" id="connexionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-lock-fill text-primary me-2"></i>
                        Connexion requise
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="display-1 mb-3">🔒</div>
                    <h4 class="fw-bold mb-3">Connectez-vous pour vous inscrire</h4>
                    <p class="text-muted mb-2">Pour vous inscrire à la formation :</p>
                    <p class="fw-bold text-primary" id="modalFormationTitreConnexion">-</p>
                    <p class="text-muted mb-4">Vous devez être connecté à votre compte pour effectuer une inscription.</p>
                    <div class="d-flex flex-column gap-3">
                        <a href="?page=login" class="btn btn-primary rounded-pill py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Se connecter
                        </a>
                        <div>
                            <span class="text-muted">Pas encore de compte ?</span>
                            <a href="?page=register" class="btn-inscription-link">
                                Créer un compte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL INSCRIPTION ===== -->
    <div class="modal fade" id="inscriptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 24px; border: none;">
                <div class="modal-header" style="border-bottom: 2px solid #f0f0f0; padding: 20px 24px;">
                    <h5 class="modal-title fw-bold" id="inscriptionModalLabel">
                        <i class="bi bi-journal-bookmark text-primary me-2"></i>
                        Confirmation d'inscription
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <div class="display-1 mb-3">📝</div>
                    <h4 class="fw-bold mb-3">Confirmer votre inscription</h4>
                    <p class="text-muted mb-2">Vous allez vous inscrire à la formation :</p>
                    <p class="fw-bold" id="modalFormationTitre">-</p>
                    <p class="text-muted">Montant : <strong class="text-primary" id="modalFormationPrix">-</strong> FCFA</p>
                    <form method="POST" id="inscriptionForm">
                        <input type="hidden" name="action" value="inscrire">
                        <input type="hidden" name="formation_id" id="modalFormationId" value="">
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success rounded-pill px-5 me-2">
                                <i class="bi bi-check-circle me-2"></i>
                                Confirmer l'inscription
                            </button>
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL CONFIRMATION ===== -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 24px; border: none;">
                <div class="modal-body text-center p-5">
                    <div class="display-1 mb-3">✅</div>
                    <h4 class="fw-bold mb-3">Inscription réussie !</h4>
                    <p class="text-muted mb-4">Vous êtes maintenant inscrit à cette formation.</p>
                    <button type="button" class="btn btn-primary rounded-pill px-5" data-bs-dismiss="modal" id="confirmationContinueBtn">
                        <i class="bi bi-check-circle me-2"></i>
                        Continuer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== SCROLL REVEAL =====
            var reveals = document.querySelectorAll('.reveal');
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.12
            });
            reveals.forEach(function(el) {
                observer.observe(el);
            });

            // ===== MODAL CONNEXION =====
            var connexionModal = new bootstrap.Modal(document.getElementById('connexionModal'));

            window.showConnexionModal = function(formationTitre) {
                document.getElementById('modalFormationTitreConnexion').textContent = formationTitre;
                connexionModal.show();
            };

            // ===== INSCRIPTION =====
            var inscriptionModal = new bootstrap.Modal(document.getElementById('inscriptionModal'));
            var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            document.querySelectorAll('.inscription-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    var formationId = this.dataset.formationId;
                    var formationTitre = this.dataset.formationTitre;
                    var formationPrix = this.dataset.formationPrix;

                    document.getElementById('modalFormationId').value = formationId;
                    document.getElementById('modalFormationTitre').textContent = formationTitre;
                    document.getElementById('modalFormationPrix').textContent = parseInt(formationPrix)
                        .toLocaleString();

                    inscriptionModal.show();
                });
            });

            document.getElementById('inscriptionForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                try {
                    var response = await fetch('includes/inscription_handler.php', {
                        method: 'POST',
                        body: formData
                    });

                    var data = await response.json();

                    if (data.success) {
                        inscriptionModal.hide();

                        document.getElementById('confirmationModalLabel').textContent = 'Inscription réussie !';
                        document.getElementById('confirmationTitle').textContent = '✅ Inscription réussie !';
                        document.getElementById('confirmationMessage').textContent =
                            'Vous êtes maintenant inscrit à cette formation.';
                        confirmationModal.show();

                        document.getElementById('confirmationContinueBtn').onclick = function() {
                            location.reload();
                        };
                    } else {
                        alert('Erreur : ' + (data.error || 'Une erreur est survenue'));
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Une erreur technique est survenue. Veuillez réessayer.');
                }
            });

            // ===== CONSOLE LOG DEBUG =====
            console.log('=== 🐛 FORMATIONS ===');
            console.log('👤 Utilisateur connecté:', <?= json_encode($isLoggedIn) ?>);
            <?php if ($isLoggedIn && $user): ?>
                console.log('👤 Infos utilisateur:', <?= json_encode($user) ?>);
            <?php endif; ?>
            console.log('📚 Formations:', <?= json_encode($formations) ?>);
            console.log('📊 Inscriptions:', <?= json_encode($formationsInscrites) ?>);
            console.log('=== FIN DEBUG ===');
        });
    </script>

</body>

</html>