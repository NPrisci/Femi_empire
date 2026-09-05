<?php
// ============================================================
// pages/soumettre_realisation.php
// Soumission d'une réalisation par un utilisateur
// ============================================================

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ============================================================
// VÉRIFIER LA CONNEXION
// ============================================================

if (empty($_SESSION['user_id'])) {

    header('Location: ?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];


// ============================================================
// VÉRIFIER LA MÉTHODE
// ============================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: ?page=mesformations');
    exit;
}


// ============================================================
// RÉCUPÉRER LES DONNÉES
// ============================================================

$exerciceId = isset($_POST['exercice_id'])
    ? (int) $_POST['exercice_id']
    : 0;

$formationId = isset($_POST['formation_id'])
    ? (int) $_POST['formation_id']
    : 0;

$titre = trim(
    $_POST['titre'] ?? ''
);

$description = trim(
    $_POST['description'] ?? ''
);


// ============================================================
// VÉRIFICATIONS
// ============================================================

if ($exerciceId <= 0 || $formationId <= 0) {

    header(
        'Location: ?page=formation_detail&id=' .
        $formationId .
        '&soumission=error'
    );

    exit;
}


if ($titre === '') {

    header(
        'Location: ?page=formation_detail&id=' .
        $formationId .
        '&soumission=error'
    );

    exit;
}


// ============================================================
// VÉRIFIER LE FICHIER
// ============================================================

if (
    !isset($_FILES['fichier'])
    || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK
) {

    header(
        'Location: ?page=formation_detail&id=' .
        $formationId .
        '&soumission=error'
    );

    exit;
}


$file = $_FILES['fichier'];


// ============================================================
// TAILLE MAXIMALE
// 10 Mo
// ============================================================

$maxSize = 10 * 1024 * 1024;

if ($file['size'] > $maxSize) {

    header(
        'Location: ?page=formation_detail&id=' .
        $formationId .
        '&soumission=error'
    );

    exit;
}


// ============================================================
// EXTENSIONS AUTORISÉES
// ============================================================

$allowedExtensions = [
    'pdf',
    'doc',
    'docx',
    'jpg',
    'jpeg',
    'png',
    'zip'
];

$extension = strtolower(
    pathinfo(
        $file['name'],
        PATHINFO_EXTENSION
    )
);

if (!in_array(
    $extension,
    $allowedExtensions,
    true
)) {

    header(
        'Location: ?page=formation_detail&id=' .
        $formationId .
        '&soumission=error'
    );

    exit;
}


// ============================================================
// VÉRIFIER QUE L'EXERCICE APPARTIENT À LA FORMATION
// ============================================================

try {

    $pdo = getDB();


    // ========================================================
    // VÉRIFIER L'EXERCICE
    // ========================================================

    $stmt = $pdo->prepare("
        SELECT
            id,
            formation_id
        FROM exercices
        WHERE id = ?
          AND formation_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $exerciceId,
        $formationId
    ]);

    $exercice = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$exercice) {

        header(
            'Location: ?page=mesformations'
        );

        exit;
    }


    // ========================================================
    // VÉRIFIER QUE L'UTILISATEUR A PAYÉ LA FORMATION
    // ========================================================

    $stmt = $pdo->prepare("
        SELECT id
        FROM commandes
        WHERE utilisateur_id = ?
          AND formation_id = ?
          AND status = 'payee'
        LIMIT 1
    ");

    $stmt->execute([
        $userId,
        $formationId
    ]);

    $commande = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$commande) {

        header(
            'Location: ?page=mesformations'
        );

        exit;
    }


    // ========================================================
    // EMPÊCHER UNE DOUBLE SOUMISSION
    // ========================================================

    $stmt = $pdo->prepare("
        SELECT id
        FROM realisations
        WHERE utilisateur_id = ?
          AND exercice_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $userId,
        $exerciceId
    ]);

    $ancienneRealisation = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($ancienneRealisation) {

        header(
            'Location: ?page=formation_detail&id=' .
            $formationId .
            '&soumission=error'
        );

        exit;
    }


    // ========================================================
    // DOSSIER UPLOAD
    // ========================================================

    $uploadDir = __DIR__
        . '/../uploads/realisations/';


    // Créer le dossier s'il n'existe pas

    if (!is_dir($uploadDir)) {

        if (!mkdir(
            $uploadDir,
            0755,
            true
        )) {

            throw new Exception(
                'Impossible de créer le dossier upload.'
            );
        }
    }


    // ========================================================
    // NOM UNIQUE DU FICHIER
    // ========================================================

    $newFileName =
        bin2hex(random_bytes(8))
        . '_'
        . time()
        . '.'
        . $extension;


    $destination =
        $uploadDir . $newFileName;


    // ========================================================
    // DÉPLACER LE FICHIER
    // ========================================================

    if (!move_uploaded_file(
        $file['tmp_name'],
        $destination
    )) {

        throw new Exception(
            'Impossible de déplacer le fichier.'
        );
    }


    // ========================================================
    // ENREGISTRER EN BDD
    // ========================================================

    $stmt = $pdo->prepare("
        INSERT INTO realisations (
            utilisateur_id,
            exercice_id,
            titre,
            description,
            fichier,
            statut,
            date_soumission
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            'en_attente',
            NOW()
        )
    ");

    $stmt->execute([
        $userId,
        $exerciceId,
        $titre,
        $description,
        $newFileName
    ]);


    // ========================================================
    // SUCCÈS
    // ========================================================

    header(
        'Location: ?page=formation_detail&id=' .
        $formationId .
        '&soumission=success'
    );

    exit;


} catch (Throwable $e) {

    error_log(
        'ERREUR SOUMISSION REALISATION : '
        . $e->getMessage()
    );


    // Si le fichier a été déplacé mais que l'insertion BDD
    // échoue, supprimer le fichier.

    if (
        isset($destination)
        && is_file($destination)
    ) {

        unlink($destination);
    }


    header(
        'Location: ?page=formation_detail&id=' .
        $formationId .
        '&soumission=error'
    );

    exit;
}