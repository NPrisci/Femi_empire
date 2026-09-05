<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Header compact fixe -->
<div class="header-compact">

    <!-- Top header -->
    <div class="top-headar-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="top-info">
                        <ul>
                            <li><i class="bi bi-envelope"></i>youremail@gmail.com</li>
                            <li><i class="bi bi-alarm"></i>Sat - Sun (9.00am - 5pm)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="top-social-icon">
                        <ul>
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                            <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main header compact -->
    <div class="main-header-compact">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-lg-2 col-md-3 col-6">
                    <a href="?page=home" class="logo-compact">
                        <img src="assets/img/home-1/log1.png" alt="" height="35">
                    </a>
                </div>

                <!-- Menu - caché sur mobile -->
                <div class="col-lg-7 col-xl-6 d-none d-lg-block">
                    <ul class="nav-menu-compact">
                        <li><a href="?page=about">About</a></li>
                        <li><a href="?page=service">Service</a></li>
                        <li><a href="?page=formation">Formation</a></li>
                    </ul>
                </div>

                <!-- User et contact -->
                <div class=" ms-auto colonne3">
                    <div class="header-actions-compact">
                        <!-- User menu compact -->
                        <div class="user-menu-compact">
                            <button class="user-btn-compact" id="userBtn" type="button">
                                <svg viewBox="0 0 32 32" width="18" height="18">
                                    <circle cx="16" cy="10" r="6" />
                                    <path d="M4 30c0-6.627 5.373-12 12-12s12 5.373 12 12H4z" />
                                </svg>
                                <span class="user-name d-none d-md-inline">
                                    <?= isset($_SESSION['user_id']) ? ($_SESSION['user_nom'] ?? 'Compte') : 'Compte' ?>
                                </span>
                            </button>

                            <div class="user-dropdown-compact" id="userDropdown">
                                <?php if (!isset($_SESSION['user_id'])): ?>
                                    <a class="dropdown-item" href="?page=login">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4" />
                                            <polyline points="10 17 15 12 10 7" />
                                            <line x1="15" y1="12" x2="3" y2="12" />
                                        </svg>
                                        Se connecter
                                    </a>
                                    <a class="dropdown-item" href="?page=register">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                            <circle cx="8.5" cy="7" r="4" />
                                            <line x1="20" y1="8" x2="20" y2="14" />
                                            <line x1="23" y1="11" x2="17" y2="11" />
                                        </svg>
                                        Créer un compte
                                    </a>
                                <?php else: ?>
                                    <div class="dropdown-user-info">
                                        <strong><?= $_SESSION['user_nom'] ?></strong>
                                        <small><?= $_SESSION['user_email'] ?></small>
                                        <small><?= $_SESSION['user_role'] ?></small>
                                    </div>
                                    <a class="dropdown-item" href="<?= (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'pages/admin/index.php' : '?page=dashboard' ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="3" width="7" height="7" />
                                            <rect x="14" y="3" width="7" height="7" />
                                            <rect x="14" y="14" width="7" height="7" />
                                            <rect x="3" y="14" width="7" height="7" />
                                        </svg>
                                        Dashboard
                                    </a>
                                    <a class="dropdown-item" href="?page=profile">
                                        <svg viewBox="0 0 32 32" fill="currentColor" width="16" height="16">
                                            <circle cx="16" cy="10" r="6" />
                                            <path d="M4 30c0-6.627 5.373-12 12-12s12 5.373 12 12H4z" />
                                        </svg>
                                        Profil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item danger" href="?page=logout" onclick="event.preventDefault(); logoutUser();">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                                            <polyline points="16 17 21 12 16 7" />
                                            <line x1="21" y1="12" x2="9" y2="12" />
                                        </svg>
                                        Déconnexion
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bouton contact -->
                        <a href="#" class="btn-contact-compact d-none d-sm-inline-flex">
                            Contact
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <!-- Menu hamburger pour mobile -->
                        <button class="mobile-menu-toggle d-lg-none" id="mobileMenuToggle" type="button">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu mobile -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <div class="mobile-menu-content">
            <button class="mobile-menu-close" id="mobileMenuClose" type="button">&times;</button>
            <ul class="mobile-nav-menu">
                <li><a href="?page=about">About</a></li>
                <li><a href="?page=service">Service</a></li>
                <li><a href="?page=formation">Formation</a></li>
                <li><a href="?page=client">Client</a></li>
            </ul>
            <div class="mobile-menu-footer">
                <a href="#" class="btn-contact-compact mobile-contact">
                    Contact
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
       HEADER COMPACT - STYLES COMPLETS
       ============================================ */
    .colonne3 {
        margin-left: auto;
        flex: 0 0 auto;
        max-width: none;
        width: auto;
    }

    .main-header-compact .container-fluid {
        width: 100%;
        padding: 0 20px;
    }

    .header-actions-compact {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }

    /* --- Reset et base --- */
    .header-compact {
        position: fixed;
        top: 0;
        right: 0;
        width: 100%;
        z-index: 1000;
        background: #ffffff;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        font-size: 14px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
        }

        to {
            transform: translateY(0);
        }
    }

    /* --- Top header --- */
    .top-headar-area {
        background: #0A132E;
        padding: 6px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .top-headar-area .row {
        align-items: center;
    }

    .top-info ul,
    .top-social-icon ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 15px;
    }

    .top-info ul {
        justify-content: flex-start;
    }

    .top-social-icon ul {
        justify-content: flex-end;
    }

    .top-info ul li {
        color: rgba(255, 255, 255, 0.7);
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .top-info ul li i {
        font-size: 13px;
        color: var(--primary-color, #8B5CF6);
    }

    .top-social-icon ul li a {
        color: rgba(255, 255, 255, 0.6);
        transition: 0.3s;
        display: inline-block;
        font-size: 14px;
    }

    .top-social-icon ul li a:hover {
        color: var(--primary-color, #8B5CF6);
        transform: translateY(-2px);
    }

    /* --- Main header --- */
    .main-header-compact {
        padding: 8px 0;
        background: #ffffff;
    }

    .main-header-compact .container-fluid {
        padding: 0 20px;
    }

    /* --- Logo --- */
    .logo-compact {
        display: inline-block;
    }

    .logo-compact img {
        height: 35px;
        width: auto;
        transition: 0.3s;
    }

    /* --- Navigation desktop --- */
    .nav-menu-compact {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .nav-menu-compact li {
        display: inline-block;
    }

    .nav-menu-compact li a {
        display: inline-block;
        padding: 8px 16px;
        color: #1a1a2e;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: 0.3s;
        border-radius: 6px;
        position: relative;
    }

    .nav-menu-compact li a::after {
        content: '';
        position: absolute;
        bottom: 2px;
        left: 50%;
        width: 0;
        height: 2px;
        background: var(--primary-color, #8B5CF6);
        transition: 0.3s;
        transform: translateX(-50%);
        border-radius: 2px;
    }

    .nav-menu-compact li a:hover {
        color: var(--primary-color, #8B5CF6);
    }

    .nav-menu-compact li a:hover::after {
        width: 60%;
    }

    /* --- Actions header --- */
    .header-actions-compact {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }

    /* --- User menu --- */
    .user-menu-compact {
        position: relative;
        display: inline-block;
    }

    .user-btn-compact {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8f7fc;
        border: 1px solid #e8e7ed;
        border-radius: 30px;
        padding: 6px 14px 6px 10px;
        cursor: pointer;
        transition: 0.3s;
        font-size: 13px;
        color: #1a1a2e;
        font-weight: 500;
        background: transparent;
        position: relative;
        z-index: 10;
    }

    .user-btn-compact:hover {
        background: var(--primary-color, #8B5CF6);
        border-color: var(--primary-color, #8B5CF6);
        color: #fff;
    }

    .user-btn-compact svg {
        fill: currentColor;
        stroke: currentColor;
        flex-shrink: 0;
    }

    .user-btn-compact .user-name {
        white-space: nowrap;
    }

    /* --- Dropdown user --- */
    .user-dropdown-compact {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        min-width: 220px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
        padding: 8px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px) scale(0.95);
        transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: top right;
        border: 1px solid #f0f0f5;
        z-index: 1000;
        pointer-events: none;
    }

    .user-dropdown-compact.open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .user-dropdown-compact .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 18px;
        color: #1a1a2e;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: 0.2s;
        border: none;
        background: transparent;
        width: 100%;
        cursor: pointer;
    }

    .user-dropdown-compact .dropdown-item svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        opacity: 0.6;
    }

    .user-dropdown-compact .dropdown-item:hover {
        background: #f8f7fc;
        color: var(--primary-color, #8B5CF6);
    }

    .user-dropdown-compact .dropdown-item.danger:hover {
        color: #dc3545;
        background: #fef0f0;
    }

    .user-dropdown-compact .dropdown-divider {
        height: 1px;
        background: #e8e7ed;
        margin: 6px 18px;
    }

    .dropdown-user-info {
        padding: 12px 18px;
        border-bottom: 1px solid #f0f0f5;
        margin-bottom: 6px;
    }

    .dropdown-user-info strong {
        display: block;
        font-size: 14px;
        color: #1a1a2e;
    }

    .dropdown-user-info small {
        display: block;
        font-size: 12px;
        color: #888;
        margin-top: 2px;
    }

    /* --- Bouton contact --- */
    .btn-contact-compact {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary-color, #8B5CF6);
        color: #fff;
        padding: 8px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        transition: 0.3s;
        border: 1px solid var(--primary-color, #8B5CF6);
        white-space: nowrap;
    }

    .btn-contact-compact:hover {
        background: transparent;
        color: var(--primary-color, #8B5CF6);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .btn-contact-compact i {
        font-size: 14px;
        transition: 0.3s;
    }

    .btn-contact-compact:hover i {
        transform: translateX(4px);
    }

    /* --- Menu hamburger mobile --- */
    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        gap: 5px;
        background: transparent;
        border: none;
        padding: 8px 4px;
        cursor: pointer;
        margin-left: 4px;
    }

    .mobile-menu-toggle span {
        display: block;
        width: 25px;
        height: 2.5px;
        background: #1a1a2e;
        border-radius: 2px;
        transition: 0.3s;
    }

    .mobile-menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .mobile-menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .mobile-menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    /* --- Overlay menu mobile --- */
    .mobile-menu-overlay {
        position: fixed;
        top: 0;
        right: -100%;
        width: 100%;
        max-width: 320px;
        height: 100vh;
        background: #ffffff;
        z-index: 2000;
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: -5px 0 30px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
    }

    .mobile-menu-overlay.open {
        right: 0;
    }

    .mobile-menu-overlay::before {
        content: '';
        position: fixed;
        top: 0;
        left: -100vw;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.3);
        opacity: 0;
        transition: 0.4s;
        pointer-events: none;
        z-index: -1;
    }

    .mobile-menu-overlay.open::before {
        opacity: 1;
        left: 0;
        pointer-events: auto;
    }

    .mobile-menu-content {
        padding: 30px 24px 30px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .mobile-menu-close {
        align-self: flex-end;
        font-size: 30px;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #1a1a2e;
        padding: 0 8px;
        line-height: 1;
        transition: 0.3s;
    }

    .mobile-menu-close:hover {
        color: var(--primary-color, #8B5CF6);
        transform: rotate(90deg);
    }

    .mobile-nav-menu {
        list-style: none;
        padding: 0;
        margin: 30px 0 40px;
        flex: 1;
    }

    .mobile-nav-menu li {
        border-bottom: 1px solid #f0f0f5;
    }

    .mobile-nav-menu li a {
        display: block;
        padding: 16px 0;
        color: #1a1a2e;
        text-decoration: none;
        font-size: 17px;
        font-weight: 600;
        transition: 0.2s;
        letter-spacing: 0.3px;
    }

    .mobile-nav-menu li a:hover {
        color: var(--primary-color, #8B5CF6);
        padding-left: 8px;
    }

    .mobile-menu-footer {
        padding-top: 20px;
        border-top: 1px solid #f0f0f5;
    }

    .mobile-contact {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 14px;
        font-size: 15px;
    }

    /* ============================================
       RESPONSIVE BREAKPOINTS
       ============================================ */

    /* --- Tablettes et petits écrans --- */
    @media (max-width: 991px) {
        .top-headar-area {
            padding: 4px 0;
        }

        .top-info ul,
        .top-social-icon ul {
            justify-content: center !important;
            gap: 12px;
        }

        .top-info ul li {
            font-size: 11px;
        }

        .main-header-compact .container-fluid {
            padding: 0 15px;
        }

        .mobile-menu-toggle {
            display: flex;
        }

        .user-btn-compact {
            padding: 6px 10px 6px 8px;
        }

        .user-btn-compact .user-name {
            display: none;
        }

        .logo-compact img {
            height: 30px;
        }

        .btn-contact-compact {
            padding: 6px 14px;
            font-size: 12px;
        }
    }

    /* --- Mobiles --- */
    @media (max-width: 576px) {
        .top-headar-area {
            display: none;
        }

        .main-header-compact {
            padding: 6px 0;
        }

        .main-header-compact .container-fluid {
            padding: 0 12px;
        }

        .logo-compact img {
            height: 28px;
        }

        .user-btn-compact {
            padding: 5px 8px 5px 6px;
            border: none;
        }

        .user-btn-compact svg {
            width: 20px;
            height: 20px;
        }

        .btn-contact-compact {
            display: none !important;
        }

        .mobile-menu-toggle {
            padding: 4px 2px;
            gap: 4px;
        }

        .mobile-menu-toggle span {
            width: 22px;
            height: 2px;
        }

        .user-dropdown-compact {
            min-width: 200px;
            right: -10px;
        }

        .mobile-menu-overlay {
            max-width: 100%;
        }

        .mobile-menu-content {
            padding: 20px 18px 30px;
        }

        .mobile-nav-menu li a {
            font-size: 16px;
            padding: 14px 0;
        }
    }

    /* --- Très petits écrans --- */
    @media (max-width: 375px) {
        .logo-compact img {
            height: 24px;
        }

        .user-btn-compact svg {
            width: 18px;
            height: 18px;
        }

        .header-actions-compact {
            gap: 6px;
        }

        .mobile-menu-toggle span {
            width: 18px;
            height: 2px;
        }
    }

    /* --- Support des écrans larges --- */
    @media (min-width: 1200px) {
        .nav-menu-compact li a {
            font-size: 15px;
            padding: 8px 20px;
        }

        .btn-contact-compact {
            padding: 10px 28px;
            font-size: 14px;
        }

        .user-btn-compact {
            padding: 8px 18px 8px 12px;
            font-size: 14px;
        }
    }

    /* ============================================
       UTILITAIRES
       ============================================ */
    .d-none {
        display: none !important;
    }

    @media (min-width: 992px) {
        .d-lg-block {
            display: block !important;
        }

        .d-lg-none {
            display: none !important;
        }
    }

    @media (min-width: 576px) {
        .d-sm-inline-flex {
            display: inline-flex !important;
        }
    }

    @media (min-width: 768px) {
        .d-md-inline {
            display: inline !important;
        }
    }
</style>

<!-- JavaScript pour les interactions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ============================================
        // MENU MOBILE
        // ============================================
        const toggleBtn = document.getElementById('mobileMenuToggle');
        const overlay = document.getElementById('mobileMenuOverlay');
        const closeBtn = document.getElementById('mobileMenuClose');

        if (toggleBtn && overlay) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                overlay.classList.toggle('open');
                toggleBtn.classList.toggle('active');
                document.body.style.overflow = overlay.classList.contains('open') ? 'hidden' : '';
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    overlay.classList.remove('open');
                    toggleBtn.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            // Fermer le menu en cliquant sur un lien
            overlay.querySelectorAll('.mobile-nav-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    overlay.classList.remove('open');
                    toggleBtn.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });

            // Fermer en cliquant sur le fond
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('open');
                    toggleBtn.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }

        // ============================================
        // FONCTION DE DÉCONNEXION
        // ============================================
        window.logoutUser = function() {
            if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
                window.location.href = '?page=logout';
            }
        };

    });
</script>