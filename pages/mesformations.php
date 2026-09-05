<?php

// ============================================================
// mesformations.php
// Formations de l'utilisateur connecté
// ============================================================

require_once __DIR__ . '/../config/database.php';

// ------------------------------------------------------------
// SESSION
// ------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------------------------------------
// VÉRIFICATION CONNEXION
// ------------------------------------------------------------
if (empty($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];

$success = '';
$error = '';

try {

    $pdo = getDB();

    // ========================================================
    // RÉCUPÉRER L'UTILISATEUR CONNECTÉ
    // ========================================================

    $stmt = $pdo->prepare("
        SELECT
            id,
            prenom,
            nom,
            email,
            role,
            created_at
        FROM utilisateurs
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$userId]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();

        header('Location: ?page=login');
        exit;
    }


    // ========================================================
    // RÉCUPÉRER LES FORMATIONS DE L'UTILISATEUR
    // ========================================================
    //
    // Une seule requête suffit.
    //
    // On récupère :
    // - les informations de la formation
    // - les informations de la commande
    // - la progression
    // - le statut du paiement
    //
    // ========================================================

    $stmt = $pdo->prepare("
        SELECT

            f.id AS formation_id,
            f.titre,
            f.description,
            f.image,
            f.categorie,
            f.prix,
            f.duree,
            f.statut AS formation_statut,

            c.id AS commande_id,
            c.montant,
            c.status AS commande_status,
            c.transaction_id,
            c.reference AS commande_reference,
            c.progression,
            c.modules_done,
            c.date_creation AS date_commande,
            c.date_obtention

        FROM commandes c

        INNER JOIN formations f
            ON f.id = c.formation_id

        WHERE c.utilisateur_id = ?

        ORDER BY c.date_creation DESC
    ");

    $stmt->execute([$userId]);

    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // ========================================================
    // STATISTIQUES
    // ========================================================

    $nbFormations = 0;
    $nbEnAttente = 0;
    $nbAnnulees = 0;
    $nbTerminees = 0;

    $progressionTotal = 0;


    foreach ($formations as &$formation) {

        $status = $formation['commande_status'];

        $progression = (int)($formation['progression'] ?? 0);

        // Sécurité : progression entre 0 et 100
        if ($progression < 0) {
            $progression = 0;
        }

        if ($progression > 100) {
            $progression = 100;
        }

        $formation['progression'] = $progression;


        // ----------------------------------------------------
        // PAYÉE
        // ----------------------------------------------------

        if ($status === 'payee') {

            $nbFormations++;

            $progressionTotal += $progression;

            if ($progression >= 100) {
                $nbTerminees++;
            }
        }


        // ----------------------------------------------------
        // EN ATTENTE
        // ----------------------------------------------------

        elseif ($status === 'en_attente') {

            $nbEnAttente++;
        }


        // ----------------------------------------------------
        // ANNULÉE
        // ----------------------------------------------------

        elseif ($status === 'annulee') {

            $nbAnnulees++;
        }
    }

    unset($formation);


    // ========================================================
    // PROGRESSION MOYENNE
    // ========================================================

    $progressTotal = $nbFormations > 0
        ? (int) round($progressionTotal / $nbFormations)
        : 0;


    // ============================================================
    // ERREUR
    // ============================================================

} catch (PDOException $e) {

    error_log(
        'ERREUR MES FORMATIONS : ' . $e->getMessage()
    );

    die('Une erreur est survenue lors du chargement de vos formations.');
}


// ============================================================
// FONCTIONS UTILITAIRES
// ============================================================

function h($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


function initiales($prenom, $nom): string
{
    $prenom = trim((string)$prenom);
    $nom = trim((string)$nom);

    $initiales = '';

    if ($prenom !== '') {
        $initiales .= mb_substr($prenom, 0, 1);
    }

    if ($nom !== '') {
        $initiales .= mb_substr($nom, 0, 1);
    }

    return strtoupper($initiales ?: '?');
}


function formatDateFr($date): string
{
    if (empty($date)) {
        return '—';
    }

    $timestamp = strtotime($date);

    if (!$timestamp) {
        return '—';
    }

    $mois = [
        'janvier',
        'février',
        'mars',
        'avril',
        'mai',
        'juin',
        'juillet',
        'août',
        'septembre',
        'octobre',
        'novembre',
        'décembre'
    ];

    return
        date('j', $timestamp)
        . ' '
        . $mois[(int)date('n', $timestamp) - 1]
        . ' '
        . date('Y', $timestamp);
}


function getCategoryIcon($categorie): string
{
    $icons = [

        'onglerie' => '💅',
        'business' => '💼',
        'design' => '🎨',
        'marketing' => '📈',
        'beaute' => '✨',
        'beauté' => '✨',
        'bien-etre' => '🧘',
        'bien-être' => '🧘',

        'default' => '📚'
    ];

    $key = strtolower(trim((string)$categorie));

    return $icons[$key] ?? $icons['default'];
}


function getCategoryColor($categorie): string
{
    $colors = [

        'onglerie' => '#fde8e5',
        'business' => '#e8d5f5',
        'design' => '#d5e8f5',
        'marketing' => '#fef0dc',
        'beaute' => '#fce8f0',
        'beauté' => '#fce8f0',
        'bien-etre' => '#d5f5e8',
        'bien-être' => '#d5f5e8',

        'default' => '#f0eded'
    ];

    $key = strtolower(trim((string)$categorie));

    return $colors[$key] ?? $colors['default'];
}


function formatPrix($prix): string
{
    return number_format(
        (float)$prix,
        0,
        ',',
        ' '
    ) . ' FCFA';
}

?>
<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        FEMI Fairy Finger — Mes Formations
    </title>


    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">


    <style>
        /* =====================================================
           VARIABLES
        ====================================================== */

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

            --green: #1a7a45;

            --green-light: #d4f5e4;

            --orange: #a05c10;

            --orange-light: #fef0dc;

            --red: #a8200d;

            --red-light: #fde8e5;

            --shadow:
                0 8px 32px rgba(0, 0, 0, .12);
        }


        /* =====================================================
           RESET
        ====================================================== */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family: 'DM Sans', sans-serif;

            background: var(--cream);

            color: var(--charcoal);

            min-height: 100vh;
        }


        /* =====================================================
           NAVBAR
        ====================================================== */

        .navbar {

            position: sticky;

            top: 0;

            z-index: 100;

            background:
                rgba(250, 247, 244, .94);

            backdrop-filter: blur(12px);

            border-bottom:
                1px solid var(--border);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 48px;

            height: 64px;
        }


        .logo {

            font-family:
                'Cormorant Garamond',
                serif;

            font-size: 1.4rem;

            font-weight: 600;

            letter-spacing: .04em;
        }


        .logo span {

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

            background:
                linear-gradient(135deg,
                    var(--rose),
                    var(--gold));

            display: flex;

            align-items: center;

            justify-content: center;

            font-family:
                'Cormorant Garamond',
                serif;

            font-size: 1rem;

            color: white;

            font-weight: 600;

            border:
                2px solid var(--border);
        }


        /* =====================================================
           LAYOUT
        ====================================================== */

        .page-wrap {

            max-width: 1200px;

            margin: 0 auto;

            padding:
                40px 32px 80px;

            display: grid;

            grid-template-columns:
                300px 1fr;

            gap: 32px;

            align-items: start;
        }


        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {

            display: flex;

            flex-direction: column;

            gap: 16px;

            position: sticky;

            top: 80px;
        }


        .profile-card {

            background: var(--white);

            border:
                1px solid var(--border);

            border-radius: 20px;

            padding:
                32px 24px;

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

            background:
                linear-gradient(135deg,
                    var(--blush),
                    #f5e8e0);
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

            background:
                linear-gradient(135deg,
                    var(--rose),
                    var(--gold));

            display: flex;

            align-items: center;

            justify-content: center;

            font-family:
                'Cormorant Garamond',
                serif;

            font-size: 2.2rem;

            color: white;

            font-weight: 600;

            border:
                4px solid var(--white);

            box-shadow:
                0 4px 20px rgba(201, 135, 122, .3);
        }


        .profile-name {

            font-family:
                'Cormorant Garamond',
                serif;

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

            gap: 20px;

            padding: 16px 0;

            border-top:
                1px solid var(--border);
        }


        .stat {

            text-align: center;
        }


        .stat-val {

            font-family:
                'Cormorant Garamond',
                serif;

            font-size: 1.5rem;

            font-weight: 600;

            display: block;
        }


        .stat-lbl {

            font-size: .68rem;

            color: var(--muted);

            text-transform: uppercase;

            letter-spacing: .06em;
        }


        /* =====================================================
           SIDE NAV
        ====================================================== */

        .side-nav {

            background: var(--white);

            border:
                1px solid var(--border);

            border-radius: 16px;

            overflow: hidden;
        }


        .side-nav-item {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                14px 20px;

            font-size: .85rem;

            font-weight: 500;

            color: var(--muted);

            text-decoration: none;

            transition: all .2s;

            border-left:
                3px solid transparent;
        }


        .side-nav-item:not(:last-child) {

            border-bottom:
                1px solid var(--border);
        }


        .side-nav-item:hover {

            background: var(--cream);

            color: var(--charcoal);
        }


        .side-nav-item.active {

            background: #fef4f2;

            color: var(--rose);

            border-left-color:
                var(--rose);
        }


        /* =====================================================
           LOGOUT
        ====================================================== */

        .logout-btn {

            background: var(--white);

            border:
                1px solid var(--border);

            border-radius: 12px;

            padding:
                13px 20px;

            display: flex;

            align-items: center;

            gap: 10px;

            font-size: .83rem;

            font-weight: 500;

            color: #c0392b;

            text-decoration: none;

            transition: all .2s;
        }


        .logout-btn:hover {

            background: #fff5f5;

            border-color:
                #f5c6c0;
        }


        /* =====================================================
           MAIN
        ====================================================== */

        .main-content {

            display: flex;

            flex-direction: column;

            gap: 24px;
        }


        /* =====================================================
           HEADER
        ====================================================== */

        .page-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            flex-wrap: wrap;

            gap: 16px;
        }


        .page-title {

            font-family:
                'Cormorant Garamond',
                serif;

            font-size: 2.2rem;

            font-weight: 600;
        }


        .page-title em {

            color: var(--rose);

            font-style: italic;
        }


        .header-stats {

            display: flex;

            gap: 20px;

            background: var(--white);

            padding:
                12px 20px;

            border-radius: 16px;

            border:
                1px solid var(--border);

            flex-wrap: wrap;
        }


        .stat-item {

            text-align: center;
        }


        .stat-number {

            font-family:
                'Cormorant Garamond',
                serif;

            font-size: 1.7rem;

            font-weight: 600;

            display: block;

            color: var(--rose);
        }


        .stat-label {

            font-size: .68rem;

            color: var(--muted);

            text-transform: uppercase;

            letter-spacing: .06em;
        }


        .stat-divider {

            width: 1px;

            background: var(--border);
        }


        /* =====================================================
           ALERTS
        ====================================================== */

        .alert {

            padding:
                14px 18px;

            border-radius: 12px;

            font-size: .85rem;

            border: 1px solid transparent;
        }


        .alert-success {

            background: var(--green-light);

            color: var(--green);

            border-color: #b9e8cd;
        }


        .alert-warning {

            background: var(--orange-light);

            color: var(--orange);

            border-color: #f5d7ac;
        }


        .alert-info {

            background: #e8f1ff;

            color: #255a9b;

            border-color: #c7dcfb;
        }


        .alert-danger {

            background: var(--red-light);

            color: var(--red);

            border-color: #f3c3bb;
        }


        /* =====================================================
           FILTRES
        ====================================================== */

        .filters {

            display: flex;

            gap: 8px;

            flex-wrap: wrap;
        }


        .filter-btn {

            padding:
                8px 20px;

            border:
                1px solid var(--border);

            border-radius: 20px;

            background: var(--white);

            font-family:
                'DM Sans',
                sans-serif;

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


        /* =====================================================
           GRILLE
        ====================================================== */

        .formations-grid {

            display: grid;

            grid-template-columns:
                repeat(auto-fill,
                    minmax(320px, 1fr));

            gap: 24px;
        }


        /* =====================================================
           CARD
        ====================================================== */

        .formation-card {

            background: var(--white);

            border:
                1px solid var(--border);

            border-radius: 20px;

            overflow: hidden;

            transition:
                transform .3s ease,
                box-shadow .3s ease;

            position: relative;

            animation:
                fadeUp .5s ease both;
        }


        .formation-card:hover {

            transform:
                translateY(-4px);

            box-shadow:
                var(--shadow);
        }


        .formation-card.hidden {

            display: none !important;
        }


        /* =====================================================
           IMAGE
        ====================================================== */

        .formation-image {

            width: 100%;

            height: 180px;

            object-fit: cover;

            display: block;
        }


        .formation-image-placeholder {

            width: 100%;

            height: 180px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 4rem;
        }


        /* =====================================================
           CARD HEADER
        ====================================================== */

        .card-header {

            padding:
                20px 24px;

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            border-bottom:
                1px solid var(--border);
        }


        .card-top {

            display: flex;

            align-items: center;

            width: 100%;
        }


        .card-icon {

            width: 48px;

            height: 48px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 1.5rem;

            flex-shrink: 0;
        }


        .card-info {

            flex: 1;

            margin-left: 14px;
        }


        .card-title {

            font-family:
                'Cormorant Garamond',
                serif;

            font-size: 1.25rem;

            font-weight: 600;

            margin-bottom: 4px;
        }


        .card-category {

            font-size: .72rem;

            color: var(--muted);

            text-transform: uppercase;

            letter-spacing: .08em;
        }


        /* =====================================================
           BODY
        ====================================================== */

        .card-body {

            padding:
                16px 24px 20px;
        }


        .card-description {

            font-size: .85rem;

            color: var(--muted);

            line-height: 1.6;

            margin-bottom: 12px;

            display: -webkit-box;

            -webkit-line-clamp: 2;

            -webkit-box-orient: vertical;

            overflow: hidden;
        }


        .card-meta {

            display: flex;

            gap: 12px;

            font-size: .76rem;

            color: var(--muted);

            margin-bottom: 12px;

            flex-wrap: wrap;
        }


        .card-meta span {

            display: flex;

            align-items: center;

            gap: 4px;
        }


        /* =====================================================
           PROGRESSION
        ====================================================== */

        .progress-section {

            margin-top: 12px;
        }


        .progress-header {

            display: flex;

            justify-content: space-between;

            font-size: .72rem;

            color: var(--muted);

            margin-bottom: 6px;
        }


        .progress-bar {

            width: 100%;

            height: 7px;

            background: var(--cream);

            border-radius: 10px;

            overflow: hidden;
        }


        .progress-fill {

            height: 100%;

            border-radius: 10px;

            transition:
                width .6s ease;
        }


        /* =====================================================
           FOOTER
        ====================================================== */

        .card-footer {

            padding:
                12px 24px;

            border-top:
                1px solid var(--border);

            display: flex;

            justify-content: space-between;

            align-items: center;

            background: var(--cream);

            gap: 12px;
        }


        /* =====================================================
           STATUS
        ====================================================== */

        .status-badge {

            font-size: .68rem;

            font-weight: 600;

            padding:
                5px 12px;

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


        /* =====================================================
           BUTTONS
        ====================================================== */

        .btn-continuer {

            background: var(--rose);

            color: white;

            border: none;

            border-radius: 8px;

            padding:
                7px 16px;

            font-family:
                'DM Sans',
                sans-serif;

            font-size: .78rem;

            font-weight: 500;

            cursor: pointer;

            transition: all .2s;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            white-space: nowrap;
        }


        .btn-continuer:hover {

            background: var(--rose-dark);

            transform:
                translateY(-1px);
        }


        .btn-paiement {

            background:
                linear-gradient(135deg,
                    var(--rose),
                    var(--rose-dark));

            box-shadow:
                0 4px 12px rgba(201, 135, 122, .25);
        }


        .btn-paiement:hover {

            box-shadow:
                0 6px 16px rgba(201, 135, 122, .35);
        }


        .btn-disabled {

            opacity: .65;

            background: var(--muted);

            cursor: default;
        }


        .btn-certificat {

            background: var(--green);
        }


        .btn-certificat:hover {

            background: #126438;
        }


        /* =====================================================
           EMPTY STATE
        ====================================================== */

        .empty-state {

            text-align: center;

            padding:
                80px 20px;

            color: var(--muted);

            background: var(--white);

            border:
                1px solid var(--border);

            border-radius: 20px;
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

            padding:
                12px 32px;

            border-radius: 12px;

            text-decoration: none;

            font-weight: 500;

            transition: all .2s;

            margin-top: 16px;
        }


        .btn-primary:hover {

            background: var(--rose-dark);

            transform:
                translateY(-2px);

            box-shadow:
                0 4px 20px rgba(201, 135, 122, .35);
        }


        /* =====================================================
           ANIMATION
        ====================================================== */

        @keyframes fadeUp {

            from {

                opacity: 0;

                transform:
                    translateY(20px);
            }

            to {

                opacity: 1;

                transform:
                    translateY(0);
            }
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 900px) {

            .page-wrap {

                grid-template-columns: 1fr;
            }

            .sidebar {

                position: static;
            }
        }


        @media (max-width: 768px) {

            .navbar {

                padding:
                    0 20px;
            }


            .navbar-right {

                gap: 10px;
            }


            .navbar-link {

                display: none;
            }


            .page-wrap {

                padding:
                    24px 16px 60px;
            }


            .page-header {

                flex-direction: column;

                align-items: flex-start;
            }


            .header-stats {

                width: 100%;

                justify-content:
                    space-around;
            }


            .formations-grid {

                grid-template-columns: 1fr;
            }


            .card-footer {

                flex-direction: column;

                align-items: stretch;
            }


            .card-footer .status-badge {

                text-align: center;
            }


            .btn-continuer {

                width: 100%;
            }
        }
    </style>

</head>


<body>

    <!-- ==========================================================
     PAGE
=========================================================== -->

    <div class="page-wrap">


        <!-- ======================================================
         SIDEBAR
    ======================================================= -->

        <aside class="sidebar">


            <!-- PROFILE -->

            <div class="profile-card">

                <div class="avatar-wrap">

                    <div class="avatar-main">

                        <?= h(
                            initiales(
                                $user['prenom'],
                                $user['nom']
                            )
                        ) ?>

                    </div>

                </div>


                <div class="profile-name">

                    <?= h(
                        $user['prenom']
                            . ' '
                            . $user['nom']
                    ) ?>

                </div>


                <div class="profile-role">

                    <?= $user['role'] === 'admin'
                        ? '⚙ Administrateur'
                        : '✧ Membre'
                    ?>

                </div>


                <div class="profile-stats">

                    <div class="stat">

                        <span class="stat-val">
                            <?= $nbFormations ?>
                        </span>

                        <span class="stat-lbl">
                            Formations
                        </span>

                    </div>


                    <div class="stat">

                        <span class="stat-val">
                            <?= $nbTerminees ?>
                        </span>

                        <span class="stat-lbl">
                            Terminées
                        </span>

                    </div>


                    <div class="stat">

                        <span class="stat-val">
                            <?= h(
                                date(
                                    'Y',
                                    strtotime(
                                        $user['created_at']
                                    )
                                )
                            ) ?>
                        </span>

                        <span class="stat-lbl">
                            Depuis
                        </span>

                    </div>

                </div>

            </div>



            <!-- MENU -->

            <nav class="side-nav">

                <a
                    href="?page=dashboard"
                    class="side-nav-item">
                    📊
                    Tableau de bord
                </a>


                <a
                    href="?page=mesformations"
                    class="side-nav-item active">
                    📚
                    Mes formations
                </a>


                <a
                    href="?page=certificats"
                    class="side-nav-item">
                    🏆
                    Certificats
                </a>


                <a
                    href="?page=profile"
                    class="side-nav-item">
                    👤
                    Mon profil
                </a>

            </nav>



            <!-- LOGOUT -->

            <a
                href="?action=logout"
                class="logout-btn">
                🚪
                Se déconnecter
            </a>

        </aside>



        <!-- ======================================================
         MAIN
    ======================================================= -->

        <main class="main-content">


            <!-- ==================================================
             ALERT PAIEMENT
        =================================================== -->

            <?php if (isset($_GET['paiement'])): ?>


                <?php if ($_GET['paiement'] === 'success'): ?>

                    <div class="alert alert-success">

                        ✅
                        Paiement effectué avec succès.
                        Votre formation est maintenant disponible.

                    </div>


                <?php elseif ($_GET['paiement'] === 'cancelled'): ?>

                    <div class="alert alert-warning">

                        ⚠️
                        Le paiement a été annulé.

                    </div>


                <?php elseif ($_GET['paiement'] === 'pending'): ?>

                    <div class="alert alert-info">

                        ⏳
                        Le paiement est encore en attente de confirmation.

                    </div>


                <?php elseif ($_GET['paiement'] === 'error'): ?>

                    <div class="alert alert-danger">

                        ❌
                        Impossible de confirmer le paiement.

                    </div>

                <?php endif; ?>


            <?php endif; ?>



            <!-- ==================================================
             HEADER
        =================================================== -->

            <div class="page-header">

                <h1 class="page-title">

                    Mes
                    <em>formations</em>

                </h1>


                <div class="header-stats">


                    <div class="stat-item">

                        <span class="stat-number">
                            <?= $nbFormations ?>
                        </span>

                        <span class="stat-label">
                            Formations
                        </span>

                    </div>


                    <div class="stat-divider"></div>


                    <div class="stat-item">

                        <span class="stat-number">
                            <?= $nbEnAttente ?>
                        </span>

                        <span class="stat-label">
                            En attente
                        </span>

                    </div>


                    <div class="stat-divider"></div>


                    <div class="stat-item">

                        <span class="stat-number">
                            <?= $progressTotal ?>%
                        </span>

                        <span class="stat-label">
                            Progression
                        </span>

                    </div>


                    <div class="stat-divider"></div>


                    <div class="stat-item">

                        <span class="stat-number">
                            <?= $nbTerminees ?>
                        </span>

                        <span class="stat-label">
                            Terminées
                        </span>

                    </div>

                </div>

            </div>



            <!-- ==================================================
             FILTRES
        =================================================== -->

            <div class="filters">


                <button
                    type="button"
                    class="filter-btn active"
                    data-filter="all">
                    Toutes
                </button>


                <button
                    type="button"
                    class="filter-btn"
                    data-filter="payee">
                    En cours
                </button>


                <button
                    type="button"
                    class="filter-btn"
                    data-filter="termine">
                    Terminées
                </button>


                <button
                    type="button"
                    class="filter-btn"
                    data-filter="en_attente">
                    En attente
                </button>


                <button
                    type="button"
                    class="filter-btn"
                    data-filter="annulee">
                    Annulées
                </button>

            </div>



            <!-- ==================================================
             FORMATIONS
        =================================================== -->

            <?php if (empty($formations)): ?>


                <div class="empty-state">

                    <span class="empty-icon">
                        📚
                    </span>


                    <h2
                        style="
                        font-family:'Cormorant Garamond',serif;
                        font-size:1.6rem;
                        margin-bottom:8px;
                    ">
                        Aucune formation
                    </h2>


                    <p>
                        Vous n'avez pas encore de formation.
                    </p>


                    <a
                        href="?page=formations"
                        class="btn-primary">
                        Découvrir nos formations
                    </a>

                </div>


            <?php else: ?>


                <div class="formations-grid">


                    <?php foreach ($formations as $index => $f): ?>


                        <?php

                        // ------------------------------------------------
                        // DONNÉES
                        // ------------------------------------------------

                        $formationId =
                            (int)$f['formation_id'];

                        $titre =
                            $f['titre'];

                        $categorie =
                            $f['categorie'] ?? 'Général';

                        $description =
                            $f['description']
                            ?? 'Formation complète.';

                        $prix =
                            (float)($f['prix'] ?? 0);

                        $duree =
                            (int)($f['duree'] ?? 0);

                        $progression =
                            (int)($f['progression'] ?? 0);

                        $commandeStatus =
                            $f['commande_status']
                            ?? 'en_attente';


                        // ------------------------------------------------
                        // TERMINÉE
                        // ------------------------------------------------

                        $estTerminee =
                            (
                                $commandeStatus === 'payee'
                                &&
                                $progression >= 100
                            );


                        // ------------------------------------------------
                        // CLASSE DE FILTRE
                        // ------------------------------------------------

                        if ($estTerminee) {

                            $filterStatus = 'termine';
                        } else {

                            $filterStatus =
                                $commandeStatus;
                        }


                        // ------------------------------------------------
                        // STATUT AFFICHÉ
                        // ------------------------------------------------

                        if ($estTerminee) {

                            $statusLabel =
                                '✅ Terminée';

                            $statusClass =
                                'termine';
                        } elseif ($commandeStatus === 'payee') {

                            $statusLabel =
                                'En cours';

                            $statusClass =
                                'payee';
                        } elseif ($commandeStatus === 'en_attente') {

                            $statusLabel =
                                '💳 Paiement requis';

                            $statusClass =
                                'en_attente';
                        } elseif ($commandeStatus === 'annulee') {

                            $statusLabel =
                                'Annulée';

                            $statusClass =
                                'annulee';
                        } else {

                            $statusLabel =
                                ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $commandeStatus
                                    )
                                );

                            $statusClass =
                                'en_attente';
                        }


                        // ------------------------------------------------
                        // COULEUR PROGRESSION
                        // ------------------------------------------------

                        $progressColor =
                            $estTerminee
                            ? 'var(--green)'
                            : 'var(--rose)';


                        // ------------------------------------------------
                        // IMAGE
                        // ------------------------------------------------

                        $image =
                            trim(
                                (string)(
                                    $f['image'] ?? ''
                                )
                            );

                        ?>


                        <!-- ==================================================
                         CARD
                    =================================================== -->

                        <article
                            class="formation-card"
                            data-status="<?= h($filterStatus) ?>"
                            style="
                            animation-delay:
                            <?= $index * 0.05 ?>s;
                        ">


                            <!-- ==============================================
                             IMAGE
                        =============================================== -->

                            <?php if ($image !== ''): ?>

                                <img
                                    src="<?= h($image) ?>"
                                    alt="<?= h($titre) ?>"
                                    class="formation-image">

                            <?php else: ?>

                                <div
                                    class="formation-image-placeholder"
                                    style="
                                    background:
                                    <?= h(
                                        getCategoryColor(
                                            $categorie
                                        )
                                    ) ?>;
                                ">

                                    <?= h(
                                        getCategoryIcon(
                                            $categorie
                                        )
                                    ) ?>

                                </div>

                            <?php endif; ?>



                            <!-- ==============================================
                             HEADER
                        =============================================== -->

                            <div class="card-header">

                                <div class="card-top">


                                    <div
                                        class="card-icon"
                                        style="
                                        background:
                                        <?= h(
                                            getCategoryColor(
                                                $categorie
                                            )
                                        ) ?>;
                                    ">

                                        <?= h(
                                            getCategoryIcon(
                                                $categorie
                                            )
                                        ) ?>

                                    </div>


                                    <div class="card-info">

                                        <h3 class="card-title">

                                            <?= h($titre) ?>

                                        </h3>


                                        <div class="card-category">

                                            <?= h(
                                                ucfirst(
                                                    $categorie
                                                )
                                            ) ?>

                                        </div>

                                    </div>

                                </div>

                            </div>



                            <!-- ==============================================
                             BODY
                        =============================================== -->

                            <div class="card-body">


                                <p class="card-description">

                                    <?= h($description) ?>

                                </p>



                                <div class="card-meta">


                                    <span>

                                        ⏱
                                        <?= $duree ?>
                                        heure<?= $duree > 1 ? 's' : '' ?>

                                    </span>


                                    <span>

                                        💰
                                        <?= h(
                                            formatPrix($prix)
                                        ) ?>

                                    </span>


                                    <?php if (!empty($f['date_obtention'])): ?>

                                        <span>

                                            📅
                                            <?= h(
                                                formatDateFr(
                                                    $f['date_obtention']
                                                )
                                            ) ?>

                                        </span>

                                    <?php elseif (!empty($f['date_commande'])): ?>

                                        <span>

                                            📅
                                            <?= h(
                                                formatDateFr(
                                                    $f['date_commande']
                                                )
                                            ) ?>

                                        </span>

                                    <?php endif; ?>


                                </div>



                                <!-- ==========================================
                                 PROGRESSION
                            =========================================== -->

                                <div class="progress-section">

                                    <div class="progress-header">

                                        <span>
                                            Progression
                                        </span>

                                        <span>
                                            <?= $progression ?>%
                                        </span>

                                    </div>


                                    <div class="progress-bar">

                                        <div
                                            class="progress-fill"
                                            style="width: <?= $progression ?>%; background: <?= $progressColor ?>;"></div>


                                    </div>

                                </div>


                            </div>



                            <!-- ==============================================
                             FOOTER
                        =============================================== -->

                            <div class="card-footer">


                                <span
                                    class="
                                    status-badge
                                    <?= h($statusClass) ?>
                                ">

                                    <?= h($statusLabel) ?>

                                </span>



                                <!-- =================================================
                                 FORMATION PAYÉE
                            ================================================== -->

                                <?php if ($commandeStatus === 'payee'): ?>


                                    <?php if ($estTerminee): ?>


                                        <a
                                            href="?page=certificats"
                                            class="
                                            btn-continuer
                                            btn-certificat
                                        ">
                                            🏆 Voir le certificat
                                        </a>


                                    <?php else: ?>


                                        <a
                                            href="??page=formation_detail&id=<?= $formationId ?>"
                                            class="btn-continuer">
                                            Continuer →
                                        </a>


                                    <?php endif; ?>


                                    <!-- =================================================
                                 PAIEMENT EN ATTENTE
                            ================================================== -->

                                <?php elseif ($commandeStatus === 'en_attente'): ?>


                                    <form
                                        action="?page=paiement"
                                        method="POST"
                                        class="formation-payment-form"
                                        style="margin:0;">


                                        <input
                                            type="hidden"
                                            name="formation_id"
                                            value="<?= $formationId ?>">


                                        <button
                                            type="button"
                                            class="
                                            btn-continuer
                                            btn-paiement
                                            paiement-btn
                                            inscription-btn
                                        "
                                            data-formation-id="<?= $formationId ?>"
                                            data-formation-titre="<?= h($titre) ?>"
                                            data-formation-prix="<?= $prix ?>">

                                            💳 Payer maintenant

                                        </button>


                                    </form>


                                    <!-- =================================================
                                 FORMATION ANNULÉE
                            ================================================== -->

                                <?php elseif ($commandeStatus === 'annulee'): ?>


                                    <form
                                        action="?page=paiement"
                                        method="POST"
                                        class="formation-payment-form"
                                        style="margin:0;">


                                        <input
                                            type="hidden"
                                            name="formation_id"
                                            value="<?= $formationId ?>">


                                        <button
                                            type="button"
                                            class="
                                            btn-continuer
                                            btn-paiement
                                            paiement-btn
                                            inscription-btn
                                        "
                                            data-formation-id="<?= $formationId ?>"
                                            data-formation-titre="<?= h($titre) ?>"
                                            data-formation-prix="<?= $prix ?>">

                                            💳 Reprendre le paiement

                                        </button>


                                    </form>


                                <?php endif; ?>


                            </div>


                        </article>


                    <?php endforeach; ?>


                </div>


                <!-- MESSAGE SI LE FILTRE NE TROUVE RIEN -->

                <div
                    id="no-filter-result"
                    class="empty-state"
                    style="display:none;">

                    <span class="empty-icon">
                        🔎
                    </span>

                    <h2
                        style="
                        font-family:'Cormorant Garamond',serif;
                        font-size:1.6rem;
                        margin-bottom:8px;
                    ">
                        Aucune formation trouvée
                    </h2>

                    <p>
                        Aucune formation ne correspond à ce filtre.
                    </p>

                </div>


            <?php endif; ?>


        </main>

    </div>



    <!-- ==========================================================
     JAVASCRIPT
