<div class="footar-area">
	<div class="container">
		<div class="row">
			<!-- Colonne 1 - Logo & Infos -->
			<div class="col-lg-4 col-md-12">
				<div class="footer-logo text-center-mobile">
					<a href="?page=home" class="logo-compact">
						<img src="assets/img/home-1/log1.png" alt="" height="35">
						<span class="femiempire-name">
							<span class="femi">Femi</span><span class="empire">Empire</span>
						</span>
					</a>
				</div>

				<p class="footer-desc text-center-mobile">
					Un soin sur mesure et un design unique à chaque visite. Votre beauté, notre passion.
				</p>
				<div class="footar-contact text-center-mobile">
					<p><img src="assets/img/home-1/call-icon.png" alt="" class="call-icon">+123 (4567) 890</p>
				</div>
				<div class="footar-social-icon text-center-mobile">
					<ul>
						<li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
						<li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
						<li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
						<li><a href="#"><i class="fab fa-instagram"></i></a></li>
					</ul>
				</div>
			</div>

			<!-- Colonne 2 - Company -->
			<div class="col-lg-2 col-md-4 col-sm-6 col-6">
				<div class="footar-content">
					<div class="footar-title">
						<h4>Company</h4>
					</div>
					<div class="footar-list-item">
						<ul>
							<li><a href="?page=about">About Us</a></li>
							<li><a href="?page=services">Our Services</a></li>
							<li><a href="?page=home#portfolio-area">Our Works</a></li>
							<li><a href="?page=home#blog-area">Latest Blog</a></li>
						</ul>
					</div>
				</div>
			</div>

			<!-- Colonne 3 - Services -->
			<div class="col-lg-2 col-md-4 col-sm-6 col-6">
				<div class="footar-content fotar-content">
					<div class="footar-title">
						<h4>Services</h4>
					</div>
					<div class="footar-list-item ">
						<ul>
							<li><a href="#" class="footer-link">Formation Onglerie</a></li>
							<li><a href="#" class="footer-link">Pédicure – Manucure</a></li>
							<li><a href="#" class="footer-link">Nail Art</a></li>
							<li><a href="#" class="footer-link">Maquillage Pro</a></li>
						</ul>
					</div>
				</div>
			</div>

			<!-- Colonne 4 - Horaires -->
			<div class="col-lg-4 col-md-12">
				<div class="footar-content">
					<div class="footar-title text-center-mobile">
						<h4>Working Hrs</h4>
					</div>
					<div class="footar-working-list">
						<ul>
							<li><span class="day">Monday - Thursday</span> <span class="hour">10.00 am - 5.00 pm</span></li>
							<li><span class="day">Saturday</span> <span class="hour">9.00 am - 5.00 pm</span></li>
							<li><span class="day">Sunday</span> <span class="hour">2.00 pm - 5.00 pm</span></li>
							<li><span class="day">FRIDAY</span> <span class="hour offday">OFFDAY</span></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Footer Bottom -->
		<div class="row add-bg align-items-center">
			<div class="col-lg-6 col-md-12">
				<div class="footer-bottom-content text-center-mobile">
					<p>© 2026 Femi Empire - Tous droits réservés</p>
				</div>
			</div>
			<div class="col-lg-6 col-md-12">
				<div class="footer-bottom-content footer-bottom-links text-center-mobile">
					<ul>
						<li><a href="#">Privacy Policy</a></li>
						<li><a href="#">Terms & Conditions</a></li>
					</ul>
				</div>
			</div>
		</div>

		<!-- Shapes -->
		<div class="footer-shape-one">
			<img src="assets/img/home-1/footer-shape1.png" alt="">
		</div>
		<div class="footer-shape-two">
			<img src="assets/img/home-1/footer-shape2.png" alt="">
		</div>
	</div>
</div>

<!-- Progress Indicator -->
<div class="prgoress_indicator active-progress">
	<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
		<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 57.3858;"></path>
	</svg>
