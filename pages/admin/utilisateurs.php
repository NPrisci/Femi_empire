<?php
// pages/admin/utilisateurs.php - Gestion des utilisateurs

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Modification du rôle
if ($action === 'update_role' && $id > 0) {
    $role = sanitize($_GET['role'] ?? '');
    $valid_roles = ['client', 'admin'];
    
    if (in_array($role, $valid_roles)) {
        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
            $stmt->execute([$role, $id]);
            setFlash('success', 'Rôle mis à jour avec succès.');
        } catch (PDOException $e) {
            setFlash('error', 'Erreur : ' . $e->getMessage());
        }
    } else {
        setFlash('error', 'Rôle invalide.');
    }
    header('Location: utilisateurs.php');
    exit;
}

// Suppression
if ($action === 'delete' && $id > 0) {
    // Empêcher la suppression de son propre compte
    if ($id == $_SESSION['user_id']) {
        setFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Utilisateur supprimé avec succès.');
        } catch (PDOException $e) {
            setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
    header('Location: utilisateurs.php');
    exit;
}

// --- Filtres ---
$filter_role = isset($_GET['role']) ? sanitize($_GET['role']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// --- Récupération des données ---
$query = "SELECT * FROM utilisateurs WHERE 1=1";
$params = [];

if ($filter_role) {
    $query .= " AND role = ?";
    $params[] = $filter_role;
}

if ($search) {
    $query .= " AND (prenom LIKE ? OR nom LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$utilisateurs = $stmt->fetchAll();

// --- Statistiques ---
$stats = $pdo->query("
    SELECT role, COUNT(*) as total 
    FROM utilisateurs 
    GROUP BY role
")->fetchAll();

$stats_assoc = [];
foreach ($stats as $s) {
    $stats_assoc[$s['role']] = $s['total'];
}
?>

<div class="admin-content">

    <!-- Statistiques -->
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card">
            <div class="stat-number"><?= $stats_assoc['client'] ?? 0 ?></div>
            <div class="stat-label">👤 Clients</div>
        </div>
        <!-- <div class="stat-card blue">
            <div class="stat-number"><?= $stats_assoc['formateur'] ?? 0 ?></div>
            <div class="stat-label">👨‍🏫 Formateurs</div>
        </div> -->
        <div class="stat-card orange">
            <div class="stat-number"><?= $stats_assoc['admin'] ?? 0 ?></div>
            <div class="stat-label">🛡️ Administrateurs</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-number"><?= array_sum($stats_assoc) ?></div>
            <div class="stat-label">📊 Total</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card">
        <form method="GET" action="utilisateurs.php" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Rôle</label>
                <select class="form-control form-select" name="role" style="width:150px;">
                    <option value="">Tous</option>
                    <option value="client" <?= $filter_role == 'client' ? 'selected' : '' ?>>Client</option>
                    <!-- <option value="formateur" <?= $filter_role == 'formateur' ? 'selected' : '' ?>>Formateur</option> -->
                    <option value="admin" <?= $filter_role == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;flex:1;">
                <label class="form-label">Rechercher</label>
                <input type="text" class="form-control" name="search" placeholder="Nom, prénom, email..." 
                       value="<?= htmlspecialchars($search) ?>" style="min-width:200px;">
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="utilisateurs.php" class="btn btn-warning">Réinitialiser</a>
        </form>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tous les utilisateurs</h3>
            <span style="font-size:14px;color:var(--text-light);"><?= count($utilisateurs) ?> utilisateur(s)</span>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Inscrit le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($utilisateurs)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--text-light);">Aucun utilisateur trouvé</td></tr>
                    <?php else: ?>
                    <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></strong>
                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                            <span class="badge badge-info" style="font-size:10px;">Vous</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>
                        <td>
                            <span class="badge badge-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'formateur' ? 'warning' : 'info') ?>">
                                <?= $user['role'] == 'admin' ? 'Admin' : ($user['role'] == 'formateur' ? 'Formateur' : 'Client') ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <select class="form-control form-select" style="width:120px;padding:4px 8px;font-size:12px;" 
                                    onchange="if(this.value) window.location.href='utilisateurs.php?action=update_role&id=<?= $user['id'] ?>&role='+this.value">
                                <option value="">Changer rôle</option>
                                <option value="client" <?= $user['role'] == 'client' ? 'selected' : '' ?>>Client</option>
                                <!-- <option value="formateur" <?= $user['role'] == 'formateur' ? 'selected' : '' ?>>Formateur</option> -->
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <a href="utilisateurs.php?action=delete&id=<?= $user['id'] ?>" class="btn btn-sm btn-danger delete-btn">🗑️</a>
                            <?php endif; ?>
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