=========================================================== -->

    <script>
        // ============================================================
        // FILTRES
        // ============================================================

        document
            .querySelectorAll('.filter-btn')
            .forEach(function(button) {


                button.addEventListener(
                    'click',
                    function() {


                        // --------------------------------------------
                        // Bouton actif
                        // --------------------------------------------

                        document
                            .querySelectorAll('.filter-btn')
                            .forEach(function(btn) {

                                btn.classList.remove('active');

                            });


                        this.classList.add('active');


                        // --------------------------------------------
                        // Filtre demandé
                        // --------------------------------------------

                        const filter =
                            this.dataset.filter;


                        // --------------------------------------------
                        // Cartes
                        // --------------------------------------------

                        const cards =
                            document.querySelectorAll(
                                '.formation-card'
                            );


                        let visibleCount = 0;


                        cards.forEach(function(card) {


                            const status =
                                card.dataset.status;


                            if (
                                filter === 'all' ||
                                status === filter
                            ) {

                                card.classList.remove(
                                    'hidden'
                                );

                                visibleCount++;

                            } else {

                                card.classList.add(
                                    'hidden'
                                );

                            }

                        });


                        // --------------------------------------------
                        // Message aucun résultat
                        // --------------------------------------------

                        const emptyMessage =
                            document.getElementById(
                                'no-filter-result'
                            );


                        if (emptyMessage) {

                            if (visibleCount === 0) {

                                emptyMessage.style.display =
                                    'block';

                            } else {

                                emptyMessage.style.display =
                                    'none';
                            }
                        }

                    }
                );

            });



        // ============================================================
        // PAIEMENT
        // ============================================================
        //
        // IMPORTANT :
        //
        // Le bouton "Paiement requis" utilise volontairement
        // les mêmes classes que "S'inscrire maintenant" :
        //
        //     paiement-btn inscription-btn
        //
        // Ainsi, si ton système actuel écoute déjà
        // ".inscription-btn", il pourra également fonctionner.
        //
        // ============================================================

        // ============================================================
