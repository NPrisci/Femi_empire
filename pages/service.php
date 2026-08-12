<?php
// ============================================================================
// SERVICE.PHP - NAIL ART BY LILY / SALON DE BEAUTÉ
// Page autonome, complète et optimisée avec HTML5, CSS3 responsive et PHP.
// ============================================================================

$salonName = "Nail Art by Lily";
$salonTagline = "L'Art de la Beauté à portée de main";
$salonPhone = "+33 1 42 68 00 00";
$salonEmail = "contact@nailartbylily.fr";
$salonAddress = "12 Rue de la Beauté, 75008 Paris";
$salonHours = "Lun - Sam : 09h00 - 19h00";

// Gestion des soumissions de formulaires PHP (Newsletter & Réservation)
$newsletterMsg = "";
$bookingMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'subscribe') {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $newsletterMsg = "Merci ! Votre e-mail (" . htmlspecialchars($email) . ") a bien été inscrit à nos offres privées.";
        } else {
            $newsletterMsg = "Veuillez saisir une adresse e-mail valide.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'booking') {
        $clientName = htmlspecialchars($_POST['client_name'] ?? 'Client(e)');
        $serviceName = htmlspecialchars($_POST['service_name'] ?? 'Prestation');
        $bookingDate = htmlspecialchars($_POST['booking_date'] ?? date('Y-m-d'));
        $bookingTime = htmlspecialchars($_POST['booking_time'] ?? '14:00');
        $bookingMsg = "Merci $clientName ! Votre réservation pour « $serviceName » le $bookingDate à $bookingTime a bien été enregistrée.";
    }
}

// Catégories de prestations
$categories = [
    'all' => 'Toutes les Prestations',
    'manucure' => 'Manucure',
    'pedicure' => 'Pédicure',
    'onglerie' => 'Onglerie & Gel X',
    'soins' => 'Soins Spécifiques'
];

