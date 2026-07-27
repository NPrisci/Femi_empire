<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Header compact fixe -->
<div class="header-compact">

    <div class="top-headar-area">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-6">
				<div class="top-info">
					<ul>
						<li><i class="bi bi-envelope"></i>youremail@gmail.com</li>
						<li><i class="bi bi-alarm"></i>Sat - Sun (9.00am - 5pm)</li>
					</ul>
				</div>
			</div>
			<div class="col-lg-6">
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
                <!-- Logo plus petit -->
                <div class="col-lg-2">
                    <a href="?page=home" class="logo-compact">
                        <img src="assets/img/home-1/log1.png" alt="" height="35">
                    </a>
                </div>

                <!-- Menu compact -->
                <div class="col-lg-7 col-xl-6">
                    <ul class="nav-menu-compact">
                        <li><a href="?page=about">About</a></li>
                        <li><a href="?page=service">Service</a></li>
                        <li><a href="?page=formation">Formation</a></li>
                        <li><a href="?page=client">Client</a></li>
                    </ul>
                </div>

                <!-- User et contact -->
                <div class="col-lg-3 col-xl-4 text-end">
                    <div class="header-actions-compact">
                        <!-- User menu compact -->
                        <div class="user-menu-compact">
                            <button class="user-btn-compact" id="userBtn">
                                <svg viewBox="0 0 32 32" width="18" height="18">
                                    <circle cx="16" cy="10" r="6" />
                                    <path d="M4 30c0-6.627 5.373-12 12-12s12 5.373 12 12H4z" />
                                </svg>
                                <span class="user-name">
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
                                    Se connecter</a>
                                    <a class="dropdown-item" href="?page=register">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
								<path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
								<circle cx="8.5" cy="7" r="4" />
								<line x1="20" y1="8" x2="20" y2="14" />
								<line x1="23" y1="11" x2="17" y2="11" />
							</svg>    
                                    Créer un compte</a>
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
                                    Dashboard</a>
                                    <a class="dropdown-item" href="?page=profile">
                                    <svg viewBox="0 0 32 32" fill="currentColor" width="16" height="16">
								<circle cx="16" cy="10" r="6" />
								<path d="M4 30c0-6.627 5.373-12 12-12s12 5.373 12 12H4z" />
							</svg>    
                                    Profil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item danger" href="?page=logout" onclick="logoutUser()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
								<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
								<polyline points="16 17 21 12 16 7" />
								<line x1="21" y1="12" x2="9" y2="12" />
							</svg>    
                                    Déconnexion</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bouton contact compact -->
                        <a href="#" class="btn-contact-compact">
                            Contact
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>