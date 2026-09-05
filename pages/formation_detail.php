<?php
// ============================================================
// pages/formation_detail.php
// Détail d'une formation avec supports, exercices et soumissions
// ============================================================

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// VÉRIFICATION DE LA CONNEXION
// ============================================================

if (empty($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$formationId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($formationId <= 0) {
    header('Location: ?page=mesformations');
    exit;
}

$error = '';
$success = '';

$supports = [];
$exercices = [];
$realisations = [];
$exercicesSoumis = [];
$formation = null;

// ============================================================
// CONNEXION BDD
// ============================================================

try {

    $pdo = getDB();

    // ========================================================
    // VÉRIFIER QUE L'UTILISATEUR A BIEN PAYÉ LA FORMATION
    // ========================================================

    $stmt = $pdo->prepare("
        SELECT 
            c.id AS commande_id,
            c.status AS commande_status,
            c.progression,
            f.id,
            f.titre,
            f.description,
            f.categorie,
            f.prix,
            f.duree
        FROM commandes c
        INNER JOIN formations f 
            ON f.id = c.formation_id
        WHERE c.utilisateur_id = ?
          AND c.formation_id = ?
          AND c.status = 'payee'
        LIMIT 1
    ");

    $stmt->execute([
        $userId,
        $formationId
    ]);

    $formation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$formation) {
        header('Location: ?page=mesformations');
        exit;
    }

    // ========================================================
    // RÉCUPÉRER LES SUPPORTS
    // ========================================================

    try {

        $stmt = $pdo->prepare("
            SELECT 
                id,
                formation_id,
                titre,
                type,
                fichier,
                lien_externe,
                created_at
            FROM supports
            WHERE formation_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->execute([$formationId]);

        $supports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        error_log(
            "ERREUR RECUPERATION SUPPORTS : " .
            $e->getMessage()
        );

        $supports = [];
    }

    // ========================================================
    // RÉCUPÉRER LES EXERCICES
    // ========================================================

    try {

        $stmt = $pdo->prepare("
            SELECT 
                id,
                formation_id,
                titre,
                description,
                fichier,
                date_limite,
                created_at
            FROM exercices
            WHERE formation_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->execute([$formationId]);

        $exercices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        error_log(
            "ERREUR RECUPERATION EXERCICES : " .
            $e->getMessage()
        );

        $exercices = [];
    }

    // ========================================================
    // RÉCUPÉRER LES RÉALISATIONS DE L'UTILISATEUR
    // ========================================================

    try {

        $stmt = $pdo->prepare("
            SELECT 
                r.*,
                e.titre AS exercice_titre
            FROM realisations r
            INNER JOIN exercices e 
                ON e.id = r.exercice_id
            WHERE r.utilisateur_id = ?
              AND e.formation_id = ?
            ORDER BY r.date_soumission DESC
        ");

        $stmt->execute([
            $userId,
            $formationId
        ]);

        $realisations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        error_log(
            "ERREUR RECUPERATION REALISATIONS : " .
            $e->getMessage()
        );

        $realisations = [];
    }

    // ========================================================
    // CRÉER UN TABLEAU POUR SAVOIR QUELS EXERCICES SONT SOUMIS
    // ========================================================

    foreach ($realisations as $realisation) {

        if (!empty($realisation['exercice_id'])) {

            $exercicesSoumis[
                (int) $realisation['exercice_id']
            ] = $realisation;
        }
    }

} catch (PDOException $e) {

    error_log(
        "ERREUR FORMATION DETAIL : " .
        $e->getMessage()
    );

    $error = "Une erreur est survenue lors du chargement de la formation.";
}

// ============================================================
// FONCTIONS UTILITAIRES
// ============================================================

function h($value)
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}

function safeCount($value)
{
    return is_array($value)
        ? count($value)
        : 0;
}

function getStatusBadge($statut)
{
    $badges = [
        'en_attente' => 'warning',
        'validee'    => 'success',
        'refusee'    => 'danger'
    ];

    return $badges[$statut] ?? 'secondary';
}

function getStatusLabel($statut)
{
    $labels = [
        'en_attente' => 'En attente de validation',
        'validee'    => '✅ Validée',
        'refusee'    => '❌ Refusée'
    ];

    return $labels[$statut] ?? $statut;
}

// ============================================================
// CALCUL DES STATISTIQUES
// ============================================================

$valides = 0;

foreach ($realisations as $realisation) {

    if (($realisation['statut'] ?? '') === 'validee') {
        $valides++;
    }
}

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= h($formation['titre'] ?? 'Formation') ?>
        - FEMI Fairy Finger
    </title>

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >

    <style>

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
            --shadow: 0 8px 32px rgba(0,0,0,.12);
        }

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

        /* ====================================================
           NAVBAR
        ==================================================== */

        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(250,247,244,.94);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 48px;
            height: 64px;
        }

        .logo {
            font-family: 'Cormorant Garamond', serif;
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

        .navbar-link:hover,
        .navbar-link.active {
            color: var(--rose);
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    var(--rose),
                    var(--gold)
                );

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            color: white;
            font-weight: 600;

            border: 2px solid var(--border);
        }

        /* ====================================================
           LAYOUT
        ==================================================== */

        .page-wrap {
            max-width: 1200px;
            margin: 0 auto;

            padding: 40px 32px 80px;

            display: grid;
            grid-template-columns: 1fr 320px;

            gap: 40px;
            align-items: start;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        /* ====================================================
           FORMATION HEADER
        ==================================================== */

        .formation-header {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;

            padding: 32px 32px 24px;
        }

        .badge-category {
            display: inline-block;

            padding: 4px 16px;

            border-radius: 20px;

            font-size: .72rem;
            font-weight: 500;

            text-transform: uppercase;
            letter-spacing: .08em;

            background: var(--blush);
            color: var(--rose);

            margin-bottom: 12px;
        }

        .formation-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.4rem;
            font-weight: 600;

            margin-bottom: 8px;
        }

        .formation-header p {
            color: var(--muted);
            line-height: 1.6;
        }

        .formation-meta {
            display: flex;
            gap: 20px;

            margin-top: 16px;

            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;

            font-size: .85rem;
            color: var(--muted);
        }

        /* ====================================================
           TITRES
        ==================================================== */

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 600;

            margin-bottom: 16px;
        }

        .section-title em {
            color: var(--rose);
            font-style: italic;
        }

        /* ====================================================
           SUPPORTS
        ==================================================== */

        .supports-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .support-item {
            background: var(--white);

            border: 1px solid var(--border);
            border-radius: 12px;

            padding: 16px 20px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            transition: all .2s;
        }

        .support-item:hover {
            border-color: var(--rose);
            box-shadow: var(--shadow);
        }

        .support-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .support-icon {
            font-size: 1.5rem;
        }

        .support-title {
            font-weight: 500;
        }

        .support-desc {
            font-size: .82rem;
            color: var(--muted);

            margin-top: 3px;
        }

        /* ====================================================
           BOUTONS
        ==================================================== */

        .btn-download {
            padding: 7px 16px;

            border-radius: 8px;

            background: var(--rose);
            color: white;

            border: none;

            font-weight: 500;
            font-size: .78rem;

            cursor: pointer;
            text-decoration: none;

            transition: all .2s;

            display: inline-block;
        }

        .btn-download:hover {
            background: var(--rose-dark);
            transform: translateY(-1px);
        }

        /* ====================================================
           EXERCICES
        ==================================================== */

        .exercices-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(280px, 1fr)
                );

            gap: 16px;
        }

        .exercice-card {
            background: var(--white);

            border: 1px solid var(--border);
            border-radius: 16px;

            padding: 20px;

            transition: all .2s;
        }

        .exercice-card:hover {
            border-color: var(--rose);
            box-shadow: var(--shadow);
        }

        .exercice-type {
            display: inline-block;

            padding: 2px 12px;

            border-radius: 12px;

            font-size: .65rem;
            font-weight: 600;

            text-transform: uppercase;
            letter-spacing: .05em;

            background: var(--cream);
            color: var(--muted);

            margin-bottom: 8px;
        }

        .exercice-card h4 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            font-weight: 600;

            margin-bottom: 4px;
        }

        .exercice-actions {
            display: flex;
            gap: 8px;

            margin-top: 14px;

            flex-wrap: wrap;
            align-items: center;
        }

        .btn-exercice {
            padding: 7px 14px;

            border-radius: 8px;

            font-size: .75rem;
            font-weight: 500;

            border: none;

            cursor: pointer;
            text-decoration: none;

            transition: all .2s;

            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-exercice-download {
            background: var(--cream);
            color: var(--charcoal);

            border: 1px solid var(--border);
        }

        .btn-exercice-download:hover {
            background: var(--blush);
        }

        .btn-exercice-submit {
            background: var(--rose);
            color: white;
        }

        .btn-exercice-submit:hover {
            background: var(--rose-dark);
        }

        .btn-exercice-submit.soumis {
            background: var(--green);
        }

        /* ====================================================
           RÉALISATIONS
        ==================================================== */

        .realisations-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .realisation-item {
            background: var(--white);

            border: 1px solid var(--border);
            border-radius: 12px;

            padding: 16px 20px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;
        }

        .realisation-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .realisation-title {
            font-weight: 500;
        }

        .realisation-exercice,
        .realisation-date {
            font-size: .82rem;
            color: var(--muted);
        }

        .badge-status {
            display: inline-block;

            padding: 4px 14px;

            border-radius: 20px;

            font-size: .7rem;
            font-weight: 600;

            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .badge-warning {
            background: #fef0dc;
            color: #a05c10;
        }

        .badge-success {
            background: var(--green-light);
            color: var(--green);
        }

        .badge-danger {
            background: #fde8e5;
            color: #a8200d;
        }

        .badge-secondary {
            background: #eee;
            color: #666;
        }

        /* ====================================================
           SIDEBAR
        ==================================================== */

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;

            position: sticky;
            top: 80px;
        }

        .sidebar-card {
            background: var(--white);

            border: 1px solid var(--border);
            border-radius: 16px;

            padding: 24px;
        }

        .sidebar-card h4 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            font-weight: 600;

            margin-bottom: 12px;
        }

        .progress-container {
            margin-top: 8px;
        }

        .progress-bar-bg {
            width: 100%;
            height: 8px;

            background: var(--cream);

            border-radius: 10px;

            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;

            border-radius: 10px;

            background: var(--rose);

            transition: width .6s ease;
        }

        .progress-text {
            display: flex;
            justify-content: space-between;

            font-size: .82rem;
            color: var(--muted);

            margin-top: 4px;
        }

        .sidebar-stats {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 12px;
        }

        .sidebar-stat {
            text-align: center;

            padding: 12px;

            background: var(--cream);

            border-radius: 12px;
        }

        .sidebar-stat .number {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;

            color: var(--rose);

            display: block;
        }

        .sidebar-stat .label {
            font-size: .7rem;
            color: var(--muted);

            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            color: var(--muted);

            text-decoration: none;
            font-size: .85rem;

            transition: color .2s;
        }

        .btn-back:hover {
            color: var(--rose);
        }

        /* ====================================================
           MODAL
        ==================================================== */

        .modal-overlay {
            display: none;

            position: fixed;

            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            background: rgba(0,0,0,.5);

            z-index: 1000;

            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: var(--white);

            border-radius: 24px;

            padding: 32px;

            max-width: 600px;
            width: 90%;

            max-height: 90vh;

            overflow-y: auto;

            position: relative;
        }

        .modal-close {
            position: absolute;

            top: 16px;
            right: 20px;

            font-size: 1.5rem;

            background: none;
            border: none;

            cursor: pointer;

            color: var(--muted);
        }

        .modal-content h3 {
            font-family: 'Cormorant Garamond', serif;

            font-size: 1.6rem;
            font-weight: 600;

            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;

            font-weight: 500;
            font-size: .85rem;

            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;

            padding: 10px 14px;

            border: 1px solid var(--border);

            border-radius: 8px;

            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-submit {
            padding: 10px 24px;

            border-radius: 8px;

            background: var(--rose);
            color: white;

            border: none;

            font-weight: 500;

            cursor: pointer;

            transition: all .2s;
        }

        .btn-submit:hover {
            background: var(--rose-dark);
        }

        /* ====================================================
           RESPONSIVE
        ==================================================== */

        @media (max-width: 900px) {

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

            .navbar-right {
                gap: 10px;
            }

            .navbar-link {
                display: none;
            }

            .formation-header h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {

            .exercices-grid {
                grid-template-columns: 1fr;
            }

            .support-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .realisation-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .formation-header {
                padding: 20px;
            }

            .modal-content {
                padding: 24px 20px;
            }
        }

    </style>

</head>

<body>
<!-- ============================================================
     PAGE
============================================================ -->

<div class="page-wrap">

    <!-- ========================================================
         CONTENU PRINCIPAL
    ======================================================== -->

    <main class="main-content">

        <!-- Retour -->

        <a
            href="?page=mesformations"
            class="btn-back"
        >
            ← Retour à mes formations
        </a>


        <!-- ====================================================
             FORMATION
        ==================================================== -->

        <div class="formation-header">

            <div class="badge-category">

                <?= h(
                    ucfirst(
                        $formation['categorie'] ?? 'Général'
                    )
                ) ?>

            </div>

            <h1>
                <?= h($formation['titre']) ?>
            </h1>

            <p>
                <?= h(
                    $formation['description']
                    ?? 'Aucune description disponible.'
                ) ?>
            </p>

            <div class="formation-meta">

                <span class="meta-item">
                    ⏱
                    <?= (int)(
                        $formation['duree'] ?? 0
                    ) ?>
                    heures
                </span>

                <span class="meta-item">
                    💰
                    <?= number_format(
                        $formation['prix'] ?? 0,
                        0,
                        ',',
                        ' '
                    ) ?>
                    FCFA
                </span>

                <span class="meta-item">
                    📚
                    <?= safeCount($supports) ?>
                    supports
                </span>

                <span class="meta-item">
                    📝
                    <?= safeCount($exercices) ?>
                    exercices
                </span>

            </div>

        </div>


        <!-- ====================================================
             SUPPORTS
        ==================================================== -->

        <section>

            <h2 class="section-title">
                📚 Supports de <em>cours</em>
            </h2>

            <?php if (empty($supports)): ?>

                <p style="color:var(--muted);">
                    Aucun support disponible pour cette formation.
                </p>

            <?php else: ?>

                <div class="supports-list">

                    <?php foreach ($supports as $support): ?>

                        <div class="support-item">

                            <div class="support-info">

                                <span class="support-icon">
                                    📄
                                </span>

                                <div>

                                    <div class="support-title">
                                        <?= h(
                                            $support['titre']
                                        ) ?>
                                    </div>

                                    <?php if (
                                        !empty($support['type'])
                                    ): ?>

                                        <div class="support-desc">
                                            <?= h(
                                                ucfirst(
                                                    $support['type']
                                                )
                                            ) ?>
                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <a
                                href="?page=telecharger_support&id=<?= (int)$support['id'] ?>" 
                                class="btn-download"
                            >
                                📥 Télécharger
                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <!-- ====================================================
             EXERCICES
        ==================================================== -->

        <!-- ===== EXERCICES ===== -->

        <section>

            <h2 class="section-title">
                📝 Exercices <em>pratiques</em>
            </h2>

            <?php if (empty($exercices)): ?>

                <p style="color:var(--muted);">
                    Aucun exercice disponible pour cette formation.
                </p>

            <?php else: ?>

                <div class="exercices-grid">

                    <?php foreach ($exercices as $exercice): ?>

                        <?php

                        $exerciceId =
                            (int)$exercice['id'];

                        $estSoumis =
                            isset(
                                $exercicesSoumis[
                                    $exerciceId
                                ]
                            );

                        $realisation =
                            $estSoumis
                            ? $exercicesSoumis[
                                $exerciceId
                            ]
                            : null;

                        ?>

                        <div class="exercice-card">

                            <!-- Type -->

                            <span class="exercice-type">
                                Exercice pratique
                            </span>


                            <!-- Titre -->

                            <h4>
                                <?= h(
                                    $exercice['titre']
                                ) ?>
                            </h4>


                            <!-- Description -->

                            <?php if (
                                !empty(
                                    $exercice['description']
                                )
                            ): ?>

                                <p style="
                                    color:var(--muted);
                                    font-size:.82rem;
                                    line-height:1.5;
                                    margin-top:6px;
                                ">

                                    <?= nl2br(
                                        h(
                                            $exercice[
                                                'description'
                                            ]
                                        )
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <!-- Date limite -->

                            <?php if (
                                !empty(
                                    $exercice['date_limite']
                                )
                            ): ?>

                                <div style="
                                    margin-top:10px;
                                    font-size:.75rem;
                                    color:var(--muted);
                                ">

                                    📅 Date limite :

                                    <strong>

                                        <?= date(
                                            'd/m/Y à H:i',
                                            strtotime(
                                                $exercice[
                                                    'date_limite'
                                                ]
                                            )
                                        ) ?>

                                    </strong>

                                </div>

                            <?php endif; ?>


                            <!-- ACTIONS -->

                            <div class="exercice-actions">

                                <?php if (
                                    !empty(
                                        $exercice['fichier']
                                    )
                                ): ?>

                                    <a
                                        href="?page=telecharger_exercice&id=<?= $exerciceId ?>"
                                        class="btn-exercice btn-exercice-download"
                                    >
                                        📥 Télécharger
                                    </a>

                                <?php endif; ?>


                                <!-- =================================================
                                     EXERCICE DÉJÀ SOUMIS
                                ================================================== -->

                                <?php if ($estSoumis): ?>

                                    <button
                                        type="button"
                                        class="btn-exercice btn-exercice-submit soumis"
                                        disabled
                                    >
                                        ✅ Soumis
                                    </button>


                                    <?php if ($realisation): ?>

                                        <span
                                            class="badge-status badge-<?= h(
                                                getStatusBadge(
                                                    $realisation[
                                                        'statut'
                                                    ] ?? ''
                                                )
                                            ) ?>"
                                        >

                                            <?= h(
                                                getStatusLabel(
                                                    $realisation[
                                                        'statut'
                                                    ] ?? ''
                                                )
                                            ) ?>

                                        </span>

                                    <?php endif; ?>


                                <?php else: ?>

                                    <!-- =================================================
                                         SOUMETTRE
                                    ================================================== -->

                                    <button
                                        type="button"
                                        class="btn-exercice btn-exercice-submit"
                                        data-exercice-id="<?= (int) $exerciceId ?>"
                                        data-exercice-title="<?= h($exercice['titre']) ?>"
                                        onclick="openModal(this.dataset.exerciceId, this.dataset.exerciceTitle)"
                                    >
                                        📤 Soumettre
                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <!-- ====================================================
             MES RÉALISATIONS
        ==================================================== -->

        <?php if (!empty($realisations)): ?>

            <section>

                <h2 class="section-title">
                    📤 Mes <em>réalisations</em>
                </h2>

                <div class="realisations-list">

                    <?php foreach (
                        $realisations
                        as $realisation
                    ): ?>

                        <div class="realisation-item">

                            <div class="realisation-info">

                                <span class="realisation-title">

                                    <?= h(
                                        $realisation['titre']
                                    ) ?>

                                </span>

                                <span class="realisation-exercice">

                                    Exercice :
                                    <?= h(
                                        $realisation[
                                            'exercice_titre'
                                        ]
                                    ) ?>

                                </span>

                                <span class="realisation-date">

                                    📅

                                    <?= !empty(
                                        $realisation[
                                            'date_soumission'
                                        ]
                                    )
                                        ? date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $realisation[
                                                    'date_soumission'
                                                ]
                                            )
                                        )
                                        : ''
                                    ?>

                                </span>

                                <?php if (
                                    !empty(
                                        $realisation[
                                            'commentaire_admin'
                                        ]
                                    )
                                ): ?>

                                    <span style="
                                        font-size:.82rem;
                                        color:var(--muted);
                                    ">

                                        💬

                                        <?= h(
                                            $realisation[
                                                'commentaire_admin'
                                            ]
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </div>

                            <div>

                                <span
                                    class="badge-status badge-<?= h(getStatusBadge($realisation['statut'] ?? '')) ?>"
                                >

                                    <?= h(getStatusLabel($realisation['statut'] ?? '')) ?>

                                </span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </section>

        <?php endif; ?>

    </main>


    <!-- ========================================================
         SIDEBAR
    ======================================================== -->

    <aside class="sidebar">


        <!-- PROGRESSION -->

        <div class="sidebar-card">

            <h4>
                📊 Progression
            </h4>

            <div class="progress-container">

                <div class="progress-bar-bg">

                    <div
                        class="progress-bar-fill"
                        style="width: <?= min(100, max(0, (int) ($formation['progression'] ?? 0))) ?>%;"
                    ></div>

                </div>

                <div class="progress-text">

                    <span>
                        <?= (int) ($formation['progression'] ?? 0) ?>%
                    </span>

                    <span>
                        <?= safeCount($supports) + safeCount($exercices) ?>
                        éléments
                    </span>

                </div>

            </div>

        </div>


        <!-- STATISTIQUES -->

        <div class="sidebar-card">

            <h4>
                📈 Statistiques
            </h4>

            <div class="sidebar-stats">

                <div class="sidebar-stat">

                    <span class="number">
                        <?= safeCount($supports) ?>
                    </span>

                    <span class="label">
                        Supports
                    </span>

                </div>


                <div class="sidebar-stat">

                    <span class="number">
                        <?= safeCount($exercices) ?>
                    </span>

                    <span class="label">
                        Exercices
                    </span>

                </div>


                <div class="sidebar-stat">

                    <span class="number">
                        <?= safeCount($realisations) ?>
                    </span>

                    <span class="label">
                        Soumis
                    </span>

                </div>


                <div class="sidebar-stat">

                    <span class="number">
                        <?= $valides ?>
                    </span>

                    <span class="label">
                        Validés
                    </span>

                </div>

            </div>

        </div>


        <!-- AIDE -->

        <div class="sidebar-card">

            <h4>
                💡 Besoin d'aide ?
            </h4>

            <p style="
                font-size:.85rem;
                color:var(--muted);
                margin-bottom:12px;
            ">
                Contactez votre formatrice pour toute question.
            </p>

            <a
                href="https://wa.me/2290161798094"
                class="btn-download"
                style="
                    display:inline-block;
                    text-align:center;
                    width:100%;
                "
                target="_blank"
                rel="noopener noreferrer"
            >
                📱 WhatsApp
            </a>

        </div>

    </aside>

</div>


<!-- ============================================================
     MODAL DE SOUMISSION
============================================================ -->

<div
    class="modal-overlay"
    id="submitModal"
>

    <div class="modal-content">

        <button
            type="button"
            class="modal-close"
            onclick="closeModal()"
        >
            ✕
        </button>

        <h3>
            📤 Soumettre votre réalisation
        </h3>

        <p style="
            color:var(--muted);
            margin-bottom:16px;
        ">

            Exercice :

            <strong id="modalExerciceTitre"></strong>

        </p>

        <form
            id="submitForm"
            method="POST"
            action="?page=soumettre_realisation"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="exercice_id"
                id="modalExerciceId"
            >

            <input
                type="hidden"
                name="formation_id"
                value="<?= $formationId ?>"
            >


            <div class="form-group">

                <label for="realisation_titre">
                    Titre de votre réalisation *
                </label>

                <input
                    type="text"
                    id="realisation_titre"
                    name="titre"
                    required
                    placeholder="Ex : Mon projet final"
                >

            </div>


            <div class="form-group">

                <label for="realisation_description">
                    Description
                </label>

                <textarea
                    id="realisation_description"
                    name="description"
                    placeholder="Décrivez votre travail..."
                ></textarea>

            </div>


            <div class="form-group">

                <label for="realisation_fichier">
                    Fichier (PDF, image, etc.) *
                </label>

                <input
                    type="file"
                    id="realisation_fichier"
                    name="fichier"
                    required
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                >

            </div>


            <button
                type="submit"
                class="btn-submit"
            >
                📤 Soumettre
            </button>

        </form>

    </div>

</div>


<!-- ============================================================
     JAVASCRIPT
============================================================ -->

<script>

function openModal(exerciceId, exerciceTitre)
{
    document.getElementById(
        'modalExerciceId'
    ).value = exerciceId;

    document.getElementById(
        'modalExerciceTitre'
    ).textContent = exerciceTitre;

    document.getElementById(
        'submitModal'
    ).classList.add('active');
}


function closeModal()
{
    document.getElementById(
        'submitModal'
    ).classList.remove('active');
}


// Fermer en cliquant sur le fond

document
    .getElementById('submitModal')
    .addEventListener('click', function(event)
    {
        if (event.target === this) {
            closeModal();
        }
    });


// Fermer avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});



// ============================================================
// MESSAGE APRÈS SOUMISSION
// ============================================================

<?php if (
    isset($_GET['soumission'])
    && $_GET['soumission'] === 'success'
): ?>

    alert(
        '✅ Votre réalisation a été soumise avec succès !'
    );

<?php elseif (
    isset($_GET['soumission'])
    && $_GET['soumission'] === 'error'
): ?>

    alert(
        '❌ Une erreur est survenue lors de la soumission.'
    );

<?php endif; ?>

</script>

</body>
</html>