// Liste des Services
$services = [
    [
        'id' => 1,
        'category' => 'manucure',
        'title' => 'Manucure Signature',
        'duration' => '45',
        'unit' => 'MIN',
        'price' => 35,
        'desc' => 'Offrez à vos mains un soin complet avec limage de précision, soin des cuticules à l\'huile précieuse d\'argan, massage hydratant et pose de vernis semi-permanent.',
        'features' => ['Limage & Polissage sur-mesure', 'Soin cuticules à l\'huile bio', 'Modelage relaxant des mains', 'Pose vernis semi-permanent (14J+)'],
        'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=700&q=80',
        'badge' => 'Incontournable'
    ],
    [
        'id' => 2,
        'category' => 'pedicure',
        'title' => 'Pédicure Spa Relaxante',
        'duration' => '60',
        'unit' => 'MIN',
        'price' => 45,
        'desc' => 'Un moment de détente absolue pour vos pieds comprenant un bain bouillonnant aux sels marins aromatiques, une exfoliation aux cristaux et un modelage apaisant.',
        'features' => ['Bain bouillonnant aux huiles essentielles', 'Exfoliation douce aux cristaux de sel', 'Soin réparateur des callosités', 'Pose de vernis brillante'],
        'image' => 'https://images.unsplash.com/photo-1519014816548-bf5fe059798b?auto=format&fit=crop&w=700&q=80',
        'badge' => 'Bien-être'
    ],
    [
        'id' => 3,
        'category' => 'onglerie',
        'title' => 'Pose & Extension Gel X',
        'duration' => '90',
        'unit' => 'MIN',
        'price' => 65,
        'desc' => 'Rallongement et création sur-mesure avec la technique américaine Gel X. Légèreté, tenue optimale de 3 à 4 semaines et rendu naturel sans agresser l\'ongle.',
        'features' => ['Capsules souples 100% Gel', 'Absence d\'odeur & respect de la plaque', 'Forme & Longueur au choix', 'Finition semi-permanente incluse'],
        'image' => 'https://images.unsplash.com/photo-1632345031435-8727f6897d53?auto=format&fit=crop&w=700&q=80',
        'badge' => 'Tendance'
    ],
    [
        'id' => 4,
        'category' => 'onglerie',
        'title' => 'Nail Art Sur-Mesure',
        'duration' => '60',
        'unit' => 'MIN',
        'price' => 25,
        'desc' => 'Expression artistique personnalisée sur vos ongles : French réinventée, motifs fin peints à la main, effets chrome, babyboomer ou incrustation de strass.',
        'features' => ['Dessins peints à la main', 'Effet Chrome, Glazed & Babyboomer', 'Strass Swarovski & Feuilles d\'or', 'Designs exclusifs illimités'],
        'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=700&q=80',
        'badge' => 'Création'
    ],
    [
        'id' => 5,
        'category' => 'soins',
        'title' => 'Soin Régénérant Cuticules',
        'duration' => '30',
        'unit' => 'MIN',
        'price' => 25,
        'desc' => 'Un traitement concentré réparateur pour fortifier les ongles fragilisés, assouplir les cuticules desséchées et redonner une brillance naturelle intense.',
        'features' => ['Bain tiède aux huiles botaniques', 'Masque fortifiant à la kératine', 'Repousse douce des cuticules', 'Sérum protecteur bio'],
        'image' => 'https://images.unsplash.com/photo-1599847995571-c8c97616940d?auto=format&fit=crop&w=700&q=80',
        'badge' => 'Soin Bio'
    ],
    [
        'id' => 6,
        'category' => 'pedicure',
        'title' => 'Beauté des Pieds Semi-Permanent',
        'duration' => '50',
        'unit' => 'MIN',
        'price' => 40,
        'desc' => 'Sublimez vos ongles de pieds avec une préparation minutieuse et la pose d\'un vernis semi-permanent ultra-résistant pour une tenue jusqu\'à 4 semaines.',
        'features' => ['Préparation approfondie de l\'ongle', 'Séchage immédiat sous lampe LED', 'Haute résistance à l\'eau & frottements', 'Brillance miroir longue durée'],
        'image' => 'https://images.unsplash.com/photo-1507652313519-d4e9174996dd?auto=format&fit=crop&w=700&q=80',
        'badge' => 'Longue Tenue'
    ]
];

// FAQs
$faqs = [
    [
        'q' => 'Puis-je choisir la couleur et la finition de mon vernis ?',
        'a' => 'Absolument ! Notre bar à vernis dispose de plus de 150 nuances exclusives (marques professionnelles OPI, Essie, Manucurist). Notre équipe vous guide pour trouver la teinte parfaite selon la saison et votre style.'
    ],
    [
        'q' => 'Combien de temps dure une pose de Gel X ?',
        'a' => 'Une pose complète de Gel X nécessite entre 1h15 et 2h selon la complexité du Nail Art. Sa tenue est garantie entre 3 et 4 semaines sans altération ni décollement.'
    ],
    [
        'q' => 'Proposez-vous des soins adaptés aux hommes ?',
        'a' => 'Oui, tout à fait ! Nous proposons des manucures et pédicures mixtes axées sur la propreté de l\'ongle, l\'exfoliation et l\'hydratation profonde, sans pose de vernis coloré.'
    ],
    [
        'q' => 'Comment entretenir mon vernis semi-permanent à la maison ?',
        'a' => 'Appliquez une goutte d\'huile pour cuticules chaque soir pour garder la peau souple. Évitez les produits nettoyants décapants sans gants de protection.'
    ],
    [
        'q' => 'Quelles sont les modalités d\'annulation de rendez-vous ?',
        'a' => 'Vous pouvez annuler ou modifier votre réservation sans frais jusqu\'à 24h avant votre rendez-vous directement par téléphone ou via notre formulaire de contact.'
    ]
];

