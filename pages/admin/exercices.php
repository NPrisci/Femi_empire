<?php
// pages/admin/exercices.php
// Gestion des exercices

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// IMPORTANT : activer les exceptions PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ============================================================
// PARAMÈTRES
// ============================================================

$formation_id = isset($_GET['formation_id'])
    ? (int) $_GET['formation_id']
    : 0;

$action = $_GET['action'] ?? 'list';

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


// ============================================================
// TRAITEMENT AJOUT / MODIFICATION
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $post_action = $_POST['action'] ?? '';

    // Logs de debug
    error_log('========================================');
    error_log('EXERCICES - POST');
    error_log('========================================');
    error_log('POST: ' . print_r($_POST, true));
    error_log('FILES: ' . print_r($_FILES, true));
    error_log('ACTION: ' . $post_action);


    // --------------------------------------------------------
    // SAUVEGARDE
    // --------------------------------------------------------

    if ($post_action === 'save') {

        // Récupération des données
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $date_limite = trim($_POST['date_limite'] ?? '');

        $edit_id = isset($_POST['edit_id'])
            ? (int) $_POST['edit_id']
            : 0;

        $formation_id = isset($_POST['formation_id'])
            ? (int) $_POST['formation_id']
            : 0;


        error_log("Titre = {$titre}");
        error_log("Formation ID = {$formation_id}");
        error_log("Edit ID = {$edit_id}");
        error_log("Date limite = {$date_limite}");


        // ----------------------------------------------------
        // VALIDATION
        // ----------------------------------------------------

        $errors = [];


        // Titre obligatoire
        if ($titre === '') {
            $errors[] = 'Le titre est obligatoire.';
        }


        // Longueur du titre
        if (mb_strlen($titre) > 255) {
            $errors[] = 'Le titre ne peut pas dépasser 255 caractères.';
        }


        // Formation obligatoire
        if ($formation_id <= 0) {
            $errors[] = 'Veuillez sélectionner une formation.';
        }


        // Vérifier que la formation existe
        if ($formation_id > 0) {

            $stmt = $pdo->prepare(
                'SELECT id FROM formations WHERE id = ? LIMIT 1'
            );

            $stmt->execute([$formation_id]);

            $formation_exists = $stmt->fetchColumn();

            if (!$formation_exists) {
                $errors[] = 'La formation sélectionnée n\'existe pas.';
            }
        }


        // ----------------------------------------------------
        // VÉRIFICATION DE L'EXERCICE EN CAS DE MODIFICATION
        // ----------------------------------------------------

        $ancien_exercice = null;

        if ($edit_id > 0) {

            $stmt = $pdo->prepare(
                'SELECT *
                 FROM exercices
                 WHERE id = ?
                 LIMIT 1'
            );

            $stmt->execute([$edit_id]);

            $ancien_exercice = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ancien_exercice) {
                $errors[] = 'L\'exercice à modifier n\'existe pas.';
            }
        }


        // ----------------------------------------------------
        // GESTION DE LA DATE
        // ----------------------------------------------------

        $date_limite_sql = null;

        if ($date_limite !== '') {

            // Le champ HTML type="date" envoie normalement :
            // YYYY-MM-DD

            $date_obj = DateTime::createFromFormat(
                'Y-m-d',
                $date_limite
            );

            $date_errors = DateTime::getLastErrors();

            // Selon la version de PHP, getLastErrors()
            // peut retourner false
            if ($date_errors === false) {
                $date_errors = [
                    'warning_count' => 0,
                    'error_count' => 0
                ];
            }

            if (
                !$date_obj ||
                $date_errors['warning_count'] > 0 ||
                $date_errors['error_count'] > 0 ||
                $date_obj->format('Y-m-d') !== $date_limite
            ) {

                $errors[] =
                    'La date limite est invalide. Utilisez le format AAAA-MM-JJ.';

            } else {

                // DATETIME MySQL
                // On met 23:59:59 pour que la date soit valable
                // jusqu'à la fin de la journée.

                $date_limite_sql =
                    $date_obj->format('Y-m-d') . ' 23:59:59';

                error_log(
                    "Date SQL = {$date_limite_sql}"
                );
            }
        }


        // ----------------------------------------------------
        // GESTION DU FICHIER
        // ----------------------------------------------------

        $fichier_name = null;

        $nouveau_fichier = false;


        // Un fichier a-t-il été envoyé ?
        $fichier_envoye = (
            isset($_FILES['fichier']) &&
            isset($_FILES['fichier']['error']) &&
            $_FILES['fichier']['error'] !== UPLOAD_ERR_NO_FILE
        );


        if ($fichier_envoye) {

            // Vérifier l'erreur PHP
            if ($_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {

                switch ($_FILES['fichier']['error']) {

                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $errors[] =
                            'Le fichier est trop volumineux.';
                        break;

                    case UPLOAD_ERR_PARTIAL:
                        $errors[] =
                            'Le fichier n\'a été que partiellement envoyé.';
                        break;

                    default:
                        $errors[] =
                            'Une erreur est survenue lors de l\'envoi du fichier.';
                        break;
                }

            } else {

                // Extensions autorisées
                $allowed = [
                    'pdf',
                    'doc',
                    'docx',
                    'txt',
                    'zip'
                ];


                // Dossier d'upload
                $upload_dir =
                    __DIR__ . '/../../uploads/exercices';


                // Créer le dossier s'il n'existe pas
                if (!is_dir($upload_dir)) {

                    if (!mkdir($upload_dir, 0755, true)) {

                        $errors[] =
                            'Impossible de créer le dossier des exercices.';

                    }
                }


                // Vérifier que le dossier est accessible
                if (
                    empty($errors) &&
                    !is_writable($upload_dir)
                ) {

                    $errors[] =
                        'Le dossier uploads/exercices n\'est pas accessible en écriture.';
                }


                // Utiliser ta fonction uploadFile()
                if (empty($errors)) {

                    $upload_result = uploadFile(
                        $_FILES['fichier'],
                        '../../uploads/exercices',
                        $allowed
                    );


                    if (
                        isset($upload_result['success']) &&
                        $upload_result['success'] === true
                    ) {

                        $fichier_name =
                            $upload_result['filename'];

                        $nouveau_fichier = true;

                        error_log(
                            'Nouveau fichier : ' . $fichier_name
                        );

                    } else {

                        $message_upload =
                            $upload_result['message']
                            ?? 'Erreur lors de l\'upload du fichier.';

                        $errors[] = $message_upload;

                        error_log(
                            'ERREUR UPLOAD : ' . $message_upload
                        );
                    }
                }
            }
        }


        // ----------------------------------------------------
        // ENREGISTREMENT EN BASE
        // ----------------------------------------------------

        if (empty($errors)) {

            try {

                // ====================================================
                // MODIFICATION
                // ====================================================

                if ($edit_id > 0) {

                    error_log(
                        "MODIFICATION EXERCICE ID = {$edit_id}"
                    );


                    // Si un nouveau fichier est envoyé
                    if ($nouveau_fichier) {

                        // Supprimer l'ancien fichier
                        if (
                            $ancien_exercice &&
                            !empty($ancien_exercice['fichier'])
                        ) {

                            $ancien_fichier =
                                basename(
                                    $ancien_exercice['fichier']
                                );

                            $ancien_path =
                                __DIR__ .
                                '/../../uploads/exercices/' .
                                $ancien_fichier;


                            if (is_file($ancien_path)) {

                                unlink($ancien_path);

                                error_log(
                                    'Ancien fichier supprimé : ' .
                                    $ancien_path
                                );
                            }
                        }


                        // UPDATE avec fichier
                        $sql = "
                            UPDATE exercices
                            SET
                                titre = ?,
                                description = ?,
                                fichier = ?,
                                date_limite = ?
                            WHERE id = ?
                        ";

                        $stmt = $pdo->prepare($sql);

                        $stmt->execute([
                            $titre,
                            $description !== ''
                                ? $description
                                : null,
                            $fichier_name,
                            $date_limite_sql,
                            $edit_id
                        ]);

                    } else {

                        // UPDATE sans toucher au fichier
                        $sql = "
                            UPDATE exercices
                            SET
                                titre = ?,
                                description = ?,
                                date_limite = ?
                            WHERE id = ?
                        ";

                        $stmt = $pdo->prepare($sql);

                        $stmt->execute([
                            $titre,
                            $description !== ''
                                ? $description
                                : null,
                            $date_limite_sql,
                            $edit_id
                        ]);
                    }


                    error_log(
                        'Modification réussie.'
                    );


                    setFlash(
                        'success',
                        'Exercice modifié avec succès.'
                    );


                // ====================================================
                // AJOUT
                // ====================================================

                } else {

                    error_log(
                        'AJOUT EXERCICE'
                    );


                    // INSERT
                    $sql = "
                        INSERT INTO exercices
                        (
                            formation_id,
                            titre,
                            description,
                            fichier,
                            date_limite
                        )
                        VALUES
                        (
                            ?,
                            ?,
                            ?,
                            ?,
                            ?
                        )
                    ";


                    $stmt = $pdo->prepare($sql);


                    $params = [
                        $formation_id,
                        $titre,
                        $description !== ''
                            ? $description
                            : null,
                        $fichier_name,
                        $date_limite_sql
                    ];


                    error_log(
                        'PARAMETRES INSERT : ' .
                        print_r($params, true)
                    );


                    $stmt->execute($params);


                    $new_id =
                        $pdo->lastInsertId();


                    error_log(
                        'EXERCICE AJOUTÉ - ID = ' .
                        $new_id
                    );


                    setFlash(
                        'success',
                        'Exercice ajouté avec succès.'
                    );
                }


                // ------------------------------------------------
                // REDIRECTION
                // ------------------------------------------------

                header(
                    'Location: exercices.php?formation_id=' .
                    $formation_id
                );

                exit;


            } catch (PDOException $e) {

                // Erreur SQL
                error_log(
                    'PDO ERROR EXERCICES : ' .
                    $e->getMessage()
                );

                error_log(
                    'PDO CODE : ' .
                    $e->getCode()
                );


                $errors[] =
                    'Erreur base de données : ' .
                    $e->getMessage();


                setFlash(
                    'error',
                    htmlspecialchars(
                        $errors[count($errors) - 1],
                        ENT_QUOTES,
                        'UTF-8'
                    )
                );
            }
        }


        // ----------------------------------------------------
        // ERREURS
        // ----------------------------------------------------

        if (!empty($errors)) {

            error_log(
                'ERREURS EXERCICE : ' .
                implode(' | ', $errors)
            );


            setFlash(
                'error',
                implode('<br>', array_map(
                    function ($error) {
                        return htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    },
                    $errors
                ))
            );
        }
    }
}


