<aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
      <div class="sidebar-header">
    <a class="brand-mark" href="?action=dashboard" aria-label="FemiEmpire Admin Dashboard">
        <div class="admin-avatar">
            <?php 
            // Récupérer les infos admin
            $adminNom = $admin['nom'] ?? $_SESSION['user_nom'] ?? 'Admin';
            $adminPrenom = $admin['prenom'] ?? $_SESSION['user_prenom'] ?? '';
            $initiales = strtoupper(substr($adminPrenom, 0, 1) . substr($adminNom, 0, 1));
            ?>
            <div class="avatar-circle">
                <?= $initiales ?: 'AD' ?>
            </div>
        </div>
        <div class="brand-copy">
            <span class="brand-title">
                <?php 
                if (!empty($adminPrenom) && !empty($adminNom)) {
                    echo htmlspecialchars($adminPrenom . ' ' . $adminNom);
                } else {
                    echo 'FemiEmpire Admin';
                }
                ?>
            </span>
            <span class="brand-subtitle">
                <i class="fas fa-gem"></i> Administration
            </span>
        </div>
    </a>
</div>



      <nav class="sidebar-nav">
        <a class="nav-link active" href="index-2.html" aria-current="page">
          <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
          <span class="nav-text">Dashboard</span>
        </a>
        <a class="nav-link" href="users.html">
          <span class="nav-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
          <span class="nav-text">Users</span>
        </a>
        <a class="nav-link" href="add-user.html">
          <span class="nav-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
          <span class="nav-text">Add User</span>
        </a>
        <a class="nav-link" href="profile.html">
          <span class="nav-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
          <span class="nav-text">Profile</span>
        </a>
        <a class="nav-link" href="charts.html">
          <span class="nav-icon"><i class="bi bi-bar-chart-line" aria-hidden="true"></i></span>
          <span class="nav-text">Charts</span>
        </a>
        <a class="nav-link" href="tables.html">
          <span class="nav-icon"><i class="bi bi-table" aria-hidden="true"></i></span>
          <span class="nav-text">Tables</span>
        </a>
        <a class="nav-link" href="forms.html">
          <span class="nav-icon"><i class="bi bi-ui-checks-grid" aria-hidden="true"></i></span>
          <span class="nav-text">Forms</span>
        </a>
        <a class="nav-link" href="components.html">
          <span class="nav-icon"><i class="bi bi-grid-3x3-gap" aria-hidden="true"></i></span>
          <span class="nav-text">Components</span>
        </a>
        <a class="nav-link" href="alerts.html">
          <span class="nav-icon"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
          <span class="nav-text">Alerts</span>
        </a>
        <a class="nav-link" href="modals.html">
          <span class="nav-icon"><i class="bi bi-window-stack" aria-hidden="true"></i></span>
          <span class="nav-text">Modals</span>
        </a>
        <a class="nav-link" href="settings.html">
          <span class="nav-icon"><i class="bi bi-gear" aria-hidden="true"></i></span>
          <span class="nav-text">Settings</span>
        </a>
        <a class="nav-link" href="blank.html">
          <span class="nav-icon"><i class="bi bi-file-earmark" aria-hidden="true"></i></span>
          <span class="nav-text">Blank Page</span>
        </a>
      </nav>
<div class="sidebar-user">
    <?php 
    $adminPrenom = $admin['prenom'] ?? $_SESSION['user_prenom'] ?? 'Admin';
    $adminNom = $admin['nom'] ?? $_SESSION['user_nom'] ?? 'Femi';
    $adminEmail = $admin['email'] ?? $_SESSION['user_email'] ?? '';
    $adminRole = $admin['role'] ?? $_SESSION['user_role'] ?? 'admin';
    
    $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($adminPrenom . '+' . $adminNom) . "&background=e2836a&color=fff&size=128";
    ?>
    
    <div class="sidebar-user-trigger" id="sidebarUserTrigger">
        <img class="avatar-img avatar-md sidebar-user-avatar" 
             src="<?= $avatarUrl ?>" 
             alt="<?= htmlspecialchars($adminPrenom . ' ' . $adminNom) ?>">
        <div class="sidebar-user-details">
            <strong><?= htmlspecialchars($adminPrenom . ' ' . $adminNom) ?></strong>
            <small>
                <span class="role-badge"><?= ucfirst($adminRole) ?></span>
            </small>
        </div>
        <i class="fas fa-chevron-down user-toggle-icon"></i>
    </div>
    
    <div class="sidebar-user-dropdown" id="sidebarUserDropdown">
        <div class="dropdown-header">
            <strong><?= htmlspecialchars($adminPrenom . ' ' . $adminNom) ?></strong>
            <small><?= htmlspecialchars($adminEmail) ?></small>
        </div>
        <div class="dropdown-divider"></div>
        <a href="?action=profile" class="dropdown-item">
            <i class="fas fa-user-cog"></i> Mon profil
        </a>
        <a href="?action=settings" class="dropdown-item">
            <i class="fas fa-sliders-h"></i> Paramètres
        </a>
        <div class="dropdown-divider"></div>
        <a href="http://localhost/FemiEmpire/?page=logout" class="dropdown-item danger">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</div>



      <div class="sidebar-footer">
        <span class="status-dot"></span>
        <span class="sidebar-footer-text">System running smoothly</span>
      </div>
    </aside>
    

