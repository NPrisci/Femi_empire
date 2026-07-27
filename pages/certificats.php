<?php
// certificats.php - Page des certificats avec Dompdf corrigé

require_once __DIR__ . '/../config/database.php';

// Charger l'autoload de Composer pour Dompdf
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Démarrer la session si elle n'est pas active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$success = '';
$error = '';

try {
    $pdo = getDB();
    
    // Récupérer les infos de l'utilisateur
    $stmt = $pdo->prepare('
        SELECT id, prenom, nom, email, telephone, role, created_at
        FROM utilisateurs
        WHERE id = ?
    ');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: ?page=login');
        exit;
    }

    // Récupérer les formations terminées (progression = 100%) avec les détails
    $stmt = $pdo->prepare('
        SELECT 
            f.id as formation_id,
            f.titre,
            f.description,
            f.categorie,
            f.duree,
            c.id as commande_id,
            c.reference as commande_reference,
            c.progression,
            c.modules_done,
            c.date_creation as date_commande,
            c.date_obtention as date_obtention
        FROM commandes c
        INNER JOIN formations f ON f.id = c.formation_id
        WHERE c.utilisateur_id = ? 
        AND c.status = "payee"
        AND c.progression >= 100
        ORDER BY c.date_creation DESC
    ');
    $stmt->execute([$userId]);
    $certificats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $nbCertificats = count($certificats);

} catch (PDOException $e) {
    error_log("ERREUR CONNEXION: " . $e->getMessage());
    die('Erreur connexion : ' . $e->getMessage());
}

// Fonctions utilitaires
function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function initiales(string $prenom, string $nom): string {
    return strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}