// ============================================================
// SUPPRESSION
// ============================================================

if ($action === 'delete' && $id > 0) {

    try {

        // Récupérer l'exercice
        $stmt = $pdo->prepare(
            'SELECT *
             FROM exercices
             WHERE id = ?
             LIMIT 1'
        );

        $stmt->execute([$id]);

        $exercice = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$exercice) {

            setFlash(
                'error',
                'Exercice introuvable.'
            );

        } else {

            // -----------------------------------------------
            // Supprimer le fichier
            // -----------------------------------------------

            if (!empty($exercice['fichier'])) {

                $filename =
                    basename($exercice['fichier']);

                $file_path =
                    __DIR__ .
                    '/../../uploads/exercices/' .
                    $filename;


                if (is_file($file_path)) {

                    unlink($file_path);

                    error_log(
                        'Fichier supprimé : ' .
                        $file_path
                    );
                }
            }


            // -----------------------------------------------
            // Supprimer de la base
            // -----------------------------------------------

            $stmt = $pdo->prepare(
                'DELETE FROM exercices WHERE id = ?'
            );

            $stmt->execute([$id]);


            setFlash(
                'success',
                'Exercice supprimé avec succès.'
            );
        }


    } catch (PDOException $e) {

        error_log(
            'ERREUR SUPPRESSION EXERCICE : ' .
            $e->getMessage()
        );


        setFlash(
            'error',
            'Erreur lors de la suppression : ' .
            htmlspecialchars(
                $e->getMessage(),
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }


    // Redirection
    if ($formation_id > 0) {

        header(
            'Location: exercices.php?formation_id=' .
            $formation_id
        );

    } else {

        header(
            'Location: exercices.php'
        );
    }

    exit;
}


// ============================================================
// RÉCUPÉRATION EXERCICE POUR MODIFICATION
// ============================================================

$edit_data = null;


if ($action === 'edit' && $id > 0) {

    $stmt = $pdo->prepare(
        'SELECT *
         FROM exercices
         WHERE id = ?
         LIMIT 1'
    );

    $stmt->execute([$id]);

    $edit_data =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$edit_data) {

        setFlash(
            'error',
            'Exercice introuvable.'
        );

        header(
            'Location: exercices.php' .
            (
                $formation_id > 0
                    ? '?formation_id=' . $formation_id
                    : ''
            )
        );

        exit;
    }


    // Récupérer automatiquement la formation
    if (
        isset($edit_data['formation_id']) &&
        (int)$edit_data['formation_id'] > 0
    ) {

        $formation_id =
            (int)$edit_data['formation_id'];
    }
}


