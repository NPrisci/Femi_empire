<?php
// pages/admin/paiements.php - Gestion des paiements

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Mise à jour du statut
if ($action === 'update_status' && $id > 0) {
    $status = sanitize($_GET['status'] ?? '');
    $valid_status = ['payee', 'en_attente', 'annulee'];
    
    if (in_array($status, $valid_status)) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE commandes SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            
            // Si le paiement est payé, créer une inscription
            if ($status === 'payee') {
                $stmt = $pdo->prepare("SELECT utilisateur_id, formation_id FROM commandes WHERE id = ?");
                $stmt->execute([$id]);
                $cmd = $stmt->fetch();
                
                if ($cmd) {
                    // Vérifier si une inscription existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM inscriptions WHERE commande_id = ?");
                    $stmt->execute([$id]);
                    if (!$stmt->fetch()) {
                        $stmt = $pdo->prepare("
                            INSERT INTO inscriptions (utilisateur_id, formation_id, commande_id, date_inscription, statut, progression) 
                            VALUES (?, ?, ?, NOW(), 'active', 0)
                        ");
                        $stmt->execute([$cmd['utilisateur_id'], $cmd['formation_id'], $id]);
                    }
                }
            }
            
            $pdo->commit();
            setFlash('success', 'Statut du paiement mis à jour avec succès.');
        } catch (PDOException $e) {
            $pdo->rollBack();
            setFlash('error', 'Erreur : ' . $e->getMessage());
        }
    } else {
        setFlash('error', 'Statut invalide.');
    }
    header('Location: paiements.php');
    exit;
}

// Suppression
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Commande supprimée avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
    header('Location: paiements.php');
    exit;
}

// --- Filtres ---
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// --- Récupération des données ---
$query = "
    SELECT c.*, u.prenom, u.nom, u.email, f.titre as formation_titre 
    FROM commandes c
    JOIN utilisateurs u ON c.utilisateur_id = u.id
    JOIN formations f ON c.formation_id = f.id
    WHERE 1=1
";

$params = [];

if ($filter_status) {
    $query .= " AND c.status = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY c.date_creation DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$commandes = $stmt->fetchAll();

// --- Statistiques ---
$stats = $pdo->query("
    SELECT status, COUNT(*) as total, SUM(montant) as total_montant 
    FROM commandes 
    GROUP BY status
")->fetchAll();

$stats_assoc = [];
foreach ($stats as $s) {
    $stats_assoc[$s['status']] = [
        'total' => $s['total'],
        'montant' => $s['total_montant']
    ];
}

$ca_total = $pdo->query("SELECT SUM(montant) as total FROM commandes WHERE status = 'payee'")->fetch()['total'] ?? 0;
?>

<div class="admin-content">

    <!-- Statistiques -->
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card green">
            <div class="stat-number"><?= $stats_assoc['payee']['total'] ?? 0 ?></div>
            <div class="stat-label">✅ Payées</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-number"><?= $stats_assoc['en_attente']['total'] ?? 0 ?></div>
            <div class="stat-label">⏳ En attente</div>
        </div>
        <div class="stat-card danger" style="border-color:#ef4444;">
            <div class="stat-number"><?= $stats_assoc['annulee']['total'] ?? 0 ?></div>
            <div class="stat-label">❌ Annulées</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-number"><?= formatPrice($ca_total) ?></div>
            <div class="stat-label">💰 CA total (payé)</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card">
        <form method="GET" action="paiements.php" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Statut</label>
                <select class="form-control form-select" name="status" style="width:150px;" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="payee" <?= $filter_status == 'payee' ? 'selected' : '' ?>>Payée</option>
                    <option value="en_attente" <?= $filter_status == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="annulee" <?= $filter_status == 'annulee' ? 'selected' : '' ?>>Annulée</option>
                </select>
            </div>
            <a href="paiements.php" class="btn btn-warning">Réinitialiser</a>
        </form>
    </div>

    <!-- Liste des commandes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Toutes les commandes</h3>
            <span style="font-size:14px;color:var(--text-light);"><?= count($commandes) ?> commande(s)</span>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Formation</th>
                        <th>Montant</th>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commandes)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--text-light);">Aucune commande trouvée</td></tr>
                    <?php else: ?>
                    <?php foreach ($commandes as $cmd): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?></strong><br>
                            <span style="font-size:12px;color:var(--text-light);"><?= htmlspecialchars($cmd['email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($cmd['formation_titre']) ?></td>
                        <td><strong><?= formatPrice($cmd['montant']) ?></strong></td>
                        <td><?= htmlspecialchars($cmd['reference'] ?? '-') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($cmd['date_creation'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $cmd['status'] == 'payee' ? 'success' : ($cmd['status'] == 'en_attente' ? 'warning' : 'danger') ?>">
                                <?= $cmd['status'] == 'payee' ? 'Payée' : ($cmd['status'] == 'en_attente' ? 'En attente' : 'Annulée') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($cmd['status'] != 'payee'): ?>
                            <a href="paiements.php?action=update_status&id=<?= $cmd['id'] ?>&status=payee" class="btn btn-sm btn-success">✅ Payer</a>
                            <?php endif; ?>
                            <?php if ($cmd['status'] != 'annulee'): ?>
                            <a href="paiements.php?action=update_status&id=<?= $cmd['id'] ?>&status=annulee" class="btn btn-sm btn-danger">❌</a>
                            <?php endif; ?>
                            <a href="paiements.php?action=delete&id=<?= $cmd['id'] ?>" class="btn btn-sm btn-danger delete-btn">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>