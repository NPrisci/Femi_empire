 

        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header-fixed-container');
            if (window.scrollY > 100) {
                header.classList.add('header-sticky');
            } else {
                header.classList.remove('header-sticky');
            }
        });
   
        // Fonction pour mettre à jour l'interface utilisateur
        function updateUserInterface() {
            fetch('pages/auth/me.php', {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                .then(response => response.json())
                .then(data => {
                    const guestLinks = document.getElementById('ddLoggedOut');
                    const userLinks = document.getElementById('ddLoggedIn');
                    const userNameElement = document.getElementById('ddUserName');
                    const userBtnLabel = document.getElementById('userBtnLabel');

                    if (data.loggedIn) {
                        // Utilisateur connecté
                        if (guestLinks) guestLinks.style.display = 'none';
                        if (userLinks) userLinks.style.display = 'block';

                        // Mettre à jour le nom
                        if (userNameElement && data.name) {
                            userNameElement.textContent = data.name;
                        }

                        // Mettre à jour le label du bouton
                        if (userBtnLabel && data.prenom) {
                            userBtnLabel.textContent = data.prenom;
                        }

                        // Optionnel: mettre en cache dans localStorage pour d'autres usages
                        localStorage.setItem('userData', JSON.stringify(data));
                    } else {
                        // Utilisateur non connecté
                        if (guestLinks) guestLinks.style.display = 'block';
                        if (userLinks) userLinks.style.display = 'none';
                        if (userBtnLabel) userBtnLabel.textContent = '';

                        // Nettoyer le cache
                        localStorage.removeItem('userData');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du statut:', error);
                    // En cas d'erreur, afficher le mode déconnecté par sécurité
                    const guestLinks = document.getElementById('ddLoggedOut');
                    const userLinks = document.getElementById('ddLoggedIn');
                    if (guestLinks) guestLinks.style.display = 'block';
                    if (userLinks) userLinks.style.display = 'none';
                });
        }

        // Fonction de déconnexion
        function logoutUser() {
            // Nettoyer localStorage
            localStorage.removeItem('userData');

            // Rediriger vers la page de déconnexion PHP
            window.location.href = 'pages/auth/logout.php';
        }

        // Fonctions de redirection
        function redirectToLogin() {
            window.location.href = '?page=login';
        }

        function redirectToRegister() {
            window.location.href = '?page=register';
        }

        function redirectToProfile() {
            window.location.href = '?page=profile';
        }

        function redirectToDashboard() {
            window.location.href = '?page=dashboard';
        }

        // Vérifier le statut au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            updateUserInterface();
        });

        // Optionnel: mettre à jour quand l'utilisateur revient sur la page
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                updateUserInterface();
            }
        });

        // Fonction de connexion (à utiliser depuis le formulaire de login)
        async function loginUser(email, password) {
            try {
                const response = await fetch('/../pages/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    credentials: 'same-origin',
                    body: new URLSearchParams({
                        email: email,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Mettre à jour l'interface
                    updateUserInterface();

                    // Rediriger vers le dashboard
                    window.location.href = '?page=dashboard';
                } else {
                    alert('Erreur de connexion: ' + (data.message || 'Identifiants incorrects'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de connexion au serveur');
            }
        }

        // Fonction d'inscription
        async function registerUser(nom, prenom, email, password, confirmPassword) {
            // Vérifier que les mots de passe correspondent
            if (password !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas');
                return;
            }

            try {
                const response = await fetch('pages/auth/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    credentials: 'same-origin',
                    body: new URLSearchParams({
                        nom: nom,
                        prenom: prenom,
                        email: email,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Connexion automatique après inscription
                    updateUserInterface();

                    // Rediriger vers le dashboard
                    window.location.href = '?page=dashboard';
                } else {
                    alert("Erreur d'inscription: " + (data.message || 'Veuillez vérifier vos informations'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert("Erreur d'inscription au serveur");
            }
        }

        var wind = $(window);
        var sticky = $('#sticky-header');
        wind.on('scroll', function() {
            var scroll = wind.scrollTop();
            if (scroll < 100) {
                sticky.removeClass('sticky');
            } else {
                sticky.addClass('sticky');
            }
        });
      
        function togglePwd(id, btn) {
            const inp = document.getElementById(id);
            inp.type = inp.type === 'password' ? 'text' : 'password';
            btn.textContent = inp.type === 'password' ? '👁' : '🙈';
        }

        function checkStrength(val) {
            const bar = document.getElementById('pwdBar');
            const lbl = document.getElementById('pwdLabel');
            bar.className = 'pwd-strength';
            if (!val) {
                lbl.textContent = '';
                return;
            }
            if (val.length < 6) {
                bar.classList.add('weak');
                lbl.textContent = 'Faible';
            } else if (val.length < 10 || !/[0-9]/.test(val)) {
                bar.classList.add('medium');
                lbl.textContent = 'Moyen';
            } else {
                bar.classList.add('strong');
                lbl.textContent = '✓ Fort';
            }
        }

        // Vérification confirmation mdp
        document.querySelector('form').addEventListener('submit', function(e) {
            const p1 = document.getElementById('password').value;
            const p2 = document.getElementById('password2').value;
            if (p1 !== p2) {
                e.preventDefault();
                document.getElementById('password2').style.borderColor = '#e74c3c';
                document.getElementById('password2').focus();
                alert('Les mots de passe ne correspondent pas.');
            }
        });
    