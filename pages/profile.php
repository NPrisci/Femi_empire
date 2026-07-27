<?php
// profile.php - Mon profil

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

// ================================================
// TRAITEMENT DES FORMULAIRES
// ================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // --- Mise à jour des infos personnelles ---
    if ($_POST['action'] === 'update_infos') {
        $prenom    = trim($_POST['prenom'] ?? '');
        $nom       = trim($_POST['nom'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');

        if ($prenom === '' || $nom === '' || $email === '') {
            $error = 'Prénom, nom et e-mail sont obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse e-mail invalide.';
        } else {
            try {
                $pdo = getDB();
                $check = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND id != ?');
                $check->execute([$email, $userId]);
                if ($check->fetch()) {
                    $error = 'Cette adresse e-mail est déjà utilisée.';
                } else {
                    $stmt = $pdo->prepare('
                        UPDATE utilisateurs
                        SET prenom = ?, nom = ?, email = ?, telephone = ?
                        WHERE id = ?
                    ');
                    $stmt->execute([$prenom, $nom, $email, $telephone, $userId]);
                    
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_prenom'] = $prenom;
                    $_SESSION['user_nom'] = $nom;
                    
                    $success = 'Informations mises à jour avec succès !';
                }
            } catch (PDOException $e) {
                $error = 'Erreur base de données : ' . $e->getMessage();
            }
        }
    }

    // --- Changement de mot de passe ---
    if ($_POST['action'] === 'update_password') {
        $current  = $_POST['pwd_current'] ?? '';
        $new      = $_POST['pwd_new'] ?? '';
        $confirm  = $_POST['pwd_confirm'] ?? '';

        if (strlen($new) < 8) {
            $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        } elseif ($new !== $confirm) {
            $error = 'Les deux nouveaux mots de passe ne correspondent pas.';
        } else {
            try {
                $pdo = getDB();
                $stmt = $pdo->prepare('SELECT password FROM utilisateurs WHERE id = ?');
                $stmt->execute([$userId]);
                $row = $stmt->fetch();

                if (!$row || !password_verify($current, $row['password'])) {
                    $error = 'Mot de passe actuel incorrect.';
                } else {
                    $hash = password_hash($new, PASSWORD_BCRYPT);
                    $pdo->prepare('UPDATE utilisateurs SET password = ? WHERE id = ?')
                        ->execute([$hash, $userId]);
                    $success = 'Mot de passe mis à jour avec succès !';
                }
            } catch (PDOException $e) {
                $error = 'Erreur base de données : ' . $e->getMessage();
            }
        }
    }
}

// ================================================
// RÉCUPÉRATION DES DONNÉES
// ================================================

try {
    $pdo = getDB();
    
    // --- Infos utilisateur ---
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

    // --- Récupérer les formations payées de l'utilisateur ---
    $formations = [];
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
            c.date_creation as date_commande
        FROM commandes c
        INNER JOIN formations f ON f.id = c.formation_id
        WHERE c.utilisateur_id = ? 
        AND c.status = "payee"
        ORDER BY c.date_creation DESC
    ');
    $stmt->execute([$userId]);
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques
    $nbFormations = count($formations);
    $progressTotal = $nbFormations > 0
        ? (int) round(array_sum(array_column($formations, 'progression')) / $nbFormations)
        : 0;

} catch (PDOException $e) {
    error_log("ERREUR CONNEXION: " . $e->getMessage());
    die('Erreur connexion : ' . $e->getMessage());
}

// ================================================
// FONCTIONS UTILITAIRES
// ================================================

function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function initiales(string $prenom, string $nom): string {
    return strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}

function formatDate(string $date): string {
    if (empty($date)) return '—';
    $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
             'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $ts = strtotime($date);
    return $mois[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEMI Fairy Finger — Mon Profil</title>
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
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            min-height: 100vh;
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

        /* ===== SECTION CARD ===== */
        .section-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            animation: fadeUp .5s ease both;
        }

        .section-card:nth-child(2) { animation-delay: .08s; }
        .section-card:nth-child(3) { animation-delay: .16s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 28px;
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

        /* ===== FORM ===== */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 24px 28px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.span-2 {
            grid-column: span 2;
        }

        .form-label {
            font-size: .76rem;
            font-weight: 500;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .form-input {
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: .88rem;
            color: var(--charcoal);
            transition: all .2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--rose);
            background: white;
            box-shadow: 0 0 0 3px rgba(201, 135, 122, .12);
        }

        .form-input[readonly] {
            cursor: default;
            opacity: .75;
        }

        .btn-save {
            background: var(--charcoal);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 28px;
            font-family: 'DM Sans', sans-serif;
            font-size: .85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
            margin-top: 4px;
        }

        .btn-save:hover {
            background: var(--rose);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(201, 135, 122, .35);
        }

        .form-actions {
            padding: 0 28px 24px;
        }

        /* ===== ALERTS ===== */
        .alert {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: .85rem;
            margin: 0 28px 4px;
        }

        .alert-success {
            background: var(--green-light);
            color: var(--green);
            border: 1px solid #a8e6c5;
        }

        .alert-error {
            background: #fde8e5;
            color: #a8200d;
            border: 1px solid #f5c0ba;
        }

        /* ===== PASSWORD ===== */
        .pwd-section {
            padding: 24px 28px;
        }

        .pwd-tip {
            font-size: .8rem;
            color: var(--muted);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .input-wrap {
            position: relative;
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            opacity: .5;
            transition: opacity .2s;
        }

        .eye-btn:hover {
            opacity: 1;
        }

        /* ===== INFO READONLY ===== */
        .info-readonly {
            padding: 24px 28px;
        }

        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-key {
            font-size: .76rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 500;
        }

        .info-val {
            font-size: .88rem;
            color: var(--charcoal);
            font-weight: 500;
        }

        .role-badge {
            background: linear-gradient(135deg, #fde8e5, #fdf3e3);
            color: var(--rose);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.span-2 {
                grid-column: span 1;
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
                        <span class="stat-val"><?= $nbFormations ?></span>
                        <span class="stat-lbl">Formations</span>
                    </div>
                    <div class="stat">
                        <span class="stat-val"><?= $progressTotal ?>%</span>
                        <span class="stat-lbl">Progression</span>
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
                <a href="?page=profile" class="side-nav-item active">
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

            <?php if ($success): ?>
                <div class="alert alert-success"><?= h($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= h($error) ?></div>
            <?php endif; ?>

            <!-- ── Informations personnelles ── -->
            <div class="section-card" id="infos">
                <div class="section-header">
                    <div class="section-title">Informations <em>personnelles</em></div>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_infos">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="prenom">Prénom</label>
                            <input class="form-input" type="text" id="prenom" name="prenom"
                                value="<?= h($user['prenom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom</label>
                            <input class="form-input" type="text" id="nom" name="nom"
                                value="<?= h($user['nom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Adresse e-mail</label>
                            <input class="form-input" type="email" id="email" name="email"
                                value="<?= h($user['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="telephone">Téléphone</label>
                            <input class="form-input" type="tel" id="telephone" name="telephone"
                                value="<?= h($user['telephone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="info-readonly" style="padding-top:0">
                        <div class="info-row">
                            <span class="info-key">Rôle</span>
                            <span class="role-badge"><?= h(ucfirst($user['role'])) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Membre depuis</span>
                            <span class="info-val"><?= h(formatDate($user['created_at'])) ?></span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save">Enregistrer les modifications →</button>
                    </div>
                </form>
            </div>

            <!-- ── Sécurité ── -->
            <div class="section-card" id="securite">
                <div class="section-header">
                    <div class="section-title">Sécurité & <em>mot de passe</em></div>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <div class="pwd-section">
                        <p class="pwd-tip">Choisissez un mot de passe d'au moins 8 caractères avec majuscules, chiffres et symboles.</p>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Mot de passe actuel</label>
                                <div class="input-wrap">
                                    <input class="form-input" type="password" name="pwd_current" id="pwd1" placeholder="••••••••" required>
                                    <button type="button" class="eye-btn" onclick="togglePwd('pwd1',this)">👁</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nouveau mot de passe</label>
                                <div class="input-wrap">
                                    <input class="form-input" type="password" name="pwd_new" id="pwd2" placeholder="••••••••" required>
                                    <button type="button" class="eye-btn" onclick="togglePwd('pwd2',this)">👁</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirmer le nouveau mot de passe</label>
                                <div class="input-wrap">
                                    <input class="form-input" type="password" name="pwd_confirm" id="pwd3" placeholder="••••••••" required>
                                    <button type="button" class="eye-btn" onclick="togglePwd('pwd3',this)">👁</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions" style="padding:0; margin-top:16px">
                            <button type="submit" class="btn-save">Mettre à jour le mot de passe →</button>
                        </div>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script>
        function togglePwd(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.style.opacity = input.type === 'text' ? '1' : '.5';
        }

        console.log('=== 🐛 PROFIL ===');
        console.log('👤 Utilisateur:', <?= json_encode($user) ?>);
        console.log('📚 Formations:', <?= json_encode($formations) ?>);
        console.log('📊 Statistiques:', {
            nbFormations: <?= json_encode($nbFormations) ?>,
            progressTotal: <?= json_encode($progressTotal) ?>
        });
        console.log('=== FIN DEBUG ===');
    </script>

</body>
</html>