<?php
// pages/admin/exercices.php - Gestion des exercices

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$programme_id = isset($_GET['programme_id']) ? (int)$_GET['programme_id'] : 0;

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Suppression
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT fichier FROM exercices WHERE id = ?");
        $stmt->execute([$id]);
        $exercice = $stmt->fetch();
        
        if ($exercice && $exercice['fichier']) {
            $file_path = '../../uploads/exercices/' . $exercice['fichier'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM exercices WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Exercice supprimé avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
    header('Location: exercices.php' . ($programme_id ? '?programme_id=' . $programme_id : ''));
    exit;
}

// --- Ajout / Modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $titre = sanitize($_POST['titre'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $date_limite = sanitize($_POST['date_limite'] ?? '');
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $programme_id = isset($_POST['programme_id']) ? (int)$_POST['programme_id'] : 0;
    
    $errors = [];
    if (empty($titre)) $errors[] = 'Le titre est obligatoire.';
    if ($programme_id <= 0) $errors[] = 'Programme invalide.';
    
    // Gestion du fichier
    $fichier_name = null;
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf', 'doc', 'docx', 'txt', 'zip'];
        $upload_result = uploadFile($_FILES['fichier'], '../../uploads/exercices', $allowed);
        if ($upload_result['success']) {
            $fichier_name = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        try {
            if ($edit_id > 0) {
                // Modification
                if ($fichier_name) {
                    $stmt = $pdo->prepare("SELECT fichier FROM exercices WHERE id = ?");
                    $stmt->execute([$edit_id]);
                    $old = $stmt->fetch();
                    if ($old && $old['fichier'] && file_exists('../../uploads/exercices/' . $old['fichier'])) {
                        unlink('../../uploads/exercices/' . $old['fichier']);
                    }
                    $sql = "UPDATE exercices SET titre=?, description=?, fichier=?, date_limite=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$titre, $description, $fichier_name, $date_limite ?: null, $edit_id]);
                } else {
                    $sql = "UPDATE exercices SET titre=?, description=?, date_limite=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$titre, $description, $date_limite ?: null, $edit_id]);
                }
                setFlash('success', 'Exercice modifié avec succès.');
            } else {
                // Ajout
                $sql = "INSERT INTO exercices (programme_id, titre, description, fichier, date_limite) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$programme_id, $titre, $description, $fichier_name, $date_limite ?: null]);
                setFlash('success', 'Exercice ajouté avec succès.');
            }
            header('Location: exercices.php?programme_id=' . $programme_id);
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur : ' . $e->getMessage();
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// --- Récupération des données pour modification ---
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM exercices WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if ($edit_data) {
        $programme_id = $edit_data['programme_id'];
    }
}

// --- Récupération du programme et de la formation ---
$programme = null;
$formation = null;
if ($programme_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM programmes WHERE id = ?");
    $stmt->execute([$programme_id]);
    $programme = $stmt->fetch();
    
    if ($programme) {
        $stmt = $pdo->prepare("SELECT titre FROM formations WHERE id = ?");
        $stmt->execute([$programme['formation_id']]);
        $formation = $stmt->fetch();
    }
}

// --- Liste des exercices ---
$exercices = [];
if ($programme_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM exercices WHERE programme_id = ? ORDER BY created_at DESC");
    $stmt->execute([$programme_id]);
    $exercices = $stmt->fetchAll();
}

// --- Programmes pour sélection ---
$programmes = $pdo->query("
    SELECT p.*, f.titre as formation_titre 
    FROM programmes p
    JOIN formations f ON p.formation_id = f.id
    ORDER BY f.titre, p.ordre
")->fetchAll();
?>

<div class="admin-content">

    <?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Formulaire d'ajout/modification -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> un exercice</h3>
            <a href="exercices.php<?= $programme_id ? '?programme_id=' . $programme_id : '' ?>" class="btn btn-sm btn-warning">← Retour</a>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="flash-message flash-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="exercices.php?action=save" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? 0 ?>">
            <input type="hidden" name="programme_id" value="<?= $programme_id ?>">
            
            <div class="form-group">
                <label class="form-label">Titre *</label>
                <input type="text" class="form-control" name="titre" required 
                       value="<?= htmlspecialchars($edit_data['titre'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date limite</label>
                    <input type="date" class="form-control" name="date_limite" 
                           value="<?= isset($edit_data['date_limite']) ? date('Y-m-d', strtotime($edit_data['date_limite'])) : '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Fichier (PDF, DOC, ZIP)</label>
                    <input type="file" class="form-control" name="fichier">
                    <?php if (isset($edit_data['fichier']) && $edit_data['fichier']): ?>
                    <div style="margin-top:8px;font-size:13px;color:var(--text-light);">
                        Fichier actuel : <a href="../../uploads/exercices/<?= htmlspecialchars($edit_data['fichier']) ?>" target="_blank"><?= htmlspecialchars($edit_data['fichier']) ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Mettre à jour' : 'Ajouter' ?></button>
            <a href="exercices.php<?= $programme_id ? '?programme_id=' . $programme_id : '' ?>" class="btn btn-warning">Annuler</a>
        </form>
    </div>
    
    <?php else: ?>
    <!-- Liste des exercices -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Exercices
                <?php if ($programme): ?>
                de <span style="color:var(--primary);"><?= htmlspecialchars($programme['titre']) ?></span>
                <?php endif; ?>
            </h3>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <select class="form-control form-select" style="width:auto;padding:6px 12px;" onchange="window.location.href='exercices.php?programme_id='+this.value">
                    <option value="">Changer de programme</option>
                    <?php foreach ($programmes as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $programme_id == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['formation_titre']) ?> - <?= htmlspecialchars($p['titre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($programme_id > 0): ?>
                <a href="exercices.php?action=add&programme_id=<?= $programme_id ?>" class="btn btn-primary">➕ Ajouter</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($programme_id == 0): ?>
        <p style="color:var(--text-light);">Veuillez sélectionner un programme pour voir ses exercices.</p>
        <?php elseif (empty($exercices)): ?>
        <p style="color:var(--text-light);">Aucun exercice pour ce programme. <a href="exercices.php?action=add&programme_id=<?= $programme_id ?>">Ajouter un exercice</a></p>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Date limite</th>
                        <th>Fichier</th>
                        <th>Réalisations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exercices as $ex): ?>
                    <?php 
                    // Compter les réalisations pour cet exercice
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM realisations WHERE exercice_id = ?");
                    $stmt->execute([$ex['id']]);
                    $real_count = $stmt->fetch()['total'] ?? 0;
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($ex['titre']) ?></strong></td>
                        <td><?= nl2br(htmlspecialchars(substr($ex['description'] ?? '', 0, 100))) ?></td>
                        <td>
                            <?php if ($ex['date_limite']): ?>
                            <?= date('d/m/Y', strtotime($ex['date_limite'])) ?>
                            <?php else: ?>
                            <span style="color:var(--text-light);font-size:12px;">Pas de limite</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($ex['fichier']): ?>
                            <a href="../../uploads/exercices/<?= htmlspecialchars($ex['fichier']) ?>" target="_blank" class="btn btn-sm btn-info" style="background:#3b82f6;color:#fff;">📎 Voir</a>
                            <?php else: ?>
                            <span style="color:var(--text-light);font-size:12px;">Aucun</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="realisations.php?exercice_id=<?= $ex['id'] ?>" class="btn btn-sm btn-success">
                                <?= $real_count ?> réalisations
                            </a>
                        </td>
                        <td>
                            <a href="exercices.php?action=edit&id=<?= $ex['id'] ?>&programme_id=<?= $programme_id ?>" class="btn btn-sm btn-primary">✏️</a>
                            <a href="exercices.php?action=delete&id=<?= $ex['id'] ?>&programme_id=<?= $programme_id ?>" class="btn btn-sm btn-danger delete-btn">🗑️</a>
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