// ============================================================
// RÉCUPÉRATION DE LA FORMATION
// ============================================================

$formation = null;


if ($formation_id > 0) {

    $stmt = $pdo->prepare(
        'SELECT *
         FROM formations
         WHERE id = ?
         LIMIT 1'
    );

    $stmt->execute([$formation_id]);

    $formation =
        $stmt->fetch(PDO::FETCH_ASSOC);
}


// ============================================================
// LISTE DES EXERCICES
// ============================================================

$exercices = [];


if ($formation_id > 0) {

    $sql = "
        SELECT
            e.*,
            (
                SELECT COUNT(*)
                FROM realisations r
                WHERE r.exercice_id = e.id
            ) AS realisations_count
        FROM exercices e
        WHERE e.formation_id = ?
        ORDER BY e.created_at DESC
    ";


    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $formation_id
    ]);

    $exercices =
        $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// ============================================================
// LISTE DES FORMATIONS
// ============================================================

$stmt = $pdo->query(
    'SELECT *
     FROM formations
     ORDER BY titre ASC'
);

$formations =
    $stmt->fetchAll(PDO::FETCH_ASSOC);


// ============================================================
// MESSAGES FLASH
// ============================================================

$flash_messages = getFlash();

?>

<div class="admin-content">

    <!-- =====================================================
         MESSAGES FLASH
         ===================================================== -->

    <?php if (!empty($flash_messages)): ?>

        <?php foreach ($flash_messages as $message): ?>

            <div class="flash-message flash-<?= htmlspecialchars(
                $message['type'] ?? 'info',
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

                <?= $message['message'] ?? '' ?>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>


    <!-- =====================================================
         FORMULAIRE AJOUT / MODIFICATION
         ===================================================== -->

    <?php if ($action === 'add' || $action === 'edit'): ?>

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <?= $action === 'edit'
                        ? 'Modifier'
                        : 'Ajouter'
                    ?>

                    un exercice

                </h3>


                <a
                    href="exercices.php<?= $formation_id > 0
                        ? '?formation_id=' . $formation_id
                        : ''
                    ?>"
                    class="btn btn-sm btn-warning"
                >
                    ← Retour
                </a>

            </div>


            <!-- Message d'erreur éventuel -->

            <?php if (!empty($errors)): ?>

                <div class="flash-message flash-error">

                    <?php foreach ($errors as $error): ?>

                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?><br>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- FORMULAIRE -->

            <form
                method="POST"
                action="exercices.php?action=save"
                enctype="multipart/form-data"
            >

                <!-- Action -->

                <input
                    type="hidden"
                    name="action"
                    value="save"
                >


                <!-- ID exercice -->

                <input
                    type="hidden"
                    name="edit_id"
                    value="<?= (int)($edit_data['id'] ?? 0) ?>"
                >


                <!-- ID formation -->

                <input
                    type="hidden"
                    name="formation_id"
                    value="<?= (int)$formation_id ?>"
                >


                <!-- =================================================
                     FORMATION
                     ================================================= -->

                <div class="form-group">

                    <label class="form-label">
                        Formation *
                    </label>


                    <select
                        name="formation_id_select"
                        class="form-control form-select"
                        disabled
                    >

                        <?php if ($formation): ?>

                            <option value="<?= (int)$formation['id'] ?>">

                                <?= htmlspecialchars(
                                    $formation['titre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>

                        <?php else: ?>

                            <option>
                                Formation invalide
                            </option>

                        <?php endif; ?>

                    </select>


                    <?php if (!$formation): ?>

                        <small style="color:#dc2626;">
                            Aucune formation valide n'est sélectionnée.
                        </small>

                    <?php endif; ?>

                </div>


                <!-- =================================================
                     TITRE
                     ================================================= -->

                <div class="form-group">

                    <label class="form-label">
                        Titre *
                    </label>


                    <input
                        type="text"
                        class="form-control"
                        name="titre"
                        required
                        maxlength="255"
                        value="<?= htmlspecialchars(
                            $edit_data['titre'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <!-- =================================================
                     DESCRIPTION
                     ================================================= -->

                <div class="form-group">

                    <label class="form-label">
                        Description
                    </label>


                    <textarea
                        class="form-control"
                        name="description"
                        rows="4"
                    ><?= htmlspecialchars(
                        $edit_data['description'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>


                <!-- =================================================
                     DATE + FICHIER
                     ================================================= -->

                <div class="form-row">

                    <!-- DATE -->

                    <div class="form-group">

                        <label class="form-label">
                            Date limite
                        </label>


                        <input
                            type="date"
                            class="form-control"
                            name="date_limite"
                            value="<?=
                                !empty($edit_data['date_limite'])
                                    ? date(
                                        'Y-m-d',
                                        strtotime(
                                            $edit_data['date_limite']
                                        )
                                    )
                                    : ''
                            ?>"
                        >


                        <small
                            style="
                                color:var(--text-light);
                                font-size:11px;
                            "
                        >
                            Format : AAAA-MM-JJ
                        </small>

                    </div>


                    <!-- FICHIER -->

                    <div class="form-group">

                        <label class="form-label">

                            Fichier

                        </label>


                        <input
                            type="file"
                            class="form-control"
                            name="fichier"
                            accept=".pdf,.doc,.docx,.txt,.zip"
                        >


                        <small
                            style="
                                color:var(--text-light);
                                font-size:11px;
                            "
                        >
                            PDF, DOC, DOCX, TXT ou ZIP
                        </small>


                        <?php if (
                            !empty($edit_data['fichier'])
                        ): ?>

                            <div
                                style="
                                    margin-top:8px;
                                    font-size:13px;
                                    color:var(--text-light);
                                "
                            >

                                Fichier actuel :

                                <a
                                    href="../../uploads/exercices/<?= htmlspecialchars(
                                        basename(
                                            $edit_data['fichier']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    target="_blank"
                                    rel="noopener"
                                    style="color:var(--primary);"
                                >

                                    <?= htmlspecialchars(
                                        basename(
                                            $edit_data['fichier']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </a>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- =================================================
                     BOUTONS
                     ================================================= -->

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <?= $action === 'edit'
                        ? '💾 Mettre à jour'
                        : '➕ Ajouter'
                    ?>

                </button>


                <a
                    href="exercices.php<?= $formation_id > 0
                        ? '?formation_id=' . $formation_id
                        : ''
                    ?>"
                    class="btn btn-warning"
                >
                    Annuler
                </a>

            </form>

        </div>


    <?php else: ?>


        <!-- =====================================================
             LISTE DES EXERCICES
             ===================================================== -->

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    Exercices

                    <?php if ($formation): ?>

                        de

                        <span
                            style="color:var(--primary);"
                        >

                            <?= htmlspecialchars(
                                $formation['titre'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>

                    <?php endif; ?>

                </h3>


                <div
                    style="
                        display:flex;
                        gap:8px;
                        flex-wrap:wrap;
                    "
                >

                    <!-- Sélection formation -->

                    <select
                        class="form-control form-select"
                        style="
                            width:auto;
                            padding:6px 12px;
                        "
                        onchange="
                            if(this.value) {
                                window.location.href =
                                'exercices.php?formation_id='
                                + this.value;
                            }
                        "
                    >

                        <option value="">
                            Changer de formation
                        </option>


                        <?php foreach ($formations as $f): ?>

                            <option
                                value="<?= (int)$f['id'] ?>"
                                <?= $formation_id == $f['id']
                                    ? 'selected'
                                    : ''
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $f['titre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>


                    <!-- Ajouter -->

                    <?php if ($formation_id > 0 && $formation): ?>

                        <a
                            href="exercices.php?action=add&formation_id=<?= (int)$formation_id ?>"
                            class="btn btn-primary"
                        >
                            ➕ Ajouter
                        </a>

                    <?php endif; ?>

                </div>

            </div>


            <!-- =================================================
                 AUCUNE FORMATION
                 ================================================= -->

            <?php if ($formation_id <= 0): ?>

                <p
                    style="color:var(--text-light);"
                >
                    Veuillez sélectionner une formation
                    pour voir ses exercices.
                </p>


            <!-- =================================================
                 FORMATION INEXISTANTE
                 ================================================= -->

            <?php elseif (!$formation): ?>

                <div class="flash-message flash-error">

                    La formation sélectionnée
                    n'existe pas.

                </div>


            <!-- =================================================
                 AUCUN EXERCICE
                 ================================================= -->

            <?php elseif (empty($exercices)): ?>

                <p
                    style="color:var(--text-light);"
                >

                    Aucun exercice pour cette formation.

                    <a
                        href="exercices.php?action=add&formation_id=<?= (int)$formation_id ?>"
                        style="color:var(--primary);"
                    >
                        Ajouter un exercice
                    </a>

                </p>


            <!-- =================================================
                 TABLEAU
                 ================================================= -->

            <?php else: ?>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Titre
                                </th>

                                <th>
                                    Description
                                </th>

                                <th>
                                    Date limite
                                </th>

                                <th>
                                    Fichier
                                </th>

                                <th>
                                    Réalisations
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($exercices as $ex): ?>

                                <tr>

                                    <!-- TITRE -->

                                    <td>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $ex['titre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- DESCRIPTION -->

                                    <td>

                                        <?php

                                        $description_affichage =
                                            $ex['description'] ?? '';

                                        if (
                                            mb_strlen(
                                                $description_affichage
                                            ) > 100
                                        ) {

                                            $description_affichage =
                                                mb_substr(
                                                    $description_affichage,
                                                    0,
                                                    100
                                                ) . '...';
                                        }

                                        ?>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $description_affichage,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                        ) ?>

                                    </td>


                                    <!-- DATE LIMITE -->

                                    <td>

                                        <?php if (
                                            !empty(
                                                $ex['date_limite']
                                            )
                                        ): ?>

                                            <?= date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $ex['date_limite']
                                                )
                                            ) ?>

                                        <?php else: ?>

                                            <span
                                                style="
                                                    color:var(--text-light);
                                                    font-size:12px;
                                                "
                                            >
                                                Pas de limite
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- FICHIER -->

                                    <td>

                                        <?php if (
                                            !empty(
                                                $ex['fichier']
                                            )
                                        ): ?>

                                            <a
                                                href="../../uploads/exercices/<?= htmlspecialchars(
                                                    basename(
                                                        $ex['fichier']
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                target="_blank"
                                                rel="noopener"
                                                class="btn btn-sm btn-info"
                                                style="
                                                    background:#3b82f6;
                                                    color:#fff;
                                                "
                                            >
                                                📎 Voir
                                            </a>

                                        <?php else: ?>

                                            <span
                                                style="
                                                    color:var(--text-light);
                                                    font-size:12px;
                                                "
                                            >
                                                Aucun
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- REALISATIONS -->

                                    <td>

                                        <a
                                            href="realisations.php?exercice_id=<?= (int)$ex['id'] ?>"
                                            class="btn btn-sm btn-success"
                                        >

                                            <?= (int)(
                                                $ex[
                                                    'realisations_count'
                                                ] ?? 0
                                            ) ?>

                                            réalisations

                                        </a>

                                    </td>


                                    <!-- ACTIONS -->

                                    <td>

                                        <a
                                            href="exercices.php?action=edit&id=<?= (int)$ex['id'] ?>&formation_id=<?= (int)$formation_id ?>"
                                            class="btn btn-sm btn-primary"
                                            title="Modifier"
                                        >
                                            ✏️
                                        </a>


                                        <a
                                            href="exercices.php?action=delete&id=<?= (int)$ex['id'] ?>&formation_id=<?= (int)$formation_id ?>"
                                            class="btn btn-sm btn-danger delete-btn"
                                            title="Supprimer"
                                            onclick="
                                                return confirm(
                                                    'Êtes-vous sûr de vouloir supprimer cet exercice ?'
                                                );
                                            "
                                        >
                                            🗑️
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>


<?php

require_once __DIR__ . '/includes/footer.php';

?>
