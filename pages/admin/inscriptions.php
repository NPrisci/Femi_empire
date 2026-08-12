<?php
// pages/admin/inscriptions.php - Gestion des inscriptions

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// --- Traitement des actions ---
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Mise à jour du statut de paiement
if ($action === 'update_payment_status' && $id > 0) {
    $status = sanitize($_GET['status'] ?? '');
    $valid_status = ['payee', 'en_attente', 'annulee'];
    
    if (in_array($status, $valid_status)) {
        try {
            $stmt = $pdo->prepare("UPDATE commandes SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            setFlash('success', 'Statut de paiement mis à jour avec succès.');
        } catch (PDOException $e) {
            setFlash('error', 'Erreur : ' . $e->getMessage());
        }
    } else {
        setFlash('error', 'Statut invalide.');
    }
    header('Location: inscriptions.php');
    exit;
}

// Mise à jour de la progression
if ($action === 'update_progress' && $id > 0) {
    $progression = (int)($_POST['progression'] ?? 0);
    
    if ($progression >= 0 && $progression <= 100) {
        try {
            // Mettre à jour la progression
            $stmt = $pdo->prepare("UPDATE commandes SET progression = ? WHERE id = ?");
            $stmt->execute([$progression, $id]);
            
            // Message personnalisé
            $status_message = '';
            if ($progression == 100) {
                $status_message = ' 🎉 Félicitations ! La formation est terminée !';
            } elseif ($progression >= 50) {
                $status_message = ' 🚀 La formation est bien avancée !';
            } elseif ($progression > 0) {
                $status_message = ' 📖 La formation a commencé !';
            } else {
                $status_message = ' ⏳ Pas encore commencé';
            }
            
            setFlash('success', 'Progression mise à jour à ' . $progression . '%' . $status_message);
        } catch (PDOException $e) {
            setFlash('error', 'Erreur : ' . $e->getMessage());
        }
    } else {
        setFlash('error', 'La progression doit être entre 0 et 100.');
    }
    header('Location: inscriptions.php');
    exit;
}

// Suppression
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Inscription supprimée avec succès.');
    } catch (PDOException $e) {
        setFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
    header('Location: inscriptions.php');
    exit;
}

// --- Filtres ---
$filter_progression = isset($_GET['progression']) ? sanitize($_GET['progression']) : '';
$filter_status = isset($_GET['payment_status']) ? sanitize($_GET['payment_status']) : '';
$filter_formation = isset($_GET['formation']) ? (int)$_GET['formation'] : 0;

// --- Récupération des données ---
$query = "
    SELECT c.*, u.prenom, u.nom, u.email, u.telephone, f.titre as formation_titre 
    FROM commandes c
    JOIN utilisateurs u ON c.utilisateur_id = u.id
    JOIN formations f ON c.formation_id = f.id
    WHERE 1=1
";

$params = [];

// Filtre par progression
if ($filter_progression !== '') {
    if ($filter_progression == '0') {
        $query .= " AND c.progression = 0";
    } elseif ($filter_progression == '1-49') {
        $query .= " AND c.progression BETWEEN 1 AND 49";
    } elseif ($filter_progression == '50-99') {
        $query .= " AND c.progression BETWEEN 50 AND 99";
    } elseif ($filter_progression == '100') {
        $query .= " AND c.progression = 100";
    }
}

// Filtre par statut de paiement
if ($filter_status) {
    $query .= " AND c.status = ?";
    $params[] = $filter_status;
}

if ($filter_formation > 0) {
    $query .= " AND c.formation_id = ?";
    $params[] = $filter_formation;
}

$query .= " ORDER BY c.date_creation DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$inscriptions = $stmt->fetchAll();

// --- Formations pour le filtre ---
$formations = $pdo->query("SELECT id, titre FROM formations ORDER BY titre")->fetchAll();

// --- Statistiques basées sur la progression ---
// Récupérer toutes les progressions
$all_progressions = $pdo->query("SELECT progression FROM commandes")->fetchAll(PDO::FETCH_COLUMN);

// Compter les progressions par catégorie
$stats_progression = [
    'termine' => 0,      // 100%
    'avance' => 0,       // 50-99%
    'encours' => 0,      // 1-49%
    'pascommence' => 0,  // 0%
];

