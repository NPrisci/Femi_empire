<?php
ob_start();
session_start();


$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : '';

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once 'data.php';
$pageStartTime = microtime(true);

// Vérification de l'authentification pour toutes les pages sauf login et certaines exceptions
$public_pages = [''];
$page = $_GET['page'] ?? 'home';

// Si l'utilisateur n'est pas connecté et que la page n'est pas publique
if (!isset($_SESSION['user_id']) && !in_array($page, $public_pages)) {
    // Rediriger vers la page de connexion
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ?page=login');
    exit;
}

$default_page = 'home';
$page = $_GET['page'] ?? $default_page;

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

?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from themewagon.github.io/adminhmd/html/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 21 May 2026 13:11:56 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->

<?php include __DIR__ . '/include/head.php'; ?>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <?php include __DIR__ . '/include/sidebar.php'; ?>
    

    <div class="admin-main">
      <?php include __DIR__ . '/include/header.php'; ?>
      
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
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

      <footer class="admin-footer">
        
        <?php include __DIR__ . '/include/footer.php'; ?>
      </footer>
    </div>
  </div>

  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>

<!-- Mirrored from themewagon.github.io/adminhmd/html/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 21 May 2026 13:12:13 GMT -->
</html>
