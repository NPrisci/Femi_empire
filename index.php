<?php
ob_start();
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : '';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once 'data.php';

$pageStartTime = microtime(true);


$default_page = 'home';
$page = $_GET['page'] ?? $default_page;

/*
|--------------------------------------------------------------------------
| TRAITEMENT AJAX / POST PAIEMENT
|--------------------------------------------------------------------------
*/

if ($page === 'paiement' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/pages/paiement.php';
    exit;
}

$pagesSansFooter = [
    'dashboard',
    'profile',
    'certificats',
    'mesformations',
    'test'
];

// Sécuriser le paramètre de page
$page = preg_replace('/[^a-z0-9_\-]/', '', strtolower($page));

if (!array_key_exists($page, $pages_allowed)) {
    header("HTTP/1.0 404 Not Found");
    $page = '404';
}

$page_title = isset($pages[$page]) ? $pages[$page] : 'Femi';

// Le <head> commun (meta, CSS global, etc.)
include __DIR__ . '/include/head.php';

// Le header du site — toujours affiché, y compris pour le dashboard
include __DIR__ . '/include/header.php';
?>

<!-- Zone principale : sidebar + contenu pour le dashboard,
     contenu seul pour les autres pages -->
<main>
    <div>
        <?php
        $page_file = $pages_allowed[$page];

        if (file_exists(__DIR__ . '/' . $page_file)) {
            include __DIR__ . '/' . $page_file;
        } else {
            include __DIR__ . '/pages/404.php';
        }

        ?>
    </div>
</main>

<?php

if (!in_array($page, $pagesSansFooter)) {
    include __DIR__ . '/include/footer.php';
}
include __DIR__ . '/include/script.php';
?>
<?php include __DIR__ . '/include/bouton.php'; ?>
</body>

</html>
<?php ob_end_flush(); ?>