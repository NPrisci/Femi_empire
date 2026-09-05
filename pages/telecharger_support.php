<?php
// ============================================================
// pages/telecharger_support.php
// Téléchargement sécurisé d'un support de formation
// ============================================================

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// VÉRIFIER LA CONNEXION
// ============================================================

if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Vous devez être connecté pour télécharger ce support.');
}

$userId = (int) $_SESSION['user_id'];


// ============================================================
// RÉCUPÉRER L'ID DU SUPPORT
// ============================================================

$supportId = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($supportId <= 0) {
    http_response_code(400);
    exit('Support invalide.');
}


// ============================================================
// CONNEXION BDD
// ============================================================

try {

    $pdo = getDB();

    // ========================================================
    // RÉCUPÉRER LE SUPPORT
    // ET VÉRIFIER QUE L'UTILISATEUR A PAYÉ LA FORMATION
    // ========================================================

    $stmt = $pdo->prepare("
        SELECT
            s.id,
            s.titre,
            s.type,
            s.fichier,
            s.lien_externe,
            s.formation_id
        FROM supports s
        INNER JOIN commandes c
            ON c.formation_id = s.formation_id
        WHERE s.id = ?
          AND c.utilisateur_id = ?
          AND c.status = 'payee'
        LIMIT 1
    ");

    $stmt->execute([
        $supportId,
        $userId
    ]);

    $support = $stmt->fetch(PDO::FETCH_ASSOC);


    // ========================================================
    // SUPPORT INTROUVABLE OU FORMATION NON PAYÉE
    // ========================================================

    if (!$support) {

        http_response_code(404);

        exit(
            'Support introuvable ou accès non autorisé.'
        );
    }


    // ========================================================
    // SI LE SUPPORT EST UN LIEN EXTERNE
    // ========================================================

    if (!empty($support['lien_externe'])) {

        $url = trim($support['lien_externe']);

        // Autoriser uniquement HTTP/HTTPS
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (
            $scheme !== 'http'
            && $scheme !== 'https'
        ) {

            http_response_code(400);

            exit(
                'Lien externe invalide.'
            );
        }

        header(
            'Location: ' . $url
        );

        exit;
    }


    // ========================================================
    // VÉRIFIER QU'UN FICHIER EXISTE
    // ========================================================

    if (empty($support['fichier'])) {

        http_response_code(404);

        exit(
            'Aucun fichier disponible pour ce support.'
        );
    }


    // ========================================================
    // CHEMIN DU FICHIER
    // ========================================================

    /*
     * IMPORTANT :
     *
     * Adapte cette partie selon l'endroit où tes fichiers
     * sont réellement enregistrés.
     *
     * Exemple :
     *
     * uploads/supports/mon-cours.pdf
     */

    $relativePath = ltrim(
        $support['fichier'],
        '/\\'
    );

    $filePath = __DIR__ . '/../' . $relativePath;


    // ========================================================
    // VÉRIFIER QUE LE FICHIER EXISTE
    // ========================================================

    if (!is_file($filePath)) {

        error_log(
            "Fichier support introuvable : " .
            $filePath
        );

        http_response_code(404);

        exit(
            'Le fichier du support est introuvable sur le serveur.'
        );
    }


    // ========================================================
    // VÉRIFIER QUE LE FICHIER EST LISIBLE
    // ========================================================

    if (!is_readable($filePath)) {

        http_response_code(403);

        exit(
            'Impossible de lire ce fichier.'
        );
    }


    // ========================================================
    // NOM DU FICHIER POUR LE TÉLÉCHARGEMENT
    // ========================================================

    $extension = pathinfo(
        $filePath,
        PATHINFO_EXTENSION
    );

    $safeTitle = preg_replace(
        '/[^a-zA-Z0-9_\-]/',
        '_',
        $support['titre'] ?? 'support'
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

        if ($detectedMime) {

            $mimeType = $detectedMime;
        }
    }


    // ========================================================
    // ENVOYER LE FICHIER
    // ========================================================

    header(
        'Content-Description: File Transfer'
    );

    header(
        'Content-Type: ' . $mimeType
    );

    header(
        'Content-Disposition: attachment; filename="' .
        $downloadName .
        '"'
    );

    header(
        'Content-Length: ' . filesize($filePath)
    );

    header(
        'Cache-Control: private, must-revalidate'
    );

    header(
        'Pragma: public'
    );

    header(
        'Expires: 0'
    );


    // ========================================================
    // ENVOYER LE CONTENU
    // ========================================================

    readfile($filePath);

    exit;


} catch (PDOException $e) {

    error_log(
        "ERREUR TELECHARGEMENT SUPPORT : " .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Une erreur est survenue lors du téléchargement.'
    );
}