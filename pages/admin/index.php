<?php
// pages/admin/index.php - Tableau de bord administrateur

require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$stats = getDashboardStats($pdo);
?>

<div class="admin-content">

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= number_format($stats['utilisateurs']) ?></div>
            <div class="stat-label">👤 Utilisateurs</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-number"><?= number_format($stats['formations']) ?></div>
            <div class="stat-label">📚 Formations</div>
        </div>
        <div class="stat-card green">
            <div class="stat-number"><?= number_format($stats['commandes']) ?></div>
            <div class="stat-label">🛒 Commandes</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-number"><?= formatPrice($stats['ca_total']) ?></div>
            <div class="stat-label">💰 Chiffre d'affaires</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-number"><?= number_format($stats['commandes_attente']) ?></div>
            <div class="stat-label">⏳ Commandes en attente</div>
        </div>
        <div class="stat-card green">
            <!-- <div class="stat-number"><?= number_format($stats['inscriptions_actives']) ?></div> -->
            <div class="stat-label">✅ Inscriptions actives</div>
        </div>
    </div>

    <div class="row" style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <!-- Commandes récentes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Commandes récentes</h3>
                <a href="paiements.php" class="btn btn-sm btn-primary">Voir tout</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Formation</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['commandes_recentes'])): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;color:var(--text-light);">Aucune commande récente</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['commandes_recentes'] as $cmd): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?></td>
                                    <td><?= htmlspecialchars($cmd['formation_titre']) ?></td>
                                    <td><?= formatPrice($cmd['montant']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $cmd['status'] == 'payee' ? 'success' : ($cmd['status'] == 'en_attente' ? 'warning' : 'danger') ?>">
                                            <?= $cmd['status'] == 'payee' ? 'Payée' : ($cmd['status'] == 'en_attente' ? 'En attente' : 'Annulée') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inscriptions récentes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Inscriptions récentes</h3>
                <a href="inscriptions.php" class="btn btn-sm btn-primary">Voir tout</a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Formation</th>
                            <th>Statut</th>
                            <th>Progression</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $inscriptionsPayees = array_filter(
                            $stats['inscriptions_recentes'],
                            fn($insc) => $insc['status'] === 'payee'
                        );
                        ?>

                        <?php if (empty($inscriptionsPayees)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;color:var(--text-light);">
                                    Aucune inscription récente
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($inscriptionsPayees as $insc): ?>
                                <?php
                                $progression = $insc['progression'] ?? 0;
                                $statut = $progression == 100 ? 'Terminée' : 'En cours';
                                $badge = $progression == 100 ? 'info' : 'success';
                                ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars($insc['prenom'] . ' ' . $insc['nom']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($insc['formation_titre']) ?>
                                    </td>

                                    <td>
                                        <span class="badge badge-<?= $badge ?>">
                                            <?= $statut ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= $progression ?>%
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Répartition des formations par catégorie -->
    <div class="card" style="margin-top:24px;">
        <div class="card-header">
            <h3 class="card-title">Répartition des formations par catégorie</h3>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;">
            <?php foreach ($stats['formations_categories'] as $cat): ?>
                <div style="background:var(--bg);padding:16px;border-radius:8px;text-align:center;">
                    <div style="font-size:28px;font-weight:700;color:var(--primary);"><?= $cat['total'] ?></div>
                    <div style="font-size:13px;color:var(--text-light);"><?= htmlspecialchars(ucfirst($cat['categorie'])) ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($stats['formations_categories'])): ?>
                <p style="color:var(--text-light);">Aucune formation enregistrée</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="card" style="margin-top:24px;">
        <div class="card-header">
            <h3 class="card-title">Actions rapides</h3>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="formations.php?action=add" class="btn btn-primary">➕ Ajouter une formation</a>
            <a href="utilisateurs.php" class="btn btn-success">👤 Gérer les utilisateurs</a>
            <a href="paiements.php" class="btn btn-warning">💰 Voir les paiements</a>
            <a href="realisations.php" class="btn btn-info" style="background:#3b82f6;color:#fff;">🖼️ Valider des réalisations</a>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>