<style>
.sidebar-user {
    padding: 20px 15px;
    border-bottom: 1px solid rgba(226,131,106,0.2);
    position: relative;
}

.sidebar-user-trigger {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sidebar-user-trigger:hover {
    opacity: 0.8;
}

.sidebar-user-details {
    flex: 1;
}

.sidebar-user-details strong {
    display: block;
    font-size: 0.9rem;
    color: #2c3e50;
    margin-bottom: 2px;
}

.sidebar-user-details small {
    font-size: 0.7rem;
}

.role-badge {
    background: rgba(226,131,106,0.15);
    color: #e2836a;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.6rem;
    font-weight: 500;
}

.user-toggle-icon {
    font-size: 0.8rem;
    color: #7f8c8d;
    transition: transform 0.3s ease;
}

.sidebar-user-trigger.active .user-toggle-icon {
    transform: rotate(180deg);
}

.sidebar-user-dropdown {
    position: absolute;
    top: 100%;
    left: 10px;
    right: 10px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    margin-top: 10px;
    z-index: 1000;
    display: none;
    overflow: hidden;
}

.sidebar-user-dropdown.show {
    display: block;
    animation: dropdownFadeIn 0.2s ease;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-header {
    padding: 12px 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.dropdown-header strong {
    display: block;
    font-size: 0.85rem;
    color: #2c3e50;
}

.dropdown-header small {
    display: block;
    font-size: 0.7rem;
    color: #7f8c8d;
    margin-top: 3px;
}

.dropdown-divider {
    height: 1px;
    background: #e9ecef;
    margin: 5px 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    color: #2c3e50;
    text-decoration: none;
    font-size: 0.8rem;
    transition: background 0.2s ease;
}

.dropdown-item i {
    width: 18px;
    font-size: 0.85rem;
    color: #7f8c8d;
}

.dropdown-item:hover {
    background: #f8f9fa;
}

.dropdown-item.danger {
    color: #e74c3c;
}

.dropdown-item.danger i {
    color: #e74c3c;
}
</style>

<script>
// Toggle du dropdown utilisateur
document.addEventListener('DOMContentLoaded', function() {
    const userTrigger = document.getElementById('sidebarUserTrigger');
    const userDropdown = document.getElementById('sidebarUserDropdown');
    
    if (userTrigger && userDropdown) {
        userTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
            userDropdown.classList.toggle('show');
        });
        
        // Fermer le dropdown en cliquant ailleurs
        document.addEventListener('click', function(e) {
            if (!userTrigger.contains(e.target)) {
                userTrigger.classList.remove('active');
                userDropdown.classList.remove('show');
            }
        });
    }
});
</script>
<style>
/* Styles pour le dashboard admin */
.brand-mark {
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 15px;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(226,131,106,0.2);
}

.brand-mark:hover {
    background: rgba(226,131,106,0.05);
}

.admin-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #e2836a, #c26a52);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
    box-shadow: 0 4px 10px rgba(226,131,106,0.3);
}

.brand-copy {
    display: flex;
    flex-direction: column;
}

.brand-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    line-height: 1.3;
}

.brand-subtitle {
    font-size: 0.7rem;
    color: #e2836a;
    margin-top: 4px;
}

.brand-subtitle i {
    font-size: 0.65rem;
    margin-right: 3px;
}
</style>