<?php
// ============================================================
// pages/telecharger_exercice.php
// Téléchargement sécurisé d'un exercice PDF
// ============================================================

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// VÉRIFICATION CONNEXION
// ============================================================

if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Vous devez être connecté.');
}

$userId = (int) $_SESSION['user_id'];


// ============================================================
// RÉCUPÉRER L'ID
// ============================================================

$exerciceId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($exerciceId <= 0) {
    http_response_code(400);
    exit('Exercice invalide.');
}


// ============================================================
// BDD
// ============================================================

try {

    $pdo = getDB();

    $stmt = $pdo->prepare("
        SELECT
            e.id,
            e.formation_id,
            e.titre,
            e.fichier
        FROM exercices e

        INNER JOIN commandes c
            ON c.formation_id = e.formation_id

        WHERE e.id = ?
          AND c.utilisateur_id = ?
          AND c.status = 'payee'

        LIMIT 1
    ");

    $stmt->execute([
        $exerciceId,
        $userId
    ]);

    $exercice = $stmt->fetch(PDO::FETCH_ASSOC);


    // ========================================================
    // VÉRIFIER L'ACCÈS
    // ========================================================

    if (!$exercice) {

        http_response_code(404);

        exit(
            'Exercice introuvable ou accès non autorisé.'
        );
    }


    // ========================================================
    // VÉRIFIER LE NOM DU FICHIER
    // ========================================================

    if (empty($exercice['fichier'])) {

        http_response_code(404);

        exit(
            'Aucun fichier associé à cet exercice.'
        );
    }


    // ========================================================
    // CONSTRUIRE LE CHEMIN
    // ========================================================

    $fileName = basename(
        $exercice['fichier']
    );

    $filePath = __DIR__
        . '/../uploads/exercices/'
        . $fileName;


    // ========================================================
    // VÉRIFIER LE FICHIER
    // ========================================================

    if (!is_file($filePath)) {

        error_log(
            'Fichier exercice introuvable : '
            . $filePath
        );

        http_response_code(404);

        exit(
            'Le fichier de l’exercice est introuvable.'
        );
    }


    if (!is_readable($filePath)) {

        http_response_code(403);

        exit(
            'Le fichier ne peut pas être lu.'
        );
    }


    // ========================================================
    // VÉRIFIER LA TAILLE
    // ========================================================

    $fileSize = filesize($filePath);

    if ($fileSize === false || $fileSize <= 0) {

        http_response_code(500);

        exit(
            'Le fichier est vide ou invalide.'
        );
    }


    // ========================================================
    // NOM DU FICHIER
    // ========================================================

    $extension = strtolower(
        pathinfo(
            $fileName,
            PATHINFO_EXTENSION
        )
    );

    $titre = $exercice['titre']
        ?: 'exercice';

    $safeTitle = preg_replace(
        '/[^a-zA-Z0-9_\-]/',
        '_',
        $titre
    );

    $downloadName = $safeTitle;

    if ($extension !== '') {

        $downloadName .= '.' . $extension;
    }


    // ========================================================
    // TYPE MIME
    // ========================================================

    $mimeType = 'application/octet-stream';

    if (function_exists('mime_content_type')) {

        $detectedMime = mime_content_type(
            $filePath
        );

        if (!empty($detectedMime)) {

            $mimeType = $detectedMime;
        }
    }


    // ========================================================
    // NETTOYER TOUT CE QUI POURRAIT ÊTRE ENVOYÉ AVANT LE PDF
    // ========================================================

    while (ob_get_level() > 0) {
        ob_end_clean();
    }


    // ========================================================
    // HEADERS
    // ========================================================

    header(
        'Content-Type: ' . $mimeType
    );

    header(
        'Content-Disposition: attachment; filename="' .
        $downloadName .
        '"'
    );

    header(
        'Content-Length: ' . $fileSize
    );

    header(
        'Cache-Control: private, no-store, no-cache, must-revalidate'
    );

    header(
        'Pragma: public'
    );

    header(
        'Expires: 0'
    );


    // ========================================================
    // ENVOYER LE FICHIER
    // ========================================================

    readfile($filePath);

    exit;

} catch (PDOException $e) {

    error_log(
        'ERREUR TELECHARGEMENT EXERCICE : '
        . $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Une erreur est survenue lors du téléchargement.'
    );
}