// Plans tarifaires
$tarifs = [
    [
        'title' => 'Offre Essentielle',
        'price' => 35,
        'period' => '/ Par Soin',
        'badge' => 'Classique',
        'popular' => false,
        'features' => [
            'Manucure Soin & Limage',
            'Pose Vernis Semi-Permanent',
            'Soin des Cuticules à l\'huile bio',
            'Modelage court des mains'
        ]
    ],
    [
        'title' => 'Offre Prestige',
        'price' => 65,
        'period' => '/ Par Soin',
        'badge' => 'Le plus populaire',
        'popular' => true,
        'features' => [
            'Pose Gel X (extensions naturelles)',
            'Nail Art Personnalisé (2 ongles)',
            'Spa des Pieds Complet avec gommage',
            'Modelage relaxant Mains & Pieds'
        ]
    ],
    [
        'title' => 'Offre VIP Deluxe',
        'price' => 95,
        'period' => '/ Par Soin',
        'badge' => 'Expérience Ultime',
        'popular' => false,
        'features' => [
            'Rituel Mains & Pieds combiné',
            'Nail Art Illimité (Chrome, 3D, Strass)',
            'Soin Paraffine chaude nourrissant',
            'Boisson artisanale & Gourmandise offertes'
        ]
    ]
];
?>
    <style>
        /* ============================================================
           CSS UNIFIÉ ET OPTIMISÉ POUR LA PAGE SERVICE
           ============================================================ */

        :root {
            --primary: #E2836A;
            --primary-hover: #C26A52;
            --primary-light: #F5956A;
            --primary-pale: #FFF0E6;
            --gold: #C49B6C;
            --gold-light: #E8D5B8;
            --dark: #1C1208;
            --dark-soft: rgba(28, 18, 8, 0.06);
            --cream: #FDF8F3;
            --cream-alt: #F8F3ED;
            --warm-gray: #8A7060;
            --white: #FFFFFF;
            --border: rgba(28, 18, 8, 0.1);
            --shadow-sm: 0 4px 15px rgba(28, 18, 8, 0.05);
            --shadow-md: 0 10px 30px rgba(28, 18, 8, 0.08);
            --shadow-lg: 0 20px 50px rgba(28, 18, 8, 0.12);
            --radius-sm: 8px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* RESET & BASE */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--cream);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
        }

        ul {
            list-style: none;
        }

        img {
            max-width: 100%;
            display: block;
            height: auto;
        }

        button, input, select, textarea {
            font-family: inherit;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* TYPOGRAPHIE & TITRES DE SECTION */
        .section-title {
            text-align: center;
            margin-bottom: 56px;
        }

        .section-title h4 {
            font-size: 12px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 12px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .section-title h4::before,
        .section-title h4::after {
            content: '';
            width: 30px;
            height: 1px;
            background: var(--primary);
            opacity: 0.6;
        }

        .section-title h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(32px, 4.5vw, 56px);
            font-weight: 700;
            color: var(--dark);
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .section-title h1 em {
            color: var(--primary);
            font-style: italic;
            font-weight: 600;
        }

        .section-title p {
            font-size: 15px;
            color: var(--warm-gray);
            max-width: 600px;
            margin: 12px auto 0;
            line-height: 1.7;
        }

        /* NOTIFICATIONS FLASH */
        .flash-notification {
            background: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
            padding: 16px 24px;
            border-radius: var(--radius-sm);
            margin: 20px auto;
            max-width: 800px;
            text-align: center;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            animation: fadeIn 0.4s ease-out;
        }

        /* BARRE DE NAVIGATION EN TÊTE */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(28, 18, 8, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: var(--transition);
        }

        .header-top-bar {
            background: #120A04;
            padding: 8px 0;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.65);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .header-top-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-top-info {
            display: flex;
            gap: 24px;
        }

        .header-top-info span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .header-main-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }

        .brand-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo span {
            color: var(--primary);
            font-style: italic;
        }

        .desktop-nav-menu {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .desktop-nav-menu li a {
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
        }

        .desktop-nav-menu li a:hover,
        .desktop-nav-menu li.active a {
            color: var(--white);
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-reserve-nav {
            background: var(--primary);
            color: var(--white);
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(226, 131, 106, 0.3);
        }

        .btn-reserve-nav:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 24px;
            cursor: pointer;
        }

        /* MENU MOBILE DRAWER */
        .mobile-nav-drawer {
            display: none;
            background: var(--dark);
            border-bottom: 2px solid var(--primary);
            padding: 20px;
        }

        .mobile-nav-drawer.open {
            display: block;
        }

        .mobile-nav-drawer ul li a {
            display: block;
            padding: 12px 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* HERO / BREADCRUMB SECTION */
        .hero-banner {
            position: relative;
            min-height: 55vh;
            background: linear-gradient(135deg, rgba(28, 18, 8, 0.88) 0%, rgba(45, 26, 14, 0.75) 100%), 
                        url('https://images.unsplash.com/photo-1519014816548-bf5fe059798b?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 0 60px;
            overflow: hidden;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, transparent 0%, rgba(28, 18, 8, 0.4) 100%);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-eyebrow {
            font-size: 12px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--gold-light);
            margin-bottom: 16px;
            font-weight: 500;
        }

        .hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(38px, 6vw, 68px);
            font-weight: 700;
            color: var(--white);
            line-height: 1.1;
            margin-bottom: 20px;
        }

        .hero-title span {
            color: var(--primary);
            font-style: italic;
        }

        .hero-subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
            line-height: 1.7;
            margin-bottom: 32px;
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
        }

        .breadcrumb-list {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
        }

        .breadcrumb-list a {
            color: var(--primary-light);
        }

        .breadcrumb-list a:hover {
            color: var(--white);
        }

        /* FILTRE PAR CATÉGORIE */
        .filter-section {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 24px 0;
            /* position: sticky; */
            top: 76px;
            z-index: 900;
            box-shadow: var(--shadow-sm);
        }

        .filter-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 24px;
            border: 1.5px solid var(--border);
            border-radius: 50px;
            background: transparent;
            font-size: 13px;
            font-weight: 500;
            color: var(--warm-gray);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(226, 131, 106, 0.3);
        }

        /* SECTION SERVICES (PRESTATIONS) */
        .services-section {
            padding: 90px 0;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 32px;
        }

        .service-card {
            background: var(--white);
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(226, 131, 106, 0.3);
        }

        .service-card-img-wrap {
            position: relative;
            height: 240px;
            overflow: hidden;
            background: var(--cream-alt);
        }

        .service-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .service-card:hover .service-card-img-wrap img {
            transform: scale(1.08);
        }

        .service-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: var(--primary);
            color: var(--white);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .service-duration-badge {
            position: absolute;
            bottom: 16px;
            right: 16px;
            background: var(--dark);
            color: var(--gold-light);
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .service-duration-badge strong {
            font-size: 16px;
            line-height: 1;
        }

        .service-duration-badge span {
            font-size: 9px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            opacity: 0.8;
        }

        .service-card-body {
            padding: 28px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .service-card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .service-card-desc {
            font-size: 14px;
            color: var(--warm-gray);
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .service-feature-list {
            margin-bottom: 24px;
            padding-top: 16px;
            border-top: 1px dashed var(--border);
        }

        .service-feature-list li {
            font-size: 13px;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .service-feature-list li::before {
            content: '✓';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: var(--primary-pale);
            color: var(--primary);
            border-radius: 50%;
            font-size: 11px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .service-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        .service-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }

        .btn-book-service {
            padding: 10px 20px;
            background: var(--dark);
            color: var(--white);
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-book-service:hover {
            background: var(--primary);
            box-shadow: 0 4px 15px rgba(226, 131, 106, 0.4);
        }

        /* FAQ SECTION */
        .faqs-section {
            padding: 100px 0;
            background: var(--white);
        }

        .faqs-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .faqs-media-wrap {
            position: relative;
        }

        .faqs-media-wrap img {
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
        }

        .faqs-media-badge {
    position: absolute;
    bottom: -20px;
    right: -20px;
    background: var(--primary);
    color: var(--white);
    padding: 24px;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    max-width: 220px;
    z-index: 10;
}

/* Version mobile */
@media (max-width: 768px) {
    .faqs-media-badge {
        padding: 12px;
        max-width: 150px;
        bottom: 10px;
        right: 30px;
        font-size: 0.85rem;
    }
}

        .faqs-accordion {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .faq-item {
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--cream);
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-item.active {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: var(--shadow-sm);
        }

        .faq-header {
            padding: 20px 24px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
        }

        .faq-icon {
            font-size: 20px;
            color: var(--primary);
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        .faq-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.3s ease;
        }

        .faq-item.active .faq-body {
            max-height: 250px;
            padding: 0 24px 20px;
        }

        .faq-body p {
            font-size: 14px;
            color: var(--warm-gray);
            line-height: 1.7;
        }

        /* MARQUEE BAND */
        .marquee-section {
            background: var(--primary);
            padding: 20px 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-track {
            display: inline-flex;
            animation: marquee 25s linear infinite;
        }

        .marquee-item {
            display: inline-flex;
            align-items: center;
            gap: 24px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 600;
            color: var(--white);
            padding: 0 30px;
        }

        .marquee-item::after {
            content: '✦';
            font-size: 16px;
            color: rgba(255, 255, 255, 0.6);
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* PRICING SECTION */
        .pricing-section {
            padding: 100px 0;
            background: var(--cream-alt);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
        }

        .pricing-card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 40px 32px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            position: relative;
            transition: var(--transition);
        }

        .pricing-card.popular {
            border: 2px solid var(--primary);
            box-shadow: var(--shadow-md);
            transform: scale(1.03);
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .pricing-badge {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: var(--white);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 4px 18px;
            border-radius: 50px;
            letter-spacing: 1px;
        }

        .pricing-card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--warm-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            text-align: center;
        }

        .pricing-card-price {
            text-align: center;
            margin-bottom: 28px;
        }

        .pricing-card-price strong {
            font-family: 'Cormorant Garamond', serif;
            font-size: 56px;
            font-weight: 700;
            color: var(--dark);
            line-height: 1;
        }

        .pricing-card-price span {
            font-size: 14px;
            color: var(--warm-gray);
        }

        .pricing-feature-list {
            margin-bottom: 36px;
            flex: 1;
        }

        .pricing-feature-list li {
            padding: 12px 0;
            border-bottom: 1px solid var(--dark-soft);
            font-size: 14px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pricing-feature-list li::before {
            content: '✦';
            color: var(--primary);
        }

        .btn-pricing-cta {
            width: 100%;
            padding: 14px;
            background: var(--dark);
            color: var(--white);
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .pricing-card.popular .btn-pricing-cta {
            background: var(--primary);
        }

        .btn-pricing-cta:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* NEWSLETTER SECTION */
        .subscribe-section {
            padding: 80px 0;
            background: var(--dark);
            color: var(--white);
        }

        .subscribe-box {
            background: linear-gradient(135deg, rgba(226, 131, 106, 0.15) 0%, rgba(28, 18, 8, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .subscribe-text h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .subscribe-text p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .subscribe-form {
            display: flex;
            gap: 12px;
        }

        .subscribe-form input {
            flex: 1;
            padding: 16px 20px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: var(--white);
            font-size: 14px;
            outline: none;
        }

        .subscribe-form input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .subscribe-form button {
            padding: 16px 28px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .subscribe-form button:hover {
            background: var(--primary-hover);
        }

        /* FOOTER */
        .site-footer {
            background: #120A04;
            color: rgba(255, 255, 255, 0.65);
            padding: 70px 0 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        .footer-col h4 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px;
            color: var(--white);
            margin-bottom: 20px;
        }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a:hover {
            color: var(--primary-light);
        }

        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            font-size: 13px;
        }

        /* MODAL DE RÉSERVATION */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: var(--white);
            border-radius: var(--radius-md);
            max-width: 540px;
            width: 100%;
            padding: 36px;
            position: relative;
            box-shadow: var(--shadow-lg);
            animation: modalUp 0.3s ease-out;
        }

        @keyframes modalUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--warm-gray);
        }

        .modal-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--warm-gray);
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: 4px;
            font-size: 14px;
            background: var(--cream);
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary);
            background: var(--white);
        }

        .btn-submit-modal {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-submit-modal:hover {
            background: var(--primary-hover);
        }

        /* RESPONSIVE DESIGN METICULEUX */
        @media (max-width: 991px) {
            .desktop-nav-menu { display: none; }
            .mobile-toggle { display: block; }
            .faqs-layout { grid-template-columns: 1fr; gap: 40px; }
            .subscribe-box { grid-template-columns: 1fr; padding: 40px 24px; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 30px; }
        }

        @media (max-width: 600px) {
            .services-grid { grid-template-columns: 1fr; }
            .header-top-info span:last-child { display: none; }
            .footer-grid { grid-template-columns: 1fr; }
            .subscribe-form { flex-direction: column; }
            .filter-inner { gap: 8px; }
            .filter-btn { padding: 8px 16px; font-size: 12px; }
        }
    </style>

    <!-- NOTIFICATIONS FLASH DU SERVEUR -->
    <?php if (!empty($newsletterMsg)): ?>
        <div class="flash-notification"><?= $newsletterMsg ?></div>
    <?php endif; ?>

    <?php if (!empty($bookingMsg)): ?>
        <div class="flash-notification"><?= $bookingMsg ?></div>
    <?php endif; ?>

    <!-- HERO BREADCRUMB -->
    <section class="hero-banner">
        <div class="hero-content">
            <div class="hero-eyebrow">NAIL ART & SOINS D'EXCEPTION</div>
            <h1 class="hero-title">L'Art de la Beauté <span>sur-mesure</span></h1>
            <p class="hero-subtitle">
                Offrez à vos mains et vos pieds une parenthèse de bien-être absolu. 
                Des soins méticuleux, des créations artistiques uniques et une tenue irréprochable.
            </p>
            <div class="breadcrumb-list">
                <a href="#">Accueil</a>
                <span>/</span>
                <span>Nos Services & Prestations</span>
            </div>
        </div>
    </section>

    <!-- FILTRES INTERACTIFS -->
    <section class="filter-section" id="prestations">
        <div class="container filter-inner">
            <?php foreach ($categories as $key => $label): ?>
                <button 
                    class="filter-btn <?= $key === 'all' ? 'active' : '' ?>" 
                    onclick="filterServices('<?= $key ?>', this)">
                    <?= htmlspecialchars($label) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- LISTE DES SERVICES -->
    <main class="services-section">
        <div class="container">
            <div class="section-title">
                <h4>Nos Prestations</h4>
                <h1>Des soins d'exception pour <em>sublimer</em> votre beauté</h1>
                <p>Chaque rituel est réalisé avec des produits rigoureusement sélectionnés pour préserver la santé et l'éclat de vos ongles.</p>
            </div>

            <div class="services-grid" id="servicesContainer">
                <?php foreach ($services as $service): ?>
                    <article class="service-card" data-category="<?= htmlspecialchars($service['category']) ?>">
                        <div class="service-card-img-wrap">
                            <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['title']) ?>" loading="lazy">
                            <span class="service-badge"><?= htmlspecialchars($service['badge']) ?></span>
                            <div class="service-duration-badge">
                                <strong><?= htmlspecialchars($service['duration']) ?></strong>
                                <span><?= htmlspecialchars($service['unit']) ?></span>
                            </div>
                        </div>
                        <div class="service-card-body">
                            <h3 class="service-card-title"><?= htmlspecialchars($service['title']) ?></h3>
                            <p class="service-card-desc"><?= htmlspecialchars($service['desc']) ?></p>
                            
                            <ul class="service-feature-list">
                                <?php foreach ($service['features'] as $feature): ?>
                                    <li><?= htmlspecialchars($feature) ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="service-card-footer">
                                <div class="service-price"><?= $service['price'] ?> FCFA</div>
                                <button class="btn-book-service" onclick="openBookingModal('<?= htmlspecialchars($service['title']) ?>')">Réserver</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- BANDEROLE DE DÉFILEMENT (MARQUEE) -->
    <section class="marquee-section">
        <div class="marquee-track">
            <div class="marquee-item">Nail Art by Lily</div>
            <div class="marquee-item">Manucure Signature</div>
            <div class="marquee-item">Pose Gel X Américaine</div>
            <div class="marquee-item">Pédicure Spa Relaxante</div>
            <div class="marquee-item">Nail Art Sur-Mesure</div>
            <div class="marquee-item">Vernis Semi-Permanent</div>
            <!-- Duplication pour boucle infinie -->
            <div class="marquee-item">Nail Art by Lily</div>
            <div class="marquee-item">Manucure Signature</div>
            <div class="marquee-item">Pose Gel X Américaine</div>
            <div class="marquee-item">Pédicure Spa Relaxante</div>
            <div class="marquee-item">Nail Art Sur-Mesure</div>
            <div class="marquee-item">Vernis Semi-Permanent</div>
        </div>
    </section>

    <!-- ACCORDION FAQ -->
    <section class="faqs-section" id="faq">
        <div class="container">
            <div class="faqs-layout">
                <div class="faqs-media-wrap">
                    <div class="faqs-thumb">
                        <img src="assets/img/home-2/question.jpg" alt="Image FAQ beauté">
                    </div>
                    <!-- <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80" alt="Soin de beauté et Nail Art" loading="lazy"> -->
                    <div class="faqs-media-badge">
                        <strong style="font-size: 24px; font-family: 'Cormorant Garamond'; display: block;">100%</strong>
                        <span style="font-size: 13px;">Satisfaction et produits certifiés de qualité supérieure</span>
                    </div>
                </div>

                <div>
                    <div class="section-title" style="text-align: left; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
                        <div>
                            <h4>Questions Fréquentes</h4>
                            <h1 style="font-size: 38px;">On répond à <em>toutes</em> vos questions</h1>
                        </div>
                        <button onclick="toggleAllFaqs()" id="btnToggleAllFaqs" style="background: rgba(226,131,106,0.1); border: 1px solid var(--primary); color: var(--primary); padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer;">
                            Tout réduire
                        </button>
                    </div>

                    <div class="faqs-accordion">
                        <?php foreach ($faqs as $index => $faq): ?>
                            <div class="faq-item">
                                <div class="faq-header" onclick="toggleFaq(this)">
                                    <span><?= htmlspecialchars($faq['q']) ?></span>
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-body">
                                    <p><?= htmlspecialchars($faq['a']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TARIFS ET FORMULES -->
    <section class="pricing-section" id="tarifs">
        <div class="container">
            <div class="section-title">
                <h4>Nos Tarifs</h4>
                <h1>Des prix adaptés à toutes vos <em>envies</em></h1>
                <p>Choisissez la formule qui correspond le mieux à vos besoins de beauté au quotidien.</p>
            </div>

            <div class="pricing-grid">
                <?php foreach ($tarifs as $plan): ?>
                    <div class="pricing-card <?= $plan['popular'] ? 'popular' : '' ?>">
                        <?php if ($plan['popular']): ?>
                            <span class="pricing-badge"><?= htmlspecialchars($plan['badge']) ?></span>
                        <?php endif; ?>
                        
                        <h3 class="pricing-card-title"><?= htmlspecialchars($plan['title']) ?></h3>
                        <div class="pricing-card-price">
                            <strong><?= $plan['price'] ?>FCFA</strong>
                            <span><?= htmlspecialchars($plan['period']) ?></span>
                        </div>

                        <ul class="pricing-feature-list">
                            <?php foreach ($plan['features'] as $feat): ?>
                                <li><?= htmlspecialchars($feat) ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <button class="btn-pricing-cta" onclick="openBookingModal('<?= htmlspecialchars($plan['title']) ?>')">
                            Choisir cette formule
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- MODAL DE RESERVATION EN LIGNE -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-card">
            <button class="modal-close" onclick="closeBookingModal()">&times;</button>
            <h2 class="modal-title">Réserver un Soin</h2>
            <p style="font-size: 14px; color: var(--warm-gray); margin-bottom: 24px;">Remplissez ce formulaire pour valider votre demande de rendez-vous instantanée.</p>

            <form method="POST" action="">
                <input type="hidden" name="action" value="booking">
                
                <div class="form-group">
                    <label for="modalService">Prestation choisie</label>
                    <select name="service_name" id="modalService" required>
                        <?php foreach ($services as $srv): ?>
                            <option value="<?= htmlspecialchars($srv['title']) ?>"><?= htmlspecialchars($srv['title']) ?> (<?= $srv['price'] ?>FCFA)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="clientName">Nom & Prénom</label>
                    <input type="text" id="clientName" name="client_name" placeholder="Ex: Marie Dupont" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="bookingDate">Date souhaitée</label>
                        <input type="date" id="bookingDate" name="booking_date" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="bookingTime">Heure</label>
                        <select name="booking_time" id="bookingTime" required>
                            <option value="10:00">10h00</option>
                            <option value="11:30">11h30</option>
                            <option value="14:00" selected>14h00</option>
                            <option value="15:30">15h30</option>
                            <option value="17:00">17h00</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit-modal">Confirmer ma réservation</button>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT INTÉGRÉ POUR L'INTERACTIVITÉ -->
    <script>
        // FILTRAGE DES SERVICES
        function filterServices(category, button) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const cards = document.querySelectorAll('.service-card');
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // ACCORDEON FAQ
        function toggleFaq(headerElement) {
            const currentItem = headerElement.parentElement;
            currentItem.classList.toggle('active');
            updateToggleAllButtonText();
        }

        function toggleAllFaqs() {
            const allItems = document.querySelectorAll('.faq-item');
            const btn = document.getElementById('btnToggleAllFaqs');
            const anyClosed = Array.from(allItems).some(item => !item.classList.contains('active'));

            allItems.forEach(item => {
                if (anyClosed) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });

            if (btn) {
                btn.textContent = anyClosed ? 'Tout réduire' : 'Tout développer';
            }
        }

        function updateToggleAllButtonText() {
            const allItems = document.querySelectorAll('.faq-item');
            const btn = document.getElementById('btnToggleAllFaqs');
            const allActive = Array.from(allItems).every(item => item.classList.contains('active'));
            if (btn) {
                btn.textContent = allActive ? 'Tout réduire' : 'Tout développer';
            }
        }

        // NAVIGATION MOBILE
        function toggleMobileMenu() {
            const drawer = document.getElementById('mobileDrawer');
            drawer.classList.toggle('open');
        }

        // MODAL DE RÉSERVATION
        function openBookingModal(serviceName = '') {
            const modal = document.getElementById('bookingModal');
            const select = document.getElementById('modalService');
            
            if (serviceName && select) {
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value.includes(serviceName) || select.options[i].text.includes(serviceName)) {
                        select.selectedIndex = i;
                        break;
                    }
                }
            }
            modal.classList.add('active');
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.remove('active');
        }

        // Fermeture de la modal en cliquant à l'extérieur
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('bookingModal');
            if (e.target === modal) {
                closeBookingModal();
            }
        });
    </script>