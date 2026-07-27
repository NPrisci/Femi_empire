<!-- Breadcrumb Navigation -->
<?php
// Définir les breadcrumbs en fonction de la page actuelle
$breadcrumbs = [
    'home' => [
        ['label' => 'Accueil', 'url' => './home']
    ],
    'introduction' => [
        ['label' => 'Accueil', 'url' => './home'],
        ['label' => 'Introduction', 'url' => null]
    ],
    'requirements' => [
        ['label' => 'Accueil', 'url' => './home'],
        ['label' => 'Configuration requise', 'url' => null]
    ],
    'user-guide' => [
        ['label' => 'Accueil', 'url' => './home'],
        ['label' => 'Guide d\'utilisation', 'url' => null]
    ],
    'api' => [
        ['label' => 'Accueil', 'url' => './home'],
        ['label' => 'Documentation API', 'url' => null]
    ],
    'faq' => [
        ['label' => 'Accueil', 'url' => './home'],
        ['label' => 'FAQ', 'url' => null]
    ],
    'changelog' => [
        ['label' => 'Accueil', 'url' => './home'],
        ['label' => 'Changelog', 'url' => null]
    ],
    'support' => [
        ['label' => 'Accueil', 'url' => './home'],
        ['label' => 'Aide & Support', 'url' => null]
    ]
];

// Récupérer la page actuelle
$current_page = isset($page) ? $page : 'home';
$current_breadcrumbs = isset($breadcrumbs[$current_page]) ? $breadcrumbs[$current_page] : $breadcrumbs['home'];
?>

