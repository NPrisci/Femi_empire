<?php

require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDB();

$message = '';
$error = '';

/*
|--------------------------------------------------------------------------
| VÉRIFICATION ADMIN
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['user_id'])) {
    header('Location: /pages/admin/index.php?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| TRAITEMENT DES FORMULAIRES
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $formAction = $_POST['action'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION DES INFORMATIONS ADMIN
    |--------------------------------------------------------------------------
    */
    if ($formAction === 'update_admin_infos') {

        $prenom = trim($_POST['prenom'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');

        if ($prenom === '' || $nom === '' || $email === '') {

            setFlash(
                'error',
                'Le prénom, le nom et l’adresse e-mail sont obligatoires.'
            );

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            setFlash(
                'error',
                'L’adresse e-mail n’est pas valide.'
            );

        } else {

            try {

                // Vérifier que l'e-mail n'est pas déjà utilisé
                $check = $pdo->prepare("
                    SELECT id
                    FROM utilisateurs
                    WHERE email = ?
                    AND id != ?
                    LIMIT 1
                ");

                $check->execute([$email, $userId]);

                if ($check->fetch()) {

                    setFlash(
                        'error',
                        'Cette adresse e-mail est déjà utilisée par un autre utilisateur.'
                    );

                } else {

                    $stmt = $pdo->prepare("
                        UPDATE utilisateurs
                        SET prenom = ?,
                            nom = ?,
                            email = ?,
                            telephone = ?
                        WHERE id = ?
                    ");

                    $stmt->execute([
                        $prenom,
                        $nom,
                        $email,
                        $telephone,
                        $userId
                    ]);

                    // Mettre à jour également les données de session
                    $_SESSION['user_prenom'] = $prenom;
                    $_SESSION['user_nom'] = $nom;
                    $_SESSION['user_email'] = $email;

                    setFlash(
                        'success',
                        'Votre profil a été mis à jour avec succès.'
                    );
                }

            } catch (PDOException $e) {

                error_log(
                    'Erreur modification profil admin : ' . $e->getMessage()
                );

                setFlash(
                    'error',
                    'Une erreur est survenue lors de la modification du profil.'
                );
            }
        }

        header('Location: parametres.php#profil');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION DU MOT DE PASSE ADMIN
    |--------------------------------------------------------------------------
    */
    if ($formAction === 'update_admin_password') {

        $current = $_POST['pwd_current'] ?? '';
        $new = $_POST['pwd_new'] ?? '';
        $confirm = $_POST['pwd_confirm'] ?? '';

        if ($current === '') {

            setFlash(
                'error',
                'Veuillez saisir votre mot de passe actuel.'
            );

        } elseif (strlen($new) < 8) {

            setFlash(
                'error',
                'Le nouveau mot de passe doit contenir au moins 8 caractères.'
            );

        } elseif ($new !== $confirm) {

            setFlash(
                'error',
                'Les deux nouveaux mots de passe ne correspondent pas.'
            );

        } else {

            try {

                $stmt = $pdo->prepare("
                    SELECT password
                    FROM utilisateurs
                    WHERE id = ?
                    LIMIT 1
                ");

                $stmt->execute([$userId]);

                $admin = $stmt->fetch();

                if (!$admin) {

                    setFlash(
                        'error',
                        'Compte administrateur introuvable.'
                    );

                } elseif (!password_verify($current, $admin['password'])) {

                    setFlash(
                        'error',
                        'Le mot de passe actuel est incorrect.'
                    );

                } else {

                    $hash = password_hash(
                        $new,
                        PASSWORD_DEFAULT
                    );

                    $stmt = $pdo->prepare("
                        UPDATE utilisateurs
                        SET password = ?
                        WHERE id = ?
                    ");

                    $stmt->execute([
                        $hash,
                        $userId
                    ]);

                    setFlash(
                        'success',
                        'Votre mot de passe a été modifié avec succès.'
                    );
                }

            } catch (PDOException $e) {

                error_log(
                    'Erreur modification mot de passe admin : ' . $e->getMessage()
                );

                setFlash(
                    'error',
                    'Une erreur est survenue lors de la modification du mot de passe.'
                );
            }
        }

        header('Location: parametres.php#securite-admin');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION DES PARAMÈTRES DU SITE
    |--------------------------------------------------------------------------
    */
    if ($formAction === 'update_site_settings') {

        $params = $_POST['params'] ?? [];
        $errors = [];
        $success = 0;

        foreach ($params as $key => $value) {

            $value = sanitize($value);

            try {

                $stmt = $pdo->prepare("
                    UPDATE parametres
                    SET valeur = ?
                    WHERE cle = ?
                ");

                $stmt->execute([
                    $value,
                    $key
                ]);

                $success++;

            } catch (PDOException $e) {

                $errors[] =
                    "Erreur pour $key : " . $e->getMessage();
            }
        }

        if ($success > 0) {
            setFlash(
                'success',
                "$success paramètre(s) mis à jour avec succès."
            );
        }

        if (!empty($errors)) {
            setFlash(
                'error',
                implode('<br>', $errors)
            );
        }

        header('Location: parametres.php#parametres-site');
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION DU PROFIL ADMIN
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id, prenom, nom, email, telephone, role, created_at
    FROM utilisateurs
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {

    session_unset();
    session_destroy();

    header('Location: /pages/admin/index.php?page=login');
    exit;
}

/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION DES PARAMÈTRES DU SITE
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT cle, valeur, description
    FROM parametres
    ORDER BY cle
");

$parametres = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| HEADER ADMIN
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/header.php';
?>


<div class="admin-content">

    <!-- =========================================================
         PROFIL ADMINISTRATEUR
         ========================================================= -->
    <div class="card" id="profil">

        <div class="card-header">
            <div>
                <h3 class="card-title">👤 Mon profil</h3>

                <span style="font-size:14px;color:var(--text-light);">
                    Modifiez vos informations personnelles et votre mot de passe
                </span>
            </div>

            <span class="badge badge-primary">
                <?= htmlspecialchars(ucfirst($admin['role'])) ?>
            </span>
        </div>

        <!-- INFORMATIONS PERSONNELLES -->
        <form method="POST" action="parametres.php#profil">

            <input
                type="hidden"
                name="action"
                value="update_admin_infos"
            >

            <div
                style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    gap:20px;
                    padding:24px;
                "
            >

                <div class="form-group">

                    <label class="form-label">
                        Prénom
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="prenom"
                        value="<?= htmlspecialchars($admin['prenom'] ?? '') ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Nom
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="nom"
                        value="<?= htmlspecialchars($admin['nom'] ?? '') ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Adresse e-mail
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        name="email"
                        value="<?= htmlspecialchars($admin['email'] ?? '') ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Téléphone
                    </label>

                    <input
                        type="tel"
                        class="form-control"
                        name="telephone"
                        value="<?= htmlspecialchars($admin['telephone'] ?? '') ?>"
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Rôle
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars(ucfirst($admin['role'])) ?>"
                        readonly
                        style="opacity:.7;cursor:not-allowed;"
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Membre depuis
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= !empty($admin['created_at'])
                            ? date('d/m/Y', strtotime($admin['created_at']))
                            : '—' ?>"
                        readonly
                        style="opacity:.7;cursor:not-allowed;"
                    >

                </div>

            </div>

            <div style="padding:0 24px 24px;">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    💾 Enregistrer mon profil
                </button>

            </div>

        </form>
    </div>


    <!-- =========================================================
         MOT DE PASSE ADMIN
         ========================================================= -->
    <div class="card" id="securite-admin">

        <div class="card-header">

            <div>

                <h3 class="card-title">
                    🔐 Sécurité
                </h3>

                <span style="font-size:14px;color:var(--text-light);">
                    Modifier votre mot de passe administrateur
                </span>

            </div>

        </div>

        <form
            method="POST"
            action="parametres.php#securite-admin"
        >

            <input
                type="hidden"
                name="action"
                value="update_admin_password"
            >

            <div
                style="
                    display:grid;
                    grid-template-columns:1fr 1fr 1fr;
                    gap:20px;
                    padding:24px;
                "
            >

                <div class="form-group">

                    <label class="form-label">
                        Mot de passe actuel
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        name="pwd_current"
                        required
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Nouveau mot de passe
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        name="pwd_new"
                        minlength="8"
                        required
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Confirmer le mot de passe
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        name="pwd_confirm"
                        minlength="8"
                        required
                    >

                </div>

            </div>

            <div style="padding:0 24px 24px;">

                <small
                    style="
                        display:block;
                        color:var(--text-light);
                        margin-bottom:12px;
                    "
                >
                    Le nouveau mot de passe doit contenir au moins
                    8 caractères.
                </small>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    🔑 Modifier mon mot de passe
                </button>

            </div>

        </form>
    </div>


    <!-- =========================================================
         PARAMÈTRES DU SITE
         ========================================================= -->
    <div class="card" id="parametres-site">

        <div class="card-header">

            <h3 class="card-title">
                ⚙️ Paramètres du site
            </h3>

            <span style="font-size:14px;color:var(--text-light);">
                Configurez les informations générales de votre site
            </span>

        </div>

        <form
            method="POST"
            action="parametres.php#parametres-site"
        >

            <input
                type="hidden"
                name="action"
                value="update_site_settings"
            >

            <div
                style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    gap:20px;
                    padding:24px;
                "
            >

                <?php foreach ($parametres as $param): ?>

                    <div class="form-group">

                        <label class="form-label">

                            <?= htmlspecialchars(
                                ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $param['cle']
                                    )
                                )
                            ) ?>

                            <?php if ($param['description']): ?>

                                <span
                                    style="
                                        font-weight:400;
                                        font-size:12px;
                                        color:var(--text-light);
                                    "
                                >
                                    (<?= htmlspecialchars($param['description']) ?>)
                                </span>

                            <?php endif; ?>

                        </label>

                        <?php if (
                            strpos($param['cle'], 'email') !== false ||
                            strpos($param['cle'], 'telephone') !== false
                        ): ?>

                            <input
                                type="text"
                                class="form-control"
                                name="params[<?= htmlspecialchars($param['cle']) ?>]"
                                value="<?= htmlspecialchars($param['valeur']) ?>"
                            >

                        <?php elseif (
                            strpos($param['cle'], 'description') !== false ||
                            strpos($param['cle'], 'adresse') !== false
                        ): ?>

                            <textarea
                                class="form-control"
                                name="params[<?= htmlspecialchars($param['cle']) ?>]"
                                rows="2"
                            ><?= htmlspecialchars($param['valeur']) ?></textarea>

                        <?php elseif ($param['cle'] === 'devise'): ?>

                            <select
                                class="form-control form-select"
                                name="params[<?= htmlspecialchars($param['cle']) ?>]"
                            >

                                <option
                                    value="€"
                                    <?= $param['valeur'] === '€' ? 'selected' : '' ?>
                                >
                                    Euro (€)
                                </option>

                                <option
                                    value="$"
                                    <?= $param['valeur'] === '$' ? 'selected' : '' ?>
                                >
                                    Dollar ($)
                                </option>

                                <option
                                    value="CFA"
                                    <?= $param['valeur'] === 'CFA' ? 'selected' : '' ?>
                                >
                                    CFA (XOF)
                                </option>

                            </select>

                        <?php elseif ($param['cle'] === 'frais_inscription'): ?>

                            <input
                                type="number"
                                class="form-control"
                                name="params[<?= htmlspecialchars($param['cle']) ?>]"
                                value="<?= htmlspecialchars($param['valeur']) ?>"
                                step="0.01"
                                min="0"
                            >

                        <?php else: ?>

                            <input
                                type="text"
                                class="form-control"
                                name="params[<?= htmlspecialchars($param['cle']) ?>]"
                                value="<?= htmlspecialchars($param['valeur']) ?>"
                            >

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

            <div style="padding:0 24px 24px;">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    💾 Enregistrer les paramètres
                </button>

            </div>

        </form>
    </div>

</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>