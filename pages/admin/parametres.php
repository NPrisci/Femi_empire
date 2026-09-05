<?php
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// --- Traitement ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = $_POST['params'] ?? [];
    $errors = [];
    $success = 0;
    
    foreach ($params as $key => $value) {
        $value = sanitize($value);
        try {
            $stmt = $pdo->prepare("UPDATE parametres SET valeur = ? WHERE cle = ?");
            $stmt->execute([$value, $key]);
            $success++;
        } catch (PDOException $e) {
            $errors[] = "Erreur pour $key : " . $e->getMessage();
        }
    }
    
    if ($success > 0) {
        setFlash('success', "$success paramètre(s) mis à jour avec succès.");
    }
    if (!empty($errors)) {
        setFlash('error', implode('<br>', $errors));
    }
    header('Location: parametres.php');
    exit;
}

// --- Récupération des paramètres ---
$stmt = $pdo->query("SELECT cle, valeur, description FROM parametres ORDER BY cle");
$parametres = $stmt->fetchAll();
?>

<div class="admin-content">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">⚙️ Paramètres du site</h3>
            <span style="font-size:14px;color:var(--text-light);">Configurez les informations générales de votre site</span>
        </div>
        
        <form method="POST" action="parametres.php">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <?php foreach ($parametres as $param): ?>
                <div class="form-group">
                    <label class="form-label">
                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $param['cle']))) ?>
                        <?php if ($param['description']): ?>
                        <span style="font-weight:400;font-size:12px;color:var(--text-light);">(<?= htmlspecialchars($param['description']) ?>)</span>
                        <?php endif; ?>
                    </label>
                    <?php if (strpos($param['cle'], 'email') !== false || strpos($param['cle'], 'telephone') !== false): ?>
                    <input type="text" class="form-control" name="params[<?= htmlspecialchars($param['cle']) ?>]" 
                           value="<?= htmlspecialchars($param['valeur']) ?>">
                    <?php elseif (strpos($param['cle'], 'description') !== false || strpos($param['cle'], 'adresse') !== false): ?>
                    <textarea class="form-control" name="params[<?= htmlspecialchars($param['cle']) ?>]" rows="2"><?= htmlspecialchars($param['valeur']) ?></textarea>
                    <?php elseif ($param['cle'] == 'devise'): ?>
                    <select class="form-control form-select" name="params[<?= htmlspecialchars($param['cle']) ?>]">
                        <option value="€" <?= $param['valeur'] == '€' ? 'selected' : '' ?>>Euro (€)</option>
                        <option value="$" <?= $param['valeur'] == '$' ? 'selected' : '' ?>>Dollar ($)</option>
                        <option value="CFA" <?= $param['valeur'] == 'CFA' ? 'selected' : '' ?>>CFA (XOF)</option>
                    </select>
                    <?php elseif ($param['cle'] == 'frais_inscription'): ?>
                    <input type="number" class="form-control" name="params[<?= htmlspecialchars($param['cle']) ?>]" 
                           value="<?= htmlspecialchars($param['valeur']) ?>" step="0.01" min="0">
                    <?php else: ?>
                    <input type="text" class="form-control" name="params[<?= htmlspecialchars($param['cle']) ?>]" 
                           value="<?= htmlspecialchars($param['valeur']) ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="margin-top:20px;display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
            </div>
        </form>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>