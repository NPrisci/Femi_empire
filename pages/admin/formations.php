<?php
// pages/admin/formations.php - Gestion CRUD des formations avec supports et exercices

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$message = '';
$error = '';

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'informations'; // Onglet actif

// === GESTION DES FORMATIONS ===

// Suppression d'une formation
if ($action === 'delete' && $id > 0) {
    try {
        // Vérifier si la formation existe
        $stmt = $pdo->prepare("SELECT image FROM formations WHERE id = ?");
        $stmt->execute([$id]);
        $formation = $stmt->fetch();
        
        if ($formation) {
            // Supprimer l'image si elle existe
            if ($formation['image'] && file_exists('../../uploads/formations/' . $formation['image'])) {
                unlink('../../uploads/formations/' . $formation['image']);
            }
            
            // Supprimer la formation (les supports et exercices seront supprimés en cascade)
            $stmt = $pdo->prepare("DELETE FROM formations WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Formation supprimée avec succès.');
        } else {
            setFlash('error', 'Formation introuvable.');
        }
    } catch (PDOException $e) {
        setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
    header('Location: formations.php');
    exit;
}

// Ajout / Modification d'une formation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $titre = sanitize($_POST['titre'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $categorie = sanitize($_POST['categorie'] ?? '');
    $prix = (float)($_POST['prix'] ?? 0);
    $duree = sanitize($_POST['duree'] ?? '');
    $statut = sanitize($_POST['statut'] ?? 'active');
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    
    $errors = [];
    if (empty($titre)) $errors[] = 'Le titre est obligatoire.';
    if (empty($description)) $errors[] = 'La description est obligatoire.';
    if (empty($categorie)) $errors[] = 'La catégorie est obligatoire.';
    if ($prix < 0) $errors[] = 'Le prix doit être positif.';
    if (empty($duree)) $errors[] = 'La durée est obligatoire.';
    
    // Gestion de l'image
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadFile($_FILES['image'], '../../uploads/formations', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        if ($upload_result['success']) {
            $image_name = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        try {
            if ($edit_id > 0) {
                // Modification
                if ($image_name) {
                    // Supprimer l'ancienne image
                    $stmt = $pdo->prepare("SELECT image FROM formations WHERE id = ?");
                    $stmt->execute([$edit_id]);
                    $old = $stmt->fetch();
                    if ($old && $old['image'] && file_exists('../../uploads/formations/' . $old['image'])) {
                        unlink('../../uploads/formations/' . $old['image']);
                    }
                    $sql = "UPDATE formations SET titre=?, description=?, categorie=?, prix=?, duree=?, statut=?, image=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$titre, $description, $categorie, $prix, $duree, $statut, $image_name, $edit_id]);
                } else {
                    $sql = "UPDATE formations SET titre=?, description=?, categorie=?, prix=?, duree=?, statut=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$titre, $description, $categorie, $prix, $duree, $statut, $edit_id]);
                }
                setFlash('success', 'Formation modifiée avec succès.');
            } else {
                // Ajout
                $sql = "INSERT INTO formations (titre, description, categorie, prix, duree, statut, image, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$titre, $description, $categorie, $prix, $duree, $statut, $image_name]);
                setFlash('success', 'Formation ajoutée avec succès.');
            }
            header('Location: formations.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur : ' . $e->getMessage();
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// === GESTION DES SUPPORTS ===

// Ajout d'un support
if ($action === 'add_support' && $id > 0) {
    $titre = sanitize($_POST['titre'] ?? '');
    $type = sanitize($_POST['type'] ?? 'document');
    $fichier = null;
    $lien_externe = sanitize($_POST['lien_externe'] ?? '');
    
    if (empty($titre)) {
        setFlash('error', 'Le titre du support est obligatoire.');
    } else {
        // Gestion du fichier uploadé
        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['fichier'], '../../uploads/supports', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm']);
            if ($upload_result['success']) {
                $fichier = $upload_result['filename'];
            } else {
                setFlash('error', 'Erreur upload : ' . $upload_result['message']);
                header('Location: formations.php?action=edit&id=' . $id . '&tab=supports');
                exit;
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO supports (formation_id, titre, type, fichier, lien_externe) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $titre, $type, $fichier, $lien_externe]);
            setFlash('success', 'Support ajouté avec succès.');
        } catch (PDOException $e) {
            setFlash('error', 'Erreur : ' . $e->getMessage());
        }
    }
    header('Location: formations.php?action=edit&id=' . $id . '&tab=supports');
    exit;
}

// Suppression d'un support
if ($action === 'delete_support' && isset($_GET['support_id'])) {
    $support_id = (int)$_GET['support_id'];
    $formation_id = $id;
    try {
        // Supprimer le fichier si existe
        $stmt = $pdo->prepare("SELECT fichier FROM supports WHERE id = ?");
        $stmt->execute([$support_id]);
        $support = $stmt->fetch();
        if ($support && $support['fichier'] && file_exists('../../uploads/supports/' . $support['fichier'])) {
            unlink('../../uploads/supports/' . $support['fichier']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM supports WHERE id = ?");
        $stmt->execute([$support_id]);
        setFlash('success', 'Support supprimé avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur : ' . $e->getMessage());
    }
    header('Location: formations.php?action=edit&id=' . $formation_id . '&tab=supports');
    exit;
}

// === GESTION DES EXERCICES ===

// Ajout d'un exercice
if ($action === 'add_exercice' && $id > 0) {
    $titre = sanitize($_POST['titre'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $date_limite = !empty($_POST['date_limite']) ? sanitize($_POST['date_limite']) : null;
    $fichier = null;
    
    if (empty($titre)) {
        setFlash('error', 'Le titre de l\'exercice est obligatoire.');
    } else {
        // Gestion du fichier uploadé
        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['fichier'], '../../uploads/exercices', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip']);
            if ($upload_result['success']) {
                $fichier = $upload_result['filename'];
            } else {
                setFlash('error', 'Erreur upload : ' . $upload_result['message']);
                header('Location: formations.php?action=edit&id=' . $id . '&tab=exercices');
                exit;
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO exercices (formation_id, titre, description, fichier, date_limite) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $titre, $description, $fichier, $date_limite]);
            setFlash('success', 'Exercice ajouté avec succès.');
        } catch (PDOException $e) {
            setFlash('error', 'Erreur : ' . $e->getMessage());
        }
    }
    header('Location: formations.php?action=edit&id=' . $id . '&tab=exercices');
    exit;
}

// Suppression d'un exercice
if ($action === 'delete_exercice' && isset($_GET['exercice_id'])) {
    $exercice_id = (int)$_GET['exercice_id'];
    $formation_id = $id;
    try {
        // Supprimer le fichier si existe
        $stmt = $pdo->prepare("SELECT fichier FROM exercices WHERE id = ?");
        $stmt->execute([$exercice_id]);
        $exercice = $stmt->fetch();
        if ($exercice && $exercice['fichier'] && file_exists('../../uploads/exercices/' . $exercice['fichier'])) {
            unlink('../../uploads/exercices/' . $exercice['fichier']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM exercices WHERE id = ?");
        $stmt->execute([$exercice_id]);
        setFlash('success', 'Exercice supprimé avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur : ' . $e->getMessage());
    }
    header('Location: formations.php?action=edit&id=' . $formation_id . '&tab=exercices');
    exit;
}

// --- Récupération des données pour modification ---
$edit_data = null;
$supports = [];
$exercices = [];

if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM formations WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if (!$edit_data) {
        setFlash('error', 'Formation introuvable.');
        header('Location: formations.php');
        exit;
    }
    
    // Récupérer les supports
    $stmt = $pdo->prepare("SELECT * FROM supports WHERE formation_id = ? ORDER BY id ASC");
    $stmt->execute([$id]);
    $supports = $stmt->fetchAll();
    
    // Récupérer les exercices
    $stmt = $pdo->prepare("SELECT * FROM exercices WHERE formation_id = ? ORDER BY id ASC");
    $stmt->execute([$id]);
    $exercices = $stmt->fetchAll();
}

// --- Liste des formations ---
$formations = $pdo->query("SELECT * FROM formations ORDER BY created_at DESC")->fetchAll();

// Fonctions pour les statuts
function getStatusLabel($statut) {
    $statut = (string)$statut;
    if (in_array($statut, ['1', 'actif', 'active', 'yes', 'on', 'true'])) {
        return 'Actif';
    } elseif (in_array($statut, ['0', 'inactif', 'inactive', 'no', 'off', 'false'])) {
        return 'Inactif';
    }
    return ucfirst($statut);
}

function getStatusBadgeClass($statut) {
    $statut = (string)$statut;
    if (in_array($statut, ['1', 'actif', 'active', 'yes', 'on', 'true'])) {
        return 'success';
    } elseif (in_array($statut, ['0', 'inactif', 'inactive', 'no', 'off', 'false'])) {
        return 'danger';
    }
    return 'secondary';
}

function getStatusValue($statut) {
    $statut = (string)$statut;
    if (in_array($statut, ['1', 'actif', 'active', 'yes', 'on', 'true'])) {
        return 'active';
    } elseif (in_array($statut, ['0', 'inactif', 'inactive', 'no', 'off', 'false'])) {
        return 'inactive';
    }
    return 'active';
}

// Fonctions pour les types de supports
function getSupportTypeLabel($type) {
    $types = [
        'video' => '🎬 Vidéo',
        'pdf' => '📄 PDF',
        'document' => '📝 Document',
        'lien' => '🔗 Lien',
        'image' => '🖼️ Image',
        'audio' => '🎵 Audio'
    ];
    return $types[$type] ?? ucfirst($type);
}

function getSupportTypeBadgeClass($type) {
    $classes = [
        'video' => 'info',
        'pdf' => 'danger',
        'document' => 'primary',
        'lien' => 'success',
        'image' => 'warning',
        'audio' => 'secondary'
    ];
    return $classes[$type] ?? 'secondary';
}

// Fonction pour formater la date
function formatDate($date) {
    if (!$date) return 'Non définie';
    return date('d/m/Y H:i', strtotime($date));
}
?>

<div class="admin-content">

    <?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Formulaire d'ajout/modification -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $action === 'edit' ? 'Modifier' : 'Ajouter' ?> une formation</h3>
            <a href="formations.php" class="btn btn-sm btn-warning">← Retour</a>
        </div>
        
        <?php if ($error): ?>
        <div class="flash-message flash-error"><?= $error ?></div>
        <?php endif; ?>
        
        <!-- Onglets -->
        <?php if ($action === 'edit'): ?>
        <div style="display:flex;gap:4px;border-bottom:2px solid var(--border);margin-bottom:16px;padding:0 16px;">
            <a href="?action=edit&id=<?= $id ?>&tab=informations" 
               class="btn <?= $tab === 'informations' ? 'btn-primary' : 'btn-secondary' ?>" 
               style="border-radius:8px 8px 0 0;padding:10px 20px;text-decoration:none;">
                📝 Informations
            </a>
            <a href="?action=edit&id=<?= $id ?>&tab=supports" 
               class="btn <?= $tab === 'supports' ? 'btn-primary' : 'btn-secondary' ?>" 
               style="border-radius:8px 8px 0 0;padding:10px 20px;text-decoration:none;">
                📎 Supports (<?= count($supports) ?>)
            </a>
            <a href="?action=edit&id=<?= $id ?>&tab=exercices" 
               class="btn <?= $tab === 'exercices' ? 'btn-primary' : 'btn-secondary' ?>" 
               style="border-radius:8px 8px 0 0;padding:10px 20px;text-decoration:none;">
                ✏️ Exercices (<?= count($exercices) ?>)
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Contenu des onglets -->
        <?php if ($tab === 'informations' || $action === 'add'): ?>
        <!-- Formulaire principal -->
        <form method="POST" action="formations.php?action=save" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? 0 ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Titre *</label>
                    <input type="text" class="form-control" name="titre" required 
                           value="<?= htmlspecialchars($edit_data['titre'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Catégorie *</label>
                    <select class="form-control form-select" name="categorie" required>
                        <option value="">Sélectionner...</option>
                        <option value="ongles" <?= (isset($edit_data['categorie']) && $edit_data['categorie'] == 'ongles') ? 'selected' : '' ?>>Prothésie Ongulaire</option>
                        <option value="maquillage" <?= (isset($edit_data['categorie']) && $edit_data['categorie'] == 'maquillage') ? 'selected' : '' ?>>Maquillage</option>
                        <option value="soins" <?= (isset($edit_data['categorie']) && $edit_data['categorie'] == 'soins') ? 'selected' : '' ?>>Soins du visage</option>
                        <option value="epilation" <?= (isset($edit_data['categorie']) && $edit_data['categorie'] == 'epilation') ? 'selected' : '' ?>>Épilation</option>
                        <option value="cils" <?= (isset($edit_data['categorie']) && $edit_data['categorie'] == 'cils') ? 'selected' : '' ?>>Extension de cils</option>
                        <option value="formation" <?= (isset($edit_data['categorie']) && $edit_data['categorie'] == 'formation') ? 'selected' : '' ?>>Formation</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Prix (FCFA)</label>
                    <input type="number" class="form-control" name="prix" step="0.01" min="0"
                           value="<?= $edit_data['prix'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Durée (minutes)</label>
                    <input type="number" class="form-control" name="duree" min="0"
                           value="<?= htmlspecialchars($edit_data['duree'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select class="form-control form-select" name="statut">
                        <option value="active" <?= (isset($edit_data['statut']) && getStatusValue($edit_data['statut']) == 'active') ? 'selected' : '' ?>>✅ Actif</option>
                        <option value="inactive" <?= (isset($edit_data['statut']) && getStatusValue($edit_data['statut']) == 'inactive') ? 'selected' : '' ?>>❌ Inactif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*" id="imageInput">
                    <?php if (isset($edit_data['image']) && $edit_data['image']): ?>
                    <div style="margin-top:8px;">
                        <img id="imagePreview" src="../../uploads/formations/<?= htmlspecialchars($edit_data['image']) ?>" 
                             style="max-height:100px;border-radius:4px;border:1px solid var(--border);">
                    </div>
                    <?php else: ?>
                    <img id="imagePreview" style="display:none;max-height:100px;border-radius:4px;border:1px solid var(--border);margin-top:8px;">
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? '💾 Mettre à jour' : '➕ Ajouter' ?></button>
                <a href="formations.php" class="btn btn-warning">❌ Annuler</a>
            </div>
        </form>
        <?php endif; ?>
        
        <!-- Onglet Supports -->
        <?php if ($action === 'edit' && $tab === 'supports'): ?>
        <div style="padding:16px 0;">
            <!-- Ajouter un support -->
            <div style="background:var(--bg);padding:16px;border-radius:8px;margin-bottom:20px;">
                <h4 style="margin-bottom:12px;">➕ Ajouter un support</h4>
                <form method="POST" action="formations.php?action=add_support&id=<?= $id ?>" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="text" class="form-control" name="titre" placeholder="Titre du support *" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <select class="form-control form-select" name="type">
                            <option value="document">📝 Document</option>
                            <option value="video">🎬 Vidéo</option>
                            <option value="pdf">📄 PDF</option>
                            <option value="image">🖼️ Image</option>
                            <option value="audio">🎵 Audio</option>
                            <option value="lien">🔗 Lien</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="file" class="form-control" name="fichier" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.mp4,.webm">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="text" class="form-control" name="lien_externe" placeholder="Lien externe (si pas de fichier)">
                    </div>
                    <div style="grid-column:1/-1;">
                        <button type="submit" class="btn btn-primary">Ajouter le support</button>
                    </div>
                </form>
            </div>
            
            <!-- Liste des supports -->
            <?php if (empty($supports)): ?>
            <p style="color:var(--text-light);text-align:center;padding:20px;">Aucun support pour cette formation.</p>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Fichier / Lien</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($supports as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['titre']) ?></strong></td>
                        <td><span class="badge badge-<?= getSupportTypeBadgeClass($s['type']) ?>"><?= getSupportTypeLabel($s['type']) ?></span></td>
                        <td>
                            <?php if ($s['fichier']): ?>
                                <a href="../../uploads/supports/<?= htmlspecialchars($s['fichier']) ?>" target="_blank" class="btn btn-sm btn-info">📎 Télécharger</a>
                            <?php elseif ($s['lien_externe']): ?>
                                <a href="<?= htmlspecialchars($s['lien_externe']) ?>" target="_blank" class="btn btn-sm btn-success">🔗 Ouvrir le lien</a>
                            <?php else: ?>
                                <span style="color:var(--text-light);">Aucun fichier</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="formations.php?action=delete_support&id=<?= $id ?>&support_id=<?= $s['id'] ?>" 
                               class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce support ?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Onglet Exercices -->
        <?php if ($action === 'edit' && $tab === 'exercices'): ?>
        <div style="padding:16px 0;">
            <!-- Ajouter un exercice -->
            <div style="background:var(--bg);padding:16px;border-radius:8px;margin-bottom:20px;">
                <h4 style="margin-bottom:12px;">➕ Ajouter un exercice</h4>
                <form method="POST" action="formations.php?action=add_exercice&id=<?= $id ?>" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group" style="margin-bottom:0;grid-column:1/-1;">
                        <input type="text" class="form-control" name="titre" placeholder="Titre de l'exercice *" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;grid-column:1/-1;">
                        <textarea class="form-control" name="description" placeholder="Description de l'exercice" rows="3"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="file" class="form-control" name="fichier" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="date" class="form-control" name="date_limite">
                    </div>
                    <div style="grid-column:1/-1;">
                        <button type="submit" class="btn btn-primary">Ajouter l'exercice</button>
                    </div>
                </form>
            </div>
            
            <!-- Liste des exercices -->
            <?php if (empty($exercices)): ?>
            <p style="color:var(--text-light);text-align:center;padding:20px;">Aucun exercice pour cette formation.</p>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Fichier</th>
                        <th>Date limite</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exercices as $e): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($e['titre']) ?></strong></td>
                        <td><small style="color:var(--text-light);"><?= htmlspecialchars(substr($e['description'] ?? '', 0, 60)) ?></small></td>
                        <td>
                            <?php if ($e['fichier']): ?>
                                <a href="../../uploads/exercices/<?= htmlspecialchars($e['fichier']) ?>" target="_blank" class="btn btn-sm btn-info">📎 Télécharger</a>
                            <?php else: ?>
                                <span style="color:var(--text-light);">Aucun fichier</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $e['date_limite'] ? date('d/m/Y', strtotime($e['date_limite'])) : 'Non définie' ?></td>
                        <td>
                            <a href="formations.php?action=delete_exercice&id=<?= $id ?>&exercice_id=<?= $e['id'] ?>" 
                               class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet exercice ?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php else: ?>
    <!-- Liste des formations -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📚 Toutes les formations</h3>
            <a href="formations.php?action=add" class="btn btn-primary">➕ Ajouter</a>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($formations)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--text-light);">Aucune formation enregistrée</td></tr>
                    <?php else: ?>
                    <?php foreach ($formations as $f): ?>
                    <?php 
                    // Compter les supports et exercices
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM supports WHERE formation_id = ?");
                    $stmt->execute([$f['id']]);
                    $nb_supports = $stmt->fetch()['total'];
                    
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM exercices WHERE formation_id = ?");
                    $stmt->execute([$f['id']]);
                    $nb_exercices = $stmt->fetch()['total'];
                    ?>
                    <tr>
                        <td>
                            <?php if ($f['image']): ?>
                            <img src="../../uploads/formations/<?= htmlspecialchars($f['image']) ?>" 
                                 style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                            <?php else: ?>
                            <span style="color:var(--text-light);font-size:12px;">Aucune</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($f['titre']) ?></strong>
                            <div style="font-size:11px;color:var(--text-light);">
                                📎 <?= $nb_supports ?> supports | ✏️ <?= $nb_exercices ?> exercices
                            </div>
                        </td>
                        <td><?= htmlspecialchars(ucfirst($f['categorie'])) ?></td>
                        <td><?= formatPrice($f['prix']) ?></td>
                        <td><?= htmlspecialchars($f['duree']) ?> min</td>
                        <td>
                            <span class="badge badge-<?= getStatusBadgeClass($f['statut']) ?>">
                                <?= getStatusLabel($f['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="formations.php?action=edit&id=<?= $f['id'] ?>&tab=informations" class="btn btn-sm btn-primary">✏️</a>
                            <a href="formations.php?action=delete&id=<?= $f['id'] ?>" class="btn btn-sm btn-danger delete-btn" onclick="return confirm('Supprimer cette formation ?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Script pour la prévisualisation de l'image
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            if (imagePreview) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    imagePreview.style.display = 'none';
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>