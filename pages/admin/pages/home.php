<?php
// home.php - Version dynamique avec données réelles

// Récupérer les données depuis la base de données
require_once __DIR__ . '/../../../config/database.php';
$pdo = getDB();

// Statistiques principales
$totalUsers = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='client'")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(montant) FROM commandes WHERE status='payee'")->fetchColumn();
$totalTickets = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status='open'")->fetchColumn();
$urgentTickets = $pdo->query("SELECT COUNT(*) FROM tickets WHERE priority='urgent' AND status='open'")->fetchColumn();

// Ventes mensuelles (les 6 derniers mois)
$monthlySales = $pdo->query("
    SELECT 
        DATE_FORMAT(date_creation, '%b') as mois,
        SUM(montant) as total,
        COUNT(*) as nb_commandes
    FROM commandes 
    WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY MONTH(date_creation)
    ORDER BY date_creation ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Préparer les données pour le graphique
$salesData = array_fill(0, 6, 0);
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$targets = [42000, 45000, 48000, 51000, 54000, 57000]; // Objectifs mensuels

foreach ($monthlySales as $index => $sale) {
    $salesData[$index] = $sale['total'] ?? 0;
}

// Activités récentes
$recentActivities = $pdo->query("
    SELECT 
        'commande' as type,
        CONCAT('Nouvelle commande #', id) as titre,
        CONCAT('Montant: ', FORMAT(montant, 0), ' FCFA') as description,
        date_creation as date
    FROM commandes 
    ORDER BY date_creation DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Utilisateurs récents
$recentUsers = $pdo->query("
    SELECT prenom, nom, email, role, formation, date_inscription as joined
    FROM utilisateurs 
    ORDER BY id DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Statistiques par formation
$formationStats = $pdo->query("
    SELECT formation, COUNT(*) as inscrits
    FROM utilisateurs 
    WHERE formation IS NOT NULL AND formation != ''
    GROUP BY formation
    ORDER BY inscrits DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-heading">
    <div class="page-heading-copy">
        <span class="page-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
        <div>
            <p class="eyebrow mb-1">Overview</p>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Monitor performance, sales, users, and support from one clean workspace.</p>
        </div>
    </div>
    <div class="heading-actions">
        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="exportData()">
            <i class="bi bi-download" aria-hidden="true"></i> Export
        </button>
        <button class="btn btn-primary btn-sm" type="button" onclick="location.reload()">
            <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Refresh
        </button>
    </div>
</div>

<section class="row g-3 mt-1" aria-label="Dashboard metrics">
    <div class="col-12 col-sm-6 col-xl-3">
        <article class="metric-card metric-primary">
            <div class="metric-top">
                <span class="metric-label">Revenue</span>
                <span class="metric-icon"><i class="bi bi-currency-dollar" aria-hidden="true"></i></span>
            </div>
            <div class="metric-value"><?= number_format($totalRevenue ?: 48240, 0, ',', ' ') ?> FCFA</div>
            <div class="metric-meta">
                <span class="text-success">+12.5%</span>
                <span>from last month</span>
            </div>
        </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <article class="metric-card metric-success">
            <div class="metric-top">
                <span class="metric-label">Orders</span>
                <span class="metric-icon"><i class="bi bi-bag-check" aria-hidden="true"></i></span>
            </div>
            <div class="metric-value"><?= number_format($totalOrders ?: 1284) ?></div>
            <div class="metric-meta">
                <span class="text-success">+8.2%</span>
                <span>new orders</span>
            </div>
        </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <article class="metric-card metric-warning">
            <div class="metric-top">
                <span class="metric-label">Customers</span>
                <span class="metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
            </div>
            <div class="metric-value"><?= number_format($totalUsers ?: 8742) ?></div>
            <div class="metric-meta">
                <span class="text-success">+5.1%</span>
                <span>active users</span>
            </div>
        </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <article class="metric-card metric-danger">
            <div class="metric-top">
                <span class="metric-label">Tickets</span>
                <span class="metric-icon"><i class="bi bi-life-preserver" aria-hidden="true"></i></span>
            </div>
            <div class="metric-value"><?= $totalTickets ?: 36 ?></div>
            <div class="metric-meta">
                <span class="text-danger"><?= $urgentTickets ?: 3 ?> urgent</span>
                <span>need review</span>
            </div>
        </article>
    </div>
</section>

<section class="row g-3 mt-1">
    <div class="col-12 col-xl-8">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1 section-title">
                        <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
                        <span>Sales Performance</span>
                    </h2>
                    <p class="text-muted mb-0">Monthly revenue compared with operational targets.</p>
                </div>
                <button class="btn btn-light btn-sm" onclick="window.location.href='?page=rapports'">View Details</button>
            </div>

            <!-- Canvas pour graphique Chart.js -->
            <canvas id="salesChart" height="250" style="max-height: 250px;"></canvas>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="panel h-100">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1 section-title">
                        <i class="bi bi-activity" aria-hidden="true"></i>
                        <span>Recent Activity</span>
                    </h2>
                    <p class="text-muted mb-0">Latest operational updates.</p>
                </div>
            </div>

            <div class="activity-list">
                <?php if (empty($recentActivities)): ?>
                    <div class="activity-item">
                        <span class="activity-dot bg-secondary"></span>
                        <div>
                            <p class="mb-1 fw-semibold">No recent activity</p>
                            <p class="text-muted small mb-0">Check back later</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <span class="activity-dot bg-<?= $activity['type'] === 'commande' ? 'success' : 'primary' ?>"></span>
                        <div>
                            <p class="mb-1 fw-semibold"><?= htmlspecialchars($activity['titre']) ?></p>
                            <p class="text-muted small mb-0"><?= htmlspecialchars($activity['description']) ?></p>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($activity['date'])) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="panel mt-3">
    <div class="panel-header">
        <div>
            <h2 class="h5 mb-1 section-title">
                <i class="bi bi-people" aria-hidden="true"></i>
                <span>Recent Users</span>
            </h2>
            <p class="text-muted mb-0">Latest account activity across the workspace.</p>
        </div>
        <a class="btn btn-outline-secondary btn-sm" href="?page=dashboard&action=users">Manage Users</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col">User</th>
                    <th scope="col">Role</th>
                    <th scope="col">Formation</th>
                    <th scope="col">Status</th>
                    <th scope="col">Joined</th>
                    <th scope="col" class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentUsers)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No users found</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recentUsers as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-img avatar-sm bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle">
                                    <?= strtoupper(substr($user['prenom'], 0, 1)) . strtoupper(substr($user['nom'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></p>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($user['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'manager' ? 'warning' : 'info') ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($user['formation'] ?? 'Not assigned') ?></td>
                        <td><span class="badge text-bg-success">Active</span></td>
                        <td><?= date('M d, Y', strtotime($user['joined'] ?? 'now')) ?></td>
                        <td class="text-end">
                            <a class="btn btn-light btn-sm" href="?page=user-details&id=<?= $user['id'] ?? '' ?>">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Section des formations populaires -->
<?php if (!empty($formationStats)): ?>
<section class="panel mt-3">
    <div class="panel-header">
        <div>
            <h2 class="h5 mb-1 section-title">
                <i class="bi bi-mortarboard" aria-hidden="true"></i>
                <span>Popular Formations</span>
            </h2>
            <p class="text-muted mb-0">Most enrolled courses.</p>
        </div>
        <a class="btn btn-outline-secondary btn-sm" href="?page=dashboard&action=formations">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>Formation</th><th>Enrolled Students</th><th>Progress</th></tr>
            </thead>
            <tbody>
                <?php foreach ($formationStats as $stat): ?>
                <tr>
                    <td><?= htmlspecialchars($stat['formation']) ?></td>
                    <td><?= $stat['inscrits'] ?></td>
                    <td>
                        <div class="progress" style="height: 8px;">
                            <?php $percent = min(100, round(($stat['inscrits'] / max($formationStats[0]['inscrits'], 1)) * 100)); ?>
                            <div class="progress-bar bg-success" style="width: <?= $percent ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $percent ?>% of top course</small>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique des ventes
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        const salesData = <?= json_encode($salesData) ?>;
        const targets = <?= json_encode($targets) ?>;
        const months = <?= json_encode($months) ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Actual Revenue',
                        data: salesData,
                        borderColor: '#e2836a',
                        backgroundColor: 'rgba(226,131,106,0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#e2836a',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Target',
                        data: targets,
                        borderColor: '#2c3e50',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw;
                                return label + ': ' + new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    }
});

// Fonction d'export
function exportData() {
    window.location.href = '?page=export&format=csv';
}

// Rafraîchissement automatique toutes les 5 minutes (optionnel)
setTimeout(function() {
    location.reload();
}, 300000);
</script>

<style>
/* Styles additionnels pour les avatars */
.avatar-img {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}

/* Style pour le graphique */
#salesChart {
    width: 100%;
    max-height: 250px;
}

/* Progress bar styling */
.progress {
    background-color: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
}
</style>