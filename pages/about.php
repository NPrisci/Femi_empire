<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FemiEmpire - À propos</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet" />

    <style>
        /* ---------- RESET & BASE ---------- */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #faf7f5;
            color: #1c1917;
            line-height: 1.6;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        a {
            text-decoration: none;
        }

        /* ---------- ANIMATIONS ---------- */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .animate-pulse {
            animation: pulse 2s ease-in-out infinite;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* =========================================================
                   MARQUEE / BANDEAU DÉFILANT
                ========================================================= */
        .marquee-section {
            background-color: #e2836a;
            color: #fcd34d;
            padding: 1rem 0;
            overflow: hidden;
            border-top: 1px solid #e2836a;
            border-bottom: 1px solid #e2836a;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        @media (min-width: 640px) {
            .marquee-section {
                padding: 1.25rem 0;
            }
        }

        .marquee-wrapper {
            position: relative;
            display: flex;
            white-space: nowrap;
            overflow: hidden;
        }

        .marquee-track {
            display: flex;
            align-items: center;
            gap: 2rem;
            font-size: 0.875rem;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            animation: marquee 25s linear infinite;
        }

        @media (min-width: 640px) {
            .marquee-track {
                font-size: 1rem;
                gap: 2.5rem;
            }
        }

        .marquee-track .item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: color 0.2s;
            cursor: default;
        }

        .marquee-track .item:hover {
            color: #ffffff;
        }

        .marquee-track .item svg {
            width: 1rem;
            height: 1rem;
            color: #fbbf24;
            opacity: 0.7;
        }

        /* =========================================================
                   BREADCRUMB / HERO
                ========================================================= */
        .breadcrumb-hero {
            position: relative;
            background: linear-gradient(to bottom, rgba(30, 7, 18, 0.82), rgba(30, 7, 18, 0.88)),
                url('https://images.unsplash.com/photo-1596462502278-27bfdc5ffea9?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 360px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 6rem;
            padding-bottom: 4rem;
        }

        @media (min-width: 640px) {
            .breadcrumb-hero {
                min-height: 420px;
                padding-top: 7rem;
                padding-bottom: 7rem;
            }
        }

        @media (min-width: 1024px) {
            .breadcrumb-hero {
                min-height: 480px;
                padding-top: 9rem;
                padding-bottom: 9rem;
            }
        }

        .breadcrumb-hero .blur-circle {
            position: absolute;
            border-radius: 9999px;
            filter: blur(3rem);
            pointer-events: none;
        }

        .breadcrumb-hero .blur-circle.one {
            top: 25%;
            left: 2.5rem;
            width: 12rem;
            height: 12rem;
            background: rgba(244, 63, 94, 0.1);
        }

        .breadcrumb-hero .blur-circle.two {
            bottom: 2.5rem;
            right: 2.5rem;
            width: 16rem;
            height: 16rem;
            background: rgba(251, 191, 36, 0.1);
        }

        .breadcrumb-content {
            position: relative;
            max-width: 64rem;
            margin: 0 auto;
            padding: 0 1rem;
            text-align: center;
            z-index: 10;
        }

        .breadcrumb-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(244, 63, 94, 0.2);
            border: 1px solid rgba(251, 146, 60, 0.3);
            backdrop-filter: blur(8px);
            padding: 0.375rem 1rem;
            border-radius: 9999px;
            color: #fecdd3;
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        @media (min-width: 640px) {
            .breadcrumb-badge {
                font-size: 0.875rem;
            }
        }

        .breadcrumb-badge svg {
            width: 0.875rem;
            height: 0.875rem;
            color: #fcd34d;
        }

        .breadcrumb-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        @media (min-width: 640px) {
            .breadcrumb-title {
                font-size: 3rem;
            }
        }

        @media (min-width: 1024px) {
            .breadcrumb-title {
                font-size: 3.75rem;
            }
        }

        .breadcrumb-subtitle {
            max-width: 42rem;
            margin: 0 auto 2rem;
            color: rgba(254, 205, 211, 0.9);
            font-size: 0.875rem;
            font-weight: 300;
            line-height: 1.7;
        }

        @media (min-width: 640px) {
            .breadcrumb-subtitle {
                font-size: 1rem;
            }
        }

        @media (min-width: 1024px) {
            .breadcrumb-subtitle {
                font-size: 1.125rem;
            }
        }

        .breadcrumb-subtitle span {
            font-weight: 600;
            color: #fde68a;
        }

        .breadcrumb-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.625rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #fecdd3;
        }

        @media (min-width: 640px) {
            .breadcrumb-nav {
                font-size: 0.875rem;
            }
        }

        .breadcrumb-nav a {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            color: #fecdd3;
            transition: color 0.2s;
        }

        .breadcrumb-nav a:hover {
            color: #fff;
        }

        .breadcrumb-nav a svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        .breadcrumb-nav .separator {
            width: 0.875rem;
            height: 0.875rem;
            color: rgba(251, 113, 133, 0.6);
        }

        .breadcrumb-nav .current {
            color: #fcd34d;
            font-weight: 600;
        }

        /* =========================================================
                   SECTION À PROPOS
                ========================================================= */
        .about-area {
            padding: 3rem 1rem;
            background-color: #f5f0ee;
            overflow: hidden;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            align-items: center;
        }

        @media (min-width: 1024px) {
            .about-grid {
                grid-template-columns: 1fr 1fr;
                gap: 3.5rem;
            }
        }

        /* =========================================================
                   BLOC IMAGE
                ========================================================= */
        .image-wrapper {
            position: relative;
            max-width: 28rem;
            margin: 0 auto;
        }

        @media (min-width: 1024px) {
            .image-wrapper {
                max-width: none;
            }
        }

        .border-deco-top-left {
            display: none;
        }

        .border-deco-bottom-right {
            display: none;
        }

        @media (min-width: 640px) {
            .border-deco-top-left {
                display: block;
                position: absolute;
                top: -1rem;
                left: -1rem;
                width: 100%;
                height: 100%;
                border: 2px solid #f9a8d4;
                border-radius: 1.5rem;
                z-index: 0;
                transform: rotate(-2deg);
            }

            .border-deco-bottom-right {
                display: block;
                position: absolute;
                bottom: -1rem;
                right: -1rem;
                width: 100%;
                height: 100%;
                background-color: rgba(226, 131, 106, 0.5);
                border-radius: 1.5rem;
                z-index: 0;
                transform: rotate(2deg);
            }
        }

        .photo-container {
            position: relative;
            z-index: 10;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 4px solid #ffffff;
            background-color: #d6ccc4;
            aspect-ratio: 4 / 5;
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }

        .photo-container:hover img {
            transform: scale(1.05);
        }

        .photo-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.4), transparent 60%);
        }

        .experience-badge {
            position: absolute;
            bottom: -1.5rem;
            right: 1rem;
            z-index: 20;
            background: linear-gradient(to bottom right, #e2836a, #fa8b70);
            color: #fff;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(251, 146, 60, 0.3);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .experience-badge {
                bottom: -2rem;
                right: -1.5rem;
                padding: 1.5rem 1.75rem;
            }
        }

        .experience-badge .number {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #fcd34d;
        }

        @media (min-width: 640px) {
            .experience-badge .number {
                font-size: 2.25rem;
            }
        }

        .experience-badge .label {
            display: flex;
            flex-direction: column;
        }

        .experience-badge .label span:first-child {
            font-size: 0.7rem;
            font-weight: 700;
            color: #fde68a;
        }

        @media (min-width: 640px) {
            .experience-badge .label span:first-child {
                font-size: 0.875rem;
            }
        }

        .experience-badge .label span:last-child {
            font-size: 0.65rem;
            color: #fecdd3;
            font-weight: 300;
        }

        @media (min-width: 640px) {
            .experience-badge .label span:last-child {
                font-size: 0.75rem;
            }
        }

        /* =========================================================
                   CONTENU TEXTE
                ========================================================= */
        .content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #fce7f3;
            color: #e2836a;
            border: 1px solid #fbcfe8;
            margin-bottom: 0.5rem;
            width: fit-content;
        }

        .section-label svg {
            width: 0.875rem;
            height: 0.875rem;
            color: #e2836a;
        }

        .main-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: #1c1917;
            line-height: 1.2;
        }

        @media (min-width: 640px) {
            .main-title {
                font-size: 2.25rem;
            }
        }

        @media (min-width: 1024px) {
            .main-title {
                font-size: 2.5rem;
            }
        }

        .main-title .highlight {
            color: #f47e60;
        }

        .description {
            margin-top: 0.5rem;
            color: #57534e;
            font-size: 0.9rem;
            line-height: 1.7;
        }

        @media (min-width: 640px) {
            .description {
                font-size: 1rem;
            }
        }

        .description strong {
            color: #f47e60;
            font-weight: 600;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            padding-top: 0.5rem;
        }

        @media (min-width: 640px) {
            .features-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .feature-card {
            padding: 1rem;
            border-radius: 1rem;
            background-color: #fff;
            border: 1px solid rgba(214, 211, 209, 0.8);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.2s;
        }

        .feature-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .feature-card .icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            transition: background 0.2s, color 0.2s;
        }

        .feature-card .icon.rose {
            background-color: #fce7f3;
            color: #e2836a;
        }

        .feature-card:hover .icon.rose {
            background-color: #e2836a;
            color: #fff;
        }

        .feature-card .icon.amber {
            background-color: #fef3c7;
            color: #b45309;
        }

        .feature-card:hover .icon.amber {
            background-color: #b45309;
            color: #fff;
        }

        .feature-card h5 {
            font-weight: 600;
            color: #1c1917;
            font-size: 0.9rem;
            margin-bottom: 0.15rem;
        }

        .feature-card p {
            font-size: 0.75rem;
            color: #78716c;
            line-height: 1.5;
        }

        .counters {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            padding: 1rem;
            border-radius: 1rem;
            background: linear-gradient(to right, #fdf2f2, #fffbeb);
            border: 1px solid rgba(252, 165, 165, 0.7);
            text-align: center;
        }

        @media (min-width: 640px) {
            .counters {
                gap: 1rem;
                padding: 1.25rem;
            }
        }

        .counters .counter-item h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #e2836a;
        }

        @media (min-width: 640px) {
            .counters .counter-item h3 {
                font-size: 1.5rem;
            }
        }

        .counters .counter-item span {
            font-size: 0.65rem;
            font-weight: 500;
            color: #57534e;
        }

        @media (min-width: 640px) {
            .counters .counter-item span {
                font-size: 0.75rem;
            }
        }

        .counters .divider {
            border-left: 1px solid rgba(252, 165, 165, 0.8);
        }

        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(to right, #e2836a, #fa8b70);
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.75rem 1.75rem;
            border: none;
            border-radius: 9999px;
            box-shadow: 0 10px 15px -3px rgba(226, 131, 106, 0.3);
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            width: fit-content;
        }

        .btn-cta:hover {
            background: linear-gradient(to right, #e2836a, #fa8b70);
            transform: translateY(-2px);
        }

        .btn-cta svg {
            width: 1rem;
            height: 1rem;
        }

        /* =========================================================
                   SPECIALTIES SECTION
                ========================================================= */
        .section-padding {
            padding: 4rem 1rem;
        }

        @media (min-width: 640px) {
            .section-padding {
                padding: 5rem 1rem;
            }
        }

        @media (min-width: 1024px) {
            .section-padding {
                padding: 6rem 1rem;
            }
        }

        .section-header {
            text-align: center;
            max-width: 48rem;
            margin: 0 auto 3rem;
        }

        @media (min-width: 640px) {
            .section-header {
                margin-bottom: 4rem;
            }
        }

        .section-header .badge {
            display: inline-block;
            padding: 0.25rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #fce7f3;
            color: #e2836a;
            margin-bottom: 0.5rem;
        }

        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1c1917;
            margin-bottom: 0.5rem;
        }

        @media (min-width: 640px) {
            .section-header h2 {
                font-size: 2.25rem;
            }
        }

        @media (min-width: 1024px) {
            .section-header h2 {
                font-size: 2.5rem;
            }
        }

        .section-header p {
            color: #57534e;
            font-size: 0.9rem;
        }

        @media (min-width: 640px) {
            .section-header p {
                font-size: 1rem;
            }
        }

        .section-header .highlight-text {
            color: #e2836a;
            font-weight: 500;
        }

        /* Specialties Grid */
        .specialties-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .specialties-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (min-width: 1024px) {
            .specialties-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 2rem;
            }
        }

        .specialty-card {
            background-color: #f5f0ee;
            border-radius: 1.5rem;
            border: 1px solid rgba(214, 211, 209, 0.8);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: all 0.5s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            cursor: pointer;
        }

        .specialty-card:hover {
            background-color: #e2836a;
            color: #fff;
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
        }

        .specialty-card .card-image {
            position: relative;
            height: 11rem;
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
            background-color: #d6ccc4;
        }

        .specialty-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s;
        }

        .specialty-card:hover .card-image img {
            transform: scale(1.1);
        }

        .specialty-card .card-image .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.2);
            transition: background 0.3s;
        }

        .specialty-card:hover .card-image .overlay {
            background: rgba(76, 5, 25, 0.4);
        }

        .specialty-card .card-image .icon-badge {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2836a;
            transition: all 0.3s;
        }

        .specialty-card:hover .card-image .icon-badge {
            background: #fcd34d;
            color: #e2836a;
        }

        .specialty-card .card-image .icon-badge svg {
            width: 1.5rem;
            height: 1.5rem;
        }

        .specialty-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.125rem;
            font-weight: 700;
            color: #1c1917;
            transition: color 0.3s;
            margin-bottom: 0.5rem;
        }

        .specialty-card:hover h3 {
            color: #fcd34d;
        }

        .specialty-card p {
            font-size: 0.8rem;
            color: #57534e;
            line-height: 1.6;
            transition: color 0.3s;
            margin-bottom: 1.5rem;
            flex: 1;
        }

        @media (min-width: 640px) {
            .specialty-card p {
                font-size: 0.875rem;
            }
        }

        .specialty-card:hover p {
            color: #fecdd3;
        }

        .specialty-card .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px solid rgba(214, 211, 209, 0.5);
            transition: border-color 0.3s;
        }

        .specialty-card:hover .card-footer {
            border-color: #e2836a;
        }

        .specialty-card .card-footer .learn-link {
            font-size: 0.7rem;
            font-weight: 600;
            color: #efae9e;
            transition: color 0.3s;
        }

        .specialty-card:hover .card-footer .learn-link {
            color: #fcd34d;
        }

        .specialty-card .card-footer .arrow-btn {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
            background-color: #fce7f3;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2836a;
            transition: all 0.3s;
        }

        .specialty-card:hover .card-footer .arrow-btn {
            background-color: #e2836a;
            color: #fcd34d;
        }

        .specialty-card .card-footer .arrow-btn svg {
            width: 1rem;
            height: 1rem;
        }

        /* =========================================================
                   SERVICES SECTION
                ========================================================= */
        .services-section {
            background-color: rgba(253, 242, 242, 0.6);
        }

        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2.5rem;
        }

        .filter-tabs .tab-btn {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
            color: #57534e;
            border: 1px solid #d6d3d1;
        }

        @media (min-width: 640px) {
            .filter-tabs .tab-btn {
                font-size: 0.875rem;
                padding: 0.5rem 1.25rem;
            }
        }

        .filter-tabs .tab-btn.active {
            background: #e2836a;
            color: #fcd34d;
            border-color: #e2836a;
            box-shadow: 0 4px 6px -1px rgba(226, 131, 106, 0.3);
        }

        .filter-tabs .tab-btn:hover:not(.active) {
            background: rgba(244, 63, 94, 0.1);
        }

        .services-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .services-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (min-width: 1024px) {
            .services-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .service-card {
            background: #fff;
            border-radius: 1.5rem;
            overflow: hidden;
            border: 1px solid #fce7f3;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .service-card .card-image {
            position: relative;
            height: 13rem;
            overflow: hidden;
            background: #d6ccc4;
        }

        @media (min-width: 640px) {
            .service-card .card-image {
                height: 14rem;
            }
        }

        .service-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .service-card:hover .card-image img {
            transform: scale(1.05);
        }

        .service-card .card-image .gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent 50%);
        }

        .service-card .card-image .duration-badge {
            position: absolute;
            bottom: 0.75rem;
            left: 0.75rem;
            background: rgba(226, 131, 106, 0.9);
            backdrop-filter: blur(4px);
            color: #fcd34d;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            border: 1px solid #e2836a;
        }

        .service-card .card-image .duration-badge svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        .service-card .card-image .price-tag {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: #fcd34d;
            color: #e2836a;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .service-card .card-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .service-card .card-body h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.125rem;
            font-weight: 700;
            color: #1c1917;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        .service-card:hover .card-body h3 {
            color: #f7beaf;
        }

        .service-card .card-body p {
            font-size: 0.8rem;
            color: #57534e;
            line-height: 1.6;
            margin-bottom: 1rem;
            flex: 1;
        }

        @media (min-width: 640px) {
            .service-card .card-body p {
                font-size: 0.875rem;
            }
        }

        .service-card .card-body .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px solid #f5f0ee;
        }

        .service-card .card-body .card-footer .availability {
            font-size: 0.7rem;
            color: #e2836a;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .service-card .card-body .card-footer .availability svg {
            width: 0.875rem;
            height: 0.875rem;
            color: #059669;
        }

        .service-card .card-body .card-footer .book-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background: #e2836a;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.5rem 0.875rem;
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .service-card .card-body .card-footer .book-btn:hover {
            background: #e2836a;
        }

        .service-card .card-body .card-footer .book-btn svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        /* =========================================================
                   PRICING SECTION
                ========================================================= */
        .pricing-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            align-items: stretch;
        }

        @media (min-width: 1024px) {
            .pricing-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .pricing-card {
            border-radius: 1.5rem;
            padding: 1.5rem 1.75rem;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
            position: relative;
        }

        @media (min-width: 640px) {
            .pricing-card {
                padding: 2rem 2.25rem;
            }
        }

        .pricing-card.popular {
            background: linear-gradient(to bottom, #e2836a, #fa8b70, #e2836a);
            color: #fff;
            border: 2px solid rgba(251, 191, 36, 0.6);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }

        @media (min-width: 1024px) {
            .pricing-card.popular {
                transform: translateY(-8px);
            }
        }

        .pricing-card.standard {
            background: #f5f0ee;
            color: #1c1917;
            border: 1px solid rgba(214, 211, 209, 0.9);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .pricing-card.standard:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .pricing-card .popular-badge {
            position: absolute;
            top: -0.875rem;
            left: 50%;
            transform: translateX(-50%);
            background: #fcd34d;
            color: #e2836a;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 1rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .pricing-card .popular-badge svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        .pricing-card .card-header {
            border-bottom: 1px solid rgba(214, 211, 209, 0.2);
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .pricing-card .card-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .pricing-card.popular .card-header h3 {
            color: #fcd34d;
        }

        .pricing-card .card-header .price {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-top: 0.75rem;
        }

        .pricing-card .card-header .price .amount {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 800;
        }

        @media (min-width: 640px) {
            .pricing-card .card-header .price .amount {
                font-size: 2.25rem;
            }
        }

        .pricing-card.popular .card-header .price .amount {
            color: #fff;
        }

        .pricing-card.standard .card-header .price .amount {
            color: #e2836a;
        }

        .pricing-card .card-header .price .period {
            font-size: 0.7rem;
            font-weight: 500;
        }

        .pricing-card.popular .card-header .price .period {
            color: #fecdd3;
        }

        .pricing-card.standard .card-header .price .period {
            color: #78716c;
        }

        .pricing-card .features-list {
            list-style: none;
            flex: 1;
            margin-bottom: 2rem;
        }

        .pricing-card .features-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.375rem 0;
            font-size: 0.8rem;
        }

        @media (min-width: 640px) {
            .pricing-card .features-list li {
                font-size: 0.875rem;
            }
        }

        .pricing-card.popular .features-list li {
            color: #fecdd3;
        }

        .pricing-card.standard .features-list li {
            color: #44403c;
        }

        .pricing-card .features-list li .check {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.125rem;
        }

        .pricing-card.popular .features-list li .check {
            background: rgba(251, 191, 36, 0.2);
            color: #fcd34d;
        }

        .pricing-card.standard .features-list li .check {
            background: #fce7f3;
            color: #e2836a;
        }

        .pricing-card .features-list li .check svg {
            width: 0.75rem;
            height: 0.75rem;
        }

        .pricing-card .cta-btn {
            width: 100%;
            padding: 0.875rem;
            border: none;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .pricing-card.popular .cta-btn {
            background: #fcd34d;
            color: #4c0519;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        }

        .pricing-card.popular .cta-btn:hover {
            background: #fbbf24;
            transform: scale(1.02);
        }

        .pricing-card.standard .cta-btn {
            background: #e2836a;
            color: #fff;
            box-shadow: 0 4px 6px -1px rgba(226, 131, 106, 0.2);
        }

        .pricing-card.standard .cta-btn:hover {
            background: #e2836a;
        }

        .pricing-card .cta-btn svg {
            width: 1rem;
            height: 1rem;
        }

        /* =========================================================
                   TEAM SECTION
                ========================================================= */
        .team-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .team-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (min-width: 1024px) {
            .team-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 2rem;
            }
        }

        .team-card {
            background: #fff;
            border-radius: 1.5rem;
            overflow: hidden;
            border: 1px solid rgba(214, 211, 209, 0.8);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .team-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        .team-card .card-image {
            position: relative;
            height: 17rem;
            overflow: hidden;
            background: #d6ccc4;
        }

        @media (min-width: 640px) {
            .team-card .card-image {
                height: 18rem;
            }
        }

        .team-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .team-card:hover .card-image img {
            transform: scale(1.05);
        }

        .team-card .card-image .gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(76, 5, 25, 0.8), transparent 50%);
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .team-card:hover .card-image .gradient {
            opacity: 0.9;
        }

        .team-card .card-image .info-pill {
            position: absolute;
            bottom: 0.75rem;
            left: 0.75rem;
            right: 0.75rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            padding: 0.75rem;
            border-radius: 1rem;
            border: 1px solid #fce7f3;
        }

        .team-card .card-image .info-pill h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            color: #1c1917;
        }

        .team-card .card-image .info-pill p {
            font-size: 0.7rem;
            font-weight: 600;
            color: #e2836a;
        }

        .team-card .card-body {
            padding: 1.25rem;
            flex: 1;
        }

        .team-card .card-body p {
            font-size: 0.8rem;
            color: #57534e;
            line-height: 1.6;
        }

        @media (min-width: 640px) {
            .team-card .card-body p {
                font-size: 0.875rem;
            }
        }

        .team-card .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem 1.25rem;
            border-top: 1px solid #f5f0ee;
            padding-top: 1rem;
        }

        .team-card .card-footer .label {
            font-size: 0.65rem;
            font-weight: 500;
            color: #a8a29e;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .team-card .card-footer .label svg {
            width: 0.75rem;
            height: 0.75rem;
            color: #f59e0b;
        }

        .team-card .card-footer .socials {
            display: flex;
            gap: 0.5rem;
        }

        .team-card .card-footer .socials a {
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: #fdf2f2;
            color: #e2836a;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 0.7rem;
        }

        .team-card .card-footer .socials a:hover {
            background: #e2836a;
            color: #fff;
        }

        .team-card .card-footer .socials a svg {
            width: 1rem;
            height: 1rem;
        }

        /* =========================================================
                   TESTIMONIALS SECTION
                ========================================================= */
        .testimonials-section {
            position: relative;
            overflow: hidden;
            background: #fff;
        }

        .testimonials-section .bg-shape {
            position: absolute;
            border-radius: 9999px;
            filter: blur(3rem);
            pointer-events: none;
        }

        .testimonials-section .bg-shape.one {
            top: 2.5rem;
            right: 0;
            width: 18rem;
            height: 18rem;
            background: rgba(244, 63, 94, 0.1);
        }

        .testimonials-section .bg-shape.two {
            bottom: 0;
            left: 0;
            width: 24rem;
            height: 24rem;
            background: rgba(251, 191, 36, 0.08);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        @media (min-width: 768px) {
            .testimonials-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (min-width: 1024px) {
            .testimonials-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .testimonial-card {
            background: #f5f0ee;
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid rgba(214, 211, 209, 0.8);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .testimonial-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .testimonial-card .quote-icon {
            position: absolute;
            top: 1rem;
            right: 1.25rem;
            color: #fecdd3;
            transition: color 0.3s;
        }

        .testimonial-card:hover .quote-icon {
            color: #f9a8d4;
        }

        .testimonial-card .quote-icon svg {
            width: 2rem;
            height: 2rem;
            opacity: 0.4;
        }

        .testimonial-card .stars {
            display: flex;
            gap: 0.125rem;
            margin-bottom: 1rem;
        }

        .testimonial-card .stars svg {
            width: 1rem;
            height: 1rem;
            fill: #fcd34d;
            color: #fcd34d;
        }

        .testimonial-card .stars .rating-text {
            font-size: 0.7rem;
            font-weight: 700;
            color: #44403c;
            margin-left: 0.25rem;
        }

        .testimonial-card blockquote {
            font-size: 0.8rem;
            color: #44403c;
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            flex: 1;
        }

        @media (min-width: 640px) {
            .testimonial-card blockquote {
                font-size: 0.875rem;
            }
        }

        .testimonial-card .author {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(214, 211, 209, 0.6);
        }

        .testimonial-card .author .avatar {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 9999px;
            object-fit: cover;
            border: 2px solid #f9a8d4;
        }

        .testimonial-card .author .info {
            flex: 1;
            overflow: hidden;
        }

        .testimonial-card .author .info .name {
            font-family: 'Playfair Display', serif;
            font-size: 0.875rem;
            font-weight: 700;
            color: #1c1917;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .testimonial-card .author .info .role {
            font-size: 0.65rem;
            font-weight: 500;
            color: #f7b9aa;
        }

        .testimonial-card .author .info .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.125rem;
            font-size: 0.55rem;
            font-weight: 600;
            color: #b45309;
            background: #fffbeb;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            border: 1px solid #fde68a;
            margin-top: 0.125rem;
        }

        .testimonial-card .author .info .badge svg {
            width: 0.625rem;
            height: 0.625rem;
            color: #d97706;
        }

        /* =========================================================
                   SUBSCRIBE SECTION
                ========================================================= */
        .subscribe-section {
            padding: 3rem 1rem;
            background: linear-gradient(to right, #e2836a, #fa8b70, #e2836a);
            color: #fff;
            border-top: 1px solid #e2836a;
            border-bottom: 1px solid #e2836a;
        }

        @media (min-width: 640px) {
            .subscribe-section {
                padding: 4rem 1rem;
            }
        }

        .subscribe-wrapper {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            align-items: center;
        }

        @media (min-width: 1024px) {
            .subscribe-wrapper {
                flex-direction: row;
                justify-content: space-between;
                padding: 2rem 3rem;
            }
        }

        .subscribe-wrapper .text-content {
            text-align: center;
            max-width: 32rem;
        }

        @media (min-width: 1024px) {
            .subscribe-wrapper .text-content {
                text-align: left;
            }
        }

        .subscribe-wrapper .text-content .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #fcd34d;
        }

        .subscribe-wrapper .text-content .badge svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        .subscribe-wrapper .text-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.5rem;
        }

        @media (min-width: 640px) {
            .subscribe-wrapper .text-content h2 {
                font-size: 1.875rem;
            }
        }

        @media (min-width: 1024px) {
            .subscribe-wrapper .text-content h2 {
                font-size: 2.25rem;
            }
        }

        .subscribe-wrapper .text-content h2 span {
            color: #fcd34d;
        }

        .subscribe-wrapper .text-content p {
            font-size: 0.8rem;
            color: rgba(252, 194, 204, 0.8);
            font-weight: 300;
            margin-top: 0.5rem;
        }

        @media (min-width: 640px) {
            .subscribe-wrapper .text-content p {
                font-size: 0.875rem;
            }
        }

        .subscribe-wrapper .form-wrapper {
            width: 100%;
            max-width: 28rem;
        }

        .subscribe-wrapper .form-wrapper .success-msg {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(52, 211, 153, 0.4);
            color: #a7f3d0;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            font-size: 0.8rem;
        }

        .subscribe-wrapper .form-wrapper .success-msg svg {
            width: 1.25rem;
            height: 1.25rem;
            color: #34d399;
            flex-shrink: 0;
        }

        .subscribe-wrapper .form-wrapper form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        @media (min-width: 640px) {
            .subscribe-wrapper .form-wrapper form {
                flex-direction: row;
            }
        }

        .subscribe-wrapper .form-wrapper form .input-wrap {
            flex: 1;
            position: relative;
        }

        .subscribe-wrapper .form-wrapper form .input-wrap input {
            width: 100%;
            background: rgba(28, 25, 23, 0.8);
            color: #fff;
            padding: 0.875rem 1.25rem;
            border-radius: 9999px;
            border: 1px solid #fcd34d;
            font-size: 0.8rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .subscribe-wrapper .form-wrapper form .input-wrap input::placeholder {
            color: #78716c;
        }

        .subscribe-wrapper .form-wrapper form .input-wrap input:focus {
            border-color: #fcd34d;
        }

        .subscribe-wrapper .form-wrapper form .submit-btn {
            background: linear-gradient(to right, #fcd34d, #fbbf24);
            color: #4c0519;
            font-weight: 700;
            padding: 0.875rem 1.75rem;
            border: none;
            border-radius: 9999px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        @media (min-width: 640px) {
            .subscribe-wrapper .form-wrapper form .submit-btn {
                font-size: 0.875rem;
            }
        }

        .subscribe-wrapper .form-wrapper form .submit-btn:hover {
            background: linear-gradient(to right, #fbbf24, #f59e0b);
            transform: scale(1.02);
        }

        .subscribe-wrapper .form-wrapper form .submit-btn svg {
            width: 1rem;
            height: 1rem;
        }

        /* =========================================================
                   MODAL DE RÉSERVATION
                ========================================================= */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: #fff;
            border-radius: 1.5rem;
            max-width: 28rem;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid #fce7f3;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            background: linear-gradient(to right, #e2836a, #fa8b70, #e2836a);
            padding: 1.5rem;
            color: #fff;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-close svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .modal-header .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #fcd34d;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .modal-header .badge svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        .modal-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .modal-header p {
            color: rgba(252, 194, 204, 0.8);
            font-size: 0.75rem;
            font-weight: 300;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            color: #44403c;
            margin-bottom: 0.25rem;
        }

        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
            background: #faf7f5;
            border: 1px solid #d6d3d1;
            border-radius: 0.75rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.8rem;
            color: #1c1917;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }

        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #e2836a;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 3rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .form-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon svg {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            color: #a8a29e;
        }

        .input-with-icon input,
        .input-with-icon select {
            padding-left: 2.25rem;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(to right, #e2836a, #fa8b70);
            color: #fff;
            font-weight: 700;
            padding: 0.875rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background: linear-gradient(to right, #e2836a, #fa8b70);
        }

        .success-screen {
            text-align: center;
            padding: 1.5rem 0;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .success-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 9999px;
            background: #d1fae5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .success-icon svg {
            width: 2.5rem;
            height: 2.5rem;
        }

        .success-screen h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1c1917;
        }

        .success-screen p {
            font-size: 0.875rem;
            color: #57534e;
            max-width: 20rem;
            margin: 0 auto;
        }

        .success-screen p strong {
            color: #e2836a;
        }

        .summary-box {
            background: #fdf2f2;
            border: 1px solid #fecdd3;
            border-radius: 1rem;
            padding: 1rem;
            text-align: left;
            font-size: 0.75rem;
        }

        .summary-box .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.375rem 0;
            border-bottom: 1px solid rgba(252, 165, 165, 0.6);
        }

        .summary-box .summary-row:last-child {
            border-bottom: none;
        }

        .summary-box .summary-row .label {
            color: #78716c;
        }

        .summary-box .summary-row .value {
            font-weight: 600;
            color: #1c1917;
        }

        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: #059669;
            color: #fff;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.2s;
        }

        .btn-whatsapp:hover {
            background: #047857;
        }

        .btn-whatsapp svg {
            width: 1rem;
            height: 1rem;
        }

        .btn-close-modal {
            background: none;
            border: none;
            color: #78716c;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            padding: 0.5rem;
            transition: color 0.2s;
        }

        .btn-close-modal:hover {
            color: #1c1917;
        }

        .success-screen .actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        /* =========================================================
                   UTILITY
                ========================================================= */
        .bg-white {
            background: #fff;
        }

        .bg-stone-50 {
            background: #f5f0ee;
        }

        .text-rose-700 {
            color: #e2836a;
        }

        .text-rose-800 {
            color: #e2836a;
        }

        .border-rose-100 {
            border-color: #fce7f3;
        }

        .border-rose-800 {
            border-color: #e2836a;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .relative {
            position: relative;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .border-t {
            border-top: 1px solid #e5e7eb;
        }

        .border-b {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>



    <!-- ============================================================
    BREADCRUMB / HERO
    ============================================================ -->
    <section id="home" class="breadcrumb-hero">
        <div class="blur-circle one"></div>
        <div class="blur-circle two"></div>

        <div class="breadcrumb-content">
            <div class="breadcrumb-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                </svg>
                <span>Maison de Beauté &amp; Académie</span>
            </div>
            <h1 class="breadcrumb-title">À propos de nous</h1>
            <p class="breadcrumb-subtitle">
                Découvrez l'histoire, l'expertise et la passion qui animent le salon &amp; centre de formation
                <span>FemiEmpire</span>.
            </p>
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="#home">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Accueil</span>
                </a>
                <svg class="separator" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                <span class="current">À propos</span>
            </nav>
        </div>
    </section>

    <!-- ============================================================
    SECTION À PROPOS
    ============================================================ -->
    <section class="about-area">
        <div class="container">
            <div class="about-grid">

                <div class="image-wrapper">
                    <div class="border-deco-top-left"></div>
                    <div class="border-deco-bottom-right"></div>
                    <div class="photo-container">
                        <!-- <img src="https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=800&q=80" alt="Salon FemiEmpire" loading="lazy" /> -->
                        <img src="../assets/img/inner/about.jpg" alt="Salon FemiEmpire" loading="lazy" />
                        <div class="photo-overlay"></div>
                    </div>
                    <div class="experience-badge">
                        <span class="number">5+</span>
                        <div class="label">
                            <span>Années</span>
                            <span>d'expérience</span>
                        </div>
                    </div>
                </div>

                <div class="content">
                    <div class="section-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                        À propos de nous
                    </div>
                    <h2 class="main-title">
                        Votre partenaire beauté
                        <br />
                        <span class="highlight">&amp; Formation professionnelle</span>
                    </h2>
                    <p class="description">
                        Chez <strong>FemiEmpire</strong>, nous sublimons votre beauté grâce à notre expertise en prothésie ongulaire, maquillage professionnel et formations certifiantes destinées aux futures professionnelles.
                    </p>

                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="icon rose">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                            </div>
                            <h5>Prothésie Ongulaire</h5>
                            <p>Gel, capsules, nail art et designs personnalisés.</p>
                        </div>
                        <div class="feature-card">
                            <div class="icon amber">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </div>
                            <h5>Maquillage</h5>
                            <p>Mariage, cérémonies, shooting et événements.</p>
                        </div>
                        <div class="feature-card">
                            <div class="icon rose">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <h5>Formation</h5>
                            <p>Devenez une experte certifiée avec un accompagnement complet.</p>
                        </div>
                        <div class="feature-card">
                            <div class="icon amber">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                                </svg>
                            </div>
                            <h5>Qualité Premium</h5>
                            <p>Produits professionnels et techniques modernes.</p>
                        </div>
                    </div>

                    <div class="counters">
                        <div class="counter-item">
                            <h3>200+</h3>
                            <span>Élèves formées</span>
                        </div>
                        <div class="counter-item divider">
                            <h3>100%</h3>
                            <span>Satisfaction</span>
                        </div>
                        <div class="counter-item">
                            <h3>24/7</h3>
                            <span>Conseils</span>
                        </div>
                    </div>

                    <button class="btn-cta" id="openBookingBtn">
                        <span>Prendre rendez-vous</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================
    SPECIALTIES SECTION
    ============================================================ -->
    <section id="specialties" class="section-padding bg-white">
        <div class="container">
            <div class="section-header">
                <span class="badge">Nos Spécialités</span>
                <h2>Ce qui fait notre différence</h2>
                <p>Beauté, créativité et excellence au service de votre style</p>
            </div>

            <div class="specialties-grid">
                <!-- Spécialité 1 -->
                <div class="specialty-card" onclick="openBooking('specialty-1')">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=400&q=80" alt="Prothésie Ongulaire" loading="lazy" />
                        <div class="overlay"></div>
                        <div class="icon-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                        </div>
                    </div>
                    <h3>Prothésie Ongulaire</h3>
                    <p>Pose de gel, capsules, nail art et designs exclusifs pour des mains sublimes.</p>
                    <div class="card-footer">
                        <span class="learn-link">En savoir plus</span>
                        <div class="arrow-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Spécialité 2 -->
                <div class="specialty-card" onclick="openBooking('specialty-2')">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1522338242992-e1a54906a8da?auto=format&fit=crop&w=400&q=80" alt="Maquillage Artistique" loading="lazy" />
                        <div class="overlay"></div>
                        <div class="icon-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
                            </svg>
                        </div>
                    </div>
                    <h3>Maquillage Artistique</h3>
                    <p>Mariage, soirées, shooting photo & événements avec des produits professionnels.</p>
                    <div class="card-footer">
                        <span class="learn-link">En savoir plus</span>
                        <div class="arrow-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Spécialité 3 -->
                <div class="specialty-card" onclick="openBooking('specialty-3')">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=400&q=80" alt="Formation Professionnelle" loading="lazy" /> -->
                        <img src="../assets/img/home-2/onglerie.jfif" alt="Formation Professionnelle" loading="lazy" />
                        <div class="overlay"></div>
                        <div class="icon-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.25 5.25 0 007.5 15h9m-9 0a5.25 5.25 0 01-3.507-4.507" />
                            </svg>
                        </div>
                    </div>
                    <h3>Formation Professionnelle</h3>
                    <p>Programmes certifiants en prothésie ongulaire et maquillage avec accompagnement.</p>
                    <div class="card-footer">
                        <span class="learn-link">En savoir plus</span>
                        <div class="arrow-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Spécialité 4 -->
                <div class="specialty-card" onclick="openBooking('specialty-4')">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=400&q=80" alt="Qualité Premium" loading="lazy" /> -->
                        <img src="../assets/img/home-1/maquillage3.jpg" alt="Qualité Premium" loading="lazy" />
                        <div class="overlay"></div>
                        <div class="icon-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                            </svg>
                        </div>
                    </div>
                    <h3>Qualité Premium</h3>
                    <p>Produits haut de gamme, hygiène irréprochable et techniques de pointe.</p>
                    <div class="card-footer">
                        <span class="learn-link">En savoir plus</span>
                        <div class="arrow-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    MARQUEE / BANDEAU DÉFILANT
    ============================================================ -->
    <section class="marquee-section">
        <div class="marquee-wrapper">
            <div class="marquee-track">
                <span class="item">
                    FemiEmpire
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Prothésie Ongulaire
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Maquillage Pro
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Formation Beauté
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Nail Art
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Gel &amp; Capsules
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Qualité Premium
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <!-- Duplication pour effet infini -->
                <span class="item">
                    FemiEmpire
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Prothésie Ongulaire
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Maquillage Pro
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Formation Beauté
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Nail Art
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Gel &amp; Capsules
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
                <span class="item">
                    Qualité Premium
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </span>
            </div>
        </div>
    </section>

    <!-- ============================================================
    SERVICES SECTION
    ============================================================ -->
    <section id="services" class="section-padding services-section">
        <div class="container">
            <div class="section-header">
                <span class="badge">Nos Prestations</span>
                <h2>Découvrez nos services beauté &amp; nos formations</h2>
                <p>Des soins personnalisés et des programmes certifiants pour sublimer et former.</p>
            </div>

            <div class="filter-tabs">
                <button class="tab-btn active" data-filter="all">Toutes les Prestations</button>
                <button class="tab-btn" data-filter="ongles">Prothésie Ongulaire</button>
                <button class="tab-btn" data-filter="maquillage">Maquillage</button>
                <button class="tab-btn" data-filter="formation">Formations Certifiantes</button>
            </div>

            <div class="services-grid" id="servicesGrid">
                <!-- Service 1 -->
                <div class="service-card" data-category="ongles">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=400&q=80" alt="Pose d'ongles en gel" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="duration-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            1h30
                        </div>
                        <span class="price-tag">35 FCFA</span>
                    </div>
                    <div class="card-body">
                        <h3>Pose d'ongles en gel</h3>
                        <p>Réalisation d'une pose de gel naturelle ou colorée avec finition brillante.</p>
                        <div class="card-footer">
                            <span class="availability">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Disponible
                            </span>
                            <button class="book-btn" onclick="openBooking('ongles-gel')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                Réserver
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="service-card" data-category="ongles">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1596462502278-27bfdc5ffea9?auto=format&fit=crop&w=400&q=80" alt="Capsules & Nail Art" loading="lazy" /> -->
                        <img src="../assets/img/home-2/ongle-service.jpg" alt="Capsules & Nail Art" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="duration-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            2h
                        </div>
                        <span class="price-tag">55 FCFA</span>
                    </div>
                    <div class="card-body">
                        <h3>Capsules &amp; Nail Art</h3>
                        <p>Pose de capsules avec décorations artistiques et motifs personnalisés.</p>
                        <div class="card-footer">
                            <span class="availability">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Disponible
                            </span>
                            <button class="book-btn" onclick="openBooking('capsules')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                Réserver
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="service-card" data-category="maquillage">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1522338242992-e1a54906a8da?auto=format&fit=crop&w=400&q=80" alt="Maquillage Mariée" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="duration-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            2h
                        </div>
                        <span class="price-tag">80 FCFA</span>
                    </div>
                    <div class="card-body">
                        <h3>Maquillage Mariée</h3>
                        <p>Maquillage professionnel pour mariage avec essai préalable et tenue longue durée.</p>
                        <div class="card-footer">
                            <span class="availability">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Disponible
                            </span>
                            <button class="book-btn" onclick="openBooking('maquillage-mariee')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                Réserver
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Service 4 -->
                <div class="service-card" data-category="formation">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=400&q=80" alt="Formation Prothésie" loading="lazy" /> -->
                        <img src="../assets/img/home-2/for1.jpg" alt="Formation Prothésie" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="duration-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            5 jours
                        </div>
                        <span class="price-tag">450 FCFA</span>
                    </div>
                    <div class="card-body">
                        <h3>Formation Prothésie Ongulaire</h3>
                        <p>Programme complet de 5 jours pour maîtriser la pose de gel, capsules et nail art.</p>
                        <div class="card-footer">
                            <span class="availability">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Disponible
                            </span>
                            <button class="book-btn" onclick="openBooking('formation-ongles')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                Réserver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    PRICING SECTION
    ============================================================ -->
    <section id="pricing" class="section-padding bg-white">
        <div class="container">
            <div class="section-header">
                <span class="badge">Nos Tarifs</span>
                <h2>Des prestations de qualité à prix accessibles</h2>
                <p>Transparence, excellence et flexibilité pour tous vos besoins de soins ou de formation.</p>
            </div>

            <div class="pricing-grid">
                <!-- Forfait Découverte -->
                <div class="pricing-card standard">
                    <div class="card-header">
                        <h3>Forfait Découverte</h3>
                        <div class="price">
                            <span class="amount">50</span>
                            <span class="period">FCFA / séance</span>
                        </div>
                    </div>
                    <ul class="features-list">
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Pose d'ongles en gel
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Maquillage de jour
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Conseil personnalisé
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Produits premium
                        </li>
                    </ul>
                    <button class="cta-btn" onclick="openBooking('forfait-decouverte')">
                        Choisir cette offre
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>

                <!-- Forfait Prestige (Populaire) -->
                <div class="pricing-card popular">
                    <div class="popular-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                        Le plus populaire
                    </div>
                    <div class="card-header">
                        <h3>Forfait Prestige</h3>
                        <div class="price">
                            <span class="amount">120</span>
                            <span class="period">FCFA / séance</span>
                        </div>
                    </div>
                    <ul class="features-list">
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Nail Art personnalisé
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Maquillage complet + pose
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Produits haut de gamme
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Essai préalable offert
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Tenue longue durée
                        </li>
                    </ul>
                    <button class="cta-btn" onclick="openBooking('forfait-prestige')">
                        Choisir cette offre
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>

                <!-- Forfait Formation -->
                <div class="pricing-card standard">
                    <div class="card-header">
                        <h3>Forfait Formation</h3>
                        <div class="price">
                            <span class="amount">450</span>
                            <span class="period">FCFA    / programme</span>
                        </div>
                    </div>
                    <ul class="features-list">
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Formation complète 5 jours
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Certificat reconnu
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Kit professionnel inclus
                        </li>
                        <li>
                            <span class="check"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg></span>
                            Accompagnement post-formation
                        </li>
                    </ul>
                    <button class="cta-btn" onclick="openBooking('forfait-formation')">
                        Choisir cette offre
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    TEAM SECTION
    ============================================================ -->
    <section id="team" class="section-padding bg-stone-50">
        <div class="container">
            <div class="section-header">
                <span class="badge">Notre Équipe</span>
                <h2>Des expertes passionnées</h2>
                <p>Par la beauté, l'art du geste et la transmission de savoir-faire</p>
            </div>

            <div class="team-grid">
                <!-- Membre 1 -->
                <div class="team-card">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80" alt="Marie-Claire Kouassi" loading="lazy" /> -->
                        <img src="../assets/img/home-1/blog_author1.jfif" alt="Marie-Claire Kouassi" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="info-pill">
                            <h3>Marie-Claire Kouassi</h3>
                            <p>Fondatrice &amp; Experte Prothésie</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Passionnée par l'art des ongles depuis plus de 8 ans, elle forme et sublime avec élégance.</p>
                    </div>
                    <div class="card-footer">
                        <span class="label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            FemiEmpire Pro
                        </span>
                        <div class="socials">
                            <a href="#" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3.75L9.75 18.75" />
                                </svg></a>
                            <a href="#" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                                </svg></a>
                        </div>
                    </div>
                </div>

                <!-- Membre 2 -->
                <div class="team-card">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=400&q=80" alt="Sophie Konan" loading="lazy" /> -->
                        <img src="../assets/img/home-1/blog_author2.jfif" alt="Sophie Konan" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="info-pill">
                            <h3>Sophie Konan</h3>
                            <p>Maquilleuse Professionnelle</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Spécialiste du maquillage de mariage et des shooting photo, elle crée des looks uniques.</p>
                    </div>
                    <div class="card-footer">
                        <span class="label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            FemiEmpire Pro
                        </span>
                        <div class="socials">
                            <a href="#" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3.75L9.75 18.75" />
                                </svg></a>
                            <a href="#" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                                </svg></a>
                        </div>
                    </div>
                </div>

                <!-- Membre 3 -->
                <div class="team-card">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?auto=format&fit=crop&w=400&q=80" alt="Aïcha Bamba" loading="lazy" /> -->
                        <img src="../assets/img/home-1/blog_author3.jfif" alt="Aïcha Bamba" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="info-pill">
                            <h3>Aïcha Bamba</h3>
                            <p>Formatrice &amp; Artiste Nail Art</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Passionnée par le nail art et la transmission, elle forme les futures expertes.</p>
                    </div>
                    <div class="card-footer">
                        <span class="label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            FemiEmpire Pro
                        </span>
                        <div class="socials">
                            <a href="#" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3.75L9.75 18.75" />
                                </svg></a>
                            <a href="#" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                                </svg></a>
                        </div>
                    </div>
                </div>

                <!-- Membre 4 -->
                <div class="team-card">
                    <div class="card-image">
                        <!-- <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80" alt="David N'Guessan" loading="lazy" /> -->
                        <img src="../assets/img/home-1/blog_author1.jfif" alt="David N'Guessan" loading="lazy" />
                        <div class="gradient"></div>
                        <div class="info-pill">
                            <h3>David N'Guessan</h3>
                            <p>Responsable Relation Client</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>À l'écoute et professionnel, il assure une expérience client exceptionnelle.</p>
                    </div>
                    <div class="card-footer">
                        <span class="label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            FemiEmpire Pro
                        </span>
                        <div class="socials">
                            <a href="#" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3.75L9.75 18.75" />
                                </svg></a>
                            <a href="#" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                                </svg></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    TESTIMONIALS SECTION
    ============================================================ -->
    <section id="testimonials" class="section-padding testimonials-section">
        <div class="bg-shape one"></div>
        <div class="bg-shape two"></div>

        <div class="container" style="position:relative;z-index:1;">
            <div class="section-header">
                <span class="badge">Témoignages</span>
                <h2>Ce que disent nos clientes &amp; nos apprenantes</h2>
                <p>Des histoires de réussite, de confiance et de beauté partagées par notre communauté.</p>
            </div>

            <div class="testimonials-grid">
                <!-- Témoignage 1 -->
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <div class="stars">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <span class="rating-text">(5.0)</span>
                    </div>
                    <blockquote>Un accueil chaleureux et des prestations impeccables. Mon maquillage a tenu toute la journée ! Je recommande vivement.</blockquote>
                    <div class="author">
                        <!-- <img class="avatar" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Fatima Cissé" /> -->
                        <img class="avatar" src="../assets/img/home-1/blog_author1.jfif" alt="Fatima Cissé" />
                        <div class="info">
                            <div class="name">Fatima Cissé</div>
                            <div class="role">Mariée</div>
                            <div class="badge">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                                Client satisfaite
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Témoignage 2 -->
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <div class="stars">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <span class="rating-text">(5.0)</span>
                    </div>
                    <blockquote>La formation était exceptionnelle ! J'ai appris toutes les techniques de pose de gel et je suis maintenant certifiée. Merci FemiEmpire !</blockquote>
                    <div class="author">
                        <!-- <img class="avatar" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80" alt="Aminata Touré" /> -->
                        <img class="avatar" src="../assets/img/home-1/blog_author2.jfif" alt="Aminata Touré" />
                        <div class="info">
                            <div class="name">Aminata Touré</div>
                            <div class="role">Apprenante certifiée</div>
                            <div class="badge">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                                Formation réussie
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Témoignage 3 -->
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <div class="stars">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <span class="rating-text">(5.0)</span>
                    </div>
                    <blockquote>Une équipe talentueuse et professionnelle. Mon nail art était parfait et a tenu plus de 3 semaines !</blockquote>
                    <div class="author">
                        <!-- <img class="avatar" src="https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?auto=format&fit=crop&w=100&q=80" alt="Clarisse Koffi" /> -->
                        <img class="avatar" src="../assets/img/home-1/blog_author3.jfif" alt="Clarisse Koffi" />
                        <div class="info">
                            <div class="name">Clarisse Koffi</div>
                            <div class="role">Cliente régulière</div>
                            <div class="badge">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                                Fidèle cliente
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Témoignage 4 -->
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <div class="stars">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.714.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                        </svg>
                        <span class="rating-text">(5.0)</span>
                    </div>
                    <blockquote>Je suis venue pour un shooting photo et le résultat était incroyable. Une équipe à l'écoute et très créative.</blockquote>
                    <div class="author">
                        <!-- <img class="avatar" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Jean-Marc Bamba" /> -->
                        <img class="avatar" src="../assets/img/home-1/blog_author1.jfif" alt="Jean-Marc Bamba" />
                        <div class="info">
                            <div class="name">Jean-Marc Bamba</div>
                            <div class="role">Photographe</div>
                            <div class="badge">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                                Partenaire confiance
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    SUBSCRIBE SECTION
    ============================================================ -->
    <section class="subscribe-section">
        <div class="subscribe-wrapper">
            <div class="text-content">
                <div class="badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    Newsletter VIP
                </div>
                <h2>
                    Restez informée de nos offres
                    <br />
                    <span>&amp; nouveautés beauté</span>
                </h2>
                <p>
                    Inscrivez-vous pour recevoir nos promotions exclusives, conseils beauté et dates des prochaines sessions de formation.
                </p>
            </div>

            <div class="form-wrapper">
                <div id="subscribeSuccess" style="display:none;" class="success-msg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Merci ! Vous êtes bien inscrite à la newsletter FemiEmpire.</span>
                </div>

                <form id="subscribeForm">
                    <div class="input-wrap">
                        <input type="email" id="subscribeEmail" placeholder="Entrez votre E-mail" required />
                    </div>
                    <button type="submit" class="submit-btn">
                        <span>S'abonner</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- ============================================================
    MODAL DE RÉSERVATION
    ============================================================ -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-container animate-fadeIn">

            <div class="modal-header">
                <button class="modal-close" id="closeModalBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    FemiEmpire Réservation
                </div>
                <h2>Prendre rendez-vous</h2>
                <p>Réservez votre soin de beauté ou votre session de formation.</p>
            </div>

            <div class="modal-body" id="modalBody">

                <div id="formStep">
                    <form id="bookingForm" novalidate>

                        <div class="form-group">
                            <label for="serviceSelect">Choisir la Prestation ou Formation</label>
                            <select id="serviceSelect" required>
                                <optgroup label="Services & Formations">
                                    <option value="ongles-gel">Pose d'ongles en gel (1h30)</option>
                                    <option value="capsules">Capsules & Nail Art (2h)</option>
                                    <option value="maquillage-mariee">Maquillage Mariée (2h)</option>
                                    <option value="maquillage-soiree">Maquillage Soirée (1h)</option>
                                    <option value="formation-ongles">Formation Prothésie Ongulaire (5 jours)</option>
                                    <option value="formation-maquillage">Formation Maquillage Pro (4 jours)</option>
                                </optgroup>
                                <optgroup label="Forfaits & Tarifs">
                                    <option value="forfait-decouverte">Forfait Découverte - 50€</option>
                                    <option value="forfait-prestige">Forfait Prestige - 120€</option>
                                    <option value="forfait-formation">Forfait Formation Complète - 450€</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="dateInput">Date souhaitée</label>
                                <input type="date" id="dateInput" required />
                            </div>
                            <div class="form-group">
                                <label for="timeSelect">Heure</label>
                                <select id="timeSelect">
                                    <option value="09:00">09:00</option>
                                    <option value="10:30" selected>10:30</option>
                                    <option value="12:00">12:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:30">15:30</option>
                                    <option value="17:00">17:00</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="fullNameInput">Nom & Prénom</label>
                            <div class="input-with-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <input type="text" id="fullNameInput" placeholder="Ex: Marie-Claire Kouassi" required />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phoneInput">Téléphone (WhatsApp)</label>
                                <div class="input-with-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 1.729.877 3.255 2.206 4.145l-1.205 1.205a.75.75 0 101.061 1.061l1.204-1.205a6.75 6.75 0 01-.75-1.206 6.75 6.75 0 01-.75-1.206 6.75 6.75 0 01-.75-1.206 6.75 6.75 0 01-.75-1.206z" />
                                    </svg>
                                    <input type="tel" id="phoneInput" placeholder="+225 07 00 00 00" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="emailInput">Email (facultatif)</label>
                                <div class="input-with-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    <input type="email" id="emailInput" placeholder="nom@exemple.com" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notesInput">Remarques ou préférences</label>
                            <textarea id="notesInput" placeholder="Ex: Style de Nail art souhaité, événements..." rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Valider ma demande de rendez-vous</button>

                    </form>
                </div>

                <div id="successStep" style="display: none;">
                    <div class="success-screen">
                        <div class="success-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3>Demande reçue !</h3>
                            <p>
                                Merci <strong id="successName">—</strong>, votre rendez-vous pour
                                <strong id="successService">—</strong> le
                                <strong id="successDate">—</strong> à <strong id="successTime">—</strong>
                                a été pré-enregistré.
                            </p>
                        </div>
                        <div class="summary-box" id="summaryBox">
                            <div class="summary-row">
                                <span class="label">Service :</span>
                                <span class="value" id="summaryService">—</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Date &amp; Heure :</span>
                                <span class="value" id="summaryDateTime">—</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Téléphone :</span>
                                <span class="value" id="summaryPhone">—</span>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="#" id="whatsappLink" target="_blank" rel="noopener noreferrer" class="btn-whatsapp">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                </svg>
                                Confirmer directement sur WhatsApp
                            </a>
                            <button class="btn-close-modal" id="closeSuccessBtn">Fermer la fenêtre</button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- ============================================================
    JAVASCRIPT
    ============================================================ -->
    <script>
        (function() {
            'use strict';

            // =========================================================
            // 1. FILTRES DES SERVICES
            // =========================================================
            const filterBtns = document.querySelectorAll('.tab-btn');
            const serviceCards = document.querySelectorAll('.service-card');

            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    // Retirer la classe active de tous les boutons
                    filterBtns.forEach(function(b) {
                        b.classList.remove('active');
                    });
                    btn.classList.add('active');

                    const filter = btn.dataset.filter;

                    serviceCards.forEach(function(card) {
                        if (filter === 'all' || card.dataset.category === filter) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            // =========================================================
            // 2. NEWSLETTER
            // =========================================================
            const subscribeForm = document.getElementById('subscribeForm');
            const subscribeEmail = document.getElementById('subscribeEmail');
            const subscribeSuccess = document.getElementById('subscribeSuccess');

            subscribeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = subscribeEmail.value.trim();
                if (email) {
                    subscribeSuccess.style.display = 'flex';
                    subscribeForm.style.display = 'none';
                    setTimeout(function() {
                        subscribeSuccess.style.display = 'none';
                        subscribeForm.style.display = 'flex';
                        subscribeEmail.value = '';
                    }, 4000);
                }
            });

            // =========================================================
            // 3. MODAL DE RÉSERVATION
            // =========================================================
            const modal = document.getElementById('bookingModal');
            const openBtn = document.getElementById('openBookingBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const closeSuccessBtn = document.getElementById('closeSuccessBtn');

            const formStep = document.getElementById('formStep');
            const successStep = document.getElementById('successStep');

            const bookingForm = document.getElementById('bookingForm');

            const serviceSelect = document.getElementById('serviceSelect');
            const dateInput = document.getElementById('dateInput');
            const timeSelect = document.getElementById('timeSelect');
            const fullNameInput = document.getElementById('fullNameInput');
            const phoneInput = document.getElementById('phoneInput');
            const emailInput = document.getElementById('emailInput');
            const notesInput = document.getElementById('notesInput');

            // Fonction pour obtenir le nom du service
            function getServiceName(id) {
                const map = {
                    'ongles-gel': 'Pose d\'ongles en gel (1h30)',
                    'capsules': 'Capsules & Nail Art (2h)',
                    'maquillage-mariee': 'Maquillage Mariée (2h)',
                    'maquillage-soiree': 'Maquillage Soirée (1h)',
                    'formation-ongles': 'Formation Prothésie Ongulaire (5 jours)',
                    'formation-maquillage': 'Formation Maquillage Pro (4 jours)',
                    'forfait-decouverte': 'Forfait Découverte - 50€',
                    'forfait-prestige': 'Forfait Prestige - 120€',
                    'forfait-formation': 'Forfait Formation Complète - 450€'
                };
                return map[id] || 'Prestation FemiEmpire';
            }

            function formatDate(dateStr) {
                if (!dateStr) return 'bientôt';
                const parts = dateStr.split('-');
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }

            function resetForm() {
                bookingForm.reset();
                serviceSelect.value = 'ongles-gel';
                timeSelect.value = '10:30';
                dateInput.value = '';
                fullNameInput.value = '';
                phoneInput.value = '';
                emailInput.value = '';
                notesInput.value = '';
            }

            function closeModal() {
                modal.classList.remove('active');
                formStep.style.display = 'block';
                successStep.style.display = 'none';
                resetForm();
                document.body.style.overflow = '';
            }

            function openModal() {
                modal.classList.add('active');
                formStep.style.display = 'block';
                successStep.style.display = 'none';
                document.body.style.overflow = 'hidden';
            }

            // Fonction globale pour ouvrir le modal avec pré-sélection
            window.openBooking = function(serviceId) {
                if (serviceId) {
                    const option = serviceSelect.querySelector('option[value="' + serviceId + '"]');
                    if (option) {
                        serviceSelect.value = serviceId;
                    }
                }
                openModal();
            };

            // Gestion du formulaire
            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const service = serviceSelect.value;
                const serviceName = getServiceName(service);
                const date = dateInput.value;
                const time = timeSelect.value;
                const fullName = fullNameInput.value.trim();
                const phone = phoneInput.value.trim();
                const email = emailInput.value.trim();
                const notes = notesInput.value.trim();

                if (!fullName || !phone || !date) {
                    alert('Veuillez remplir tous les champs obligatoires (Nom, Téléphone, Date).');
                    return;
                }

                document.getElementById('successName').textContent = fullName;
                document.getElementById('successService').textContent = serviceName;
                document.getElementById('successDate').textContent = formatDate(date);
                document.getElementById('successTime').textContent = time;

                document.getElementById('summaryService').textContent = serviceName;
                document.getElementById('summaryDateTime').textContent = formatDate(date) + ' - ' + time;
                document.getElementById('summaryPhone').textContent = phone;

                const whatsappMsg = encodeURIComponent(
                    'Bonjour FemiEmpire, je souhaite confirmer mon rendez-vous pour: ' +
                    serviceName + ' le ' + formatDate(date) + ' à ' + time + '.\n' +
                    'Nom: ' + fullName + '\n' +
                    'Téléphone: ' + phone + (email ? '\nEmail: ' + email : '') +
                    (notes ? '\nRemarques: ' + notes : '')
                );
                document.getElementById('whatsappLink').href =
                    'https://wa.me/22507070707?text=' + whatsappMsg;

                formStep.style.display = 'none';
                successStep.style.display = 'block';
            });

            // Ouverture / Fermeture
            if (openBtn) {
                openBtn.addEventListener('click', function() {
                    openModal();
                });
            }

            closeModalBtn.addEventListener('click', closeModal);
            closeSuccessBtn.addEventListener('click', closeModal);

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal();
                }
            });

            // Pré-sélection depuis l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const preset = urlParams.get('service');
            if (preset) {
                const option = serviceSelect.querySelector('option[value="' + preset + '"]');
                if (option) {
                    serviceSelect.value = preset;
                }
            }

        })();
    </script>

</body>

</html>