function formatDateFr(string $date): string {
    if (empty($date)) return '—';
    $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
             'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $ts = strtotime($date);
    return (int)date('j', $ts) . ' ' . $mois[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

function generateCertifNumber($formationId, $userId, $date): string {
    return 'CERT-' . date('Y') . '-' . str_pad($formationId, 4, '0', STR_PAD_LEFT) . 
           '-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '-' . date('md', strtotime($date));
}

// ================================================
// TRAITEMENT DU TÉLÉCHARGEMENT PDF
// ================================================

if (isset($_GET['action']) && $_GET['action'] === 'download_pdf' && isset($_GET['id'])) {
    // Nettoyer le buffer de sortie
    ob_end_clean();
    
    $formationId = (int)$_GET['id'];
    
    try {
        // Récupérer les données du certificat
        $stmt = $pdo->prepare('
            SELECT 
                f.id as formation_id,
                f.titre,
                f.description,
                f.categorie,
                f.duree,
                c.date_creation as date_obtention
            FROM commandes c
            INNER JOIN formations f ON f.id = c.formation_id
            WHERE c.utilisateur_id = ? 
            AND f.id = ?
            AND c.status = "payee"
            AND c.progression >= 100
        ');
        $stmt->execute([$userId, $formationId]);
        $certificat = $stmt->fetch();
        
        if (!$certificat) {
            die('Certificat non trouvé ou non disponible.');
        }
        
        $certifNumber = generateCertifNumber($formationId, $userId, $certificat['date_obtention']);
        $dateFormatted = formatDateFr($certificat['date_obtention']);
        
        // Générer le HTML du certificat
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Certificat - ' . h($certificat['titre']) . '</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: "DejaVu Sans", sans-serif;
                    background: #faf7f4;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    padding: 40px;
                    margin: 0;
                }
                .certificat-wrapper {
                    max-width: 800px;
                    width: 100%;
                    margin: 0 auto;
                }
                .certif-border {
                    border: 3px double #c9a96e;
                    border-radius: 20px;
                    padding: 50px 40px;
                    background: #fefcf9;
                    position: relative;
                    text-align: center;
                }
                .certif-border::before {
                    content: "✦ ✦ ✦";
                    position: absolute;
                    top: -12px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: #fefcf9;
                    padding: 0 16px;
                    color: #c9a96e;
                    font-size: 12px;
                    letter-spacing: 4px;
                }
                .certif-preview-title {
                    font-size: 28px;
                    font-weight: 700;
                    color: #a8655a;
                    margin-bottom: 8px;
                }
                .certif-preview-subtitle {
                    font-size: 13px;
                    color: #9a8a85;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    margin-bottom: 24px;
                }
                .certif-preview-name {
                    font-size: 36px;
                    font-weight: 700;
                    color: #2c2420;
                    margin: 16px 0 8px;
                }
                .certif-preview-formation {
                    font-size: 18px;
                    color: #c9877a;
                    margin-bottom: 16px;
                }
                .certif-preview-desc {
                    font-size: 14px;
                    color: #9a8a85;
                    max-width: 500px;
                    margin: 0 auto 20px;
                    line-height: 1.6;
                }
                .certif-meta-info {
                    margin: 16px 0;
                    display: flex;
                    justify-content: center;
                    gap: 24px;
                    flex-wrap: wrap;
                }
                .certif-meta-info span {
                    font-size: 12px;
                    color: #9a8a85;
                }
                .certif-preview-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding-top: 20px;
                    border-top: 1px solid #e8ddd8;
                    margin-top: 20px;
                    font-size: 12px;
                    color: #9a8a85;
                }
                .certif-preview-footer .signature {
                    font-style: italic;
                    color: #c9877a;
                    font-size: 16px;
                }
                .certif-preview-footer .footer-left {
                    text-align: left;
                }
                .certif-preview-footer .footer-right {
                    text-align: right;
                }
                .certif-preview-footer .footer-label {
                    font-size: 10px;
                    color: #9a8a85;
                }
                .certif-preview-footer .footer-value {
                    font-weight: 500;
                }
            </style>
        </head>
        <body>
            <div class="certificat-wrapper">
                <div class="certif-border">
                    <div class="certif-preview-title">🎓 CERTIFICAT DE RÉUSSITE</div>
                    <div class="certif-preview-subtitle">FEMI Fairy Finger — Formation professionnelle</div>
                    
                    <div class="certif-preview-name">' . h($user['prenom'] . ' ' . $user['nom']) . '</div>
                    <div class="certif-preview-formation">« ' . h($certificat['titre']) . ' »</div>
                    <div class="certif-preview-desc">' . h($certificat['description'] ?? 'Formation complète en onglerie professionnelle') . '</div>
                    
                    <div class="certif-meta-info">
                        <span>📅 ' . $dateFormatted . '</span>
                        <span>⏱ ' . ($certificat['duree'] ?? 0) . ' heures</span>
                        <span>#' . h($certifNumber) . '</span>
                    </div>

                    <div class="certif-preview-footer">
                        <div class="footer-left">
                            <div class="footer-label">Délivré par</div>
                            <div class="footer-value">FEMI Fairy Finger</div>
                        </div>
                        <div class="footer-right">
                            <div class="footer-label">Signature</div>
                            <div class="signature">✨ La Direction</div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
        
        // Configuration de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('debugKeepTemp', false);
        $options->set('isCachedRemoteEnabled', false);
        $options->set('fontDir', sys_get_temp_dir());
        $options->set('fontCache', sys_get_temp_dir());
        $options->set('tempDir', sys_get_temp_dir());
        $options->set('logOutputFile', sys_get_temp_dir() . '/dompdf_log.txt');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Nettoyer le buffer avant d'envoyer le PDF
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Envoyer les en-têtes pour le téléchargement
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Certificat_' . str_replace(' ', '_', $certificat['titre']) . '_' . date('Y-m-d') . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Envoyer le PDF
        echo $dompdf->output();
        exit;
        
    } catch (Exception $e) {
        error_log("Erreur génération PDF: " . $e->getMessage());
        die('Erreur lors de la génération du PDF : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEMI Fairy Finger — Mes Certificats</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* ===== STYLES GLOBAUX ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --cream: #faf7f4;
            --blush: #f0e6df;
            --rose: #c9877a;
            --rose-dark: #a8655a;
            --charcoal: #2c2420;
            --muted: #9a8a85;
            --white: #ffffff;
            --border: #e8ddd8;
            --gold: #c9a96e;
            --gold-light: #e8d5a3;
            --shadow: 0 8px 32px rgba(0,0,0,0.12);
            --green: #1a7a45;
            --green-light: #d4f5e4;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            min-height: 100vh;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rose), var(--gold));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            color: white;
            font-weight: 600;
            border: 2px solid var(--border);
            cursor: pointer;
        }

        /* ===== LAYOUT ===== */
        .page-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 32px 80px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 32px;
            align-items: start;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: sticky;
            top: 80px;
        }

        .profile-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: linear-gradient(135deg, var(--blush), #f5e8e0);
        }

        .avatar-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 16px;
            margin-top: 20px;
            z-index: 1;
        }

        .avatar-main {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rose), var(--gold));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            color: white;
            font-weight: 600;
            border: 4px solid var(--white);
            box-shadow: 0 4px 20px rgba(201, 135, 122, .3);
        }

        .profile-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .profile-role {
            font-size: .78rem;
            color: var(--rose);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 16px;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 24px;
            padding: 16px 0;
            border-top: 1px solid var(--border);
        }

        .stat {
            text-align: center;
        }

        .stat-val {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 600;
            display: block;
        }

        .stat-lbl {
            font-size: .72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .side-nav {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .side-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            font-size: .85rem;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            border-left: 3px solid transparent;
        }

        .side-nav-item:not(:last-child) {
            border-bottom: 1px solid var(--border);
        }

        .side-nav-item:hover {
            background: var(--cream);
            color: var(--charcoal);
        }

        .side-nav-item.active {
            background: #fef4f2;
            color: var(--rose);
            border-left-color: var(--rose);
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            opacity: .6;
            flex-shrink: 0;
        }

        .side-nav-item.active .nav-icon {
            opacity: 1;
        }

        .logout-btn {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 13px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .83rem;
            font-weight: 500;
            color: #c0392b;
            cursor: pointer;
            transition: all .2s;
            width: 100%;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #fff5f5;
            border-color: #f5c6c0;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* ===== HEADER ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            font-weight: 600;
        }

        .page-title em {
            color: var(--rose);
            font-style: italic;
        }

        .header-stats {
            display: flex;
            gap: 24px;
            background: var(--white);
            padding: 12px 24px;
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;
            display: block;
            color: var(--rose);
        }

        .stat-label {
            font-size: .72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .stat-divider {
            width: 1px;
            background: var(--border);
        }

        /* ===== GRILLE DES CERTIFICATS ===== */
        .certificats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .certificat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            transition: all .3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .certificat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
            border-color: var(--gold);
        }

        .certificat-card::before {
            content: '🎓';
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 4rem;
            opacity: .08;
            transform: rotate(15deg);
        }

        .certif-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 16px;
        }

        .certif-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .certif-meta {
            font-size: .82rem;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .certif-badge {
            display: inline-block;
            background: var(--green-light);
            color: var(--green);
            font-size: .7rem;
            font-weight: 600;
            border-radius: 20px;
            padding: 3px 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .certif-date {
            font-size: .78rem;
            color: var(--muted);
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .certif-number {
            font-size: .7rem;
            color: var(--muted);
            font-family: monospace;
            margin-top: 4px;
        }

        .view-btn {
            background: var(--charcoal);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: .78rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
        }

        .view-btn:hover {
            background: var(--rose);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 16px;
            display: block;
        }

        .btn-primary {
            display: inline-block;
            background: var(--rose);
            color: white;
            padding: 12px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all .2s;
            margin-top: 16px;
        }

        .btn-primary:hover {
            background: var(--rose-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(201, 135, 122, .35);
        }

        /* ===== MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn .3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleUp {
            from { transform: scale(.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-content {
            background: var(--white);
            border-radius: 24px;
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 40px;
            position: relative;
            animation: scaleUp .3s ease;
            box-shadow: 0 24px 80px rgba(0,0,0,0.3);
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 20px;
            background: var(--cream);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: #fde8e5;
            transform: rotate(90deg);
        }

        /* ===== CERTIFICAT PREVIEW ===== */
        .certificat-preview {
            text-align: center;
            padding: 20px 0;
        }

        .certificat-preview .certif-border {
            border: 3px double var(--gold);
            border-radius: 20px;
            padding: 40px 30px;
            background: linear-gradient(135deg, #fefcf9, #faf7f4);
            position: relative;
        }

        .certificat-preview .certif-border::before {
            content: '✦ ✦ ✦';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--white);
            padding: 0 16px;
            color: var(--gold);
            font-size: .8rem;
            letter-spacing: 4px;
        }

        .certif-preview-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--rose-dark);
            margin-bottom: 8px;
        }

        .certif-preview-subtitle {
            font-size: .85rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .15em;
            margin-bottom: 24px;
        }

        .certif-preview-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            font-weight: 600;
            color: var(--charcoal);
            margin: 16px 0 8px;
        }

        .certif-preview-formation {
            font-size: 1.2rem;
            color: var(--rose);
            margin-bottom: 16px;
        }

        .certif-preview-desc {
            font-size: .9rem;
            color: var(--muted);
            max-width: 500px;
            margin: 0 auto 20px;
            line-height: 1.6;
        }

        .certif-preview-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            margin-top: 20px;
            font-size: .78rem;
            color: var(--muted);
        }

        .certif-preview-footer .signature {
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
            color: var(--rose);
            font-size: 1.1rem;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .btn-download {
            background: var(--rose);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 32px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            font-size: .9rem;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-download:hover {
            background: var(--rose-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(201, 135, 122, .35);
        }

        .btn-close-modal {
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 32px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            font-size: .9rem;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-close-modal:hover {
            background: var(--border);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .page-wrap {
                grid-template-columns: 1fr;
                padding: 24px 16px 60px;
            }

            .sidebar {
                position: static;
            }

            .navbar {
                padding: 0 20px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-stats {
                width: 100%;
                justify-content: space-around;
            }

            .certificats-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                padding: 24px 16px;
            }

            .certificat-preview .certif-border {
                padding: 24px 16px;
            }

            .certif-preview-name {
                font-size: 2rem;
            }

            .certif-preview-title {
                font-size: 1.6rem;
            }

            .certif-preview-footer {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }

            .modal-actions {
                flex-direction: column;
            }

            .btn-download, .btn-close-modal {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- ===== PAGE ===== -->
    <div class="page-wrap">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <div class="profile-card">
                <div class="avatar-wrap">
                    <div class="avatar-main"><?= h(initiales($user['prenom'], $user['nom'])) ?></div>
                </div>
                <div class="profile-name"><?= h($user['prenom'] . ' ' . $user['nom']) ?></div>
                <div class="profile-role">
                    <?= $user['role'] === 'admin' ? '⚙ Administrateur' : '✧ Membre' ?>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-val"><?= $nbCertificats ?></span>
                        <span class="stat-lbl">Certificats</span>
                    </div>
                    <div class="stat">
                        <span class="stat-val">🎓</span>
                        <span class="stat-lbl">Validés</span>
                    </div>
                    <div class="stat">
                        <span class="stat-val"><?= date('Y', strtotime($user['created_at'])) ?></span>
                        <span class="stat-lbl">Depuis</span>
                    </div>
                </div>
            </div>

            <nav class="side-nav">
                <a href="?page=dashboard" class="side-nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-20v8h8V1h-8z" />
                    </svg>
                    Tableau de bord
                </a>
                <a href="?page=mesformations" class="side-nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                    </svg>
                    Mes formations
                </a>
                <a href="?page=certificats" class="side-nav-item active">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z" />
                    </svg>
                    Certificats
                </a>
                <a href="?page=profile" class="side-nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                    </svg>
                    Mon profil
                </a>
            </nav>

            <a href="?action=logout" class="logout-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#c0392b">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                </svg>
                Se déconnecter
            </a>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="main-content">

            <!-- HEADER -->
            <div class="page-header">
                <h1 class="page-title">Mes <em>certificats</em></h1>
                <div class="header-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?= $nbCertificats ?></span>
                        <span class="stat-label">Certificats obtenus</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $nbCertificats > 0 ? '🎓' : '📚' ?></span>
                        <span class="stat-label">Formations validées</span>
                    </div>
                </div>
            </div>

            <!-- ===== GRILLE DES CERTIFICATS ===== -->
            <?php if (empty($certificats)): ?>
                <div class="empty-state">
                    <span class="empty-icon">📜</span>
                    <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; margin-bottom:8px;">Aucun certificat</h2>
                    <p>Vous n'avez pas encore terminé de formation.<br>Terminez une formation pour obtenir votre certificat !</p>
                    <a href="?page=mesformations" class="btn-primary">Voir mes formations</a>
                </div>
            <?php else: ?>
                <div class="certificats-grid">
                    <?php foreach ($certificats as $certif): 
                        $certifNumber = generateCertifNumber($certif['formation_id'], $userId, $certif['date_obtention']);
                    ?>
                        <div class="certificat-card" onclick="openModal(<?= htmlspecialchars(json_encode($certif), ENT_QUOTES) ?>, '<?= h($certifNumber) ?>')">
                            <div class="certif-icon">🎓</div>
                            <h3 class="certif-title"><?= h($certif['titre']) ?></h3>
                            <div class="certif-meta"><?= h(ucfirst($certif['categorie'] ?? 'Formation')) ?></div>
                            <span class="certif-badge">✅ Validé</span>
                            <div class="certif-date">
                                <span>📅 <?= h(formatDateFr($certif['date_obtention'])) ?></span>
                                <button class="view-btn" onclick="event.stopPropagation(); openModal(<?= htmlspecialchars(json_encode($certif), ENT_QUOTES) ?>, '<?= h($certifNumber) ?>')">Voir</button>
                            </div>
                            <div class="certif-number">#<?= h($certifNumber) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- ===== MODAL ===== -->
    <div class="modal-overlay" id="certifModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">✕</button>
            
            <div class="certificat-preview" id="certifPreview">
                <!-- Le contenu sera injecté par JavaScript -->
            </div>

            <div class="modal-actions">
                <a href="#" class="btn-download" id="downloadBtn" target="_blank">
                    📥 Télécharger le PDF
                </a>
                <button class="btn-close-modal" onclick="closeModal()">Fermer</button>
            </div>
        </div>
    </div>

    <!-- ===== SCRIPT ===== -->
    <script>
        let currentCertificat = null;
        let currentCertifNumber = '';
        let currentFormationId = null;

        function openModal(certificat, certifNumber) {
            currentCertificat = certificat;
            currentCertifNumber = certifNumber;
            currentFormationId = certificat.formation_id;

            const preview = document.getElementById('certifPreview');
            preview.innerHTML = `
                <div class="certif-border">
                    <div class="certif-preview-title">🎓 CERTIFICAT DE RÉUSSITE</div>
                    <div class="certif-preview-subtitle">FEMI Fairy Finger — Formation professionnelle</div>
                    
                    <div class="certif-preview-name"><?= h($user['prenom']) ?> <?= h($user['nom']) ?></div>
                    <div class="certif-preview-formation">« ${certificat.titre} »</div>
                    <div class="certif-preview-desc">${certificat.description || 'Formation complète en onglerie professionnelle'}</div>
                    
                    <div style="margin: 16px 0; display:flex; justify-content:center; gap:24px; flex-wrap:wrap;">
                        <span style="font-size:.78rem; color:var(--muted);">📅 ${formatDate(certificat.date_obtention)}</span>
                        <span style="font-size:.78rem; color:var(--muted);">⏱ ${certificat.duree || 0} heures</span>
                        <span style="font-size:.78rem; color:var(--muted);">#${certifNumber}</span>
                    </div>

                    <div class="certif-preview-footer">
                        <div>
                            <div style="font-size:.7rem; color:var(--muted);">Délivré par</div>
                            <div style="font-weight:500;">FEMI Fairy Finger</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:.7rem; color:var(--muted);">Signature</div>
                            <div class="signature">✨ La Direction</div>
                        </div>
                    </div>
                </div>
            `;

            const downloadBtn = document.getElementById('downloadBtn');
            downloadBtn.href = `?page=certificats&action=download_pdf&id=${certificat.formation_id}`;

            document.getElementById('certifModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('certifModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        function formatDate(dateStr) {
            const months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin',
                           'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
            const d = new Date(dateStr);
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        document.getElementById('certifModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        console.log('=== 🐛 CERTIFICATS ===');
        console.log('👤 Utilisateur:', <?= json_encode($user) ?>);
        console.log('📜 Certificats:', <?= json_encode($certificats) ?>);
        console.log('📊 Nombre:', <?= json_encode($nbCertificats) ?>);
        console.log('=== FIN DEBUG ===');
    </script>

</body>
</html>