// assets/js/user-icon.js
(function () {
  var userBtn      = document.getElementById('userBtn');
  var userDropdown = document.getElementById('userDropdown');
  var ddLoggedOut  = document.getElementById('ddLoggedOut');
  var ddLoggedIn   = document.getElementById('ddLoggedIn');
  var userNameSpan = document.getElementById('ddUserName');
  var userBtnLabel = document.getElementById('userBtnLabel');

  if (!userBtn || !userDropdown) return;

  // Toggle dropdown
  userBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    userDropdown.classList.toggle('open');
  });
  
  document.addEventListener('click', function () {
    userDropdown.classList.remove('open');
  });
  
  userDropdown.addEventListener('click', function (e) {
    e.stopPropagation();
  });

  // Fonction pour mettre à jour l'affichage
  function updateUIBasedOnAuth() {
    // Vérifier d'abord dans PHP
    fetch('pages/auth/me.php', {
      method: 'GET',
      credentials: 'same-origin',
      cache: 'no-store'
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.loggedIn) {
        // Utilisateur connecté via PHP
        showLoggedIn(data);
      } else {
        // Vérifier dans localStorage comme fallback
        const userData = localStorage.getItem('userData');
        if (userData) {
          try {
            const user = JSON.parse(userData);
            showLoggedIn(user);
          } catch {
            showLoggedOut();
          }
        } else {
          showLoggedOut();
        }
      }
    })
    .catch(function () {
      // En cas d'erreur, vérifier localStorage
      const userData = localStorage.getItem('userData');
      if (userData) {
        try {
          const user = JSON.parse(userData);
          showLoggedIn(user);
        } catch {
          showLoggedOut();
        }
      } else {
        showLoggedOut();
      }
    });
  }

  function showLoggedIn(userData) {
    if (ddLoggedOut) ddLoggedOut.style.display = 'none';
    if (ddLoggedIn)  ddLoggedIn.style.display  = 'block';
    
    // Mettre à jour le nom si disponible
    if (userNameSpan && userData.name) {
      userNameSpan.textContent = userData.name;
    }
    
    // Mettre à jour le label du bouton
    if (userBtnLabel && userData.prenom) {
      userBtnLabel.textContent = userData.prenom;
    }
    
    // Sauvegarder dans localStorage pour cohérence
    localStorage.setItem('userData', JSON.stringify(userData));
  }

  function showLoggedOut() {
    if (ddLoggedOut) ddLoggedOut.style.display = 'block';
    if (ddLoggedIn)  ddLoggedIn.style.display  = 'none';
    if (userBtnLabel) userBtnLabel.textContent = '';
    
    // Nettoyer localStorage
    localStorage.removeItem('userData');
  }

  // Initial check
  updateUIBasedOnAuth();

  // Écouter les changements dans localStorage (pour mise à jour entre onglets)
  window.addEventListener('storage', function(e) {
    if (e.key === 'userData') {
      if (e.newValue) {
        try {
          const user = JSON.parse(e.newValue);
          showLoggedIn(user);
        } catch {
          showLoggedOut();
        }
      } else {
        showLoggedOut();
      }
    }
  });
})();

// Fonction de déconnexion globale
function logoutUser() {
  // Nettoyer localStorage
  localStorage.removeItem('userData');
  
  // Rediriger vers la page de déconnexion PHP
  window.location.href = 'pages/auth/logout.php';
}