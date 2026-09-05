<?php
// pages/admin/supports.php - Gestion des supports de cours

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$formation_id = isset($_GET['formation_id']) ? (int)$_GET['formation_id'] : 0;

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Suppression
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT fichier FROM supports WHERE id = ?");
        $stmt->execute([$id]);
        $support = $stmt->fetch();
        
        if ($support && $support['fichier']) {
            $file_path = '../../uploads/supports/' . $support['fichier'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM supports WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Support supprimé avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
    header('Location: supports.php' . ($formation_id ? '?formation_id=' . $formation_id : ''));
    exit;
}

// --- Ajout / Modification ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $titre = sanitize($_POST['titre'] ?? '');
    $type = sanitize($_POST['type'] ?? 'document');
    $lien_externe = sanitize($_POST['lien_externe'] ?? '');
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $formation_id = isset($_POST['formation_id']) ? (int)$_POST['formation_id'] : 0;
    
    $errors = [];
    if (empty($titre)) $errors[] = 'Le titre est obligatoire.';
    if ($formation_id <= 0) $errors[] = 'Formation invalide.';
    
    // Gestion du fichier
    $fichier_name = null;
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'mp4', 'avi', 'mov'];
        $upload_result = uploadFile($_FILES['fichier'], '../../uploads/supports', $allowed);
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
                    // Supprimer l'ancien fichier
                    $stmt = $pdo->prepare("SELECT fichier FROM supports WHERE id = ?");
                    $stmt->execute([$edit_id]);
                    $old = $stmt->fetch();
                    if ($old && $old['fichier'] && file_exists('../../uploads/supports/' . $old['fichier'])) {
                        unlink('../../uploads/supports/' . $old['fichier']);
                    }
                    $sql = "UPDATE supports SET titre=?, type=?, fichier=?, lien_externe=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$titre, $type, $fichier_name, $lien_externe, $edit_id]);
                } else {
                    $sql = "UPDATE supports SET titre=?, type=?, lien_externe=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$titre, $type, $lien_externe, $edit_id]);
                }
                setFlash('success', 'Support modifié avec succès.');
            } else {
                // Ajout
                $sql = "INSERT INTO supports (formation_id, titre, type, fichier, lien_externe) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$formation_id, $titre, $type, $fichier_name, $lien_externe]);
                setFlash('success', 'Support ajouté avec succès.');
            }
            header('Location: supports.php?formation_id=' . $formation_id);
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
    $stmt = $pdo->prepare("SELECT * FROM supports WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if ($edit_data) {
        $formation_id = $edit_data['formation_id'];
    }
}

// --- Récupération de la formation ---
$formation = null;
if ($formation_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM formations WHERE id = ?");
    $stmt->execute([$formation_id]);
    $formation = $stmt->fetch();
}

// --- Liste des supports ---
$supports = [];
if ($formation_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM supports WHERE formation_id = ? ORDER BY created_at DESC");
    $stmt->execute([$formation_id]);
    $supports = $stmt->fetchAll();
}

// --- Formations pour sélection ---
$formations = $pdo->query("
    SELECT * FROM formations 
    ORDER BY titre
")->fetchAll();
?>

<div class="admin-content">

    <?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Formulaire d'ajout/modification -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> un support</h3>
            <a href="supports.php<?= $formation_id ? '?formation_id=' . $formation_id : '' ?>" class="btn btn-sm btn-warning">← Retour</a>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="flash-message flash-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="supports.php?action=save" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? 0 ?>">
            <input type="hidden" name="formation_id" value="<?= $formation_id ?>">
            
            <div class="form-group">
                <label class="form-label">Titre *</label>
                <input type="text" class="form-control" name="titre" required 
                       value="<?= htmlspecialchars($edit_data['titre'] ?? '') ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Type *</label>
                    <select class="form-control form-select" name="type" required>
                        <option value="document" <?= (isset($edit_data['type']) && $edit_data['type'] == 'document') ? 'selected' : '' ?>>Document</option>
                        <option value="pdf" <?= (isset($edit_data['type']) && $edit_data['type'] == 'pdf') ? 'selected' : '' ?>>PDF</option>
                        <option value="video" <?= (isset($edit_data['type']) && $edit_data['type'] == 'video') ? 'selected' : '' ?>>Vidéo</option>
                        <option value="lien" <?= (isset($edit_data['type']) && $edit_data['type'] == 'lien') ? 'selected' : '' ?>>Lien externe</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fichier (PDF, DOC, MP4, etc.)</label>
                    <input type="file" class="form-control" name="fichier">
                    <?php if (isset($edit_data['fichier']) && $edit_data['fichier']): ?>
                    <div style="margin-top:8px;font-size:13px;color:var(--text-light);">
                        Fichier actuel : <a href="../../uploads/supports/<?= htmlspecialchars($edit_data['fichier']) ?>" target="_blank"><?= htmlspecialchars($edit_data['fichier']) ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Lien externe (YouTube, Vimeo, etc.)</label>
                <input type="url" class="form-control" name="lien_externe" placeholder="https://..." 
                       value="<?= htmlspecialchars($edit_data['lien_externe'] ?? '') ?>">
            </div>
            
            <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Mettre à jour' : 'Ajouter' ?></button>
            <a href="supports.php<?= $formation_id ? '?formation_id=' . $formation_id : '' ?>" class="btn btn-warning">Annuler</a>
        </form>
    </div>
    
    <?php else: ?>
    <!-- Liste des supports -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Supports
                <?php if ($formation): ?>
                de <span style="color:var(--primary);"><?= htmlspecialchars($formation['titre']) ?></span>
                <?php endif; ?>
            </h3>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <select class="form-control form-select" style="width:auto;padding:6px 12px;" onchange="window.location.href='supports.php?formation_id='+this.value">
                    <option value="">Changer de formation</option>
                    <?php foreach ($formations as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= $formation_id == $f['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['titre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($formation_id > 0): ?>
                <a href="supports.php?action=add&formation_id=<?= $formation_id ?>" class="btn btn-primary">➕ Ajouter</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($formation_id == 0): ?>
        <p style="color:var(--text-light);">Veuillez sélectionner une formation pour voir ses supports.</p>
        <?php elseif (empty($supports)): ?>
        <p style="color:var(--text-light);">Aucun support pour cette formation. <a href="supports.php?action=add&formation_id=<?= $formation_id ?>">Ajouter un support</a></p>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Fichier/Lien</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($supports as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['titre']) ?></strong></td>
                        <td>
                            <span class="badge badge-<?= $s['type'] == 'video' ? 'danger' : ($s['type'] == 'lien' ? 'info' : 'warning') ?>">
                                <?= ucfirst($s['type']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($s['fichier']): ?>
                            <a href="../../uploads/supports/<?= htmlspecialchars($s['fichier']) ?>" target="_blank" class="btn btn-sm btn-info" style="background:#3b82f6;color:#fff;">📎 Voir</a>
                            <?php elseif ($s['lien_externe']): ?>
                            <a href="<?= htmlspecialchars($s['lien_externe']) ?>" target="_blank" class="btn btn-sm btn-info" style="background:#8b5cf6;color:#fff;">🔗 Lien</a>
                            <?php else: ?>
                            <span style="color:var(--text-light);font-size:12px;">Aucun</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                        <td>
                            <a href="supports.php?action=edit&id=<?= $s['id'] ?>&formation_id=<?= $formation_id ?>" class="btn btn-sm btn-primary">✏️</a>
                            <a href="supports.php?action=delete&id=<?= $s['id'] ?>&formation_id=<?= $formation_id ?>" class="btn btn-sm btn-danger delete-btn">🗑️</a>
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