</div>

<style>
	/* ============================================
   FOOTER - CSS PUR (SANS BOOTSTRAP)
   ============================================ */

	/* --- Reset & Container --- */
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	.container {
		width: 100%;
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 15px;
	}

	.row {
		display: flex;
		flex-wrap: wrap;
		margin: 0 -15px;
	}

	/* --- Colonnes (sans Bootstrap) --- */
	[class*="col-"] {
		padding: 0 15px;
		flex-shrink: 0;
	}

	.col-6 {
		flex: 0 0 50%;
		max-width: 50%;
	}

	.col-sm-6 {
		flex: 0 0 50%;
		max-width: 50%;
	}

	.col-md-4 {
		flex: 0 0 33.333333%;
		max-width: 33.333333%;
	}

	.col-md-12 {
		flex: 0 0 100%;
		max-width: 100%;
	}

	.col-lg-2 {
		flex: 0 0 16.666667%;
		max-width: 16.666667%;
	}

	.col-lg-4 {
		flex: 0 0 33.333333%;
		max-width: 33.333333%;
	}

	.col-lg-6 {
		flex: 0 0 50%;
		max-width: 50%;
	}

	/* ============================================
   FOOTER - STYLES PRINCIPAUX
   ============================================ */

	.footar-area {
		background: #0a132e;
		padding: 50px 0 0;
		position: relative;
		overflow: hidden;
		margin-top: 0;
	}

	/* --- Logo & Description --- */
	.footer-logo {
		margin-bottom: 12px;
	}

	.footer-logo-img {
		max-height: 45px;
		width: auto;
	}

	.footer-desc {
		font-size: 14px;
		line-height: 22px;
		color: #a4a7b3;
		width: 95%;
		margin: 10px 0 12px;
	}

	/* --- Contact --- */
	.footar-contact p {
		display: flex;
		align-items: center;
		gap: 10px;
		margin: 0;
		font-size: 14px;
		color: #ffffff;
		font-weight: 500;
	}

	.call-icon {
		width: 20px;
		height: auto;
		flex-shrink: 0;
	}

	/* --- Réseaux sociaux --- */
	.footar-social-icon {
		margin-top: 12px;
	}

	.footar-social-icon ul {
		display: flex;
		flex-wrap: wrap;
		gap: 6px;
		padding: 0;
		margin: 0;
		list-style: none;
	}

	.footar-social-icon ul li i {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 34px;
		height: 34px;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.08);
		color: #fff;
		font-size: 13px;
		transition: all .3s ease;
	}

	.footar-social-icon ul li i:hover {
		background: #e2836a;
		transform: translateY(-2px);
	}

	/* --- Titres --- */
	.footar-title {
		margin-bottom: 10px;
	}

	.footar-title h4 {
		margin: 0;
		font-size: 16px;
		font-weight: 600;
		color: #ffffff;
	}

	/* --- Contenu des colonnes --- */
	.footar-content {
		margin-bottom: 15px;
	}

	/* --- Listes --- */
	.footar-list-item ul,
	.footar-working-list ul {
		margin: 0;
		padding: 0;
		list-style: none;
	}

	.footar-list-item ul li {
		margin-bottom: 2px;
	}

	.footar-list-item ul li a {
		display: inline-block;
		font-size: 13px;
		line-height: 28px;
		color: #a4a7b3;
		font-weight: 400;
		text-decoration: none;
		transition: color .3s ease;
	}

	.footar-list-item ul li a:hover {
		color: #e2836a;
	}

	/* ============================================
   HORAIRES - RESTENT SUR UNE LIGNE
   ============================================ */
	.footar-working-list ul li {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 10px;
		padding: 6px 0;
		border-bottom: 1px solid rgba(255, 255, 255, 0.06);
		font-size: 13px;
		color: #ffffff;
		flex-wrap: nowrap;
		/* ← TOUJOURS SUR UNE LIGNE */
	}

	.footar-working-list ul li:last-child {
		border-bottom: none;
	}

	.footar-working-list .day {
		color: #a4a7b3;
		white-space: nowrap;
		/* ← RESTE SUR UNE LIGNE */
		text-align: left;
		font-size: 14px;
		flex-shrink: 0;
		margin-right: auto;
	}

	.footar-working-list .hour {
		color: #a4a7b3;
		white-space: nowrap;
		/* ← RESTE SUR UNE LIGNE */
		text-align: right;
		font-size: 12px;
		flex-shrink: 0;
		/* ← NE SE RÉTRÉCIT PAS */
		margin-left: auto;
		/* ← POUSSE À DROITE */
	}

	.footar-working-list .offday {
		color: #e2836a;
		font-weight: 600;
		text-transform: uppercase;
	}

	/* --- Footer Bottom --- */
	.footar-area .row.add-bg {
		background: #172140;
		padding: 12px 20px;
		margin-top: 30px;
		border-radius: 8px;
		align-items: center;
	}

	.footer-bottom-content p {
		margin: 0;
		font-size: 13px;
		color: #a4a7b3;
	}

	.footer-bottom-content ul {
		display: flex;
		justify-content: flex-end;
		flex-wrap: wrap;
		gap: 15px;
		margin: 0;
		padding: 0;
		list-style: none;
	}

	.footer-bottom-content ul li a {
		font-size: 12px;
		color: #a4a7b3;
		text-decoration: none;
		transition: color .3s ease;
	}

	.footer-bottom-content ul li a:hover {
		color: #e2836a;
	}

	/* --- Shapes décoratives --- */
	.footer-shape-one,
	.footer-shape-two {
		pointer-events: none;
		user-select: none;
		display: none;
	}

	.footer-shape-one {
		position: absolute;
		left: 50px;
		top: 80px;
		opacity: .5;
		animation: bns1 3s linear infinite;
	}

	.footer-shape-two {
		position: absolute;
		right: -38px;
		top: 20px;
		opacity: .5;
		animation: bns2 5s linear infinite;
	}

	@keyframes bns1 {
		50% {
			transform: rotate(-5deg);
		}

		80% {
			transform: translateX(8px);
		}
	}

	@keyframes bns2 {
		50% {
			transform: translateX(-50px);
		}

		80% {
			transform: translateX(20px);
		}
	}

	/* ============================================
   RESPONSIVE - CSS PUR
   ============================================ */

	/* --- Tablettes (max-width: 991px) --- */
	@media (max-width: 991px) {
		.footar-area {
			padding: 40px 0 0;
		}

		.col-md-4 {
			flex: 0 0 33.333333%;
			max-width: 33.333333%;
		}

		.col-md-12 {
			flex: 0 0 100%;
			max-width: 100%;
		}

		.footer-desc {
			width: 100%;
			font-size: 13px;
		}

		.footar-title h4 {
			font-size: 15px;
		}

		.footar-area .row.add-bg {
			padding: 10px 15px;
			margin-top: 25px;
		}

		.footer-bottom-content p {
			font-size: 12px;
			text-align: center;
		}

		.footer-bottom-content ul {
			justify-content: center;
			margin-top: 5px;
		}

		.footer-bottom-content ul li a {
			font-size: 11px;
		}

		.text-center-mobile {
			text-align: center !important;
		}

		.footar-social-icon ul {
			justify-content: center;
		}

		.footar-contact p {
			justify-content: center;
		}

		.footer-logo {
			text-align: center;
		}

		/* HORAIRES - Restent sur une ligne sur tablette */
		.footar-working-list ul li {
			flex-wrap: nowrap !important;
			font-size: 12px;
			padding: 5px 0;
		}

		.footar-working-list .day {
			font-size: 12px;
			white-space: nowrap !important;
		}

		.footar-working-list .hour {
			font-size: 11px;
			white-space: nowrap !important;
		}
	}

	/* --- Mobiles (max-width: 767px) --- */
	@media (max-width: 767px) {
		.fotar-content {
			display: flex;
			flex-direction: column;
			align-items: flex-end;
		}

		.footar-area {
			padding: 30px 0 0;
		}

		.col-sm-6 {
			flex: 0 0 50%;
			max-width: 50%;
		}

		.col-md-4 {
			flex: 0 0 50%;
			max-width: 50%;
		}

		.col-md-12 {
			flex: 0 0 100%;
			max-width: 100%;
		}

		.col-lg-2.col-md-4,
		.col-lg-2.col-md-4.col-sm-6 {
			flex: 0 0 50%;
			max-width: 50%;
		}

		.footer-logo-img {
			max-height: 40px;
		}

		.footer-desc {
			font-size: 12px;
			line-height: 20px;
			margin: 8px 0 10px;
		}

		.footar-contact p {
			font-size: 13px;
		}

		.call-icon {
			width: 18px;
		}

		.footar-social-icon ul li i {
			width: 30px;
			height: 30px;
			font-size: 12px;
		}

		.footar-title h4 {
			font-size: 14px;
			margin-bottom: 8px;
		}

		.footar-list-item ul li a {
			font-size: 12px;
			line-height: 26px;
		}

		.footer-link {
			display: inline-block;
			text-decoration: none;
			color: #555;
			transition: all 0.3s ease;
		}

		.footer-link:hover {
			color: #e2836a;
			transform: translateX(-5px);
		}

		/* ============================================
       HORAIRES - RESTENT SUR UNE LIGNE SUR MOBILE
       ============================================ */
		.footar-working-list ul li {
			display: flex !important;
			justify-content: space-between !important;
			align-items: center !important;
			flex-wrap: nowrap !important;
			/* ← JAMAIS DE RETOUR À LA LIGNE */
			gap: 6px;
			padding: 5px 0;
			font-size: 11px;
		}

		.footar-working-list .day {
			flex: 0 1 auto;
			white-space: nowrap !important;
			/* ← JAMAIS DE RETOUR À LA LIGNE */
			font-size: 11px;
			color: #ffffff;
			overflow: hidden;
			text-overflow: ellipsis;
			/* ← AJOUTE "..." SI TROP LONG */
		}

		.footar-working-list .hour {
			flex-shrink: 0;
			white-space: nowrap !important;
			/* ← JAMAIS DE RETOUR À LA LIGNE */
			font-size: 10px;
			text-align: right;
			color: #a4a7b3;
			margin-left: auto;
		}

		.footar-working-list .offday {
			font-size: 10px;
		}

		/* ========================================== */

		.footar-area .row.add-bg {
			padding: 8px 12px;
			margin-top: 20px;
			flex-direction: column;
			gap: 5px;
		}

		.footer-bottom-content p {
			font-size: 11px;
		}

		.footer-bottom-content ul {
			gap: 10px;
			justify-content: center;
		}

		.footer-bottom-content ul li a {
			font-size: 10px;
		}

		.footer-shape-one,
		.footer-shape-two {
			display: none !important;
		}

		.footar-content {
			margin-bottom: 10px;
		}

		[class*="col-"] {
			padding: 0 10px;
		}

		.footar-working-list ul li {
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 100%;
		}

		.footar-working-list .day {
			white-space: nowrap;
		}

		.footar-working-list .hour {
			white-space: nowrap;
			text-align: right;
		}
	}

	@media (max-width: 767px) {
		.fotar-content {
			display: flex;
			flex-direction: column;
			align-items: flex-end;
		}

		.footer-link {
			display: inline-block;
			text-decoration: none;
			color: #555;
			transition: all 0.3s ease;
		}

		.footer-link:hover {
			color: #e2836a;
			transform: translateX(-5px);
		}

		.footar-working-list ul li {
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: nowrap;
			font-size: 10px;
			padding: 4px 0;
			gap: 4px;
		}

		.footar-working-list .day {
			font-size: 10px;
			flex: 1;
			white-space: nowrap;
			text-align: left;
		}

		.footar-working-list .hour {
			font-size: 9px;
			white-space: nowrap;
			text-align: right;
		}

		.footar-working-list ul li {
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 100%;
		}

		.footar-working-list .day {
			white-space: nowrap;
		}

		.footar-working-list .hour {
			white-space: nowrap;
			text-align: right;
		}
	}

	/* --- Très petits écrans (max-width: 375px) --- */
	@media (max-width: 375px) {
		.fotar-content {
			display: flex;
			flex-direction: column;
			align-items: flex-end;
		}

		.footer-link {
			display: inline-block;
			text-decoration: none;
			color: #555;
			transition: all 0.3s ease;
		}

		.footer-link:hover {
			color: #e2836a;
			transform: translateX(-5px);
		}

		.footar-area {
			padding: 25px 0 0;
		}

		.footer-logo-img {
			max-height: 35px;
		}

		.footer-desc {
			font-size: 11px;
			line-height: 18px;
		}

		.footar-title h4 {
			font-size: 13px;
		}

		.footar-list-item ul li a {
			font-size: 11px;
			line-height: 24px;
		}

		.footer-bottom-content p {
			font-size: 10px;
		}

		.footer-bottom-content ul li a {
			font-size: 9px;
		}

		[class*="col-"] {
			padding: 0 6px;
		}
	}

	/* --- Grands écrans (afficher les shapes) --- */
	@media (min-width: 992px) {
		.footer-link {
			display: inline-block;
			text-decoration: none;
			color: #555;
			transition: all 0.3s ease;
		}

		.footer-link:hover {
			color: #e2836a;
			transform: translateX(-5px);
		}

		.footer-shape-one,
		.footer-shape-two {
			display: block;
		}

		.col-lg-2 {
			flex: 0 0 16.666667%;
			max-width: 16.666667%;
		}

		.col-lg-4 {
			flex: 0 0 33.333333%;
			max-width: 33.333333%;
		}

		.col-lg-6 {
			flex: 0 0 50%;
			max-width: 50%;
		}
	}

	/* --- Ultra grands écrans (min-width: 1200px) --- */
	@media (min-width: 1200px) {
		.footer-link {
			display: inline-block;
			text-decoration: none;
			color: #555;
			transition: all 0.3s ease;
		}

		.footer-link:hover {
			color: #e2836a;
			transform: translateX(-5px);
		}

		.footar-area {
			padding: 60px 0 0;
		}

		.footer-desc {
			font-size: 14px;
			line-height: 24px;
			width: 90%;
		}

		.footar-title h4 {
			font-size: 18px;
		}

		.footar-list-item ul li a {
			font-size: 14px;
			line-height: 30px;
		}

		.footar-working-list ul li {
			font-size: 14px;
			padding: 8px 0;
		}

		.footar-working-list .hour {
			font-size: 13px;
		}
	}

	/* ============================================
   UTILITAIRES
   ============================================ */
	.text-center-mobile {
		text-align: left;
	}

	@media (max-width: 991px) {
		.footer-link {
			display: inline-block;
			text-decoration: none;
			color: #555;
			transition: all 0.3s ease;
		}

		.footer-link:hover {
			color: #e2836a;
			transform: translateX(-5px);
		}

		.text-center-mobile {
			text-align: left !important;
		}
	}

	.align-items-center {
		align-items: center;
	}

	/* --- Progress Indicator --- */
	.prgoress_indicator {
		position: fixed;
		bottom: 30px;
		right: 30px;
		width: 50px;
		height: 50px;
		z-index: 999;
		cursor: pointer;
	}

	.prgoress_indicator .progress-circle {
		transform: rotate(-90deg);
		width: 100%;
		height: 100%;
	}

	.prgoress_indicator .progress-circle path {
		stroke: #e2836a;
		stroke-width: 4;
		fill: none;
	}
</style>