// PAIEMENT - MES FORMATIONS
// ============================================================

document
    .querySelectorAll('.inscription-btn')
    .forEach(function(button) {

        button.addEventListener('click', async function() {

            const formationId = this.dataset.formationId;

            if (!formationId) {
                alert('Formation introuvable.');
                return;
            }

            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '⏳ Préparation du paiement...';

            try {

                const response = await fetch('pages/paiement.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    },
                    body: new URLSearchParams({
                        formation_id: formationId
                    })
                });

                const text = await response.text();

                console.log('Réponse brute :', text);

                let data;

                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Réponse non JSON :', text);
                    throw new Error(
                        'Le serveur a retourné une réponse qui n’est pas du JSON.'
                    );
                }

                console.log('Réponse JSON :', data);

                if (!data.success) {
                    alert(
                        data.message ||
                        'Impossible de préparer le paiement.'
                    );
                    this.disabled = false;
                    this.innerHTML = originalText;
                    return;
                }

                if (data.payment_url) {
                    console.log(
                        'Redirection vers FedaPay :',
                        data.payment_url
                    );
                    window.location.assign(data.payment_url);
                    return;
                }

                throw new Error(
                    'FedaPay n’a pas retourné de payment_url.'
                );

            } catch (error) {

                console.error('Erreur paiement :', error);

                alert(
                    error.message ||
                    'Une erreur est survenue lors du paiement.'
                );

                this.disabled = false;
                this.innerHTML = originalText;
            }

        });

    });

        // ============================================================
        // DEBUG
        // ============================================================

        console.log(
            '=== MES FORMATIONS ==='
        );

        console.log(
            'Utilisateur :',
            <?= json_encode(
                $user,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            ) ?>
        );

        console.log(
            'Formations :',
            <?= json_encode(
                $formations,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            ) ?>
        );

        console.log(
            'Statistiques :', {
                formations: <?= (int)$nbFormations ?>,

                enAttente: <?= (int)$nbEnAttente ?>,

                annulees: <?= (int)$nbAnnulees ?>,

                terminees: <?= (int)$nbTerminees ?>,

                progression: <?= (int)$progressTotal ?>
            }
        );

        console.log(
            '=== FIN DEBUG ==='
        );
    </script>


</body>

</html>