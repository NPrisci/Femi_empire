<?php
// admin_dashboard.php - Tableau de bord complet pour salon de beauté


require_once __DIR__ . '/../../../config/database.php';
$pdo = getDB();

// ========== STATISTIQUES ==========

// Nombre total de formations actives
$totalFormations = $pdo->query("SELECT COUNT(*) FROM formations WHERE statut = 'active'")->fetchColumn();

// Liste des formations
$formations = $pdo->query("
    SELECT f.*, COUNT(r.id) as total_reservations
    FROM formations f
    LEFT JOIN rendez_vous r ON f.id = r.formation_id
    WHERE f.statut = 'active'
    GROUP BY f.id
    ORDER BY f.categorie, f.titre
")->fetchAll();

// Statistiques par catégorie de formation
$statsCategorie = $pdo->query("
    SELECT 
        categorie,
        COUNT(*) as total,
        SUM(prix) as chiffre_affaires_potentiel
    FROM formations 
    WHERE statut = 'active'
    GROUP BY categorie
")->fetchAll();

// Nombre total de clients
$totalClients = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client'")->fetchColumn();
$clientsActifs = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client' AND statut = 'actif'")->fetchColumn();
$clientsSuspendus = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client' AND statut = 'suspendu'")->fetchColumn();
$clientsEnAttente = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client' AND statut = 'en_attente'")->fetchColumn();

// Liste des clients
$clients = $pdo->query("
    SELECT 
        u.*,
        COUNT(r.id) as total_rdv,
        SUM(r.montant_total) as total_depenses,
        MAX(r.date_rdv) as derniere_visite
    FROM utilisateurs u
    LEFT JOIN rendez_vous r ON u.id = r.client_id
    WHERE u.role = 'client'
    GROUP BY u.id
    ORDER BY u.date_inscription DESC
")->fetchAll();

// Nombre total de rendez-vous
$totalRdvs = $pdo->query("SELECT COUNT(*) FROM rendez_vous")->fetchColumn();
$rdvEnAttente = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'en_attente'")->fetchColumn();
$rdvConfirme = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'confirme'")->fetchColumn();
$rdvTermine = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'termine'")->fetchColumn();

// Liste des rendez-vous
$rendezVous = $pdo->query("
    SELECT 
        r.*,
        CONCAT(u.prenom, ' ', u.nom) as client_nom,
        u.telephone as client_tel,
        u.email as client_email,
        f.titre as formation_titre,
        f.categorie as formation_categorie,
        f.prix as formation_prix
    FROM rendez_vous r
    JOIN utilisateurs u ON r.client_id = u.id
    JOIN formations f ON r.formation_id = f.id
    ORDER BY r.date_rdv ASC, r.heure_rdv ASC
")->fetchAll();

// Revenus totaux (prestations confirmées + terminées)
$revenuTotal = $pdo->query("SELECT COALESCE(SUM(montant_total), 0) FROM rendez_vous WHERE statut IN ('termine', 'confirme')")->fetchColumn();

// Chiffre d'affaires par mois (6 derniers mois)
$caMensuel = $pdo->query("
    SELECT 
        DATE_FORMAT(date_rdv, '%b') as mois,
        SUM(montant_total) as total,
        COUNT(*) as nb_rdv
    FROM rendez_vous 
    WHERE date_rdv >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    AND statut IN ('termine', 'confirme')
    GROUP BY MONTH(date_rdv)
    ORDER BY date_rdv ASC
")->fetchAll();

// Top formations les plus populaires
$topFormations = $pdo->query("
    SELECT 
        f.titre,
        f.categorie,
        COUNT(r.id) as nombre_reservations,
        SUM(r.montant_total) as revenu
    FROM formations f
    JOIN rendez_vous r ON f.id = r.formation_id
    WHERE r.statut IN ('termine', 'confirme')
    GROUP BY f.id
    ORDER BY nombre_reservations DESC
    LIMIT 5
")->fetchAll();

// ========== GESTION DES ACTIONS POST ==========
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // AJOUTER UNE FORMATION
    if ($action === 'add_formation') {
        $titre = $_POST['titre'] ?? '';
        $categorie = $_POST['categorie'] ?? 'onglerie';
        $duree = $_POST['duree'] ?? 60;
        $prix = $_POST['prix'] ?? 0;
        $description = $_POST['description'] ?? '';
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO formations (titre, categorie, duree, prix, description, statut) 
                VALUES (?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$titre, $categorie, $duree, $prix, $description]);
            $message = "Formation ajoutée avec succès !";
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } catch(Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
    
    // MODIFIER UNE FORMATION
    elseif ($action === 'edit_formation') {
        $id = $_POST['formation_id'] ?? 0;
        $titre = $_POST['titre'] ?? '';
        $categorie = $_POST['categorie'] ?? 'onglerie';
        $duree = $_POST['duree'] ?? 60;
        $prix = $_POST['prix'] ?? 0;
        $description = $_POST['description'] ?? '';
        
        try {
            $stmt = $pdo->prepare("
                UPDATE formations 
                SET titre = ?, categorie = ?, duree = ?, prix = ?, description = ? 
                WHERE id = ?
            ");
            $stmt->execute([$titre, $categorie, $duree, $prix, $description, $id]);
            $message = "Formation modifiée avec succès !";
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } catch(Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
    
    // SUPPRIMER UNE FORMATION (désactiver)
    elseif ($action === 'delete_formation') {
        $id = $_POST['formation_id'] ?? 0;
        try {
            $stmt = $pdo->prepare("UPDATE formations SET statut = 'inactive' WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Formation supprimée avec succès !";
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } catch(Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
    
    // MODIFIER STATUT CLIENT
    elseif ($action === 'edit_client_status') {
        $id = $_POST['user_id'] ?? 0;
        $statut = $_POST['statut'] ?? 'actif';
        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET statut = ? WHERE id = ? AND role = 'client'");
            $stmt->execute([$statut, $id]);
            $message = "Statut client modifié avec succès !";
            header("Location: " . $_SERVER['PHP_SELF'] . "?tab=clients&success=1");
            exit();
        } catch(Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
    
    // MODIFIER CLIENT
    elseif ($action === 'edit_client') {
        $id = $_POST['user_id'] ?? 0;
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        try {
            $stmt = $pdo->prepare("
                UPDATE utilisateurs 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, notes = ? 
                WHERE id = ? AND role = 'client'
            ");
            $stmt->execute([$nom, $prenom, $email, $telephone, $notes, $id]);
            $message = "Client modifié avec succès !";
            header("Location: " . $_SERVER['PHP_SELF'] . "?tab=clients&success=1");
            exit();
        } catch(Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
    
    // MODIFIER STATUT RENDEZ-VOUS
    elseif ($action === 'edit_rdv_status') {
        $id = $_POST['rdv_id'] ?? 0;
        $statut = $_POST['statut'] ?? 'en_attente';
        try {
            $stmt = $pdo->prepare("UPDATE rendez_vous SET statut = ? WHERE id = ?");
            $stmt->execute([$statut, $id]);
            $message = "Statut du rendez-vous modifié !";
            header("Location: " . $_SERVER['PHP_SELF'] . "?tab=rendezvous&success=1");
            exit();
        } catch(Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}

$activeTab = $_GET['tab'] ?? 'formations';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Salon de Beauté & Onglerie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #e83e8c;
            --primary-dark: #c8236c;
            --secondary: #6c5ce7;
            --success: #00b894;
            --danger: #d63031;
            --warning: #fdcb6e;
            --info: #0984e3;
        }
        
        body {
            background: linear-gradient(135deg, #fdfbfb 0%, #f7f9fc 100%);
            font-family: 'Segoe UI', 'Poppins', system-ui, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #2d1b4e 0%, #1a0f2e 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .metric-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0 0.25rem;
            color: #2d1b4e;
        }
        
        .metric-label {
            color: #6c757d;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .panel {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #f8e5f0;
            padding-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d1b4e;
            margin: 0;
        }
        
        .section-title i {
            color: var(--primary);
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            border-radius: 12px;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #2d1b4e;
            background: #faf5f8;
        }
        
        .badge-status {
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-actif { background: #d4edda; color: #155724; }
        .badge-suspendu { background: #f8d7da; color: #721c24; }
        .badge-en_attente { background: #fff3cd; color: #856404; }
        .badge-onglerie { background: #fce4ec; color: #c2185b; }
        .badge-visage { background: #e8f5e9; color: #2e7d32; }
        .badge-cheveux { background: #fff3e0; color: #ef6c00; }
        .badge-termine { background: #d4edda; color: #155724; }
        .badge-confirme { background: #cce5ff; color: #004085; }
        .badge-annule { background: #f8d7da; color: #721c24; }
        
        .tabs {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: white;
            border: 2px solid #f0dbe8;
            border-radius: 50px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .tab-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: transparent;
            color: white;
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .avatar-circle {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
        }
        
        .modal-content {
            border-radius: 20px;
        }
        
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #e0d4dc;
            padding: 0.7rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(232, 62, 140, 0.25);
        }
        
        .rdv-card {
            background: #faf5f8;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }
        
        .rdv-card:hover {
            background: #fdf8fb;
            transform: translateX(5px);
        }
        
        .btn-action {
            border-radius: 10px;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .panel-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .metric-value {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-dark">
    <div class="container-fluid px-4 py-2">
        <a class="navbar-brand" href="#">
            <i class="bi bi-scissors fs-4"></i>
            <strong>Salon Beauty & Onglerie</strong>
            <small class="ms-2 opacity-75">Admin Dashboard</small>
        </a>
        <div class="d-flex gap-3 align-items-center">
            <span class="text-white-50">
                <i class="bi bi-calendar3"></i> <?= date('l d/m/Y') ?>
            </span>
            <button class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="location.reload()">
                <i class="bi bi-arrow-repeat"></i> Rafraîchir
            </button>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    <!-- Alertes -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Opération effectuée avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Cartes métriques -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="metric-card" onclick="document.querySelector('[data-tab=\'formations\']').click()">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="metric-label"><i class="bi bi-star-fill text-warning"></i> Formations</span>
                        <div class="metric-value"><?= $totalFormations ?></div>
                        <small class="text-muted">prestations disponibles</small>
                    </div>
                    <div class="metric-icon" style="background: #fce4ec;">
                        <i class="bi bi-scissors" style="color: var(--primary); font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="metric-card" onclick="document.querySelector('[data-tab=\'clients\']').click()">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="metric-label"><i class="bi bi-heart-fill text-danger"></i> Clients</span>
                        <div class="metric-value"><?= $totalClients ?></div>
                        <small>
                            <span class="text-success"><?= $clientsActifs ?> actifs</span>
                            <span class="text-muted mx-1">|</span>
                            <span class="text-warning"><?= $clientsEnAttente ?> en attente</span>
                        </small>
                    </div>
                    <div class="metric-icon" style="background: #e8f5e9;">
                        <i class="bi bi-people" style="color: var(--success); font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="metric-card" onclick="document.querySelector('[data-tab=\'rendezvous\']').click()">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="metric-label"><i class="bi bi-calendar-check"></i> Rendez-vous</span>
                        <div class="metric-value"><?= $totalRdvs ?></div>
                        <small>
                            <span class="text-info"><?= $rdvConfirme ?> confirmés</span>
                            <span class="text-muted mx-1">|</span>
                            <span class="text-warning"><?= $rdvEnAttente ?> en attente</span>
                        </small>
                    </div>
                    <div class="metric-icon" style="background: #e3f2fd;">
                        <i class="bi bi-calendar-week" style="color: var(--info); font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="metric-label"><i class="bi bi-graph-up"></i> Chiffre d'affaires</span>
                        <div class="metric-value"><?= number_format($revenuTotal, 0, ',', ' ') ?> FCFA</div>
                        <small class="text-muted">prestations confirmées</small>
                    </div>
                    <div class="metric-icon" style="background: #fff3e0;">
                        <i class="bi bi-currency-dollar" style="color: var(--warning); font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique CA mensuel -->
    <div class="panel mb-4">
        <div class="panel-header">
            <div>
                <h3 class="section-title">
                    <i class="bi bi-graph-up"></i> Évolution du Chiffre d'Affaires
                </h3>
                <p class="text-muted mb-0">6 derniers mois</p>
            </div>
        </div>
        <canvas id="caChart" height="80" style="max-height: 300px;"></canvas>
    </div>

    <!-- Onglets -->
    <div class="tabs">
        <button class="tab-btn <?= $activeTab == 'formations' ? 'active' : '' ?>" data-tab="formations" onclick="showTab('formations')">
            <i class="bi bi-book"></i> Formations
        </button>
        <button class="tab-btn <?= $activeTab == 'clients' ? 'active' : '' ?>" data-tab="clients" onclick="showTab('clients')">
            <i class="bi bi-people"></i> Clients
        </button>
        <button class="tab-btn <?= $activeTab == 'rendezvous' ? 'active' : '' ?>" data-tab="rendezvous" onclick="showTab('rendezvous')">
            <i class="bi bi-calendar-check"></i> Rendez-vous
        </button>
        <button class="tab-btn <?= $activeTab == 'stats' ? 'active' : '' ?>" data-tab="stats" onclick="showTab('stats')">
            <i class="bi bi-pie-chart"></i> Statistiques
        </button>
    </div>

    <!-- ============================================ -->
    <!-- SECTION FORMATIONS -->
    <!-- ============================================ -->
    <div id="formations" class="tab-content <?= $activeTab == 'formations' ? 'active' : '' ?>">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="section-title">
                        <i class="bi bi-book"></i> Gestion des Formations
                    </h3>
                    <p class="text-muted mb-0">Ajoutez, modifiez ou supprimez des prestations</p>
                </div>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFormationModal">
                    <i class="bi bi-plus-circle"></i> Nouvelle formation
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Catégorie</th>
                            <th>Durée</th>
                            <th>Prix (FCFA)</th>
                            <th>Réservations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($formations as $formation): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($formation['titre']) ?></strong><br>
                                <small class="text-muted"><?= substr(htmlspecialchars($formation['description']), 0, 50) ?>...</small>
                            </td>
                            <td>
                                <span class="badge-status badge-<?= $formation['categorie'] ?>">
                                    <?= ucfirst($formation['categorie']) ?>
                                </span>
                            </td>
                            <td><?= $formation['duree'] ?> min</td>
                            <td><strong><?= number_format($formation['prix'], 0, ',', ' ') ?></strong></td>
                            <td><?= $formation['total_reservations'] ?? 0 ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1 rounded-pill" onclick='editFormation(<?= json_encode($formation) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="deleteFormation(<?= $formation['id'] ?>, '<?= htmlspecialchars($formation['titre']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SECTION CLIENTS -->
    <!-- ============================================ -->
    <div id="clients" class="tab-content <?= $activeTab == 'clients' ? 'active' : '' ?>">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="section-title">
                        <i class="bi bi-people"></i> Gestion des Clients
                    </h3>
                    <p class="text-muted mb-0">Consultez et modifiez les comptes clients</p>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>RDV</th>
                            <th>Dépenses</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clients as $client): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        <?= strtoupper(substr($client['prenom'], 0, 1)) . strtoupper(substr($client['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($client['email']) ?></td>
                            <td><?= htmlspecialchars($client['telephone'] ?? '-') ?></td>
                            <td>
                                <span class="badge-status <?= $client['statut'] == 'actif' ? 'badge-actif' : ($client['statut'] == 'suspendu' ? 'badge-suspendu' : 'badge-en_attente') ?>">
                                    <?= ucfirst($client['statut']) ?>
                                </span>
                            </td>
                            <td><?= $client['total_rdv'] ?? 0 ?></td>
                            <td><?= number_format($client['total_depenses'] ?? 0, 0, ',', ' ') ?> FCFA</td>
                            <td><?= date('d/m/Y', strtotime($client['date_inscription'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary rounded-pill me-1" onclick='editClient(<?= json_encode($client) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="editClientStatus(<?= $client['id'] ?>, '<?= $client['statut'] ?>')">
                                    <i class="bi bi-shield-check"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SECTION RENDEZ-VOUS -->
    <!-- ============================================ -->
    <div id="rendezvous" class="tab-content <?= $activeTab == 'rendezvous' ? 'active' : '' ?>">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="section-title">
                        <i class="bi bi-calendar-check"></i> Gestion des Rendez-vous
                    </h3>
                    <p class="text-muted mb-0">Consultez et modifiez le statut des rendez-vous</p>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Date & Heure</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rendezVous as $rdv): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($rdv['client_nom']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($rdv['client_tel'] ?? '-') ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($rdv['formation_titre']) ?><br>
                                <small class="text-muted"><?= ucfirst($rdv['formation_categorie']) ?></small>
                            </td>
                            <td>
                                <strong><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?></strong><br>
                                <small><?= date('H:i', strtotime($rdv['heure_rdv'])) ?></small>
                            </td>
                            <td><?= number_format($rdv['montant_total'], 0, ',', ' ') ?> FCFA</td>
                            <td>
                                <span class="badge-status <?= $rdv['statut'] == 'termine' ? 'badge-actif' : ($rdv['statut'] == 'confirme' ? 'badge-confirme' : ($rdv['statut'] == 'annule' ? 'badge-annule' : 'badge-en_attente')) ?>">
                                    <?= ucfirst($rdv['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <select class="form-select form-select-sm rounded-pill" style="width: auto; display: inline-block;" onchange="updateRdvStatus(<?= $rdv['id'] ?>, this.value)">
                                    <option value="en_attente" <?= $rdv['statut'] == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="confirme" <?= $rdv['statut'] == 'confirme' ? 'selected' : '' ?>>Confirmé</option>
                                    <option value="termine" <?= $rdv['statut'] == 'termine' ? 'selected' : '' ?>>Terminé</option>
                                    <option value="annule" <?= $rdv['statut'] == 'annule' ? 'selected' : '' ?>>Annulé</option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SECTION STATISTIQUES -->
    <!-- ============================================ -->
    <div id="stats" class="tab-content <?= $activeTab == 'stats' ? 'active' : '' ?>">
        <div class="row g-4">
            <!-- Top formations -->
            <div class="col-md-6">
                <div class="panel h-100">
                    <div class="panel-header">
                        <h3 class="section-title">
                            <i class="bi bi-trophy"></i> Top 5 des formations
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr><th>Formation</th><th>Catégorie</th><th>Réservations</th><th>Revenu</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($topFormations as $top): ?>
                                <tr>
                                    <td><?= htmlspecialchars($top['titre']) ?></td>
                                    <td><span class="badge-status badge-<?= $top['categorie'] ?>"><?= ucfirst($top['categorie']) ?></span></td>
                                    <td><?= $top['nombre_reservations'] ?></td>
                                    <td><?= number_format($top['revenu'], 0, ',', ' ') ?> FCFA</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Stats par catégorie -->
            <div class="col-md-6">
                <div class="panel h-100">
                    <div class="panel-header">
                        <h3 class="section-title">
                            <i class="bi bi-pie-chart"></i> Répartition par catégorie
                        </h3>
                    </div>
                    <canvas id="categorieChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODALS -->
<!-- ============================================ -->

<!-- Modal Ajouter Formation -->
<div class="modal fade" id="addFormationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle text-primary"></i> Ajouter une formation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_formation">
                    <div class="mb-3">
                        <label class="form-label">Nom du service *</label>
                        <input type="text" name="titre" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catégorie *</label>
                            <select name="categorie" class="form-select" required>
                                <option value="onglerie">Onglerie</option>
                                <option value="visage">Visage</option>
                                <option value="cheveux">Cheveux</option>
                                <option value="epilation">Épilation</option>
                                <option value="massage">Massage</option>
                                <option value="maquillage">Maquillage</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Durée (minutes) *</label>
                            <input type="number" name="duree" class="form-control" value="60" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix (FCFA) *</label>
                            <input type="number" name="prix" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Formation -->
<div class="modal fade" id="editFormationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square text-primary"></i> Modifier la formation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_formation">
                    <input type="hidden" name="formation_id" id="edit_formation_id">
                    <div class="mb-3">
                        <label class="form-label">Nom du service *</label>
                        <input type="text" name="titre" id="edit_titre" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catégorie *</label>
                            <select name="categorie" id="edit_categorie" class="form-select" required>
                                <option value="onglerie">Onglerie</option>
                                <option value="visage">Visage</option>
                                <option value="cheveux">Cheveux</option>
                                <option value="epilation">Épilation</option>
                                <option value="massage">Massage</option>
                                <option value="maquillage">Maquillage</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Durée (minutes) *</label>
                            <input type="number" name="duree" id="edit_duree" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix (FCFA) *</label>
                            <input type="number" name="prix" id="edit_prix" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Client -->
<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-gear text-primary"></i> Modifier le client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_client">
                    <input type="hidden" name="user_id" id="edit_client_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" id="edit_nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" id="edit_prenom" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="edit_telephone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modification Statut Client -->
<div class="modal fade" id="editClientStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shield-check text-primary"></i> Modifier le statut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_client_status">
                    <input type="hidden" name="user_id" id="status_user_id">
                    <label class="form-label">Nouveau statut</label>
                    <select name="statut" id="status_select" class="form-select">
                        <option value="actif">Actif</option>
                        <option value="suspendu">Suspendu</option>
                        <option value="en_attente">En attente</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulaire modification statut RDV -->
<form id="updateRdvForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="edit_rdv_status">
    <input type="hidden" name="rdv_id" id="rdv_id">
    <input type="hidden" name="statut" id="rdv_statut">
</form>

<!-- Formulaire suppression formation -->
<form id="deleteFormationForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete_formation">
    <input type="hidden" name="formation_id" id="delete_formation_id">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Gestion des onglets
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(tabName).classList.add('active');
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Mettre à jour l'URL sans recharger
    const url = new URL(window.location.href);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
}

// Graphique CA
document.addEventListener('DOMContentLoaded', function() {
    const caData = <?= json_encode($caMensuel) ?>;
    const mois = caData.map(item => item.mois);
    const montants = caData.map(item => item.total);
    
    const ctx = document.getElementById('caChart');
    if (ctx && mois.length > 0) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: mois,
                datasets: [{
                    label: 'Chiffre d\'affaires (FCFA)',
                    data: montants,
                    borderColor: '#e83e8c',
                    backgroundColor: 'rgba(232,62,140,0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#e83e8c',
                    pointBorderColor: '#fff',
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.raw) + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Graphique catégories
    const categorieData = <?= json_encode($statsCategorie) ?>;
    if (document.getElementById('categorieChart') && categorieData.length > 0) {
        const labels = categorieData.map(item => item.categorie);
        const counts = categorieData.map(item => item.total);
        
        new Chart(document.getElementById('categorieChart'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: ['#e83e8c', '#00b894', '#fdcb6e', '#0984e3', '#6c5ce7', '#d63031'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});

// Modifier formation
function editFormation(formation) {
    document.getElementById('edit_formation_id').value = formation.id;
    document.getElementById('edit_titre').value = formation.titre;
    document.getElementById('edit_categorie').value = formation.categorie;
    document.getElementById('edit_duree').value = formation.duree;
    document.getElementById('edit_prix').value = formation.prix;
    document.getElementById('edit_description').value = formation.description || '';
    new bootstrap.Modal(document.getElementById('editFormationModal')).show();
}

// Supprimer formation
function deleteFormation(id, titre) {
    if (confirm(`Supprimer la formation "${titre}" ?`)) {
        document.getElementById('delete_formation_id').value = id;
        document.getElementById('deleteFormationForm').submit();
    }
}

// Modifier client
function editClient(client) {
    document.getElementById('edit_client_id').value = client.id;
    document.getElementById('edit_nom').value = client.nom;
    document.getElementById('edit_prenom').value = client.prenom;
    document.getElementById('edit_email').value = client.email;
    document.getElementById('edit_telephone').value = client.telephone || '';
    document.getElementById('edit_notes').value = client.notes || '';
    new bootstrap.Modal(document.getElementById('editClientModal')).show();
}

// Modifier statut client
function editClientStatus(id, currentStatus) {
    document.getElementById('status_user_id').value = id;
    document.getElementById('status_select').value = currentStatus;
    new bootstrap.Modal(document.getElementById('editClientStatusModal')).show();
}

// Modifier statut rendez-vous
function updateRdvStatus(rdvId, status) {
    document.getElementById('rdv_id').value = rdvId;
    document.getElementById('rdv_statut').value = status;
    document.getElementById('updateRdvForm').submit();
}
</script>

</body>
</html>