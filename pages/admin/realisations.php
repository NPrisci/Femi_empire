<?php
// pages/admin/realisations.php - Gestion des réalisations des apprenants

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validation ou refus d'une réalisation
if (($action === 'validate' || $action === 'reject') && $id > 0) {
    $statut = $action === 'validate' ? 'validee' : 'refusee';
    $commentaire = sanitize($_POST['commentaire'] ?? '');
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE realisations SET statut = ?, commentaire_admin = ?, date_validation = NOW() WHERE id = ?");
        $stmt->execute([$statut, $commentaire, $id]);
        
        // Récupérer les infos pour notification (optionnel)
        $stmt = $pdo->prepare("
            SELECT r.*, u.prenom, u.nom, u.email, e.titre as exercice_titre 
            FROM realisations r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            JOIN exercices e ON r.exercice_id = e.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $realisation = $stmt->fetch();
        
        $pdo->commit();
        setFlash('success', 'Réalisation ' . ($action === 'validate' ? 'validée' : 'refusée') . ' avec succès.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        setFlash('error', 'Erreur : ' . $e->getMessage());
    }
    header('Location: realisations.php');
    exit;
}

// Suppression
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT fichier FROM realisations WHERE id = ?");
        $stmt->execute([$id]);
        $realisation = $stmt->fetch();
        
        if ($realisation && $realisation['fichier']) {
            $file_path = '../../uploads/realisations/' . $realisation['fichier'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM realisations WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Réalisation supprimée avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
    header('Location: realisations.php');
    exit;
}

// --- Filtres ---
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// --- Récupération des données ---
$query = "
    SELECT r.*, u.prenom, u.nom, u.email, e.titre as exercice_titre 
    FROM realisations r
    JOIN utilisateurs u ON r.utilisateur_id = u.id
    JOIN exercices e ON r.exercice_id = e.id
    WHERE 1=1
";

$params = [];

if ($filter_status) {
    $query .= " AND r.statut = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY r.date_soumission DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$realisations = $stmt->fetchAll();

// --- Statistiques ---
$stats = $pdo->query("
    SELECT statut, COUNT(*) as total 
    FROM realisations 
    GROUP BY statut
")->fetchAll();

$stats_assoc = [];
foreach ($stats as $s) {
    $stats_assoc[$s['statut']] = $s['total'];
}
?>

<div class="admin-content">

    <!-- Statistiques -->
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
        <div class="stat-card orange">
            <div class="stat-number"><?= $stats_assoc['en_attente'] ?? 0 ?></div>
            <div class="stat-label">⏳ En attente de validation</div>
        </div>
        <div class="stat-card green">
            <div class="stat-number"><?= $stats_assoc['validee'] ?? 0 ?></div>
            <div class="stat-label">✅ Validées</div>
        </div>
        <div class="stat-card danger" style="border-color:#ef4444;">
            <div class="stat-number"><?= $stats_assoc['refusee'] ?? 0 ?></div>
            <div class="stat-label">❌ Refusées</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card">
        <form method="GET" action="realisations.php" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Statut</label>
                <select class="form-control form-select" name="status" style="width:150px;" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="en_attente" <?= $filter_status == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="validee" <?= $filter_status == 'validee' ? 'selected' : '' ?>>Validée</option>
                    <option value="refusee" <?= $filter_status == 'refusee' ? 'selected' : '' ?>>Refusée</option>
                </select>
            </div>
            <a href="realisations.php" class="btn btn-warning">Réinitialiser</a>
        </form>
    </div>

    <!-- Liste des réalisations -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Toutes les réalisations</h3>
            <span style="font-size:14px;color:var(--text-light);"><?= count($realisations) ?> réalisation(s)</span>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Apprenant</th>
                        <th>Exercice</th>
                        <th>Titre</th>
                        <th>Fichier</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($realisations)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--text-light);">Aucune réalisation trouvée</td></tr>
                    <?php else: ?>
                    <?php foreach ($realisations as $real): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($real['prenom'] . ' ' . $real['nom']) ?></strong><br>
                            <span style="font-size:12px;color:var(--text-light);"><?= htmlspecialchars($real['email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($real['exercice_titre']) ?></td>
                        <td><?= htmlspecialchars($real['titre']) ?></td>
                        <td>
                            <?php if ($real['fichier']): ?>
                            <a href="../../uploads/realisations/<?= htmlspecialchars($real['fichier']) ?>" target="_blank" class="btn btn-sm btn-info" style="background:#3b82f6;color:#fff;">📎 Voir</a>
                            <?php else: ?>
                            <span style="color:var(--text-light);font-size:12px;">Aucun fichier</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($real['date_soumission'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $real['statut'] == 'validee' ? 'success' : ($real['statut'] == 'en_attente' ? 'warning' : 'danger') ?>">
                                <?= $real['statut'] == 'validee' ? 'Validée' : ($real['statut'] == 'en_attente' ? 'En attente' : 'Refusée') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($real['statut'] == 'en_attente'): ?>
                            <button onclick="openModal('validateModal_<?= $real['id'] ?>')" class="btn btn-sm btn-success">✅ Valider</button>
                            <button onclick="openModal('rejectModal_<?= $real['id'] ?>')" class="btn btn-sm btn-danger">❌ Refuser</button>
                            <?php endif; ?>
                            <a href="realisations.php?action=delete&id=<?= $real['id'] ?>" class="btn btn-sm btn-danger delete-btn">🗑️</a>
                            
                            <!-- Modal Validation -->
                            <div class="modal-overlay" id="validateModal_<?= $real['id'] ?>">
                                <div class="modal">
                                    <div class="modal-header">
                                        <h3 class="modal-title">Valider la réalisation</h3>
                                        <button class="modal-close" onclick="closeModal('validateModal_<?= $real['id'] ?>')">&times;</button>
                                    </div>
                                    <form method="POST" action="realisations.php?action=validate&id=<?= $real['id'] ?>">
                                        <div class="modal-body">
                                            <p><strong>Apprenant :</strong> <?= htmlspecialchars($real['prenom'] . ' ' . $real['nom']) ?></p>
                                            <p><strong>Exercice :</strong> <?= htmlspecialchars($real['exercice_titre']) ?></p>
                                            <p><strong>Titre :</strong> <?= htmlspecialchars($real['titre']) ?></p>
                                            <?php if ($real['fichier']): ?>
                                            <p><a href="../../uploads/realisations/<?= htmlspecialchars($real['fichier']) ?>" target="_blank" class="btn btn-sm btn-info" style="background:#3b82f6;color:#fff;">📎 Voir le fichier</a></p>
                                            <?php endif; ?>
                                            <div class="form-group" style="margin-top:16px;">
                                                <label class="form-label">Commentaire (optionnel)</label>
                                                <textarea class="form-control" name="commentaire" rows="3" placeholder="Félicitations pour votre travail !"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-warning" onclick="closeModal('validateModal_<?= $real['id'] ?>')">Annuler</button>
                                            <button type="submit" class="btn btn-success">✅ Valider</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Modal Refus -->
                            <div class="modal-overlay" id="rejectModal_<?= $real['id'] ?>">
                                <div class="modal">
                                    <div class="modal-header">
                                        <h3 class="modal-title">Refuser la réalisation</h3>
                                        <button class="modal-close" onclick="closeModal('rejectModal_<?= $real['id'] ?>')">&times;</button>
                                    </div>
                                    <form method="POST" action="realisations.php?action=reject&id=<?= $real['id'] ?>">
                                        <div class="modal-body">
                                            <p><strong>Apprenant :</strong> <?= htmlspecialchars($real['prenom'] . ' ' . $real['nom']) ?></p>
                                            <p><strong>Exercice :</strong> <?= htmlspecialchars($real['exercice_titre']) ?></p>
                                            <p><strong>Titre :</strong> <?= htmlspecialchars($real['titre']) ?></p>
                                            <?php if ($real['fichier']): ?>
                                            <p><a href="../../uploads/realisations/<?= htmlspecialchars($real['fichier']) ?>" target="_blank" class="btn btn-sm btn-info" style="background:#3b82f6;color:#fff;">📎 Voir le fichier</a></p>
                                            <?php endif; ?>
                                            <div class="form-group" style="margin-top:16px;">
                                                <label class="form-label">Commentaire *</label>
                                                <textarea class="form-control" name="commentaire" rows="3" required placeholder="Veuillez indiquer les points à améliorer..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-warning" onclick="closeModal('rejectModal_<?= $real['id'] ?>')">Annuler</button>
                                            <button type="submit" class="btn btn-danger">❌ Refuser</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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