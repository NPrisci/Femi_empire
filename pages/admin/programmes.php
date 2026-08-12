<?php
// pages/admin/programmes.php - Gestion des programmes de formation

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$formation_id = isset($_GET['formation_id']) ? (int)$_GET['formation_id'] : 0;

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Suppression
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM programmes WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Programme supprimé avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
    header('Location: programmes.php' . ($formation_id ? '?formation_id=' . $formation_id : ''));
    exit;
}

// --- Ajout / Modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $titre = sanitize($_POST['titre'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $ordre = (int)($_POST['ordre'] ?? 0);
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $formation_id = isset($_POST['formation_id']) ? (int)$_POST['formation_id'] : 0;
    
    $errors = [];
    if (empty($titre)) $errors[] = 'Le titre est obligatoire.';
    if ($formation_id <= 0) $errors[] = 'Formation invalide.';
    
    if (empty($errors)) {
        try {
            if ($edit_id > 0) {
                $sql = "UPDATE programmes SET titre=?, description=?, ordre=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titre, $description, $ordre, $edit_id]);
                setFlash('success', 'Programme modifié avec succès.');
            } else {
                $sql = "INSERT INTO programmes (formation_id, titre, description, ordre) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$formation_id, $titre, $description, $ordre]);
                setFlash('success', 'Programme ajouté avec succès.');
            }
            header('Location: programmes.php?formation_id=' . $formation_id);
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur : ' . $e->getMessage();
        }
    }
}

// --- Récupération des données pour modification ---
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM programmes WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if ($edit_data) {
        $formation_id = $edit_data['formation_id'];
    }
}

// --- Liste des formations pour le select ---
$formations = $pdo->query("SELECT id, titre FROM formations ORDER BY titre")->fetchAll();

// --- Liste des programmes ---
$programmes = [];
if ($formation_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM programmes WHERE formation_id = ? ORDER BY ordre ASC");
    $stmt->execute([$formation_id]);
    $programmes = $stmt->fetchAll();
    
    // Récupérer le nom de la formation
    $stmt = $pdo->prepare("SELECT titre FROM formations WHERE id = ?");
    $stmt->execute([$formation_id]);
    $formation = $stmt->fetch();
}
?>

<div class="admin-content">

    <?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Formulaire d'ajout/modification -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> un programme</h3>
            <a href="programmes.php<?= $formation_id ? '?formation_id=' . $formation_id : '' ?>" class="btn btn-sm btn-warning">← Retour</a>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="flash-message flash-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="programmes.php?action=save">
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? 0 ?>">
            <input type="hidden" name="formation_id" value="<?= $formation_id ?>">
            
            <div class="form-group">
                <label class="form-label">Titre *</label>
                <input type="text" class="form-control" name="titre" required 
                       value="<?= htmlspecialchars($edit_data['titre'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Ordre d'affichage</label>
                <input type="number" class="form-control" name="ordre" min="0" 
                       value="<?= $edit_data['ordre'] ?? 0 ?>">
            </div>
            
            <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Mettre à jour' : 'Ajouter' ?></button>
            <a href="programmes.php<?= $formation_id ? '?formation_id=' . $formation_id : '' ?>" class="btn btn-warning">Annuler</a>
        </form>
    </div>
    
    <?php else: ?>
    <!-- Liste des programmes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Programmes 
                <?php if (isset($formation)): ?>
                de <span style="color:var(--primary);"><?= htmlspecialchars($formation['titre']) ?></span>
                <?php endif; ?>
            </h3>
            <div style="display:flex;gap:8px;">
                <select class="form-control" style="width:auto;padding:6px 12px;" onchange="window.location.href='programmes.php?formation_id='+this.value">
                    <option value="">Changer de formation</option>
                    <?php foreach ($formations as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= $formation_id == $f['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['titre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($formation_id > 0): ?>
                <a href="programmes.php?action=add&formation_id=<?= $formation_id ?>" class="btn btn-primary">➕ Ajouter</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($formation_id == 0): ?>
        <p style="color:var(--text-light);">Veuillez sélectionner une formation pour voir ses programmes.</p>
        <?php elseif (empty($programmes)): ?>
        <p style="color:var(--text-light);">Aucun programme pour cette formation. <a href="programmes.php?action=add&formation_id=<?= $formation_id ?>">Ajouter un programme</a></p>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programmes as $p): ?>
                    <tr>
                        <td><?= $p['ordre'] ?></td>
                        <td><strong><?= htmlspecialchars($p['titre']) ?></strong></td>
                        <td><?= nl2br(htmlspecialchars(substr($p['description'] ?? '', 0, 100))) ?></td>
                        <td>
                            <a href="programmes.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">✏️</a>
                            <a href="programmes.php?action=delete&id=<?= $p['id'] ?>&formation_id=<?= $formation_id ?>" class="btn btn-sm btn-danger delete-btn">🗑️</a>
                            <a href="supports.php?programme_id=<?= $p['id'] ?>" class="btn btn-sm btn-info" style="background:#3b82f6;color:#fff;">📎 Supports</a>
                            <a href="exercices.php?programme_id=<?= $p['id'] ?>" class="btn btn-sm btn-success">✏️ Exercices</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>