foreach ($all_progressions as $progress) {
    $progress = (int)$progress;
    if ($progress == 100) {
        $stats_progression['termine']++;
    } elseif ($progress >= 50) {
        $stats_progression['avance']++;
    } elseif ($progress > 0) {
        $stats_progression['encours']++;
    } else {
        $stats_progression['pascommence']++;
    }
}

// Statistiques de paiement
$stats_paiement = $pdo->query("
    SELECT status, COUNT(*) as total 
    FROM commandes 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Statistiques totales
$total_inscriptions = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();

// Fonction pour obtenir la classe CSS du badge de progression
function getProgressBadgeClass($progress) {
    $progress = (int)$progress;
    if ($progress == 100) return 'success';
    if ($progress >= 50) return 'info';
    if ($progress > 0) return 'warning';
    return 'secondary';
}

// Fonction pour obtenir le libellé de la progression
function getProgressLabel($progress) {
    $progress = (int)$progress;
    if ($progress == 100) return '✅ Terminée';
    if ($progress >= 50) return '🚀 Bien avancé';
    if ($progress > 0) return '📖 En cours';
    return '⏳ Pas commencé';
}

// Fonction pour obtenir la couleur de la barre de progression
function getProgressColor($progress) {
    $progress = (int)$progress;
    if ($progress == 100) return '#22c55e';
    if ($progress >= 50) return '#3b82f6';
    if ($progress > 0) return '#eab308';
    return '#94a3b8';
}

// Fonction pour obtenir la classe CSS du badge de paiement
function getPaymentBadgeClass($status) {
    switch ($status) {
        case 'payee':
            return 'success';
        case 'en_attente':
            return 'warning';
        case 'annulee':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Fonction pour obtenir le libellé du statut de paiement
function getPaymentLabel($status) {
    switch ($status) {
        case 'payee':
            return '✅ Payée';
        case 'en_attente':
            return '⏳ En attente';
        case 'annulee':
            return '❌ Annulée';
        default:
            return ucfirst($status);
    }
}
?>

<div class="admin-content">

    <!-- Statistiques de Progression -->
    <div class="stats-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:16px;">
        <div class="stat-card success">
            <div class="stat-number"><?= $stats_progression['termine'] ?></div>
            <div class="stat-label">🏆 Terminées (100%)</div>
        </div>
        <div class="stat-card info">
            <div class="stat-number"><?= $stats_progression['avance'] ?></div>
            <div class="stat-label">🚀 Bien avancées (50-99%)</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?= $stats_progression['encours'] ?></div>
            <div class="stat-label">📖 En cours (1-49%)</div>
        </div>
        <div class="stat-card" style="background:var(--bg);border-color:var(--text-light);">
            <div class="stat-number"><?= $stats_progression['pascommence'] ?></div>
            <div class="stat-label">⏳ Pas commencé (0%)</div>
        </div>
        <div class="stat-card primary">
            <div class="stat-number"><?= $total_inscriptions ?></div>
            <div class="stat-label">📊 Total</div>
        </div>
    </div>

    <!-- Statistiques de Paiement -->
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:16px;">
        <div class="stat-card success">
            <div class="stat-number"><?= $stats_paiement['payee'] ?? 0 ?></div>
            <div class="stat-label">💰 Payées</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?= $stats_paiement['en_attente'] ?? 0 ?></div>
            <div class="stat-label">⏳ En attente</div>
        </div>
        <div class="stat-card danger" style="border-color:#ef4444;">
            <div class="stat-number"><?= $stats_paiement['annulee'] ?? 0 ?></div>
            <div class="stat-label">❌ Annulées</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card">
        <form method="GET" action="inscriptions.php" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Progression</label>
                <select class="form-control form-select" name="progression" style="width:180px;">
                    <option value="">Tous</option>
                    <option value="0" <?= $filter_progression === '0' ? 'selected' : '' ?>>⏳ Pas commencé (0%)</option>
                    <option value="1-49" <?= $filter_progression === '1-49' ? 'selected' : '' ?>>📖 En cours (1-49%)</option>
                    <option value="50-99" <?= $filter_progression === '50-99' ? 'selected' : '' ?>>🚀 Bien avancé (50-99%)</option>
                    <option value="100" <?= $filter_progression === '100' ? 'selected' : '' ?>>🏆 Terminé (100%)</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Statut Paiement</label>
                <select class="form-control form-select" name="payment_status" style="width:150px;">
                    <option value="">Tous</option>
                    <option value="payee" <?= $filter_status == 'payee' ? 'selected' : '' ?>>Payée</option>
                    <option value="en_attente" <?= $filter_status == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="annulee" <?= $filter_status == 'annulee' ? 'selected' : '' ?>>Annulée</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Formation</label>
                <select class="form-control form-select" name="formation" style="width:200px;">
                    <option value="">Toutes</option>
                    <?php foreach ($formations as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= $filter_formation == $f['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['titre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <a href="inscriptions.php" class="btn btn-warning">Réinitialiser</a>
        </form>
    </div>

    <!-- Liste des inscriptions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Toutes les inscriptions</h3>
            <span style="font-size:14px;color:var(--text-light);"><?= count($inscriptions) ?> inscription(s)</span>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Apprenant</th>
                        <th>Formation</th>
                        <th>Date</th>
                        <th>Progression</th>
                        <th>Statut Paiement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inscriptions)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--text-light);">Aucune inscription trouvée</td></tr>
                    <?php else: ?>
                    <?php foreach ($inscriptions as $insc): ?>
                    <?php $progress = (int)($insc['progression'] ?? 0); ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($insc['prenom'] . ' ' . $insc['nom']) ?></strong><br>
                            <span style="font-size:12px;color:var(--text-light);"><?= htmlspecialchars($insc['email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($insc['formation_titre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($insc['date_creation'] ?? 'now')) ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="flex:1;height:8px;background:var(--bg);border-radius:4px;overflow:hidden;min-width:80px;">
                                    <div style="height:100%;width:<?= $progress ?>%;background:<?= getProgressColor($progress); ?>;border-radius:4px;transition:width 0.5s ease;"></div>
                                </div>
                                <span style="font-size:13px;font-weight:700;min-width:40px;color:<?= getProgressColor($progress); ?>;">
                                    <?= $progress ?>%
                                </span>
                            </div>
                            <span style="font-size:11px;font-weight:600;color:<?= getProgressColor($progress); ?>;">
                                <?= getProgressLabel($progress) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= getPaymentBadgeClass($insc['status'] ?? 'en_attente') ?>">
                                <?= getPaymentLabel($insc['status'] ?? 'en_attente') ?>
                            </span>
                        </td>
                        <td>
                            <button onclick="openModal('progressModal_<?= $insc['id'] ?>')" class="btn btn-sm btn-primary">📊 Progression</button>
                            <a href="inscriptions.php?action=delete&id=<?= $insc['id'] ?>" class="btn btn-sm btn-danger delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')">🗑️</a>
                            
                            <!-- Modal progression -->
                            <div class="modal-overlay" id="progressModal_<?= $insc['id'] ?>">
                                <div class="modal" style="max-width:500px;">
                                    <div class="modal-header">
                                        <h3 class="modal-title">📊 Gérer la progression</h3>
                                        <button class="modal-close" onclick="closeModal('progressModal_<?= $insc['id'] ?>')">&times;</button>
                                    </div>
                                    <form method="POST" action="inscriptions.php?action=update_progress&id=<?= $insc['id'] ?>">
                                        <div class="modal-body">
                                            <p><strong>Apprenant :</strong> <?= htmlspecialchars($insc['prenom'] . ' ' . $insc['nom']) ?></p>
                                            <p><strong>Formation :</strong> <?= htmlspecialchars($insc['formation_titre']) ?></p>
                                            <p><strong>Statut paiement :</strong> <?= getPaymentLabel($insc['status'] ?? 'en_attente') ?></p>
                                            
                                            <div style="background:var(--bg);padding:12px;border-radius:8px;margin:12px 0;">
                                                <p style="margin:4px 0;"><strong>Progression actuelle :</strong> <?= $progress ?>%</p>
                                                <p style="margin:4px 0;"><strong>Statut actuel :</strong> <?= getProgressLabel($progress) ?></p>
                                            </div>
                                            
                                            <div style="background:#f0fdf4;padding:12px;border-radius:8px;margin:12px 0;border:1px solid #bbf7d0;">
                                                <p style="margin:4px 0;font-size:13px;color:#16a34a;">💡 <strong>Les niveaux de progression :</strong></p>
                                                <ul style="margin:4px 0;font-size:13px;color:#16a34a;padding-left:20px;">
                                                    <li>0% → ⏳ Pas commencé</li>
                                                    <li>1-49% → 📖 En cours</li>
                                                    <li>50-99% → 🚀 Bien avancé</li>
                                                    <li>100% → 🏆 Terminée</li>
                                                </ul>
                                            </div>
                                            
                                            <div class="form-group" style="margin-top:16px;">
                                                <label class="form-label">Progression (%)</label>
                                                
                                                <!-- Boutons de progression rapide -->
                                                <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:12px;">
                                                    <button type="button" class="btn btn-sm btn-secondary" onclick="setProgress(<?= $insc['id'] ?>, 0)" style="font-size:12px;">0%</button>
                                                    <button type="button" class="btn btn-sm btn-warning" onclick="setProgress(<?= $insc['id'] ?>, 25)" style="font-size:12px;">25%</button>
                                                    <button type="button" class="btn btn-sm btn-info" onclick="setProgress(<?= $insc['id'] ?>, 50)" style="font-size:12px;">50%</button>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="setProgress(<?= $insc['id'] ?>, 75)" style="font-size:12px;">75%</button>
                                                    <button type="button" class="btn btn-sm btn-success" onclick="setProgress(<?= $insc['id'] ?>, 100)" style="font-size:12px;">100%</button>
                                                </div>
                                                
                                                <!-- Slider de progression -->
                                                <input type="range" class="form-control" name="progression" id="progressSlider_<?= $insc['id'] ?>" 
                                                       min="0" max="100" value="<?= $progress ?>" 
                                                       oninput="updateProgressDisplay(<?= $insc['id'] ?>, this.value)">
                                                
                                                <!-- Affichage de la progression -->
                                                <div style="text-align:center;font-size:28px;font-weight:700;color:var(--primary);margin-top:12px;">
                                                    <span id="progressValue_<?= $insc['id'] ?>"><?= $progress ?></span>%
                                                </div>
                                                
                                                <!-- Statut prévu en temps réel -->
                                                <div style="text-align:center;font-size:16px;color:var(--text-light);margin-top:8px;padding:8px;background:var(--bg);border-radius:6px;">
                                                    Statut prévu : <strong id="statusPreview_<?= $insc['id'] ?>" style="font-size:18px;">
                                                        <?php 
                                                            echo $progress == 100 ? '🏆 Terminée' : ($progress >= 50 ? '🚀 Bien avancé' : ($progress > 0 ? '📖 En cours' : '⏳ Pas commencé'));
                                                        ?>
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="display:flex;gap:10px;justify-content:flex-end;">
                                            <button type="button" class="btn btn-warning" onclick="closeModal('progressModal_<?= $insc['id'] ?>')">Annuler</button>
                                            <button type="submit" class="btn btn-primary" style="font-size:16px;padding:10px 24px;">
                                                💾 Mettre à jour
                                            </button>
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

<!-- JavaScript pour les modals et actions rapides -->
<script>
// Fonctions pour les modals
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Fermer le modal en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
});

// Fonction pour définir une progression rapide
function setProgress(id, value) {
    var slider = document.getElementById('progressSlider_' + id);
    if (slider) {
        slider.value = value;
        updateProgressDisplay(id, value);
    }
}

// Fonction pour mettre à jour l'affichage de la progression
function updateProgressDisplay(id, value) {
    var progressValue = document.getElementById('progressValue_' + id);
    var statusPreview = document.getElementById('statusPreview_' + id);
    var val = parseInt(value);
    
    // Mettre à jour le pourcentage
    if (progressValue) {
        progressValue.textContent = val;
    }
    
    // Mettre à jour le statut prévu
    if (statusPreview) {
        var statusText = '';
        if (val == 100) {
            statusText = '🏆 Terminée';
        } else if (val >= 50) {
            statusText = '🚀 Bien avancé';
        } else if (val > 0) {
            statusText = '📖 En cours';
        } else {
            statusText = '⏳ Pas commencé';
        }
        statusPreview.textContent = statusText;
    }
}

// Mise à jour rapide de la progression (pour les boutons dans le tableau)
function quickUpdateProgress(id, progress) {
    if (confirm('Mettre à jour la progression à ' + progress + '% ?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'inscriptions.php?action=update_progress&id=' + id;
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'progression';
        input.value = progress;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>